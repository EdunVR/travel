<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DEBUGGING POS SLOW PERFORMANCE ===\n\n";

$outletId = 1;

// 1. Test raw SQL directly
echo "1. Testing raw SQL query directly...\n";
$start = microtime(true);

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

$rawTime = microtime(true) - $start;
echo "Raw SQL time: " . round($rawTime * 1000, 2) . "ms\n";

// 2. Test array mapping
echo "\n2. Testing array mapping...\n";
$start = microtime(true);

$products = array_map(function($product) {
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

$mapTime = microtime(true) - $start;
echo "Array mapping time: " . round($mapTime * 1000, 2) . "ms\n";

// 3. Test asset() function performance
echo "\n3. Testing asset() function performance...\n";
$start = microtime(true);

for ($i = 0; $i < 100; $i++) {
    $url = asset('storage/test.jpg');
}

$assetTime = microtime(true) - $start;
echo "Asset function time (100 calls): " . round($assetTime * 1000, 2) . "ms\n";

// 4. Test cache service performance
echo "\n4. Testing CacheService performance...\n";
$start = microtime(true);

$cacheKey = "test_performance_key";
$result = \App\Services\CacheService::remember($cacheKey, function() {
    return ['test' => 'data'];
}, 60);

$cacheTime = microtime(true) - $start;
echo "CacheService time: " . round($cacheTime * 1000, 2) . "ms\n";

// 5. Test without CacheService
echo "\n5. Testing without CacheService (direct cache)...\n";
$start = microtime(true);

$directResult = Cache::remember('direct_test_key', 60, function() {
    return ['test' => 'data'];
});

$directCacheTime = microtime(true) - $start;
echo "Direct cache time: " . round($directCacheTime * 1000, 2) . "ms\n";

// 6. Identify bottleneck
echo "\n6. Performance Analysis:\n";
echo "- Raw SQL: " . round($rawTime * 1000, 2) . "ms\n";
echo "- Array mapping: " . round($mapTime * 1000, 2) . "ms\n";
echo "- Asset function: " . round($assetTime * 1000, 2) . "ms\n";
echo "- CacheService: " . round($cacheTime * 1000, 2) . "ms\n";
echo "- Direct cache: " . round($directCacheTime * 1000, 2) . "ms\n";

$totalExpected = ($rawTime + $mapTime + $cacheTime) * 1000;
echo "- Expected total: " . round($totalExpected, 2) . "ms\n";

// 7. Test optimized version without asset() in SQL
echo "\n7. Testing optimized SQL without asset()...\n";
$start = microtime(true);

$optimizedProducts = DB::select("
    SELECT 
        p.id_produk,
        p.kode_produk as sku,
        p.nama_produk as name,
        p.harga_jual as price,
        COALESCE(k.nama_kategori, 'Barang') as category,
        COALESCE(s.nama_satuan, 'pcs') as satuan,
        COALESCE(SUM(hpp.stok), 0) as stock,
        pi.path as image_path
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
", [$outletId]);

$optimizedTime = microtime(true) - $start;
echo "Optimized SQL time: " . round($optimizedTime * 1000, 2) . "ms\n";

echo "\n=== DEBUG COMPLETE ===\n";