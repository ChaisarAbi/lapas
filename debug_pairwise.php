<?php

// Debug script untuk analisis pairwise
$host = 'localhost';
$dbname = 'penjara';
$username = 'root';
$password = 'leaveempty';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUG PAIRWISE ANALISIS ===\n\n";
    
    // 1. Cari periode aktif
    $stmt = $pdo->query("SELECT id, nama_periode FROM periode_penilaian WHERE status = 'aktif'");
    $periode = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$periode) {
        echo "Tidak ada periode aktif ditemukan.\n";
        exit;
    }
    
    $periodeId = $periode['id'];
    echo "Periode aktif: ID = " . $periode['id'] . ", Nama = " . $periode['nama_periode'] . "\n\n";
    
    // 2. Ambil semua subkriteria
    $stmt = $pdo->query("SELECT id, kode, nama FROM subkriteria ORDER BY id");
    $subkriteria = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $n = count($subkriteria);
    echo "Jumlah subkriteria: $n\n\n";
    
    // 3. Ambil semua edges
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM anp_edges WHERE periode_id = ?");
    $stmt->execute([$periodeId]);
    $edgesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Total edges: $edgesCount\n\n";
    
    // 4. Analisis untuk setiap target
    echo "=== ANALISIS PER TARGET ===\n";
    
    foreach ($subkriteria as $target) {
        $targetId = $target['id'];
        
        // a. Dapatkan influencer edges
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM anp_edges 
            WHERE periode_id = ? AND to_node_id = ?
        ");
        $stmt->execute([$periodeId, $targetId]);
        $influencerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // b. Dapatkan pairwise untuk target ini
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM anp_pairwise_histori 
            WHERE periode_id = ? AND target_node_id = ?
        ");
        $stmt->execute([$periodeId, $targetId]);
        $pairwiseCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // c. Dapatkan canonical pairwise untuk target ini
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM anp_pairwise_histori 
            WHERE periode_id = ? AND target_node_id = ? 
            AND canonical_min_node_id IS NOT NULL
        ");
        $stmt->execute([$periodeId, $targetId]);
        $canonicalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // d. Hitung total pairs yang diperlukan
        if ($influencerCount < 2) {
            echo "Target " . $target['kode'] . " (ID:$targetId): Influencer < 2 ($influencerCount) - SKIP\n";
            continue;
        }
        
        $totalPairsNeeded = $influencerCount * ($influencerCount - 1) / 2;
        $isComplete = ($pairwiseCount >= $totalPairsNeeded);
        
        echo "Target " . $target['kode'] . " (ID:$targetId):\n";
        echo "  Influencer: $influencerCount nodes\n";
        echo "  Total pairs needed: $totalPairsNeeded\n";
        echo "  Pairwise count: $pairwiseCount\n";
        echo "  Canonical count: $canonicalCount\n";
        echo "  Status: " . ($isComplete ? "✅ LENGKAP" : "❌ KURANG") . "\n";
        
        // e. Jika tidak lengkap, tampilkan detail
        if (!$isComplete && $pairwiseCount > 0) {
            echo "  Detail pairwise:\n";
            
            // Ambil sample pairwise
            $stmt = $pdo->prepare("
                SELECT node_dari_id, node_ke_id, skala, canonical_min_node_id, canonical_max_node_id
                FROM anp_pairwise_histori 
                WHERE periode_id = ? AND target_node_id = ?
                LIMIT 5
            ");
            $stmt->execute([$periodeId, $targetId]);
            $samplePairs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($samplePairs as $pair) {
                echo "    Dari: " . $pair['node_dari_id'] . " -> Ke: " . $pair['node_ke_id'] . 
                     ", Skala: " . $pair['skala'] . 
                     ", Canonical: " . ($pair['canonical_min_node_id'] ? $pair['canonical_min_node_id'] . '-' . $pair['canonical_max_node_id'] : 'NO') . "\n";
            }
            
            // Hitung unique pairs
            $stmt = $pdo->prepare("
                SELECT COUNT(DISTINCT CONCAT(
                    LEAST(node_dari_id, node_ke_id), 
                    '-', 
                    GREATEST(node_dari_id, node_ke_id)
                )) as unique_pairs
                FROM anp_pairwise_histori 
                WHERE periode_id = ? AND target_node_id = ?
            ");
            $stmt->execute([$periodeId, $targetId]);
            $uniquePairs = $stmt->fetch(PDO::FETCH_ASSOC)['unique_pairs'];
            echo "  Unique pairs: $uniquePairs (seharusnya $totalPairsNeeded)\n";
        }
        
        echo "\n";
    }
    
    // 5. Analisis keseluruhan pairwise
    echo "\n=== ANALISIS KESELURUHAN ===\n";
    
    // Total pairwise di database
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anp_pairwise_histori WHERE periode_id = ?");
    $stmt->execute([$periodeId]);
    $totalPairwise = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total pairwise di database: $totalPairwise\n";
    
    // Pairwise dengan canonical
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anp_pairwise_histori WHERE periode_id = ? AND canonical_min_node_id IS NOT NULL");
    $stmt->execute([$periodeId]);
    $totalCanonical = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total pairwise canonical: $totalCanonical\n";
    
    // Pairwise tanpa canonical
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anp_pairwise_histori WHERE periode_id = ? AND canonical_min_node_id IS NULL");
    $stmt->execute([$periodeId]);
    $totalNonCanonical = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total pairwise non-canonical: $totalNonCanonical\n";
    
    // 6. Cek duplikat pairwise (dari data sebelumnya: 272 pairwise per target)
    echo "\n=== CEK DUPLIKAT PAIRWISE ===\n";
    foreach ($subkriteria as $target) {
        $targetId = $target['id'];
        
        // Hitung semua pairwise untuk target ini
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM anp_pairwise_histori WHERE periode_id = ? AND target_node_id = ?");
        $stmt->execute([$periodeId, $targetId]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count > 0) {
            // Hitung unique canonical pairs
            $stmt = $pdo->prepare("
                SELECT COUNT(DISTINCT CONCAT(canonical_min_node_id, '-', canonical_max_node_id)) as unique_canonical
                FROM anp_pairwise_histori 
                WHERE periode_id = ? AND target_node_id = ? AND canonical_min_node_id IS NOT NULL
            ");
            $stmt->execute([$periodeId, $targetId]);
            $uniqueCanonical = $stmt->fetch(PDO::FETCH_ASSOC)['unique_canonical'];
            
            echo "Target " . $target['kode'] . " (ID:$targetId): $count pairwise, $uniqueCanonical unique canonical pairs\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}