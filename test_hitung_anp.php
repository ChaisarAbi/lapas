<?php

// Test script untuk hitung ANP Target-First
// Bootstrap CodeIgniter
require_once __DIR__ . '/app/Config/Bootstrap.php';
$app = Config\Services::codeigniter();
$app->initialize();

// Load models
$anpModel = new \App\Models\AnpModel();
$periodeModel = new \App\Models\PeriodeModel();
$subkriteriaModel = new \App\Models\SubkriteriaModel();
$edgeModel = new \App\Models\EdgeModel();

try {
    echo "=== TEST HITUNG ANP TARGET-FIRST ===\n\n";
    
    // 1. Cari periode aktif
    $periodeAktif = $periodeModel->where('status', 'aktif')->first();
    if (!$periodeAktif) {
        echo "❌ Tidak ada periode aktif.\n";
        exit;
    }
    $periodeId = $periodeAktif['id'];
    echo "Periode aktif: ID = " . $periodeAktif['id'] . ", Nama = " . $periodeAktif['nama_periode'] . "\n\n";
    
    // 2. Ambil semua subkriteria
    $subkriteria = $subkriteriaModel->getWithKriteria();
    if (empty($subkriteria)) {
        echo "❌ Tidak ada subkriteria.\n";
        exit;
    }
    $n = count($subkriteria);
    echo "Jumlah subkriteria: $n\n";
    
    // 3. Buat mapping id -> index
    $idToIndex = [];
    foreach ($subkriteria as $idx => $sk) {
        $idToIndex[$sk['id']] = $idx;
    }
    
    // 4. Matrix bobot pengaruh (rows = influencer, cols = target)
    $W = array_fill(0, $n, array_fill(0, $n, 0.0));
    
    // 5. Loop tiap node sebagai TARGET
    echo "\n=== PROSES PER TARGET ===\n";
    $completeTargetCount = 0;
    
    foreach ($subkriteria as $tIndex => $target) {
        $targetId = $target['id'];
        echo "\nTarget: " . $target['kode'] . " (ID: $targetId)\n";
        
        // a. Dapatkan influencer nodes untuk target ini
        $influencerEdges = $edgeModel->getInfluencerNodes($targetId, $periodeId);
        echo "  Influencer edges count: " . count($influencerEdges) . "\n";
        
        if (empty($influencerEdges)) {
            echo "  ❌ Tidak ada edges untuk target ini\n";
            continue;
        }
        
        // b. Bangun matrix untuk target ini
        $influencers = [];
        $idToInfIndex = [];
        $infIdx = 0;
        foreach ($influencerEdges as $edge) {
            // Cari subkriteria dari influencer edges
            foreach ($subkriteria as $sub) {
                if ($sub['id'] == $edge['id']) {
                    $influencers[] = $sub;
                    $idToInfIndex[$sub['id']] = $infIdx;
                    $infIdx++;
                    break;
                }
            }
        }
        
        $k = count($influencers);
        echo "  Jumlah influencer unik: $k\n";
        
        if ($k < 2) {
            echo "  ❌ Influencer < 2, skip\n";
            continue;
        }
        
        // c. Buat matriks kosong
        $matrix = array_fill(0, $k, array_fill(0, $k, 0));
        
        // d. Ambil pairwise yang sudah ada
        $db = \Config\Database::connect();
        $pairwiseData = $db->table('anp_pairwise_histori')
            ->where('target_node_id', $targetId)
            ->where('periode_id', $periodeId)
            ->get()
            ->getResultArray();
        
        echo "  Pairwise data count: " . count($pairwiseData) . "\n";
        
        // e. Isi matrix dari data pairwise
        foreach ($pairwiseData as $pairwise) {
            $dariIndex = $idToInfIndex[$pairwise['node_dari_id']] ?? null;
            $keIndex = $idToInfIndex[$pairwise['node_ke_id']] ?? null;
            
            if ($dariIndex !== null && $keIndex !== null) {
                $matrix[$dariIndex][$keIndex] = (float)$pairwise['skala'];
            }
        }
        
        // f. Isi diagonal dengan 1 dan reciprocal
        for ($i = 0; $i < $k; $i++) {
            $matrix[$i][$i] = 1;
            
            for ($j = 0; $j < $k; $j++) {
                if ($i != $j && $matrix[$i][$j] > 0 && $matrix[$j][$i] == 0) {
                    $matrix[$j][$i] = 1 / $matrix[$i][$j];
                }
            }
        }
        
        // g. Hitung jumlah unique pairs yang sudah terisi
        $filledPairs = 0;
        for ($i = 0; $i < $k; $i++) {
            for ($j = $i + 1; $j < $k; $j++) {
                if ($matrix[$i][$j] > 0 || $matrix[$j][$i] > 0) {
                    $filledPairs++;
                }
            }
        }
        
        $totalPairs = $k * ($k - 1) / 2;
        echo "  Filled pairs: $filledPairs / $totalPairs\n";
        
        // h. Cek apakah matrix sudah lengkap
        if ($filledPairs >= $totalPairs && $k >= 2) {
            echo "  ✅ Matrix LENGKAP\n";
            
            // i. Hitung AHP report
            $ahp = $anpModel->calculateAhpReport($matrix, $influencers);
            if (!$ahp || empty($ahp['weights'])) {
                echo "  ❌ Gagal menghitung AHP\n";
                continue;
            }
            
            // j. Simpan ke matrix W
            foreach ($influencers as $infIdx => $inf) {
                $iIndex = $idToIndex[$inf['id']] ?? null;
                if ($iIndex === null) continue;
                
                $W[$iIndex][$tIndex] = (float)$ahp['weights'][$infIdx];
                echo "    Influencer " . $inf['kode'] . " -> Target " . $target['kode'] . " = " . $ahp['weights'][$infIdx] . "\n";
            }
            
            $completeTargetCount++;
        } else {
            echo "  ❌ Matrix TIDAK LENGKAP\n";
        }
    }
    
    echo "\n=== HASIL ===\n";
    echo "Jumlah target yang lengkap: $completeTargetCount dari $n\n";
    
    if ($completeTargetCount === 0) {
        echo "\n❌ TIDAK ADA TARGET YANG LENGKAP!\n";
        echo "Ini menjelaskan error: 'Belum ada target yang matriksnya lengkap'\n";
        
        // Debug: cek kenapa pairwise tidak lengkap
        echo "\n=== DEBUG: ANALISIS PAIRWISE ===\n";
        
        foreach ($subkriteria as $target) {
            $targetId = $target['id'];
            $influencerEdges = $edgeModel->getInfluencerNodes($targetId, $periodeId);
            $k = count($influencerEdges);
            
            if ($k < 2) {
                echo "Target " . $target['kode'] . " (ID:$targetId): Influencer < 2 ($k)\n";
                continue;
            }
            
            // Hitung pairwise yang sudah ada untuk target ini
            $db = \Config\Database::connect();
            $pairwiseCount = $db->table('anp_pairwise_histori')
                ->where('target_node_id', $targetId)
                ->where('periode_id', $periodeId)
                ->countAllResults();
            
            $totalPairsNeeded = $k * ($k - 1) / 2;
            $canonicalCount = $db->table('anp_pairwise_histori')
                ->where('target_node_id', $targetId)
                ->where('periode_id', $periodeId)
                ->where('canonical_min_node_id IS NOT NULL')
                ->countAllResults();
            
            echo "Target " . $target['kode'] . " (ID:$targetId):\n";
            echo "  Influencer: $k nodes\n";
            echo "  Total pairs needed: $totalPairsNeeded\n";
            echo "  Pairwise count: $pairwiseCount\n";
            echo "  Canonical count: $canonicalCount\n";
            echo "  Status: " . ($pairwiseCount >= $totalPairsNeeded ? "✅ LENGKAP" : "❌ KURANG") . "\n";
        }
    } else {
        echo "\n✅ SUCCESS: Ada $completeTargetCount target yang lengkap\n";
        echo "Matrix W berhasil dibangun.\n";
    }
    
} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}