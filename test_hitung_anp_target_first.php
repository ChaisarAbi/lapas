<?php
// Test script untuk menjalankan hitungAnpTargetFirst

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

// Create request and response
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\URI;
use Config\App;

$config = new App();
$request = new IncomingRequest($config, new URI('http://localhost/tpp/anp/hitung-anp-target-first'), null, new \CodeIgniter\HTTP\UserAgent());
Services::injectMock('request', $request);

// Create controller instance
use App\Controllers\TppAnpController;
use App\Models\PeriodeModel;
use App\Models\SubkriteriaModel;
use App\Models\AnpModel;

echo "=== TEST HITUNG ANP TARGET-FIRST ===\n\n";

$controller = new TppAnpController();

// Gunakan reflection untuk memanggil method private buildMatrixForTargetWithEdges
$reflectionMethod = new ReflectionMethod($controller, 'buildMatrixForTargetWithEdges');
$reflectionMethod->setAccessible(true);

// Test untuk satu target dulu
$periodeModel = new PeriodeModel();
$periodeAktif = $periodeModel->where('status', 'aktif')->first();
if (!$periodeAktif) {
    echo "❌ Tidak ada periode aktif.\n";
    exit;
}

$periodeId = $periodeAktif['id'];
echo "Periode aktif: ID = $periodeId\n";

$subkriteriaModel = new SubkriteriaModel();
$allSubkriteria = $subkriteriaModel->getWithKriteria();
echo "Total subkriteria: " . count($allSubkriteria) . "\n\n";

// Hitung complete target count
$completeTargetCount = 0;
$edgeModel = new \App\Models\EdgeModel();
$anpModel = new AnpModel();

// Map id -> index
$idToIndex = [];
foreach ($allSubkriteria as $idx => $sk) {
    $idToIndex[$sk['id']] = $idx;
}

echo "=== CHECKING EACH TARGET ===\n";

foreach ($allSubkriteria as $tIndex => $target) {
    $targetId = $target['id'];
    
    echo "Target {$target['kode']} (ID: $targetId): ";
    
    // build matrix menggunakan edges
    $matrixData = $reflectionMethod->invoke($controller, $targetId, $periodeId, $allSubkriteria);
    if (!$matrixData || empty($matrixData['influencers'])) {
        echo "❌ Tidak ada influencers\n";
        continue;
    }
    
    $k = count($matrixData['influencers']);
    if ($k < 2) {
        echo "❌ Kurang dari 2 influencers\n";
        continue;
    }
    
    $totalPairs = $k * ($k - 1) / 2;
    $filledPairs = $matrixData['filled_pairs'] ?? 0;
    
    // harus lengkap baru dipakai
    if ($filledPairs < $totalPairs) {
        echo "❌ Pairwise tidak lengkap ($filledPairs/$totalPairs)\n";
        continue;
    }
    
    // hitung bobot influencer terhadap target
    $ahp = $anpModel->calculateAhpReport($matrixData['matrix'], $matrixData['influencers']);
    if (!$ahp || empty($ahp['weights'])) {
        echo "❌ Gagal hitung AHP\n";
        continue;
    }
    
    $completeTargetCount++;
    echo "✅ LENGKAP (influencers: $k, weights: " . count($ahp['weights']) . ")\n";
}

echo "\nTotal target lengkap: $completeTargetCount dari " . count($allSubkriteria) . "\n";

if ($completeTargetCount === 0) {
    echo "\n❌ ERROR: Tidak ada target yang lengkap. Ini akan menyebabkan error 'Belum ada target yang matriksnya lengkap'\n";
    
    // Debug lebih detail
    echo "\n=== DETAILED DEBUG ===\n";
    foreach ($allSubkriteria as $target) {
        $targetId = $target['id'];
        $matrixData = $reflectionMethod->invoke($controller, $targetId, $periodeId, $allSubkriteria);
        
        echo "Target {$target['kode']}:\n";
        echo "  Influencers: " . (isset($matrixData['influencers']) ? count($matrixData['influencers']) : 0) . "\n";
        
        if (isset($matrixData['filled_pairs'])) {
            $k = isset($matrixData['influencers']) ? count($matrixData['influencers']) : 0;
            $totalPairs = $k * ($k - 1) / 2;
            $filledPairs = $matrixData['filled_pairs'];
            echo "  Filled pairs: $filledPairs/$totalPairs\n";
            echo "  Is complete: " . ($filledPairs >= $totalPairs ? 'YES' : 'NO') . "\n";
        }
        echo "\n";
    }
} else {
    echo "\n✅ Siap untuk hitung ANP Target-First!\n";
    
    // Coba panggil method hitungAnpTargetFirst langsung
    try {
        echo "\n=== MENCoba hitungAnpTargetFirst ===\n";
        
        // Gunakan reflection untuk memanggil method public hitungAnpTargetFirst
        $hitungMethod = new ReflectionMethod($controller, 'hitungAnpTargetFirst');
        
        // Buat mock response
        $response = new Response($config);
        Services::injectMock('response', $response);
        
        // Execute method
        $result = $hitungMethod->invoke($controller);
        
        echo "Method executed successfully.\n";
        
        // Cek apakah ada error
        $session = Services::session();
        $error = $session->getFlashdata('error');
        $success = $session->getFlashdata('success');
        
        if ($error) {
            echo "❌ Error: $error\n";
        } elseif ($success) {
            echo "✅ Success: $success\n";
        } else {
            echo "⚠️  No flash messages.\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
}

echo "\n=== TEST SELESAI ===\n";