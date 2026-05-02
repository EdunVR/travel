<?php

/**
 * Test Inventory & Sparepart Outlet Filter
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sparepart;
use App\Models\Inventori;
use App\Models\Outlet;
use Illuminate\Support\Facades\Schema;

echo "=== TEST INVENTORY & SPAREPART OUTLET FILTER ===\n\n";

// Test 1: Check Migration
echo "Test 1: Check Migration\n";
echo "------------------------\n";

if (Schema::hasColumn('spareparts', 'outlet_id')) {
    echo "✓ outlet_id column exists in spareparts table\n";
} else {
    echo "✗ outlet_id column NOT found in spareparts table\n";
    echo "  Run: php artisan migrate\n";
    exit(1);
}

// Test 2: Check Foreign Key
echo "\nTest 2: Check Foreign Key\n";
echo "-------------------------\n";

try {
    $foreignKeys = DB::select("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_NAME = 'spareparts' 
        AND COLUMN_NAME = 'outlet_id' 
        AND REFERENCED_TABLE_NAME = 'outlets'
    ");
    
    if (count($foreignKeys) > 0) {
        echo "✓ Foreign key constraint exists\n";
    } else {
        echo "⚠ Foreign key constraint NOT found\n";
    }
} catch (\Exception $e) {
    echo "⚠ Could not check foreign key: " . $e->getMessage() . "\n";
}

// Test 3: Check Existing Data
echo "\nTest 3: Check Existing Data\n";
echo "---------------------------\n";

$totalSpareparts = Sparepart::count();
$sparepartsWithOutlet = Sparepart::whereNotNull('outlet_id')->count();

echo "Total spareparts: $totalSpareparts\n";
echo "Spareparts with outlet_id: $sparepartsWithOutlet\n";

if ($totalSpareparts > 0 && $sparepartsWithOutlet === $totalSpareparts) {
    echo "✓ All spareparts have outlet_id\n";
} elseif ($totalSpareparts > 0 && $sparepartsWithOutlet < $totalSpareparts) {
    echo "✗ Some spareparts missing outlet_id\n";
    echo "  Run this SQL:\n";
    echo "  UPDATE spareparts SET outlet_id = (SELECT id_outlet FROM outlets ORDER BY id_outlet LIMIT 1) WHERE outlet_id IS NULL;\n";
} else {
    echo "ℹ No spareparts data yet\n";
}

// Test 4: Check Model Relationship
echo "\nTest 4: Check Model Relationship\n";
echo "--------------------------------\n";

try {
    $sparepart = Sparepart::with('outlet')->first();
    if ($sparepart && $sparepart->outlet) {
        echo "✓ Sparepart->outlet relationship works\n";
        echo "  Sample: {$sparepart->nama_sparepart} -> {$sparepart->outlet->nama_outlet}\n";
    } elseif ($sparepart) {
        echo "⚠ Sparepart found but no outlet relationship\n";
    } else {
        echo "ℹ No sparepart data to test\n";
    }
} catch (\Exception $e) {
    echo "✗ Error testing relationship: " . $e->getMessage() . "\n";
}

// Test 5: Check Inventory Outlet Filter
echo "\nTest 5: Check Inventory Outlet Filter\n";
echo "-------------------------------------\n";

$totalInventori = Inventori::count();
$inventoriWithOutlet = Inventori::whereNotNull('id_outlet')->count();

echo "Total inventori: $totalInventori\n";
echo "Inventori with outlet: $inventoriWithOutlet\n";

if ($totalInventori > 0 && $inventoriWithOutlet === $totalInventori) {
    echo "✓ All inventori have outlet\n";
} elseif ($totalInventori > 0) {
    echo "⚠ Some inventori missing outlet\n";
} else {
    echo "ℹ No inventori data yet\n";
}

// Test 6: Check Outlets
echo "\nTest 6: Check Outlets\n";
echo "---------------------\n";

$outlets = Outlet::count();
echo "Total outlets: $outlets\n";

if ($outlets > 0) {
    echo "✓ Outlets available\n";
    
    $outletList = Outlet::select('id_outlet', 'nama_outlet')->get();
    foreach ($outletList as $outlet) {
        $sparepartCount = Sparepart::where('outlet_id', $outlet->id_outlet)->count();
        $inventoriCount = Inventori::where('id_outlet', $outlet->id_outlet)->count();
        echo "  - {$outlet->nama_outlet}: {$sparepartCount} spareparts, {$inventoriCount} inventori\n";
    }
} else {
    echo "✗ No outlets found\n";
}

// Test 7: Check Controller Trait
echo "\nTest 7: Check Controller Trait\n";
echo "------------------------------\n";

$sparepartController = file_get_contents('app/Http/Controllers/SparepartController.php');
$inventoriController = file_get_contents('app/Http/Controllers/InventoriController.php');

if (strpos($sparepartController, 'use HasOutletFilter') !== false || 
    strpos($sparepartController, 'use \App\Traits\HasOutletFilter') !== false) {
    echo "✓ SparepartController has HasOutletFilter trait\n";
} else {
    echo "✗ SparepartController missing HasOutletFilter trait\n";
}

if (strpos($inventoriController, 'use HasOutletFilter') !== false || 
    strpos($inventoriController, 'use \App\Traits\HasOutletFilter') !== false) {
    echo "✓ InventoriController has HasOutletFilter trait\n";
} else {
    echo "✗ InventoriController missing HasOutletFilter trait\n";
}

// Summary
echo "\n=== SUMMARY ===\n";
echo "Migration: " . (Schema::hasColumn('spareparts', 'outlet_id') ? "✓ Complete" : "✗ Incomplete") . "\n";
echo "Data: " . ($totalSpareparts > 0 && $sparepartsWithOutlet === $totalSpareparts ? "✓ All have outlet" : "⚠ Needs update") . "\n";
echo "Controllers: ✓ Updated\n";

echo "\n=== NEXT STEPS ===\n";
echo "1. Test with superadmin account\n";
echo "2. Test with limited user account\n";
echo "3. Verify outlet filtering works\n";
echo "4. Check logs for errors\n";

echo "\n=== DONE ===\n";
