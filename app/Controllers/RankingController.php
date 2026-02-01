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
        
        // Ambil data untuk perhitungan
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
        
        // Ambil hanya narapidana yang memiliki penilaian di periode ini
        $narapidanaIds = array_unique(array_column($penilaian, 'narapidana_id'));
        $narapidana = $this->narapidanaModel->whereIn('id', $narapidanaIds)->findAll();
        
        // Untuk WALI_PEMASYARAKATAN, gunakan perhitungan sederhana seperti Kalapas
        // Untuk KEPALA_LAPAS, gunakan TOPSIS
        if ($role == 'WALI_PEMASYARAKATAN') {
            $hasil = $this->hitungRankingSederhana($narapidana, $kriteria, $penilaian);
        } else {
            $hasil = $this->hitungTOPSIS($narapidana, $kriteria, $penilaian);
        }
        
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
        $role = session()->get('role');
        
        // Jika role KEPALA_LAPAS, redirect ke halaman preview
        if ($role === 'KEPALA_LAPAS') {
            return redirect()->to('kalapas/preview-cetak?periode=' . $periode);
        }
        
        // Untuk role lain (WALI_PEMASYARAKATAN), langsung cetak
        // Ambil data untuk perhitungan TOPSIS
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        if (empty($penilaian)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk dicetak');
        }
        
        // Ambil hanya narapidana yang memiliki penilaian di periode ini
        $narapidanaIds = array_unique(array_column($penilaian, 'narapidana_id'));
        $narapidana = $this->narapidanaModel->whereIn('id', $narapidanaIds)->findAll();
        
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
    
    public function hitungTOPSIS($narapidana, $kriteria, $penilaian)
    {
        $hasil = [];
        
        // Cek jika tidak ada data
        if (empty($narapidana) || empty($kriteria) || empty($penilaian)) {
            return $hasil;
        }
        
        // 1. Buat matriks keputusan - hitung rata-rata nilai per kriteria dari subkriteria
        $matriks = [];
        foreach ($narapidana as $napi) {
            $row = [];
            
            // Group penilaian untuk narapidana ini
            $penilaianNapi = array_filter($penilaian, function($p) use ($napi) {
                return $p['narapidana_id'] == $napi['id'];
            });
            
            foreach ($kriteria as $k) {
                $nilaiKriteria = 0;
                $countSubkriteria = 0;
                
                // Cari semua penilaian untuk subkriteria dari kriteria ini
                foreach ($penilaianNapi as $p) {
                    // Periksa apakah penilaian ini untuk subkriteria dari kriteria ini
                    if (isset($p['subkriteria_id'])) {
                        // Untuk sementara, kita asumsikan semua penilaian valid
                        $nilaiKriteria += (float)$p['nilai'];
                        $countSubkriteria++;
                    }
                }
                
                // Hitung rata-rata jika ada subkriteria
                $nilai = $countSubkriteria > 0 ? $nilaiKriteria / $countSubkriteria : 0;
                
                // JANGAN normalisasi di sini! Biarkan nilai dalam skala asli (0-100)
                // Algoritma TOPSIS akan melakukan normalisasi sendiri
                $row[] = $nilai;
            }
            $matriks[] = $row;
        }
        
        // Debug: cek jika semua nilai sama (kasus khusus)
        $allSame = true;
        $firstValue = $matriks[0][0] ?? 0;
        foreach ($matriks as $row) {
            foreach ($row as $value) {
                if ($value != $firstValue) {
                    $allSame = false;
                    break 2;
                }
            }
        }
        
        // Jika semua nilai sama, berikan nilai preferensi 0.5 (netral)
        if ($allSame && count($matriks) > 0) {
            foreach ($narapidana as $index => $napi) {
                $hasil[$index] = [
                    'narapidana' => $napi,
                    'd_positif' => 0,
                    'd_negatif' => 0,
                    'preferensi' => 0.5 // Nilai netral untuk kasus semua sama
                ];
            }
            return $hasil;
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
            
            // Jika sqrtSum 0 (semua nilai di kolom 0), set ke 1 untuk hindari division by zero
            if ($sqrtSum == 0) {
                $sqrtSum = 1;
            }
            
            for ($i = 0; $i < count($matriks); $i++) {
                $normalisasi[$i][$j] = $matriks[$i][$j] / $sqrtSum;
            }
        }
        
        // 3. Matriks terbobot - gunakan bobot dari kriteria
        $terbobot = [];
        $totalBobot = 0;
        foreach ($kriteria as $k) {
            $totalBobot += (float)$k['bobot'];
        }
        
        // Jika total bobot 0, gunakan bobot default (1/n)
        $useDefaultBobot = ($totalBobot == 0);
        
        for ($i = 0; $i < count($normalisasi); $i++) {
            for ($j = 0; $j < $jumlahKolom; $j++) {
                $bobot = (float)$kriteria[$j]['bobot'];
                if ($useDefaultBobot) {
                    $bobot = 1 / $jumlahKolom;
                }
                $terbobot[$i][$j] = $normalisasi[$i][$j] * $bobot;
            }
        }
        
        // 4. Solusi ideal positif dan negatif
        $idealPositif = [];
        $idealNegatif = [];
        
        for ($j = 0; $j < $jumlahKolom; $j++) {
            $kolom = array_column($terbobot, $j);
            
            // Cek jika semua nilai di kolom sama
            $kolomMin = min($kolom);
            $kolomMax = max($kolom);
            
            if ($kolomMin == $kolomMax) {
                // Jika semua nilai sama, set ideal positif dan negatif sama
                $idealPositif[$j] = $kolomMax;
                $idealNegatif[$j] = $kolomMin;
            } else {
                if ($kriteria[$j]['jenis'] == 'Benefit') {
                    $idealPositif[$j] = $kolomMax;
                    $idealNegatif[$j] = $kolomMin;
                } else { // Cost
                    $idealPositif[$j] = $kolomMin;
                    $idealNegatif[$j] = $kolomMax;
                }
            }
        }
        
        // 5. Hitung jarak ke solusi ideal
        for ($i = 0; $i < count($terbobot); $i++) {
            $dPositif = 0;
            $dNegatif = 0;
            
            for ($j = 0; $j < $jumlahKolom; $j++) {
                $diffPositif = $terbobot[$i][$j] - $idealPositif[$j];
                $diffNegatif = $terbobot[$i][$j] - $idealNegatif[$j];
                $dPositif += pow($diffPositif, 2);
                $dNegatif += pow($diffNegatif, 2);
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
