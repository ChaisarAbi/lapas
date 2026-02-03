<?php
// Verifikasi consistency antara edges dan pairwise data

$host = 'localhost';
$dbname = 'penjara';
$username = 'root';
$password = 'leaveempty';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFIKASI CONSISTENCY EDGES DAN PAIRWISE ===\n\n";
    
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
    
    // 3. Ambil semua edges
    $stmt = $pdo->prepare("SELECT from_node_id, to_node_id FROM anp_edges WHERE periode_id = ?");
    $stmt->execute([$periodeId]);
    $edges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total edges: " . count($edges) . "\n";
    
    // Hitung edges per target
    $edgesPerTarget = [];
    foreach ($edges as $edge) {
        $targetId = $edge['to_node_id'];
        if (!isset($edgesPerTarget[$targetId])) {
            $edgesPerTarget[$targetId] = [];
        }
        $edgesPerTarget[$targetId][] = $edge['from_node_id'];
    }
    
    // 4. Untuk setiap target, verifikasi pairwise data
    foreach ($subkriteria as $target) {
        $targetId = $target['id'];
        
        echo "\n=== TARGET: ID {$targetId} ===\n";
        
        $influencers = $edgesPerTarget[$targetId] ?? [];
        echo "Influencers dari edges: " . count($influencers) . " nodes\n";
        
        if (count($influencers) < 2) {
            echo "❌ Kurang dari 2 influencers\n";
            continue;
        }
        
        // Ambil semua pairwise untuk target ini
        $stmt = $pdo->prepare("
            SELECT node_dari_id, node_ke_id, skala, node1_id, node2_id 
            FROM anp_pairwise_histori 
            WHERE periode_id = ? AND target_node_id = ?
        ");
        $stmt->execute([$periodeId, $targetId]);
        $pairwiseData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Total pairwise records: " . count($pairwiseData) . "\n";
        
        // Hitung unique canonical pairs
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT CONCAT(node1_id, '-', node2_id)) as unique_pairs
            FROM anp_pairwise_histori 
            WHERE periode_id = ? AND target_node_id = ? AND node1_id IS NOT NULL AND node2_id IS NOT NULL
        ");
        $stmt->execute([$periodeId, $targetId]);
        $uniquePairs = $stmt->fetch(PDO::FETCH_ASSOC)['unique_pairs'];
        
        // Hitung pairs yang seharusnya (combinations of influencers)
        $k = count($influencers);
        $totalPairsNeeded = $k * ($k - 1) / 2;
        
        echo "Unique canonical pairs: $uniquePairs / $totalPairsNeeded\n";
        
        // Cek apakah semua pairwise adalah antara influencer nodes
        $invalidPairwise = [];
        foreach ($pairwiseData as $pw) {
            $dari = $pw['node_dari_id'];
            $ke = $pw['node_ke_id'];
            
            if (!in_array($dari, $influencers) || !in_array($ke, $influencers)) {
                $invalidPairwise[] = "$dari→$ke";
            }
        }
        
        if (!empty($invalidPairwise)) {
            echo "❌ Invalid pairwise (bukan antara influencer nodes): " . implode(', ', array_slice($invalidPairwise, 0, 5));
            if (count($invalidPairwise) > 5) echo " ... (" . count($invalidPairwise) . " total)";
            echo "\n";
        } else {
            echo "✅ Semua pairwise adalah antara influencer nodes\n";
        }
        
        // Cek completeness
        $isComplete = ($uniquePairs >= $totalPairsNeeded);
        echo "Status completeness: " . ($isComplete ? "✅ LENGKAP" : "❌ KURANG") . "\n";
        
        // Tampilkan beberapa influencer
        echo "Sample influencers (" . min(5, count($influencers)) . " of " . count($influencers) . "): ";
        echo implode(', ', array_slice($influencers, 0, 5)) . "\n";
        
        // Tampilkan beberapa pairwise
        if (count($pairwiseData) > 0) {
            echo "Sample pairwise (max 3):\n";
            for ($i = 0; $i < min(3, count($pairwiseData)); $i++) {
                $pw = $pairwiseData[$i];
                echo "  {$pw['node_dari_id']}→{$pw['node_ke_id']} = {$pw['skala']}";
                if ($pw['node1_id'] && $pw['node2_id']) {
                    echo " [canonical: {$pw['node1_id']},{$pw['node2_id']}]";
                }
                echo "\n";
            }
        }
    }
    
    // 5. Summary
    echo "\n=== SUMMARY ===\n";
    
    $completeTargets = 0;
    $incompleteTargets = 0;
    
    foreach ($subkriteria as $target) {
        $targetId = $target['id'];
        $influencers = $edgesPerTarget[$targetId] ?? [];
        
        if (count($influencers) >= 2) {
            $stmt = $pdo->prepare("
                SELECT COUNT(DISTINCT CONCAT(node1_id, '-', node2_id)) as unique_pairs
                FROM anp_pairwise_histori 
                WHERE periode_id = ? AND target_node_id = ? AND node1_id IS NOT NULL AND node2_id IS NOT NULL
            ");
            $stmt->execute([$periodeId, $targetId]);
            $uniquePairs = $stmt->fetch(PDO::FETCH_ASSOC)['unique_pairs'];
            
            $k = count($influencers);
            $totalPairsNeeded = $k * ($k - 1) / 2;
            
            if ($uniquePairs >= $totalPairsNeeded) {
                $completeTargets++;
            } else {
                $incompleteTargets++;
            }
        }
    }
    
    echo "Total targets dengan ≥2 influencers: " . ($completeTargets + $incompleteTargets) . "\n";
    echo "Complete targets: $completeTargets\n";
    echo "Incomplete targets: $incompleteTargets\n";
    
    if ($completeTargets == 0) {
        echo "\n❌ TIDAK ADA TARGET YANG LENGKAP!\n";
        echo "Ini menjelaskan error 'Belum ada target yang matriksnya lengkap'\n";
    } else {
        echo "\n✅ Ada $completeTargets target yang lengkap, seharusnya bisa hitung ANP.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}