<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnpTargetSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Ambil periode aktif
        $periode = $db->table('periode_penilaian')
            ->where('status', 'aktif')
            ->get()
            ->getRowArray();
        
        if (!$periode) {
            echo "Tidak ada periode aktif. Buat periode terlebih dahulu.\n";
            return;
        }
        
        $periodeId = $periode['id'];
        
        // Ambil semua subkriteria
        $subkriteria = $db->table('subkriteria')
            ->select('subkriteria.*, kriteria.nama as kriteria_nama')
            ->join('kriteria', 'kriteria.id = subkriteria.kriteria_id')
            ->orderBy('kriteria.id', 'ASC')
            ->orderBy('subkriteria.kode', 'ASC')
            ->get()
            ->getResultArray();
        
        if (empty($subkriteria)) {
            echo "Tidak ada subkriteria. Buat subkriteria terlebih dahulu.\n";
            return;
        }
        
        echo "Jumlah subkriteria: " . count($subkriteria) . "\n";
        
        // Hapus data edges dan pairwise histori untuk periode ini
        $db->table('anp_edges')->where('periode_id', $periodeId)->delete();
        $db->table('anp_pairwise_histori')->where('periode_id', $periodeId)->delete();
        
        // Buat edges sederhana: setiap subkriteria mempengaruhi subkriteria berikutnya
        $edges = [];
        $pairwiseData = [];
        
        for ($i = 0; $i < count($subkriteria); $i++) {
            for ($j = 0; $j < count($subkriteria); $j++) {
                if ($i != $j) {
                    // Buat edge dari i ke j (i mempengaruhi j)
                    $edges[] = [
                        'periode_id' => $periodeId,
                        'from_node_id' => $subkriteria[$i]['id'],
                        'to_node_id' => $subkriteria[$j]['id'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // Buat pairwise untuk target j dengan influencer i dan k (i != k)
                    for ($k = 0; $k < count($subkriteria); $k++) {
                        if ($i != $k && $j != $k) {
                            // Buat pairwise dengan nilai random antara 1-9
                            $skala = rand(1, 9);
                            $pairwiseData[] = [
                                'periode_id' => $periodeId,
                                'target_node_id' => $subkriteria[$j]['id'],
                                'target_node_kode' => $subkriteria[$j]['kode'],
                                'target_node_nama' => $subkriteria[$j]['nama'],
                                'node_dari_id' => $subkriteria[$i]['id'],
                                'node_dari_kode' => $subkriteria[$i]['kode'],
                                'node_dari_nama' => $subkriteria[$i]['nama'],
                                'node_ke_id' => $subkriteria[$k]['id'],
                                'node_ke_kode' => $subkriteria[$k]['kode'],
                                'node_ke_nama' => $subkriteria[$k]['nama'],
                                'skala' => $skala,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                }
            }
        }
        
        // Insert edges
        if (!empty($edges)) {
            $db->table('anp_edges')->insertBatch($edges);
            echo "Berhasil menambahkan " . count($edges) . " edges.\n";
        }
        
        // Insert pairwise data
        if (!empty($pairwiseData)) {
            $db->table('anp_pairwise_histori')->insertBatch($pairwiseData);
            echo "Berhasil menambahkan " . count($pairwiseData) . " pairwise data.\n";
        }
        
        echo "Seeder ANP Target berhasil dijalankan.\n";
    }
}