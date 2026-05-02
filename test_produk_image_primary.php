<?php

/**
 * Script untuk testing perbaikan Produk Image Primary Default
 * 
 * Jalankan dengan: php test_produk_image_primary.php
 */

require_once 'vendor/autoload.php';

use App\Models\Produk;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;

echo "=== Testing Produk Image Primary Default Fix ===\n\n";

// Test 1: Cek struktur tabel product_images
echo "Test 1: Cek struktur tabel product_images\n";
try {
    $columns = DB::select("DESCRIBE product_images");
    foreach ($columns as $column) {
        if ($column->Field === 'is_primary') {
            echo "✓ Field is_primary ditemukan\n";
            echo "  - Type: {$column->Type}\n";
            echo "  - Default: {$column->Default}\n";
            break;
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Cek produk yang punya gambar
echo "Test 2: Cek produk dengan gambar\n";
try {
    $produksWithImages = Produk::whereHas('images')->with('images')->take(5)->get();
    
    foreach ($produksWithImages as $produk) {
        echo "Produk: {$produk->nama_produk} (ID: {$produk->id_produk})\n";
        
        $primaryCount = $produk->images->where('is_primary', 1)->count();
        $totalImages = $produk->images->count();
        
        echo "  - Total gambar: {$totalImages}\n";
        echo "  - Primary images: {$primaryCount}\n";
        
        if ($primaryCount === 0 && $totalImages > 0) {
            echo "  ⚠️  WARNING: Produk punya gambar tapi tidak ada yang primary!\n";
        } elseif ($primaryCount > 1) {
            echo "  ⚠️  WARNING: Produk punya lebih dari 1 gambar primary!\n";
        } elseif ($primaryCount === 1) {
            echo "  ✓ OK: Produk punya 1 gambar primary\n";
        }
        
        echo "\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Cek produk tanpa gambar primary
echo "Test 3: Cek produk yang punya gambar tapi tidak ada primary\n";
try {
    $produksWithoutPrimary = DB::select("
        SELECT p.id_produk, p.nama_produk, COUNT(pi.id_image) as total_images
        FROM produk p
        JOIN product_images pi ON p.id_produk = pi.id_produk
        WHERE p.id_produk NOT IN (
            SELECT DISTINCT id_produk 
            FROM product_images 
            WHERE is_primary = 1
        )
        GROUP BY p.id_produk, p.nama_produk
        LIMIT 10
    ");
    
    if (count($produksWithoutPrimary) > 0) {
        echo "⚠️  Ditemukan " . count($produksWithoutPrimary) . " produk yang punya gambar tapi tidak ada primary:\n";
        foreach ($produksWithoutPrimary as $produk) {
            echo "  - {$produk->nama_produk} (ID: {$produk->id_produk}) - {$produk->total_images} gambar\n";
        }
    } else {
        echo "✓ Semua produk yang punya gambar sudah punya primary image\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Cek produk dengan multiple primary
echo "Test 4: Cek produk yang punya lebih dari 1 gambar primary\n";
try {
    $produksMultiplePrimary = DB::select("
        SELECT p.id_produk, p.nama_produk, COUNT(pi.id_image) as primary_count
        FROM produk p
        JOIN product_images pi ON p.id_produk = pi.id_produk
        WHERE pi.is_primary = 1
        GROUP BY p.id_produk, p.nama_produk
        HAVING primary_count > 1
        LIMIT 10
    ");
    
    if (count($produksMultiplePrimary) > 0) {
        echo "⚠️  Ditemukan " . count($produksMultiplePrimary) . " produk yang punya lebih dari 1 primary image:\n";
        foreach ($produksMultiplePrimary as $produk) {
            echo "  - {$produk->nama_produk} (ID: {$produk->id_produk}) - {$produk->primary_count} primary images\n";
        }
    } else {
        echo "✓ Tidak ada produk yang punya lebih dari 1 primary image\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Statistik keseluruhan
echo "Test 5: Statistik keseluruhan\n";
try {
    $stats = DB::select("
        SELECT 
            COUNT(DISTINCT p.id_produk) as total_produk,
            COUNT(DISTINCT CASE WHEN pi.id_image IS NOT NULL THEN p.id_produk END) as produk_dengan_gambar,
            COUNT(DISTINCT CASE WHEN pi.is_primary = 1 THEN p.id_produk END) as produk_dengan_primary,
            COUNT(pi.id_image) as total_gambar,
            COUNT(CASE WHEN pi.is_primary = 1 THEN 1 END) as total_primary_images
        FROM produk p
        LEFT JOIN product_images pi ON p.id_produk = pi.id_produk
    ")[0];
    
    echo "Statistik:\n";
    echo "  - Total produk: {$stats->total_produk}\n";
    echo "  - Produk dengan gambar: {$stats->produk_dengan_gambar}\n";
    echo "  - Produk dengan primary image: {$stats->produk_dengan_primary}\n";
    echo "  - Total gambar: {$stats->total_gambar}\n";
    echo "  - Total primary images: {$stats->total_primary_images}\n";
    
    if ($stats->produk_dengan_gambar == $stats->produk_dengan_primary) {
        echo "  ✓ Semua produk yang punya gambar sudah punya primary image\n";
    } else {
        $missing = $stats->produk_dengan_gambar - $stats->produk_dengan_primary;
        echo "  ⚠️  {$missing} produk punya gambar tapi tidak ada primary image\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Selesai ===\n";

// Saran perbaikan jika ada masalah
echo "\nSaran Perbaikan:\n";
echo "1. Jika ada produk tanpa primary image, jalankan query:\n";
echo "   UPDATE product_images pi1 \n";
echo "   SET is_primary = 1 \n";
echo "   WHERE pi1.id_image = (\n";
echo "       SELECT MIN(pi2.id_image) \n";
echo "       FROM product_images pi2 \n";
echo "       WHERE pi2.id_produk = pi1.id_produk\n";
echo "   ) AND pi1.id_produk NOT IN (\n";
echo "       SELECT DISTINCT id_produk \n";
echo "       FROM product_images \n";
echo "       WHERE is_primary = 1\n";
echo "   );\n\n";

echo "2. Jika ada produk dengan multiple primary, jalankan query:\n";
echo "   UPDATE product_images \n";
echo "   SET is_primary = 0 \n";
echo "   WHERE id_image NOT IN (\n";
echo "       SELECT * FROM (\n";
echo "           SELECT MIN(id_image) \n";
echo "           FROM product_images \n";
echo "           WHERE is_primary = 1 \n";
echo "           GROUP BY id_produk\n";
echo "       ) as temp\n";
echo "   ) AND is_primary = 1;\n";