<?php

namespace App\Controllers;

use App\Models\PeriodeModel;

class TppPeriodeController extends BaseController
{
    protected $periodeModel;

    public function __construct()
    {
        $this->periodeModel = new PeriodeModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Periode Penilaian',
            'page_title' => 'Kelola Periode Penilaian',
            'dashboard_url' => 'tpp/dashboard',
            'periodes' => $this->periodeModel->getAll(),
            'active_periode' => $this->periodeModel->getAktif()
        ];
        
        return view('tpp_periode/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Periode Penilaian',
            'page_title' => 'Tambah Periode Penilaian Baru',
            'dashboard_url' => 'tpp/dashboard'
        ];
        
        return view('tpp_periode/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nama_periode' => 'required|min_length[3]|max_length[100]',
            'tahun' => 'required|integer|greater_than[2020]',
            'bulan' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[12]',
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date',
            'status' => 'required|in_list[aktif,nonaktif,selesai]',
            'keterangan' => 'permit_empty|max_length[500]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'nama_periode' => $this->request->getPost('nama_periode'),
            'tahun' => $this->request->getPost('tahun'),
            'bulan' => $this->request->getPost('bulan'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        // Cek apakah sudah ada periode dengan tahun dan bulan yang sama
        $existing = $this->periodeModel->getByTahunBulan($data['tahun'], $data['bulan']);
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Periode dengan tahun dan bulan yang sama sudah ada.');
        }
        
        // Jika status aktif, nonaktifkan periode lain
        if ($data['status'] == 'aktif') {
            $this->periodeModel->where('status', 'aktif')->set('status', 'nonaktif')->update();
        }
        
        if ($this->periodeModel->insert($data)) {
            return redirect()->to('/tpp/periode')->with('success', 'Periode penilaian berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan periode penilaian.');
        }
    }

    public function edit($id)
    {
        $periode = $this->periodeModel->find($id);
        
        if (!$periode) {
            return redirect()->to('/tpp/periode')->with('error', 'Periode tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Periode Penilaian',
            'page_title' => 'Edit Periode Penilaian',
            'dashboard_url' => 'tpp/dashboard',
            'periode' => $periode
        ];
        
        return view('tpp_periode/edit', $data);
    }

    public function update($id)
    {
        $periode = $this->periodeModel->find($id);
        
        if (!$periode) {
            return redirect()->to('/tpp/periode')->with('error', 'Periode tidak ditemukan.');
        }
        
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nama_periode' => 'required|min_length[3]|max_length[100]',
            'tahun' => 'required|integer|greater_than[2020]',
            'bulan' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[12]',
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date',
            'status' => 'required|in_list[aktif,nonaktif,selesai]',
            'keterangan' => 'permit_empty|max_length[500]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'nama_periode' => $this->request->getPost('nama_periode'),
            'tahun' => $this->request->getPost('tahun'),
            'bulan' => $this->request->getPost('bulan'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        // Cek apakah sudah ada periode dengan tahun dan bulan yang sama (kecuali diri sendiri)
        $existing = $this->periodeModel->getByTahunBulan($data['tahun'], $data['bulan']);
        if ($existing && $existing['id'] != $id) {
            return redirect()->back()->withInput()->with('error', 'Periode dengan tahun dan bulan yang sama sudah ada.');
        }
        
        // Jika status diubah menjadi aktif, nonaktifkan periode lain
        if ($data['status'] == 'aktif' && $periode['status'] != 'aktif') {
            $this->periodeModel->where('status', 'aktif')->set('status', 'nonaktif')->update();
        }
        
        if ($this->periodeModel->update($id, $data)) {
            return redirect()->to('/tpp/periode')->with('success', 'Periode penilaian berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui periode penilaian.');
        }
    }

    public function delete($id)
    {
        $periode = $this->periodeModel->find($id);
        
        if (!$periode) {
            return redirect()->to('/tpp/periode')->with('error', 'Periode tidak ditemukan.');
        }
        
        // Cek apakah periode sedang aktif
        if ($periode['status'] == 'aktif') {
            return redirect()->to('/tpp/periode')->with('error', 'Tidak dapat menghapus periode yang sedang aktif.');
        }
        
        if ($this->periodeModel->delete($id)) {
            return redirect()->to('/tpp/periode')->with('success', 'Periode penilaian berhasil dihapus.');
        } else {
            return redirect()->to('/tpp/periode')->with('error', 'Gagal menghapus periode penilaian.');
        }
    }

    public function setActive($id)
    {
        $periode = $this->periodeModel->find($id);
        
        if (!$periode) {
            return redirect()->to('/tpp/periode')->with('error', 'Periode tidak ditemukan.');
        }
        
        // Nonaktifkan semua periode
        $this->periodeModel->where('status', 'aktif')->set('status', 'nonaktif')->update();
        
        // Aktifkan periode yang dipilih
        $data = ['status' => 'aktif'];
        
        if ($this->periodeModel->update($id, $data)) {
            return redirect()->to('/tpp/periode')->with('success', 'Periode berhasil diaktifkan.');
        } else {
            return redirect()->to('/tpp/periode')->with('error', 'Gagal mengaktifkan periode.');
        }
    }
}
