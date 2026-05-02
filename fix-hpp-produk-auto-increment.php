<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Fixing hpp_produk AUTO_INCREMENT ===\n\n";

try {
    // Check current status
    echo "1. Checking current hpp_produk table structure:\n";
    $result = DB::select("SHOW CREATE TABLE hpp_produk");
    $createTable = $result[0]->{'Create Table'};
    
    if (strpos($createTable, 'AUTO_INCREMENT') !== false) {
        echo "   ✓ hpp_produk already has AUTO_INCREMENT\n";
    } else {
        echo "   ✗ hpp_produk MISSING AUTO_INCREMENT and PRIMARY KEY\n";
        
        echo "\n2. Adding PRIMARY KEY and AUTO_INCREMENT to hpp_produk.id:\n";
        // First, make id the primary key with AUTO_INCREMENT
        DB::statement("ALTER TABLE hpp_produk MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
        echo "   ✓ PRIMARY KEY and AUTO_INCREMENT added successfully\n";
    }
    
    // Verify the fix
    echo "\n3. Verifying the fix:\n";
    $result = DB::select("SHOW CREATE TABLE hpp_produk");
    $createTable = $result[0]->{'Create Table'};
    
    if (strpos($createTable, 'AUTO_INCREMENT') !== false && strpos($createTable, 'PRIMARY KEY') !== false) {
        echo "   ✓ Verification successful - hpp_produk now has PRIMARY KEY and AUTO_INCREMENT\n";
    } else {
        echo "   ✗ Verification failed\n";
    }
    
    // Test insert
    echo "\n4. Testing insert:\n";
    $testData = [
        'id_produk' => 1,
        'hpp' => 1000,
        'stok' => 10,
        'created_at' => now(),
        'updated_at' => now(),
    ];
    
    $id = DB::table('hpp_produk')->insertGetId($testData);
    echo "   ✓ Test insert successful with ID: $id\n";
    
    // Clean up test data
    DB::table('hpp_produk')->where('id', $id)->delete();
    echo "   ✓ Test data cleaned up\n";
    
    echo "\n✅ FIX COMPLETE!\n";
    echo "\nNext steps:\n";
    echo "1. Model HppProduk.php has been updated with incrementing config\n";
    echo "2. Try adding stock again - the error should be gone\n";
    echo "3. Clear cache if needed: php artisan config:clear\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
