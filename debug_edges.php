<?php
// Script debug untuk memeriksa data edges dan influencer nodes

require_once 'app/Models/EdgeModel.php';
require_once 'app/Models/SubkriteriaModel.php';
require_once 'app/Models/PeriodeModel.php';

// Inisialisasi model
$edgeModel = new \App\Models\EdgeModel();
$subkriteriaModel = new \App\Models\SubkriteriaModel();
$periodeModel = new \App\Models\PeriodeModel();

// Ambil periode aktif
$periodeAktif = $periodeModel->where('status', 'aktif')->first();
$periodeId = $periodeAktif ? $periodeAktif['id'] : null;

echo "=== DEBUG EDGES ===\n";
echo "Periode ID: " . ($periodeId ?? 'null') . "\n\n";

// Ambil semua subkriteria
$subkriteria = $subkriteriaModel->getWithKriteria();
echo "Total subkriteria: " . count($subkriteria) . "\n";
foreach ($subkriteria as $sk) {
    echo "  {$sk['id']}: {$sk['kode']} - {$sk['nama']} ({$sk['kriteria_nama']})\n";
}
echo "\n";

// Ambil semua edges
$allEdges = $edgeModel->getEdgesByPeriode($periodeId);
echo "Total edges: " . count($allEdges) . "\n";
foreach ($allEdges as $edge) {
    echo "  From: {$edge['from_node_id']} -> To: {$edge['to_node_id']}\n";
}
echo "\n";

// Cek influencer nodes untuk KP1 (node 1)
$targetNodeId = 1;
echo "=== INFLUENCER NODES FOR TARGET $targetNodeId ===\n";
$influencers = $edgeModel->getInfluencerNodes($targetNodeId, $periodeId);
echo "Jumlah influencer: " . count($influencers) . "\n";
foreach ($influencers as $inf) {
    echo "  {$inf['id']}: {$inf['kode']} - {$inf['nama']} ({$inf['kriteria_nama']})\n";
}
echo "\n";

// Cek targets nodes untuk KP1 (node 1)
echo "=== TARGET NODES FOR INFLUENCER $targetNodeId ===\n";
$targets = $edgeModel->getTargetNodes($targetNodeId, $periodeId);
echo "Jumlah target: " . count($targets) . "\n";
foreach ($targets as $tgt) {
    echo "  {$tgt['id']}: {$tgt['kode']} - {$tgt['nama']} ({$tgt['kriteria_nama']})\n";
}
echo "\n";

// Cek edges secara manual dari database
echo "=== MANUAL QUERY ===\n";
$db = \Config\Database::connect();
$query = $db->table('anp_edges')
    ->where('periode_id', $periodeId)
    ->get();
$manualEdges = $query->getResultArray();

echo "Manual query edges: " . count($manualEdges) . "\n";
foreach ($manualEdges as $edge) {
    echo "  From: {$edge['from_node_id']} -> To: {$edge['to_node_id']}\n";
}
echo "\n";

// Cek influencer nodes secara manual
$query2 = $db->table('anp_edges e')
    ->select('s.id, s.kode, s.nama, s.kriteria_id, k.nama as kriteria_nama')
    ->join('subkriteria s', 's.id = e.from_node_id')
    ->join('kriteria k', 'k.id = s.kriteria_id')
    ->where('e.to_node_id', $targetNodeId);
if ($periodeId) {
    $query2->where('e.periode_id', $periodeId);
}
$query2->orderBy('s.kriteria_id', 'ASC')
       ->orderBy('s.kode', 'ASC');
$manualInfluencers = $query2->get()->getResultArray();

echo "Manual query influencer nodes: " . count($manualInfluencers) . "\n";
foreach ($manualInfluencers as $inf) {
    echo "  {$inf['id']}: {$inf['kode']} - {$inf['nama']} ({$inf['kriteria_nama']})\n";
}
echo "\n";

echo "=== SELESAI ===\n";