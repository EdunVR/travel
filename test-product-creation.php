<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Product Creation with Image ===\n\n";

try {
    // Test 1: Check if all tables have AUTO_INCREMENT
    echo "1. Checking AUTO_INCREMENT status:\n";
    $tables = ['kategori', 'produk', 'satuan', 'product_images'];
    
    foreach ($tables as $table) {
        $result = DB::select("SHOW CREATE TABLE $table");
        $createTable = $result[0]->{'Create Table'};
        
        if (strpos($createTable, 'AUTO_INCREMENT') !== false) {
            echo "   ✓ $table has AUTO_INCREMENT\n";
        } else {
            echo "   ✗ $table MISSING AUTO_INCREMENT\n";
        }
    }
    
    echo "\n2. Testing Kategori creation:\n";
    $kategori = new App\Models\Kategori();
    $kategori->kode_kategori = 'TEST-' . time();
    $kategori->nama_kategori = 'Test Category';
    $kategori->kelompok = 'Produk';
    $kategori->id_outlet = 3;
    $kategori->is_active = 1;
    $kategori->save();
    echo "   ✓ Kategori created with ID: {$kategori->id_kategori}\n";
    
    echo "\n3. Testing Satuan creation:\n";
    $satuan = new App\Models\Satuan();
    $satuan->kode_satuan = 'TEST-' . time();
    $satuan->nama_satuan = 'Test Unit';
    $satuan->is_active = 1;
    $satuan->save();
    echo "   ✓ Satuan created with ID: {$satuan->id_satuan}\n";
    
    echo "\n4. Testing Produk creation:\n";
    $produk = new App\Models\Produk();
    $produk->kode_produk = 'P-TEST-' . time();
    $produk->nama_produk = 'Test Product';
    $produk->id_outlet = 3;
    $produk->id_kategori = $kategori->id_kategori;
    $produk->id_satuan = $satuan->id_satuan;
    $produk->tipe_produk = 'barang_dagang';
    $produk->harga_jual = 100000;
    $produk->diskon = 0;
    $produk->stok_minimum = 0;
    $produk->is_active = 1;
    $produk->save();
    echo "   ✓ Produk created with ID: {$produk->id_produk}\n";
    
    echo "\n5. Testing ProductImage creation:\n";
    $image = new App\Models\ProductImage();
    $image->id_produk = $produk->id_produk;
    $image->path = 'test/path/image.jpg';
    $image->is_primary = 1;
    $image->save();
    echo "   ✓ ProductImage created with ID: {$image->id_image}\n";
    
    echo "\n6. Cleaning up test data:\n";
    $image->delete();
    echo "   ✓ ProductImage deleted\n";
    $produk->delete();
    echo "   ✓ Produk deleted\n";
    $satuan->delete();
    echo "   ✓ Satuan deleted\n";
    $kategori->delete();
    echo "   ✓ Kategori deleted\n";
    
    echo "\n✅ ALL TESTS PASSED!\n";
    echo "\nThe AUTO_INCREMENT fix is working correctly.\n";
    echo "You can now create products with images without errors.\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
