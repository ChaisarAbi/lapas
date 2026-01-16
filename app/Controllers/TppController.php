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
        
        // Untuk dashboard ANP, kita gunakan persentase bobot sebagai placeholder
        // (Dalam implementasi lengkap, hitung persentase interdependensi dari tabel anp_interdependensi)
        $persentaseInterdependensi = $persentaseBobot; // Placeholder
        
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
        
        $data = [
            'title' => 'Dashboard TPP',
            'page_title' => 'Dashboard Tim Pengamat Pemasyarakatan',
            'dashboard_url' => 'tpp/dashboard',
            'totalKriteria' => $totalKriteria,
            'totalSubkriteria' => $totalSubkriteria,
            'kriteriaDenganBobot' => $kriteriaDenganBobot,
            'persentaseBobot' => $persentaseBobot,
            'persentaseInterdependensi' => $persentaseInterdependensi,
            'periodeAktif' => $periode,
            'progressData' => $progressData
        ];
        
        return view('dashboard/tpp', $data);
    }
}
