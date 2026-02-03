<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddWaliAndKalapasUsers extends Seeder
{
    public function run()
    {
        // Check existing users
        $existingUsers = $this->db->table('users')->select('username')->get()->getResultArray();
        $existingUsernames = array_column($existingUsers, 'username');
        
        $usersToAdd = [
            [
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Administrator',
                'role' => 'ADMIN',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'tpp01',
                'password' => password_hash('tpp123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas TPP 01',
                'role' => 'TPP',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'bimkes01',
                'password' => password_hash('bimkes123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas Bimbingan 01',
                'role' => 'BIMKEMASWAT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'wali01',
                'password' => password_hash('wali123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Wali Pembinaan 01',
                'role' => 'WALI_PEMASYARAKATAN',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'kalapas01',
                'password' => password_hash('kalapas123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Kepala Lapas 01',
                'role' => 'KEPALA_LAPAS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $insertCount = 0;
        foreach ($usersToAdd as $user) {
            if (!in_array($user['username'], $existingUsernames)) {
                $this->db->table('users')->insert($user);
                $insertCount++;
            }
        }
        
        echo "Users added successfully!\n";
        if ($insertCount > 0) {
            echo "Added $insertCount new user(s)\n";
            echo "- ADMIN: admin / admin123\n";
            echo "- TPP: tpp01 / tpp123\n";
            echo "- BIMKEMASWAT: bimkes01 / bimkes123\n";
            echo "- WALI: wali01 / wali123\n";
            echo "- KALAPAS: kalapas01 / kalapas123\n";
        } else {
            echo "All users already exist\n";
        }
    }
}