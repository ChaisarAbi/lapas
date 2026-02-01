<?php

namespace App\Controllers;

use App\Models\KriteriaModel;

class TppBobotController extends BaseController
{
    protected $kriteriaModel;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
    }

    public function index()
    {
        // Redirect ke pairwise comparison ANP
        return redirect()->to('/tpp/anp/pairwise-comparison');
    }

    public function simpan()
    {
        // Ambil data bobot dari form
        $bobotData = $this->request->getPost('bobot');
        $kriteriaIds = $this->request->getPost('kriteria_id');
        
        // Validasi: pastikan ada data
        if (empty($kriteriaIds) || empty($bobotData)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data bobot yang dikirim');
        }
        
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'bobot.*' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[1]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $updatedCount = 0;
        // Update bobot untuk setiap kriteria
        foreach ($kriteriaIds as $index => $id) {
            // Pastikan id dan bobot valid
            if (!empty($id) && isset($bobotData[$index]) && $bobotData[$index] !== '') {
                $data = [
                    'id' => $id,
                    'bobot' => floatval($bobotData[$index])
                ];
                if ($this->kriteriaModel->save($data)) {
                    $updatedCount++;
                }
            }
        }
        
        if ($updatedCount === 0) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data yang berhasil diperbarui. Pastikan input valid.');
        }
        
        // Hitung total bobot baru
        $totalBobot = $this->kriteriaModel->getTotalBobot();
        
        // Simpan total bobot ke session untuk validasi
        session()->setFlashdata('totalBobot', $totalBobot);
        
        return redirect()->to('/tpp/bobot')->with('success', 'Bobot kriteria berhasil diperbarui! Total bobot: ' . number_format($totalBobot, 3));
    }

    public function matriksPerbandingan()
    {
        // Redirect ke pairwise comparison ANP
        return redirect()->to('/tpp/anp/pairwise-comparison');
    }

    public function simpanMatriks()
    {
        // Redirect ke pairwise comparison ANP
        return redirect()->to('/tpp/anp/pairwise-comparison');
    }

    public function konsistensi()
    {
        // Redirect ke pairwise comparison ANP
        return redirect()->to('/tpp/anp/pairwise-comparison');
    }
}
