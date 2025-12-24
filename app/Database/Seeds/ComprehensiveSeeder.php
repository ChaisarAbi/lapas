<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class ComprehensiveSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        
        // Kosongkan tabel terlebih dahulu (kecuali users karena sudah ada data default)
        $this->db->table('validasi')->truncate();
        $this->db->table('penilaian')->truncate();
        $this->db->table('subkriteria')->truncate();
        $this->db->table('kriteria')->truncate();
        $this->db->table('narapidana')->truncate();
        
        // Aktifkan kembali foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        
        // Cek apakah user sudah ada, jika belum tambahkan
        $existingUsers = $this->db->table('users')->select('username')->get()->getResultArray();
        $existingUsernames = array_column($existingUsers, 'username');
        
        // 1. TAMBAH USER TAMBAHAN (hanya yang belum ada)
        $usersToAdd = [];
        $potentialUsers = [
            [
                'username' => 'tpp02',
                'password' => password_hash('tpp123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas TPP 02',
                'role' => 'TPP',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'bimkes02',
                'password' => password_hash('bimkes123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas Bimbingan 02',
                'role' => 'BIMKEMASWAT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'bimkes03',
                'password' => password_hash('bimkes123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas Bimbingan 03',
                'role' => 'BIMKEMASWAT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'wali01',
                'password' => password_hash('wali123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Wali Pemasyarakatan 01',
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
        
        foreach ($potentialUsers as $user) {
            if (!in_array($user['username'], $existingUsernames)) {
                $usersToAdd[] = $user;
            }
        }
        
        if (!empty($usersToAdd)) {
            $this->db->table('users')->insertBatch($usersToAdd);
        }
        
        // 2. DATA NARAPIDANA (40+ DATA)
        $narapidana = [];
        $jenisKejahatan = [
            'Narkotika', 'Pencurian', 'Penganiayaan', 'Penipuan', 'Korupsi',
            'Pembunuhan', 'Perampokan', 'Pemalsuan', 'Pencucian Uang', 'Terorisme'
        ];
        
        $tempatLahir = [
            'Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Makassar',
            'Semarang', 'Palembang', 'Denpasar', 'Yogyakarta', 'Malang'
        ];
        
        $jenisKelamin = ['Laki-laki', 'Perempuan'];
        
        for ($i = 1; $i <= 45; $i++) {
            $nomorRegistrasi = 'NAPI-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $kasus = $jenisKejahatan[array_rand($jenisKejahatan)];
            $tempat = $tempatLahir[array_rand($tempatLahir)];
            $tahunLahir = rand(1970, 2000);
            $bulanLahir = rand(1, 12);
            $hariLahir = rand(1, 28);
            $jk = $jenisKelamin[array_rand($jenisKelamin)];
            
            $narapidana[] = [
                'nomor_registrasi' => $nomorRegistrasi,
                'nama_lengkap' => 'Narapidana ' . $i,
                'jenis_kelamin' => $jk,
                'tempat_lahir' => $tempat,
                'tanggal_lahir' => date('Y-m-d', mktime(0, 0, 0, $bulanLahir, $hariLahir, $tahunLahir)),
                'alamat' => 'Jl. Contoh No.' . $i . ', ' . $tempat,
                'kasus' => $kasus,
                'masa_tahanan' => rand(1, 20),
                'tanggal_masuk' => date('Y-m-d', strtotime('-' . rand(1, 60) . ' months')),
                'status' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        $this->db->table('narapidana')->insertBatch($narapidana);
        
        // 3. DATA KRITERIA
        $kriteria = [
            [
                'kode' => 'K1',
                'nama' => 'Kedisiplinan',
                'bobot' => 0.25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode' => 'K2',
                'nama' => 'Kepatuhan',
                'bobot' => 0.20,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode' => 'K3',
                'nama' => 'Keterampilan',
                'bobot' => 0.15,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode' => 'K4',
                'nama' => 'Perilaku Sosial',
                'bobot' => 0.20,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode' => 'K5',
                'nama' => 'Kesehatan',
                'bobot' => 0.10,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kode' => 'K6',
                'nama' => 'Motivasi Perubahan',
                'bobot' => 0.10,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('kriteria')->insertBatch($kriteria);
        
        // 4. DATA SUBKRITERIA
        $subkriteria = [];
        $subkriteriaData = [
            'K1' => ['Hadir tepat waktu', 'Mengikuti aturan', 'Menyelesaikan tugas'],
            'K2' => ['Patuh pada petugas', 'Mengikuti program', 'Menghormati sesama'],
            'K3' => ['Keterampilan kerja', 'Kreativitas', 'Kemampuan belajar'],
            'K4' => ['Kerjasama', 'Komunikasi', 'Empati'],
            'K5' => ['Kesehatan fisik', 'Kesehatan mental', 'Kebersihan diri'],
            'K6' => ['Keinginan berubah', 'Partisipasi program', 'Rencana masa depan'],
        ];
        
        $kriteriaIds = $this->db->table('kriteria')->select('id, kode')->get()->getResultArray();
        $kriteriaMap = [];
        foreach ($kriteriaIds as $k) {
            $kriteriaMap[$k['kode']] = $k['id'];
        }
        
        foreach ($subkriteriaData as $kode => $subs) {
            $kriteriaId = $kriteriaMap[$kode];
            $bobot = 1 / count($subs); // Bobot sama rata
            
            foreach ($subs as $index => $nama) {
                $subkriteria[] = [
                    'kriteria_id' => $kriteriaId,
                    'nama' => $nama,
                    'bobot' => $bobot,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        
        $this->db->table('subkriteria')->insertBatch($subkriteria);
        
        // 5. DATA PENILAIAN (3 periode terakhir)
        $periodeList = ['2025-12', '2025-11', '2025-10'];
        $penilaian = [];
        $narapidanaIds = $this->db->table('narapidana')->select('id')->get()->getResultArray();
        $kriteriaIds = $this->db->table('kriteria')->select('id')->get()->getResultArray();
        $bimkesUsers = $this->db->table('users')->select('id')->where('role', 'BIMKEMASWAT')->get()->getResultArray();
        
        foreach ($periodeList as $periode) {
            foreach ($narapidanaIds as $napi) {
                foreach ($kriteriaIds as $kriteria) {
                    // Nilai random antara 40-95
                    $nilai = rand(40, 95);
                    $penilaiId = $bimkesUsers[array_rand($bimkesUsers)]['id'];
                    
                    $penilaian[] = [
                        'periode' => $periode,
                        'narapidana_id' => $napi['id'],
                        'kriteria_id' => $kriteria['id'],
                        'nilai' => $nilai,
                        'penilai_id' => $penilaiId,
                        'created_at' => date('Y-m-d H:i:s', strtotime($periode . '-15')),
                        'updated_at' => date('Y-m-d H:i:s', strtotime($periode . '-15')),
                    ];
                }
            }
        }
        
        $this->db->table('penilaian')->insertBatch($penilaian);
        
        // 6. DATA VALIDASI (untuk periode terakhir)
        $kalapasUser = $this->db->table('users')->select('id')->where('role', 'KEPALA_LAPAS')->get()->getRowArray();
        $validasi = [];
        $statuses = ['menunggu', 'disetujui', 'perlu_review', 'ditolak'];
        
        foreach ($narapidanaIds as $index => $napi) {
            // Untuk 30% narapidana, buat validasi
            if ($index < 15) {
                $status = $statuses[array_rand($statuses)];
                
                $validasi[] = [
                    'periode' => '2025-12',
                    'narapidana_id' => $napi['id'],
                    'status_validasi' => $status,
                    'catatan' => $status == 'disetujui' ? 'Hasil valid, dapat dilanjutkan' : ($status == 'ditolak' ? 'Perlu evaluasi ulang' : null),
                    'validated_by' => $kalapasUser['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        
        if (!empty($validasi)) {
            $this->db->table('validasi')->insertBatch($validasi);
        }
        
        echo "Seeder berhasil dijalankan!\n";
        echo "- " . count($usersToAdd) . " user tambahan\n";
        echo "- " . count($narapidana) . " narapidana\n";
        echo "- " . count($kriteria) . " kriteria\n";
        echo "- " . count($subkriteria) . " subkriteria\n";
        echo "- " . count($penilaian) . " data penilaian\n";
        echo "- " . count($validasi) . " data validasi\n";
    }
}
