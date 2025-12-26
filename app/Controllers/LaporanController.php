<?php

namespace App\Controllers;

use App\Models\LaporanModel;
use App\Models\PerhitunganModel;

class LaporanController extends BaseController
{
    protected $laporanModel;
    protected $perhitunganModel;
    
    public function __construct()
    {
        $this->laporanModel = new LaporanModel();
        $this->perhitunganModel = new PerhitunganModel();
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
        
        $data = [
            'title' => 'Preview Laporan Ranking',
            'page_title' => 'Preview Laporan Ranking Narapidana',
            'dashboard_url' => 'admin/dashboard',
            'periode' => $periode,
            'narapidana' => $dataLaporan['narapidana'],
            'kriteria' => $dataLaporan['kriteria'],
            'ranking' => $ranking,
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
        
        $data = [
            'title' => 'Laporan Ranking Narapidana',
            'narapidana' => $dataLaporan['narapidana'],
            'kriteria' => $dataLaporan['kriteria'],
            'periode' => $periode,
            'ranking' => $ranking,
            'tanggal_cetak' => date('d/m/Y H:i:s')
        ];
        
        return view('laporan/cetak_ranking', $data);
    }
    
    // Method untuk laporan validasi dan penilaian petugas dihapus sesuai permintaan user
    // untuk menghilangkan cetak laporan lama pada admin
}
