<?php

// Check if this is being run directly
if (!defined('BASEPATH')) {
    // Set the correct path to CodeIgniter's index.php
    define('BASEPATH', __DIR__ . '/');
    
    // Bootstrap CodeIgniter
    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/system/CodeIgniter.php';
    
    $app = new CodeIgniter\CodeIgniter();
    $app->initialize();
}

// Load necessary models
$edgeModel = new App\Models\EdgeModel();
$subkriteriaModel = new App\Models\SubkriteriaModel();

// Get periode aktif
$periodeModel = new App\Models\PeriodeModel();
$periodeAktif = $periodeModel->where('status', 'aktif')->first();
$periodeId = $periodeAktif['id'] ?? null;

// Get all subkriteria with IDs
$subkriteria = $subkriteriaModel->getWithKriteria();

echo "=== Daftar Subkriteria ===\n";
foreach ($subkriteria as $sk) {
    echo "ID: {$sk['id']}\tKode: {$sk['kode']}\tNama: {$sk['nama']}\tKriteria: {$sk['kriteria_nama']}\n";
}
echo "\n";

// Get all edges
echo "=== Daftar Edges ===\n";
if ($periodeId) {
    $edges = $edgeModel->where('periode_id', $periodeId)->findAll();
    
    foreach ($edges as $edge) {
        $fromNode = array_column($subkriteria, null, 'id')[$edge['from_node_id']] ?? null;
        $toNode = array_column($subkriteria, null, 'id')[$edge['to_node_id']] ?? null;
        
        echo "From ID: {$edge['from_node_id']}" . 
             ($fromNode ? "\tFrom: {$fromNode['kode']} - {$fromNode['nama']}" : "") . 
             "\tTo ID: {$edge['to_node_id']}" . 
             ($toNode ? "\tTo: {$toNode['kode']} - {$toNode['nama']}" : "") . 
             "\n";
    }
}

echo "\n=== Edge untuk KM1 ===";
echo "\n=== Sebagai From Node ===\n";
if ($periodeId) {
    // Find KM1
    $km1 = null;
    foreach ($subkriteria as $sk) {
        if (strpos(strtolower($sk['nama']), 'pelatihan') !== false && strpos(strtolower($sk['nama']), 'keterampilan') !== false) {
            $km1 = $sk;
            break;
        }
    }
    
    if ($km1) {
        echo "KM1 ID: {$km1['id']}\tKode: {$km1['kode']}\tNama: {$km1['nama']}\n";
        
        // Get edges from KM1
        $km1Edges = $edgeModel->where('from_node_id', $km1['id'])->where('periode_id', $periodeId)->findAll();
        foreach ($km1Edges as $edge) {
            $toNode = array_column($subkriteria, null, 'id')[$edge['to_node_id']] ?? null;
            echo "To ID: {$edge['to_node_id']}" . 
                 ($toNode ? "\tTo: {$toNode['kode']} - {$toNode['nama']}" : "") . 
                 "\n";
        }
        
        // Get edges to KM1
        echo "\n=== Sebagai To Node ===\n";
        $km1Influencers = $edgeModel->getInfluencerNodes($km1['id'], $periodeId);
        foreach ($km1Influencers as $inf) {
            echo "From ID: {$inf['id']}\tFrom: {$inf['kode']} - {$inf['nama']}\n";
        }
    }
}