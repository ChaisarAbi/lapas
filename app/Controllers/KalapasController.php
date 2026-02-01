<?php

namespace App\Controllers;

use App\Models\NarapidanaModel;
use App\Models\PenilaianModel;
use App\Models\ValidasiModel;

class KalapasController extends BaseController
{
    protected $narapidanaModel;
    protected $penilaianModel;
    protected $validasiModel;
    
    public function __construct()
    {
        $this->narapidanaModel = new NarapidanaModel();
        $this->penilaianModel = new PenilaianModel();
        $this->validasiModel = new ValidasiModel();
    }

    public function dashboard()
    {
        $periode = date('Y-m');
        
        // Ambil data untuk dashboard
        $narapidana = $this->narapidanaModel->getAktif();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        // Hitung statistik
        $totalNarapidana = count($narapidana);
        $totalPenilaian = count($penilaian);
        
        // Hitung status validasi (simulasi)
        $menungguValidasi = 0;
        $tervalidasi = 0;
        $perluTindakan = 0;
        
        if (!empty($penilaian)) {
            // Simulasi: 30% menunggu, 50% tervalidasi, 20% perlu tindakan
            $menungguValidasi = ceil($totalPenilaian * 0.3);
            $tervalidasi = ceil($totalPenilaian * 0.5);
            $perluTindakan = $totalPenilaian - $menungguValidasi - $tervalidasi;
        }
        
        $data = [
            'title' => 'Dashboard Kepala Lapas',
            'page_title' => 'Dashboard Kepala Lembaga Pemasyarakatan',
            'dashboard_url' => 'kalapas/dashboard',
            'totalNarapidana' => $totalNarapidana,
            'totalPenilaian' => $totalPenilaian,
            'menungguValidasi' => $menungguValidasi,
            'tervalidasi' => $tervalidasi,
            'perluTindakan' => $perluTindakan,
            'periode' => $periode
        ];
        
        return view('dashboard/kalapas', $data);
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
        
        $data = [
            'title' => 'Hasil Penilaian Narapidana',
            'page_title' => 'Hasil Penilaian Narapidana',
            'dashboard_url' => 'kalapas/dashboard',
            'activeMenu' => 'hasil',
            'penilaian' => $penilaian,
            'periode' => $periode,
            'periode_list' => $periode_list,
            'totalNarapidana' => $totalNarapidana,
            'totalPenilaian' => $totalPenilaian
        ];
        
        return view('kalapas/hasil', $data);
    }

    public function validasi()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data penilaian untuk periode tertentu
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        // Ambil hanya narapidana yang sudah ada penilaiannya
        $narapidanaIds = array_unique(array_column($penilaian, 'narapidana_id'));
        $narapidana = [];
        
        if (!empty($narapidanaIds)) {
            $narapidana = $this->narapidanaModel->whereIn('id', $narapidanaIds)->findAll();
        }
        
        $kriteria = $this->penilaianModel->getKriteria();
        
        $ranking = [];
        if (!empty($penilaian)) {
            // Hitung ranking menggunakan RATA-RATA SEDERHANA (konsisten dengan halaman lain)
            $ranking = $this->hitungRankingSederhana($narapidana, $kriteria, $penilaian);
        }
        
        $data = [
            'title' => 'Validasi Hasil Penilaian',
            'page_title' => 'Validasi Hasil Penilaian',
            'dashboard_url' => 'kalapas/dashboard',
            'activeMenu' => 'validasi',
            'ranking' => $ranking,
            'periode' => $periode,
            'periode_list' => $this->penilaianModel->getPeriodeForDropdown()
        ];
        
        return view('kalapas/validasi', $data);
    }

    public function simpanValidasi()
    {
        $periode = $this->request->getPost('periode');
        $validasiData = $this->request->getPost('validasi');
        $catatan = $this->request->getPost('catatan');
        $userId = session()->get('user_id');
        
        if ($validasiData) {
            foreach ($validasiData as $narapidanaId => $status) {
                $data = [
                    'periode' => $periode,
                    'narapidana_id' => $narapidanaId,
                    'status_validasi' => $status,
                    'catatan' => $catatan,
                    'validated_by' => $userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $this->validasiModel->saveValidasi($data);
            }
            
            return redirect()->to('kalapas/hasil-validasi?periode=' . $periode)->with('success', 'Validasi berhasil disimpan');
        }
        
        return redirect()->to('kalapas/validasi')->with('error', 'Tidak ada data validasi yang dikirim');
    }
    
    public function hasilValidasi()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data validasi dari database
        $validasi = $this->validasiModel->getValidasiByPeriode($periode);
        $statistik = $this->validasiModel->getStatistikValidasi($periode);
        
        $data = [
            'title' => 'Hasil Validasi',
            'page_title' => 'Hasil Validasi Penilaian',
            'dashboard_url' => 'kalapas/dashboard',
            'activeMenu' => 'hasil-validasi',
            'validasi' => $validasi,
            'statistik' => $statistik,
            'periode' => $periode,
            'periode_list' => $this->penilaianModel->getPeriodeForDropdown()
        ];
        
        return view('kalapas/hasil_validasi', $data);
    }

    public function riwayatValidasi()
    {
        // Ambil semua riwayat validasi dengan urutan terbaru
        $riwayat = $this->validasiModel->getRiwayatValidasi();
        
        $data = [
            'title' => 'Riwayat Validasi',
            'page_title' => 'Riwayat Validasi Penilaian',
            'dashboard_url' => 'kalapas/dashboard',
            'activeMenu' => 'riwayat-validasi',
            'riwayat' => $riwayat
        ];
        
        return view('kalapas/riwayat_validasi', $data);
    }
    
    /**
     * Preview cetak laporan ranking untuk kalapas
     */
    public function previewCetak()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data untuk perhitungan
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        if (empty($penilaian)) {
            return redirect()->to('kalapas/validasi')->with('error', 'Tidak ada data penilaian untuk periode ' . $periode);
        }
        
        // Ambil hanya narapidana yang memiliki penilaian di periode ini
        $narapidanaIds = array_unique(array_column($penilaian, 'narapidana_id'));
        $narapidana = $this->narapidanaModel->whereIn('id', $narapidanaIds)->findAll();
        
        // Hitung ranking menggunakan RATA-RATA SEDERHANA (konsisten dengan halaman validasi)
        $hasil = $this->hitungRankingSederhana($narapidana, $kriteria, $penilaian);
        
        $data = [
            'title' => 'Preview Cetak Laporan Ranking',
            'page_title' => 'Preview Cetak Laporan Ranking',
            'dashboard_url' => 'kalapas/dashboard',
            'activeMenu' => 'validasi',
            'narapidana' => $narapidana,
            'kriteria' => $kriteria,
            'periode' => $periode,
            'periode_list' => $this->penilaianModel->getPeriodeForDropdown(),
            'ranking' => $hasil
        ];
        
        return view('kalapas/preview_cetak', $data);
    }
    
    /**
     * Cetak laporan ranking untuk kalapas
     */
    public function cetakLaporan()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data untuk perhitungan
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        if (empty($penilaian)) {
            return redirect()->to('kalapas/validasi')->with('error', 'Tidak ada data penilaian untuk periode ' . $periode);
        }
        
        // Ambil hanya narapidana yang memiliki penilaian di periode ini
        $narapidanaIds = array_unique(array_column($penilaian, 'narapidana_id'));
        $narapidana = $this->narapidanaModel->whereIn('id', $narapidanaIds)->findAll();
        
        // Hitung ranking menggunakan RATA-RATA SEDERHANA (konsisten dengan halaman validasi)
        $hasil = $this->hitungRankingSederhana($narapidana, $kriteria, $penilaian);
        
        $data = [
            'title' => 'Laporan Ranking Narapidana',
            'narapidana' => $narapidana,
            'kriteria' => $kriteria,
            'periode' => $periode,
            'ranking' => $hasil,
            'tanggal_cetak' => date('d/m/Y H:i:s')
        ];
        
        return view('kalapas/cetak_laporan', $data);
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
        
        // 3. Urutkan berdasarkan preferensi tertinggi
        usort($hasil, function($a, $b) {
            return $b['preferensi'] <=> $a['preferensi'];
        });
        
        return $hasil;
    }
}
