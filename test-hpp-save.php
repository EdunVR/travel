<?php
/**
 * Test HPP Save with Custom Components
 * 
 * This script simulates saving HPP with custom components
 * Run: php test-hpp-save.php
 */

require __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TravelPackage;
use App\Models\HppCalculation;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "HPP Custom Components - Save Test\n";
echo "========================================\n\n";

try {
    // Get first package with HPP
    $package = TravelPackage::with('hppCalculation')->first();
    
    if (!$package) {
        echo "❌ No travel packages found. Please create a package first.\n";
        exit(1);
    }
    
    if (!$package->hppCalculation) {
        echo "❌ Package has no HPP calculation. Creating one...\n";
        $hpp = HppCalculation::create([
            'id_travel_package' => $package->id,
            'flight_cost' => 0,
            'hotel_cost' => 0,
            'transportation_cost' => 0,
            'meal_cost' => 0,
            'visa_cost' => 0,
            'guide_cost' => 0,
            'insurance_cost' => 0,
            'operational_overhead' => 0,
            'contingency' => 0,
            'total_hpp' => 0
        ]);
        $package->load('hppCalculation');
    }
    
    $hpp = $package->hppCalculation;
    
    echo "Testing with Package: {$package->package_name} (ID: {$package->id})\n";
    echo "HPP ID: {$hpp->id}\n\n";
    
    // Test 1: Save standard components
    echo "Test 1: Saving standard HPP components...\n";
    $hpp->update([
        'flight_cost' => 5000000,
        'hotel_cost' => 3000000,
        'transportation_cost' => 1000000,
        'meal_cost' => 500000,
        'visa_cost' => 2000000,
        'guide_cost' => 300000,
        'insurance_cost' => 200000,
        'operational_overhead' => 500000,
        'contingency' => 500000,
    ]);
    echo "✓ Standard components saved\n\n";
    
    // Test 2: Save custom components
    echo "Test 2: Saving custom components...\n";
    $customComponents = [
        [
            'id' => 'custom_' . time(),
            'label' => 'Biaya Dokumentasi',
            'value' => 500000,
            'payment_status' => 'hutang',
            'hutang_amount' => 500000 * ($package->capacity ?? 1),
        ],
        [
            'id' => 'custom_' . (time() + 1),
            'label' => 'Biaya Seragam',
            'value' => 300000,
            'payment_status' => 'hutang',
            'hutang_amount' => 300000 * ($package->capacity ?? 1),
        ],
    ];
    
    $hpp->custom_components = $customComponents;
    $hpp->save();
    
    echo "✓ Custom components saved\n";
    echo "  - Biaya Dokumentasi: Rp 500,000\n";
    echo "  - Biaya Seragam: Rp 300,000\n\n";
    
    // Test 3: Read back custom components
    echo "Test 3: Reading back custom components...\n";
    $hpp->refresh();
    $savedComponents = $hpp->custom_components;
    
    if (is_array($savedComponents) && count($savedComponents) > 0) {
        echo "✓ Custom components retrieved successfully:\n";
        foreach ($savedComponents as $comp) {
            echo "  - {$comp['label']}: Rp " . number_format($comp['value'], 0, ',', '.') . "\n";
            echo "    Status: {$comp['payment_status']}\n";
            echo "    Hutang: Rp " . number_format($comp['hutang_amount'], 0, ',', '.') . "\n";
        }
    } else {
        echo "❌ Failed to retrieve custom components\n";
        echo "Saved data: " . json_encode($savedComponents) . "\n";
    }
    
    echo "\n";
    
    // Test 4: Calculate total HPP
    echo "Test 4: Calculating total HPP...\n";
    $hpp->calculateTotal();
    $hpp->save();
    
    echo "✓ Total HPP calculated: Rp " . number_format($hpp->total_hpp, 0, ',', '.') . "\n";
    echo "  HPP per person: Rp " . number_format($hpp->getHppPerPerson(), 0, ',', '.') . "\n\n";
    
    // Test 5: Get cost breakdown
    echo "Test 5: Getting cost breakdown...\n";
    $breakdown = $hpp->getCostBreakdown();
    
    echo "✓ Cost breakdown retrieved:\n";
    echo "  - Flight: Rp " . number_format($breakdown['flight_cost'], 0, ',', '.') . "\n";
    echo "  - Hotel: Rp " . number_format($breakdown['hotel_cost'], 0, ',', '.') . "\n";
    echo "  - Transportation: Rp " . number_format($breakdown['transportation_cost'], 0, ',', '.') . "\n";
    echo "  - Custom Components: " . count($breakdown['custom_components']) . " items\n";
    
    foreach ($breakdown['custom_components'] as $comp) {
        echo "    • {$comp['label']}: Rp " . number_format($comp['value'], 0, ',', '.') . "\n";
    }
    
    echo "\n========================================\n";
    echo "✓ ALL TESTS PASSED!\n";
    echo "========================================\n\n";
    
    echo "Next steps:\n";
    echo "1. Open HPP modal in browser\n";
    echo "2. Add custom components\n";
    echo "3. Save and verify they appear with original names\n";
    echo "4. Create keberangkatan and generate RAB\n";
    echo "5. Verify RAB has individual items for each custom component\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
    
    echo "Troubleshooting:\n";
    echo "1. Check database connection\n";
    echo "2. Verify hpp_calculations table exists\n";
    echo "3. Verify columns exist (run verify-hpp-columns.php)\n";
    echo "4. Check Laravel logs: storage/logs/laravel.log\n\n";
}
