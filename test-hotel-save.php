<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== TEST HOTEL DATA SAVE ===\n\n";

// Get a package
$package = TravelPackage::first();

if (!$package) {
    echo "No package found. Please create a package first.\n";
    exit;
}

echo "Testing with Package ID: {$package->id}\n";
echo "Package Name: {$package->package_name}\n\n";

echo "Current Hotel Data:\n";
echo "  id_hotel_makkah: " . ($package->id_hotel_makkah ?? 'NULL') . "\n";
echo "  id_hotel_room_type_makkah: " . ($package->id_hotel_room_type_makkah ?? 'NULL') . "\n";
echo "  makkah_check_in: " . ($package->makkah_check_in ?? 'NULL') . "\n";
echo "  makkah_check_out: " . ($package->makkah_check_out ?? 'NULL') . "\n";
echo "  id_hotel_madinah: " . ($package->id_hotel_madinah ?? 'NULL') . "\n";
echo "  id_hotel_room_type_madinah: " . ($package->id_hotel_room_type_madinah ?? 'NULL') . "\n";
echo "  madinah_check_in: " . ($package->madinah_check_in ?? 'NULL') . "\n";
echo "  madinah_check_out: " . ($package->madinah_check_out ?? 'NULL') . "\n\n";

// Test update with hotel data
echo "Testing update with hotel data...\n";

$testData = [
    'id_hotel_makkah' => 1,
    'id_hotel_room_type_makkah' => 1,
    'makkah_check_in' => '2026-05-01',
    'makkah_check_out' => '2026-05-10',
    'id_hotel_madinah' => 2,
    'id_hotel_room_type_madinah' => 2,
    'madinah_check_in' => '2026-05-10',
    'madinah_check_out' => '2026-05-15',
];

try {
    $package->update($testData);
    echo "✓ Update successful\n\n";
    
    // Refresh and check
    $package->refresh();
    
    echo "After Update:\n";
    echo "  id_hotel_makkah: " . ($package->id_hotel_makkah ?? 'NULL') . "\n";
    echo "  id_hotel_room_type_makkah: " . ($package->id_hotel_room_type_makkah ?? 'NULL') . "\n";
    echo "  makkah_check_in: " . ($package->makkah_check_in ?? 'NULL') . "\n";
    echo "  makkah_check_out: " . ($package->makkah_check_out ?? 'NULL') . "\n";
    echo "  id_hotel_madinah: " . ($package->id_hotel_madinah ?? 'NULL') . "\n";
    echo "  id_hotel_room_type_madinah: " . ($package->id_hotel_room_type_madinah ?? 'NULL') . "\n";
    echo "  madinah_check_in: " . ($package->madinah_check_in ?? 'NULL') . "\n";
    echo "  madinah_check_out: " . ($package->madinah_check_out ?? 'NULL') . "\n\n";
    
    if ($package->id_hotel_makkah == 1) {
        echo "✓ Hotel data saved successfully!\n";
    } else {
        echo "✗ Hotel data NOT saved!\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Check if columns exist in database
echo "\n=== Checking Database Schema ===\n";
$columns = \DB::select("SHOW COLUMNS FROM travel_packages WHERE Field LIKE '%hotel%'");
echo "Hotel-related columns:\n";
foreach ($columns as $col) {
    echo "  - {$col->Field} ({$col->Type})\n";
}
