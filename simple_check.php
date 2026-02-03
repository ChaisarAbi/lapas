<?php

require 'vendor/autoload.php';

use Config\Database;
use App\Models\EdgeModel;
use App\Models\SubkriteriaModel;
use App\Models\PeriodeModel;

$db = Database::connect();
$edgeModel = new EdgeModel();
$subkriteriaModel = new SubkriteriaModel();
$periodeModel = new PeriodeModel();

// Get active periode
$periodeAktif = $periodeModel->where('status', 'aktif')->first();
$periodeId = $periodeAktif['id'] ?? null;

// Get all subkriteria
$subkriteria = $subkriteriaModel->getWithKriteria();
echo "=== Subkriteria ===\n";
foreach ($subkriteria as $sk) {
    echo "ID: {$sk['id']} | Kode: {$sk['kode']} | Nama: {$sk['nama']} | Kriteria: {$sk['kriteria_nama']}\n";
}

// Get edges for specific target (KM1)
echo "\n=== Find KM1 ===\n";
$km1 = null;
foreach ($subkriteria as $sk) {
    if (strpos($sk['kode'], 'KM1') !== false) {
        $km1 = $sk;
        break;
    }
}

if ($km1) {
    echo "Found KM1:\n";
    echo "ID: {$km1['id']} | Kode: {$km1['kode']} | Nama: {$km1['nama']}\n";
    
    // Get influencers for KM1 as target
    $influencers = $edgeModel->getInfluencerNodes($km1['id'], $periodeId);
    echo "\n=== Influencers for KM1 ===\n";
    foreach ($influencers as $inf) {
        echo "ID: {$inf['id']} | Kode: {$inf['kode']} | Nama: {$inf['nama']} | Kriteria: {$inf['kriteria_nama']}\n";
    }
    
    if (empty($influencers)) {
        echo "No influencers found for KM1.\n";
    }
} else {
    echo "KM1 not found!\n";
}