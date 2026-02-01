<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnpTestSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama jika ada (dalam urutan yang benar untuk menghindari foreign key constraint)
        $this->db->table('anp_pairwise_histori')->where('id >', 0)->delete();
        $this->db->table('anp_interdependensi')->where('id >', 0)->delete();
        // Hapus tabel lain yang mungkin memiliki foreign key ke subkriteria atau kriteria
        $this->db->table('penilaian')->where('id >', 0)->delete(); // Jika ada tabel penilaian yang merujuk ke subkriteria
        $this->db->table('subkriteria')->where('id >', 0)->delete();
        $this->db->table('kriteria')->where('id >', 0)->delete();
        $this->db->table('anp_clusters')->where('id >', 0)->delete();
        $this->db->table('periode_penilaian')->where('id >', 0)->delete();
        
        // 1. Buat 4 Cluster untuk Penilaian Remisi Tahanan
        $clusters = [
            [
                'id' => 1,
                'nama' => 'Perilaku dan Disiplin',
                'deskripsi' => 'Penilaian terhadap perilaku dan kedisiplinan tahanan selama menjalani masa tahanan',
                'urutan' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'nama' => 'Kesehatan dan Rehabilitasi',
                'deskripsi' => 'Penilaian terhadap kondisi kesehatan dan partisipasi dalam program rehabilitasi',
                'urutan' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'nama' => 'Pendidikan dan Keterampilan',
                'deskripsi' => 'Penilaian terhadap partisipasi dalam program pendidikan dan pelatihan keterampilan',
                'urutan' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'nama' => 'Kondisi Hukum',
                'deskripsi' => 'Penilaian terhadap kondisi hukum dan administrasi tahanan',
                'urutan' => 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $this->db->table('anp_clusters')->insertBatch($clusters);
        
        // 2. Buat Kriteria (Cluster)
        $kriteria = [
            // Cluster 1: Perilaku dan Disiplin
            [
                'id' => 1,
                'kode' => 'C1',
                'nama' => 'Perilaku dan Disiplin',
                'jenis' => 'Benefit',
                'bobot' => 0.25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Cluster 2: Kesehatan dan Rehabilitasi
            [
                'id' => 2,
                'kode' => 'C2',
                'nama' => 'Kesehatan dan Rehabilitasi',
                'jenis' => 'Benefit',
                'bobot' => 0.20,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Cluster 3: Pendidikan dan Keterampilan
            [
                'id' => 3,
                'kode' => 'C3',
                'nama' => 'Pendidikan dan Keterampilan',
                'jenis' => 'Benefit',
                'bobot' => 0.30,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Cluster 4: Kondisi Hukum
            [
                'id' => 4,
                'kode' => 'C4',
                'nama' => 'Kondisi Hukum',
                'jenis' => 'Cost',
                'bobot' => 0.25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $this->db->table('kriteria')->insertBatch($kriteria);
        
        // 3. Buat Subkriteria (Node) - 3 per Cluster
        $subkriteria = [
            // Cluster 1: Perilaku dan Disiplin (3 node)
            [
                'id' => 1,
                'kriteria_id' => 1,
                'kode' => 'C1N1',
                'nama' => 'Kedisiplinan',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'kriteria_id' => 1,
                'kode' => 'C1N2',
                'nama' => 'Sikap Sosial',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'kriteria_id' => 1,
                'kode' => 'C1N3',
                'nama' => 'Kerjasama',
                'bobot' => 0.34,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Cluster 2: Kesehatan dan Rehabilitasi (3 node)
            [
                'id' => 4,
                'kriteria_id' => 2,
                'kode' => 'C2N1',
                'nama' => 'Kondisi Fisik',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 5,
                'kriteria_id' => 2,
                'kode' => 'C2N2',
                'nama' => 'Kondisi Mental',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 6,
                'kriteria_id' => 2,
                'kode' => 'C2N3',
                'nama' => 'Partisipasi Rehabilitasi',
                'bobot' => 0.34,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Cluster 3: Pendidikan dan Keterampilan (3 node)
            [
                'id' => 7,
                'kriteria_id' => 3,
                'kode' => 'C3N1',
                'nama' => 'Pendidikan Formal',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 8,
                'kriteria_id' => 3,
                'kode' => 'C3N2',
                'nama' => 'Pelatihan Keterampilan',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 9,
                'kriteria_id' => 3,
                'kode' => 'C3N3',
                'nama' => 'Prestasi Belajar',
                'bobot' => 0.34,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Cluster 4: Kondisi Hukum (3 node)
            [
                'id' => 10,
                'kriteria_id' => 4,
                'kode' => 'C4N1',
                'nama' => 'Status Hukum',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 11,
                'kriteria_id' => 4,
                'kode' => 'C4N2',
                'nama' => 'Administrasi',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 12,
                'kriteria_id' => 4,
                'kode' => 'C4N3',
                'nama' => 'Kesempatan Remisi Sebelumnya',
                'bobot' => 0.34,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $this->db->table('subkriteria')->insertBatch($subkriteria);
        
        // 4. Buat Periode Aktif untuk Testing
        $periode = [
            'id' => 1,
            'nama_periode' => 'Periode Test ANP',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'status' => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->table('periode_penilaian')->insert($periode);
        
        echo "Seed data ANP berhasil dibuat:\n";
        echo "- 4 Cluster\n";
        echo "- 4 Kriteria\n";
        echo "- 12 Subkriteria (3 per cluster)\n";
        echo "- 1 Periode Aktif\n";
    }
}