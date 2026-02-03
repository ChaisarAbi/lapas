<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReverseEdgesForTesting extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Periode aktif (periode_id = 5 dari data yang ada)
        $periodeId = 5;
        
        // Ambil semua edges existing
        $existingEdges = $db->table('anp_edges')
            ->where('periode_id', $periodeId)
            ->get()
            ->getResultArray();
        
        // Buat map untuk cek duplikat
        $edgeMap = [];
        foreach ($existingEdges as $edge) {
            $key = $edge['from_node_id'] . '_' . $edge['to_node_id'];
            $edgeMap[$key] = true;
        }
        
        // Tambahkan edges reverse (to -> from) untuk testing target-first ANP
        $reverseEdges = [];
        
        foreach ($existingEdges as $edge) {
            $fromNode = $edge['to_node_id'];    // Reverse: to menjadi from
            $toNode = $edge['from_node_id'];    // Reverse: from menjadi to
            $key = $fromNode . '_' . $toNode;
            
            // Skip jika edge sudah ada (jangan buat duplikat)
            if (!isset($edgeMap[$key]) && $fromNode != $toNode) {
                $reverseEdges[] = [
                    'periode_id' => $periodeId,
                    'from_node_id' => $fromNode,
                    'to_node_id' => $toNode,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $edgeMap[$key] = true;
            }
        }
        
        // Insert reverse edges jika ada
        if (!empty($reverseEdges)) {
            $db->table('anp_edges')->insertBatch($reverseEdges);
            
            echo "Added " . count($reverseEdges) . " reverse edges for testing target-first ANP.\n";
        } else {
            echo "No reverse edges added (already exist or no edges to reverse).\n";
        }
        
        // Juga tambahkan edges untuk target lainnya jika belum ada
        // Untuk testing, buat edges dari semua nodes ke semua nodes (kecuali diri sendiri)
        // Tapi untuk sekarang cukup reverse edges dulu
    }

    public function down()
    {
        // Tidak perlu rollback untuk testing data
        // Data ini hanya untuk testing dan bisa dihapus manual jika perlu
    }
}