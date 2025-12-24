<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddWaliAndKalapasUsers extends Seeder
{
    public function run()
    {
        // Insert sample WALI_PEMASYARAKATAN user
        $this->db->table('users')->insert([
            'username' => 'wali01',
            'password' => password_hash('wali123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Wali Pembinaan 01',
            'role' => 'WALI_PEMASYARAKATAN',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Insert sample KEPALA_LAPAS user
        $this->db->table('users')->insert([
            'username' => 'kalapas01',
            'password' => password_hash('kalapas123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Kepala Lapas 01',
            'role' => 'KEPALA_LAPAS',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        // Insert additional BIMKEMASWAT user
        $this->db->table('users')->insert([
            'username' => 'bimkes02',
            'password' => password_hash('bimkes123', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Petugas Bimbingan 02',
            'role' => 'BIMKEMASWAT',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        echo "Users added successfully!\n";
    }
}
