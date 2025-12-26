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
    
    /**
     * Preview laporan validasi
     */
    public function previewValidasi()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        $dataLaporan = $this->laporanModel->getDataLaporanValidasi($periode);
        
        $data = [
            'title' => 'Preview Laporan Validasi',
            'page_title' => 'Preview Laporan Validasi Hasil',
            'dashboard_url' => 'admin/dashboard',
            'periode' => $periode,
            'validasi' => $dataLaporan['validasi'],
            'statistik' => $dataLaporan['statistik'],
            'periode_list' => $this->laporanModel->getListPeriode()
        ];
        
        return view('laporan/preview_validasi', $data);
    }
    
    /**
     * Cetak laporan validasi
     */
    public function cetakValidasi()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        
        $dataLaporan = $this->laporanModel->getDataLaporanValidasi($periode);
        
        $data = [
            'title' => 'Laporan Validasi Hasil',
            'periode' => $periode,
            'validasi' => $dataLaporan['validasi'],
            'statistik' => $dataLaporan['statistik'],
            'tanggal_cetak' => date('d/m/Y H:i:s')
        ];
        
        return view('laporan/cetak_validasi', $data);
    }
    
    /**
     * Preview laporan penilaian petugas
     */
    public function previewPenilaianPetugas()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        $petugasId = $this->request->getGet('petugas_id');
        
        $dataLaporan = $this->laporanModel->getDataLaporanPenilaianPetugas($periode, $petugasId);
        
        $data = [
            'title' => 'Preview Laporan Penilaian Petugas',
            'page_title' => 'Preview Laporan Penilaian per Petugas',
            'dashboard_url' => 'admin/dashboard',
            'periode' => $periode,
            'petugas_id' => $petugasId,
            'penilaian' => $dataLaporan['penilaian'],
            'statistik' => $dataLaporan['statistik'],
            'periode_list' => $this->laporanModel->getListPeriode(),
            'petugas_list' => $this->laporanModel->getListPetugas()
        ];
        
        return view('laporan/preview_penilaian_petugas', $data);
    }
    
    /**
     * Cetak laporan penilaian petugas
     */
    public function cetakPenilaianPetugas()
    {
        $periode = $this->request->getGet('periode') ?: date('Y-m');
        $petugasId = $this->request->getGet('petugas_id');
        
        $dataLaporan = $this->laporanModel->getDataLaporanPenilaianPetugas($periode, $petugasId);
        
        $data = [
            'title' => 'Laporan Penilaian Petugas',
            'periode' => $periode,
            'petugas_id' => $petugasId,
            'penilaian' => $dataLaporan['penilaian'],
            'statistik' => $dataLaporan['statistik'],
            'tanggal_cetak' => date('d/m/Y H:i:s')
        ];
        
        return view('laporan/cetak_penilaian_petugas', $data);
    }
}
