<?php
// Simple script to check pairwise values in database

// Bootstrapping
require __DIR__ . '/vendor/autoload.php';

// Create CI instance
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Boot/psr.php';

$app = new \CodeIgniter\CodeIgniter($paths);
$app->initialize();
\CodeIgniter\Config\Services::router()->setGlobalFilters(['toolbar', 'csrf']);

// Load models
$db = \Config\Database::connect();

// Check if table exists
echo "=== Checking anp_pairwise_histori table ===\n";

$tableExists = $db->tableExists('anp_pairwise_histori');
if (!$tableExists) {
    echo "❌ Table anp_pairwise_histori does NOT exist\n";
    echo "   Run migrations first: php spark migrate\n";
    exit(1);
}

echo "✅ Table exists\n\n";

// Count total records
$totalRecords = $db->table('anp_pairwise_histori')->countAll();
echo "Total records: $totalRecords\n";

// Get distinct periods
$periods = $db->table('anp_pairwise_histori')
    ->select('periode_id, COUNT(*) as count')
    ->groupBy('periode_id')
    ->get()
    ->getResultArray();

echo "Records per period:\n";
foreach ($periods as $period) {
    echo "  Period {$period['periode_id']}: {$period['count']} records\n";
}

// Check for target node 1
echo "\n=== Target node 1 ===\n";
$target1Data = $db->table('anp_pairwise_histori')
    ->where('target_node_id', 1)
    ->get()
    ->getResultArray();

echo "Records for target 1: " . count($target1Data) . "\n";

if (!empty($target1Data)) {
    echo "Sample values:\n";
    for ($i = 0; $i < min(5, count($target1Data)); $i++) {
        $row = $target1Data[$i];
        echo "  From " . $row['node_dari_kode'] . " to " . $row['node_ke_kode'] . ": " . $row['skala'] . "\n";
    }
}

// Get unique scale values
echo "\n=== Unique scale values ===\n";
$uniqueScales = $db->table('anp_pairwise_histori')
    ->select('skala')
    ->distinct()
    ->orderBy('skala')
    ->get()
    ->getResultArray();

echo "Unique values: " . count($uniqueScales) . "\n";
echo "Values:\n";
foreach ($uniqueScales as $scale) {
    $count = $db->table('anp_pairwise_histori')
        ->where('skala', $scale['skala'])
        ->countAllResults();
    echo "  " . $scale['skala'] . " (" . $count . " times)\n";
}

// Check if decimal values exist
$decimalValues = $db->table('anp_pairwise_histori')
    ->where('skala NOT IN (1,2,3,4,5,6,7,8,9)')
    ->get()
    ->getResultArray();

echo "\n=== Decimal values ===\n";
echo "Count: " . count($decimalValues) . "\n";
if (!empty($decimalValues)) {
    echo "Sample decimal values:\n";
    for ($i = 0; $i < min(3, count($decimalValues)); $i++) {
        $row = $decimalValues[$i];
        echo "  From " . $row['node_dari_kode'] . " to " . $row['node_ke_kode'] . ": " . $row['skala'] . "\n";
    }
}

// Display average scale value
$avgScale = $db->table('anp_pairwise_histori')
    ->selectAvg('skala', 'avg_skala')
    ->get()
    ->getRow();

echo "\n=== Average scale ===\n";
echo "Average: " . $avgScale->avg_skala . "\n";

echo "\n=== Summary ===\n";
echo "Database has $totalRecords pairwise entries\n";
echo count($decimalValues) . " entries have decimal values\n";
echo (count($decimalValues) > 0) ? "✅ Decimal values are properly stored in the database\n" : "❌ No decimal values found in the database";
?>