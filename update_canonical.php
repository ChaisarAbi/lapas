<?php

// Script untuk mengupdate data pairwise existing ke format canonical
$host = 'localhost';
$dbname = 'penjara';
$username = 'root';
$password = 'leaveempty';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== UPDATE PAIRWISE KE FORMAT CANONICAL ===\n\n";
    
    // 1. Cari periode aktif
    $stmt = $pdo->query("SELECT id FROM periode_penilaian WHERE status = 'aktif'");
    $periode = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$periode) {
        echo "Tidak ada periode aktif ditemukan.\n";
        exit;
    }
    
    $periodeId = $periode['id'];
    echo "Periode aktif: ID = $periodeId\n\n";
    
    // 2. Hitung total pairwise sebelum update
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anp_pairwise_histori WHERE periode_id = ?");
    $stmt->execute([$periodeId]);
    $beforeCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total pairwise sebelum update: $beforeCount\n";
    
    // 3. Hitung pairwise yang sudah canonical (node1_id dan node2_id terisi)
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anp_pairwise_histori WHERE periode_id = ? AND node1_id IS NOT NULL AND node2_id IS NOT NULL");
    $stmt->execute([$periodeId]);
    $canonicalCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Pairwise sudah canonical: $canonicalCount\n";
    
    // 4. Update pairwise yang belum canonical
    echo "\n=== MENGUPDATE DATA CANONICAL ===\n";
    
    // Update node1_id, node2_id, dan value_node1_over_node2
    $stmt = $pdo->prepare("
        UPDATE anp_pairwise_histori 
        SET 
            node1_id = LEAST(node_dari_id, node_ke_id),
            node2_id = GREATEST(node_dari_id, node_ke_id),
            value_node1_over_node2 = CASE 
                WHEN node_dari_id = LEAST(node_dari_id, node_ke_id) THEN skala
                ELSE 1.0 / skala
            END
        WHERE periode_id = ? 
            AND (node1_id IS NULL OR node2_id IS NULL)
    ");
    $stmt->execute([$periodeId]);
    $updatedRows = $stmt->rowCount();
    
    echo "Berhasil update $updatedRows rows ke format canonical.\n\n";
    
    // 5. Verifikasi setelah update
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anp_pairwise_histori WHERE periode_id = ?");
    $stmt->execute([$periodeId]);
    $afterCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Total pairwise setelah update: $afterCount\n";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM anp_pairwise_histori WHERE periode_id = ? AND node1_id IS NOT NULL AND node2_id IS NOT NULL");
    $stmt->execute([$periodeId]);
    $canonicalAfter = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "Pairwise canonical setelah update: $canonicalAfter\n";
    
    // 6. Hapus duplikat berdasarkan canonical pair (node1_id, node2_id)
    echo "\n=== MENGHAPUS DUPLIKAT CANONICAL PAIRS ===\n";
    
    // Cari duplikat canonical pairs
    $stmt = $pdo->prepare("
        SELECT periode_id, target_node_id, node1_id, node2_id, COUNT(*) as count, MIN(id) as keep_id
        FROM anp_pairwise_histori 
        WHERE periode_id = ? 
            AND node1_id IS NOT NULL 
            AND node2_id IS NOT NULL
        GROUP BY periode_id, target_node_id, node1_id, node2_id
        HAVING count > 1
    ");
    $stmt->execute([$periodeId]);
    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Ditemukan " . count($duplicates) . " duplikat canonical pairs.\n";
    
    foreach ($duplicates as $dup) {
        // Hapus duplikat, simpan hanya record dengan id terkecil
        $deleteStmt = $pdo->prepare("
            DELETE FROM anp_pairwise_histori 
            WHERE periode_id = ? 
                AND target_node_id = ? 
                AND node1_id = ? 
                AND node2_id = ? 
                AND id != ?
        ");
        $deleteStmt->execute([
            $dup['periode_id'], 
            $dup['target_node_id'], 
            $dup['node1_id'], 
            $dup['node2_id'], 
            $dup['keep_id']
        ]);
        echo "  Dihapus duplikat untuk target {$dup['target_node_id']}, pair ({$dup['node1_id']},{$dup['node2_id']})\n";
    }
    
    // 7. Hitung unique canonical pairs per target
    echo "\n=== UNIQUE CANONICAL PAIRS PER TARGET ===\n";
    
    // Ambil semua subkriteria
    $stmt = $pdo->query("SELECT id, kode FROM subkriteria ORDER BY id");
    $subkriteria = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($subkriteria as $target) {
        $targetId = $target['id'];
        
        // Hitung influencer edges untuk target ini
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM anp_edges WHERE periode_id = ? AND to_node_id = ?");
        $stmt->execute([$periodeId, $targetId]);
        $influencerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($influencerCount >= 2) {
            // Hitung unique canonical pairs untuk target ini
            $stmt = $pdo->prepare("
                SELECT COUNT(DISTINCT CONCAT(node1_id, '-', node2_id)) as unique_pairs
                FROM anp_pairwise_histori 
                WHERE periode_id = ? AND target_node_id = ? AND node1_id IS NOT NULL AND node2_id IS NOT NULL
            ");
            $stmt->execute([$periodeId, $targetId]);
            $uniquePairs = $stmt->fetch(PDO::FETCH_ASSOC)['unique_pairs'];
            
            $totalPairsNeeded = $influencerCount * ($influencerCount - 1) / 2;
            $isComplete = ($uniquePairs >= $totalPairsNeeded);
            
            echo "Target " . $target['kode'] . " (ID:$targetId):\n";
            echo "  Influencer: $influencerCount nodes\n";
            echo "  Unique canonical pairs: $uniquePairs / $totalPairsNeeded\n";
            echo "  Status: " . ($isComplete ? "✅ LENGKAP" : "❌ KURANG") . "\n\n";
        }
    }
    
    echo "\n=== UPDATE SELESAI ===\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
