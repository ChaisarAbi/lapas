<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubkriteriaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kriteria_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'bobot' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,3',
                'default'    => 0.000,
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['Benefit', 'Cost'],
                'default'    => 'Benefit',
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('kriteria_id');
        $this->forge->addForeignKey('kriteria_id', 'kriteria', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subkriteria');
    }

    public function down()
    {
        $this->forge->dropTable('subkriteria');
    }
}
