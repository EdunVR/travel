<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== DEBUG PACKAGE FLIGHT & HOTEL SAVED DATA ===\n\n";

$packages = TravelPackage::with(['flight', 'hotel', 'hotelRoomType'])->get();

foreach ($packages as $package) {
    echo "Package: {$package->package_name}\n";
    echo "  ID: {$package->id}\n";
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
}

echo "=== SOLUTION ===\n";
echo "Jika id_flight, id_hotel, id_hotel_room_type masih NULL:\n";
echo "- Data belum tersimpan saat create package\n";
echo "- Perlu cek PackageController store() method\n";
echo "- Pastikan field ada di \$fillable dan di-save\n";
