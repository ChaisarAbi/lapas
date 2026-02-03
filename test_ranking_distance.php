<?php
// Test script to verify ranking distance calculation

// Include necessary files
require 'vendor/autoload.php';

// Test function to simulate rank calculation
function testDistanceCalculation() {
    echo "=== Testing Distance Calculation ===\n\n";
    
    // Test case 1: Multiple narapidana with different scores
    echo "Test 1: Multiple narapidana with different scores\n";
    $narapidana = [
        ['id' => 1, 'nama_lengkap' => 'Narapidana A', 'nomor_registrasi' => '123'],
        ['id' => 2, 'nama_lengkap' => 'Narapidana B', 'nomor_registrasi' => '456'],
        ['id' => 3, 'nama_lengkap' => 'Narapidana C', 'nomor_registrasi' => '789']
    ];
    
    $kriteria = [
        ['id' => 1, 'nama' => 'Kriteria 1', 'bobot' => 0.3, 'jenis' => 'Benefit'],
        ['id' => 2, 'nama' => 'Kriteria 2', 'bobot' => 0.5, 'jenis' => 'Benefit'],
        ['id' => 3, 'nama' => 'Kriteria 3', 'bobot' => 0.2, 'jenis' => 'Cost']
    ];
    
    $penilaian = [
        ['narapidana_id' => 1, 'subkriteria_id' => 1, 'nilai' => 85],
        ['narapidana_id' => 1, 'subkriteria_id' => 2, 'nilai' => 75],
        ['narapidana_id' => 1, 'subkriteria_id' => 3, 'nilai' => 60],
        ['narapidana_id' => 2, 'subkriteria_id' => 1, 'nilai' => 70],
        ['narapidana_id' => 2, 'subkriteria_id' => 2, 'nilai' => 80],
        ['narapidana_id' => 2, 'subkriteria_id' => 3, 'nilai' => 50],
        ['narapidana_id' => 3, 'subkriteria_id' => 1, 'nilai' => 90],
        ['narapidana_id' => 3, 'subkriteria_id' => 2, 'nilai' => 85],
        ['narapidana_id' => 3, 'subkriteria_id' => 3, 'nilai' => 70]
    ];
    
    // Test each controller's hitungRankingSederhana method
    $controllers = [
        'KalapasController' => new \App\Controllers\KalapasController(),
        'RankingController' => new \App\Controllers\RankingController(),
        'WaliController' => new \App\Controllers\WaliController()
    ];
    
    foreach ($controllers as $name => $controller) {
        echo "\n--- Testing {$name} ---\n";
        
        // Check if method exists
        if (method_exists($controller, 'hitungRankingSederhana')) {
            try {
                $ranking = $controller->hitungRankingSederhana($narapidana, $kriteria, $penilaian);
                
                if (!empty($ranking)) {
                    echo "✅ Method returned ranking data\n";
                    
                    // Check if distance values exist
                    $hasDistance = true;
                    foreach ($ranking as $item) {
                        if (!isset($item['d_positif']) || !isset($item['d_negatif'])) {
                            $hasDistance = false;
                            break;
                        }
                        
                        // Check if values are numeric
                        if (!is_numeric($item['d_positif']) || !is_numeric($item['d_negatif'])) {
                            $hasDistance = false;
                            break;
                        }
                    }
                    
                    if ($hasDistance) {
                        echo "✅ Distance values (d_positif, d_negatif) are present and numeric\n";
                        
                        // Show sample data
                        echo "Sample results:\n";
                        for ($i = 0; $i < min(3, count($ranking)); $i++) {
                            $item = $ranking[$i];
                        echo "  #" . ($i+1) . " " . $item['narapidana']['nama_lengkap'] . 
                             " - d_positif: " . number_format($item['d_positif'], 4) . 
                             ", d_negatif: " . number_format($item['d_negatif'], 4) . "\n";
                        }
                        
                    } else {
                        echo "❌ Distance values are missing or invalid\n";
                    }
                    
                } else {
                    echo "❌ Method returned empty ranking\n";
                }
                
            } catch (\Exception $e) {
                echo "❌ Error: " . $e->getMessage() . "\n";
            }
        } else {
            echo "❌ Method hitungRankingSederhana not found\n";
        }
    }
    
    // Test case 2: Single narapidana
    echo "\n\nTest 2: Single narapidana\n";
    $singleNarapidana = [
        ['id' => 1, 'nama_lengkap' => 'Narapidana Satu', 'nomor_registrasi' => '111']
    ];
    
    $singlePenilaian = [
        ['narapidana_id' => 1, 'subkriteria_id' => 1, 'nilai' => 80],
        ['narapidana_id' => 1, 'subkriteria_id' => 2, 'nilai' => 75],
        ['narapidana_id' => 1, 'subkriteria_id' => 3, 'nilai' => 65]
    ];
    
    foreach ($controllers as $name => $controller) {
        echo "\n--- Testing {$name} ---\n";
        
        try {
            $ranking = $controller->hitungRankingSederhana($singleNarapidana, $kriteria, $singlePenilaian);
            
            if (!empty($ranking)) {
                $item = $ranking[0];
                
                if (isset($item['d_positif']) && isset($item['d_negatif'])) {
                    echo "✅ Distance values are present for single narapidana\n";
                    echo "d_positif: " . number_format($item['d_positif'], 4) . 
                         ", d_negatif: " . number_format($item['d_negatif'], 4) . "\n";
                } else {
                    echo "❌ Distance values are missing for single narapidana\n";
                }
                
            } else {
                echo "❌ Method returned empty ranking\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n\n=== All tests completed ===";
}

// Run the test
try {
    testDistanceCalculation();
} catch (\Exception $e) {
    echo "❌ Critical error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>