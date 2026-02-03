<?php

require_once __DIR__ . '/vendor/autoload.php';

use CodeIgniter\Config\Services;

// Initialize CodeIgniter
$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== DEBUG PERIODE ===\n";

// Cari semua periode
$periodes = $db->table('periode_penilaian')
    ->orderBy('id', 'ASC')
    ->get()
    ->getResultArray();

echo "Total periode di database: " . count($periodes) . "\n\n";

foreach ($periodes as $periode) {
    echo "ID: " . $periode['id'] . "\n";
    echo "Nama: " . $periode['nama_periode'] . "\n";
    echo "Status: " . $periode['status'] . "\n";
    echo "Tahun: " . $periode['tahun'] . ", Bulan: " . $periode['bulan'] . "\n";
    echo "Tanggal Mulai: " . $periode['tanggal_mulai'] . "\n";
    echo "Tanggal Selesai: " . $periode['tanggal_selesai'] . "\n";
    echo "Created At: " . $periode['created_at'] . "\n";
    echo "Updated At: " . $periode['updated_at'] . "\n";
    echo "---\n";
}

// Cari periode aktif
$periodeAktif = $db->table('periode_penilaian')
    ->where('status', 'aktif')
    ->orderBy('id', 'ASC')
    ->get()
    ->getResultArray();

echo "\n=== PERIODE AKTIF ===\n";
if (empty($periodeAktif)) {
    echo "Tidak ada periode aktif.\n";
} else {
    echo "Jumlah periode aktif: " . count($periodeAktif) . "\n";
    foreach ($periodeAktif as $periode) {
        echo "ID: " . $periode['id'] . " - " . $periode['nama_periode'] . "\n";
    }
}

// Cek edges per periode
echo "\n=== EDGES PER PERIODE ===\n";
$edgesByPeriode = $db->table('anp_edges')
    ->select('periode_id, COUNT(*) as total')
    ->groupBy('periode_id')
    ->orderBy('periode_id', 'ASC')
    ->get()
    ->getResultArray();

foreach ($edgesByPeriode as $item) {
    echo "Periode ID: " . $item['periode_id'] . " - Total edges: " . $item['total'] . "\n";
}

// Cek jumlah subkriteria
$subkriteriaCount = $db->table('subkriteria')->countAllResults();
echo "\n=== SUBKRITERIA ===\n";
echo "Total subkriteria: " . $subkriteriaCount . "\n";

// Ambil semua subkriteria
$subkriteria = $db->table('subkriteria')
    ->select('id, kode, nama, kriteria_id')
    ->orderBy('id', 'ASC')
    ->get()
    ->getResultArray();

echo "\n=== LIST SUBKRITERIA ===\n";
foreach ($subkriteria as $sk) {
    echo "ID: " . $sk['id'] . " - Kode: " . $sk['kode'] . " - Nama: " . $sk['nama'] . " - Kriteria ID: " . $sk['kriteria_id'] . "\n";
}

echo "\n=== Selesai ===\n";