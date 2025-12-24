<?php

namespace App\Controllers;

use App\Models\NarapidanaModel;
use App\Models\PenilaianModel;

class BimkesmaswatController extends BaseController
{
    protected $narapidanaModel;
    protected $penilaianModel;
    
    public function __construct()
    {
        $this->narapidanaModel = new NarapidanaModel();
        $this->penilaianModel = new PenilaianModel();
    }
    
    public function dashboard()
    {
        // Ambil periode aktif
        $periodeAktif = $this->penilaianModel->getPeriodeAktif();
        $periodeDefault = date('Y-m');
        
        if (!empty($periodeAktif) && isset($periodeAktif['periode'])) {
            $periode = $periodeAktif['periode'];
        } else {
            $periode = $periodeDefault;
        }
        
        // Ambil data narapidana aktif
        $narapidanaAktif = $this->narapidanaModel->where('status', 'Aktif')->findAll();
        $totalNarapidanaAktif = count($narapidanaAktif);
        
        // Ambil data penilaian untuk periode aktif
        $penilaianPeriode = $this->penilaianModel->where('periode', $periode)->findAll();
        
        // Hitung statistik
        $narapidanaSudahDinilai = [];
        $narapidanaBelumDinilai = [];
        
        foreach ($narapidanaAktif as $napi) {
            $sudahDinilai = false;
            foreach ($penilaianPeriode as $penilaian) {
                if ($penilaian['narapidana_id'] == $napi['id']) {
                    $sudahDinilai = true;
                    break;
                }
            }
            
            if ($sudahDinilai) {
                $narapidanaSudahDinilai[] = $napi;
            } else {
                $narapidanaBelumDinilai[] = $napi;
            }
        }
        
        $totalSudahDinilai = count($narapidanaSudahDinilai);
        $totalBelumDinilai = count($narapidanaBelumDinilai);
        $persentaseSelesai = $totalNarapidanaAktif > 0 ? round(($totalSudahDinilai / $totalNarapidanaAktif) * 100, 1) : 0;
        
        // Ambil data penilaian terbaru (5 terbaru)
        $penilaianTerbaru = $this->penilaianModel->select('penilaian.*, narapidana.nama_lengkap, narapidana.nomor_registrasi')
            ->join('narapidana', 'narapidana.id = penilaian.narapidana_id')
            ->where('periode', $periode)
            ->orderBy('penilaian.created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        $data = [
            'title' => 'Dashboard BIMKEMASWAT',
            'page_title' => 'Dashboard Bimbingan dan Perawatan',
            'dashboard_url' => 'bimkesmaswat/dashboard',
            'periodeAktif' => $periode,
            'totalNarapidanaAktif' => $totalNarapidanaAktif,
            'totalSudahDinilai' => $totalSudahDinilai,
            'totalBelumDinilai' => $totalBelumDinilai,
            'persentaseSelesai' => $persentaseSelesai,
            'narapidanaBelumDinilai' => $narapidanaBelumDinilai,
            'penilaianTerbaru' => $penilaianTerbaru
        ];
        
        return view('dashboard/bimkesmaswat', $data);
    }
}
