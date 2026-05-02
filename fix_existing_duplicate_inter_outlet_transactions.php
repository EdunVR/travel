<?php

/**
 * Fix Existing Duplicate Inter Outlet Sale Transaction Numbers
 * 
 * This script identifies and fixes existing duplicate transaction numbers
 * by regenerating unique numbers for duplicates.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

echo "🔧 Fixing Existing Duplicate Inter Outlet Sale Transaction Numbers...\n\n";

try {
    // Step 1: Find all duplicate transaction numbers
    echo "📊 Searching for duplicate transaction numbers...\n";
    
    $duplicates = DB::select("
        SELECT no_transaksi, COUNT(*) as count 
        FROM inter_outlet_sales 
        GROUP BY no_transaksi 
        HAVING COUNT(*) > 1
        ORDER BY count DESC, no_transaksi
    ");
    
    if (empty($duplicates)) {
        echo "✅ No duplicate transaction numbers found. System is clean!\n";
        exit(0);
    }
    
    echo "⚠️  Found " . count($duplicates) . " sets of duplicate transaction numbers:\n";
    foreach ($duplicates as $duplicate) {
        echo "   - {$duplicate->no_transaksi}: {$duplicate->count} occurrences\n";
    }
    echo "\n";
    
    // Step 2: Fix each set of duplicates
    echo "🔄 Fixing duplicate transaction numbers...\n";
    $totalFixed = 0;
    
    foreach ($duplicates as $duplicate) {
        echo "\n📝 Processing duplicates for: {$duplicate->no_transaksi}\n";
        
        // Get all transactions with this duplicate number, ordered by ID (oldest first)
        $transactions = DB::select("
            SELECT id, outlet_asal, tanggal, created_at
            FROM inter_outlet_sales 
            WHERE no_transaksi = ? 
            ORDER BY id ASC
        ", [$duplicate->no_transaksi]);
        
        // Keep the first (oldest) transaction unchanged
        echo "   ✅ Keeping original: ID {$transactions[0]->id} (created: {$transactions[0]->created_at})\n";
        
        // Fix the rest by generating new unique transaction numbers
        for ($i = 1; $i < count($transactions); $i++) {
            $transaction = $transactions[$i];
            
            try {
                $newNumber = generateUniqueTransactionNumber(
                    $transaction->outlet_asal, 
                    $transaction->tanggal
                );
                
                // Update the transaction with the new number
                DB::update("
                    UPDATE inter_outlet_sales 
                    SET no_transaksi = ? 
                    WHERE id = ?
                ", [$newNumber, $transaction->id]);
                
                echo "   🔄 Updated ID {$transaction->id}: {$duplicate->no_transaksi} → {$newNumber}\n";
                $totalFixed++;
                
            } catch (Exception $e) {
                echo "   ❌ Failed to update ID {$transaction->id}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n🎉 Duplicate fix completed!\n";
    echo "📊 Summary:\n";
    echo "   - Duplicate sets found: " . count($duplicates) . "\n";
    echo "   - Transactions fixed: {$totalFixed}\n";
    
    // Step 3: Verify no duplicates remain
    echo "\n🔍 Verifying fix...\n";
    $remainingDuplicates = DB::select("
        SELECT no_transaksi, COUNT(*) as count 
        FROM inter_outlet_sales 
        GROUP BY no_transaksi 
        HAVING COUNT(*) > 1
    ");
    
    if (empty($remainingDuplicates)) {
        echo "✅ Verification successful! No duplicate transaction numbers remain.\n";
    } else {
        echo "⚠️  Warning: " . count($remainingDuplicates) . " duplicate sets still exist:\n";
        foreach ($remainingDuplicates as $remaining) {
            echo "   - {$remaining->no_transaksi}: {$remaining->count} occurrences\n";
        }
    }
    
    echo "\n✅ Script completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

/**
 * Generate a unique transaction number for the given outlet and date
 */
function generateUniqueTransactionNumber($outletId, $transactionDate)
{
    // Get outlet code
    $outlet = DB::selectOne("SELECT kode_outlet FROM outlets WHERE id_outlet = ?", [$outletId]);
    $prefix = $outlet ? $outlet->kode_outlet : 'OUT';
    
    // Parse the transaction date
    $date = Carbon::parse($transactionDate);
    $dateStr = $date->format('Ymd');
    
    // Find the highest sequence number for this outlet and date
    $lastTransaction = DB::selectOne("
        SELECT no_transaksi 
        FROM inter_outlet_sales 
        WHERE outlet_asal = ? 
        AND DATE(tanggal) = ? 
        AND no_transaksi LIKE ?
        ORDER BY no_transaksi DESC 
        LIMIT 1
    ", [$outletId, $date->format('Y-m-d'), "IOS-{$prefix}-{$dateStr}-%"]);
    
    $sequence = 1;
    if ($lastTransaction) {
        $lastNumber = $lastTransaction->no_transaksi;
        // Extract sequence from the last 4 characters
        $lastSequence = (int) substr($lastNumber, -4);
        $sequence = $lastSequence + 1;
    }
    
    // Generate unique transaction number
    $maxAttempts = 100;
    $attempt = 0;
    
    do {
        $transactionNumber = "IOS-{$prefix}-{$dateStr}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        // Check if this number already exists
        $exists = DB::selectOne("
            SELECT id FROM inter_outlet_sales 
            WHERE no_transaksi = ?
        ", [$transactionNumber]);
        
        if (!$exists) {
            return $transactionNumber;
        }
        
        $sequence++;
        $attempt++;
        
    } while ($attempt < $maxAttempts);
    
    throw new Exception("Unable to generate unique transaction number after {$maxAttempts} attempts for outlet {$outletId} on {$transactionDate}");
}

echo "\nScript execution completed.\n";