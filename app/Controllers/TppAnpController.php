<?php

namespace App\Controllers;

use App\Models\KriteriaModel;
use App\Models\SubkriteriaModel;
use App\Models\AnpModel;
use App\Models\PeriodeModel;

class TppAnpController extends BaseController
{
    protected $kriteriaModel;
    protected $subkriteriaModel;
    protected $anpModel;
    protected $periodeModel;

    public function __construct()
    {
        $this->kriteriaModel = new KriteriaModel();
        $this->subkriteriaModel = new SubkriteriaModel();
        $this->anpModel = new AnpModel();
        $this->periodeModel = new PeriodeModel();
    }

    public function index()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
        
        // Ambil semua subkriteria (node ANP)
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        
        // Ambil data interdependensi ANP
        $interdependensi = $this->anpModel->getElementInterdependensi($periodeId);
        
        // Jika tidak ada data interdependensi, buat default
        if (empty($interdependensi)) {
            $interdependensi = $this->buatInterdependensiDefault($subkriteria, $periodeId);
        }
        
        // Bangun supermatrix dari interdependensi
        $clusters = $this->getClusters();
        $supermatrix = $this->anpModel->buildSupermatrix($subkriteria, $interdependensi, $clusters);
        
        // Hitung hasil ANP lengkap
        $hasilAnp = $this->hitungANPLengkap($supermatrix, $subkriteria);
        
        $data = [
            'title' => 'Hasil Analytic Network Process (ANP) - SPK Pembinaan',
            'subkriteria' => $subkriteria,
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

    private function buatInterdependensiDefault($subkriteria, $periodeId = null)
    {
        $interdependensi = [];
        $n = count($subkriteria);
        
        // Buat interdependensi default: hanya self-comparison
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $interdependensi[] = [
                    'cluster_id_dari' => $subkriteria[$i]['kriteria_id'], // Cluster = kriteria_id
                    'cluster_id_ke' => $subkriteria[$j]['kriteria_id'],
                    'kriteria_id_dari' => $subkriteria[$i]['id'], // Node = subkriteria_id
                    'kriteria_id_ke' => $subkriteria[$j]['id'],
                    'nilai' => ($i == $j) ? 1.0 : 0.0, // Diagonal = 1, lainnya = 0
                    'tipe' => 'element_to_element',
                    'periode_id' => $periodeId
                ];
            }
        }
        
        return $interdependensi;
    }

    private function hitungANPLengkap($supermatrix, $subkriteria)
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
        $bobot = $this->anpModel->extractWeights($limitSupermatrix, $subkriteria);
        
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
        
        // Ambil semua subkriteria dengan info kriteria
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        
        // Ambil data interdependensi yang sudah ada
        $interdependensi = $this->anpModel->getElementInterdependensi($periodeId);
        
        $data = [
            'title' => 'Input Matriks Interdependensi ANP - SPK Pembinaan',
            'subkriteria' => $subkriteria,
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
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        $n = count($subkriteria);
        
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
                            'cluster_id_dari' => $subkriteria[$i]['kriteria_id'],
                            'cluster_id_ke' => $subkriteria[$j]['kriteria_id'],
                            'kriteria_id_dari' => $subkriteria[$i]['id'],
                            'kriteria_id_ke' => $subkriteria[$j]['id'],
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
        $subkriteriaIds = $this->request->getPost('subkriteria_id');
        
        // Validasi: pastikan ada data
        if (empty($subkriteriaIds) || empty($bobotAkhir)) {
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
        $bobotPerKriteria = []; // Untuk menghitung rata-rata bobot per kriteria
        
        // Update bobot di database (ke tabel subkriteria)
        foreach ($subkriteriaIds as $index => $id) {
            // Pastikan id dan bobot valid
            if (!empty($id) && isset($bobotAkhir[$index]) && $bobotAkhir[$index] !== '') {
                $bobot = floatval($bobotAkhir[$index]);
                
                // Ambil data subkriteria untuk mendapatkan kriteria_id
                $subkriteria = $this->subkriteriaModel->find($id);
                if ($subkriteria) {
                    $kriteriaId = $subkriteria['kriteria_id'];
                    
                    // Simpan bobot ke subkriteria
                    $data = [
                        'id' => $id,
                        'bobot' => $bobot
                    ];
                    if ($this->subkriteriaModel->save($data)) {
                        $updatedCount++;
                        
                        // Kumpulkan bobot per kriteria untuk menghitung rata-rata
                        if (!isset($bobotPerKriteria[$kriteriaId])) {
                            $bobotPerKriteria[$kriteriaId] = [
                                'total' => 0,
                                'count' => 0
                            ];
                        }
                        $bobotPerKriteria[$kriteriaId]['total'] += $bobot;
                        $bobotPerKriteria[$kriteriaId]['count']++;
                    }
                }
            }
        }
        
        // Update bobot kriteria berdasarkan rata-rata bobot subkriteria
        foreach ($bobotPerKriteria as $kriteriaId => $data) {
            if ($data['count'] > 0) {
                $rataRata = $data['total'] / $data['count'];
                $this->kriteriaModel->update($kriteriaId, ['bobot' => $rataRata]);
            }
        }
        
        if ($updatedCount === 0) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada data yang berhasil diperbarui. Pastikan input valid.');
        }
        
        return redirect()->to('/tpp/anp')->with('success', 'Bobot akhir ANP berhasil disimpan ke database! (Subkriteria dan Kriteria telah diperbarui)');
    }
}
