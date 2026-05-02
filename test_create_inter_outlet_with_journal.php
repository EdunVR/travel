<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InterOutletSale;
use App\Models\InterOutletSaleItem;
use App\Models\SettingCOAInterOutletSale;
use App\Models\Produk;
use App\Services\JournalEntryService;
use App\Http\Controllers\InterOutletSaleController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Inter Outlet Sale with Journal Creation ===\n\n";

try {
    // Find outlets with COA settings
    $outletWithCoa = null;
    $targetOutlet = null;
    
    $outlets = DB::table('outlets')->where('is_active', true)->get();
    foreach ($outlets as $outlet) {
        $setting = SettingCOAInterOutletSale::getByOutlet($outlet->id_outlet);
        if ($setting && $setting->accounting_book_id) {
            $outletWithCoa = $outlet;
            break;
        }
    }
    
    if (!$outletWithCoa) {
        echo "❌ No outlet with COA settings found. Please configure COA settings first.\n";
        exit(1);
    }
    
    // Find target outlet (different from source)
    foreach ($outlets as $outlet) {
        if ($outlet->id_outlet != $outletWithCoa->id_outlet) {
            $targetOutlet = $outlet;
            break;
        }
    }
    
    if (!$targetOutlet) {
        echo "❌ No target outlet found.\n";
        exit(1);
    }
    
    echo "Source Outlet: {$outletWithCoa->nama_outlet} (ID: {$outletWithCoa->id_outlet})\n";
    echo "Target Outlet: {$targetOutlet->nama_outlet} (ID: {$targetOutlet->id_outlet})\n\n";
    
    // Find a product in the source outlet
    $produk = Produk::where('id_outlet', $outletWithCoa->id_outlet)
        ->where('is_active', true)
        ->first();
    
    if (!$produk) {
        echo "❌ No active product found in source outlet.\n";
        exit(1);
    }
    
    echo "Using Product: {$produk->nama_produk} (ID: {$produk->id_produk})\n";
    echo "Product Price: Rp " . number_format($produk->harga_jual, 0, ',', '.') . "\n\n";
    
    // Create test transaction
    echo "Creating test inter outlet sale transaction...\n";
    
    DB::beginTransaction();
    
    try {
        // Generate transaction number
        $noTransaksi = InterOutletSale::generateTransactionNumber($outletWithCoa->id_outlet);
        
        // Create inter outlet sale
        $interOutletSale = InterOutletSale::create([
            'no_transaksi' => $noTransaksi,
            'tanggal' => now(),
            'outlet_asal' => $outletWithCoa->id_outlet,
            'outlet_tujuan' => $targetOutlet->id_outlet,
            'id_user' => 2, // Using Super Administrator
            'subtotal' => $produk->harga_jual,
            'diskon_persen' => 0,
            'diskon_nominal' => 0,
            'total_diskon' => 0,
            'ppn' => 0,
            'total' => $produk->harga_jual,
            'status' => 'pending',
            'catatan' => 'Test transaction for journal creation',
        ]);
        
        echo "✓ Inter outlet sale created: {$noTransaksi} (ID: {$interOutletSale->id})\n";
        
        // Create sale item
        InterOutletSaleItem::create([
            'inter_outlet_sale_id' => $interOutletSale->id,
            'id_produk' => $produk->id_produk,
            'kuantitas' => 1,
            'harga' => $produk->harga_jual,
            'subtotal' => $produk->harga_jual,
        ]);
        
        echo "✓ Sale item created\n";
        
        // Now test journal creation using the controller method
        echo "\nTesting journal creation...\n";
        
        // Create journal service instance
        $journalService = new JournalEntryService();
        
        // Create controller instance with journal service
        $controller = new InterOutletSaleController($journalService);
        
        // Use reflection to call the private method
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('createInterOutletSaleJournal');
        $method->setAccessible(true);
        
        // Call the journal creation method
        $journalEntry = $method->invoke($controller, $interOutletSale);
        
        if ($journalEntry) {
            echo "✓ Journal entry created successfully!\n";
            echo "  - Journal ID: {$journalEntry->id}\n";
            echo "  - Transaction Number: {$journalEntry->transaction_number}\n";
            echo "  - Total Debit: Rp " . number_format($journalEntry->total_debit, 0, ',', '.') . "\n";
            echo "  - Total Credit: Rp " . number_format($journalEntry->total_credit, 0, ',', '.') . "\n";
            echo "  - Status: {$journalEntry->status}\n";
            
            // Show journal details
            $details = DB::table('journal_entry_details')
                ->join('chart_of_accounts', 'journal_entry_details.account_id', '=', 'chart_of_accounts.id')
                ->where('journal_entry_id', $journalEntry->id)
                ->select('chart_of_accounts.code', 'chart_of_accounts.name', 'journal_entry_details.debit', 'journal_entry_details.credit', 'journal_entry_details.description')
                ->get();
            
            echo "\n  Journal Details:\n";
            foreach ($details as $detail) {
                $debit = $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '-';
                $credit = $detail->credit > 0 ? 'Rp ' . number_format($detail->credit, 0, ',', '.') : '-';
                echo "    {$detail->code} - {$detail->name}\n";
                echo "      Debit: {$debit} | Credit: {$credit}\n";
                echo "      Memo: {$detail->description}\n\n";
            }
            
        } else {
            echo "❌ Journal entry was not created\n";
            
            // Check logs for errors
            echo "\nChecking recent logs...\n";
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                $logs = file_get_contents($logFile);
                $recentLogs = array_slice(explode("\n", $logs), -20);
                foreach ($recentLogs as $log) {
                    if (strpos($log, 'inter_outlet') !== false || strpos($log, 'journal') !== false) {
                        echo "  " . $log . "\n";
                    }
                }
            }
        }
        
        DB::commit();
        echo "\n✓ Transaction committed successfully\n";
        
    } catch (Exception $e) {
        DB::rollBack();
        echo "❌ Error creating transaction: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";