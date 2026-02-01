<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangePenilaianToSubkriteria extends Migration
{
    public function up()
    {
        // Cek apakah kolom kriteria_id ada di tabel penilaian
        $fields = $this->db->getFieldData('penilaian');
        $hasKriteriaId = false;
        $hasSubkriteriaId = false;
        
        foreach ($fields as $field) {
            if ($field->name === 'kriteria_id') {
                $hasKriteriaId = true;
            }
            if ($field->name === 'subkriteria_id') {
                $hasSubkriteriaId = true;
            }
        }
        
        if ($hasKriteriaId && !$hasSubkriteriaId) {
            // 1. Backup data penilaian lama ke tabel temporary
            $this->db->query("CREATE TABLE IF NOT EXISTS penilaian_backup AS SELECT * FROM penilaian");
            
            // 2. Hapus data penilaian lama (kita akan buat ulang dengan mapping)
            $this->db->query("TRUNCATE TABLE penilaian");
            
            // 3. Ubah struktur tabel penilaian
            $this->forge->modifyColumn('penilaian', [
                'kriteria_id' => [
                    'name' => 'subkriteria_id',
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'after' => 'narapidana_id'
                ]
            ]);
            
            // 4. Tambahkan constraint foreign key ke subkriteria
            $this->forge->addForeignKey('subkriteria_id', 'subkriteria', 'id', 'CASCADE', 'CASCADE');
            
            // 5. Hapus constraint foreign key ke kriteria (jika ada)
            $this->forge->dropForeignKey('penilaian', 'penilaian_kriteria_id_foreign');
            
            echo "Migration completed. Kolom kriteria_id diubah menjadi subkriteria_id.\n";
            echo "Note: Data penilaian lama telah di-backup ke tabel 'penilaian_backup'.\n";
            echo "You need to manually migrate existing data or create new penilaian data.\n";
        } elseif ($hasSubkriteriaId) {
            // Jika sudah ada subkriteria_id, hanya tambahkan foreign key jika belum ada
            echo "Kolom subkriteria_id sudah ada. Menambahkan foreign key...\n";
            
            // Cek apakah foreign key sudah ada
            $constraints = $this->db->getForeignKeyData('penilaian');
            $hasFk = false;
            foreach ($constraints as $constraint) {
                if ($constraint->column_name === 'subkriteria_id') {
                    $hasFk = true;
                    break;
                }
            }
            
            if (!$hasFk) {
                $this->forge->addForeignKey('subkriteria_id', 'subkriteria', 'id', 'CASCADE', 'CASCADE');
                echo "Foreign key berhasil ditambahkan.\n";
            } else {
                echo "Foreign key sudah ada.\n";
            }
            
            echo "Migration completed. Struktur tabel sudah sesuai.\n";
        } else {
            echo "Migration skipped. Tidak ditemukan kolom kriteria_id atau subkriteria_id.\n";
        }
    }

    public function down()
    {
        // 1. Restore data dari backup
        $this->db->query("TRUNCATE TABLE penilaian");
        $this->db->query("INSERT INTO penilaian SELECT * FROM penilaian_backup");
        
        // 2. Ubah kembali struktur
        $this->forge->modifyColumn('penilaian', [
            'subkriteria_id' => [
                'name' => 'kriteria_id',
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'after' => 'narapidana_id'
            ]
        ]);
        
        // 3. Tambahkan constraint foreign key ke kriteria
        $this->forge->addForeignKey('kriteria_id', 'kriteria', 'id', 'CASCADE', 'CASCADE');
        
        // 4. Hapus constraint foreign key ke subkriteria
        $this->forge->dropForeignKey('penilaian', 'penilaian_subkriteria_id_foreign');
        
        // 5. Hapus tabel backup
        $this->forge->dropTable('penilaian_backup', true);
    }
}