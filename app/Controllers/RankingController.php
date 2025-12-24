<?php

namespace App\Controllers;

use App\Models\NarapidanaModel;
use App\Models\PenilaianModel;

class RankingController extends BaseController
{
    protected $narapidanaModel;
    protected $penilaianModel;
    
    public function __construct()
    {
        $this->narapidanaModel = new NarapidanaModel();
        $this->penilaianModel = new PenilaianModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        $role = session()->get('role');
        
        // Ambil data untuk perhitungan TOPSIS
        $narapidana = $this->narapidanaModel->getAktif();
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        if (empty($penilaian)) {
            $data = [
                'title' => 'Ranking Narapidana',
                'page_title' => 'Ranking Narapidana',
                'dashboard_url' => $role == 'WALI_PEMASYARAKATAN' ? 'wali/dashboard' : 'kalapas/dashboard',
                'error' => 'Tidak ada data penilaian untuk periode ' . $periode,
                'periode' => $periode,
                'periode_list' => $this->penilaianModel->getPeriodeForDropdown()
            ];
            
            return view('ranking/index', $data);
        }
        
        // Hitung TOPSIS
        $hasil = $this->hitungTOPSIS($narapidana, $kriteria, $penilaian);
        $ranking = $this->urutkanRanking($hasil);
        
        $data = [
            'title' => 'Ranking Narapidana',
            'page_title' => 'Ranking Narapidana',
            'dashboard_url' => $role == 'WALI_PEMASYARAKATAN' ? 'wali/dashboard' : 'kalapas/dashboard',
            'narapidana' => $narapidana,
            'kriteria' => $kriteria,
            'periode' => $periode,
            'periode_list' => $this->penilaianModel->getPeriodeForDropdown(),
            'ranking' => $ranking,
            'role' => $role
        ];
        
        return view('ranking/index', $data);
    }
    
    public function detail($narapidana_id)
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        $role = session()->get('role');
        
        $narapidana = $this->narapidanaModel->find($narapidana_id);
        
        if (!$narapidana) {
            return redirect()->back()->with('error', 'Data narapidana tidak ditemukan');
        }
        
        // Ambil penilaian detail untuk narapidana ini
        $penilaian = $this->penilaianModel->getPenilaianDetail($periode);
        $penilaian_narapidana = array_filter($penilaian, function($p) use ($narapidana_id) {
            return $p['narapidana_id'] == $narapidana_id;
        });
        
        $data = [
            'title' => 'Detail Penilaian',
            'page_title' => 'Detail Penilaian Narapidana',
            'dashboard_url' => $role == 'WALI_PEMASYARAKATAN' ? 'wali/dashboard' : 'kalapas/dashboard',
            'narapidana' => $narapidana,
            'penilaian' => $penilaian_narapidana,
            'periode' => $periode,
            'role' => $role
        ];
        
        return view('ranking/detail', $data);
    }
    
    public function cetakLaporan()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data untuk perhitungan TOPSIS
        $narapidana = $this->narapidanaModel->getAktif();
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        if (empty($penilaian)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk dicetak');
        }
        
        // Hitung TOPSIS
        $hasil = $this->hitungTOPSIS($narapidana, $kriteria, $penilaian);
        $ranking = $this->urutkanRanking($hasil);
        
        $data = [
            'title' => 'Laporan Ranking Narapidana',
            'narapidana' => $narapidana,
            'kriteria' => $kriteria,
            'periode' => $periode,
            'ranking' => $ranking,
            'tanggal_cetak' => date('d/m/Y H:i:s')
        ];
        
        return view('ranking/cetak', $data);
    }
    
    private function hitungTOPSIS($narapidana, $kriteria, $penilaian)
    {
        $hasil = [];
        
        // 1. Buat matriks keputusan
        $matriks = [];
        foreach ($narapidana as $napi) {
            $row = [];
            foreach ($kriteria as $k) {
                $nilai = 0;
                foreach ($penilaian as $p) {
                    if ($p['narapidana_id'] == $napi['id'] && $p['kriteria_id'] == $k['id']) {
                        $nilai = (float)$p['nilai'];
                        break;
                    }
                }
                $row[] = $nilai;
            }
            $matriks[] = $row;
        }
        
        // 2. Normalisasi matriks
        $normalisasi = [];
        $jumlahKolom = count($kriteria);
        
        for ($j = 0; $j < $jumlahKolom; $j++) {
            $sumSquares = 0;
            for ($i = 0; $i < count($matriks); $i++) {
                $sumSquares += pow($matriks[$i][$j], 2);
            }
            $sqrtSum = sqrt($sumSquares);
            
            for ($i = 0; $i < count($matriks); $i++) {
                $normalisasi[$i][$j] = $sqrtSum > 0 ? $matriks[$i][$j] / $sqrtSum : 0;
            }
        }
        
        // 3. Matriks terbobot
        $terbobot = [];
        for ($i = 0; $i < count($normalisasi); $i++) {
            for ($j = 0; $j < $jumlahKolom; $j++) {
                $terbobot[$i][$j] = $normalisasi[$i][$j] * (float)$kriteria[$j]['bobot'];
            }
        }
        
        // 4. Solusi ideal positif dan negatif
        $idealPositif = [];
        $idealNegatif = [];
        
        for ($j = 0; $j < $jumlahKolom; $j++) {
            $kolom = array_column($terbobot, $j);
            
            if ($kriteria[$j]['jenis'] == 'Benefit') {
                $idealPositif[$j] = max($kolom);
                $idealNegatif[$j] = min($kolom);
            } else { // Cost
                $idealPositif[$j] = min($kolom);
                $idealNegatif[$j] = max($kolom);
            }
        }
        
        // 5. Hitung jarak ke solusi ideal
        for ($i = 0; $i < count($terbobot); $i++) {
            $dPositif = 0;
            $dNegatif = 0;
            
            for ($j = 0; $j < $jumlahKolom; $j++) {
                $dPositif += pow($terbobot[$i][$j] - $idealPositif[$j], 2);
                $dNegatif += pow($terbobot[$i][$j] - $idealNegatif[$j], 2);
            }
            
            $dPositif = sqrt($dPositif);
            $dNegatif = sqrt($dNegatif);
            
            // 6. Hitung nilai preferensi
            $preferensi = ($dPositif + $dNegatif) > 0 ? $dNegatif / ($dPositif + $dNegatif) : 0;
            
            $hasil[$i] = [
                'narapidana' => $narapidana[$i],
                'd_positif' => $dPositif,
                'd_negatif' => $dNegatif,
                'preferensi' => $preferensi
            ];
        }
        
        return $hasil;
    }
    
    private function urutkanRanking($hasil)
    {
        usort($hasil, function($a, $b) {
            return $b['preferensi'] <=> $a['preferensi'];
        });
        
        return $hasil;
    }
}
