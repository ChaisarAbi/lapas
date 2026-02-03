<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAllToAllEdgesForTargetFirst extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        echo "Starting migration: CreateAllToAllEdgesForTargetFirst\n";
        
        // Cari periode aktif (periode dengan status 'aktif')
        $periode = $db->table('periode_penilaian')
            ->where('status', 'aktif')
            ->first();
        
        if (!$periode) {
            echo "Tidak ada periode aktif ditemukan. Migration dibatalkan.\n";
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
        
        $nodeCount = count($nodes);
        echo "Found $nodeCount nodes.\n";
        echo "Node IDs: " . implode(', ', array_column($nodes, 'id')) . "\n";
        
        // Hapus semua edges existing untuk periode ini
        $deleted = $db->table('anp_edges')->where('periode_id', $periodeId)->delete();
        echo "Deleted $deleted existing edges for periode $periodeId\n";
        
        // Buat edges untuk target-first ANP
        // Untuk setiap node sebagai TARGET, buat edges dari semua node lain sebagai INFLUENCER
        $edges = [];
        $edgeCount = 0;
        
        echo "Creating edges for each target...\n";
        
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
                    echo "Inserting batch of " . count($edges) . " edges...\n";
                    $db->table('anp_edges')->insertBatch($edges);
                    $edges = [];
                }
            }
        }
        
        // Insert sisa edges
        if (!empty($edges)) {
            echo "Inserting remaining batch of " . count($edges) . " edges...\n";
            $db->table('anp_edges')->insertBatch($edges);
        }
        
        $expectedEdges = $nodeCount * ($nodeCount - 1); // n * (n-1) karena all-to-all tanpa self edges
        echo "Created $edgeCount edges for target-first ANP.\n";
        echo "Expected edges for $nodeCount nodes: $expectedEdges\n";
        echo "Edge structure: from_node_id (influencer) -> to_node_id (target)\n";
        echo "For target node 1, influencers are nodes 2-" . $nodeCount . "\n";
        echo "For target node 2, influencers are nodes 1,3-" . $nodeCount . "\n";
        echo "... and so on for all nodes.\n";
        
        // Verify total edges in database
        $totalEdges = $db->table('anp_edges')->where('periode_id', $periodeId)->countAllResults();
        echo "Total edges in database: $totalEdges\n";
        echo "Migration completed.\n";
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        echo "Starting migration down: CreateAllToAllEdgesForTargetFirst\n";
        
        // Cari periode aktif (periode dengan status 'aktif')
        $periode = $db->table('periode_penilaian')
            ->where('status', 'aktif')
            ->first();
        
        if (!$periode) {
            echo "Tidak ada periode aktif ditemukan. Migration down dibatalkan.\n";
            return;
        }
        
        $periodeId = $periode->id;
        $deleted = $db->table('anp_edges')->where('periode_id', $periodeId)->delete();
        
        echo "Deleted $deleted edges from periode $periodeId\n";
        echo "Warning: This will remove all edges created by this migration.\n";
        echo "Migration down completed.\n";
    }
}
