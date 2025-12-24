<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenilaianTable extends Migration
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
            'narapidana_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'kriteria_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nilai' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
            ],
            'periode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'penilai_id' => [
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
        $this->forge->addKey('narapidana_id');
        $this->forge->addKey('kriteria_id');
        $this->forge->addKey('periode');
        $this->forge->addKey('penilai_id');
        $this->forge->addForeignKey('narapidana_id', 'narapidana', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('penilai_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penilaian', true);
        
        // Buat tabel kriteria terlebih dahulu jika belum ada
        if (!$this->db->tableExists('kriteria')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'kode' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                    'unique' => true,
                ],
                'nama' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'bobot' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,3',
                    'default' => 0.000,
                ],
                'jenis' => [
                    'type' => 'ENUM',
                    'constraint' => ['Benefit', 'Cost'],
                    'default' => 'Benefit',
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
            $this->forge->createTable('kriteria', true);
            
            // Insert sample kriteria
            $kriteria = [
                ['kode' => 'K1', 'nama' => 'Kedisiplinan', 'bobot' => 0.150, 'jenis' => 'Benefit'],
                ['kode' => 'K2', 'nama' => 'Kerja Sama', 'bobot' => 0.125, 'jenis' => 'Benefit'],
                ['kode' => 'K3', 'nama' => 'Keterampilan', 'bobot' => 0.175, 'jenis' => 'Benefit'],
                ['kode' => 'K4', 'nama' => 'Perilaku', 'bobot' => 0.200, 'jenis' => 'Benefit'],
                ['kode' => 'K5', 'nama' => 'Kesehatan', 'bobot' => 0.125, 'jenis' => 'Benefit'],
                ['kode' => 'K6', 'nama' => 'Pelanggaran', 'bobot' => 0.100, 'jenis' => 'Cost'],
                ['kode' => 'K7', 'nama' => 'Partisipasi', 'bobot' => 0.075, 'jenis' => 'Benefit'],
                ['kode' => 'K8', 'nama' => 'Motivasi', 'bobot' => 0.050, 'jenis' => 'Benefit'],
            ];
            
            foreach ($kriteria as $k) {
                $this->db->table('kriteria')->insert(array_merge($k, [
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]));
            }
        }
        
        // Insert sample penilaian
        $periode = '2025-12';
        $penilai_id = 3; // BIMKEMASWAT user
        
        // Untuk setiap narapidana, beri nilai untuk setiap kriteria
        $narapidana = $this->db->table('narapidana')->get()->getResultArray();
        $kriteria = $this->db->table('kriteria')->get()->getResultArray();
        
        foreach ($narapidana as $napi) {
            foreach ($kriteria as $k) {
                // Generate nilai acak antara 60-100 untuk benefit, 0-40 untuk cost
                $nilai = $k['jenis'] == 'Benefit' ? rand(70, 95) : rand(0, 30);
                
                $this->db->table('penilaian')->insert([
                    'narapidana_id' => $napi['id'],
                    'kriteria_id' => $k['id'],
                    'nilai' => $nilai,
                    'periode' => $periode,
                    'penilai_id' => $penilai_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('penilaian', true);
        $this->forge->dropTable('kriteria', true);
    }
}
