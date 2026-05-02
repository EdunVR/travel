<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Hotel;

echo "=== COMPLETE HOTEL FLOW TEST ===\n\n";

// Step 1: Check available hotels
echo "Step 1: Available Hotels\n";
echo str_repeat('-', 50) . "\n";
$hotels = Hotel::all();
echo "Total hotels: " . $hotels->count() . "\n";
foreach ($hotels as $hotel) {
    echo "  ID: {$hotel->id} | {$hotel->hotel_name} | City: {$hotel->city}\n";
}

// Step 2: Get first package
echo "\nStep 2: Get Package\n";
echo str_repeat('-', 50) . "\n";
$package = TravelPackage::first();
if (!$package) {
    echo "ERROR: No package found!\n";
    exit;
}
echo "Package ID: {$package->id}\n";
echo "Package Name: {$package->package_name}\n";

// Step 3: Check current hotel data
echo "\nStep 3: Current Hotel Data in Database\n";
echo str_repeat('-', 50) . "\n";
$rawData = DB::table('travel_packages')->where('id', $package->id)->first();
echo "id_hotel_makkah: " . ($rawData->id_hotel_makkah ?? 'NULL') . "\n";
echo "id_hotel_room_type_makkah: " . ($rawData->id_hotel_room_type_makkah ?? 'NULL') . "\n";
echo "makkah_check_in: " . ($rawData->makkah_check_in ?? 'NULL') . "\n";
echo "makkah_check_out: " . ($rawData->makkah_check_out ?? 'NULL') . "\n";
echo "id_hotel_madinah: " . ($rawData->id_hotel_madinah ?? 'NULL') . "\n";
echo "id_hotel_room_type_madinah: " . ($rawData->id_hotel_room_type_madinah ?? 'NULL') . "\n";
echo "madinah_check_in: " . ($rawData->madinah_check_in ?? 'NULL') . "\n";
echo "madinah_check_out: " . ($rawData->madinah_check_out ?? 'NULL') . "\n";

// Step 4: Test update
echo "\nStep 4: Test Update\n";
echo str_repeat('-', 50) . "\n";
echo "Updating package with hotel Makkah (ID: 1)...\n";

try {
    DB::table('travel_packages')
        ->where('id', $package->id)
        ->update([
            'id_hotel_makkah' => 1,
            'makkah_check_in' => '2026-05-01',
            'makkah_check_out' => '2026-05-10',
            'updated_at' => now()
        ]);
    
    echo "✓ Update successful (using DB::table)\n";
    
    // Verify
    $rawData = DB::table('travel_packages')->where('id', $package->id)->first();
    echo "Verification:\n";
    echo "  id_hotel_makkah: " . ($rawData->id_hotel_makkah ?? 'NULL') . "\n";
    echo "  makkah_check_in: " . ($rawData->makkah_check_in ?? 'NULL') . "\n";
    echo "  makkah_check_out: " . ($rawData->makkah_check_out ?? 'NULL') . "\n";
    
    if ($rawData->id_hotel_makkah == 1) {
        echo "✓ Data saved correctly!\n";
    } else {
        echo "✗ Data NOT saved!\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Step 5: Test loading via Model
echo "\nStep 5: Test Loading via Model\n";
echo str_repeat('-', 50) . "\n";
$package = TravelPackage::with(['hotelMakkah', 'hotelMadinah'])->find($package->id);
echo "id_hotel_makkah: " . ($package->id_hotel_makkah ?? 'NULL') . "\n";
echo "hotelMakkah relationship: " . ($package->hotelMakkah ? $package->hotelMakkah->hotel_name : 'NULL') . "\n";
echo "id_hotel_madinah: " . ($package->id_hotel_madinah ?? 'NULL') . "\n";
echo "hotelMadinah relationship: " . ($package->hotelMadinah ? $package->hotelMadinah->hotel_name : 'NULL') . "\n";

// Step 6: Simulate API response
echo "\nStep 6: Simulate API Response (for Edit Form)\n";
echo str_repeat('-', 50) . "\n";
$apiData = [
    'id' => $package->id,
    'package_name' => $package->package_name,
    'id_hotel_makkah' => $package->id_hotel_makkah,
    'id_hotel_room_type_makkah' => $package->id_hotel_room_type_makkah,
    'makkah_check_in' => $package->makkah_check_in ? $package->makkah_check_in->format('Y-m-d') : null,
    'makkah_check_out' => $package->makkah_check_out ? $package->makkah_check_out->format('Y-m-d') : null,
    'id_hotel_madinah' => $package->id_hotel_madinah,
    'id_hotel_room_type_madinah' => $package->id_hotel_room_type_madinah,
    'madinah_check_in' => $package->madinah_check_in ? $package->madinah_check_in->format('Y-m-d') : null,
    'madinah_check_out' => $package->madinah_check_out ? $package->madinah_check_out->format('Y-m-d') : null,
];
echo json_encode($apiData, JSON_PRETTY_PRINT) . "\n";

echo "\n=== TEST COMPLETE ===\n";
