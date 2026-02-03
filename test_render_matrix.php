<?php
// Setup CodeIgniter minimal
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);

$pathsPath = FCPATH . 'app/Config/Paths.php';
if (!file_exists($pathsPath)) {
    die("Cannot find the Paths file: $pathsPath");
}

require $pathsPath;
$paths = new Config\Paths();

define('APPPATH', $paths->appDirectory . DIRECTORY_SEPARATOR);
define('ROOTPATH', $paths->rootDirectory . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', $paths->systemDirectory . DIRECTORY_SEPARATOR);
define('WRITEPATH', $paths->writableDirectory . DIRECTORY_SEPARATOR);

// Load the autoloader
require_once SYSTEMPATH . 'Boot/psr.php';
require_once SYSTEMPATH . 'Config/Autoload.php';
require_once SYSTEMPATH . 'Config/Modules.php';
require_once APPPATH . 'Config/Autoload.php';
require_once APPPATH . 'Config/Constants.php';

use Config\Autoload;
use Config\Services;

$autoload = new Autoload();
$autoload->initialize();
$autoload->register();

Services::autoloader()->initialize($autoload);

// Create TppAnpController instance
use App\Controllers\TppAnpController;
use App\Models\PeriodeModel;

$controller = new TppAnpController();

// Test controller dependencies
echo "=== Testing TppAnpController dependencies ===\n";
try {
    $reflection = new ReflectionMethod($controller, 'pairwiseTarget');
    echo "✅ Method pairwiseTarget exists\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Create request and response
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\URI;
use Config\App;

$config = new App();
$request = new IncomingRequest($config, new URI('http://localhost/tpp/anp/pairwise-target?target_id=1'), null, new \CodeIgniter\HTTP\UserAgent());
Services::injectMock('request', $request);

$response = new Response($config);
Services::injectMock('response', $response);

// Set session
$session = Services::session();
$session->set('isLoggedIn', true);
$session->set('role', 'tpp');
$session->set('userId', 1);
$session->set('username', 'tpp');

echo "=== Session setup complete ===\n";

// Call pairwiseTarget method to see if it renders
echo "\n=== Calling pairwiseTarget method ===\n";
try {
    $result = $controller->pairwiseTarget();
    echo "✅ Method executed successfully\n";
    
    // Check if the response contains matrix-related content
    $output = $response->getBody();
    
    echo "\n=== Response contains matrix data ===\n";
    $hasMatrix = strpos($output, 'Matriks Node-Node (Pairwise)') !== false;
    echo "Matrix display: " . ($hasMatrix ? "✅ FOUND" : "❌ NOT FOUND") . "\n";
    
    $hasDecimalValues = preg_match('/0\.\d{1,4}/', $output);
    echo "Decimal values: " . ($hasDecimalValues ? "✅ FOUND" : "❌ NOT FOUND") . "\n";
    
    if (!$hasDecimalValues) {
        echo "\n=== Checking if any numerical values are present ===\n";
        if (preg_match('/\d+\.?\d*/', $output, $matches)) {
            echo "Found values: " . implode(", ", array_unique(array_slice($matches, 0, 5))) . "\n";
        }
    }
    
    // Find matrix cells with values
    if (preg_match_all('/<span class="badge badge-primary">([\d\.]+)<\/span>/', $output, $matches)) {
        echo "\n=== Matrix cell values ===\n";
        echo "Found " . count($matches[1]) . " cell values\n";
        echo "Sample values: " . implode(", ", array_slice($matches[1], 0, 8)) . "\n";
        
        // Check if decimal values are present
        $decimalValues = array_filter($matches[1], function($val) {
            return strpos($val, '.') !== false;
        });
        
        echo "Decimal values count: " . count($decimalValues) . "\n";
        if (!empty($decimalValues)) {
            echo "Decimal values found: " . implode(", ", array_slice($decimalValues, 0, 5)) . "\n";
        }
    }
    
    // Save output to file for debugging
    file_put_contents('test_matrix_output.html', $output);
    echo "\n✅ Response saved to test_matrix_output.html\n";
    
} catch (\Exception $e) {
    echo "❌ Error executing method: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== Test completed ===\n";
?>