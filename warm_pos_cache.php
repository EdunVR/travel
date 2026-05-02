<?php
// Cache warming untuk POS products
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$outlets = [1, 2, 3]; // Sesuaikan dengan outlet yang ada

foreach ($outlets as $outletId) {
    $cacheKey = "pos_products_optimized_outlet_{$outletId}";
    
    $products = \App\Services\CacheService::remember($cacheKey, function() use ($outletId) {
        $rawProducts = DB::select("
            SELECT 
                p.id_produk,
                p.kode_produk as sku,
                p.nama_produk as name,
                p.harga_jual as price,
                COALESCE(k.nama_kategori, 'Barang') as category,
                COALESCE(s.nama_satuan, 'pcs') as satuan,
                COALESCE(SUM(hpp.stok), 0) as stock,
                CASE 
                    WHEN pi.path IS NOT NULL THEN CONCAT(?, pi.path)
                    ELSE NULL 
                END as image
            FROM produk p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
            LEFT JOIN hpp_produk hpp ON p.id_produk = hpp.id_produk AND hpp.stok > 0
            LEFT JOIN product_images pi ON p.id_produk = pi.id_produk AND pi.is_primary = 1
            WHERE p.id_outlet = ? 
            AND p.is_active = 1
            GROUP BY p.id_produk, p.kode_produk, p.nama_produk, p.harga_jual, 
                     k.nama_kategori, s.nama_satuan, pi.path
            HAVING stock > 0
            ORDER BY p.nama_produk
        ", [asset('storage/'), $outletId]);
        
        return array_map(function($product) {
            return [
                'id_produk' => (int) $product->id_produk,
                'sku' => $product->sku,
                'name' => $product->name,
                'category' => $product->category,
                'price' => (float) $product->price,
                'stock' => (float) $product->stock,
                'satuan' => $product->satuan,
                'image' => $product->image,
            ];
        }, $rawProducts);
    }, 600);
    
    echo "Warmed cache for outlet {$outletId}: " . count($products) . " products\n";
}

echo "Cache warming complete!\n";
