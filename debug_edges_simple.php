<?php
// Script debug sederhana untuk memeriksa data edges

// Konfigurasi database
$host = 'localhost';
$dbname = 'lapas';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DEBUG EDGES ===\n\n";
    
    // Ambil periode aktif
    $stmt = $pdo->query("SELECT id, nama FROM anp_periode WHERE status = 'aktif' LIMIT 1");
    $periode = $stmt->fetch(PDO::FETCH_ASSOC);
    $periodeId = $periode ? $periode['id'] : null;
    
    echo "Periode ID: " . ($periodeId ?? 'null') . "\n";
    echo "Periode Nama: " . ($periode['nama'] ?? 'null') . "\n\n";
    
    // Ambil semua subkriteria
    $stmt = $pdo->query("SELECT id, kode, nama, kriteria_id FROM subkriteria ORDER BY id");
    $subkriteria = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total subkriteria: " . count($subkriteria) . "\n";
    foreach ($subkriteria as $sk) {
        echo "  {$sk['id']}: {$sk['kode']} - {$sk['nama']}\n";
    }
    echo "\n";
    
    // Ambil semua edges
    $stmt = $pdo->prepare("SELECT from_node_id, to_node_id FROM anp_edges WHERE periode_id = ? ORDER BY from_node_id, to_node_id");
    $stmt->execute([$periodeId]);
    $edges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total edges: " . count($edges) . "\n";
    foreach ($edges as $edge) {
        echo "  From: {$edge['from_node_id']} -> To: {$edge['to_node_id']}\n";
    }
    echo "\n";
    
    // Cek influencer nodes untuk KP1 (node 1)
    $targetNodeId = 1;
    $stmt = $pdo->prepare("
        SELECT s.id, s.kode, s.nama, s.kriteria_id, k.nama as kriteria_nama
        FROM anp_edges e
        JOIN subkriteria s ON s.id = e.from_node_id
        JOIN kriteria k ON k.id = s.kriteria_id
        WHERE e.to_node_id = ? AND e.periode_id = ?
        ORDER BY s.kriteria_id, s.kode
    ");
    $stmt->execute([$targetNodeId, $periodeId]);
    $influencers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== INFLUENCER NODES FOR TARGET $targetNodeId ===\n";
    echo "Jumlah influencer: " . count($influencers) . "\n";
    foreach ($influencers as $inf) {
        echo "  {$inf['id']}: {$inf['kode']} - {$inf['nama']} ({$inf['kriteria_nama']})\n";
    }
    echo "\n";
    
    // Cek targets nodes untuk KP1 (node 1)
    $stmt = $pdo->prepare("
        SELECT s.id, s.kode, s.nama, s.kriteria_id, k.nama as kriteria_nama
        FROM anp_edges e
        JOIN subkriteria s ON s.id = e.to_node_id
        JOIN kriteria k ON k.id = s.kriteria_id
        WHERE e.from_node_id = ? AND e.periode_id = ?
        ORDER BY s.kriteria_id, s.kode
    ");
    $stmt->execute([$targetNodeId, $periodeId]);
    $targets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== TARGET NODES FOR INFLUENCER $targetNodeId ===\n";
    echo "Jumlah target: " . count($targets) . "\n";
    foreach ($targets as $tgt) {
        echo "  {$tgt['id']}: {$tgt['kode']} - {$tgt['nama']} ({$tgt['kriteria_nama']})\n";
    }
    echo "\n";
    
    echo "=== SELESAI ===\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}