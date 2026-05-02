<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Produk;
use Illuminate\Support\Facades\DB;

echo "=== POS PERFORMANCE ANALYSIS ===\n\n";

$outletId = 1;

// 1. Analyze current query performance
echo "1. Current Query Performance Analysis...\n";

$startTime = microtime(true);

// Test current query structure
$products = Produk::select([
        'id_produk', 'kode_produk', 'nama_produk', 'harga_jual', 
        'id_outlet', 'id_kategori', 'id_satuan', 'is_active'
    ])
    ->with([
        'satuan:id_satuan,nama_satuan',
        'kategori:id_kategori,nama_kategori',
        'hppProduk' => function($query) {
            $query->select('id_produk', 'hpp', 'stok')
                  ->where('stok', '>', 0);
        },
        'primaryImage:id_image,id_produk,path',
        'images' => function($query) {
            $query->select(['id_image', 'id_produk', 'path'])->limit(1);
        }
    ])
    ->where('id_outlet', $outletId)
    ->where('is_active', true)
    ->get();

$queryTime = microtime(true) - $startTime;
echo "Current query time: " . round($queryTime * 1000, 2) . "ms\n";
echo "Products loaded: " . $products->count() . "\n";

// 2. Analyze individual bottlenecks
echo "\n2. Bottleneck Analysis...\n";

// Test without relations
$startTime = microtime(true);
$productsNoRelations = Produk::select([
        'id_produk', 'kode_produk', 'nama_produk', 'harga_jual', 
        'id_outlet', 'id_kategori', 'id_satuan', 'is_active'
    ])
    ->where('id_outlet', $outletId)
    ->where('is_active', true)
    ->get();
$noRelationsTime = microtime(true) - $startTime;
echo "Without relations: " . round($noRelationsTime * 1000, 2) . "ms\n";

// Test with minimal relations
$startTime = microtime(true);
$productsMinimal = Produk::select([
        'id_produk', 'kode_produk', 'nama_produk', 'harga_jual', 
        'id_outlet', 'id_kategori', 'id_satuan', 'is_active'
    ])
    ->with([
        'satuan:id_satuan,nama_satuan',
        'kategori:id_kategori,nama_kategori'
    ])
    ->where('id_outlet', $outletId)
    ->where('is_active', true)
    ->get();
$minimalTime = microtime(true) - $startTime;
echo "With minimal relations: " . round($minimalTime * 1000, 2) . "ms\n";

// 3. Test optimized query with JOIN
echo "\n3. Testing optimized query with JOIN...\n";

$startTime = microtime(true);
$optimizedProducts = DB::table('produk as p')
    ->select([
        'p.id_produk',
        'p.kode_produk', 
        'p.nama_produk',
        'p.harga_jual',
        'p.id_outlet',
        'k.nama_kategori',
        's.nama_satuan',
        DB::raw('COALESCE(SUM(hpp.stok), 0) as total_stok'),
        'pi.path as primary_image'
    ])
    ->leftJoin('kategori as k', 'p.id_kategori', '=', 'k.id_kategori')
    ->leftJoin('satuan as s', 'p.id_satuan', '=', 's.id_satuan')
    ->leftJoin('hpp_produk as hpp', function($join) {
        $join->on('p.id_produk', '=', 'hpp.id_produk')
             ->where('hpp.stok', '>', 0);
    })
    ->leftJoin('product_images as pi', function($join) {
        $join->on('p.id_produk', '=', 'pi.id_produk')
             ->where('pi.is_primary', '=', 1);
    })
    ->where('p.id_outlet', $outletId)
    ->where('p.is_active', true)
    ->groupBy([
        'p.id_produk', 'p.kode_produk', 'p.nama_produk', 'p.harga_jual',
        'p.id_outlet', 'k.nama_kategori', 's.nama_satuan', 'pi.path'
    ])
    ->having('total_stok', '>', 0)
    ->get();

$optimizedTime = microtime(true) - $startTime;
echo "Optimized JOIN query: " . round($optimizedTime * 1000, 2) . "ms\n";
echo "Products found: " . $optimizedProducts->count() . "\n";

// 4. Test with raw SQL for maximum performance
echo "\n4. Testing raw SQL query...\n";

$startTime = microtime(true);
$rawProducts = DB::select("
    SELECT 
        p.id_produk,
        p.kode_produk,
        p.nama_produk,
        p.harga_jual,
        COALESCE(k.nama_kategori, 'Barang') as kategori,
        COALESCE(s.nama_satuan, 'pcs') as satuan,
        COALESCE(SUM(hpp.stok), 0) as stok,
        pi.path as image_path
    FROM produk p
    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
    LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
    LEFT JOIN hpp_produk hpp ON p.id_produk = hpp.id_produk AND hpp.stok > 0
    LEFT JOIN product_images pi ON p.id_produk = pi.id_produk AND pi.is_primary = 1
    WHERE p.id_outlet = ? 
    AND p.is_active = 1
    GROUP BY p.id_produk, p.kode_produk, p.nama_produk, p.harga_jual, k.nama_kategori, s.nama_satuan, pi.path
    HAVING stok > 0
    ORDER BY p.nama_produk
", [$outletId]);

$rawTime = microtime(true) - $startTime;
echo "Raw SQL query: " . round($rawTime * 1000, 2) . "ms\n";
echo "Products found: " . count($rawProducts) . "\n";

// 5. Performance comparison
echo "\n5. Performance Comparison:\n";
echo "Current Eloquent: " . round($queryTime * 1000, 2) . "ms (baseline)\n";
echo "Optimized JOIN: " . round($optimizedTime * 1000, 2) . "ms (" . round((1 - $optimizedTime/$queryTime) * 100, 1) . "% faster)\n";
echo "Raw SQL: " . round($rawTime * 1000, 2) . "ms (" . round((1 - $rawTime/$queryTime) * 100, 1) . "% faster)\n";

// 6. Check database indexes
echo "\n6. Database Index Analysis...\n";
try {
    $indexes = DB::select("SHOW INDEX FROM produk WHERE Key_name != 'PRIMARY'");
    echo "Produk table indexes:\n";
    foreach ($indexes as $index) {
        echo "- {$index->Key_name} on {$index->Column_name}\n";
    }
    
    $hppIndexes = DB::select("SHOW INDEX FROM hpp_produk WHERE Key_name != 'PRIMARY'");
    echo "\nHPP Produk table indexes:\n";
    foreach ($hppIndexes as $index) {
        echo "- {$index->Key_name} on {$index->Column_name}\n";
    }
} catch (Exception $e) {
    echo "Could not analyze indexes: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";