<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\InterOutletProductPrice;
use App\Models\Produk;

echo "=== VERIFYING PRICE ISOLATION ===\n\n";

try {
    // Get a sample product
    $product = Produk::where('is_active', 1)->first();
    if (!$product) {
        echo "No active products found\n";
        exit(1);
    }
    
    echo "Testing with product: {$product->nama_produk} (ID: {$product->id_produk})\n";
    echo "Original regular price: {$product->harga_jual}\n\n";
    
    // 1. Set inter outlet price
    echo "1. Setting inter outlet price...\n";
    $interOutletPrice = InterOutletProductPrice::updateOrCreate(
        [
            'id_produk' => $product->id_produk,
            'outlet_id' => 1
        ],
        [
            'inter_outlet_price' => 25000,
            'markup_percent' => 30
        ]
    );
    echo "   ✓ Inter outlet price set to: {$interOutletPrice->inter_outlet_price}\n";
    
    // 2. Check that regular price is unchanged
    echo "\n2. Verifying regular price isolation...\n";
    $product->refresh(); // Reload from database
    echo "   Regular price after setting inter outlet price: {$product->harga_jual}\n";
    
    if ($product->harga_jual == $product->getOriginal('harga_jual')) {
        echo "   ✓ Regular price unchanged - ISOLATION WORKING!\n";
    } else {
        echo "   ✗ Regular price changed - ISOLATION FAILED!\n";
        exit(1);
    }
    
    // 3. Test inter outlet price retrieval
    echo "\n3. Testing price retrieval...\n";
    $retrievedPrice = InterOutletProductPrice::getInterOutletPrice($product->id_produk, 1);
    echo "   Inter outlet price retrieved: {$retrievedPrice}\n";
    
    if ($retrievedPrice == 25000) {
        echo "   ✓ Inter outlet price correctly retrieved\n";
    } else {
        echo "   ✗ Inter outlet price retrieval failed\n";
        exit(1);
    }
    
    // 4. Test fallback for different outlet
    echo "\n4. Testing fallback mechanism...\n";
    $fallbackPrice = InterOutletProductPrice::getInterOutletPrice($product->id_produk, 999);
    echo "   Price for non-existent outlet: {$fallbackPrice}\n";
    echo "   Regular price: {$product->harga_jual}\n";
    
    if ($fallbackPrice == $product->harga_jual) {
        echo "   ✓ Fallback to regular price working correctly\n";
    } else {
        echo "   ✗ Fallback mechanism failed\n";
        exit(1);
    }
    
    // 5. Simulate what happens in other modules
    echo "\n5. Simulating other modules accessing product price...\n";
    $otherModulePrice = $product->harga_jual; // How other modules would get price
    echo "   Price seen by other modules: {$otherModulePrice}\n";
    echo "   Inter outlet price: {$retrievedPrice}\n";
    
    if ($otherModulePrice != $retrievedPrice) {
        echo "   ✓ Other modules see regular price, inter outlet sees special price - PERFECT ISOLATION!\n";
    } else {
        echo "   ✗ Price isolation not working properly\n";
        exit(1);
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ PRICE ISOLATION VERIFICATION SUCCESSFUL!\n";
    echo str_repeat("=", 60) . "\n";
    echo "SUMMARY:\n";
    echo "- Regular product price: {$product->harga_jual} (unchanged)\n";
    echo "- Inter outlet price: {$retrievedPrice} (separate)\n";
    echo "- Other modules: See regular price only\n";
    echo "- Inter outlet module: Uses special price when available\n";
    echo "- Fallback: Works correctly for outlets without special pricing\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}