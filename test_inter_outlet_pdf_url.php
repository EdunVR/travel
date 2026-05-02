<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 TESTING INTER OUTLET PDF URL ISSUE\n";
echo "=====================================\n\n";

try {
    // Check if there are any inter outlet sale transactions
    $transactions = \App\Models\InterOutletSale::orderBy('id', 'desc')->limit(3)->get();
    
    if ($transactions->count() > 0) {
        echo "✅ Found " . $transactions->count() . " inter outlet sale transactions:\n";
        foreach ($transactions as $transaction) {
            echo "   ID: {$transaction->id} | No: {$transaction->no_transaksi} | Status: {$transaction->status}\n";
        }
        
        // Test the first transaction
        $firstTransaction = $transactions->first();
        echo "\n🧪 Testing PDF URL for transaction ID: {$firstTransaction->id}\n";
        
        // Test both possible URLs
        $url1 = "/admin/penjualan/inter-outlet/{$firstTransaction->id}/print";
        $url2 = "/admin/penjualan/inter-outlet-sale/{$firstTransaction->id}/print";
        
        echo "   URL 1: {$url1}\n";
        echo "   URL 2: {$url2}\n";
        
        // Check if the controller method exists
        $controller = new \App\Http\Controllers\InterOutletSaleController(new \App\Services\JournalEntryService());
        if (method_exists($controller, 'print')) {
            echo "   ✅ Controller print method exists\n";
        } else {
            echo "   ❌ Controller print method NOT found\n";
        }
        
        // Check if the view exists
        if (view()->exists('admin.penjualan.inter-outlet.print')) {
            echo "   ✅ Print view exists\n";
        } else {
            echo "   ❌ Print view NOT found\n";
        }
        
        // Test route resolution
        try {
            $route1 = route('admin.penjualan.inter-outlet.print', $firstTransaction->id);
            echo "   ✅ Route 1 resolved: {$route1}\n";
        } catch (Exception $e) {
            echo "   ❌ Route 1 failed: " . $e->getMessage() . "\n";
        }
        
        try {
            $route2 = route('admin.penjualan.inter-outlet-sale.print', $firstTransaction->id);
            echo "   ✅ Route 2 resolved: {$route2}\n";
        } catch (Exception $e) {
            echo "   ❌ Route 2 failed: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ No inter outlet sale transactions found in database\n";
        echo "   Creating a test transaction...\n";
        
        // Create a test transaction
        $outlet1 = \App\Models\Outlet::first();
        $outlet2 = \App\Models\Outlet::skip(1)->first();
        
        if ($outlet1 && $outlet2) {
            $transaction = \App\Models\InterOutletSale::create([
                'no_transaksi' => 'TEST-' . date('YmdHis'),
                'tanggal' => now(),
                'outlet_asal' => $outlet1->id_outlet,
                'outlet_tujuan' => $outlet2->id_outlet,
                'id_user' => 1,
                'subtotal' => 100000,
                'total' => 100000,
                'status' => 'approved'
            ]);
            
            echo "   ✅ Created test transaction ID: {$transaction->id}\n";
            echo "   🧪 Test URL: /admin/penjualan/inter-outlet-sale/{$transaction->id}/print\n";
        } else {
            echo "   ❌ Not enough outlets found to create test transaction\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n📋 SUMMARY:\n";
echo "- JavaScript uses: /admin/penjualan/inter-outlet-sale/{id}/print\n";
echo "- Controller method: InterOutletSaleController@print\n";
echo "- View file: resources/views/admin/penjualan/inter-outlet/print.blade.php\n";
echo "\n";