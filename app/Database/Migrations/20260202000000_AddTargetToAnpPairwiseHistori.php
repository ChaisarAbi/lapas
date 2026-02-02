<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTargetToAnpPairwiseHistori extends Migration
{
    public function up()
    {
        // Tambah kolom target_node_id, target_node_kode, target_node_nama
        $this->forge->addColumn('anp_pairwise_histori', [
            'target_node_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'periode_id'
            ],
            'target_node_kode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'target_node_id'
            ],
            'target_node_nama' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
                'after' => 'target_node_kode'
            ]
        ]);

        // Tambah index untuk query cepat
        $this->forge->addKey(['periode_id', 'target_node_id']);
        
        // Tambah unique constraint untuk upsert
        $this->forge->addKey(['periode_id', 'target_node_id', 'node_dari_id', 'node_ke_id'], false, true);
    }

    public function down()
    {
        // Hapus kolom yang ditambahkan
        $this->forge->dropColumn('anp_pairwise_histori', 'target_node_id');
        $this->forge->dropColumn('anp_pairwise_histori', 'target_node_kode');
        $this->forge->dropColumn('anp_pairwise_histori', 'target_node_nama');
        
        // Hapus index yang ditambahkan
        $this->forge->dropKey('anp_pairwise_histori', 'anp_pairwise_histori_periode_id_target_node_id');
        $this->forge->dropKey('anp_pairwise_histori', 'anp_pairwise_histori_periode_id_target_node_id_node_dari_id_node_ke_id');
    }
}