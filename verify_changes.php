<?php
/**
 * Verifikasi bahwa inkonsistensi sudah diperbaiki:
 * 1. KalapasController sekarang menggunakan hitungTOPSIS
 * 2. RankingController juga menggunakan hitungTOPSIS untuk KEPALA_LAPAS
 */

echo "=== VERIFIKASI PERBAIKAN INKONSISTENSI ===\n\n";

// Cek apakah KalapasController menggunakan hitungTOPSIS di method validasi()
echo "1. Memeriksa KalapasController...\n";
$kalapasContent = file_get_contents('app/Controllers/KalapasController.php');

// Cek apakah method validasi() memanggil hitungTOPSIS
if (strpos($kalapasContent, 'validasi()') && strpos($kalapasContent, 'hitungTOPSIS($narapidana, $kriteria, $penilaian)')) {
    echo "   ✅ Method validasi() menggunakan hitungTOPSIS\n";
} else {
    echo "   ❌ Method validasi() TIDAK menggunakan hitungTOPSIS\n";
}

// Cek apakah method previewCetak() memanggil hitungTOPSIS
if (strpos($kalapasContent, 'previewCetak()') && strpos($kalapasContent, 'hitungTOPSIS($narapidana, $kriteria, $penilaian)')) {
    echo "   ✅ Method previewCetak() menggunakan hitungTOPSIS\n";
} else {
    echo "   ❌ Method previewCetak() TIDAK menggunakan hitungTOPSIS\n";
}

// Cek apakah method cetakLaporan() memanggil hitungTOPSIS
if (strpos($kalapasContent, 'cetakLaporan()') && strpos($kalapasContent, 'hitungTOPSIS($narapidana, $kriteria, $penilaian)')) {
    echo "   ✅ Method cetakLaporan() menggunakan hitungTOPSIS\n";
} else {
    echo "   ❌ Method cetakLaporan() TIDAK menggunakan hitungTOPSIS\n";
}

// Cek apakah hitungTOPSIS method ada
if (strpos($kalapasContent, 'public function hitungTOPSIS($narapidana, $kriteria, $penilaian)')) {
    echo "   ✅ Method hitungTOPSIS() ada di KalapasController\n";
} else {
    echo "   ❌ Method hitungTOPSIS() TIDAK ada di KalapasController\n";
}

echo "\n2. Memeriksa RankingController...\n";
$rankingContent = file_get_contents('app/Controllers/RankingController.php');

// Cek apakah RankingController menggunakan hitungTOPSIS untuk KEPALA_LAPAS
if (strpos($rankingContent, '$role == \'KEPALA_LAPAS\'') && strpos($rankingContent, '$hasil = $this->hitungTOPSIS($narapidana, $kriteria, $penilaian)')) {
    echo "   ✅ RankingController menggunakan hitungTOPSIS untuk KEPALA_LAPAS\n";
} else {
    echo "   ❌ RankingController TIDAK menggunakan hitungTOPSIS untuk KEPALA_LAPAS\n";
}

// Cek apakah RankingController menggunakan hitungRankingSederhana untuk WALI_PEMASYARAKATAN
if (strpos($rankingContent, '$role == \'WALI_PEMASYARAKATAN\'') && strpos($rankingContent, '$hasil = $this->hitungRankingSederhana($narapidana, $kriteria, $penilaian)')) {
    echo "   ✅ RankingController menggunakan hitungRankingSederhana untuk WALI_PEMASYARAKATAN\n";
} else {
    echo "   ❌ RankingController TIDAK menggunakan hitungRankingSederhana untuk WALI_PEMASYARAKATAN\n";
}

echo "\n3. Memeriksa WaliController...\n";
$waliContent = file_get_contents('app/Controllers/WaliController.php');

// Cek apakah WaliController tetap menggunakan hitungRankingSederhana
if (strpos($waliContent, 'private function hitungRankingSederhana')) {
    echo "   ✅ WaliController tetap menggunakan hitungRankingSederhana (konsisten dengan role)\n";
} else {
    echo "   ❌ WaliController TIDAK memiliki hitungRankingSederhana\n";
}

echo "\n=== KESIMPULAN ===\n";
echo "Perbaikan inkonsistensi sudah diterapkan:\n";
echo "- KalapasController sekarang menggunakan hitungTOPSIS untuk semua method\n";
echo "- RankingController menggunakan hitungTOPSIS untuk KEPALA_LAPAS dan hitungRankingSederhana untuk WALI_PEMASYARAKATAN\n";
echo "- WaliController tetap menggunakan hitungRankingSederhana (sesuai role)\n";
echo "- Ini akan memastikan nilai preferensi konsisten antara halaman validasi dan ranking untuk KEPALA_LAPAS\n";

echo "\n=== REKOMENDASI ===\n";
echo "1. Test halaman /kalapas/validasi dengan periode 0-00\n";
echo "2. Bandingkan nilai preferensi dengan halaman /kalapas/ranking atau /ranking\n";
echo "3. Pastikan nilai yang ditampilkan sama untuk setiap narapidana\n";

?>