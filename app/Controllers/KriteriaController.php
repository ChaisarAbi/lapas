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
            'nama' => 'required|min_length[3]|max_length[100]',
            'bobot' => 'required|decimal|greater_than[0]|less_than_equal_to[1]',
            'jenis' => 'required|in_list[Benefit,Cost]'
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
            ],
            'bobot' => [
                'required' => 'Bobot harus diisi',
                'decimal' => 'Bobot harus berupa angka desimal',
                'greater_than' => 'Bobot harus lebih dari 0',
                'less_than_equal_to' => 'Bobot maksimal 1'
            ],
            'jenis' => [
                'required' => 'Jenis kriteria harus dipilih',
                'in_list' => 'Jenis kriteria tidak valid'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'bobot' => $this->request->getPost('bobot'),
            'jenis' => $this->request->getPost('jenis')
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
        
        $validation->setRules([
            'kode' => 'required|min_length[2]|max_length[10]',
            'nama' => 'required|min_length[3]|max_length[100]',
            'bobot' => 'required|decimal|greater_than[0]|less_than_equal_to[1]',
            'jenis' => 'required|in_list[Benefit,Cost]'
        ], [
            'kode' => [
                'required' => 'Kode kriteria harus diisi',
                'min_length' => 'Kode minimal 2 karakter',
                'max_length' => 'Kode maksimal 10 karakter'
            ],
            'nama' => [
                'required' => 'Nama kriteria harus diisi',
                'min_length' => 'Nama minimal 3 karakter',
                'max_length' => 'Nama maksimal 100 karakter'
            ],
            'bobot' => [
                'required' => 'Bobot harus diisi',
                'decimal' => 'Bobot harus berupa angka desimal',
                'greater_than' => 'Bobot harus lebih dari 0',
                'less_than_equal_to' => 'Bobot maksimal 1'
            ],
            'jenis' => [
                'required' => 'Jenis kriteria harus dipilih',
                'in_list' => 'Jenis kriteria tidak valid'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Validasi manual untuk kode unik (kecuali untuk kriteria ini)
        $kode = $this->request->getPost('kode');
        if ($kode !== $kriteria['kode']) {
            $existingKriteria = $this->kriteriaModel->where('kode', $kode)->first();
            if ($existingKriteria) {
                return redirect()->back()->withInput()->with('error', 'Kode kriteria sudah digunakan oleh kriteria lain');
            }
        }
        
        $data = [
            'id' => $id,
            'kode' => $kode,
            'nama' => $this->request->getPost('nama'),
            'bobot' => $this->request->getPost('bobot'),
            'jenis' => $this->request->getPost('jenis')
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
