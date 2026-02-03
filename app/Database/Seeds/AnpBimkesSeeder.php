<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnpBimkesSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama jika ada (dalam urutan yang benar untuk menghindari foreign key constraint)
        $this->db->table('anp_pairwise_histori')->where('id >', 0)->delete();
        $this->db->table('anp_edges')->where('id >', 0)->delete();
        $this->db->table('anp_interdependensi')->where('id >', 0)->delete();
        $this->db->table('penilaian')->where('id >', 0)->delete();
        $this->db->table('subkriteria')->where('id >', 0)->delete();
        $this->db->table('kriteria')->where('id >', 0)->delete();
        $this->db->table('anp_clusters')->where('id >', 0)->delete();
        $this->db->table('periode_penilaian')->where('id >', 0)->delete();
        
        // 1. Buat Cluster (Kriteria Utama)
        $clusters = [
            [
                'id' => 1,
                'nama' => 'Kepribadian',
                'deskripsi' => 'Kriteria terkait kepribadian dan kepemimpinan narapidana',
                'urutan' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'nama' => 'Kemandirian',
                'deskripsi' => 'Kriteria terkait kemandirian dan keterampilan narapidana',
                'urutan' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'nama' => 'Sikap',
                'deskripsi' => 'Kriteria terkait sikap dan perilaku narapidana',
                'urutan' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'nama' => 'Mental',
                'deskripsi' => 'Kriteria terkait kesehatan mental narapidana',
                'urutan' => 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $this->db->table('anp_clusters')->insertBatch($clusters);
        
        // 2. Buat Kriteria (Cluster)
        $kriteria = [
            // Cluster 1: Kepribadian
            [
                'id' => 1,
                'kode' => 'KP',
                'nama' => 'Kepribadian',
                'jenis' => 'Benefit',
                'bobot' => 0.25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Cluster 2: Kemandirian
            [
                'id' => 2,
                'kode' => 'KM',
                'nama' => 'Kemandirian',
                'jenis' => 'Benefit',
                'bobot' => 0.25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Cluster 3: Sikap
            [
                'id' => 3,
                'kode' => 'S',
                'nama' => 'Sikap',
                'jenis' => 'Benefit',
                'bobot' => 0.25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Cluster 4: Mental
            [
                'id' => 4,
                'kode' => 'M',
                'nama' => 'Mental',
                'jenis' => 'Cost',
                'bobot' => 0.25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $this->db->table('kriteria')->insertBatch($kriteria);
        
        // 3. Buat Subkriteria (Node)
        $subkriteria = [
            // Cluster KP: Kepribadian
            [
                'id' => 1,
                'kriteria_id' => 1,
                'kode' => 'KP1',
                'nama' => 'Kesadaran Beragama',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'kriteria_id' => 1,
                'kode' => 'KP2',
                'nama' => 'Kesadaran Hukum, Berbangsa, dan Bernegara',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'kriteria_id' => 1,
                'kode' => 'KP3',
                'nama' => 'Konseling & Rehabilitasi',
                'bobot' => 0.34,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Cluster KM: Kemandirian
            [
                'id' => 4,
                'kriteria_id' => 2,
                'kode' => 'KM1',
                'nama' => 'Pelatihan Keterampilan',
                'bobot' => 0.5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 5,
                'kriteria_id' => 2,
                'kode' => 'KM2',
                'nama' => 'Produksi Barang/Jasa',
                'bobot' => 0.5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Cluster S: Sikap
            [
                'id' => 6,
                'kriteria_id' => 3,
                'kode' => 'S1',
                'nama' => 'Keberfungsian & Rutinitas',
                'bobot' => 0.5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 7,
                'kriteria_id' => 3,
                'kode' => 'S2',
                'nama' => 'Pelanggaran Hukum',
                'bobot' => 0.5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Cluster M: Mental
            [
                'id' => 8,
                'kriteria_id' => 4,
                'kode' => 'M1',
                'nama' => 'Depresi',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 9,
                'kriteria_id' => 4,
                'kode' => 'M2',
                'nama' => 'Kecemasan',
                'bobot' => 0.33,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 10,
                'kriteria_id' => 4,
                'kode' => 'M3',
                'nama' => 'Potensi Bunuh Diri',
                'bobot' => 0.34,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $this->db->table('subkriteria')->insertBatch($subkriteria);
        
        // 4. Buat Periode Aktif
        $periode = [
            'id' => 1,
            'nama_periode' => 'Periode Bimkes ANP',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'status' => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->table('periode_penilaian')->insert($periode);
        
        // 5. Buat Edges (Relasi Subkriteria → Subkriteria)
        // Definisi edges: influencer → target (semua relasi yang tercantum)
        $allEdges = [
            // A. KP → KM
            ['KP1', 'KM1'],
            ['KP2', 'KM1'],
            ['KP3', 'KM1'],
            ['KP1', 'KM2'],
            ['KP2', 'KM2'],
            ['KP3', 'KM2'],
            ['KM1', 'KM2'],
            
            // B. KM → M
            ['KM1', 'M1'],
            ['KM2', 'M1'],
            ['KM1', 'M2'],
            ['KM2', 'M2'],
            ['KM2', 'M3'],
            
            // C. M → M (internal)
            ['M1', 'M2'],
            ['M1', 'M3'],
            ['M2', 'M3'],
            
            // D. M → S
            ['M1', 'S1'],
            ['M2', 'S1'],
            ['M3', 'S1'],
            ['M1', 'S2'],
            ['M2', 'S2'],
            ['M3', 'S2'],
            
            // E. S → S (internal)
            ['S1', 'S2'],
            
            // F. S → KP
            ['S1', 'KP1'],
            ['S2', 'KP1'],
            ['S1', 'KP2'],
            ['S2', 'KP2'],
            ['S1', 'KP3'],
            ['S2', 'KP3']
        ];
        
        // Buat array untuk menyimpan edges
        $edges = [];
        
        // Helper function untuk mendapatkan ID subkriteria berdasarkan kode
        $getSubkriteriaId = function($kode) use ($subkriteria) {
            foreach ($subkriteria as $sub) {
                if ($sub['kode'] == $kode) {
                    return $sub['id'];
                }
            }
            return null;
        };
        
        // Proses semua edges
        foreach ($allEdges as $edgePair) {
            $fromId = $getSubkriteriaId($edgePair[0]);
            $toId = $getSubkriteriaId($edgePair[1]);
            
            if ($fromId && $toId) {
                $edges[] = [
                    'periode_id' => 1,
                    'from_node_id' => $fromId,
                    'to_node_id' => $toId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }
        
        // Insert edges ke database
        if (!empty($edges)) {
            $this->db->table('anp_edges')->insertBatch($edges);
        }
        
        // 6. Buat Pairwise Comparison Data
        $pairwiseData = [];
        
        // Dapatkan semua edges yang telah dibuat
        $allEdges = $this->db->table('anp_edges')
            ->where('periode_id', 1)
            ->get()
            ->getResultArray();
        
        // Kelompokkan edges berdasarkan target_node_id
        $edgesByTarget = [];
        foreach ($allEdges as $edge) {
            $targetNodeId = $edge['to_node_id'];
            if (!isset($edgesByTarget[$targetNodeId])) {
                $edgesByTarget[$targetNodeId] = [];
            }
            $edgesByTarget[$targetNodeId][] = $edge['from_node_id'];
        }
        
        // Dapatkan detail subkriteria
        $subkriteriaDetail = $this->db->table('subkriteria')
            ->select('*')
            ->get()
            ->getResultArray();
        
        // Helper function untuk mendapatkan detail subkriteria
        $getSubkriteriaDetail = function($id) use ($subkriteriaDetail) {
            foreach ($subkriteriaDetail as $sub) {
                if ($sub['id'] == $id) {
                    return $sub;
                }
            }
            return null;
        };
        
        // Buat pairwise comparison untuk setiap target node
        foreach ($edgesByTarget as $targetNodeId => $influencerNodeIds) {
            $targetDetail = $getSubkriteriaDetail($targetNodeId);
            
            // Buat semua kombinasi pasangan influencer untuk target ini
            $influencerCount = count($influencerNodeIds);
            for ($i = 0; $i < $influencerCount; $i++) {
                for ($j = 0; $j < $influencerCount; $j++) {
                    if ($i != $j) {
                        $fromNodeId = $influencerNodeIds[$i];
                        $toNodeId = $influencerNodeIds[$j];
                        
                        $fromDetail = $getSubkriteriaDetail($fromNodeId);
                        $toDetail = $getSubkriteriaDetail($toNodeId);
                        
                        // Buat pairwise dengan nilai yang lebih realistis (termasuk reciprocal)
                        // Gunakan array nilai pairwise yang umum dalam AHP/ANP
                        $possibleValues = [1, 2, 3, 4, 5, 6, 7, 8, 9, 1/2, 1/3, 1/4, 1/5, 1/6, 1/7, 1/8, 1/9];
                        $skala = $possibleValues[array_rand($possibleValues)];
                        
                        $pairwiseData[] = [
                            'periode_id' => 1,
                            'target_node_id' => $targetNodeId,
                            'target_node_kode' => $targetDetail['kode'],
                            'target_node_nama' => $targetDetail['nama'],
                            'node_dari_id' => $fromNodeId,
                            'node_dari_kode' => $fromDetail['kode'],
                            'node_dari_nama' => $fromDetail['nama'],
                            'node_ke_id' => $toNodeId,
                            'node_ke_kode' => $toDetail['kode'],
                            'node_ke_nama' => $toDetail['nama'],
                            'skala' => $skala,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                }
            }
        }
        
        // Insert pairwise data ke database
        if (!empty($pairwiseData)) {
            $this->db->table('anp_pairwise_histori')->insertBatch($pairwiseData);
        }
        
        echo "Seed data ANP Bimkes berhasil dibuat:\n";
        echo "- 4 Cluster\n";
        echo "- 4 Kriteria\n";
        echo "- 10 Subkriteria\n";
        echo "- " . count($edges) . " Edges (Relasi)\n";
        echo "- " . count($pairwiseData) . " Pairwise Comparison Data\n";
        echo "- 1 Periode Aktif\n";
    }
}