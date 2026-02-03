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
        $matriks = [];
        
        // 1. Buat matriks keputusan dan hitung preferensi
        foreach ($narapidana as $napi) {
            $totalNilai = 0;
            $totalBobot = 0;
            $row = [];
            
            // Group penilaian untuk narapidana ini
            $penilaianNapi = array_filter($penilaian, function($p) use ($napi) {
                return $p['narapidana_id'] == $napi['id'];
            });
            
            // Hitung rata-rata nilai per kriteria dari subkriteria
            foreach ($kriteria as $k) {
                $nilaiKriteria = 0;
                $countSubkriteria = 0;
                
                // Cari semua penilaian untuk subkriteria dari kriteria ini
                foreach ($penilaianNapi as $p) {
                    if (isset($p['subkriteria_id'])) {
                        $nilaiKriteria += (float)$p['nilai'];
                        $countSubkriteria++;
                    }
                }
                
                // Hitung rata-rata jika ada subkriteria
                $nilai = $countSubkriteria > 0 ? $nilaiKriteria / $countSubkriteria : 0;
                
                // Normalisasi nilai 0-100 ke 0-1
                $nilaiNormalized = $nilai / 100;
                $row[] = $nilaiNormalized;
                
                // Kalikan dengan bobot kriteria untuk preferensi
                $totalNilai += $nilaiNormalized * (float)$k['bobot'];
                $totalBobot += (float)$k['bobot'];
            }
            
            $preferensi = $totalBobot > 0 ? $totalNilai / $totalBobot : 0;
            $matriks[] = $row;
            
            $hasil[] = [
                'narapidana' => $napi,
                'preferensi' => $preferensi,
                'd_positif' => 0, // Default value
                'd_negatif' => 0, // Default value
                'row' => $row // Simpan row untuk perhitungan jarak
            ];
        }
        
        // 2. Hitung jarak D+ dan D- untuk tampilan
        $jumlahKolom = count($kriteria);
        
        if (count($matriks) > 0 && $jumlahKolom > 0) {
            // Hitung solusi ideal berdasarkan nilai normalisasi
            $idealPositif = [];
            $idealNegatif = [];
            
            for ($j = 0; $j < $jumlahKolom; $j++) {
                $kolom = array_column($matriks, $j);
                $kolomMin = min($kolom);
                $kolomMax = max($kolom);
                
                if ($kriteria[$j]['jenis'] == 'Benefit') {
                    $idealPositif[$j] = $kolomMax;
                    $idealNegatif[$j] = $kolomMin;
                } else { // Cost
                    $idealPositif[$j] = $kolomMin;
                    $idealNegatif[$j] = $kolomMax;
                }
            }
            
            // Hitung jarak untuk setiap alternatif
            for ($i = 0; $i < count($hasil); $i++) {
                $dPositif = 0;
                $dNegatif = 0;
                
                for ($j = 0; $j < $jumlahKolom; $j++) {
                    $diffPositif = $matriks[$i][$j] - $idealPositif[$j];
                    $diffNegatif = $matriks[$i][$j] - $idealNegatif[$j];
                    $dPositif += pow($diffPositif, 2);
                    $dNegatif += pow($diffNegatif, 2);
                }
                
                $dPositif = sqrt($dPositif);
                $dNegatif = sqrt($dNegatif);
                
                // Tambahkan D+ dan D- ke hasil (hanya untuk tampilan)
                $hasil[$i]['d_positif'] = $dPositif;
                $hasil[$i]['d_negatif'] = $dNegatif;
                
                // Hapus row karena tidak diperlukan lagi
                unset($hasil[$i]['row']);
            }
        } else {
            // Jika tidak ada data, set D+ dan D- ke 0
            foreach ($hasil as &$item) {
                $item['d_positif'] = 0;
                $item['d_negatif'] = 0;
            }
        }
        
        // 3. Urutkan berdasarkan preferensi tertinggi
        usort($hasil, function($a, $b) {
            return $b['preferensi'] <=> $a['preferensi'];
        });
        
        return $hasil;
    }
}
