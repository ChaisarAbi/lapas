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
            // Hitung ranking sederhana
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
        
        // Ambil data untuk perhitungan TOPSIS
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        if (empty($penilaian)) {
            return redirect()->to('kalapas/validasi')->with('error', 'Tidak ada data penilaian untuk periode ' . $periode);
        }
        
        // Ambil hanya narapidana yang memiliki penilaian di periode ini
        $narapidanaIds = array_unique(array_column($penilaian, 'narapidana_id'));
        $narapidana = $this->narapidanaModel->whereIn('id', $narapidanaIds)->findAll();
        
        // Hitung TOPSIS menggunakan RankingController
        $rankingController = new \App\Controllers\RankingController();
        $hasil = $rankingController->hitungTOPSIS($narapidana, $kriteria, $penilaian);
        
        // Urutkan ranking
        usort($hasil, function($a, $b) {
            return $b['preferensi'] <=> $a['preferensi'];
        });
        
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
        
        // Ambil data untuk perhitungan TOPSIS
        $kriteria = $this->penilaianModel->getKriteria();
        $penilaian = $this->penilaianModel->getPenilaianByPeriode($periode);
        
        if (empty($penilaian)) {
            return redirect()->to('kalapas/validasi')->with('error', 'Tidak ada data penilaian untuk periode ' . $periode);
        }
        
        // Ambil hanya narapidana yang memiliki penilaian di periode ini
        $narapidanaIds = array_unique(array_column($penilaian, 'narapidana_id'));
        $narapidana = $this->narapidanaModel->whereIn('id', $narapidanaIds)->findAll();
        
        // Hitung TOPSIS menggunakan RankingController
        $rankingController = new \App\Controllers\RankingController();
        $hasil = $rankingController->hitungTOPSIS($narapidana, $kriteria, $penilaian);
        
        // Urutkan ranking
        usort($hasil, function($a, $b) {
            return $b['preferensi'] <=> $a['preferensi'];
        });
        
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
