<?php
// Test interdependensi data save and retrieval
require 'vendor/autoload.php';

// Bootstrapping
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class TestInterdependensi extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;
    
    protected $controller;
    
    public function setUp(): void
    {
        parent::setUp();
        
        // Create controller instance
        $this->controller = new \App\Controllers\TppAnpController();
        
        // Seed data if not already seeded
        $seeder = new \App\Database\Seeds\AnpBimkesSeeder();
        $seeder->run();
    }
    
    public function testInterdependensiSave()
    {
        echo "=== Testing Interdependensi Data Flow ===\n\n";
        
        // 1. Check if we have any existing interdependensi data
        $db = \Config\Database::connect();
        $count = $db->table('anp_interdependensi')->countAll();
        echo "1. Existing interdependensi count: $count\n";
        
        // 2. Clear existing interdependensi data
        $db->table('anp_interdependensi')->truncate();
        echo "2. Interdependensi table cleared\n";
        
        // 3. Check if we have any existing interdependensi data
        $count = $db->table('anp_interdependensi')->countAll();
        echo "3. Interdependensi count after clear: $count\n";
        
        // 4. Simulate calling hitungAnpTargetFirst method
        echo "\n4. Calling hitungAnpTargetFirst method...\n";
        
        // Get active period
        $periodeModel = new \App\Models\PeriodeModel();
        $periodeAktif = $periodeModel->where('status', 'aktif')->first();
        $this->assertNotNull($periodeAktif, "No active period found");
        
        // Get all subkriteria
        $subkriteriaModel = new \App\Models\SubkriteriaModel();
        $subkriteria = $subkriteriaModel->getWithKriteria();
        $this->assertNotEmpty($subkriteria, "No subkriteria found");
        
        // Call the hitungAnpTargetFirst method
        $result = $this->get('tpp/anp/hitung-anp-target-first', [
            'target_id' => 1
        ]);
        
        $result->assertStatus(200);
        
        // Check if the response contains success message
        $this->assertStringContainsStringIgnoringCase('berhasil', $result->response()->getBody());
        
        // 5. Check if interdependensi data was saved to the database
        $countAfterSave = $db->table('anp_interdependensi')->countAll();
        echo "5. Interdependensi count after save: $countAfterSave\n";
        
        $this->assertGreaterThan(0, $countAfterSave);
        
        // 6. Get first few records of interdependensi data
        $records = $db->table('anp_interdependensi')
            ->select('*')
            ->orderBy('id')
            ->limit(10)
            ->get()
            ->getResultArray();
        
        echo "6. First 10 interdependensi records:\n";
        foreach ($records as $index => $record) {
            echo "   ID: " . $record['id'] . 
                 ", Cluster From: " . $record['cluster_id_dari'] . 
                 ", Cluster To: " . $record['cluster_id_ke'] . 
                 ", Nilai: " . $record['nilai'] . 
                 ", Tipe: " . $record['tipe'] . "\n";
        }
        
        // 7. Check if interdependensi data contains decimal values
        $decimalCount = $db->table('anp_interdependensi')
            ->where('nilai NOT IN (0, 1)')
            ->orWhere('MOD(nilai * 100, 100) > 0')
            ->countAllResults();
            
        echo "\n7. Decimal values count: $decimalCount\n";
        
        // 8. Check calculation results are present in response
        $this->assertStringContainsString('Matriks Node-Node (Pairwise)', $result->response()->getBody());
        
        // 9. Check matrix cells have decimal values
        $body = $result->response()->getBody();
        preg_match_all('/<span class="badge badge-primary">([\d\.]+)<\/span>/', $body, $matches);
        
        echo "\n9. Found " . count($matches[1]) . " matrix cell values\n";
        
        // Check if decimal values are present
        $hasDecimals = false;
        foreach ($matches[1] as $value) {
            if (strpos($value, '.') !== false) {
                $hasDecimals = true;
                break;
            }
        }
        
        $this->assertTrue($hasDecimals, "Expected to find decimal values in matrix");
        
        echo "\n✅ Test passed! Interdependensi data was saved and matrix displays decimal values\n";
    }
}

// Run the test
$test = new TestInterdependensi();
$test->setUp();

try {
    $test->testInterdependensiSave();
    echo "\n🎉 All tests passed!";
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

?>