<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING TABLE STRUCTURE ===\n\n";

try {
    // 1. Check productions table
    echo "1. PRODUCTIONS TABLE COLUMNS:\n";
    $productionsColumns = \Schema::getColumnListing('productions');
    foreach ($productionsColumns as $column) {
        echo "   - $column\n";
    }
    
    // 2. Check produk table  
    echo "\n2. PRODUK TABLE COLUMNS:\n";
    $produkColumns = \Schema::getColumnListing('produk');
    foreach ($produkColumns as $column) {
        echo "   - $column\n";
    }
    
    // 3. Check hpp_produk table
    echo "\n3. HPP_PRODUK TABLE COLUMNS:\n";
    $hppColumns = \Schema::getColumnListing('hpp_produk');
    foreach ($hppColumns as $column) {
        echo "   - $column\n";
    }
    
    // 4. Check sample data from productions
    echo "\n4. SAMPLE PRODUCTIONS DATA:\n";
    $sampleProduction = \DB::table('productions')->first();
    if ($sampleProduction) {
        echo "   Available fields: " . implode(', ', array_keys((array)$sampleProduction)) . "\n";
    } else {
        echo "   No data in productions table\n";
    }
    
    // 5. Check sample data from produk
    echo "\n5. SAMPLE PRODUK DATA:\n";
    $sampleProduk = \DB::table('produk')->first();
    if ($sampleProduk) {
        echo "   Available fields: " . implode(', ', array_keys((array)$sampleProduk)) . "\n";
    } else {
        echo "   No data in produk table\n";
    }
    
    // 6. Check sample data from hpp_produk
    echo "\n6. SAMPLE HPP_PRODUK DATA:\n";
    $sampleHpp = \DB::table('hpp_produk')->first();
    if ($sampleHpp) {
        echo "   Available fields: " . implode(', ', array_keys((array)$sampleHpp)) . "\n";
    } else {
        echo "   No data in hpp_produk table\n";
    }
    
    echo "\n=== STRUCTURE CHECK COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}