<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnpEdgesTable extends Migration
{
    public function up()
    {
        // Tabel untuk edges/panah ANP (network structure)
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
                'null' => true,
            ],
            'from_node_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'to_node_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('from_node_id');
        $this->forge->addKey('to_node_id');
        
        // Unique constraint untuk mencegah duplikasi edge
        $this->forge->addUniqueKey(['periode_id', 'from_node_id', 'to_node_id']);
        
        $this->forge->createTable('anp_edges', true);
    }

    public function down()
    {
        $this->forge->dropTable('anp_edges', true);
    }
}