<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCanonicalColumnsToPairwiseHistori extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Cek dan tambah kolom canonical jika belum ada
        $columns = $db->query("SHOW COLUMNS FROM anp_pairwise_histori")->getResultArray();
        $columnNames = array_column($columns, 'Field');
        
        // Tambah kolom node1_id jika belum ada
        if (!in_array('node1_id', $columnNames)) {
            $this->forge->addColumn('anp_pairwise_histori', [
                'node1_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'target_node_id'
                ]
            ]);
        }
        
        // Tambah kolom node2_id jika belum ada
        if (!in_array('node2_id', $columnNames)) {
            $this->forge->addColumn('anp_pairwise_histori', [
                'node2_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'node1_id'
                ]
            ]);
        }
        
        // Tambah kolom value_node1_over_node2 jika belum ada
        if (!in_array('value_node1_over_node2', $columnNames)) {
            $this->forge->addColumn('anp_pairwise_histori', [
                'value_node1_over_node2' => [
                    'type' => 'DOUBLE',
                    'null' => true,
                    'after' => 'node2_id'
                ]
            ]);
        }

        // Hapus unique key lama jika ada
        $indexExists = $db->query("SHOW INDEX FROM anp_pairwise_histori WHERE Key_name = 'anp_pairwise_histori_periode_id_target_node_id_node_dari_id_node_ke_id'")->getRow();
        if ($indexExists) {
            $this->forge->dropKey('anp_pairwise_histori', 'anp_pairwise_histori_periode_id_target_node_id_node_dari_id_node_ke_id');
        }
        
        // Tambah unique index untuk canonical pair jika belum ada
        $canonicalIndexExists = $db->query("SHOW INDEX FROM anp_pairwise_histori WHERE Key_name = 'anp_pairwise_histori_periode_id_target_node_id_node1_id_node2_id'")->getRow();
        if (!$canonicalIndexExists) {
            $this->forge->addKey(['periode_id', 'target_node_id', 'node1_id', 'node2_id'], false, true);
        }
        
        // Update data existing untuk mengisi kolom canonical
        $this->updateExistingData();
    }

    public function down()
    {
        // Hapus kolom canonical
        $this->forge->dropColumn('anp_pairwise_histori', 'node1_id');
        $this->forge->dropColumn('anp_pairwise_histori', 'node2_id');
        $this->forge->dropColumn('anp_pairwise_histori', 'value_node1_over_node2');
        
        // Kembalikan unique key lama
        $this->forge->addKey(['periode_id', 'target_node_id', 'node_dari_id', 'node_ke_id'], false, true);
    }

    /**
     * Update data existing untuk mengisi kolom canonical
     */
    private function updateExistingData()
    {
        $db = \Config\Database::connect();
        
        // Ambil semua data pairwise histori
        $pairwiseData = $db->table('anp_pairwise_histori')
            ->select('*')
            ->get()
            ->getResultArray();
        
        foreach ($pairwiseData as $row) {
            $nodeDari = $row['node_dari_id'];
            $nodeKe = $row['node_ke_id'];
            $skala = (float)$row['skala'];
            
            // Tentukan node1 dan node2 (canonical order)
            $node1 = min($nodeDari, $nodeKe);
            $node2 = max($nodeDari, $nodeKe);
            
            // Hitung value_node1_over_node2
            if ($nodeDari == $node1) {
                $value = $skala; // node_dari adalah node1
            } else {
                $value = 1 / $skala; // node_dari adalah node2, jadi reciprocal
            }
            
            // Update record dengan canonical values
            $db->table('anp_pairwise_histori')
                ->where('id', $row['id'])
                ->update([
                    'node1_id' => $node1,
                    'node2_id' => $node2,
                    'value_node1_over_node2' => $value
                ]);
        }
        
        // Hapus duplikat berdasarkan canonical pair
        $this->removeDuplicateCanonicalPairs();
    }

    /**
     * Hapus duplikat canonical pairs
     */
    private function removeDuplicateCanonicalPairs()
    {
        $db = \Config\Database::connect();
        
        // Cari duplikat canonical pairs
        $duplicates = $db->table('anp_pairwise_histori')
            ->select('periode_id, target_node_id, node1_id, node2_id, COUNT(*) as count, MIN(id) as keep_id')
            ->where('node1_id IS NOT NULL')
            ->where('node2_id IS NOT NULL')
            ->groupBy('periode_id, target_node_id, node1_id, node2_id')
            ->having('count > 1')
            ->get()
            ->getResultArray();
        
        foreach ($duplicates as $dup) {
            // Hapus duplikat, simpan hanya record dengan id terkecil
            $db->table('anp_pairwise_histori')
                ->where('periode_id', $dup['periode_id'])
                ->where('target_node_id', $dup['target_node_id'])
                ->where('node1_id', $dup['node1_id'])
                ->where('node2_id', $dup['node2_id'])
                ->where('id !=', $dup['keep_id'])
                ->delete();
        }
    }
}