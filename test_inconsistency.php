<?php
// Test untuk memverifikasi konsistensi antara RankingController dan KalapasController

// Setup basic environment
require_once 'app/Controllers/BaseController.php';
require_once 'app/Controllers/RankingController.php';
require_once 'app/Controllers/KalapasController.php';
require_once 'app/Models/NarapidanaModel.php';
require_once 'app/Models/PenilaianModel.php';

// Mock session untuk testing
session_start();
$_SESSION['role'] = 'KEPALA_LAPAS';

// Create test data
$narapidana = [
    ['id' => 1, 'nama_lengkap' => 'Narapidana 4', 'no_register' => 'NAPI-0004', 'kasus' => 'Penganiayaan'],
    ['id' => 2, 'nama_lengkap' => 'Narapidana 9', 'no_register' => 'NAPI-0009', 'kasus' => 'Penipuan']
];

$kriteria = [
    ['id' => 1, 'nama' => 'Kemandirian', 'bobot' => 0.2, 'jenis' => 'Benefit'],
    ['id' => 2, 'nama' => 'Kepribadian', 'bobot' => 0.3, 'jenis' => 'Benefit'],
    ['id' => 3, 'nama' => 'Mental', 'bobot' => 0.25, 'jenis' => 'Benefit'],
    ['id' => 4, 'nama' => 'Sikap', 'bobot' => 0.25, 'jenis' => 'Benefit']
];

$penilaian = [
    // Narapidana 4
    ['narapidana_id' => 1, 'subkriteria_id' => 1, 'nilai' => 78.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 2, 'nilai' => 78.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 3, 'nilai' => 98.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 4, 'nilai' => 89.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 5, 'nilai' => 88.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 6, 'nilai' => 78.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 7, 'nilai' => 78.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 8, 'nilai' => 76.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 9, 'nilai' => 89.0],
    ['narapidana_id' => 1, 'subkriteria_id' => 10, 'nilai' => 78.0],
    
    // Narapidana 9
    ['narapidana_id' => 2, 'subkriteria_id' => 1, 'nilai' => 50.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 2, 'nilai' => 50.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 3, 'nilai' => 60.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 4, 'nilai' => 55.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 5, 'nilai' => 60.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 6, 'nilai' => 50.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 7, 'nilai' => 50.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 8, 'nilai' => 45.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 9, 'nilai' => 55.0],
    ['narapidana_id' => 2, 'subkriteria_id' => 10, 'nilai' => 50.0]
];

echo "=== TEST KONSISTENSI PERHITUNGAN ===\n\n";

// Test RankingController
echo "1. HASIL DARI RankingController (hitungTOPSIS):\n";
$rankingController = new App\Controllers\RankingController();
$rankingResult = $rankingController->hitungTOPSIS($narapidana, $kriteria, $penilaian);

foreach ($rankingResult as $index => $result) {
    $name = $result['narapidana']['nama_lengkap'];
    $preferensi = number_format($result['preferensi'], 4);
    $dPlus = number_format($result['d_positif'], 4);
    $dMinus = number_format($result['d_negatif'], 4);
    
    echo "   " . ($index + 1) . ". $name\n";
    echo "       Preferensi: $preferensi\n";
    echo "       D+: $dPlus, D-: $dMinus\n\n";
}

// Test KalapasController  
echo "\n2. HASIL DARI KalapasController (hitungTOPSIS):\n";
$kalapasController = new App\Controllers\KalapasController();
$kalapasResult = $kalapasController->hitungTOPSIS($narapidana, $kriteria, $penilaian);

foreach ($kalapasResult as $index => $result) {
    $name = $result['narapidana']['nama_lengkap'];
    $preferensi = number_format($result['preferensi'], 4);
    $dPlus = number_format($result['d_positif'], 4);
    $dMinus = number_format($result['d_negatif'], 4);
    
    echo "   " . ($index + 1) . ". $name\n";
    echo "       Preferensi: $preferensi\n";
    echo "       D+: $dPlus, D-: $dMinus\n\n";
}

// Compare results
echo "\n3. PERBANDINGAN HASIL:\n";
$inconsistent = false;

if (count($rankingResult) !== count($kalapasResult)) {
    echo "   ERROR: Jumlah hasil berbeda!\n";
    $inconsistent = true;
} else {
    for ($i = 0; $i < count($rankingResult); $i++) {
        $rankName = $rankingResult[$i]['narapidana']['nama_lengkap'];
        $kalaName = $kalapasResult[$i]['narapidana']['nama_lengkap'];
        
        $rankPref = $rankingResult[$i]['preferensi'];
        $kalaPref = $kalapasResult[$i]['preferensi'];
        
        $rankDPlus = $rankingResult[$i]['d_positif'];
        $kalaDPlus = $kalapasResult[$i]['d_positif'];
        
        $rankDMinus = $rankingResult[$i]['d_negatif'];
        $kalaDMinus = $kalapasResult[$i]['d_negatif'];
        
        if ($rankName !== $kalaName) {
            echo "   ERROR: Urutan ranking berbeda pada posisi $i!\n";
            $inconsistent = true;
        }
        
        $prefDiff = abs($rankPref - $kalaPref);
        if ($prefDiff > 0.0001) {
            echo "   PERINGATAN: Perbedaan preferensi untuk $rankName: " . number_format($prefDiff, 6) . "\n";
            echo "     RankingController: " . number_format($rankPref, 4) . "\n";
            echo "     KalapasController: " . number_format($kalaPref, 4) . "\n";
        }
    }
}

if (!$inconsistent) {
    echo "   ✅ SEMUA HASIL KONSISTEN! RankingController dan KalapasController memberikan hasil yang sama.\n";
}

echo "\n=== SELESAI ===\n";

?>