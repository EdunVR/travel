<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TESTING PRICE PRODUCTS FIX ===\n\n";

try {
    // Test the fixed SQL query
    echo "1. Testing fixed SQL query...\n";
    
    $outletId = 1;
    $rawProducts = DB::select("
        SELECT 
            p.id_produk,
            p.kode_produk as sku,
            p.nama_produk as name,
            p.harga_jual as price,
            COALESCE(k.nama_kategori, 'Barang') as category,
            COALESCE(s.nama_satuan, 'pcs') as satuan,
            COALESCE(
                (SELECT AVG(hpp.hpp) FROM hpp_produk hpp WHERE hpp.id_produk = p.id_produk), 
                0
            ) as hpp,
            COALESCE(p.markup_percent, 0) as markup_percent,
            COALESCE(
                (SELECT pi.path FROM product_images pi 
                 INNER JOIN produk p2 ON pi.id_produk = p2.id_produk 
                 WHERE p2.kode_produk = p.kode_produk AND pi.is_primary = 1 
                 LIMIT 1),
                (SELECT pi.path FROM product_images pi WHERE pi.id_produk = p.id_produk LIMIT 1)
            ) as image_path
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
        LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
        WHERE p.id_outlet = ? 
        AND p.is_active = 1
        ORDER BY p.nama_produk
        LIMIT 3
    ", [$outletId]);
    
    echo "   ✓ SQL query executed successfully\n";
    echo "   Found " . count($rawProducts) . " products\n";
    
    if (count($rawProducts) > 0) {
        echo "\n2. Sample product data:\n";
        foreach ($rawProducts as $product) {
            echo "   - {$product->name} (SKU: {$product->sku})\n";
            echo "     Price: {$product->price}, HPP: {$product->hpp}, Markup: {$product->markup_percent}%\n";
        }
    }
    
    echo "\n✓ Price products fix test completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}