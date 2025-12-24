<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
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
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'nama_lengkap' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['ADMIN', 'TPP', 'BIMKEMASWAT', 'WALI_PEMASYARAKATAN', 'KEPALA_LAPAS'],
                'default' => 'BIMKEMASWAT',
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
        $this->forge->addKey('role');
        $this->forge->createTable('users', true);
        
        // Insert default admin user
        $this->db->table('users')->insert([
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Administrator Sistem',
            'role' => 'ADMIN',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Insert sample TPP user
        $this->db->table('users')->insert([
            'username' => 'tpp01',
            'password' => password_hash('tpp123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Petugas TPP 01',
            'role' => 'TPP',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Insert sample BIMKEMASWAT user
        $this->db->table('users')->insert([
            'username' => 'bimkes01',
            'password' => password_hash('bimkes123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Petugas Bimbingan 01',
            'role' => 'BIMKEMASWAT',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
