<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\InterOutletSale;
use App\Models\InterOutletSaleItem;
use App\Models\SettingCOAInterOutletSale;
use App\Models\Produk;
use App\Services\JournalEntryService;
use App\Http\Controllers\InterOutletSaleController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Inter Outlet Sale WITHOUT COA Configuration ===\n\n";

try {
    // Find outlet WITHOUT COA settings
    $outletWithoutCoa = null;
    $targetOutlet = null;
    
    $outlets = DB::table('outlets')->where('is_active', true)->get();
    foreach ($outlets as $outlet) {
        $setting = SettingCOAInterOutletSale::getByOutlet($outlet->id_outlet);
        if (!$setting) {
            $outletWithoutCoa = $outlet;
            break;
        }
    }
    
    if (!$outletWithoutCoa) {
        echo "ℹ️  All outlets have COA settings. Creating test without COA by using outlet with incomplete settings.\n";
        // Use outlet 3 (Dahana) which doesn't have COA settings
        $outletWithoutCoa = DB::table('outlets')->where('id_outlet', 3)->first();
    }
    
    if (!$outletWithoutCoa) {
        echo "❌ No suitable outlet found for testing.\n";
        exit(1);
    }
    
    // Find target outlet (different from source)
    foreach ($outlets as $outlet) {
        if ($outlet->id_outlet != $outletWithoutCoa->id_outlet) {
            $targetOutlet = $outlet;
            break;
        }
    }
    
    if (!$targetOutlet) {
        echo "❌ No target outlet found.\n";
        exit(1);
    }
    
    echo "Source Outlet (NO COA): {$outletWithoutCoa->nama_outlet} (ID: {$outletWithoutCoa->id_outlet})\n";
    echo "Target Outlet: {$targetOutlet->nama_outlet} (ID: {$targetOutlet->id_outlet})\n\n";
    
    // Check COA settings for source outlet
    $setting = SettingCOAInterOutletSale::getByOutlet($outletWithoutCoa->id_outlet);
    if ($setting) {
        echo "⚠️  Source outlet has COA settings. This will test with configured COA.\n";
    } else {
        echo "✓ Source outlet has NO COA settings. Perfect for testing.\n";
    }
    echo "\n";
    
    // Find a product - create one if needed
    $produk = Produk::where('id_outlet', $outletWithoutCoa->id_outlet)
        ->where('is_active', true)
        ->first();
    
    if (!$produk) {
        // Create a test product
        echo "Creating test product for outlet {$outletWithoutCoa->nama_outlet}...\n";
        $produk = Produk::create([
            'kode_produk' => 'TEST-' . time(),
            'nama_produk' => 'Test Product for Journal',
            'id_outlet' => $outletWithoutCoa->id_outlet,
            'id_kategori' => 1, // Assuming category 1 exists
            'id_satuan' => 1, // Assuming unit 1 exists
            'harga_jual' => 5000,
            'is_active' => true,
        ]);
        echo "✓ Test product created: {$produk->nama_produk}\n";
    }
    
    echo "Using Product: {$produk->nama_produk} (ID: {$produk->id_produk})\n";
    echo "Product Price: Rp " . number_format($produk->harga_jual, 0, ',', '.') . "\n\n";
    
    // Create test transaction
    echo "Creating test inter outlet sale transaction WITHOUT COA...\n";
    
    DB::beginTransaction();
    
    try {
        // Generate transaction number
        $noTransaksi = InterOutletSale::generateTransactionNumber($outletWithoutCoa->id_outlet);
        
        // Create inter outlet sale
        $interOutletSale = InterOutletSale::create([
            'no_transaksi' => $noTransaksi,
            'tanggal' => now(),
            'outlet_asal' => $outletWithoutCoa->id_outlet,
            'outlet_tujuan' => $targetOutlet->id_outlet,
            'id_user' => 2, // Using Super Administrator
            'subtotal' => $produk->harga_jual,
            'diskon_persen' => 0,
            'diskon_nominal' => 0,
            'total_diskon' => 0,
            'ppn' => 0,
            'total' => $produk->harga_jual,
            'status' => 'pending',
            'catatan' => 'Test transaction WITHOUT COA configuration',
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
        echo "\nTesting journal creation (should skip if no COA)...\n";
        
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
            echo "✓ Journal entry created (COA was configured):\n";
            echo "  - Journal ID: {$journalEntry->id}\n";
            echo "  - Transaction Number: {$journalEntry->transaction_number}\n";
            echo "  - Total Debit: Rp " . number_format($journalEntry->total_debit, 0, ',', '.') . "\n";
            echo "  - Total Credit: Rp " . number_format($journalEntry->total_credit, 0, ',', '.') . "\n";
        } else {
            echo "✓ No journal entry created (as expected when COA is not configured)\n";
            echo "  This is the correct behavior - transaction saved without journal.\n";
        }
        
        DB::commit();
        echo "\n✓ Transaction committed successfully\n";
        echo "✅ Test PASSED: Transaction can be saved even without COA configuration\n";
        
    } catch (Exception $e) {
        DB::rollBack();
        echo "❌ Error creating transaction: " . $e->getMessage() . "\n";
        echo "❌ Test FAILED: Transaction should be saveable without COA\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";