<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnpPairwiseSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama jika ada
        $this->db->table('anp_pairwise_histori')->truncate();
        
        // Ambil periode aktif
        $periode = $this->db->table('periode_penilaian')
            ->where('status', 'aktif')
            ->get()
            ->getRowArray();
        
        if (!$periode) {
            echo "Error: Tidak ada periode aktif. Jalankan AnpTestSeeder terlebih dahulu.\n";
            return;
        }
        
        $periodeId = $periode['id'];
        
        // Data Subkriteria
        $subkriteria = $this->db->table('subkriteria')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
        
        if (count($subkriteria) < 12) {
            echo "Error: Data subkriteria tidak lengkap. Jalankan AnpTestSeeder terlebih dahulu.\n";
            return;
        }
        
        // Buat mapping kode ke ID
        $kodeToId = [];
        foreach ($subkriteria as $sk) {
            $kodeToId[$sk['kode']] = $sk['id'];
        }
        
        // Data Pairwise Comparison untuk 12 subkriteria
        // Format: [node_dari_kode, node_ke_kode, skala]
        // Skala 1-9: 1=sama penting, 3=sedikit lebih penting, 5=lebih penting, 7=sangat lebih penting, 9=mutlak lebih penting
        
        $pairwiseData = [
            // Cluster 1: Perilaku dan Disiplin (C1N1, C1N2, C1N3)
            ['C1N1', 'C1N2', 3], // Kedisiplinan sedikit lebih penting dari Sikap Sosial
            ['C1N1', 'C1N3', 5], // Kedisiplinan lebih penting dari Kerjasama
            ['C1N2', 'C1N3', 3], // Sikap Sosial sedikit lebih penting dari Kerjasama
            
            // Cluster 2: Kesehatan dan Rehabilitasi (C2N1, C2N2, C2N3)
            ['C2N1', 'C2N2', 5], // Kondisi Fisik lebih penting dari Kondisi Mental
            ['C2N1', 'C2N3', 3], // Kondisi Fisik sedikit lebih penting dari Partisipasi Rehabilitasi
            ['C2N2', 'C2N3', 7], // Kondisi Mental sangat lebih penting dari Partisipasi Rehabilitasi
            
            // Cluster 3: Pendidikan dan Keterampilan (C3N1, C3N2, C3N3)
            ['C3N1', 'C3N2', 3], // Pendidikan Formal sedikit lebih penting dari Pelatihan Keterampilan
            ['C3N1', 'C3N3', 5], // Pendidikan Formal lebih penting dari Prestasi Belajar
            ['C3N2', 'C3N3', 3], // Pelatihan Keterampilan sedikit lebih penting dari Prestasi Belajar
            
            // Cluster 4: Kondisi Hukum (C4N1, C4N2, C4N3)
            ['C4N1', 'C4N2', 7], // Status Hukum sangat lebih penting dari Administrasi
            ['C4N1', 'C4N3', 9], // Status Hukum mutlak lebih penting dari Kesempatan Remisi Sebelumnya
            ['C4N2', 'C4N3', 5], // Administrasi lebih penting dari Kesempatan Remisi Sebelumnya
            
            // Interdependensi antar Cluster (Cross-cluster comparisons)
            // Perilaku (C1) vs Kesehatan (C2)
            ['C1N1', 'C2N1', 5], // Kedisiplinan lebih penting dari Kondisi Fisik
            ['C1N1', 'C2N2', 7], // Kedisiplinan sangat lebih penting dari Kondisi Mental
            ['C1N2', 'C2N1', 3], // Sikap Sosial sedikit lebih penting dari Kondisi Fisik
            ['C1N3', 'C2N3', 5], // Kerjasama lebih penting dari Partisipasi Rehabilitasi
            
            // Perilaku (C1) vs Pendidikan (C3)
            ['C1N1', 'C3N1', 7], // Kedisiplinan sangat lebih penting dari Pendidikan Formal
            ['C1N2', 'C3N2', 5], // Sikap Sosial lebih penting dari Pelatihan Keterampilan
            ['C1N3', 'C3N3', 3], // Kerjasama sedikit lebih penting dari Prestasi Belajar
            
            // Perilaku (C1) vs Kondisi Hukum (C4)
            ['C1N1', 'C4N1', 3], // Kedisiplinan sedikit lebih penting dari Status Hukum
            ['C1N2', 'C4N2', 5], // Sikap Sosial lebih penting dari Administrasi
            ['C1N3', 'C4N3', 7], // Kerjasama sangat lebih penting dari Kesempatan Remisi Sebelumnya
            
            // Kesehatan (C2) vs Pendidikan (C3)
            ['C2N1', 'C3N1', 5], // Kondisi Fisik lebih penting dari Pendidikan Formal
            ['C2N2', 'C3N2', 7], // Kondisi Mental sangat lebih penting dari Pelatihan Keterampilan
            ['C2N3', 'C3N3', 3], // Partisipasi Rehabilitasi sedikit lebih penting dari Prestasi Belajar
            
            // Kesehatan (C2) vs Kondisi Hukum (C4)
            ['C2N1', 'C4N1', 3], // Kondisi Fisik sedikit lebih penting dari Status Hukum
            ['C2N2', 'C4N2', 5], // Kondisi Mental lebih penting dari Administrasi
            ['C2N3', 'C4N3', 7], // Partisipasi Rehabilitasi sangat lebih penting dari Kesempatan Remisi Sebelumnya
            
            // Pendidikan (C3) vs Kondisi Hukum (C4)
            ['C3N1', 'C4N1', 5], // Pendidikan Formal lebih penting dari Status Hukum
            ['C3N2', 'C4N2', 3], // Pelatihan Keterampilan sedikit lebih penting dari Administrasi
            ['C3N3', 'C4N3', 7], // Prestasi Belajar sangat lebih penting dari Kesempatan Remisi Sebelumnya
        ];
        
        // Simpan pairwise data
        $batchData = [];
        foreach ($pairwiseData as $data) {
            $nodeDariKode = $data[0];
            $nodeKeKode = $data[1];
            $skala = $data[2];
            
            if (!isset($kodeToId[$nodeDariKode]) || !isset($kodeToId[$nodeKeKode])) {
                continue;
            }
            
            $nodeDariId = $kodeToId[$nodeDariKode];
            $nodeKeId = $kodeToId[$nodeKeKode];
            
            // Cari data subkriteria untuk mendapatkan nama
            $subkriteriaDari = array_filter($subkriteria, function($sk) use ($nodeDariId) {
                return $sk['id'] == $nodeDariId;
            });
            $subkriteriaKe = array_filter($subkriteria, function($sk) use ($nodeKeId) {
                return $sk['id'] == $nodeKeId;
            });
            
            $subkriteriaDari = reset($subkriteriaDari);
            $subkriteriaKe = reset($subkriteriaKe);
            
            $batchData[] = [
                'periode_id' => $periodeId,
                'node_dari_id' => $nodeDariId,
                'node_dari_kode' => $nodeDariKode,
                'node_dari_nama' => $subkriteriaDari['nama'],
                'node_ke_id' => $nodeKeId,
                'node_ke_kode' => $nodeKeKode,
                'node_ke_nama' => $subkriteriaKe['nama'],
                'skala' => $skala,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($batchData)) {
            $this->db->table('anp_pairwise_histori')->insertBatch($batchData);
            echo "Seed data pairwise comparison berhasil dibuat: " . count($batchData) . " data\n";
        } else {
            echo "Error: Tidak ada data pairwise yang bisa disimpan\n";
        }
    }
}