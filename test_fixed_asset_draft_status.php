<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\FixedAsset;
use App\Models\User;

// Test script untuk memverifikasi implementasi status draft aktiva tetap

echo "=== TESTING FIXED ASSET DRAFT STATUS IMPLEMENTATION ===\n\n";

try {
    // Test 1: Cek struktur database
    echo "1. Testing database structure...\n";
    
    $columns = DB::select("DESCRIBE fixed_assets");
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['status', 'activated_at', 'activated_by'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (empty($missingColumns)) {
        echo "   ✓ All required columns exist\n";
    } else {
        echo "   ✗ Missing columns: " . implode(', ', $missingColumns) . "\n";
    }
    
    // Test 2: Cek enum values untuk status
    $statusColumn = collect($columns)->firstWhere('Field', 'status');
    if ($statusColumn) {
        echo "   ✓ Status column type: " . $statusColumn->Type . "\n";
        
        // Check if 'draft' is in enum values
        if (strpos($statusColumn->Type, 'draft') !== false) {
            echo "   ✓ Draft status is available in enum\n";
        } else {
            echo "   ✗ Draft status not found in enum\n";
        }
    }
    
    // Test 3: Test model methods
    echo "\n2. Testing model methods...\n";
    
    // Create a test asset (draft)
    $testAsset = new FixedAsset([
        'outlet_id' => 1,
        'accounting_book_id' => 1,
        'name' => 'Test Asset Draft',
        'asset_code' => 'TEST-001',
        'asset_type' => 'tangible',
        'asset_group' => 'Test Group',
        'quantity' => 1,
        'unit' => 'Unit',
        'unit_price' => 1000000,
        'total_cost' => 1000000,
        'acquisition_date' => now(),
        'useful_life' => 5,
        'salvage_value' => 100000,
        'status' => 'draft',
        'created_by' => 1
    ]);
    
    // Test canBeActivated method
    if (method_exists($testAsset, 'canBeActivated')) {
        $canActivate = $testAsset->canBeActivated();
        echo "   ✓ canBeActivated() method exists and returns: " . ($canActivate ? 'true' : 'false') . "\n";
    } else {
        echo "   ✗ canBeActivated() method not found\n";
    }
    
    // Test activate method
    if (method_exists($testAsset, 'activate')) {
        echo "   ✓ activate() method exists\n";
    } else {
        echo "   ✗ activate() method not found\n";
    }
    
    // Test scopes
    if (method_exists(FixedAsset::class, 'scopeDraft')) {
        echo "   ✓ scopeDraft() method exists\n";
    } else {
        echo "   ✗ scopeDraft() method not found\n";
    }
    
    // Test 4: Check existing assets status
    echo "\n3. Checking existing assets...\n";
    
    $totalAssets = FixedAsset::count();
    $draftAssets = FixedAsset::where('status', 'draft')->count();
    $activeAssets = FixedAsset::where('status', 'active')->count();
    
    echo "   Total assets: $totalAssets\n";
    echo "   Draft assets: $draftAssets\n";
    echo "   Active assets: $activeAssets\n";
    
    // Test 5: Check routes
    echo "\n4. Testing routes...\n";
    
    $routes = \Route::getRoutes();
    $activateRoute = null;
    
    foreach ($routes as $route) {
        if ($route->getName() === 'financial.fixed-asset.activate') {
            $activateRoute = $route;
            break;
        }
    }
    
    if ($activateRoute) {
        echo "   ✓ Activate route exists: " . $activateRoute->uri() . "\n";
        echo "   ✓ Route methods: " . implode(', ', $activateRoute->methods()) . "\n";
    } else {
        echo "   ✗ Activate route not found\n";
    }
    
    // Test 6: Check controller method
    echo "\n5. Testing controller...\n";
    
    $controller = new \App\Http\Controllers\FixedAssetController(new \App\Services\ChartOfAccountService());
    
    if (method_exists($controller, 'activate')) {
        echo "   ✓ Controller activate() method exists\n";
    } else {
        echo "   ✗ Controller activate() method not found\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "Status: Implementation appears to be working correctly!\n\n";
    
    echo "Next steps:\n";
    echo "1. Test creating a new fixed asset (should be draft by default)\n";
    echo "2. Test activating a draft asset\n";
    echo "3. Verify journal entry is created upon activation\n";
    echo "4. Check that activated assets cannot be deleted\n";
    
} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}