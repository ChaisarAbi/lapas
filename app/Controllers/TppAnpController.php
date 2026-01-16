<?php

namespace App\Controllers;

use App\Models\KriteriaModel;
use App\Models\AnpModel;
use App\Models\PeriodeModel;

class TppAnpController extends BaseController
{
    protected $kriteriaModel;
    protected $anpModel;
    protected $periodeModel;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        $this->anpModel = new AnpModel();
        $this->periodeModel = new PeriodeModel();
    }

    public function index()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
        
        // Ambil semua kriteria
        $kriteria = $this->kriteriaModel->findAll();
        
        // Ambil data interdependensi ANP
        $interdependensi = $this->anpModel->getElementInterdependensi($periodeId);
        
        // Jika tidak ada data interdependensi, buat default
        if (empty($interdependensi)) {
            $interdependensi = $this->buatInterdependensiDefault($kriteria, $periodeId);
        }
        
        // Bangun supermatrix dari interdependensi
        $clusters = $this->getClusters();
        $supermatrix = $this->anpModel->buildSupermatrix($kriteria, $interdependensi, $clusters);
        
        // Hitung hasil ANP lengkap
        $hasilAnp = $this->hitungANPLengkap($supermatrix, $kriteria);
        
        $data = [
            'title' => 'Hasil Analytic Network Process (ANP) - SPK Pembinaan',
            'kriteria' => $kriteria,
            'interdependensi' => $interdependensi,
            'hasilAnp' => $hasilAnp,
            'periode' => $periodeAktif,
            'activeMenu' => 'hasil-anp'
        ];
        
        return view('tpp_anp/index', $data);
    }

    private function getClusters()
    {
        // Ambil clusters dari database
        $db = \Config\Database::connect();
        return $db->table('anp_clusters')->orderBy('urutan', 'ASC')->get()->getResultArray();
    }

    private function buatInterdependensiDefault($kriteria, $periodeId = null)
    {
        $interdependensi = [];
        $n = count($kriteria);
        
        // Buat interdependensi default: hanya self-comparison
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $interdependensi[] = [
                    'cluster_id_dari' => 1, // Default cluster
                    'cluster_id_ke' => 1,
                    'kriteria_id_dari' => $kriteria[$i]['id'],
                    'kriteria_id_ke' => $kriteria[$j]['id'],
                    'nilai' => ($i == $j) ? 1.0 : 0.0, // Diagonal = 1, lainnya = 0
                    'tipe' => 'element_to_element',
                    'periode_id' => $periodeId
                ];
            }
        }
        
        return $interdependensi;
    }

    private function hitungANPLengkap($supermatrix, $kriteria)
    {
        $n = count($supermatrix);
        
        // 1. Hitung konsistensi
        $konsistensi = $this->anpModel->calculateConsistency($supermatrix);
        
        // 2. Normalisasi supermatrix (unweighted supermatrix)
        $unweightedSupermatrix = $this->anpModel->normalizeSupermatrix($supermatrix);
        
        // 3. Buat weighted supermatrix (dalam ANP sederhana, sama dengan unweighted)
        $weightedSupermatrix = $unweightedSupermatrix;
        
        // 4. Hitung limit supermatrix (konvergensi)
        $limitSupermatrix = $this->anpModel->calculateLimitSupermatrix($weightedSupermatrix);
        
        // 5. Ekstrak bobot dari limit supermatrix
        $bobot = $this->anpModel->extractWeights($limitSupermatrix, $kriteria);
        
        // 6. Hitung bobot akhir (normalisasi)
        $totalBobot = array_sum(array_column($bobot, 'weight'));
        $bobotAkhir = [];
        foreach ($bobot as $item) {
            $bobotAkhir[] = $totalBobot > 0 ? $item['weight'] / $totalBobot : 0;
        }
        
        return [
            'n' => $n,
            'lambda_max' => $konsistensi['lambda_max'],
            'ci' => $konsistensi['ci'],
            'ri' => $konsistensi['ri'],
            'cr' => $konsistensi['cr'],
            'konsisten' => $konsistensi['konsisten'],
            'bobot' => $bobot,
            'bobot_akhir' => $bobotAkhir,
            'supermatrix' => $supermatrix,
            'unweighted_supermatrix' => $unweightedSupermatrix,
            'weighted_supermatrix' => $weightedSupermatrix,
            'limit_supermatrix' => $limitSupermatrix
        ];
    }

    public function inputInterdependensi()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
        
        // Ambil semua kriteria
        $kriteria = $this->kriteriaModel->findAll();
        
        // Ambil data interdependensi yang sudah ada
        $interdependensi = $this->anpModel->getElementInterdependensi($periodeId);
        
        $data = [
            'title' => 'Input Matriks Interdependensi ANP - SPK Pembinaan',
            'kriteria' => $kriteria,
            'interdependensi' => $interdependensi,
            'periode' => $periodeAktif,
            'activeMenu' => 'input-interdependensi'
        ];
        
        return view('tpp_anp/input_interdependensi', $data);
    }

    public function simpanInterdependensi()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        if (!$periodeAktif) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
        }
        
        $periodeId = $periodeAktif['id'];
        $kriteria = $this->kriteriaModel->findAll();
        $n = count($kriteria);
        
        // Ambil data dari POST
        $postData = $this->request->getPost();
        $interdependensiData = [];
        
        // Proses data matriks interdependensi
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $key = "interdependensi_{$i}_{$j}";
                if (isset($postData[$key])) {
                    $nilai = floatval($postData[$key]);
                    if ($nilai > 0) {
                        $interdependensiData[] = [
                            'cluster_id_dari' => 1, // Default cluster
                            'cluster_id_ke' => 1,
                            'kriteria_id_dari' => $kriteria[$i]['id'],
                            'kriteria_id_ke' => $kriteria[$j]['id'],
                            'nilai' => $nilai,
                            'tipe' => 'element_to_element',
                            'periode_id' => $periodeId
                        ];
                    }
                }
            }
        }
        
        // Simpan ke database
        $saved = $this->anpModel->saveMatrix($interdependensiData, $periodeId);
        
        if ($saved > 0) {
            return redirect()->to('/tpp/anp')->with('success', "Matriks interdependensi ANP berhasil disimpan ($saved data)");
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan matriks interdependensi.');
        }
    }

    public function simpanBobotAkhir()
    {
        // Ambil bobot akhir dari POST
        $bobotAkhir = $this->request->getPost('bobot_akhir');
        $kriteriaIds = $this->request->getPost('kriteria_id');
        
        // Validasi: pastikan ada data
        if (empty($kriteriaIds) || empty($bobotAkhir)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data bobot yang dikirim');
        }
        
        // Validasi
        $validation = \Config\Services::validation();
        $validation->setRules([
            'bobot_akhir.*' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[1]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $updatedCount = 0;
        // Update bobot di database
        foreach ($kriteriaIds as $index => $id) {
            // Pastikan id dan bobot valid
            if (!empty($id) && isset($bobotAkhir[$index]) && $bobotAkhir[$index] !== '') {
                $data = [
                    'id' => $id,
                    'bobot' => floatval($bobotAkhir[$index])
                ];
                if ($this->kriteriaModel->save($data)) {
                    $updatedCount++;
                }
            }
        }
        
        if ($updatedCount === 0) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data yang berhasil diperbarui. Pastikan input valid.');
        }
        
        return redirect()->to('/tpp/anp')->with('success', 'Bobot akhir ANP berhasil disimpan ke database!');
    }
}
