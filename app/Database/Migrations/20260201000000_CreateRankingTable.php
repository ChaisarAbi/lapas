<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRankingTable extends Migration
{
    public function up()
    {
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
                'null' => false,
            ],
            'narapidana_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'nilai_preferensi' => [
                'type' => 'DECIMAL',
                'constraint' => '10,4',
                'null' => false,
                'default' => 0.0000,
            ],
            'ranking' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false,
                'default' => 'Tidak Layak',
            ],
            'detail_perhitungan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_validasi' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'catatan_validasi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'validator_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'tanggal_validasi' => [
                'type' => 'DATETIME',
                'null' => true,
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
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('periode_id');
        $this->forge->addKey('narapidana_id');
        $this->forge->addKey('ranking');
        $this->forge->addKey('status');
        
        // Foreign keys
        $this->forge->addForeignKey('periode_id', 'periode_penilaian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('narapidana_id', 'narapidana', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('validator_id', 'users', 'id', 'SET NULL', 'SET NULL');
        
        $this->forge->createTable('ranking', true);
        
        // Add index for faster queries
        $this->db->query('CREATE INDEX idx_ranking_periode_narapidana ON ranking(periode_id, narapidana_id)');
        $this->db->query('CREATE INDEX idx_ranking_periode_ranking ON ranking(periode_id, ranking)');
        $this->db->query('CREATE INDEX idx_ranking_status ON ranking(status)');
    }

    public function down()
    {
        $this->forge->dropTable('ranking', true);
    }
}