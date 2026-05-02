<?php

/**
 * Script untuk memperbaiki data produk images yang sudah ada
 * 
 * Jalankan dengan: php artisan tinker
 * Kemudian: include 'fix_existing_produk_images.php';
 */

use App\Models\Produk;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;

echo "=== Memperbaiki Data Produk Images yang Sudah Ada ===\n\n";

// Step 1: Set primary image untuk produk yang punya gambar tapi tidak ada primary
echo "Step 1: Set primary image untuk produk tanpa primary...\n";

try {
    $affectedRows = DB::statement("
        UPDATE product_images pi1 
        SET is_primary = 1 
        WHERE pi1.id_image = (
            SELECT MIN(pi2.id_image) 
            FROM (SELECT * FROM product_images) pi2 
            WHERE pi2.id_produk = pi1.id_produk
        ) 
        AND pi1.id_produk NOT IN (
            SELECT DISTINCT id_produk 
            FROM (SELECT * FROM product_images) pi3
            WHERE pi3.is_primary = 1
        )
    ");
    
    echo "✓ Berhasil set primary image untuk produk yang belum punya primary\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Step 2: Fix produk yang punya multiple primary images
echo "Step 2: Fix produk dengan multiple primary images...\n";

try {
    // Reset semua primary kecuali yang pertama untuk setiap produk
    $affectedRows = DB::statement("
        UPDATE product_images 
        SET is_primary = 0 
        WHERE id_image NOT IN (
            SELECT * FROM (
                SELECT MIN(id_image) 
                FROM product_images 
                WHERE is_primary = 1 
                GROUP BY id_produk
            ) as temp
        ) 
        AND is_primary = 1
    ");
    
    echo "✓ Berhasil fix produk dengan multiple primary images\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Step 3: Verifikasi hasil perbaikan
echo "Step 3: Verifikasi hasil perbaikan...\n";

try {
    // Cek produk tanpa primary
    $produksWithoutPrimary = DB::select("
        SELECT COUNT(*) as count
        FROM produk p
        JOIN product_images pi ON p.id_produk = pi.id_produk
        WHERE p.id_produk NOT IN (
            SELECT DISTINCT id_produk 
            FROM product_images 
            WHERE is_primary = 1
        )
    ")[0];
    
    if ($produksWithoutPrimary->count == 0) {
        echo "✓ Semua produk yang punya gambar sudah punya primary image\n";
    } else {
        echo "⚠️  Masih ada {$produksWithoutPrimary->count} produk tanpa primary image\n";
    }
    
    // Cek produk dengan multiple primary
    $produksMultiplePrimary = DB::select("
        SELECT COUNT(*) as count
        FROM (
            SELECT id_produk
            FROM product_images
            WHERE is_primary = 1
            GROUP BY id_produk
            HAVING COUNT(*) > 1
        ) as temp
    ")[0];
    
    if ($produksMultiplePrimary->count == 0) {
        echo "✓ Tidak ada produk dengan multiple primary images\n";
    } else {
        echo "⚠️  Masih ada {$produksMultiplePrimary->count} produk dengan multiple primary images\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Step 4: Statistik akhir
echo "Step 4: Statistik akhir...\n";

try {
    $finalStats = DB::select("
        SELECT 
            COUNT(DISTINCT p.id_produk) as total_produk,
            COUNT(DISTINCT CASE WHEN pi.id_image IS NOT NULL THEN p.id_produk END) as produk_dengan_gambar,
            COUNT(DISTINCT CASE WHEN pi.is_primary = 1 THEN p.id_produk END) as produk_dengan_primary,
            COUNT(pi.id_image) as total_gambar,
            COUNT(CASE WHEN pi.is_primary = 1 THEN 1 END) as total_primary_images
        FROM produk p
        LEFT JOIN product_images pi ON p.id_produk = pi.id_produk
    ")[0];
    
    echo "Statistik Akhir:\n";
    echo "  - Total produk: {$finalStats->total_produk}\n";
    echo "  - Produk dengan gambar: {$finalStats->produk_dengan_gambar}\n";
    echo "  - Produk dengan primary image: {$finalStats->produk_dengan_primary}\n";
    echo "  - Total gambar: {$finalStats->total_gambar}\n";
    echo "  - Total primary images: {$finalStats->total_primary_images}\n";
    
    if ($finalStats->produk_dengan_gambar == $finalStats->produk_dengan_primary && 
        $finalStats->produk_dengan_gambar == $finalStats->total_primary_images) {
        echo "  ✅ PERFECT! Semua produk sudah memiliki tepat 1 primary image\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Perbaikan Data Selesai ===\n";

// Tambahan: Update model cache jika ada
echo "\nMembersihkan cache model...\n";
try {
    if (function_exists('cache')) {
        cache()->flush();
        echo "✓ Cache berhasil dibersihkan\n";
    }
} catch (Exception $e) {
    echo "⚠️  Cache tidak bisa dibersihkan: " . $e->getMessage() . "\n";
}

echo "\n✅ Semua perbaikan selesai! Silakan test upload gambar produk baru.\n";