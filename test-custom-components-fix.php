<?php
/**
 * Test Custom Components Fix
 * 
 * This script tests:
 * 1. Custom components save with original labels
 * 2. RAB generation with HUTANG status and realisasi 0
 * 
 * Run: php test-custom-components-fix.php
 */

require __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TravelPackage;
use App\Models\HppCalculation;
use App\Models\Keberangkatan;
use App\Services\RabIntegrationService;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "Custom Components Fix - Test\n";
echo "========================================\n\n";

try {
    // Get first package
    $package = TravelPackage::with('hppCalculation')->first();
    
    if (!$package || !$package->hppCalculation) {
        echo "❌ No package with HPP found\n";
        exit(1);
    }
    
    $hpp = $package->hppCalculation;
    
    echo "Testing with Package: {$package->package_name}\n";
    echo "Capacity: {$package->capacity} pax\n\n";
    
    // Test 1: Save custom components with labels
    echo "Test 1: Saving custom components with labels...\n";
    
    $customComponents = [
        [
            'id' => 'custom_' . time(),
            'label' => 'Biaya Dokumentasi',
            'value' => 500000,
            'payment_status' => 'hutang',
            'hutang_amount' => 500000 * $package->capacity,
        ],
        [
            'id' => 'custom_' . (time() + 1),
            'label' => 'Biaya Seragam',
            'value' => 300000,
            'payment_status' => 'hutang',
            'hutang_amount' => 300000 * $package->capacity,
        ],
        [
            'id' => 'custom_' . (time() + 2),
            'label' => 'Biaya Perlengkapan',
            'value' => 200000,
            'payment_status' => 'hutang',
            'hutang_amount' => 200000 * $package->capacity,
        ],
    ];
    
    $hpp->custom_components = $customComponents;
    $hpp->save();
    
    echo "✓ Custom components saved\n\n";
    
    // Test 2: Verify custom components retrieved with labels
    echo "Test 2: Verifying custom components...\n";
    $hpp->refresh();
    $saved = $hpp->custom_components;
    
    if (!is_array($saved) || count($saved) === 0) {
        echo "❌ No custom components found\n";
        exit(1);
    }
    
    foreach ($saved as $comp) {
        $label = $comp['label'] ?? 'NO LABEL';
        $value = $comp['value'] ?? 0;
        echo "  ✓ {$label}: Rp " . number_format($value, 0, ',', '.') . "\n";
        
        if ($label === 'NO LABEL' || $label === 'Biaya Operasional') {
            echo "    ❌ ERROR: Label tidak tersimpan dengan benar!\n";
            exit(1);
        }
    }
    
    echo "\n";
    
    // Test 3: Generate RAB and check status
    echo "Test 3: Testing RAB generation...\n";
    
    // Get or create keberangkatan
    $keberangkatan = $package->keberangkatan()->first();
    if (!$keberangkatan) {
        echo "  Creating test keberangkatan...\n";
        $keberangkatan = Keberangkatan::create([
            'keberangkatan_code' => 'TEST-' . time(),
            'keberangkatan_name' => 'Test Keberangkatan',
            'id_travel_package' => $package->id,
            'departure_date' => now()->addDays(30),
            'return_date' => now()->addDays(40),
            'total_jamaah' => $package->capacity,
            'status' => 'planning',
            'id_outlet' => $package->id_outlet,
        ]);
    }
    
    $rabService = app(RabIntegrationService::class);
    $components = $rabService->generateRabComponents($keberangkatan);
    
    echo "  Generated " . count($components) . " RAB components\n\n";
    
    // Check custom components in RAB
    echo "Test 4: Verifying custom components in RAB...\n";
    
    $customFound = 0;
    foreach ($components as $comp) {
        $item = $comp['item'];
        $status = $comp['payment_status'];
        $realisasi = $comp['realisasi'];
        $budget = $comp['biaya'];
        
        // Check if this is a custom component
        if (in_array($item, ['Biaya Dokumentasi', 'Biaya Seragam', 'Biaya Perlengkapan'])) {
            $customFound++;
            echo "  ✓ Found: {$item}\n";
            echo "    Budget: Rp " . number_format($budget, 0, ',', '.') . "\n";
            echo "    Status: {$status}\n";
            echo "    Realisasi: Rp " . number_format($realisasi, 0, ',', '.') . "\n";
            
            // Verify status is HUTANG
            if ($status !== 'hutang') {
                echo "    ❌ ERROR: Status should be 'hutang', got '{$status}'\n";
                exit(1);
            }
            
            // Verify realisasi is 0
            if ($realisasi != 0) {
                echo "    ❌ ERROR: Realisasi should be 0, got {$realisasi}\n";
                exit(1);
            }
            
            echo "    ✓ Status and realisasi correct\n\n";
        }
    }
    
    if ($customFound !== 3) {
        echo "  ❌ ERROR: Expected 3 custom components, found {$customFound}\n";
        exit(1);
    }
    
    echo "========================================\n";
    echo "✓ ALL TESTS PASSED!\n";
    echo "========================================\n\n";
    
    echo "Summary:\n";
    echo "✓ Custom components save with original labels\n";
    echo "✓ Custom components retrieve with correct names\n";
    echo "✓ RAB generates individual items for each custom component\n";
    echo "✓ RAB status is HUTANG (not LUNAS)\n";
    echo "✓ RAB realisasi is 0 (not 100%)\n\n";
    
    echo "Next steps:\n";
    echo "1. Test in browser: Add custom component via HPP modal\n";
    echo "2. Verify component shows with original name (not 'Biaya Operasional')\n";
    echo "3. Create keberangkatan and generate RAB\n";
    echo "4. Verify RAB has HUTANG status with realisasi 0\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
}
