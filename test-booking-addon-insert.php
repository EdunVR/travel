<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING BOOKING ADDON INSERT ===\n\n";

try {
    // Test data
    $testData = [
        'id_jamaah_booking' => 1, // Assuming booking ID 1 exists
        'id_produk' => 4,
        'nama' => 'Test Seragam HM',
        'keterangan' => 'Test perlengkapan dari script',
        'harga' => 250000,
        'qty' => 1,
        'masuk_hpp' => false,
    ];
    
    echo "Test Data:\n";
    print_r($testData);
    echo "\n";
    
    // Try to insert
    echo "Attempting to insert...\n";
    $addon = \App\Models\BookingAddon::create($testData);
    
    echo "✓ SUCCESS! Booking addon created with ID: " . $addon->id . "\n\n";
    
    // Verify
    echo "Verifying data in database:\n";
    $verify = \App\Models\BookingAddon::find($addon->id);
    echo "ID: " . $verify->id . "\n";
    echo "Booking ID: " . $verify->id_jamaah_booking . "\n";
    echo "Product ID: " . $verify->id_produk . "\n";
    echo "Nama: " . $verify->nama . "\n";
    echo "Harga: " . $verify->harga . "\n";
    echo "Qty: " . $verify->qty . "\n";
    echo "Masuk HPP: " . ($verify->masuk_hpp ? 'Yes' : 'No') . "\n";
    
    // Clean up test data
    echo "\nCleaning up test data...\n";
    $verify->delete();
    echo "✓ Test data deleted\n";
    
    echo "\n=== TEST PASSED ===\n";
    
} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
