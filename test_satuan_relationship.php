<?php

require_once 'vendor/autoload.php';

echo "=== TEST SATUAN RELATIONSHIP ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Check satuan records
    echo "1. Checking satuan records...\n";
    $satuans = DB::select('SELECT id_satuan, nama_satuan FROM satuan LIMIT 5');
    foreach ($satuans as $satuan) {
        echo "  ID: {$satuan->id_satuan}, Name: {$satuan->nama_satuan}\n";
    }
    
    // Check produk with satuan
    echo "\n2. Checking produk with satuan relationship...\n";
    $produk = DB::select('SELECT p.id_produk, p.nama_produk, p.id_satuan, s.nama_satuan 
                          FROM produk p 
                          LEFT JOIN satuan s ON p.id_satuan = s.id_satuan 
                          WHERE p.nama_produk LIKE "%to%" 
                          LIMIT 3');
    foreach ($produk as $p) {
        echo "  Produk: {$p->nama_produk}, Satuan ID: {$p->id_satuan}, Satuan: {$p->nama_satuan}\n";
    }
    
    // Test Eloquent relationship
    echo "\n3. Testing Eloquent relationship...\n";
    $produkModel = App\Models\Produk::where('nama_produk', 'like', '%to%')
        ->with('satuan')
        ->first();
    
    if ($produkModel) {
        echo "  Produk: {$produkModel->nama_produk}\n";
        echo "  Satuan ID: {$produkModel->id_satuan}\n";
        echo "  Satuan Object: " . ($produkModel->satuan ? $produkModel->satuan->nama_satuan : 'null') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";