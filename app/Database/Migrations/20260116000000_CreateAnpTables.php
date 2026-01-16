<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnpTables extends Migration
{
    public function up()
    {
        // Tabel untuk cluster ANP
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
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
        $this->forge->createTable('anp_clusters', true);
        
        // Tabel untuk matriks interdependensi ANP
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'cluster_id_dari' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'cluster_id_ke' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'kriteria_id_dari' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'kriteria_id_ke' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'nilai' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'default' => 1.0,
            ],
            'tipe' => [
                'type' => 'ENUM',
                'constraint' => ['cluster_to_cluster', 'element_to_element', 'cluster_to_element', 'element_to_cluster'],
                'default' => 'element_to_element',
            ],
            'periode_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['cluster_id_dari', 'cluster_id_ke']);
        $this->forge->addKey(['kriteria_id_dari', 'kriteria_id_ke']);
        $this->forge->addKey('periode_id');
        $this->forge->createTable('anp_interdependensi', true);
        
        // Tabel untuk supermatrix ANP
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
            ],
            'tipe_matrix' => [
                'type' => 'ENUM',
                'constraint' => ['unweighted', 'weighted', 'limit'],
                'default' => 'unweighted',
            ],
            'matrix_data' => [
                'type' => 'TEXT', // JSON format
            ],
            'consistency_ratio' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => true,
            ],
            'is_konsisten' => [
                'type' => 'BOOLEAN',
                'default' => false,
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
        $this->forge->addKey('tipe_matrix');
        $this->forge->createTable('anp_supermatrix', true);
        
        // Insert default clusters untuk sistem pembinaan narapidana
        $defaultClusters = [
            [
                'nama' => 'Perilaku',
                'deskripsi' => 'Kriteria terkait perilaku dan sikap narapidana',
                'urutan' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'Kesehatan',
                'deskripsi' => 'Kriteria terkait kesehatan fisik dan mental',
                'urutan' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'Keterampilan',
                'deskripsi' => 'Kriteria terkait keterampilan dan pelatihan',
                'urutan' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama' => 'Sosial',
                'deskripsi' => 'Kriteria terkait hubungan sosial dan keluarga',
                'urutan' => 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        $this->db->table('anp_clusters')->insertBatch($defaultClusters);
    }

    public function down()
    {
        $this->forge->dropTable('anp_supermatrix', true);
        $this->forge->dropTable('anp_interdependensi', true);
        $this->forge->dropTable('anp_clusters', true);
    }
}
