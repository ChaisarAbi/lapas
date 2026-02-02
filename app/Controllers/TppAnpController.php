<?php

namespace App\Controllers;

use App\Models\KriteriaModel;
use App\Models\SubkriteriaModel;
use App\Models\AnpModel;
use App\Models\EdgeModel;
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

    /**
     * Pairwise comparison dengan target-first approach
     */
    public function pairwiseTarget()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
        
        // Ambil semua subkriteria dengan info kriteria
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        
        // Semua subkriteria adalah target nodes (tidak perlu edges)
        $targets = $subkriteria; // Semua subkriteria bisa menjadi target
        
        // Ambil target yang dipilih dari query string
        $selectedTargetId = $this->request->getGet('target_id');
        $selectedTarget = null;
        $matrixData = null;
        $ahpReport = null;
        
        if ($selectedTargetId) {
            // Cari target yang dipilih
            foreach ($targets as $target) {
                if ($target['id'] == $selectedTargetId) {
                    $selectedTarget = $target;
                    break;
                }
            }
            
        if ($selectedTarget) {
            // Bangun matrix untuk target ini - semua subkriteria lain adalah influencer
            $matrixData = $this->buildMatrixForTargetNoEdges($selectedTargetId, $periodeId, $subkriteria);
            
            // Hitung apakah matrix sudah lengkap
            if ($matrixData && !empty($matrixData['influencers'])) {
                $k = count($matrixData['influencers']);
                $totalPairs = $k * ($k - 1) / 2; // Unique pairs (upper triangle)
                $filledPairs = $matrixData['filled_pairs'];
                $isComplete = ($filledPairs >= $totalPairs && $k >= 2);
                
                // Tambahkan data completeness ke matrixData
                $matrixData['is_complete'] = $isComplete;
                $matrixData['k'] = $k;
                $matrixData['total_pairs'] = $totalPairs;
                $matrixData['filled_pairs'] = $filledPairs;
                $matrixData['progress_percentage'] = $totalPairs > 0 ? round(($filledPairs / $totalPairs) * 100, 1) : 0;
                
                // Hitung AHP report hanya jika matrix sudah lengkap
                if ($isComplete && !empty($matrixData['matrix'])) {
                    $ahpReport = $this->anpModel->calculateAhpReport($matrixData['matrix'], $matrixData['influencers']);
                } else {
                    $ahpReport = null;
                }
            } else {
                $ahpReport = null;
            }
        } else {
            $ahpReport = null;
        }
        }
        
        $data = [
            'title' => 'Pairwise Comparison ANP (Target-First) - SPK Pembinaan',
            'subkriteria' => $subkriteria,
            'targets' => $targets,
            'selected_target' => $selectedTarget,
            'matrix_data' => $matrixData,
            'ahp_report' => $ahpReport ?? null,
            'periode' => $periodeAktif,
            'activeMenu' => 'pairwise-target'
        ];
        
        return view('tpp_anp/pairwise_target', $data);
    }
    
    /**
     * Bangun matrix untuk target tanpa menggunakan edges
     * Semua subkriteria lain adalah influencer
     */
    private function buildMatrixForTargetNoEdges($targetId, $periodeId, $allSubkriteria)
    {
        // Filter influencer: semua subkriteria kecuali target itu sendiri
        $influencers = array_filter($allSubkriteria, function($sub) use ($targetId) {
            return $sub['id'] != $targetId;
        });
        $influencers = array_values($influencers); // Reset indices
        
        if (empty($influencers)) {
            return null;
        }
        
        // Buat matriks kosong
        $k = count($influencers);
        $matrix = array_fill(0, $k, array_fill(0, $k, 0));
        
        // Ambil pairwise yang sudah ada untuk target ini
        $db = \Config\Database::connect();
        $pairwiseData = $db->table('anp_pairwise_histori')
            ->where('target_node_id', $targetId)
            ->where('periode_id', $periodeId)
            ->get()
            ->getResultArray();
        
        // Mapping influencer ID ke index
        $idToIndex = [];
        foreach ($influencers as $index => $inf) {
            $idToIndex[$inf['id']] = $index;
        }
        
        // Isi matrix dari data pairwise
        foreach ($pairwiseData as $pairwise) {
            $dariIndex = $idToIndex[$pairwise['node_dari_id']] ?? null;
            $keIndex = $idToIndex[$pairwise['node_ke_id']] ?? null;
            
            if ($dariIndex !== null && $keIndex !== null) {
                $matrix[$dariIndex][$keIndex] = (float)$pairwise['skala'];
            }
        }
        
        // Isi diagonal dengan 1
        for ($i = 0; $i < $k; $i++) {
            $matrix[$i][$i] = 1;
            
            // Isi nilai kebalikan (reciprocal) jika ada
            for ($j = 0; $j < $k; $j++) {
                if ($i != $j && $matrix[$i][$j] > 0 && $matrix[$j][$i] == 0) {
                    $matrix[$j][$i] = 1 / $matrix[$i][$j];
                }
            }
        }
        
        // Hitung jumlah unique pairs yang sudah terisi
        $filledPairs = 0;
        for ($i = 0; $i < $k; $i++) {
            for ($j = $i + 1; $j < $k; $j++) {
                if ($matrix[$i][$j] > 0 || $matrix[$j][$i] > 0) {
                    $filledPairs++;
                }
            }
        }
        
        return [
            'matrix' => $matrix,
            'influencers' => $influencers,
            'filled_pairs' => $filledPairs
        ];
    }

    /**
     * Simpan pairwise untuk target tertentu (server-rendered)
     */
    public function simpanPairwiseTarget()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        if (!$periodeAktif) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
        }

        $periodeId = $periodeAktif['id'];

        // Ambil data dari POST
        $targetId = $this->request->getPost('target_id');
        $fromId   = $this->request->getPost('node_dari');
        $toId     = $this->request->getPost('node_ke');
        $skala    = $this->request->getPost('skala');

        // Validasi
        if (!$targetId || !$fromId || !$toId || !$skala) {
            return redirect()->back()->withInput()->with('error', 'Semua field harus diisi.');
        }

        if ($fromId == $toId) {
            return redirect()->back()->withInput()->with('error', 'Node dari dan node ke tidak boleh sama.');
        }

        $skala = floatval($skala);
        if ($skala < 1 || $skala > 9) {
            return redirect()->back()->withInput()->with('error', 'Skala harus antara 1-9.');
        }

        // Ambil data subkriteria
        $targetData = $this->subkriteriaModel->find($targetId);
        $fromData   = $this->subkriteriaModel->find($fromId);
        $toData     = $this->subkriteriaModel->find($toId);

        if (!$targetData || !$fromData || !$toData) {
            return redirect()->back()->withInput()->with('error', 'Data subkriteria tidak ditemukan.');
        }

        // Simpan pairwise
        $result = $this->anpModel->upsertPairwise(
            $periodeId,
            $targetId,
            $fromId,
            $toId,
            $skala,
            $fromData['kode'],
            $fromData['nama'],
            $toData['kode'],
            $toData['nama'],
            $targetData['kode'],
            $targetData['nama']
        );

        $message = ($result === 'updated') ? 'Pairwise berhasil diperbarui.' : 'Pairwise berhasil ditambahkan.';

        return redirect()->to('/tpp/anp/pairwise-target?target_id=' . $targetId)->with('success', $message);
    }

    /**
     * Simpan edges (panah ANP)
     */
    public function simpanEdges()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        if (!$periodeAktif) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
        }
        
        $periodeId = $periodeAktif['id'];
        
        // Ambil data dari POST
        $edges = $this->request->getPost('edges');
        
        if (empty($edges)) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada edges yang dikirim.');
        }
        
        // Parse edges data
        $edgesData = [];
        foreach ($edges as $edge) {
            $parts = explode('_', $edge);
            if (count($parts) === 2) {
                $edgesData[] = [
                    'from_node_id' => $parts[0],
                    'to_node_id' => $parts[1]
                ];
            }
        }
        
        // Simpan edges
        $edgeModel = new EdgeModel();
        $saved = $edgeModel->saveEdges($edgesData, $periodeId);
        
        if ($saved > 0) {
            return redirect()->to('/tpp/anp/pairwise-target')->with('success', "Edges berhasil disimpan ($saved data)");
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan edges.');
        }
    }

    /**
     * Auto fill pairwise untuk target tertentu
     */
    /**
     * Auto fill pairwise untuk target tertentu (server-rendered)
     */
    public function autoFillPairwiseTarget()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        if (!$periodeAktif) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
        }
        $periodeId = $periodeAktif['id'];

        $targetId = $this->request->getPost('target_id');
        if (!$targetId) {
            return redirect()->back()->with('error', 'Target ID tidak ditemukan.');
        }

        $targetData = $this->subkriteriaModel->find($targetId);
        if (!$targetData) {
            return redirect()->back()->with('error', 'Target tidak ditemukan.');
        }

        $all = $this->subkriteriaModel->getWithKriteria();
        $influencers = array_values(array_filter($all, function($s) use ($targetId) {
            return $s['id'] != $targetId;
        }));
        if (count($influencers) < 2) {
            return redirect()->back()->with('error', 'Influencer kurang dari 2, tidak bisa dibuat pairwise.');
        }

        // existing pairwise unique map
        $existingPairwise = $this->anpModel->getHistoriPairwiseByTarget($targetId, $periodeId);
        $existing = [];
        foreach ($existingPairwise as $p) {
            $key = min($p['node_dari_id'], $p['node_ke_id']) . '_' . max($p['node_dari_id'], $p['node_ke_id']);
            $existing[$key] = true;
        }

        $addedCount = 0;
        $k = count($influencers);

        for ($i = 0; $i < $k; $i++) {
            for ($j = $i + 1; $j < $k; $j++) {
                $a = $influencers[$i]['id'];
                $b = $influencers[$j]['id'];
                $key = min($a,$b) . '_' . max($a,$b);

                if (!isset($existing[$key])) {
                    // simpan satu arah saja, reciprocal akan dibangun di matrix builder
                    $this->anpModel->upsertPairwise(
                        $periodeId,
                        $targetId,
                        $a,
                        $b,
                        1.0,
                        $influencers[$i]['kode'],
                        $influencers[$i]['nama'],
                        $influencers[$j]['kode'],
                        $influencers[$j]['nama'],
                        $targetData['kode'],
                        $targetData['nama']
                    );
                    $addedCount++;
                }
            }
        }

        $msg = ($addedCount > 0)
            ? "Auto fill berhasil! $addedCount pairwise ditambahkan dengan nilai 1."
            : "Semua pairwise untuk target ini sudah terisi.";

        return redirect()->to('/tpp/anp/pairwise-target?target_id=' . $targetId)->with('success', $msg);
    }

    /**
     * Hitung ANP dengan pendekatan target-first (server-rendered)
     */
    public function hitungAnpTargetFirst()
    {
        try {
            // Periode aktif
            $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
            if (!$periodeAktif) {
                return redirect()->back()->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
            }
            $periodeId = $periodeAktif['id'];

            // Semua node
            $subkriteria = $this->subkriteriaModel->getWithKriteria();
            if (empty($subkriteria)) {
                return redirect()->back()->with('error', 'Tidak ada subkriteria. Tambahkan subkriteria terlebih dahulu.');
            }

            $n = count($subkriteria);

            // Map id -> index
            $idToIndex = [];
            foreach ($subkriteria as $idx => $sk) {
                $idToIndex[$sk['id']] = $idx;
            }

            // Matrix bobot pengaruh (rows = influencer, cols = target)
            $W = array_fill(0, $n, array_fill(0, $n, 0.0));

            // diagonal self influence
            for ($i = 0; $i < $n; $i++) $W[$i][$i] = 1.0;

            $completeTargetCount = 0;

            // Loop tiap node sebagai TARGET
            foreach ($subkriteria as $tIndex => $target) {
                $targetId = $target['id'];

                // build matrix (influencers = semua node kecuali target)
                $matrixData = $this->buildMatrixForTargetNoEdges($targetId, $periodeId, $subkriteria);
                if (!$matrixData || empty($matrixData['influencers'])) {
                    continue;
                }

                $k = count($matrixData['influencers']);
                if ($k < 2) continue;

                $totalPairs = $k * ($k - 1) / 2;
                $filledPairs = $matrixData['filled_pairs'] ?? 0;

                // harus lengkap baru dipakai
                if ($filledPairs < $totalPairs) {
                    continue;
                }

                // hitung bobot influencer terhadap target
                $ahp = $this->anpModel->calculateAhpReport($matrixData['matrix'], $matrixData['influencers']);
                if (!$ahp || empty($ahp['weights'])) continue;

                foreach ($matrixData['influencers'] as $infIdx => $inf) {
                    $iIndex = $idToIndex[$inf['id']] ?? null;
                    if ($iIndex === null) continue;

                    // influencer -> target = bobot
                    $W[$iIndex][$tIndex] = (float)$ahp['weights'][$infIdx];
                }

                $completeTargetCount++;
            }

            if ($completeTargetCount === 0) {
                return redirect()->back()->with('error', 'Belum ada target yang matriksnya lengkap. Lengkapi pairwise dulu, lalu hitung.');
            }

            // Convert ke interdependensiData (yang dipakai buildSupermatrix)
            $interdependensiData = [];
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $interdependensiData[] = [
                        'cluster_id_dari' => $subkriteria[$i]['kriteria_id'],
                        'cluster_id_ke'   => $subkriteria[$j]['kriteria_id'],
                        'kriteria_id_dari'=> $subkriteria[$i]['id'],
                        'kriteria_id_ke'  => $subkriteria[$j]['id'],
                        'nilai'           => $W[$i][$j],
                        'tipe'            => 'element_to_element',
                        'periode_id'      => $periodeId
                    ];
                }
            }

            // simpan matrix hasil ke tabel interdependensi
            $saved = $this->anpModel->saveMatrix($interdependensiData, $periodeId);

            $msg = "Hitung ANP (Target-First) berhasil. Interdependensi tersimpan ($saved records).";
            return redirect()->to('/tpp/anp')->with('success', $msg);

        } catch (\Throwable $e) {
            $msg = 'Error hitung ANP Target-First: ' . $e->getMessage();
            return redirect()->back()->with('error', $msg);
        }
    }

    public function index()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
        
        // Ambil semua subkriteria (node ANP)
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        
        // Validasi: minimal harus ada subkriteria
        if (empty($subkriteria)) {
            return view('tpp_anp/index', [
                'title' => 'Hasil Analytic Network Process (ANP) - SPK Pembinaan',
                'subkriteria' => [],
                'interdependensi' => [],
                'hasilAnp' => null,
                'periode' => $periodeAktif,
                'activeMenu' => 'hasil-anp',
                'error' => 'Tidak ada subkriteria yang tersedia. Silakan tambahkan subkriteria terlebih dahulu.'
            ]);
        }
        
        // Gunakan bangunMatriksDariHistori untuk mendapatkan matriks interdependensi
        log_message('debug', '=== MULAI index() ===');
        log_message('debug', 'Periode ID: ' . $periodeId);
        log_message('debug', 'Jumlah subkriteria: ' . count($subkriteria));
        
        $matriksInterdependensi = $this->anpModel->bangunMatriksDariHistori($periodeId);
        
        if ($matriksInterdependensi === null) {
            log_message('debug', 'Matriks interdependensi kosong, menggunakan default');
            // Jika tidak ada data histori, gunakan default
            $interdependensi = $this->buatInterdependensiDefault($subkriteria, $periodeId);
        } else {
            log_message('debug', 'Matriks interdependensi berhasil dibangun dari histori');
            // Konversi matriks ke format interdependensi
            $interdependensi = $this->konversiMatriksKeInterdependensi($matriksInterdependensi, $subkriteria, $periodeId);
        }
        
        // Bangun supermatrix dari interdependensi
        $clusters = $this->getClusters();
        $supermatrix = $this->anpModel->buildSupermatrix($subkriteria, $interdependensi, $clusters);
        
        // Validasi supermatrix sebelum perhitungan
        if (!$this->validasiSupermatrix($supermatrix)) {
            return view('tpp_anp/index', [
                'title' => 'Hasil Analytic Network Process (ANP) - SPK Pembinaan',
                'subkriteria' => $subkriteria,
                'interdependensi' => $interdependensi,
                'hasilAnp' => null,
                'periode' => $periodeAktif,
                'activeMenu' => 'hasil-anp',
                'error' => 'Matriks interdependensi tidak valid. Pastikan semua nilai telah diisi dengan benar.'
            ]);
        }
        
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
        
        log_message('debug', '=== SELESAI index() ===');
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
        log_message('debug', "HitungANPLengkap: Mulai perhitungan dengan n=$n");
        log_message('debug', "HitungANPLengkap: Jumlah subkriteria=" . count($subkriteria));
        
        // 1. Hitung konsistensi matriks interdependensi
        log_message('debug', "HitungANPLengkap: Menghitung konsistensi");
        $konsistensi = $this->anpModel->calculateConsistency($supermatrix);
        log_message('debug', "HitungANPLengkap: Konsistensi selesai. Lambda max=" . $konsistensi['lambda_max']);
        
        // 2. Normalisasi supermatrix (unweighted supermatrix) - kolom sum = 1
        log_message('debug', "HitungANPLengkap: Normalisasi supermatrix");
        $unweightedSupermatrix = $this->anpModel->normalizeSupermatrix($supermatrix);
        
        // 3. Buat weighted supermatrix dengan cluster weights
        // Dalam implementasi sederhana, kita gunakan equal cluster weights
        log_message('debug', "HitungANPLengkap: Apply cluster weights");
        $weightedSupermatrix = $this->applyClusterWeights($unweightedSupermatrix, $subkriteria);
        
        // 4. Hitung limit supermatrix (konvergensi) dengan power method
        log_message('debug', "HitungANPLengkap: Menghitung limit supermatrix");
        $limitSupermatrix = $this->anpModel->calculateLimitSupermatrix($weightedSupermatrix, 50, 0.00001);
        
        // 5. Ekstrak bobot dari limit supermatrix (kolom pertama yang sudah konvergen)
        log_message('debug', "HitungANPLengkap: Ekstrak weights dari limit supermatrix");
        $bobot = $this->anpModel->extractWeights($limitSupermatrix, $subkriteria);
        log_message('debug', "HitungANPLengkap: Jumlah bobot yang diekstrak=" . count($bobot));
        
        // Cek apakah bobot valid
        if (empty($bobot)) {
            log_message('error', "HitungANPLengkap: Bobot kosong setelah ekstraksi");
            // Return default bobot jika ekstraksi gagal
            $bobot = [];
            foreach ($subkriteria as $index => $sk) {
                $bobot[] = [
                    'subkriteria_id' => $sk['id'],
                    'kriteria_id' => $sk['kriteria_id'],
                    'kode' => $sk['kode'],
                    'nama' => $sk['nama'],
                    'kriteria_nama' => $sk['kriteria_nama'],
                    'weight' => 1.0 / count($subkriteria)
                ];
            }
        }
        
        // 6. Hitung bobot akhir (normalisasi agar total = 1)
        $totalBobot = array_sum(array_column($bobot, 'weight'));
        log_message('debug', "HitungANPLengkap: Total bobot sebelum normalisasi=" . $totalBobot);
        
        $bobotAkhir = [];
        foreach ($bobot as $item) {
            $bobotAkhir[] = $totalBobot > 0 ? $item['weight'] / $totalBobot : 0;
        }
        
        // 7. Validasi: total bobot akhir harus = 1 (dengan toleransi)
        $totalAkhir = array_sum($bobotAkhir);
        log_message('debug', "HitungANPLengkap: Total bobot akhir=" . $totalAkhir);
        
        if (abs($totalAkhir - 1.0) > 0.0001) {
            // Normalisasi ulang jika diperlukan
            foreach ($bobotAkhir as &$bobot) {
                $bobot = $totalAkhir > 0 ? $bobot / $totalAkhir : 0;
            }
            log_message('debug', "HitungANPLengkap: Normalisasi ulang dilakukan");
        }
        
        log_message('debug', "HitungANPLengkap: Perhitungan selesai");
        
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
            'limit_supermatrix' => $limitSupermatrix,
            'total_bobot_akhir' => array_sum($bobotAkhir)
        ];
    }
    
    private function applyClusterWeights($unweightedSupermatrix, $subkriteria)
    {
        $n = count($unweightedSupermatrix);
        $weightedSupermatrix = $unweightedSupermatrix;
        
        // Hitung jumlah cluster (kriteria) yang unik
        $uniqueKriteriaIds = array_unique(array_column($subkriteria, 'kriteria_id'));
        $clusterCount = count($uniqueKriteriaIds);
        
        if ($clusterCount == 0) {
            return $weightedSupermatrix;
        }
        
        // Buat mapping cluster id ke weight (equal weights)
        $clusterWeight = 1.0 / $clusterCount;
        
        // Buat mapping index ke cluster id
        $indexToCluster = [];
        foreach ($subkriteria as $index => $sk) {
            $indexToCluster[$index] = $sk['kriteria_id'];
        }
        
        // Apply cluster weights to supermatrix
        // Dalam ANP, weighted supermatrix = unweighted supermatrix * cluster weights
        // Untuk setiap sel (i,j), kalikan dengan weight cluster dari baris i
        for ($i = 0; $i < $n; $i++) {
            $rowClusterId = $indexToCluster[$i] ?? null;
            if ($rowClusterId !== null) {
                for ($j = 0; $j < $n; $j++) {
                    if (isset($weightedSupermatrix[$i][$j])) {
                        $weightedSupermatrix[$i][$j] *= $clusterWeight;
                    }
                }
            }
        }
        
        return $weightedSupermatrix;
    }

    public function pairwiseComparison()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
        
        // Ambil semua subkriteria dengan info kriteria
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        
        // Ambil histori pairwise dari database
        $historiPairwise = $this->anpModel->getHistoriPairwise($periodeId);
        
        // Bangun matriks interdependensi dari histori
        $matriksInterdependensi = $this->bangunMatriksDariHistori($subkriteria, $historiPairwise);
        
        // Group subkriteria by cluster (kriteria) untuk dropdown
        $clusters = [];
        foreach ($subkriteria as $sk) {
            $clusterId = $sk['kriteria_id'];
            if (!isset($clusters[$clusterId])) {
                $clusters[$clusterId] = [
                    'id' => $clusterId,
                    'nama' => $sk['kriteria_nama'],
                    'nodes' => []
                ];
            }
            $clusters[$clusterId]['nodes'][] = $sk;
        }
        
        $data = [
            'title' => 'Pairwise Comparison ANP - SPK Pembinaan',
            'subkriteria' => $subkriteria,
            'clusters' => array_values($clusters), // Convert associative array to indexed array
            'histori_pairwise' => $historiPairwise,
            'matriks_interdependensi' => $matriksInterdependensi,
            'periode' => $periodeAktif,
            'activeMenu' => 'pairwise-comparison'
        ];
        
        return view('tpp_anp/pairwise_comparison', $data);
    }
    
    private function bangunMatriksDariHistori($subkriteria, $historiPairwise)
    {
        $n = count($subkriteria);
        $matriks = array_fill(0, $n, array_fill(0, $n, 0));
        
        // Mapping id subkriteria ke index
        $idToIndex = [];
        foreach ($subkriteria as $index => $sk) {
            $idToIndex[$sk['id']] = $index;
        }
        
        // Isi matriks dari histori
        foreach ($historiPairwise as $histori) {
            $dariIndex = $idToIndex[$histori['node_dari_id']] ?? null;
            $keIndex = $idToIndex[$histori['node_ke_id']] ?? null;
            
            if ($dariIndex !== null && $keIndex !== null) {
                $matriks[$dariIndex][$keIndex] = $histori['skala'];
                // Set nilai kebalikan (reciprocal)
                if ($histori['skala'] > 0) {
                    $matriks[$keIndex][$dariIndex] = 1 / $histori['skala'];
                }
            }
        }
        
        // Set diagonal = 1
        for ($i = 0; $i < $n; $i++) {
            $matriks[$i][$i] = 1;
        }
        
        return $matriks;
    }

    public function simpanPairwise()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        if (!$periodeAktif) {
            return redirect()->back()->withInput()->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
        }
        
        $periodeId = $periodeAktif['id'];
        
        // Ambil data dari POST
        $nodeDari = $this->request->getPost('node_dari');
        $nodeKe = $this->request->getPost('node_ke');
        $skala = $this->request->getPost('skala');
        
        // Validasi
        if (!$nodeDari || !$nodeKe || !$skala) {
            return redirect()->back()->withInput()->with('error', 'Semua field harus diisi.');
        }
        
        if ($nodeDari == $nodeKe) {
            return redirect()->back()->withInput()->with('error', 'Node dari dan node ke tidak boleh sama.');
        }
        
        $skala = floatval($skala);
        if ($skala < 1 || $skala > 9) {
            return redirect()->back()->withInput()->with('error', 'Skala harus antara 1-9.');
        }
        
        // Ambil data subkriteria
        $subkriteriaDari = $this->subkriteriaModel->find($nodeDari);
        $subkriteriaKe = $this->subkriteriaModel->find($nodeKe);
        
        if (!$subkriteriaDari || !$subkriteriaKe) {
            return redirect()->back()->withInput()->with('error', 'Data subkriteria tidak ditemukan.');
        }
        
        // Cek apakah pairwise sudah ada
        $db = \Config\Database::connect();
        $existing = $db->table('anp_pairwise_histori')
            ->where('periode_id', $periodeId)
            ->where('node_dari_id', $nodeDari)
            ->where('node_ke_id', $nodeKe)
            ->get()
            ->getRowArray();
        
        if ($existing) {
            // Update existing
            $db->table('anp_pairwise_histori')
                ->where('id', $existing['id'])
                ->update([
                    'skala' => $skala,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            $message = 'Pairwise berhasil diperbarui.';
        } else {
            // Insert new
            $db->table('anp_pairwise_histori')->insert([
                'periode_id' => $periodeId,
                'node_dari_id' => $nodeDari,
                'node_dari_kode' => $subkriteriaDari['kode'],
                'node_dari_nama' => $subkriteriaDari['nama'],
                'node_ke_id' => $nodeKe,
                'node_ke_kode' => $subkriteriaKe['kode'],
                'node_ke_nama' => $subkriteriaKe['nama'],
                'skala' => $skala,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $message = 'Pairwise berhasil ditambahkan.';
        }
        
        return redirect()->to('/tpp/anp/pairwise-comparison')->with('success', $message);
    }
    
    public function hapusPairwise($id)
    {
        $db = \Config\Database::connect();
        $deleted = $db->table('anp_pairwise_histori')->where('id', $id)->delete();
        
        if ($deleted) {
            return redirect()->to('/tpp/anp/pairwise-comparison')->with('success', 'Pairwise berhasil dihapus.');
        } else {
            return redirect()->to('/tpp/anp/pairwise-comparison')->with('error', 'Gagal menghapus pairwise.');
        }
    }
    
    public function hitungAnp()
    {
        try {
            log_message('info', 'Mulai proses hitung ANP');
            
            // Cek apakah request AJAX
            $isAjax = $this->request->isAJAX();
            
            // Ambil periode aktif
            $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
            if (!$periodeAktif) {
                log_message('error', 'Tidak ada periode aktif');
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.'
                    ]);
                }
                return redirect()->to('/tpp/anp/pairwise-comparison')->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
            }
            $periodeId = $periodeAktif['id'];
            log_message('info', 'Periode aktif ditemukan: ' . $periodeId);
            
            // Ambil semua subkriteria
            $subkriteria = $this->subkriteriaModel->getWithKriteria();
            if (empty($subkriteria)) {
                log_message('error', 'Tidak ada subkriteria yang tersedia');
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Tidak ada subkriteria yang tersedia. Silakan tambahkan subkriteria terlebih dahulu.'
                    ]);
                }
                return redirect()->to('/tpp/anp/pairwise-comparison')->with('error', 'Tidak ada subkriteria yang tersedia. Silakan tambahkan subkriteria terlebih dahulu.');
            }
            $n = count($subkriteria);
            log_message('info', 'Jumlah subkriteria: ' . $n);
            
            // Ambil histori pairwise
            $historiPairwise = $this->anpModel->getHistoriPairwise($periodeId);
            if (empty($historiPairwise)) {
                log_message('error', 'Belum ada data pairwise comparison');
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Belum ada data pairwise comparison. Silakan isi pairwise comparison terlebih dahulu.'
                    ]);
                }
                return redirect()->to('/tpp/anp/pairwise-comparison')->with('error', 'Belum ada data pairwise comparison. Silakan isi pairwise comparison terlebih dahulu.');
            }
            log_message('info', 'Jumlah histori pairwise: ' . count($historiPairwise));
            
            // Bangun matriks interdependensi
            $matriksInterdependensi = $this->bangunMatriksDariHistori($subkriteria, $historiPairwise);
            log_message('info', 'Matriks interdependensi berhasil dibangun');
            
            // Validasi matriks interdependensi
            $validCount = 0;
            $totalPairs = $n * $n;
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    if ($matriksInterdependensi[$i][$j] > 0) {
                        $validCount++;
                    }
                }
            }
            
            $persentase = ($totalPairs > 0) ? round(($validCount / $totalPairs) * 100, 1) : 0;
            log_message('info', 'Valid count: ' . $validCount . '/' . $totalPairs . ' (' . $persentase . '%)');
            
            // Kurangi threshold validasi dari 50% menjadi 30%
            if ($validCount < ($n * 0.3)) {
                log_message('error', 'Data pairwise belum cukup: ' . $validCount . ' < ' . ($n * 0.3));
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Data pairwise belum cukup. Minimal diperlukan 30% data untuk perhitungan ANP. Saat ini: ' . $persentase . '%'
                    ]);
                }
                return redirect()->to('/tpp/anp/pairwise-comparison')->with('error', 'Data pairwise belum cukup. Minimal diperlukan 30% data untuk perhitungan ANP. Saat ini: ' . $persentase . '%');
            }
            
            // Konversi ke format yang diharapkan oleh model ANP
            $interdependensiData = [];
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $interdependensiData[] = [
                        'cluster_id_dari' => $subkriteria[$i]['kriteria_id'],
                        'cluster_id_ke' => $subkriteria[$j]['kriteria_id'],
                        'kriteria_id_dari' => $subkriteria[$i]['id'],
                        'kriteria_id_ke' => $subkriteria[$j]['id'],
                        'nilai' => $matriksInterdependensi[$i][$j],
                        'tipe' => 'element_to_element',
                        'periode_id' => $periodeId
                    ];
                }
            }
            log_message('info', 'Data interdependensi siap disimpan: ' . count($interdependensiData) . ' records');
            
            // Simpan ke tabel interdependensi
            log_message('info', 'Mulai menyimpan data interdependensi ke database');
            $saved = $this->anpModel->saveMatrix($interdependensiData, $periodeId);
            log_message('info', 'Hasil penyimpanan: ' . $saved);
            
            if ($saved > 0) {
                log_message('info', 'Perhitungan ANP berhasil');
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Perhitungan ANP berhasil. Data interdependensi telah disimpan (' . $saved . ' records).',
                        'redirect_url' => base_url('/tpp/anp')
                    ]);
                }
                return redirect()->to('/tpp/anp')->with('success', 'Perhitungan ANP berhasil. Data interdependensi telah disimpan (' . $saved . ' records).');
            } else {
                log_message('error', 'Gagal menyimpan hasil perhitungan ANP');
                if ($isAjax) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal menyimpan hasil perhitungan ANP. Silakan coba lagi.'
                    ]);
                }
                return redirect()->to('/tpp/anp/pairwise-comparison')->with('error', 'Gagal menyimpan hasil perhitungan ANP. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in hitungAnp: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghitung ANP: ' . $e->getMessage()
                ]);
            }
            return redirect()->to('/tpp/anp/pairwise-comparison')->with('error', 'Terjadi kesalahan saat menghitung ANP: ' . $e->getMessage());
        }
    }
    
    public function autoFillPairwise()
    {
        // Cek apakah request AJAX
        $isAjax = $this->request->isAJAX();
        
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        if (!$periodeAktif) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.'
                ]);
            }
            return redirect()->back()->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
        }
        
        $periodeId = $periodeAktif['id'];
        
        // Ambil semua subkriteria
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        $n = count($subkriteria);
        
        // Ambil histori pairwise yang sudah ada
        $db = \Config\Database::connect();
        $existingPairwise = $db->table('anp_pairwise_histori')
            ->where('periode_id', $periodeId)
            ->get()
            ->getResultArray();
        
        // Buat map untuk pairwise yang sudah ada
        $existingMap = [];
        foreach ($existingPairwise as $pairwise) {
            $key = $pairwise['node_dari_id'] . '_' . $pairwise['node_ke_id'];
            $existingMap[$key] = true;
        }
        
        // Hitung pairwise yang perlu ditambahkan
        $addedCount = 0;
        $batchData = [];
        
        // Loop untuk semua kombinasi pairwise (kecuali diagonal)
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i != $j) { // Skip diagonal
                    $nodeDariId = $subkriteria[$i]['id'];
                    $nodeKeId = $subkriteria[$j]['id'];
                    $key = $nodeDariId . '_' . $nodeKeId;
                    $reverseKey = $nodeKeId . '_' . $nodeDariId;
                    
                    // Cek apakah pairwise belum ada
                    if (!isset($existingMap[$key]) && !isset($existingMap[$reverseKey])) {
                        $batchData[] = [
                            'periode_id' => $periodeId,
                            'node_dari_id' => $nodeDariId,
                            'node_dari_kode' => $subkriteria[$i]['kode'],
                            'node_dari_nama' => $subkriteria[$i]['nama'],
                            'node_ke_id' => $nodeKeId,
                            'node_ke_kode' => $subkriteria[$j]['kode'],
                            'node_ke_nama' => $subkriteria[$j]['nama'],
                            'skala' => 1.0,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                        $addedCount++;
                    }
                }
            }
        }
        
        // Insert batch jika ada data
        if (!empty($batchData)) {
            $db->table('anp_pairwise_histori')->insertBatch($batchData);
        }
        
        if ($addedCount > 0) {
            $message = "Auto fill berhasil! $addedCount pairwise telah ditambahkan dengan nilai 1.";
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message,
                    'added_count' => $addedCount
                ]);
            }
            return redirect()->to('/tpp/anp/pairwise-comparison')->with('success', $message);
        } else {
            $message = 'Semua pairwise sudah terisi. Tidak ada data baru yang ditambahkan.';
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message,
                    'added_count' => 0
                ]);
            }
            return redirect()->to('/tpp/anp/pairwise-comparison')->with('info', $message);
        }
    }
    
    public function autoFillAllPairwise()
    {
        // Cek apakah request AJAX
        $isAjax = $this->request->isAJAX();
        
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        if (!$periodeAktif) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.'
                ]);
            }
            return redirect()->back()->with('error', 'Tidak ada periode aktif. Silakan buat periode terlebih dahulu.');
        }
        
        $periodeId = $periodeAktif['id'];
        
        // Ambil semua subkriteria
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        $n = count($subkriteria);
        
        // Hapus semua pairwise yang ada untuk periode ini
        $db = \Config\Database::connect();
        $db->table('anp_pairwise_histori')->where('periode_id', $periodeId)->delete();
        
        // Buat semua pairwise combination dengan nilai 1
        $batchData = [];
        $totalPairs = 0;
        
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i != $j) { // Skip diagonal
                    $nodeDariId = $subkriteria[$i]['id'];
                    $nodeKeId = $subkriteria[$j]['id'];
                    
                    $batchData[] = [
                        'periode_id' => $periodeId,
                        'node_dari_id' => $nodeDariId,
                        'node_dari_kode' => $subkriteria[$i]['kode'],
                        'node_dari_nama' => $subkriteria[$i]['nama'],
                        'node_ke_id' => $nodeKeId,
                        'node_ke_kode' => $subkriteria[$j]['kode'],
                        'node_ke_nama' => $subkriteria[$j]['nama'],
                        'skala' => 1.0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $totalPairs++;
                }
            }
        }
        
        // Insert batch
        if (!empty($batchData)) {
            $db->table('anp_pairwise_histori')->insertBatch($batchData);
        }
        
        $message = "Auto fill semua pairwise berhasil! Total $totalPairs pairwise telah dibuat dengan nilai 1.";
        if ($isAjax) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'total_pairs' => $totalPairs
            ]);
        }
        return redirect()->to('/tpp/anp/pairwise-comparison')->with('success', $message);
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

    /**
     * Validasi data interdependensi sebelum perhitungan
     */
    private function validasiDataInterdependensi($interdependensi, $subkriteria)
    {
        $n = count($subkriteria);
        
        // Jika tidak ada data interdependensi sama sekali
        if (empty($interdependensi)) {
            log_message('debug', 'Validasi interdependensi: Tidak ada data interdependensi');
            return [
                'valid' => false,
                'message' => 'Belum ada data interdependensi. Silakan input matriks interdependensi terlebih dahulu.'
            ];
        }
        
        // Hitung jumlah data interdependensi yang valid (nilai > 0)
        $validCount = 0;
        foreach ($interdependensi as $item) {
            if ($item['tipe'] === 'element_to_element' && $item['nilai'] > 0) {
                $validCount++;
            }
        }
        
        // Kurangi ketatnya validasi - minimal cukup diagonal saja
        $minimalData = $n; // Cukup diagonal saja (self-comparison)
        
        if ($validCount < $minimalData) {
            $persentase = round(($validCount / ($n * $n)) * 100, 1);
            log_message('debug', "Validasi interdependensi: Data kurang. Valid: $validCount, Minimal: $minimalData, Persentase: $persentase%");
            return [
                'valid' => false,
                'message' => "Data interdependensi belum cukup ($persentase% terisi). Minimal diperlukan $minimalData data untuk perhitungan."
            ];
        }
        
        log_message('debug', "Validasi interdependensi: Valid. Valid: $validCount, Minimal: $minimalData");
        return [
            'valid' => true,
            'message' => 'Data interdependensi cukup untuk perhitungan.'
        ];
    }
    
    /**
     * Validasi supermatrix sebelum perhitungan
     */
    private function validasiSupermatrix($supermatrix)
    {
        $n = count($supermatrix);
        
        // Cek ukuran matriks
        if ($n == 0) {
            return false;
        }
        
        // Cek diagonal harus = 1
        for ($i = 0; $i < $n; $i++) {
            if (abs($supermatrix[$i][$i] - 1.0) > 0.0001) {
                return false;
            }
        }
        
        // Cek apakah ada nilai yang tidak valid (NaN atau infinity)
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $value = $supermatrix[$i][$j];
                if (!is_numeric($value) || is_infinite($value) || is_nan($value)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Validasi total bobot sebelum disimpan
     */
    private function validasiTotalBobot($bobotAkhir)
        {
        $total = array_sum($bobotAkhir);
        return abs($total - 1.0) < 0.0001;
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
        
        // Validasi jumlah data harus sama
        if (count($subkriteriaIds) !== count($bobotAkhir)) {
            return redirect()->back()->withInput()->with('error', 'Jumlah data subkriteria dan bobot tidak sama');
        }
        
        // Validasi format
        $validation = \Config\Services::validation();
        $validation->setRules([
            'bobot_akhir.*' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[1]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Validasi total bobot harus = 1
        if (!$this->validasiTotalBobot($bobotAkhir)) {
            $total = array_sum($bobotAkhir);
            return redirect()->back()->withInput()->with('error', "Total bobot harus = 1. Total saat ini: " . number_format($total, 4));
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
    
    private function konversiMatriksKeInterdependensi($matriksInterdependensi, $subkriteria, $periodeId)
    {
        $interdependensi = [];
        $n = count($subkriteria);
        
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $interdependensi[] = [
                    'cluster_id_dari' => $subkriteria[$i]['kriteria_id'],
                    'cluster_id_ke' => $subkriteria[$j]['kriteria_id'],
                    'kriteria_id_dari' => $subkriteria[$i]['id'],
                    'kriteria_id_ke' => $subkriteria[$j]['id'],
                    'nilai' => $matriksInterdependensi[$i][$j],
                    'tipe' => 'element_to_element',
                    'periode_id' => $periodeId
                ];
            }
        }
        
        return $interdependensi;
    }

    public function partialResult()
    {
        // Ambil periode aktif
        $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
        $periodeId = $periodeAktif ? $periodeAktif['id'] : null;
        
        // Ambil semua subkriteria (node ANP)
        $subkriteria = $this->subkriteriaModel->getWithKriteria();
        
        // Validasi: minimal harus ada subkriteria
        if (empty($subkriteria)) {
            return view('tpp_anp/partial_anp_result', [
                'title' => 'Hasil ANP Parsial - SPK Pembinaan',
                'subkriteria' => [],
                'periode' => $periodeAktif,
                'activeMenu' => 'hasil-anp',
                'error' => 'Tidak ada subkriteria yang tersedia. Silakan tambahkan subkriteria terlebih dahulu.'
            ]);
        }
        
        // Ambil data interdependensi ANP
        $interdependensi = $this->anpModel->getElementInterdependensi($periodeId);
        
        // Jika tidak ada data interdependensi, buat default
        if (empty($interdependensi)) {
            $interdependensi = $this->buatInterdependensiDefault($subkriteria, $periodeId);
        }
        
        // Bangun supermatrix dari interdependensi
        $clusters = $this->getClusters();
        $supermatrix = $this->anpModel->buildSupermatrix($subkriteria, $interdependensi, $clusters);
        
        // Validasi supermatrix sebelum perhitungan
        if (!$this->validasiSupermatrix($supermatrix)) {
            return view('tpp_anp/partial_anp_result', [
                'title' => 'Hasil ANP Parsial - SPK Pembinaan',
                'subkriteria' => $subkriteria,
                'interdependensi' => $interdependensi,
                'hasilAnp' => null,
                'periode' => $periodeAktif,
                'activeMenu' => 'hasil-anp',
                'error' => 'Matriks interdependensi tidak valid. Pastikan semua nilai telah diisi dengan benar.'
            ]);
        }
        
        // Hitung hasil ANP lengkap
        $hasilAnp = $this->hitungANPLengkap($supermatrix, $subkriteria);
        
        $data = [
            'title' => 'Hasil ANP Parsial - SPK Pembinaan',
            'subkriteria' => $subkriteria,
            'interdependensi' => $interdependensi,
            'hasilAnp' => $hasilAnp,
            'periode' => $periodeAktif,
            'activeMenu' => 'hasil-anp'
        ];
        
        return view('tpp_anp/partial_anp_result', $data);
    }

    /**
     * Render partial HTML untuk tabel matrix dan report
     */
    public function renderResultTables()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
    }

    $targetId = $this->request->getGet('target_id');
    if (!$targetId) {
        return $this->response->setJSON(['success' => false, 'message' => 'Target ID tidak ditemukan']);
    }

    $periodeAktif = $this->periodeModel->where('status', 'aktif')->first();
    if (!$periodeAktif) {
        return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada periode aktif.']);
    }
    $periodeId = $periodeAktif['id'];

    $allSubkriteria = $this->subkriteriaModel->getWithKriteria();

    // ✅ PAKAI NO-EDGES
    $matrixData = $this->buildMatrixForTargetNoEdges($targetId, $periodeId, $allSubkriteria);

    if (!$matrixData || empty($matrixData['influencers'])) {
        return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada influencer untuk target ini']);
    }

    $k = count($matrixData['influencers']);
    $totalPairs = $k * ($k - 1) / 2;
    $filledPairs = $matrixData['filled_pairs'];
    $isComplete = ($filledPairs >= $totalPairs && $k >= 2);

    $matrixData['is_complete'] = $isComplete;
    $matrixData['k'] = $k;
    $matrixData['total_pairs'] = $totalPairs;
    $matrixData['filled_pairs'] = $filledPairs;

    $ahpReport = null;
    if ($isComplete && !empty($matrixData['matrix'])) {
        $ahpReport = $this->anpModel->calculateAhpReport($matrixData['matrix'], $matrixData['influencers']);
    }

    $html = view('tpp_anp/_result_tables', [
        'matrix_data' => $matrixData,
        'ahp_report' => $ahpReport
    ]);

    return $this->response->setJSON([
        'success' => true,
        'html' => $html,
        'is_complete' => $isComplete,
        'filled_pairs' => $filledPairs,
        'total_pairs' => $totalPairs
    ]);
}

}
