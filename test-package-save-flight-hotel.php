<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\HotelRoomType;

echo "=== TEST PACKAGE SAVE WITH FLIGHT & HOTEL ===\n\n";

// Get first flight and hotel room type
$flight = Flight::first();
$hotelRoomType = HotelRoomType::first();

if (!$flight) {
    echo "❌ No flights found in database. Please add flights first.\n";
    exit(1);
}

if (!$hotelRoomType) {
    echo "❌ No hotel room types found in database. Please add hotels first.\n";
    exit(1);
}

echo "✓ Found flight: {$flight->airline_name} - {$flight->flight_number}\n";
echo "✓ Found hotel room type: {$hotelRoomType->room_type_name}\n";
echo "\n";

// Test data
$testData = [
    'package_code' => 'TEST-' . time(),
    'package_name' => 'Test Package with Flight & Hotel',
    'package_type' => 'umrah',
    'description' => 'Test package to verify flight and hotel IDs are saved',
    'duration_days' => 9,
    'departure_date' => now()->addDays(30),
    'return_date' => now()->addDays(39),
    'capacity' => 45,
    'price' => 25000000,
    'status' => 'draft',
    'current_workflow_stage' => 'product_analysis',
    'id_outlet' => 2,
    'id_flight' => $flight->id,
    'id_hotel' => $hotelRoomType->hotel->id,
    'id_hotel_room_type' => $hotelRoomType->id,
    'airline' => $flight->airline_name,
    'hotel_name' => $hotelRoomType->hotel->hotel_name
];

echo "Creating test package...\n";
$package = TravelPackage::create($testData);

echo "✓ Package created with ID: {$package->id}\n\n";

// Reload package with relations
$package = TravelPackage::with(['flight', 'hotel', 'hotelRoomType'])->find($package->id);

echo "=== VERIFICATION ===\n";
echo "Package: {$package->package_name}\n";
echo "  id_flight: " . ($package->id_flight ?? 'NULL') . "\n";
echo "  id_hotel: " . ($package->id_hotel ?? 'NULL') . "\n";
echo "  id_hotel_room_type: " . ($package->id_hotel_room_type ?? 'NULL') . "\n";
echo "  airline (text): " . ($package->airline ?? 'NULL') . "\n";
echo "  hotel_name (text): " . ($package->hotel_name ?? 'NULL') . "\n";

if ($package->flight) {
    echo "  ✓ Flight relation loaded: {$package->flight->airline_name} - {$package->flight->flight_number}\n";
    echo "    Price: Rp " . number_format($package->flight->price ?? 0, 0, ',', '.') . "\n";
} else {
    echo "  ✗ Flight relation: NULL\n";
}

if ($package->hotel) {
    echo "  ✓ Hotel relation loaded: {$package->hotel->hotel_name}\n";
} else {
    echo "  ✗ Hotel relation: NULL\n";
}

if ($package->hotelRoomType) {
    echo "  ✓ Room Type relation loaded: {$package->hotelRoomType->room_type_name}\n";
    echo "    Price per night: Rp " . number_format($package->hotelRoomType->price_per_night ?? 0, 0, ',', '.') . "\n";
} else {
    echo "  ✗ Room Type relation: NULL\n";
}

echo "\n";

if ($package->id_flight && $package->id_hotel && $package->id_hotel_room_type) {
    echo "✅ SUCCESS! All IDs are saved correctly.\n";
    echo "\nNow test updating the package...\n\n";
    
    // Test update
    $package->update([
        'package_name' => 'Updated Test Package',
        'id_flight' => $flight->id,
        'id_hotel' => $hotelRoomType->hotel->id,
        'id_hotel_room_type' => $hotelRoomType->id
    ]);
    
    $package->refresh();
    
    echo "After update:\n";
    echo "  id_flight: " . ($package->id_flight ?? 'NULL') . "\n";
    echo "  id_hotel: " . ($package->id_hotel ?? 'NULL') . "\n";
    echo "  id_hotel_room_type: " . ($package->id_hotel_room_type ?? 'NULL') . "\n";
    
    if ($package->id_flight && $package->id_hotel && $package->id_hotel_room_type) {
        echo "\n✅ UPDATE SUCCESS! All IDs are still saved correctly.\n";
    } else {
        echo "\n❌ UPDATE FAILED! Some IDs were lost.\n";
    }
    
    // Clean up
    echo "\nCleaning up test data...\n";
    $package->delete();
    echo "✓ Test package deleted.\n";
} else {
    echo "❌ FAILED! Some IDs are NULL.\n";
    echo "\nCleaning up test data...\n";
    $package->delete();
    echo "✓ Test package deleted.\n";
}
