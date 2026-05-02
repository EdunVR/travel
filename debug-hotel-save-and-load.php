<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Hotel;

echo "=== DEBUG HOTEL SAVE AND LOAD ===\n\n";

// Get first package
$package = TravelPackage::first();

if (!$package) {
    echo "No package found!\n";
    exit;
}

echo "Package ID: {$package->id}\n";
echo "Package Name: {$package->package_name}\n\n";

echo "=== CURRENT DATABASE VALUES ===\n";
echo "id_hotel_makkah: " . ($package->id_hotel_makkah ?? 'NULL') . "\n";
echo "id_hotel_room_type_makkah: " . ($package->id_hotel_room_type_makkah ?? 'NULL') . "\n";
echo "makkah_check_in: " . ($package->makkah_check_in ?? 'NULL') . "\n";
echo "makkah_check_out: " . ($package->makkah_check_out ?? 'NULL') . "\n";
echo "id_hotel_madinah: " . ($package->id_hotel_madinah ?? 'NULL') . "\n";
echo "id_hotel_room_type_madinah: " . ($package->id_hotel_room_type_madinah ?? 'NULL') . "\n";
echo "madinah_check_in: " . ($package->madinah_check_in ?? 'NULL') . "\n";
echo "madinah_check_out: " . ($package->madinah_check_out ?? 'NULL') . "\n\n";

echo "=== RELATIONSHIPS ===\n";
// Check if relationships work
if ($package->id_hotel_makkah) {
    $hotelMakkah = Hotel::find($package->id_hotel_makkah);
    echo "Hotel Makkah: " . ($hotelMakkah ? $hotelMakkah->hotel_name : 'NOT FOUND') . "\n";
} else {
    echo "Hotel Makkah: NOT SET\n";
}

if ($package->id_hotel_madinah) {
    $hotelMadinah = Hotel::find($package->id_hotel_madinah);
    echo "Hotel Madinah: " . ($hotelMadinah ? $hotelMadinah->hotel_name : 'NOT FOUND') . "\n";
} else {
    echo "Hotel Madinah: NOT SET\n";
}

echo "\n=== MODEL RELATIONSHIPS ===\n";
// Check if model has relationships defined
echo "hotelMakkah relationship: " . (method_exists($package, 'hotelMakkah') ? 'EXISTS' : 'NOT DEFINED') . "\n";
echo "hotelMadinah relationship: " . (method_exists($package, 'hotelMadinah') ? 'EXISTS' : 'NOT DEFINED') . "\n";

if (method_exists($package, 'hotelMakkah')) {
    $hotelMakkah = $package->hotelMakkah;
    echo "hotelMakkah loaded: " . ($hotelMakkah ? $hotelMakkah->hotel_name : 'NULL') . "\n";
}

if (method_exists($package, 'hotelMadinah')) {
    $hotelMadinah = $package->hotelMadinah;
    echo "hotelMadinah loaded: " . ($hotelMadinah ? $hotelMadinah->hotel_name : 'NULL') . "\n";
}

echo "\n=== AVAILABLE HOTELS ===\n";
$hotels = Hotel::all();
foreach ($hotels as $hotel) {
    echo "ID: {$hotel->id} | {$hotel->hotel_name} | City: {$hotel->city}\n";
}

echo "\n=== TEST UPDATE ===\n";
echo "Attempting to update package with hotel data...\n";

try {
    $package->update([
        'id_hotel_makkah' => 1,
        'makkah_check_in' => '2026-05-01',
        'makkah_check_out' => '2026-05-10',
    ]);
    
    echo "✓ Update successful\n";
    
    $package->refresh();
    echo "After update:\n";
    echo "  id_hotel_makkah: " . ($package->id_hotel_makkah ?? 'NULL') . "\n";
    echo "  makkah_check_in: " . ($package->makkah_check_in ?? 'NULL') . "\n";
    echo "  makkah_check_out: " . ($package->makkah_check_out ?? 'NULL') . "\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
