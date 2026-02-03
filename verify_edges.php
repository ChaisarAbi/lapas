<?php

// Script verifikasi edges
// Hubungkan ke database secara langsung
$host = 'localhost';
$dbname = 'penjara';
$username = 'root';
$password = 'leaveempty';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFIKASI EDGES ===\n\n";
    
    // 1. Cari periode aktif
    $stmt = $pdo->query("SELECT id, nama_periode FROM periode_penilaian WHERE status = 'aktif'");
    $periode = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$periode) {
        echo "Tidak ada periode aktif ditemukan.\n";
        exit;
    }
    
    $periodeId = $periode['id'];
    echo "Periode aktif: ID = " . $periode['id'] . ", Nama = " . $periode['nama_periode'] . "\n\n";
    
    // 2. Hitung jumlah subkriteria (nodes)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM subkriteria");
    $nodeCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Jumlah subkriteria (nodes): $nodeCount\n";
    
    // 3. Hitung expected edges: n * (n-1)
    $expectedEdges = $nodeCount * ($nodeCount - 1);
    echo "Expected edges (n * (n-1)): $expectedEdges\n\n";
    
    // 4. Hitung actual edges di database untuk periode ini
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM anp_edges WHERE periode_id = ?");
    $stmt->execute([$periodeId]);
    $actualEdges = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Actual edges di database: $actualEdges\n";
    
    // 5. Verifikasi count
    if ($actualEdges == $expectedEdges) {
        echo "✅ SUCCESS: Jumlah edges sesuai dengan yang diharapkan!\n";
    } else {
        echo "❌ ERROR: Jumlah edges tidak sesuai! Kurang " . ($expectedEdges - $actualEdges) . " edges.\n";
    }
    
    // 6. Periksa sample edges
    echo "\n=== SAMPLE EDGES (10 pertama) ===\n";
    $stmt = $pdo->prepare("
        SELECT e.*, 
               s_from.kode as from_kode, s_from.nama as from_nama,
               s_to.kode as to_kode, s_to.nama as to_nama
        FROM anp_edges e
        JOIN subkriteria s_from ON s_from.id = e.from_node_id
        JOIN subkriteria s_to ON s_to.id = e.to_node_id
        WHERE e.periode_id = ?
        ORDER BY e.id ASC
        LIMIT 10
    ");
    $stmt->execute([$periodeId]);
    $sampleEdges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($sampleEdges as $edge) {
        echo "Edge: " . $edge['from_kode'] . " (ID:" . $edge['from_node_id'] . ") → " 
             . $edge['to_kode'] . " (ID:" . $edge['to_node_id'] . ")\n";
    }
    
    // 7. Verifikasi untuk setiap node sebagai target
    echo "\n=== VERIFIKASI PER TARGET ===\n";
    $stmt = $pdo->query("SELECT id, kode, nama FROM subkriteria ORDER BY id");
    $nodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($nodes as $target) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM anp_edges WHERE periode_id = ? AND to_node_id = ?");
        $stmt->execute([$periodeId, $target['id']]);
        $influencerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Seharusnya setiap target memiliki (n-1) influencer
        $expectedInfluencers = $nodeCount - 1;
        
        if ($influencerCount == $expectedInfluencers) {
            echo "✅ Target " . $target['kode'] . " (ID:" . $target['id'] . "): $influencerCount influencer (sesuai)\n";
        } else {
            echo "❌ Target " . $target['kode'] . " (ID:" . $target['id'] . "): $influencerCount influencer (seharusnya $expectedInfluencers)\n";
        }
    }
    
    // 8. Verifikasi untuk setiap node sebagai influencer
    echo "\n=== VERIFIKASI PER INFLUENCER ===\n";
    foreach ($nodes as $influencer) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM anp_edges WHERE periode_id = ? AND from_node_id = ?");
        $stmt->execute([$periodeId, $influencer['id']]);
        $targetCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Seharusnya setiap influencer mempengaruhi (n-1) target
        $expectedTargets = $nodeCount - 1;
        
        if ($targetCount == $expectedTargets) {
            echo "✅ Influencer " . $influencer['kode'] . " (ID:" . $influencer['id'] . "): mempengaruhi $targetCount target (sesuai)\n";
        } else {
            echo "❌ Influencer " . $influencer['kode'] . " (ID:" . $influencer['id'] . "): mempengaruhi $targetCount target (seharusnya $expectedTargets)\n";
        }
    }
    
    echo "\n=== VERIFIKASI SELESAI ===\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}