<?php
// Test sederhana untuk memverifikasi data pairwise

$host = 'localhost';
$dbname = 'penjara';
$username = 'root';
$password = 'leaveempty';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFIKASI DATA PAIRWISE ===\n\n";
    
    // 1. Cari periode aktif
    $stmt = $pdo->query("SELECT id FROM periode_penilaian WHERE status = 'aktif'");
    $periode = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$periode) {
        echo "❌ Tidak ada periode aktif.\n";
        exit;
    }
    
    $periodeId = $periode['id'];
    echo "Periode aktif: ID = $periodeId\n\n";
    
    // 2. Ambil semua subkriteria
    $stmt = $pdo->query("SELECT id, kode FROM subkriteria ORDER BY id");
    $subkriteria = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total subkriteria: " . count($subkriteria) . "\n\n";
    
    // 3. Untuk setiap subkriteria sebagai target
    foreach ($subkriteria as $target) {
        $targetId = $target['id'];
        
        echo "=== TARGET: {$target['kode']} (ID: $targetId) ===\n";
        
        // a. Hitung influencer edges
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM anp_edges WHERE periode_id = ? AND to_node_id = ?");
        $stmt->execute([$periodeId, $targetId]);
        $influencerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "  Influencer edges: $influencerCount\n";
        
        if ($influencerCount >= 2) {
            // b. Hitung unique canonical pairs
            $stmt = $pdo->prepare("
                SELECT COUNT(DISTINCT CONCAT(node1_id, '-', node2_id)) as unique_pairs
                FROM anp_pairwise_histori 
                WHERE periode_id = ? AND target_node_id = ? AND node1_id IS NOT NULL AND node2_id IS NOT NULL
            ");
            $stmt->execute([$periodeId, $targetId]);
            $uniquePairs = $stmt->fetch(PDO::FETCH_ASSOC)['unique_pairs'];
            
            // c. Hitung total pairwise records (termasuk duplikat)
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anp_pairwise_histori WHERE periode_id = ? AND target_node_id = ?");
            $stmt->execute([$periodeId, $targetId]);
            $totalRecords = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $totalPairsNeeded = $influencerCount * ($influencerCount - 1) / 2;
            $isComplete = ($uniquePairs >= $totalPairsNeeded);
            
            echo "  Total records: $totalRecords\n";
            echo "  Unique canonical pairs: $uniquePairs / $totalPairsNeeded\n";
            echo "  Status: " . ($isComplete ? "✅ LENGKAP" : "❌ KURANG") . "\n";
            
            // d. Tampilkan beberapa pairwise untuk verifikasi
            if ($uniquePairs > 0) {
                $stmt = $pdo->prepare("
                    SELECT node_dari_id, node_ke_id, node1_id, node2_id, skala, value_node1_over_node2
                    FROM anp_pairwise_histori 
                    WHERE periode_id = ? AND target_node_id = ? 
                    AND node1_id IS NOT NULL AND node2_id IS NOT NULL
                    LIMIT 3
                ");
                $stmt->execute([$periodeId, $targetId]);
                $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "  Sample pairwise data:\n";
                foreach ($samples as $sample) {
                    echo "    ({$sample['node_dari_id']}→{$sample['node_ke_id']}) skala={$sample['skala']}, ";
                    echo "canonical: ({$sample['node1_id']},{$sample['node2_id']}) value={$sample['value_node1_over_node2']}\n";
                }
            }
        } else {
            echo "  ❌ Kurang dari 2 influencer, tidak bisa dibuat pairwise\n";
        }
        
        echo "\n";
    }
    
    echo "=== TES LOGIC BUILD MATRIX ===\n\n";
    
    // Test untuk target pertama
    $target = $subkriteria[0];
    $targetId = $target['id'];
    
    echo "Testing build matrix logic untuk target: {$target['kode']} (ID: $targetId)\n";
    
    // 1. Get influencer edges
    $stmt = $pdo->prepare("
        SELECT from_node_id as influencer_id
        FROM anp_edges 
        WHERE periode_id = ? AND to_node_id = ?
        ORDER BY from_node_id
    ");
    $stmt->execute([$periodeId, $targetId]);
    $influencerEdges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "  Influencer edges count: " . count($influencerEdges) . "\n";
    
    if (count($influencerEdges) < 2) {
        echo "  ❌ Tidak cukup influencer\n";
        exit;
    }
    
    // 2. Build id to index mapping
    $influencers = [];
    $idToIndex = [];
    $index = 0;
    
    foreach ($influencerEdges as $edge) {
        // Cari data subkriteria untuk influencer
        $stmt = $pdo->prepare("SELECT id, kode, nama FROM subkriteria WHERE id = ?");
        $stmt->execute([$edge['influencer_id']]);
        $infData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($infData) {
            $influencers[] = $infData;
            $idToIndex[$infData['id']] = $index;
            $index++;
        }
    }
    
    echo "  Mapped influencers: " . count($influencers) . "\n";
    
    // 3. Buat matriks kosong
    $k = count($influencers);
    $matrix = array_fill(0, $k, array_fill(0, $k, 0));
    
    // 4. Ambil pairwise data
    $stmt = $pdo->prepare("
        SELECT node_dari_id, node_ke_id, skala 
        FROM anp_pairwise_histori 
        WHERE periode_id = ? AND target_node_id = ?
    ");
    $stmt->execute([$periodeId, $targetId]);
    $pairwiseData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "  Total pairwise records: " . count($pairwiseData) . "\n";
    
    // 5. Isi matrix dari data pairwise
    foreach ($pairwiseData as $pairwise) {
        $dariIndex = $idToIndex[$pairwise['node_dari_id']] ?? null;
        $keIndex = $idToIndex[$pairwise['node_ke_id']] ?? null;
        
        if ($dariIndex !== null && $keIndex !== null) {
            $matrix[$dariIndex][$keIndex] = (float)$pairwise['skala'];
        }
    }
    
    // 6. Isi diagonal dengan 1
    for ($i = 0; $i < $k; $i++) {
        $matrix[$i][$i] = 1;
        
        // Isi nilai kebalikan (reciprocal) jika ada
        for ($j = 0; $j < $k; $j++) {
            if ($i != $j && $matrix[$i][$j] > 0 && $matrix[$j][$i] == 0) {
                $matrix[$j][$i] = 1 / $matrix[$i][$j];
            }
        }
    }
    
    // 7. Hitung filled pairs (upper triangle)
    $filledPairs = 0;
    for ($i = 0; $i < $k; $i++) {
        for ($j = $i + 1; $j < $k; $j++) {
            if ($matrix[$i][$j] > 0 || $matrix[$j][$i] > 0) {
                $filledPairs++;
            }
        }
    }
    
    $totalPairsNeeded = $k * ($k - 1) / 2;
    $isComplete = ($filledPairs >= $totalPairsNeeded);
    
    echo "  Matrix size: $k x $k\n";
    echo "  Filled pairs (upper triangle): $filledPairs / $totalPairsNeeded\n";
    echo "  Is complete: " . ($isComplete ? "✅ YA" : "❌ TIDAK") . "\n";
    
    // 8. Tampilkan beberapa nilai matrix
    echo "  Sample matrix (3x3 first):\n";
    for ($i = 0; $i < min(3, $k); $i++) {
        for ($j = 0; $j < min(3, $k); $j++) {
            echo sprintf("    [%d][%d] = %.4f  ", $i, $j, $matrix[$i][$j]);
        }
        echo "\n";
    }
    
    // 9. Cek apakah ada nilai 0 di upper triangle
    echo "  Checking for zeros in upper triangle:\n";
    $zeroCount = 0;
    for ($i = 0; $i < $k; $i++) {
        for ($j = $i + 1; $j < $k; $j++) {
            if ($matrix[$i][$j] == 0 && $matrix[$j][$i] == 0) {
                $zeroCount++;
                if ($zeroCount <= 3) { // Tampilkan hanya 3 pertama
                    echo "    ❌ Pair ($i,$j) dan ($j,$i) keduanya 0\n";
                }
            }
        }
    }
    
    if ($zeroCount > 0) {
        echo "    Total zero pairs: $zeroCount\n";
    } else {
        echo "    ✅ Semua upper triangle pairs terisi\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}