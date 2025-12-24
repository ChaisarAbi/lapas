<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\NarapidanaModel;
use App\Models\PenilaianModel;

class AdminController extends BaseController
{
    protected $userModel;
    protected $narapidanaModel;
    protected $penilaianModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->narapidanaModel = new NarapidanaModel();
        $this->penilaianModel = new PenilaianModel();
    }
    
    public function dashboard()
    {
        // Ambil data statistik realtime
        $totalUsers = $this->userModel->countAll();
        $totalNarapidana = $this->narapidanaModel->countAll();
        $narapidanaAktif = $this->narapidanaModel->where('status', 'Aktif')->countAllResults();
        
        // Ambil data penilaian terbaru
        $periodeAktif = $this->penilaianModel->getPeriodeAktif();
        $periodeDefault = date('Y-m');
        
        if (!empty($periodeAktif) && isset($periodeAktif['periode'])) {
            $periode = $periodeAktif['periode'];
        } else {
            $periode = $periodeDefault;
        }
        
        $totalPenilaian = $this->penilaianModel->where('periode', $periode)->countAllResults();
        
        // Ambil data user berdasarkan role
        $usersByRole = $this->userModel->select('role, COUNT(*) as total')
            ->groupBy('role')
            ->findAll();
        
        $roleStats = [];
        foreach ($usersByRole as $item) {
            $roleStats[$item['role']] = $item['total'];
        }
        
        $data = [
            'title' => 'Dashboard Admin',
            'page_title' => 'Dashboard Admin',
            'dashboard_url' => 'admin/dashboard',
            'totalUsers' => $totalUsers,
            'totalNarapidana' => $totalNarapidana,
            'narapidanaAktif' => $narapidanaAktif,
            'totalPenilaian' => $totalPenilaian,
            'periodeAktif' => $periode,
            'roleStats' => $roleStats
        ];
        
        return view('dashboard/admin', $data);
    }
}
