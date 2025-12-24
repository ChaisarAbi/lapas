<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateExistingPeriods extends Migration
{
    public function up()
    {
        // Ambil distinct periode dari tabel penilaian
        $periods = $this->db->table('penilaian')
                           ->select('periode')
                           ->distinct()
                           ->orderBy('periode', 'DESC')
                           ->get()
                           ->getResultArray();
        
        if (empty($periods)) {
            echo "Tidak ada data periode untuk dimigrasi.\n";
            return;
        }
        
        echo "Menemukan " . count($periods) . " periode untuk dimigrasi:\n";
        
        foreach ($periods as $period) {
            $periodeStr = $period['periode'];
            
            // Parse periode format YYYY-MM
            if (preg_match('/^(\d{4})-(\d{2})$/', $periodeStr, $matches)) {
                $tahun = (int)$matches[1];
                $bulan = (int)$matches[2];
                
                // Cek apakah periode sudah ada di tabel periode_penilaian
                $existing = $this->db->table('periode_penilaian')
                                    ->where('tahun', $tahun)
                                    ->where('bulan', $bulan)
                                    ->countAllResults();
                
                if ($existing == 0) {
                    // Buat nama periode
                    $namaPeriode = "Periode " . date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun));
                    
                    // Tentukan tanggal mulai dan selesai (default: awal bulan - akhir bulan)
                    $tanggalMulai = date('Y-m-d', mktime(0, 0, 0, $bulan, 1, $tahun));
                    $tanggalSelesai = date('Y-m-t', mktime(0, 0, 0, $bulan, 1, $tahun));
                    
                    // Status: selesai (karena sudah ada data penilaian)
                    $data = [
                        'nama_periode' => $namaPeriode,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai,
                        'status' => 'selesai',
                        'keterangan' => 'Diimpor dari data penilaian existing',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    
                    $this->db->table('periode_penilaian')->insert($data);
                    echo "  - Migrasi periode: {$periodeStr} -> {$namaPeriode}\n";
                } else {
                    echo "  - Periode {$periodeStr} sudah ada, dilewati\n";
                }
            } else {
                echo "  - Format periode tidak valid: {$periodeStr}\n";
            }
        }
        
        echo "Migrasi selesai.\n";
    }

    public function down()
    {
        // Hapus periode yang diimpor (berdasarkan keterangan)
        $this->db->table('periode_penilaian')
                ->where('keterangan', 'Diimpor dari data penilaian existing')
                ->delete();
        
        echo "Data periode yang diimpor telah dihapus.\n";
    }
}
