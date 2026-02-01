<?php

namespace App\Controllers;

use App\Models\TopsisModel;
use App\Models\PenilaianModel;
use App\Models\PeriodeModel;

class TopsisController extends BaseController
{
    protected $topsisModel;
    protected $penilaianModel;
    protected $periodeModel;
    
    public function __construct()
    {
        $this->topsisModel = new TopsisModel();
        $this->penilaianModel = new PenilaianModel();
        $this->periodeModel = new PeriodeModel();
        helper(['form', 'url']);
    }
    
    /**
     * Halaman utama perhitungan TOPSIS
     */
    public function index()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        
        // Ambil daftar periode
        $periodeList = $this->periodeModel->orderBy('tahun', 'DESC')
                                         ->orderBy('bulan', 'DESC')
                                         ->findAll();
        
        $data = [
            'title' => 'Perhitungan TOPSIS - SPK Pembinaan',
            'page_title' => 'Perhitungan Metode TOPSIS',
            'dashboard_url' => 'bimkesmaswat/dashboard',
            'periode_aktif' => $periodeAktif,
            'periode_list' => $periodeList,
            'activeMenu' => 'topsis'
        ];
        
        return view('perhitungan/topsis_index', $data);
    }
    
    /**
     * Hitung TOPSIS untuk periode tertentu
     */
    public function hitung()
    {
        $periodeId = $this->request->getPost('periode_id');
        
        if (!$periodeId) {
            return redirect()->back()->with('error', 'Periode harus dipilih');
        }
        
        // Ambil data periode
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan');
        }
        
        // Format periode untuk query penilaian (YYYY-MM)
        $periodeFormat = $periode['tahun'] . '-' . str_pad($periode['bulan'], 2, '0', STR_PAD_LEFT);
        
        // Hitung TOPSIS
        $hasilTopsis = $this->topsisModel->hitungTopsis($periodeFormat);
        
        if (isset($hasilTopsis['error'])) {
            return redirect()->back()->with('error', $hasilTopsis['error']);
        }
        
        // Simpan hasil ke database
        $saved = $this->topsisModel->simpanHasil($periodeId, $hasilTopsis);
        
        if (!$saved) {
            return redirect()->back()->with('error', 'Gagal menyimpan hasil perhitungan');
        }
        
        // Tampilkan hasil dengan detail
        $data = [
            'title' => 'Hasil Perhitungan TOPSIS - SPK Pembinaan',
            'page_title' => 'Hasil Perhitungan TOPSIS',
            'dashboard_url' => 'bimkesmaswat/dashboard',
            'periode' => $periode,
            'hasil_topsis' => $hasilTopsis,
            'total_narapidana' => $hasilTopsis['total_narapidana'],
            'total_kriteria' => $hasilTopsis['total_kriteria'],
            'activeMenu' => 'topsis'
        ];
        
        return view('perhitungan/hasil_topsis', $data);
    }
    
    /**
     * Tampilkan detail perhitungan TOPSIS
     */
    public function detail($rankingId)
    {
        $detail = $this->topsisModel->getDetailPerhitungan($rankingId);
        
        if (!$detail) {
            return redirect()->back()->with('error', 'Detail perhitungan tidak ditemukan');
        }
        
        // Ambil data ranking
        $ranking = $this->topsisModel->find($rankingId);
        if (!$ranking) {
            return redirect()->back()->with('error', 'Data ranking tidak ditemukan');
        }
        
        $data = [
            'title' => 'Detail Perhitungan TOPSIS - SPK Pembinaan',
            'page_title' => 'Detail Perhitungan TOPSIS',
            'dashboard_url' => 'bimkesmaswat/dashboard',
            'detail' => $detail,
            'ranking' => $ranking,
            'activeMenu' => 'topsis'
        ];
        
        return view('perhitungan/detail_topsis', $data);
    }
    
    /**
     * Tampilkan riwayat hasil TOPSIS
     */
    public function riwayat()
    {
        $periodeId = $this->request->getGet('periode_id');
        
        // Ambil daftar periode
        $periodeList = $this->periodeModel->orderBy('tahun', 'DESC')
                                         ->orderBy('bulan', 'DESC')
                                         ->findAll();
        
        $hasilTopsis = [];
        if ($periodeId) {
            $hasilTopsis = $this->topsisModel->getHasilTopsis($periodeId);
        }
        
        $data = [
            'title' => 'Riwayat Hasil TOPSIS - SPK Pembinaan',
            'page_title' => 'Riwayat Hasil TOPSIS',
            'dashboard_url' => 'bimkesmaswat/dashboard',
            'periode_list' => $periodeList,
            'selected_periode' => $periodeId,
            'hasil_topsis' => $hasilTopsis,
            'activeMenu' => 'topsis-riwayat'
        ];
        
        return view('perhitungan/riwayat_topsis', $data);
    }
    
    /**
     * Export hasil TOPSIS ke PDF
     */
    public function exportPdf($periodeId)
    {
        $periode = $this->periodeModel->find($periodeId);
        if (!$periode) {
            return redirect()->back()->with('error', 'Periode tidak ditemukan');
        }
        
        $hasilTopsis = $this->topsisModel->getHasilTopsis($periodeId);
        
        if (empty($hasilTopsis)) {
            return redirect()->back()->with('error', 'Tidak ada data hasil TOPSIS untuk periode ini');
        }
        
        $data = [
            'title' => 'Laporan Hasil TOPSIS',
            'periode' => $periode,
            'hasil_topsis' => $hasilTopsis,
            'tanggal_cetak' => date('d F Y H:i:s')
        ];
        
        // Load view untuk PDF
        $html = view('perhitungan/cetak_topsis', $data);
        
        // Buat PDF (menggunakan Dompdf atau library PDF lainnya)
        // Untuk sekarang, kita tampilkan HTML dulu
        return $html;
    }
    
    /**
     * Validasi hasil TOPSIS (untuk Kalapas)
     */
    public function validasi($rankingId)
    {
        // Cek apakah user adalah Kalapas
        $userRole = session()->get('role');
        if ($userRole !== 'kalapas') {
            return redirect()->back()->with('error', 'Hanya Kalapas yang dapat melakukan validasi');
        }
        
        $ranking = $this->topsisModel->find($rankingId);
        if (!$ranking) {
            return redirect()->back()->with('error', 'Data ranking tidak ditemukan');
        }
        
        $status = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan');
        
        $data = [
            'id' => $rankingId,
            'status_validasi' => $status,
            'catatan_validasi' => $catatan,
            'validator_id' => session()->get('user_id'),
            'tanggal_validasi' => date('Y-m-d H:i:s')
        ];
        
        if ($this->topsisModel->save($data)) {
            return redirect()->back()->with('success', 'Hasil TOPSIS berhasil divalidasi');
        } else {
            return redirect()->back()->with('error', 'Gagal melakukan validasi');
        }
    }
}