<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Manajemen User',
            'page_title' => 'Manajemen User',
            'dashboard_url' => 'admin/dashboard',
            'users' => $this->userModel->findAll()
        ];
        
        return view('user/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Tambah User',
            'page_title' => 'Tambah User Baru',
            'dashboard_url' => 'admin/dashboard'
        ];
        
        return view('user/create', $data);
    }
    
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'password_confirmation' => 'required|matches[password]',
            'nama_lengkap' => 'required|min_length[3]|max_length[100]',
            'role' => 'required|in_list[ADMIN,TPP,BIMKEMASWAT,WALI_PEMASYARAKATAN,KEPALA_LAPAS]'
        ], [
            'username' => [
                'required' => 'Username harus diisi',
                'min_length' => 'Username minimal 3 karakter',
                'max_length' => 'Username maksimal 50 karakter',
                'is_unique' => 'Username sudah digunakan'
            ],
            'password' => [
                'required' => 'Password harus diisi',
                'min_length' => 'Password minimal 6 karakter'
            ],
            'password_confirmation' => [
                'required' => 'Konfirmasi password harus diisi',
                'matches' => 'Konfirmasi password tidak sama'
            ],
            'nama_lengkap' => [
                'required' => 'Nama lengkap harus diisi',
                'min_length' => 'Nama lengkap minimal 3 karakter',
                'max_length' => 'Nama lengkap maksimal 100 karakter'
            ],
            'role' => [
                'required' => 'Role harus dipilih',
                'in_list' => 'Role tidak valid'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $this->request->getPost('role')
        ];
        
        if ($this->userModel->save($data)) {
            return redirect()->to('/admin/users')->with('success', 'User berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user');
        }
    }
    
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit User',
            'page_title' => 'Edit User',
            'dashboard_url' => 'admin/dashboard',
            'user' => $user
        ];
        
        return view('user/edit', $data);
    }
    
    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }
        
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]',
            'nama_lengkap' => 'required|min_length[3]|max_length[100]',
            'role' => 'required|in_list[ADMIN,TPP,BIMKEMASWAT,WALI_PEMASYARAKATAN,KEPALA_LAPAS]'
        ], [
            'username' => [
                'required' => 'Username harus diisi',
                'min_length' => 'Username minimal 3 karakter',
                'max_length' => 'Username maksimal 50 karakter'
            ],
            'nama_lengkap' => [
                'required' => 'Nama lengkap harus diisi',
                'min_length' => 'Nama lengkap minimal 3 karakter',
                'max_length' => 'Nama lengkap maksimal 100 karakter'
            ],
            'role' => [
                'required' => 'Role harus dipilih',
                'in_list' => 'Role tidak valid'
            ]
        ]);
        
        // Jika password diisi, validasi password
        if ($this->request->getPost('password')) {
            $validation->setRule('password', 'Password', 'min_length[6]');
            $validation->setRule('password_confirmation', 'Konfirmasi Password', 'matches[password]');
        }
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Validasi manual untuk username unik (kecuali untuk user ini)
        $username = $this->request->getPost('username');
        if ($username !== $user['username']) {
            $existingUser = $this->userModel->where('username', $username)->first();
            if ($existingUser) {
                return redirect()->back()->withInput()->with('error', 'Username sudah digunakan oleh user lain');
            }
        }
        
        $data = [
            'id' => $id,
            'username' => $username,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $this->request->getPost('role')
        ];
        
        // Jika password diisi, update password
        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }
        
        if ($this->userModel->save($data)) {
            return redirect()->to('/admin/users')->with('success', 'User berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui user');
        }
    }
    
    public function delete($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }
        
        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'User berhasil dihapus');
        } else {
            return redirect()->to('/admin/users')->with('error', 'Gagal menghapus user');
        }
    }
}
