<?php

namespace App\Controllers;

use App\Models\KriteriaModel;
use App\Models\SubkriteriaModel;

class SubkriteriaController extends BaseController
{
    protected $kriteriaModel;
    protected $subkriteriaModel;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        $this->subkriteriaModel = new SubkriteriaModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Subkriteria - SPK Pembinaan',
            'subkriteria' => $this->subkriteriaModel->getWithKriteria(),
            'kriteria' => $this->kriteriaModel->findAll(),
            'activeMenu' => 'subkriteria'
        ];
        
        return view('subkriteria/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Subkriteria - SPK Pembinaan',
            'kriteria' => $this->kriteriaModel->findAll(),
            'activeMenu' => 'subkriteria'
        ];
        
        return view('subkriteria/create', $data);
    }

    public function store()
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'kriteria_id' => 'required|integer',
            'kode' => 'required|max_length[20]',
            'nama' => 'required|max_length[255]',
            'bobot' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[1]',
            'jenis' => 'required|in_list[Benefit,Cost]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Cek apakah kode sudah digunakan untuk kriteria yang sama
        $existing = $this->subkriteriaModel->where('kriteria_id', $this->request->getPost('kriteria_id'))
                                          ->where('kode', $this->request->getPost('kode'))
                                          ->first();
        
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Kode subkriteria sudah digunakan untuk kriteria ini.');
        }
        
        // Simpan data
        $data = [
            'kriteria_id' => $this->request->getPost('kriteria_id'),
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'bobot' => $this->request->getPost('bobot'),
            'jenis' => $this->request->getPost('jenis')
        ];
        
        $this->subkriteriaModel->save($data);
        
        return redirect()->to('/tpp/subkriteria')->with('success', 'Subkriteria berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $subkriteria = $this->subkriteriaModel->getByIdWithKriteria($id);
        
        if (!$subkriteria) {
            return redirect()->to('/tpp/subkriteria')->with('error', 'Subkriteria tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Subkriteria - SPK Pembinaan',
            'subkriteria' => $subkriteria,
            'kriteria' => $this->kriteriaModel->findAll(),
            'activeMenu' => 'subkriteria'
        ];
        
        return view('subkriteria/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'kriteria_id' => 'required|integer',
            'kode' => 'required|max_length[20]',
            'nama' => 'required|max_length[255]',
            'bobot' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[1]',
            'jenis' => 'required|in_list[Benefit,Cost]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Cek apakah kode sudah digunakan untuk kriteria yang sama (kecuali untuk record ini)
        $existing = $this->subkriteriaModel->where('kriteria_id', $this->request->getPost('kriteria_id'))
                                          ->where('kode', $this->request->getPost('kode'))
                                          ->where('id !=', $id)
                                          ->first();
        
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Kode subkriteria sudah digunakan untuk kriteria ini.');
        }
        
        // Update data
        $data = [
            'kriteria_id' => $this->request->getPost('kriteria_id'),
            'kode' => $this->request->getPost('kode'),
            'nama' => $this->request->getPost('nama'),
            'bobot' => $this->request->getPost('bobot'),
            'jenis' => $this->request->getPost('jenis')
        ];
        
        $this->subkriteriaModel->update($id, $data);
        
        return redirect()->to('/tpp/subkriteria')->with('success', 'Subkriteria berhasil diperbarui!');
    }

    public function delete($id)
    {
        $subkriteria = $this->subkriteriaModel->find($id);
        
        if (!$subkriteria) {
            return redirect()->to('/tpp/subkriteria')->with('error', 'Subkriteria tidak ditemukan.');
        }
        
        $this->subkriteriaModel->delete($id);
        
        return redirect()->to('/tpp/subkriteria')->with('success', 'Subkriteria berhasil dihapus!');
    }

    public function byKriteria($kriteria_id)
    {
        $kriteria = $this->kriteriaModel->find($kriteria_id);
        
        if (!$kriteria) {
            return redirect()->to('/tpp/subkriteria')->with('error', 'Kriteria tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Subkriteria ' . $kriteria['nama'] . ' - SPK Pembinaan',
            'kriteria' => $kriteria,
            'subkriteria' => $this->subkriteriaModel->getByKriteria($kriteria_id),
            'totalBobot' => $this->subkriteriaModel->getTotalBobotByKriteria($kriteria_id),
            'activeMenu' => 'subkriteria'
        ];
        
        return view('subkriteria/by_kriteria', $data);
    }

    public function updateBobot()
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'bobot.*' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[1]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Ambil data bobot dari form
        $bobotData = $this->request->getPost('bobot');
        $subkriteriaIds = $this->request->getPost('subkriteria_id');
        $kriteria_id = $this->request->getPost('kriteria_id');
        
        // Update bobot untuk setiap subkriteria
        foreach ($subkriteriaIds as $index => $id) {
            $data = [
                'bobot' => floatval($bobotData[$index])
            ];
            $this->subkriteriaModel->update($id, $data);
        }
        
        // Hitung total bobot baru
        $totalBobot = $this->subkriteriaModel->getTotalBobotByKriteria($kriteria_id);
        
        return redirect()->to('/tpp/subkriteria/by/' . $kriteria_id)->with('success', 'Bobot subkriteria berhasil diperbarui! Total bobot: ' . number_format($totalBobot, 3));
    }
}
