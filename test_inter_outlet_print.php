<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\InterOutletSale;

echo "=== Testing Inter Outlet Print Functionality ===\n";

try {
    // Check if there are any inter outlet sales
    $sales = InterOutletSale::with(['outletAsal', 'outletTujuan', 'user', 'items.produk'])->get();
    
    echo "Found " . $sales->count() . " inter outlet sales\n";
    
    if ($sales->count() > 0) {
        $sale = $sales->first();
        echo "Testing with sale ID: " . $sale->id . "\n";
        echo "Transaction number: " . $sale->no_transaksi . "\n";
        echo "Date: " . $sale->tanggal . "\n";
        echo "Status: " . $sale->status . "\n";
        echo "Items count: " . $sale->items->count() . "\n";
        
        // Test print URL
        $printUrl = route('admin.penjualan.inter-outlet.print', $sale->id);
        echo "Print URL: " . $printUrl . "\n";
        
        echo "✓ Print functionality should work\n";
    } else {
        echo "No inter outlet sales found. Create a transaction first.\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Testing COA Settings Table ===\n";

try {
    // Test if the table exists
    $settings = DB::table('setting_coa_inter_outlet_sales')->get();
    echo "✓ COA settings table exists\n";
    echo "Found " . $settings->count() . " COA settings\n";
    
} catch (Exception $e) {
    echo "✗ COA settings table error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";