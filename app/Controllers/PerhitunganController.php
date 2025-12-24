<?php

namespace App\Controllers;

use App\Models\NarapidanaModel;
use App\Models\PenilaianModel;

class PerhitunganController extends BaseController
{
    protected $narapidanaModel;
    protected $penilaianModel;
    
    public function __construct()
    {
        $this->narapidanaModel = new NarapidanaModel();
        $this->penilaianModel = new PenilaianModel();
        helper(['form', 'url']);
    }
    
    public function topsis()
    {
        // Ambil periode aktif atau default ke bulan ini
        $periodeAktif = $this->penilaianModel->getPeriodeAktif();
        $periodeDefault = date('Y-m');
        
        if ($this->request->getGet('periode')) {
            $periode = $this->request->getGet('periode');
        } elseif (!empty($periodeAktif) && isset($periodeAktif['periode'])) {
            $periode = $periodeAktif['periode'];
        } else {
            $periode = $periodeDefault;
        }
        
        // Ambil data narapidana aktif
        $narapidana = $this->narapidanaModel->getAktif();
        
        // Ambil data kriteria
        $kriteria = $this->penilaianModel->getKriteria();
        
        // Ambil data penilaian untuk periode tertentu
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        // Ambil daftar periode untuk dropdown
        $periode_list = $this->penilaianModel->getPeriodeForDropdown();
        
        // Jika tidak ada data penilaian
        if (empty($penilaian)) {
            $data = [
                'title' => 'Perhitungan TOPSIS',
                'page_title' => 'Perhitungan Ranking dengan TOPSIS',
                'dashboard_url' => 'admin/dashboard',
                'error' => 'Tidak ada data penilaian untuk periode ' . $periode,
                'periode' => $periode,
                'periode_list' => $periode_list
            ];
            
            return view('perhitungan/topsis', $data);
        }
        
        // Proses perhitungan TOPSIS
        $hasil = $this->hitungTOPSIS($narapidana, $kriteria, $penilaian);
        
        $data = [
            'title' => 'Perhitungan TOPSIS',
            'page_title' => 'Perhitungan Ranking dengan TOPSIS',
            'dashboard_url' => 'admin/dashboard',
            'narapidana' => $narapidana,
            'kriteria' => $kriteria,
            'periode' => $periode,
            'periode_list' => $periode_list,
            'hasil' => $hasil,
            'ranking' => $this->urutkanRanking($hasil)
        ];
        
        return view('perhitungan/topsis', $data);
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
        // Asumsi semua kriteria adalah benefit (semakin tinggi nilai semakin baik)
        $idealPositif = [];
        $idealNegatif = [];
        
        for ($j = 0; $j < $jumlahKolom; $j++) {
            $kolom = array_column($terbobot, $j);
            $idealPositif[$j] = max($kolom);
            $idealNegatif[$j] = min($kolom);
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
    
    public function cetakLaporan()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data untuk laporan
        $narapidana = $this->narapidanaModel->getAktif();
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        if (empty($penilaian)) {
            return redirect()->to('/admin/perhitungan/topsis')->with('error', 'Tidak ada data untuk dicetak');
        }
        
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
        
        return view('perhitungan/cetak', $data);
    }
}
