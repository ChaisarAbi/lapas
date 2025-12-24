<?php

namespace App\Controllers;

use App\Models\NarapidanaModel;
use App\Models\KriteriaModel;
use App\Models\PenilaianModel;
use App\Models\PeriodeModel;

class PenilaianBimkesController extends BaseController
{
    protected $narapidanaModel;
    protected $kriteriaModel;
    protected $penilaianModel;
    protected $periodeModel;
    
    public function __construct()
    {
        $this->narapidanaModel = new NarapidanaModel();
        $this->kriteriaModel = new KriteriaModel();
        $this->penilaianModel = new PenilaianModel();
        $this->periodeModel = new PeriodeModel();
        helper(['form', 'url']);
    }
    
    public function index()
    {
        // Get periode aktif atau default ke bulan ini
        $periodeAktif = $this->penilaianModel->getPeriodeAktif();
        $defaultPeriode = date('Y-m');
        
        if (!empty($periodeAktif) && isset($periodeAktif['tahun']) && isset($periodeAktif['bulan'])) {
            $defaultPeriode = $periodeAktif['tahun'] . '-' . str_pad($periodeAktif['bulan'], 2, '0', STR_PAD_LEFT);
        }
        
        $periode = $this->request->getGet('periode') ?: $defaultPeriode;
        
        $data = [
            'title' => 'Input Nilai Penilaian',
            'page_title' => 'Input Nilai Penilaian Narapidana',
            'dashboard_url' => 'bimkesmaswat/dashboard',
            'narapidana' => $this->narapidanaModel->getAktif(),
            'kriteria' => $this->kriteriaModel->getOrdered(),
            'periode' => $periode,
            'periode_list' => $this->penilaianModel->getPeriodeForDropdown(),
            'periode_aktif' => $periodeAktif
        ];
        
        return view('penilaian_bimkes/index', $data);
    }
    
    public function save()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'narapidana_id' => 'required|integer',
            'periode' => 'required|min_length[6]|max_length[20]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $narapidana_id = $this->request->getPost('narapidana_id');
        $periode = $this->request->getPost('periode');
        $penilai_id = session()->get('user_id');
        
        // Ambil semua kriteria
        $kriteria = $this->kriteriaModel->findAll();
        
        $successCount = 0;
        $errorMessages = [];
        
        foreach ($kriteria as $k) {
            $fieldName = 'nilai_' . $k['id'];
            $nilai = $this->request->getPost($fieldName);
            
            if ($nilai !== null) {
                $data = [
                    'narapidana_id' => $narapidana_id,
                    'kriteria_id' => $k['id'],
                    'nilai' => $nilai,
                    'periode' => $periode,
                    'penilai_id' => $penilai_id
                ];
                
                if ($this->penilaianModel->savePenilaian($data)) {
                    $successCount++;
                } else {
                    $errorMessages[] = "Gagal menyimpan nilai untuk kriteria {$k['kode']}";
                }
            }
        }
        
        if ($successCount > 0) {
            $message = "Berhasil menyimpan {$successCount} nilai penilaian";
            if (!empty($errorMessages)) {
                $message .= '. ' . implode(', ', $errorMessages);
            }
            return redirect()->to('/bimkesmaswat/penilaian')->with('success', $message);
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan nilai penilaian');
        }
    }
    
    public function riwayat()
    {
        $periode = $this->request->getGet('periode');
        $narapidana_id = $this->request->getGet('narapidana_id');
        
        // Get all narapidana for dropdown
        $narapidana_list = $this->narapidanaModel->getAktif();
        
        // Get penilaian data
        $penilaian_data = [];
        if ($narapidana_id) {
            // Get penilaian for specific narapidana
            $penilaian_data = $this->penilaianModel->getPenilaianByNarapidana($narapidana_id, $periode);
        } else {
            // Get all penilaian grouped by narapidana
            $penilaian_data = $this->penilaianModel->getPenilaianGroupedByNarapidana($periode);
        }
        
        $data = [
            'title' => 'Riwayat Penilaian',
            'page_title' => 'Riwayat Penilaian Narapidana',
            'dashboard_url' => 'bimkesmaswat/dashboard',
            'penilaian' => $penilaian_data,
            'narapidana_list' => $narapidana_list,
            'periode_list' => $this->penilaianModel->getPeriodeForDropdown(),
            'selected_periode' => $periode,
            'selected_narapidana' => $narapidana_id
        ];
        
        return view('penilaian_bimkes/riwayat', $data);
    }
    
    public function edit($id)
    {
        $penilaian = $this->penilaianModel->find($id);
        
        if (!$penilaian) {
            return redirect()->to('/bimkesmaswat/penilaian/riwayat')->with('error', 'Data penilaian tidak ditemukan');
        }
        
        // Cek apakah penilai adalah user yang sedang login
        if ($penilaian['penilai_id'] != session()->get('user_id')) {
            return redirect()->to('/bimkesmaswat/penilaian/riwayat')->with('error', 'Anda tidak memiliki akses untuk mengedit penilaian ini');
        }
        
        $data = [
            'title' => 'Edit Nilai Penilaian',
            'page_title' => 'Edit Nilai Penilaian',
            'dashboard_url' => 'bimkesmaswat/dashboard',
            'penilaian' => $penilaian,
            'kriteria' => $this->kriteriaModel->find($penilaian['kriteria_id']),
            'narapidana' => $this->narapidanaModel->find($penilaian['narapidana_id'])
        ];
        
        return view('penilaian_bimkes/edit', $data);
    }
    
    public function update($id)
    {
        $penilaian = $this->penilaianModel->find($id);
        
        if (!$penilaian) {
            return redirect()->to('/bimkesmaswat/penilaian/riwayat')->with('error', 'Data penilaian tidak ditemukan');
        }
        
        // Cek apakah penilai adalah user yang sedang login
        if ($penilaian['penilai_id'] != session()->get('user_id')) {
            return redirect()->to('/bimkesmaswat/penilaian/riwayat')->with('error', 'Anda tidak memiliki akses untuk mengedit penilaian ini');
        }
        
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'nilai' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]'
        ], [
            'nilai' => [
                'required' => 'Nilai harus diisi',
                'decimal' => 'Nilai harus berupa angka desimal',
                'greater_than_equal_to' => 'Nilai minimal 0',
                'less_than_equal_to' => 'Nilai maksimal 100'
            ]
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'id' => $id,
            'nilai' => $this->request->getPost('nilai')
        ];
        
        if ($this->penilaianModel->save($data)) {
            return redirect()->to('/bimkesmaswat/penilaian/riwayat')->with('success', 'Nilai penilaian berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui nilai penilaian');
        }
    }
}
