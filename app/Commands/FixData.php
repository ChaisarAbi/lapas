<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class FixData extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'fix:data';
    protected $description = 'Fix incomplete data and recalculate TOPSIS';

    public function run(array $params)
    {
        $db = Database::connect();

        CLI::write('=== MEMPERBAIKI DATA PENILAIAN ===', 'green');
        CLI::newLine();

        // 1. Tambahkan data kriteria 3 dan 4 untuk narapidana 11
        $dataToAdd = [
            [
                'narapidana_id' => 11,
                'kriteria_id' => 3,
                'nilai' => 89.00,
                'periode' => '2026-02',
                'penilai_id' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'narapidana_id' => 11,
                'kriteria_id' => 4,
                'nilai' => 87.00,
                'periode' => '2026-02',
                'penilai_id' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($dataToAdd as $data) {
            // Cek apakah data sudah ada
            $check = $db->table('penilaian')
                ->where('narapidana_id', $data['narapidana_id'])
                ->where('kriteria_id', $data['kriteria_id'])
                ->where('periode', $data['periode'])
                ->countAllResults();
            
            if ($check == 0) {
                $db->table('penilaian')->insert($data);
                CLI::write("✓ Data ditambahkan: Narapidana {$data['narapidana_id']}, Kriteria {$data['kriteria_id']}, Nilai {$data['nilai']}", 'green');
            } else {
                CLI::write("✓ Data sudah ada: Narapidana {$data['narapidana_id']}, Kriteria {$data['kriteria_id']}", 'yellow');
            }
        }

        // 2. Hapus ranking lama untuk periode 2026-02
        // Cari ID periode
        $periodeData = $db->table('periode_penilaian')
            ->where('tahun', 2026)
            ->where('bulan', 2)
            ->get()
            ->getRowArray();

        if ($periodeData) {
            $periodeId = $periodeData['id'];
            $db->table('ranking')->where('periode_id', $periodeId)->delete();
            CLI::newLine();
            CLI::write("✓ Data ranking lama dihapus untuk periode 2026-02 (ID: $periodeId)", 'green');
        } else {
            CLI::newLine();
            CLI::write("⚠ Periode 2026-02 tidak ditemukan di tabel periode_penilaian", 'yellow');
        }

        // 3. Hapus data topsis lama untuk periode ini
        $db->table('topsis_hasil')->where('periode', '2026-02')->delete();
        $db->table('topsis_detail')->where('periode', '2026-02')->delete();
        CLI::write("✓ Data TOPSIS lama dihapus untuk periode 2026-02", 'green');

        // 4. Verifikasi data penilaian
        CLI::newLine();
        CLI::write('=== VERIFIKASI DATA ===', 'blue');
        
        $penilaianCount = $db->table('penilaian')
            ->where('periode', '2026-02')
            ->countAllResults();

        $narapidanaCount = $db->table('penilaian')
            ->select('narapidana_id')
            ->where('periode', '2026-02')
            ->groupBy('narapidana_id')
            ->countAllResults();

        $kriteriaPerNarapidana = $db->table('penilaian')
            ->select('narapidana_id, COUNT(*) as total_kriteria')
            ->where('periode', '2026-02')
            ->groupBy('narapidana_id')
            ->get()
            ->getResultArray();

        CLI::write("Total data penilaian periode 2026-02: $penilaianCount");
        CLI::write("Total narapidana: $narapidanaCount");
        CLI::newLine();

        foreach ($kriteriaPerNarapidana as $item) {
            $narapidanaId = $item['narapidana_id'];
            $totalKriteria = $item['total_kriteria'];
            
            $narapidana = $db->table('narapidana')
                ->where('id', $narapidanaId)
                ->get()
                ->getRowArray();
            
            $nama = $narapidana ? $narapidana['nama_lengkap'] : "ID $narapidanaId";
            
            if ($totalKriteria == 4) {
                CLI::write("✓ $nama: $totalKriteria/4 kriteria (LENGKAP)", 'green');
            } else {
                CLI::write("⚠ $nama: $totalKriteria/4 kriteria (TIDAK LENGKAP)", 'yellow');
            }
        }

        CLI::newLine();
        CLI::write('=== INSTRUKSI SELANJUTNYA ===', 'blue');
        CLI::write('1. Buka browser dan akses: http://localhost:8080/bimkesmaswat/penilaian/detailTopsis/11/2026-02');
        CLI::write('2. Sistem akan menghitung TOPSIS otomatis karena data sudah lengkap');
        CLI::write('3. Periksa hasil perhitungan dan status remisi');
        CLI::newLine();
    }
}