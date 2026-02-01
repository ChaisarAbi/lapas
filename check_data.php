<?php
require 'app/Config/Database.php';
$db = \Config\Database::connect();

// Cek jumlah data interdependensi
$result = $db->query('SELECT COUNT(*) as total FROM anp_interdependensi WHERE periode_id = 4')->getRow();
echo "Total data interdependensi periode 4: " . $result->total . "\n";

// Cek jumlah subkriteria
$result2 = $db->query('SELECT COUNT(*) as total FROM subkriteria')->getRow();
echo "Total subkriteria: " . $result2->total . "\n";

// Hitung seharusnya berapa data (n x n)
$n = $result2->total;
$expected = $n * $n;
echo "Seharusnya ada data: " . $expected . " (n x n = $n x $n)\n";

// Cek beberapa data contoh
echo "\nContoh data (10 pertama):\n";
$data = $db->query('SELECT * FROM anp_interdependensi WHERE periode_id = 4 LIMIT 10')->getResultArray();
foreach ($data as $row) {
    echo "ID: {$row['id']}, Dari: {$row['kriteria_id_dari']}->{$row['kriteria_id_ke']}, Nilai: {$row['nilai']}, Tipe: {$row['tipe']}\n";
}
?>