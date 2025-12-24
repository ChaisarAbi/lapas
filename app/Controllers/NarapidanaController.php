<?php

namespace App\Controllers;

use App\Models\NarapidanaModel;

class NarapidanaController extends BaseController
{
    protected $narapidanaModel;
    
    public function __construct()
    {
        $this->narapidanaModel = new NarapidanaModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Manajemen Narapidana',
            'page_title' => 'Manajemen Data Narapidana',
            'dashboard_url' => 'admin/dashboard',
            'narapidana' => $this->narapidanaModel->findAll()
        ];
        
        return view('narapidana/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Tambah Narapidana',
            'page_title' => 'Tambah Data Narapidana Baru',
            'dashboard_url' => 'admin/dashboard'
        ];
        
        return view('narapidana/create', $data);
    }
    
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nomor_registrasi' => 'required|min_length[3]|max_length[50]|is_unique[narapidana.nomor_registrasi]',
            'nama_lengkap' => 'required|min_length[3]|max_length[100]',
            'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'tempat_lahir' => 'required|min_length[3]|max_length[50]',
            'tanggal_lahir' => 'required|valid_date',
            'alamat' => 'required|min_length[5]|max_length[255]',
            'kasus' => 'required|min_length[3]|max_length[255]',
            'masa_tahanan' => 'required|integer|greater_than[0]',
            'tanggal_masuk' => 'required|valid_date',
            'status' => 'required|in_list[Aktif,Bebas,Pindah]'
        ], [
            'nomor_registrasi' => [
                'required' => 'Nomor registrasi harus diisi',
                'min_length' => 'Nomor registrasi minimal 3 karakter',
                'max_length' => 'Nomor registrasi maksimal 50 karakter',
                'is_unique' => 'Nomor registrasi sudah digunakan'
            ],
            'nama_lengkap' => [
                'required' => 'Nama lengkap harus diisi',
                'min_length' => 'Nama lengkap minimal 3 karakter',
                'max_length' => 'Nama lengkap maksimal 100 karakter'
            ],
            'jenis_kelamin' => [
                'required' => 'Jenis kelamin harus dipilih',
                'in_list' => 'Jenis kelamin tidak valid'
            ],
            'tempat_lahir' => [
                'required' => 'Tempat lahir harus diisi',
                'min_length' => 'Tempat lahir minimal 3 karakter',
                'max_length' => 'Tempat lahir maksimal 50 karakter'
            ],
            'tanggal_lahir' => [
                'required' => 'Tanggal lahir harus diisi',
                'valid_date' => 'Tanggal lahir tidak valid'
            ],
            'alamat' => [
                'required' => 'Alamat harus diisi',
                'min_length' => 'Alamat minimal 5 karakter',
                'max_length' => 'Alamat maksimal 255 karakter'
            ],
            'kasus' => [
                'required' => 'Kasus harus diisi',
                'min_length' => 'Kasus minimal 3 karakter',
                'max_length' => 'Kasus maksimal 255 karakter'
            ],
            'masa_tahanan' => [
                'required' => 'Masa tahanan harus diisi',
                'integer' => 'Masa tahanan harus berupa angka',
                'greater_than' => 'Masa tahanan harus lebih dari 0'
            ],
            'tanggal_masuk' => [
                'required' => 'Tanggal masuk harus diisi',
                'valid_date' => 'Tanggal masuk tidak valid'
            ],
            'status' => [
                'required' => 'Status harus dipilih',
                'in_list' => 'Status tidak valid'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'nomor_registrasi' => $this->request->getPost('nomor_registrasi'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'alamat' => $this->request->getPost('alamat'),
            'kasus' => $this->request->getPost('kasus'),
            'masa_tahanan' => $this->request->getPost('masa_tahanan'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'status' => $this->request->getPost('status')
        ];
        
        if ($this->narapidanaModel->save($data)) {
            return redirect()->to('/admin/narapidana')->with('success', 'Data narapidana berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data narapidana');
        }
    }
    
    public function edit($id)
    {
        $narapidana = $this->narapidanaModel->find($id);
        
        if (!$narapidana) {
            return redirect()->to('/admin/narapidana')->with('error', 'Data narapidana tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Narapidana',
            'page_title' => 'Edit Data Narapidana',
            'dashboard_url' => 'admin/dashboard',
            'narapidana' => $narapidana
        ];
        
        return view('narapidana/edit', $data);
    }
    
    public function update($id)
    {
        $narapidana = $this->narapidanaModel->find($id);
        
        if (!$narapidana) {
            return redirect()->to('/admin/narapidana')->with('error', 'Data narapidana tidak ditemukan');
        }
        
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nomor_registrasi' => 'required|min_length[3]|max_length[50]',
            'nama_lengkap' => 'required|min_length[3]|max_length[100]',
            'jenis_kelamin' => 'required|in_list[Laki-laki,Perempuan]',
            'tempat_lahir' => 'required|min_length[3]|max_length[50]',
            'tanggal_lahir' => 'required|valid_date',
            'alamat' => 'required|min_length[5]|max_length[255]',
            'kasus' => 'required|min_length[3]|max_length[255]',
            'masa_tahanan' => 'required|integer|greater_than[0]',
            'tanggal_masuk' => 'required|valid_date',
            'status' => 'required|in_list[Aktif,Bebas,Pindah]'
        ], [
            'nomor_registrasi' => [
                'required' => 'Nomor registrasi harus diisi',
                'min_length' => 'Nomor registrasi minimal 3 karakter',
                'max_length' => 'Nomor registrasi maksimal 50 karakter'
            ],
            'nama_lengkap' => [
                'required' => 'Nama lengkap harus diisi',
                'min_length' => 'Nama lengkap minimal 3 karakter',
                'max_length' => 'Nama lengkap maksimal 100 karakter'
            ],
            'jenis_kelamin' => [
                'required' => 'Jenis kelamin harus dipilih',
                'in_list' => 'Jenis kelamin tidak valid'
            ],
            'tempat_lahir' => [
                'required' => 'Tempat lahir harus diisi',
                'min_length' => 'Tempat lahir minimal 3 karakter',
                'max_length' => 'Tempat lahir maksimal 50 karakter'
            ],
            'tanggal_lahir' => [
                'required' => 'Tanggal lahir harus diisi',
                'valid_date' => 'Tanggal lahir tidak valid'
            ],
            'alamat' => [
                'required' => 'Alamat harus diisi',
                'min_length' => 'Alamat minimal 5 karakter',
                'max_length' => 'Alamat maksimal 255 karakter'
            ],
            'kasus' => [
                'required' => 'Kasus harus diisi',
                'min_length' => 'Kasus minimal 3 karakter',
                'max_length' => 'Kasus maksimal 255 karakter'
            ],
            'masa_tahanan' => [
                'required' => 'Masa tahanan harus diisi',
                'integer' => 'Masa tahanan harus berupa angka',
                'greater_than' => 'Masa tahanan harus lebih dari 0'
            ],
            'tanggal_masuk' => [
                'required' => 'Tanggal masuk harus diisi',
                'valid_date' => 'Tanggal masuk tidak valid'
            ],
            'status' => [
                'required' => 'Status harus dipilih',
                'in_list' => 'Status tidak valid'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Validasi manual untuk nomor registrasi unik (kecuali untuk narapidana ini)
        $nomorRegistrasi = $this->request->getPost('nomor_registrasi');
        if ($nomorRegistrasi !== $narapidana['nomor_registrasi']) {
            $existingNarapidana = $this->narapidanaModel->where('nomor_registrasi', $nomorRegistrasi)->first();
            if ($existingNarapidana) {
                return redirect()->back()->withInput()->with('error', 'Nomor registrasi sudah digunakan oleh narapidana lain');
            }
        }
        
        $data = [
            'id' => $id,
            'nomor_registrasi' => $nomorRegistrasi,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'alamat' => $this->request->getPost('alamat'),
            'kasus' => $this->request->getPost('kasus'),
            'masa_tahanan' => $this->request->getPost('masa_tahanan'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'status' => $this->request->getPost('status')
        ];
        
        if ($this->narapidanaModel->save($data)) {
            return redirect()->to('/admin/narapidana')->with('success', 'Data narapidana berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data narapidana');
        }
    }
    
    public function delete($id)
    {
        $narapidana = $this->narapidanaModel->find($id);
        
        if (!$narapidana) {
            return redirect()->to('/admin/narapidana')->with('error', 'Data narapidana tidak ditemukan');
        }
        
        if ($this->narapidanaModel->delete($id)) {
            return redirect()->to('/admin/narapidana')->with('success', 'Data narapidana berhasil dihapus');
        } else {
            return redirect()->to('/admin/narapidana')->with('error', 'Gagal menghapus data narapidana');
        }
    }
}
