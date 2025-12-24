<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateValidasiTable extends Migration
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
            'periode' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'comment' => 'Format: YYYY-MM',
            ],
            'narapidana_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status_validasi' => [
                'type' => 'ENUM',
                'constraint' => ['menunggu', 'disetujui', 'perlu_review', 'ditolak'],
                'default' => 'menunggu',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'validated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID yang melakukan validasi',
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
        $this->forge->addKey(['periode', 'narapidana_id'], false);
        $this->forge->addForeignKey('narapidana_id', 'narapidana', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('validated_by', 'users', 'id', 'SET NULL', 'SET NULL');
        
        $this->forge->createTable('validasi');
    }

    public function down()
    {
        $this->forge->dropTable('validasi');
    }
}
