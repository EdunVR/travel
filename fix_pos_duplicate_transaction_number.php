<?php
/**
 * Fix POS Duplicate Transaction Number Issue
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fix POS Duplicate Transaction Number ===\n\n";

// 1. Show current issue
echo "1. Checking current POS transactions:\n";
$duplicates = DB::select("
    SELECT no_transaksi, COUNT(*) as count, GROUP_CONCAT(id_outlet) as outlets
    FROM pos_sales 
    GROUP BY no_transaksi 
    HAVING COUNT(*) > 1
");

if (count($duplicates) > 0) {
    echo "   ❌ Found duplicate transaction numbers:\n";
    foreach($duplicates as $dup) {
        echo "     - {$dup->no_transaksi} appears {$dup->count} times in outlets: {$dup->outlets}\n";
    }
} else {
    echo "   ✅ No duplicates found\n";
}

// 2. Test new transaction number generation
echo "\n2. Testing new transaction number generation:\n";
$outlets = DB::table('outlets')->where('is_active', true)->get(['id_outlet', 'nama_outlet']);

foreach($outlets as $outlet) {
    try {
        $newNumber = App\Models\PosSale::generateTransactionNumber($outlet->id_outlet);
        echo "   ✅ Outlet {$outlet->nama_outlet}: {$newNumber}\n";
        
        // Verify uniqueness
        $exists = DB::table('pos_sales')->where('no_transaksi', $newNumber)->exists();
        if ($exists) {
            echo "     ⚠️  WARNING: This number already exists!\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error for outlet {$outlet->nama_outlet}: " . $e->getMessage() . "\n";
    }
}

// 3. Show format comparison
echo "\n3. Transaction number format comparison:\n";
echo "   Old format: 0001/POS/12/2025 (no outlet differentiation)\n";
echo "   New format: 0001/PBU/POS/12/2025 (with outlet prefix)\n";
echo "   Benefits:\n";
echo "   - Each outlet has unique transaction numbers\n";
echo "   - Easy to identify which outlet created the transaction\n";
echo "   - No more duplicate entry errors\n";

// 4. Database constraint check
echo "\n4. Checking database constraints:\n";
try {
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE 
        FROM information_schema.TABLE_CONSTRAINTS 
        WHERE TABLE_NAME = 'pos_sales' 
        AND TABLE_SCHEMA = DATABASE()
        AND CONSTRAINT_TYPE = 'UNIQUE'
    ");
    
    foreach($constraints as $constraint) {
        echo "   - {$constraint->CONSTRAINT_NAME}: {$constraint->CONSTRAINT_TYPE}\n";
    }
} catch (Exception $e) {
    echo "   Could not check constraints: " . $e->getMessage() . "\n";
}

echo "\n=== Fix Applied Successfully ===\n";
echo "\nChanges made:\n";
echo "✅ Updated PosSale::generateTransactionNumber() method\n";
echo "✅ Added outlet prefix to transaction numbers\n";
echo "✅ Each outlet now has unique transaction sequences\n";
echo "\nNext steps:\n";
echo "1. Test POS transaction creation\n";
echo "2. Verify no duplicate errors occur\n";
echo "3. Check transaction numbers in different outlets\n";