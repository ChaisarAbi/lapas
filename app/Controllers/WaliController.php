<?php

namespace App\Controllers;

use App\Models\NarapidanaModel;
use App\Models\PenilaianModel;

class WaliController extends BaseController
{
    protected $narapidanaModel;
    protected $penilaianModel;
    
    public function __construct()
    {
        $this->narapidanaModel = new NarapidanaModel();
        $this->penilaianModel = new PenilaianModel();
    }

    public function dashboard()
    {
        $periode = date('Y-m');
        
        // Ambil data untuk perhitungan TOPSIS
        $narapidana = $this->narapidanaModel->getAktif();
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        $topRanking = [];
        $totalNarapidana = count($narapidana);
        $rataRataSkor = 0;
        $baikCount = 0;
        $perhatianCount = 0;
        
        if (!empty($penilaian)) {
            // Hitung TOPSIS sederhana untuk dashboard
            $ranking = $this->hitungRankingSederhana($narapidana, $kriteria, $penilaian);
            
            // Ambil top 5
            $topRanking = array_slice($ranking, 0, 5);
            
            // Hitung statistik
            $totalPreferensi = 0;
            foreach ($ranking as $item) {
                $totalPreferensi += $item['preferensi'];
                if ($item['preferensi'] >= 0.7) {
                    $baikCount++;
                } elseif ($item['preferensi'] < 0.5) {
                    $perhatianCount++;
                }
            }
            
            if (count($ranking) > 0) {
                $rataRataSkor = $totalPreferensi / count($ranking);
            }
        }
        
        $data = [
            'title' => 'Dashboard Wali Pemasyarakatan',
            'page_title' => 'Dashboard Wali Pembinaan',
            'dashboard_url' => 'wali/dashboard',
            'topRanking' => $topRanking,
            'totalNarapidana' => $totalNarapidana,
            'rataRataSkor' => $rataRataSkor,
            'baikCount' => $baikCount,
            'perhatianCount' => $perhatianCount,
            'periode' => $periode
        ];
        
        return view('dashboard/wali', $data);
    }

    public function hasil()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data penilaian detail
        $penilaian = $this->penilaianModel->getPenilaianDetail($periode);
        $periode_list = $this->penilaianModel->getPeriodeForDropdown();
        
        // Hitung statistik
        $totalNarapidana = count(array_unique(array_column($penilaian, 'narapidana_id')));
        $totalPenilaian = count($penilaian);
        
        // Hitung berdasarkan status
        $baikCount = 0;
        $cukupCount = 0;
        $perhatianCount = 0;
        
        foreach ($penilaian as $p) {
            if ($p['nilai'] >= 70) {
                $baikCount++;
            } elseif ($p['nilai'] >= 50) {
                $cukupCount++;
            } else {
                $perhatianCount++;
            }
        }
        
        $data = [
            'title' => 'Hasil Penilaian Narapidana',
            'page_title' => 'Hasil Penilaian Narapidana',
            'dashboard_url' => 'wali/dashboard',
            'activeMenu' => 'hasil',
            'penilaian' => $penilaian,
            'periode' => $periode,
            'periode_list' => $periode_list,
            'totalNarapidana' => $totalNarapidana,
            'totalPenilaian' => $totalPenilaian,
            'baikCount' => $baikCount,
            'cukupCount' => $cukupCount,
            'perhatianCount' => $perhatianCount
        ];
        
        return view('wali/hasil', $data);
    }
    
    private function hitungRankingSederhana($narapidana, $kriteria, $penilaian)
    {
        $hasil = [];
        
        foreach ($narapidana as $napi) {
            $totalNilai = 0;
            $totalBobot = 0;
            
            foreach ($kriteria as $k) {
                $nilai = 0;
                foreach ($penilaian as $p) {
                    if ($p['narapidana_id'] == $napi['id'] && $p['kriteria_id'] == $k['id']) {
                        $nilai = (float)$p['nilai'];
                        break;
                    }
                }
                
                // Normalisasi nilai 0-100 ke 0-1
                $nilaiNormalized = $nilai / 100;
                
                // Kalikan dengan bobot
                $totalNilai += $nilaiNormalized * (float)$k['bobot'];
                $totalBobot += (float)$k['bobot'];
            }
            
            $preferensi = $totalBobot > 0 ? $totalNilai / $totalBobot : 0;
            
            $hasil[] = [
                'narapidana' => $napi,
                'preferensi' => $preferensi
            ];
        }
        
        // Urutkan berdasarkan preferensi tertinggi
        usort($hasil, function($a, $b) {
            return $b['preferensi'] <=> $a['preferensi'];
        });
        
        return $hasil;
    }
}
