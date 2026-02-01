<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnpPairwiseHistoriTable extends Migration
{
    public function up()
    {
        // Tabel untuk histori pairwise comparison
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'periode_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'node_dari_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'node_dari_kode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'node_dari_nama' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'node_ke_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'node_ke_kode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'node_ke_nama' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'skala' => [
                'type' => 'DECIMAL',
                'constraint' => '3,2',
                'default' => 1.0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('periode_id');
        $this->forge->addKey(['node_dari_id', 'node_ke_id']);
        $this->forge->addKey('created_at');
        $this->forge->createTable('anp_pairwise_histori', true);
    }

    public function down()
    {
        $this->forge->dropTable('anp_pairwise_histori', true);
    }
}