<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeriodePenilaianTable extends Migration
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
            'nama_periode' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'tahun' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => false,
            ],
            'bulan' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => false,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['aktif', 'nonaktif', 'selesai'],
                'default' => 'nonaktif',
                'null' => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
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
        $this->forge->addKey(['tahun', 'bulan']);
        $this->forge->addKey('status');
        $this->forge->createTable('periode_penilaian');
    }

    public function down()
    {
        $this->forge->dropTable('periode_penilaian');
    }
}
