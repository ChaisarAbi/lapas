<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
    }

    /**
     * Menampilkan halaman login
     */
    public function login()
    {
        // Jika sudah login, redirect ke dashboard berdasarkan role
        if ($this->session->get('is_logged_in')) {
            return $this->redirectToDashboard();
        }

        return view('auth/login');
    }

    /**
     * Proses login
     */
    public function processLogin()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required',
            'password' => 'required'
        ], [
            'username' => [
                'required' => 'Username harus diisi'
            ],
            'password' => [
                'required' => 'Password harus diisi'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cari user berdasarkan username
        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah');
        }

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah');
        }

        // Buat session
        $sessionData = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'is_logged_in' => true
        ];
        $this->session->set($sessionData);

        return $this->redirectToDashboard();
    }

    /**
     * Redirect ke dashboard berdasarkan role
     */
    private function redirectToDashboard()
    {
        $role = $this->session->get('role');
        
        switch ($role) {
            case 'ADMIN':
                return redirect()->to('/admin/dashboard');
            case 'TPP':
                return redirect()->to('/tpp/dashboard');
            case 'BIMKEMASWAT':
                return redirect()->to('/bimkesmaswat/dashboard');
            case 'WALI_PEMASYARAKATAN':
                return redirect()->to('/wali/dashboard');
            case 'KEPALA_LAPAS':
                return redirect()->to('/kalapas/dashboard');
            default:
                return redirect()->to('/login');
        }
    }

    /**
     * Proses logout
     */
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }
}
