<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNarapidanaTable extends Migration
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
            'nomor_registrasi' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'nama_lengkap' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'jenis_kelamin' => [
                'type' => 'ENUM',
                'constraint' => ['Laki-laki', 'Perempuan'],
                'default' => 'Laki-laki',
            ],
            'tempat_lahir' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
            ],
            'alamat' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'kasus' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'masa_tahanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Aktif', 'Bebas', 'Pindah'],
                'default' => 'Aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('status');
        $this->forge->createTable('narapidana', true);
        
        // Insert sample data
        $this->db->table('narapidana')->insert([
            'nomor_registrasi' => 'REG001',
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1985-05-15',
            'alamat' => 'Jl. Merdeka No. 123, Jakarta',
            'kasus' => 'Pencurian dengan pemberatan',
            'masa_tahanan' => 5,
            'tanggal_masuk' => '2023-01-10',
            'status' => 'Aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->db->table('narapidana')->insert([
            'nomor_registrasi' => 'REG002',
            'nama_lengkap' => 'Siti Rahayu',
            'jenis_kelamin' => 'Perempuan',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-08-20',
            'alamat' => 'Jl. Asia Afrika No. 45, Bandung',
            'kasus' => 'Penggelapan uang',
            'masa_tahanan' => 3,
            'tanggal_masuk' => '2023-03-15',
            'status' => 'Aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->db->table('narapidana')->insert([
            'nomor_registrasi' => 'REG003',
            'nama_lengkap' => 'Joko Widodo',
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Surabaya',
            'tanggal_lahir' => '1982-11-30',
            'alamat' => 'Jl. Diponegoro No. 78, Surabaya',
            'kasus' => 'Narkotika',
            'masa_tahanan' => 10,
            'tanggal_masuk' => '2022-06-20',
            'status' => 'Aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('narapidana', true);
    }
}
