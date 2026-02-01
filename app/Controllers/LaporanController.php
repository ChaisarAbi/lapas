<?php

namespace App\Controllers;

use App\Models\LaporanModel;
use App\Models\PerhitunganModel;
use App\Models\PeriodeModel;

class LaporanController extends BaseController
{
    protected $laporanModel;
    protected $perhitunganModel;
    protected $periodeModel;
    
    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
        $this->perhitunganModel = new PerhitunganModel();
        $this->periodeModel = new PeriodeModel();
        helper(['form', 'url']);
    }
    
    /**
     * Halaman utama laporan admin
     */
    public function index()
    {
        $periodeList = $this->laporanModel->getListPeriode();
        
        // Jika tidak ada periode, gunakan periode saat ini
        if (empty($periodeList)) {
            $periodeList = [date('Y-m')];
        }
        
        $data = [
            'title' => 'Manajemen Laporan',
            'page_title' => 'Manajemen Laporan',
            'dashboard_url' => 'admin/dashboard',
            'periode_list' => $periodeList,
            'petugas_list' => $this->laporanModel->getListPetugas()
        ];
        
        return view('laporan/index', $data);
    }
    
    /**
     * Preview laporan ranking
     */
    public function previewRanking()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data untuk preview
        $dataLaporan = $this->laporanModel->getDataLaporanRanking($periode);
        
        // Hitung ranking menggunakan model perhitungan
        $ranking = $this->perhitunganModel->hitungRankingTOPSIS(
            $dataLaporan['narapidana'],
            $dataLaporan['kriteria'],
            $dataLaporan['penilaian']
        );
        
        // Tambahkan status remisi ke data ranking
        $rankingWithStatus = $this->addRemisiStatus($ranking);
        
        $data = [
            'title' => 'Preview Laporan Ranking',
            'page_title' => 'Preview Laporan Ranking Narapidana',
            'dashboard_url' => 'admin/dashboard',
            'periode' => $periode,
            'narapidana' => $dataLaporan['narapidana'],
            'kriteria' => $dataLaporan['kriteria'],
            'ranking' => $rankingWithStatus,
            'periode_list' => $this->laporanModel->getListPeriode()
        ];
        
        return view('laporan/preview_ranking', $data);
    }
    
    /**
     * Cetak laporan ranking
     */
    public function cetakRanking()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        // Ambil data untuk cetak
        $dataLaporan = $this->laporanModel->getDataLaporanRanking($periode);
        
        // Hitung ranking menggunakan model perhitungan
        $ranking = $this->perhitunganModel->hitungRankingTOPSIS(
            $dataLaporan['narapidana'],
            $dataLaporan['kriteria'],
            $dataLaporan['penilaian']
        );
        
        // Tambahkan status remisi ke data ranking
        $rankingWithStatus = $this->addRemisiStatus($ranking);
        
        $data = [
            'title' => 'Laporan Ranking Narapidana',
            'narapidana' => $dataLaporan['narapidana'],
            'kriteria' => $dataLaporan['kriteria'],
            'periode' => $periode,
            'ranking' => $rankingWithStatus,
            'tanggal_cetak' => date('d/m/Y H:i:s')
        ];
        
        return view('laporan/cetak_ranking', $data);
    }
    
    /**
     * Tambahkan status remisi ke data ranking
     */
    private function addRemisiStatus($ranking)
    {
        $totalNarapidana = count($ranking);
        
        // Tentukan batas peringkat untuk setiap status
        $batasRemisiPenuh = ceil($totalNarapidana * 0.3); // 30% terbaik
        $batasRemisiSeparuh = ceil($totalNarapidana * 0.6); // 60% berikutnya
        
        foreach ($ranking as $index => $row) {
            $peringkat = $index + 1;
            
            if ($peringkat <= $batasRemisiPenuh) {
                $ranking[$index]['status'] = 'remisi_penuh';
                $ranking[$index]['status_text'] = 'Remisi Penuh';
                $ranking[$index]['status_class'] = 'badge-success';
            } elseif ($peringkat <= $batasRemisiSeparuh) {
                $ranking[$index]['status'] = 'remisi_separuh';
                $ranking[$index]['status_text'] = 'Remisi Separuh';
                $ranking[$index]['status_class'] = 'badge-warning';
            } else {
                $ranking[$index]['status'] = 'tidak_layak';
                $ranking[$index]['status_text'] = 'Tidak Layak';
                $ranking[$index]['status_class'] = 'badge-danger';
            }
        }
        
        return $ranking;
    }
    
    // Method untuk laporan validasi dan penilaian petugas dihapus sesuai permintaan user
    // untuk menghilangkan cetak laporan lama pada admin
}