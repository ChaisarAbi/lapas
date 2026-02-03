<?php

namespace App\Test;

use CodeIgniter\Database\Migration;

class TestMigrationEdges
{
    public function test()
    {
        $db = \Config\Database::connect();
        
        // Cari periode aktif (periode dengan status 'aktif')
        $periode = $db->table('periode_penilaian')
            ->where('status', 'aktif')
            ->first();
        
        if (!$periode) {
            echo "Tidak ada periode aktif ditemukan.\n";
            return;
        }
        
        $periodeId = $periode->id;
        echo "Periode aktif ditemukan: ID = $periodeId, Nama = " . $periode->nama_periode . "\n";
        
        // Ambil semua subkriteria (nodes)
        $nodes = $db->table('subkriteria')
            ->select('id')
            ->orderBy('id')
            ->get()
            ->getResultArray();
        
        if (empty($nodes)) {
            echo "No nodes found.\n";
            return;
        }
        
        echo "Found " . count($nodes) . " nodes.\n";
        
        // Hapus semua edges existing untuk periode ini
        $deleted = $db->table('anp_edges')->where('periode_id', $periodeId)->delete();
        echo "Deleted $deleted existing edges for periode $periodeId\n";
        
        // Buat edges untuk target-first ANP
        // Untuk setiap node sebagai TARGET, buat edges dari semua node lain sebagai INFLUENCER
        $edges = [];
        $edgeCount = 0;
        
        foreach ($nodes as $targetNode) {
            $targetId = $targetNode['id'];
            
            foreach ($nodes as $influencerNode) {
                $influencerId = $influencerNode['id'];
                
                // Skip self-edge (node tidak mempengaruhi dirinya sendiri)
                if ($targetId == $influencerId) {
                    continue;
                }
                
                // Untuk target-first ANP: influencer -> target
                // from_node_id = influencer, to_node_id = target
                $edges[] = [
                    'periode_id' => $periodeId,
                    'from_node_id' => $influencerId,
                    'to_node_id' => $targetId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $edgeCount++;
                
                // Insert batch setiap 100 edges untuk menghindari memory issues
                if (count($edges) >= 100) {
                    $db->table('anp_edges')->insertBatch($edges);
                    echo "Inserted batch of " . count($edges) . " edges\n";
                    $edges = [];
                }
            }
        }
        
        // Insert sisa edges
        if (!empty($edges)) {
            $db->table('anp_edges')->insertBatch($edges);
            echo "Inserted remaining batch of " . count($edges) . " edges\n";
        }
        
        echo "Created $edgeCount edges for target-first ANP.\n";
        echo "Edge structure: from_node_id (influencer) -> to_node_id (target)\n";
        echo "For target node 1, influencers are nodes 2-" . count($nodes) . "\n";
        echo "For target node 2, influencers are nodes 1,3-" . count($nodes) . "\n";
        echo "... and so on for all nodes.\n";
        
        // Verify the edges were created
        $totalEdges = $db->table('anp_edges')->where('periode_id', $periodeId)->countAllResults();
        echo "Total edges in database: $totalEdges\n";
    }
}

// Run the test
require_once __DIR__ . '/vendor/autoload.php';
$test = new TestMigrationEdges();
$test->test();