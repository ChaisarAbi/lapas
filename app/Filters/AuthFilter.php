<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Before filter - dipanggil sebelum controller
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        
        // Cek apakah user sudah login
        if (!$session->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Jika sudah login, lanjutkan
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
}
