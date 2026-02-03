<?php
// Test script to directly call the TppAnpController::hitungAnpTargetFirst method

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Boot CodeIgniter
require __DIR__ . '/vendor/autoload.php';

// Create CodeIgniter 4 application instance
$app = \CodeIgniter\CodeIgniter::create();

// Initialize the application
$app->initialize();

try {
    // Get the controller instance
    $controller = new \App\Controllers\TppAnpController();
    
    // Call the hitungAnpTargetFirst method
    echo "Calling hitungAnpTargetFirst()...\n";
    $result = $controller->hitungAnpTargetFirst();
    
    // Check if the result is a Response object or a view
    if (is_string($result)) {
        echo "\nSuccess! View returned. Length: " . strlen($result) . " characters\n";
    } elseif (is_object($result) && method_exists($result, 'getBody')) {
        echo "\nSuccess! Response returned. Status: " . $result->getStatusCode() . "\n";
        $body = $result->getBody();
        echo "Body length: " . strlen($body) . " characters\n";
        
        // Check if the response contains the success message
        if (strpos($body, 'Hitung ANP (Target-First) berhasil') !== false) {
            echo "\n✅ Calculation completed successfully!\n";
        }
        
        // Check if the response contains calculation results
        if (strpos($body, 'Hasil Perhitungan ANP') !== false) {
            echo "\n✅ Detailed results found in response!\n";
        }
        
        // Check if the response contains per-target calculation results
        if (strpos($body, 'Hasil Perhitungan per Target') !== false) {
            echo "\n✅ Per-target calculation results found!\n";
        }
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
} catch (Throwable $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";