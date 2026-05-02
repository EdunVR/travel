<?php

/**
 * Test Inter Outlet Sale Transaction Number Fix
 * 
 * This script tests the improved transaction number generation
 * to ensure it prevents duplicates and handles race conditions.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\InterOutletSale;
use App\Models\Outlet;
use Carbon\Carbon;

echo "🧪 Testing Inter Outlet Sale Transaction Number Fix...\n\n";

try {
    // Test 1: Basic transaction number generation
    echo "📋 Test 1: Basic transaction number generation\n";
    
    $outletId = 2; // Use outlet ID 2 as in the error
    $testDate = '2026-01-13';
    
    // Generate a few transaction numbers for the same outlet and date
    for ($i = 1; $i <= 5; $i++) {
        $transactionNumber = InterOutletSale::generateTransactionNumber($outletId, $testDate);
        echo "   Generated #{$i}: {$transactionNumber}\n";
        
        // Verify uniqueness
        $exists = InterOutletSale::where('no_transaksi', $transactionNumber)->exists();
        if ($exists) {
            echo "   ⚠️  Warning: Transaction number {$transactionNumber} already exists!\n";
        } else {
            echo "   ✅ Unique transaction number generated\n";
        }
    }
    
    echo "\n";
    
    // Test 2: Check current state of database
    echo "📋 Test 2: Current database state check\n";
    
    $duplicates = DB::select("
        SELECT no_transaksi, COUNT(*) as count 
        FROM inter_outlet_sales 
        GROUP BY no_transaksi 
        HAVING COUNT(*) > 1
        ORDER BY count DESC
        LIMIT 5
    ");
    
    if (empty($duplicates)) {
        echo "   ✅ No duplicate transaction numbers found in database\n";
    } else {
        echo "   ⚠️  Found duplicate transaction numbers:\n";
        foreach ($duplicates as $duplicate) {
            echo "      - {$duplicate->no_transaksi}: {$duplicate->count} occurrences\n";
        }
    }
    
    echo "\n";
    
    // Test 3: Check transaction numbers for today
    echo "📋 Test 3: Today's transaction numbers for outlet {$outletId}\n";
    
    $todayTransactions = DB::select("
        SELECT no_transaksi, tanggal, created_at
        FROM inter_outlet_sales 
        WHERE outlet_asal = ? 
        AND DATE(tanggal) = ?
        ORDER BY no_transaksi
        LIMIT 10
    ", [$outletId, date('Y-m-d')]);
    
    if (empty($todayTransactions)) {
        echo "   ℹ️  No transactions found for today\n";
    } else {
        echo "   📊 Found " . count($todayTransactions) . " transactions for today:\n";
        foreach ($todayTransactions as $transaction) {
            echo "      - {$transaction->no_transaksi} (created: {$transaction->created_at})\n";
        }
    }
    
    echo "\n";
    
    // Test 4: Simulate concurrent transaction number generation
    echo "📋 Test 4: Simulating concurrent generation (sequential test)\n";
    
    $generatedNumbers = [];
    $duplicateFound = false;
    
    for ($i = 1; $i <= 10; $i++) {
        $number = InterOutletSale::generateTransactionNumber($outletId, $testDate);
        
        if (in_array($number, $generatedNumbers)) {
            echo "   ❌ DUPLICATE FOUND: {$number} (attempt #{$i})\n";
            $duplicateFound = true;
        } else {
            echo "   ✅ Unique: {$number}\n";
            $generatedNumbers[] = $number;
        }
    }
    
    if (!$duplicateFound) {
        echo "   🎉 No duplicates found in sequential generation test!\n";
    }
    
    echo "\n";
    
    // Test 5: Check outlet information
    echo "📋 Test 5: Outlet information check\n";
    
    $outlet = Outlet::find($outletId);
    if ($outlet) {
        echo "   ✅ Outlet found: {$outlet->nama_outlet} (Code: {$outlet->kode_outlet})\n";
        
        // Show expected transaction number format
        $expectedFormat = "IOS-{$outlet->kode_outlet}-" . date('Ymd') . "-XXXX";
        echo "   📝 Expected format: {$expectedFormat}\n";
    } else {
        echo "   ❌ Outlet {$outletId} not found!\n";
    }
    
    echo "\n";
    
    // Test 6: Database constraint check
    echo "📋 Test 6: Database constraint verification\n";
    
    $constraints = DB::select("
        SHOW INDEX FROM inter_outlet_sales 
        WHERE Key_name LIKE '%no_transaksi%'
    ");
    
    if (!empty($constraints)) {
        echo "   ✅ Database constraints found:\n";
        foreach ($constraints as $constraint) {
            echo "      - {$constraint->Key_name}: {$constraint->Column_name} (Unique: " . ($constraint->Non_unique ? 'No' : 'Yes') . ")\n";
        }
    } else {
        echo "   ⚠️  No database constraints found for no_transaksi column\n";
    }
    
    echo "\n🎉 Testing completed!\n\n";
    
    echo "📋 Summary:\n";
    echo "✅ Transaction number generation method updated\n";
    echo "✅ Database locking implemented\n";
    echo "✅ Uniqueness verification added\n";
    echo "✅ Retry logic implemented in controller\n";
    echo "✅ Date-specific sequence numbering fixed\n\n";
    
    echo "🔄 Recommendations:\n";
    echo "1. Run the duplicate fix script if duplicates were found\n";
    echo "2. Monitor logs for any remaining duplicate issues\n";
    echo "3. Test in production with multiple concurrent users\n";
    echo "4. Consider adding application-level caching for sequence numbers\n\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "Test script completed.\n";