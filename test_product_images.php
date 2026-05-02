<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Produk;
use App\Models\ProductImage;

echo "=== Test Product Images ===\n\n";

// Test 1: Cek produk dengan gambar
echo "1. Mencari produk dengan gambar...\n";
$produkDenganGambar = Produk::whereHas('images')->with('images')->first();

if ($produkDenganGambar) {
    echo "   Produk: {$produkDenganGambar->nama_produk} (ID: {$produkDenganGambar->id_produk})\n";
    echo "   Outlet: {$produkDenganGambar->id_outlet}\n";
    echo "   Jumlah gambar: " . $produkDenganGambar->images->count() . "\n";
    
    foreach ($produkDenganGambar->images as $img) {
        echo "   - Path: {$img->path}, Primary: " . ($img->is_primary ? 'Ya' : 'Tidak') . "\n";
    }
} else {
    echo "   Tidak ada produk dengan gambar\n";
}

echo "\n";

// Test 2: Cek struktur tabel product_images
echo "2. Struktur tabel product_images:\n";
try {
    $columns = DB::select("DESCRIBE product_images");
    foreach ($columns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Cek total gambar
echo "3. Total gambar di database:\n";
$totalImages = ProductImage::count();
echo "   Total: {$totalImages} gambar\n";

echo "\n";

// Test 4: Cek produk tanpa gambar
echo "4. Produk tanpa gambar:\n";
$produkTanpaGambar = Produk::doesntHave('images')->count();
echo "   Total: {$produkTanpaGambar} produk\n";

echo "\n=== Test Selesai ===\n";
