<?php
// Simple test script to directly test the calculation functionality
// This bypasses authentication for testing purposes only

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bootstrap CodeIgniter
require __DIR__ . '/vendor/autoload.php';

// Create CI instance
$app = \Config\Services::codeigniter();
$app->initialize();

// Load required models
$kriteriaModel = new \App\Models\KriteriaModel();
$subkriteriaModel = new \App\Models\SubkriteriaModel();
$anpModel = new \App\Models\AnpModel();
$periodeModel = new \App\Models\PeriodeModel();

// Get active period
$periodeAktif = $periodeModel->where('status', 'aktif')->first();
$periodeId = $periodeAktif['id'];

echo "=== Periode Aktif: " . $periodeAktif['nama'] . " ===\n";

// Get all subkriteria
$subkriteria = $subkriteriaModel->getWithKriteria();

echo "\n=== Daftar Subkriteria ===\n";
foreach ($subkriteria as $sk) {
    echo "ID: " . $sk['id'] . " | Kode: " . $sk['kode'] . " | Nama: " . $sk['nama'] . " | Kriteria: " . $sk['kriteria_nama'] . "\n";
}

// Create TppAnpController instance
$controller = new \App\Controllers\TppAnpController();

// Call the hitungAnpTargetFirst method using reflection to make it accessible
try {
    $reflection = new ReflectionMethod(\App\Controllers\TppAnpController::class, 'hitungAnpTargetFirst');
    $reflection->setAccessible(true);
    
    echo "\n=== Memanggil hitungAnpTargetFirst ===\n";
    ob_start();
    $result = $reflection->invoke($controller);
    $output = ob_get_clean();
    
    if (is_string($result)) {
        echo "✅ Success! View returned. Length: " . strlen($result) . " characters\n";
        
        // Check if the result contains the node-node matrix
        if (strpos($result, 'Matriks Node-Node (Pairwise)') !== false) {
            echo "✅ Node-node matrix found in response!\n";
            
            // Count how many targets with matrices are displayed
            $matches = [];
            preg_match_all('/Matriks Node-Node \(Pairwise\)/', $result, $matches);
            echo "✅ Matrices found for " . count($matches[0]) . " target(s)\n";
            
            // Check if we can find any matrix cells
            if (preg_match('/<span class="badge badge-primary">[\d\.]+<\/span>/', $result)) {
                echo "✅ Matrix cells with values found!\n";
            }
        }
        
        // Check if we have the final ANP results
        if (strpos($result, 'Hasil Perhitungan ANP (Target-First)') !== false) {
            echo "✅ Final ANP results found!\n";
        }
        
        // Check if per-target calculation results are present
        if (strpos($result, 'Hasil Perhitungan per Target') !== false) {
            echo "✅ Per-target calculation results found!\n";
        }
        
        // Check for any errors in the output
        if (strpos($result, 'error') !== false || strpos($result, 'Error') !== false) {
            echo "⚠️  Warning: Error messages found in response\n";
        }
        
        // Write the output to a temporary file for debugging
        file_put_contents('temp_response.html', $result);
        echo "\n✅ Full response saved to temp_response.html\n";
        
    } elseif (is_object($result)) {
        echo "✅ Success! Object returned: " . get_class($result) . "\n";
    } else {
        echo "✅ Success! Returned: " . var_export($result, true) . "\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
} catch (\Throwable $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== Test completed ===\n";
?>