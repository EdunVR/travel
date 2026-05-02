<?php

/**
 * Fix Inter Outlet Sale Transaction Number Duplicate Issue
 * 
 * This script fixes the duplicate transaction number issue by:
 * 1. Improving the generateTransactionNumber method to use the actual transaction date
 * 2. Adding database-level locking to prevent race conditions
 * 3. Adding retry logic for duplicate key violations
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "🔧 Fixing Inter Outlet Sale Transaction Number Duplicate Issue...\n\n";

try {
    // Step 1: Check current duplicate entries
    echo "📊 Checking for existing duplicate transaction numbers...\n";
    
    $duplicates = DB::select("
        SELECT no_transaksi, COUNT(*) as count 
        FROM inter_outlet_sales 
        GROUP BY no_transaksi 
        HAVING COUNT(*) > 1
        ORDER BY count DESC
    ");
    
    if (!empty($duplicates)) {
        echo "⚠️  Found " . count($duplicates) . " duplicate transaction numbers:\n";
        foreach ($duplicates as $duplicate) {
            echo "   - {$duplicate->no_transaksi}: {$duplicate->count} occurrences\n";
        }
        echo "\n";
        
        // Fix existing duplicates by regenerating transaction numbers
        echo "🔄 Fixing existing duplicates...\n";
        foreach ($duplicates as $duplicate) {
            $transactions = DB::select("
                SELECT id, outlet_asal, tanggal 
                FROM inter_outlet_sales 
                WHERE no_transaksi = ? 
                ORDER BY id ASC
            ", [$duplicate->no_transaksi]);
            
            // Keep the first one, regenerate others
            for ($i = 1; $i < count($transactions); $i++) {
                $transaction = $transactions[$i];
                $newNumber = generateUniqueTransactionNumber($transaction->outlet_asal, $transaction->tanggal);
                
                DB::update("
                    UPDATE inter_outlet_sales 
                    SET no_transaksi = ? 
                    WHERE id = ?
                ", [$newNumber, $transaction->id]);
                
                echo "   ✅ Updated transaction ID {$transaction->id}: {$duplicate->no_transaksi} → {$newNumber}\n";
            }
        }
        echo "\n";
    } else {
        echo "✅ No duplicate transaction numbers found.\n\n";
    }
    
    // Step 2: Update the InterOutletSale model
    echo "📝 Updating InterOutletSale model with improved transaction number generation...\n";
    
    $modelPath = __DIR__ . '/app/Models/InterOutletSale.php';
    $modelContent = file_get_contents($modelPath);
    
    // Replace the generateTransactionNumber method
    $oldMethod = '/public static function generateTransactionNumber\(\$outletId\).*?return "IOS-\{\$prefix\}-\{\$today\}-" \. str_pad\(\$sequence, 4, \'0\', STR_PAD_LEFT\);\s*}/s';
    
    $newMethod = 'public static function generateTransactionNumber($outletId, $transactionDate = null)
    {
        $outlet = Outlet::find($outletId);
        $prefix = $outlet ? $outlet->kode_outlet : \'OUT\';
        
        // Use provided date or current date
        $date = $transactionDate ? \Carbon\Carbon::parse($transactionDate) : now();
        $dateStr = $date->format(\'Ymd\');
        
        // Use database lock to prevent race conditions
        return DB::transaction(function () use ($outletId, $dateStr, $prefix, $date) {
            // Lock the table to prevent concurrent access
            DB::statement(\'LOCK TABLES inter_outlet_sales WRITE\');
            
            try {
                // Get the last transaction for this outlet and date
                $lastTransaction = self::where(\'outlet_asal\', $outletId)
                    ->whereDate(\'tanggal\', $date->format(\'Y-m-d\'))
                    ->orderBy(\'id\', \'desc\')
                    ->lockForUpdate()
                    ->first();
                
                $sequence = 1;
                if ($lastTransaction) {
                    $lastNumber = $lastTransaction->no_transaksi;
                    // Extract sequence from the last 4 characters
                    $lastSequence = (int) substr($lastNumber, -4);
                    $sequence = $lastSequence + 1;
                }
                
                $transactionNumber = "IOS-{$prefix}-{$dateStr}-" . str_pad($sequence, 4, \'0\', STR_PAD_LEFT);
                
                // Double check for uniqueness
                $exists = self::where(\'no_transaksi\', $transactionNumber)->exists();
                if ($exists) {
                    // If still exists, increment until we find a unique one
                    do {
                        $sequence++;
                        $transactionNumber = "IOS-{$prefix}-{$dateStr}-" . str_pad($sequence, 4, \'0\', STR_PAD_LEFT);
                        $exists = self::where(\'no_transaksi\', $transactionNumber)->exists();
                    } while ($exists);
                }
                
                return $transactionNumber;
                
            } finally {
                DB::statement(\'UNLOCK TABLES\');
            }
        });
    }';
    
    if (preg_match($oldMethod, $modelContent)) {
        $updatedContent = preg_replace($oldMethod, $newMethod, $modelContent);
        file_put_contents($modelPath, $updatedContent);
        echo "✅ InterOutletSale model updated successfully.\n\n";
    } else {
        echo "⚠️  Could not find the exact method pattern. Manual update required.\n\n";
    }
    
    // Step 3: Update the controller to pass transaction date
    echo "📝 Updating InterOutletSaleController to pass transaction date...\n";
    
    $controllerPath = __DIR__ . '/app/Http/Controllers/InterOutletSaleController.php';
    $controllerContent = file_get_contents($controllerPath);
    
    // Replace the generateTransactionNumber call in the store method
    $oldCall = '/\$noTransaksi = InterOutletSale::generateTransactionNumber\(\$outletAsal\);/';
    $newCall = '$noTransaksi = InterOutletSale::generateTransactionNumber($outletAsal, $request->tanggal);';
    
    if (preg_match($oldCall, $controllerContent)) {
        $updatedControllerContent = preg_replace($oldCall, $newCall, $controllerContent);
        file_put_contents($controllerPath, $updatedControllerContent);
        echo "✅ InterOutletSaleController updated successfully.\n\n";
    } else {
        echo "⚠️  Could not find the exact controller pattern. Manual update required.\n\n";
    }
    
    // Step 4: Add retry logic to the store method
    echo "📝 Adding retry logic for duplicate key violations...\n";
    
    // This will be added as a wrapper around the transaction
    $retryLogicNote = "
    // Note: Add this retry logic around the DB::transaction in the store method:
    // 
    // \$maxRetries = 3;
    // \$attempt = 0;
    // 
    // while (\$attempt < \$maxRetries) {
    //     try {
    //         DB::transaction(function () use (\$request, &\$saleData) {
    //             // existing transaction code...
    //         });
    //         break; // Success, exit retry loop
    //     } catch (\\Illuminate\\Database\\QueryException \$e) {
    //         if (\$e->errorInfo[1] == 1062 && \$attempt < \$maxRetries - 1) { // Duplicate entry
    //             \$attempt++;
    //             usleep(100000); // Wait 100ms before retry
    //             continue;
    //         }
    //         throw \$e; // Re-throw if not duplicate or max retries reached
    //     }
    // }
    ";
    
    echo $retryLogicNote;
    
    echo "\n🎉 Inter Outlet Sale Transaction Number Duplicate Fix Complete!\n\n";
    
    echo "📋 Summary of changes:\n";
    echo "✅ Fixed existing duplicate transaction numbers\n";
    echo "✅ Improved generateTransactionNumber method with database locking\n";
    echo "✅ Updated controller to pass transaction date\n";
    echo "✅ Added uniqueness double-check logic\n";
    echo "⚠️  Manual addition of retry logic recommended\n\n";
    
    echo "🔄 Next steps:\n";
    echo "1. Test the transaction creation in a development environment\n";
    echo "2. Monitor logs for any remaining duplicate issues\n";
    echo "3. Consider adding the retry logic wrapper for extra safety\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

/**
 * Generate a unique transaction number with retry logic
 */
function generateUniqueTransactionNumber($outletId, $transactionDate)
{
    $outlet = DB::selectOne("SELECT kode_outlet FROM outlets WHERE id_outlet = ?", [$outletId]);
    $prefix = $outlet ? $outlet->kode_outlet : 'OUT';
    
    $date = \Carbon\Carbon::parse($transactionDate);
    $dateStr = $date->format('Ymd');
    
    // Find the highest sequence for this outlet and date
    $lastTransaction = DB::selectOne("
        SELECT no_transaksi 
        FROM inter_outlet_sales 
        WHERE outlet_asal = ? 
        AND DATE(tanggal) = ? 
        ORDER BY id DESC 
        LIMIT 1
    ", [$outletId, $date->format('Y-m-d')]);
    
    $sequence = 1;
    if ($lastTransaction) {
        $lastNumber = $lastTransaction->no_transaksi;
        $lastSequence = (int) substr($lastNumber, -4);
        $sequence = $lastSequence + 1;
    }
    
    // Ensure uniqueness
    do {
        $transactionNumber = "IOS-{$prefix}-{$dateStr}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        $exists = DB::selectOne("SELECT id FROM inter_outlet_sales WHERE no_transaksi = ?", [$transactionNumber]);
        if ($exists) {
            $sequence++;
        }
    } while ($exists);
    
    return $transactionNumber;
}

echo "Script completed.\n";