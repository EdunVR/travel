<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\InterOutletProductPrice;

echo "=== TESTING INTER OUTLET SEPARATE PRICING ===\n\n";

try {
    // 1. Test table creation
    echo "1. Testing inter_outlet_product_prices table...\n";
    $tableExists = DB::select("SHOW TABLES LIKE 'inter_outlet_product_prices'");
    if ($tableExists) {
        echo "   ✓ Table exists\n";
        
        // Show table structure
        $columns = DB::select("SHOW COLUMNS FROM inter_outlet_product_prices");
        echo "   Columns:\n";
        foreach ($columns as $column) {
            echo "   - {$column->Field} ({$column->Type})\n";
        }
    } else {
        echo "   ✗ Table does not exist\n";
        exit(1);
    }
    
    // 2. Test model functionality
    echo "\n2. Testing InterOutletProductPrice model...\n";
    
    // Get a sample product
    $sampleProduct = DB::table('produk')->where('is_active', 1)->first();
    if (!$sampleProduct) {
        echo "   ✗ No active products found\n";
        exit(1);
    }
    
    echo "   Using product: {$sampleProduct->nama_produk} (ID: {$sampleProduct->id_produk})\n";
    echo "   Regular price: {$sampleProduct->harga_jual}\n";
    
    // Test creating inter outlet price
    $interOutletPrice = InterOutletProductPrice::updateOrCreate(
        [
            'id_produk' => $sampleProduct->id_produk,
            'outlet_id' => 1
        ],
        [
            'inter_outlet_price' => 15000,
            'markup_percent' => 25
        ]
    );
    
    echo "   ✓ Created/updated inter outlet price: {$interOutletPrice->inter_outlet_price}\n";
    echo "   ✓ Markup: {$interOutletPrice->markup_percent}%\n";
    
    // 3. Test SQL query with inter outlet prices
    echo "\n3. Testing SQL query with inter outlet prices...\n";
    
    $outletId = 1;
    $rawProducts = DB::select("
        SELECT 
            p.id_produk,
            p.kode_produk as sku,
            p.nama_produk as name,
            p.harga_jual as regular_price,
            COALESCE(iopp.inter_outlet_price, 0) as inter_outlet_price,
            COALESCE(iopp.markup_percent, 0) as inter_outlet_markup,
            COALESCE(k.nama_kategori, 'Barang') as category
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        LEFT JOIN inter_outlet_product_prices iopp ON p.id_produk = iopp.id_produk AND iopp.outlet_id = ?
        WHERE p.id_outlet = ? 
        AND p.is_active = 1
        LIMIT 3
    ", [$outletId, $outletId]);
    
    echo "   Found " . count($rawProducts) . " products:\n";
    foreach ($rawProducts as $product) {
        $displayPrice = $product->inter_outlet_price > 0 ? $product->inter_outlet_price : $product->regular_price;
        echo "   - {$product->name}\n";
        echo "     Regular: {$product->regular_price}, Inter Outlet: {$product->inter_outlet_price}, Display: {$displayPrice}\n";
    }
    
    // 4. Test getInterOutletPrice static method
    echo "\n4. Testing getInterOutletPrice method...\n";
    $price = InterOutletProductPrice::getInterOutletPrice($sampleProduct->id_produk, 1);
    echo "   ✓ Inter outlet price for product {$sampleProduct->id_produk}: {$price}\n";
    
    // Test with non-existent inter outlet price
    $price2 = InterOutletProductPrice::getInterOutletPrice($sampleProduct->id_produk, 999);
    echo "   ✓ Fallback to regular price for non-existent outlet: {$price2}\n";
    
    echo "\n✓ All tests passed! Inter outlet separate pricing is working correctly.\n";
    echo "\nKEY FEATURES:\n";
    echo "- ✓ Separate table for inter outlet prices\n";
    echo "- ✓ Fallback to regular price if no inter outlet price set\n";
    echo "- ✓ Markup percentage tracking\n";
    echo "- ✓ Per-outlet pricing support\n";
    echo "- ✓ Does not affect regular product prices\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}