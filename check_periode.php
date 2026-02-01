<?php
require_once 'app/Config/Database.php';

$config = new \Config\Database();
$db = \Config\Database::connect();

// Hapus data dengan tahun atau bulan 0
$db->query("DELETE FROM periode_penilaian WHERE tahun = 0 OR bulan = 0");

// Cek apakah ada data
$query = $db->query("SELECT COUNT(*) as total FROM periode_penilaian");
$result = $query->getRow();
echo "Total data periode setelah cleanup: " . $result->total . "\n";

// Buat data contoh jika kosong
if ($result->total == 0) {
    $data = [
        'nama_periode' => 'Periode Evaluasi Januari 2026',
        'tahun' => 2026,
        'bulan' => 1,
        'tanggal_mulai' => '2026-01-01',
        'tanggal_selesai' => '2026-01-31',
        'status' => 'aktif',
        'keterangan' => 'Periode evaluasi pertama'
    ];
    
    $db->table('periode_penilaian')->insert($data);
    echo "Data contoh berhasil ditambahkan.\n";
}

// Tampilkan data
$query = $db->query("SELECT * FROM periode_penilaian");
$results = $query->getResultArray();

echo "\nData periode saat ini:\n";
foreach ($results as $row) {
    echo "ID: {$row['id']}, Nama: {$row['nama_periode']}, Tahun: {$row['tahun']}, Bulan: {$row['bulan']}, Status: {$row['status']}\n";
}

$db->close();