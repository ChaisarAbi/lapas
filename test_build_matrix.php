<?php

// Test script untuk memverifikasi buildMatrixForTargetWithEdges bekerja setelah canonical update
require_once 'app/Config/Paths.php';
require_once 'app/Config/Constants.php';
require_once SYSTEMPATH . 'Boot/psr.php';
require_once SYSTEMPATH . 'Config/Autoload.php';
require_once SYSTEMPATH . 'Config/Modules.php';
require_once APPPATH . 'Config/Autoload.php';
require_once APPPATH . 'Config/Constants.php';

use Config\Autoload;
use Config\Services;

$autoload = new Autoload();
$autoload->initialize();
$autoload->register();

// Load essential services
Services::autoloader()->initialize($autoload);

// Buat controller instance
use App\Controllers\TppAnpController;

$controller = new TppAnpController();

echo "=== TEST BUILD MATRIX FOR TARGET WITH EDGES ===\n\n";

// Ambil periode aktif
$periodeModel = new \App\Models\PeriodeModel();
$periodeAktif = $periodeModel->where('status', 'aktif')->first();
if (!$periodeAktif) {
    echo "❌ Tidak ada periode aktif.\n";
    exit;
}

$periodeId = $periodeAktif['id'];
echo "Periode aktif: ID = $periodeId\n";

// Ambil semua subkriteria
$subkriteriaModel = new \App\Models\SubkriteriaModel();
$allSubkriteria = $subkriteriaModel->getWithKriteria();

echo "Total subkriteria: " . count($allSubkriteria) . "\n\n";

// Test untuk target pertama
$target = $allSubkriteria[0] ?? null;
if (!$target) {
    echo "❌ Tidak ada subkriteria.\n";
    exit;
}

$targetId = $target['id'];
echo "Testing target: {$target['kode']} (ID: $targetId)\n";

// Gunakan reflection untuk memanggil method private buildMatrixForTargetWithEdges
$reflectionMethod = new ReflectionMethod($controller, 'buildMatrixForTargetWithEdges');
$reflectionMethod->setAccessible(true);

$matrixData = $reflectionMethod->invoke($controller, $targetId, $periodeId, $allSubkriteria);

if (!$matrixData) {
    echo "❌ matrixData null atau kosong\n";
    exit;
}

echo "✓ matrixData berhasil dibuat\n";
echo "  Jumlah influencer: " . count($matrixData['influencers']) . "\n";
echo "  Filled pairs: {$matrixData['filled_pairs']}\n";

// Tampilkan influencer
echo "\nInfluencer nodes:\n";
foreach ($matrixData['influencers'] as $inf) {
    echo "  - {$inf['kode']} (ID: {$inf['id']})\n";
}

// Hitung total pairs yang diperlukan
$k = count($matrixData['influencers']);
$totalPairs = $k * ($k - 1) / 2;
echo "\nTotal pairs diperlukan: $totalPairs\n";

// Cek completeness
$isComplete = ($matrixData['filled_pairs'] >= $totalPairs && $k >= 2);
echo "Is complete: " . ($isComplete ? "✅ YA" : "❌ TIDAK") . "\n";

// Tampilkan sebagian matrix untuk verifikasi
echo "\nSample matrix values (3x3 pertama):\n";
for ($i = 0; $i < min(3, $k); $i++) {
    for ($j = 0; $j < min(3, $k); $j++) {
        $val = $matrixData['matrix'][$i][$j];
        echo sprintf("  [%d][%d] = %.4f  ", $i, $j, $val);
    }
    echo "\n";
}

// Cek data pairwise di database untuk target ini
$db = \Config\Database::connect();
$pairwiseCount = $db->table('anp_pairwise_histori')
    ->where('target_node_id', $targetId)
    ->where('periode_id', $periodeId)
    ->countAllResults();

echo "\nTotal pairwise records di database untuk target ini: $pairwiseCount\n";

// Hitung unique canonical pairs
$uniquePairs = $db->table('anp_pairwise_histori')
    ->select('COUNT(DISTINCT CONCAT(node1_id, "-", node2_id)) as unique_count')
    ->where('target_node_id', $targetId)
    ->where('periode_id', $periodeId)
    ->where('node1_id IS NOT NULL')
    ->where('node2_id IS NOT NULL')
    ->get()
    ->getRowArray();

$uniqueCount = $uniquePairs['unique_count'] ?? 0;
echo "Unique canonical pairs: $uniqueCount\n";

// Hitung influencer edges untuk target ini
$edgeModel = new \App\Models\EdgeModel();
$influencerEdges = $edgeModel->getInfluencerNodes($targetId, $periodeId);
$influencerCount = count($influencerEdges);
echo "Influencer edges dari EdgeModel: $influencerCount\n";

if ($influencerCount >= 2) {
    $totalPairsNeeded = $influencerCount * ($influencerCount - 1) / 2;
    echo "Total pairs diperlukan: $totalPairsNeeded\n";
    echo "Status: " . ($uniqueCount >= $totalPairsNeeded ? "✅ LENGKAP" : "❌ KURANG") . "\n";
}

echo "\n=== TEST SELESAI ===\n";