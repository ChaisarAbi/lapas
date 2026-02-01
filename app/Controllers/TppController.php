<?php

namespace App\Controllers;

use App\Models\KriteriaModel;
use App\Models\SubkriteriaModel;
use App\Models\PenilaianModel;

class TppController extends BaseController
{
    protected $kriteriaModel;
    protected $subkriteriaModel;
    protected $penilaianModel;
    
    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        $this->subkriteriaModel = new SubkriteriaModel();
        $this->penilaianModel = new PenilaianModel();
    }
    
    public function dashboard()
    {
        // Ambil data statistik realtime
        $totalKriteria = $this->kriteriaModel->countAll();
        $totalSubkriteria = $this->subkriteriaModel->countAll();
        
        // Ambil periode aktif
        $periodeAktif = $this->penilaianModel->getPeriodeAktif();
        $periodeDefault = date('Y-m');
        
        if (!empty($periodeAktif) && isset($periodeAktif['periode'])) {
            $periode = $periodeAktif['periode'];
        } else {
            $periode = $periodeDefault;
        }
        
        // Hitung kriteria dengan bobot (untuk progress bar)
        $kriteriaDenganBobot = $this->kriteriaModel->where('bobot >', 0)->countAllResults();
        $persentaseBobot = $totalKriteria > 0 ? round(($kriteriaDenganBobot / $totalKriteria) * 100, 1) : 0;
        
        // Hitung persentase interdependensi dari tabel anp_interdependensi
        $persentaseInterdependensi = $this->hitungPersentaseInterdependensi();
        
        // Hitung progress pairwise comparison
        $progressPairwise = $this->hitungProgressPairwise();
        
        // Ambil data untuk progress bar
        $kriteriaList = $this->kriteriaModel->findAll();
        $progressData = [];
        
        foreach ($kriteriaList as $kriteria) {
            $subkriteriaCount = $this->subkriteriaModel->where('kriteria_id', $kriteria['id'])->countAllResults();
            $progressData[] = [
                'kriteria' => $kriteria['nama'],
                'subkriteria' => $subkriteriaCount,
                'bobot' => $kriteria['bobot']
            ];
        }
        
        // Ambil status ANP terbaru
        $statusAnp = $this->getStatusAnpTerbaru();
        
        $data = [
            'title' => 'Dashboard TPP',
            'page_title' => 'Dashboard Tim Pengamat Pemasyarakatan',
            'dashboard_url' => 'tpp/dashboard',
            'totalKriteria' => $totalKriteria,
            'totalSubkriteria' => $totalSubkriteria,
            'kriteriaDenganBobot' => $kriteriaDenganBobot,
            'persentaseBobot' => $persentaseBobot,
            'persentaseInterdependensi' => $persentaseInterdependensi,
            'progressPairwise' => $progressPairwise,
            'periodeAktif' => $periode,
            'progressData' => $progressData,
            'statusAnp' => $statusAnp
        ];
        
        return view('dashboard/tpp', $data);
    }
    
    private function hitungPersentaseInterdependensi()
    {
        try {
            $db = \Config\Database::connect();
            
            // Ambil periode aktif
            $periodeModel = new \App\Models\PeriodeModel();
            $periodeAktif = $periodeModel->where('status', 'aktif')->first();
            $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
            
            // Hitung total data interdependensi yang sudah diisi (nilai > 0)
            $builder = $db->table('anp_interdependensi');
            if ($periodeId) {
                $builder->where('periode_id', $periodeId);
            }
            $filledCount = $builder->where('nilai >', 0)->countAllResults();
            
            // Hitung total subkriteria
            $totalSubkriteria = $this->subkriteriaModel->countAll();
            
            // Total kemungkinan interdependensi = n × n (termasuk diagonal)
            $totalPossible = $totalSubkriteria * $totalSubkriteria;
            
            // Persentase = (filled / total) × 100
            $persentase = $totalPossible > 0 ? round(($filledCount / $totalPossible) * 100, 1) : 0;
            
            return $persentase;
        } catch (\Exception $e) {
            // Jika tabel belum ada atau error, return 0
            return 0;
        }
    }
    
    private function hitungProgressPairwise()
    {
        try {
            $db = \Config\Database::connect();
            
            // Ambil periode aktif
            $periodeModel = new \App\Models\PeriodeModel();
            $periodeAktif = $periodeModel->where('status', 'aktif')->first();
            $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
            
            // Hitung total pairwise yang sudah diisi
            $builder = $db->table('anp_pairwise_histori');
            if ($periodeId) {
                $builder->where('periode_id', $periodeId);
            }
            $filledCount = $builder->countAllResults();
            
            // Hitung total subkriteria
            $totalSubkriteria = $this->subkriteriaModel->countAll();
            
            // Total kemungkinan pairwise (tanpa diagonal dan reciprocal)
            // Untuk n subkriteria, total pairwise = n × (n-1) / 2
            $totalPossible = $totalSubkriteria > 0 ? ($totalSubkriteria * ($totalSubkriteria - 1)) / 2 : 0;
            
            // Persentase = (filled / total) × 100
            $persentase = $totalPossible > 0 ? round(($filledCount / $totalPossible) * 100, 1) : 0;
            
            return [
                'filled' => $filledCount,
                'total' => $totalPossible,
                'persentase' => $persentase
            ];
        } catch (\Exception $e) {
            // Jika tabel belum ada atau error, return default
            return [
                'filled' => 0,
                'total' => 0,
                'persentase' => 0
            ];
        }
    }
    
    private function getStatusAnpTerbaru()
    {
        try {
            $db = \Config\Database::connect();
            
            // Ambil periode aktif
            $periodeModel = new \App\Models\PeriodeModel();
            $periodeAktif = $periodeModel->where('status', 'aktif')->first();
            $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
            
            // Cek apakah sudah ada perhitungan ANP untuk periode ini
            $builder = $db->table('anp_interdependensi');
            if ($periodeId) {
                $builder->where('periode_id', $periodeId);
            }
            $hasAnpCalculation = $builder->countAllResults() > 0;
            
            if (!$hasAnpCalculation) {
                return [
                    'status' => 'belum_dihitung',
                    'message' => 'ANP belum dihitung untuk periode ini',
                    'color' => 'warning'
                ];
            }
            
            // Cek konsistensi dari tabel anp_interdependensi
            // (Dalam implementasi lengkap, bisa ambil dari tabel terpisah yang menyimpan hasil konsistensi)
            $builder = $db->table('anp_interdependensi');
            if ($periodeId) {
                $builder->where('periode_id', $periodeId);
            }
            $totalEntries = $builder->countAllResults();
            
            // Jika ada data interdependensi, anggap sudah dihitung
            if ($totalEntries > 0) {
                return [
                    'status' => 'sudah_dihitung',
                    'message' => 'ANP sudah dihitung',
                    'color' => 'success'
                ];
            }
            
            return [
                'status' => 'tidak_diketahui',
                'message' => 'Status ANP tidak diketahui',
                'color' => 'secondary'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
                'color' => 'danger'
            ];
        }
    }
}
