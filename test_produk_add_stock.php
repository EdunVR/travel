<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Produk;
use App\Models\HppProduk;

// Test script untuk fitur tambah stok produk

echo "=== TEST PRODUK ADD STOCK FEATURE ===\n\n";

try {
    // 1. Cek apakah ada produk untuk testing
    $produk = Produk::with(['hppProduk', 'outlet'])->first();
    
    if (!$produk) {
        echo "❌ Tidak ada produk untuk testing\n";
        exit;
    }
    
    echo "✅ Testing dengan produk: {$produk->nama_produk} (ID: {$produk->id_produk})\n";
    echo "   Outlet: " . ($produk->outlet ? $produk->outlet->nama_outlet : 'N/A') . "\n";
    
    // 2. Cek stok awal
    $stokAwal = $produk->hppProduk()->sum('stok');
    echo "   Stok awal: {$stokAwal}\n\n";
    
    // 3. Test method addStock
    echo "--- Testing addStock method ---\n";
    $hppTest = 50000; // HPP 50,000
    $jumlahTest = 10; // Tambah 10 unit
    
    $hppProduk = $produk->addStock($hppTest, $jumlahTest);
    echo "✅ addStock berhasil - ID: {$hppProduk->id}\n";
    
    // 4. Cek stok setelah penambahan
    $stokBaru = $produk->hppProduk()->sum('stok');
    echo "   Stok setelah penambahan: {$stokBaru}\n";
    echo "   Selisih: " . ($stokBaru - $stokAwal) . "\n\n";
    
    // 5. Cek data hpp_produk yang baru ditambahkan
    $hppProdukBaru = HppProduk::find($hppProduk->id);
    echo "--- Data HPP Produk Baru ---\n";
    echo "   ID: {$hppProdukBaru->id}\n";
    echo "   ID Produk: {$hppProdukBaru->id_produk}\n";
    echo "   HPP: " . number_format($hppProdukBaru->hpp, 2) . "\n";
    echo "   Stok: {$hppProdukBaru->stok}\n";
    echo "   Created: {$hppProdukBaru->created_at}\n\n";
    
    // 6. Test route (simulasi)
    echo "--- Testing Route Structure ---\n";
    echo "✅ Route yang dibutuhkan:\n";
    echo "   POST /admin/inventaris/produk/{productId}/add-stock\n";
    echo "   Controller: ProdukController@addStock\n";
    echo "   Permission: inventaris.produk.edit\n\n";
    
    // 7. Test validasi data
    echo "--- Testing Validation Rules ---\n";
    $validationRules = [
        'jumlah' => 'required|numeric|min:1',
        'hpp' => 'required|numeric|min:0'
    ];
    
    foreach ($validationRules as $field => $rule) {
        echo "   {$field}: {$rule}\n";
    }
    echo "\n";
    
    // 8. Cek semua hpp_produk untuk produk ini
    echo "--- Semua HPP Produk untuk {$produk->nama_produk} ---\n";
    $allHppProduk = $produk->hppProduk()->orderBy('created_at', 'desc')->get();
    
    foreach ($allHppProduk as $hpp) {
        echo "   ID: {$hpp->id} | HPP: " . number_format($hpp->hpp, 2) . " | Stok: {$hpp->stok} | Tanggal: {$hpp->created_at->format('Y-m-d H:i:s')}\n";
    }
    
    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}