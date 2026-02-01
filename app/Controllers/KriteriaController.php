<?php

namespace App\Controllers;

use App\Models\KriteriaModel;

class KriteriaController extends BaseController
{
    protected $kriteriaModel;
    
    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Kelola Kriteria',
            'page_title' => 'Kelola Kriteria Penilaian',
            'dashboard_url' => 'tpp/dashboard',
            'kriteria' => $this->kriteriaModel->findAll()
        ];
        
        return view('kriteria/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Tambah Kriteria',
            'page_title' => 'Tambah Kriteria Baru',
            'dashboard_url' => 'tpp/dashboard'
        ];
        
        return view('kriteria/create', $data);
    }
    
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'kode' => 'required|min_length[2]|max_length[10]|is_unique[kriteria.kode]',
            'nama' => 'required|min_length[3]|max_length[100]'
        ], [
            'kode' => [
                'required' => 'Kode kriteria harus diisi',
                'min_length' => 'Kode minimal 2 karakter',
                'max_length' => 'Kode maksimal 10 karakter',
                'is_unique' => 'Kode kriteria sudah digunakan'
            ],
            'nama' => [
                'required' => 'Nama kriteria harus diisi',
                'min_length' => 'Nama minimal 3 karakter',
                'max_length' => 'Nama maksimal 100 karakter'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama')
            // Bobot dan jenis dihapus sesuai permintaan user
            // Kriteria hanya untuk pengelompokan subkriteria (cluster)
        ];
        
        if ($this->kriteriaModel->save($data)) {
            return redirect()->to('/tpp/kriteria')->with('success', 'Kriteria berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kriteria');
        }
    }
    
    public function edit($id)
    {
        $kriteria = $this->kriteriaModel->find($id);
        
        if (!$kriteria) {
            return redirect()->to('/tpp/kriteria')->with('error', 'Kriteria tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Kriteria',
            'page_title' => 'Edit Kriteria',
            'dashboard_url' => 'tpp/dashboard',
            'kriteria' => $kriteria
        ];
        
        return view('kriteria/edit', $data);
    }
    
    public function update($id)
    {
        $kriteria = $this->kriteriaModel->find($id);
        
        if (!$kriteria) {
            return redirect()->to('/tpp/kriteria')->with('error', 'Kriteria tidak ditemukan');
        }
        
        $validation = \Config\Services::validation();
        
        // Gunakan validation rule is_unique dengan pengecualian untuk id ini
        $validation->setRules([
            'kode' => "required|min_length[2]|max_length[10]|is_unique[kriteria.kode,id,{$id}]",
            'nama' => 'required|min_length[3]|max_length[100]'
        ], [
            'kode' => [
                'required' => 'Kode kriteria harus diisi',
                'min_length' => 'Kode minimal 2 karakter',
                'max_length' => 'Kode maksimal 10 karakter',
                'is_unique' => 'Kode kriteria sudah digunakan oleh kriteria lain'
            ],
            'nama' => [
                'required' => 'Nama kriteria harus diisi',
                'min_length' => 'Nama minimal 3 karakter',
                'max_length' => 'Nama maksimal 100 karakter'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'id' => $id,
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama')
            // Jenis tetap Benefit, semua kriteria setara
            // Bobot diambil dari bobot global subkriteria ANP
        ];
        
        if ($this->kriteriaModel->save($data)) {
            return redirect()->to('/tpp/kriteria')->with('success', 'Kriteria berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kriteria');
        }
    }
    
    public function delete($id)
    {
        $kriteria = $this->kriteriaModel->find($id);
        
        if (!$kriteria) {
            return redirect()->to('/tpp/kriteria')->with('error', 'Kriteria tidak ditemukan');
        }
        
        if ($this->kriteriaModel->delete($id)) {
            return redirect()->to('/tpp/kriteria')->with('success', 'Kriteria berhasil dihapus');
        } else {
            return redirect()->to('/tpp/kriteria')->with('error', 'Gagal menghapus kriteria');
        }
    }
}
