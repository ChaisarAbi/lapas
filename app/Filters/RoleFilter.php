<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Before filter - dipanggil sebelum controller
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        
        // Jika tidak ada arguments (roles yang diizinkan), lanjutkan
        if (empty($arguments)) {
            return $request;
        }
        
        // Cek apakah user sudah login
        if (!$session->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Ambil role user dari session
        $userRole = $session->get('role');
        
        // Cek apakah role user ada dalam daftar roles yang diizinkan
        if (!in_array($userRole, $arguments)) {
            // Jika tidak diizinkan, redirect ke dashboard berdasarkan role
            return $this->redirectToDashboard($userRole);
        }
        
        // Jika role diizinkan, lanjutkan
        return $request;
    }

    /**
     * After filter - dipanggil setelah controller
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada action setelah controller
        return $response;
    }
    
    /**
     * Redirect ke dashboard berdasarkan role
     */
    private function redirectToDashboard($role)
    {
        switch ($role) {
            case 'ADMIN':
                return redirect()->to('/admin/dashboard')->with('error', 'Akses ditolak');
            case 'TPP':
                return redirect()->to('/tpp/dashboard')->with('error', 'Akses ditolak');
            case 'BIMKEMASWAT':
                return redirect()->to('/bimkesmaswat/dashboard')->with('error', 'Akses ditolak');
            case 'WALI_PEMASYARAKATAN':
                return redirect()->to('/wali/dashboard')->with('error', 'Akses ditolak');
            case 'KEPALA_LAPAS':
                return redirect()->to('/kalapas/dashboard')->with('error', 'Akses ditolak');
            default:
                return redirect()->to('/login')->with('error', 'Role tidak valid');
        }
    }
}
