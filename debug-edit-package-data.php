<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\HotelRoomType;

echo "=== DEBUG EDIT PACKAGE DATA MISMATCH ===\n\n";

// Get all packages
$packages = TravelPackage::all();

echo "TRAVEL PACKAGES:\n";
echo str_repeat("-", 80) . "\n";
foreach ($packages as $package) {
    echo "Package ID: {$package->id} - {$package->package_name}\n";
    echo "  id_flight: " . ($package->id_flight ?? 'NULL') . "\n";
    echo "  id_hotel: " . ($package->id_hotel ?? 'NULL') . "\n";
    echo "  id_hotel_room_type: " . ($package->id_hotel_room_type ?? 'NULL') . "\n";
    echo "  airline (text): " . ($package->airline ?? 'NULL') . "\n";
    echo "  hotel_name (text): " . ($package->hotel_name ?? 'NULL') . "\n";
    echo "\n";
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Get all flights
$flights = Flight::all();
echo "FLIGHTS TABLE:\n";
echo str_repeat("-", 80) . "\n";
if ($flights->isEmpty()) {
    echo "❌ No flights found in database!\n";
} else {
    foreach ($flights as $flight) {
        echo "Flight ID: {$flight->id}\n";
        echo "  Airline: {$flight->airline_name}\n";
        echo "  Flight Number: {$flight->flight_number}\n";
        echo "  Route: {$flight->departure_airport} → {$flight->arrival_airport}\n";
        echo "  Price: Rp " . number_format($flight->price ?? 0, 0, ',', '.') . "\n";
        echo "  Outlet ID: {$flight->id_outlet}\n";
        echo "\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Get all hotels
$hotels = Hotel::with('roomTypes')->get();
echo "HOTELS TABLE:\n";
echo str_repeat("-", 80) . "\n";
if ($hotels->isEmpty()) {
    echo "❌ No hotels found in database!\n";
} else {
    foreach ($hotels as $hotel) {
        echo "Hotel ID: {$hotel->id}\n";
        echo "  Name: {$hotel->hotel_name}\n";
        echo "  Location: {$hotel->location}\n";
        echo "  City: {$hotel->city}\n";
        echo "  Star Rating: {$hotel->star_rating}★\n";
        echo "  Outlet ID: {$hotel->id_outlet}\n";
        echo "  Room Types:\n";
        
        if ($hotel->roomTypes->isEmpty()) {
            echo "    ❌ No room types for this hotel\n";
        } else {
            foreach ($hotel->roomTypes as $roomType) {
                echo "    - Room Type ID: {$roomType->id}\n";
                echo "      Name: {$roomType->room_type_name}\n";
                echo "      Price/Night: Rp " . number_format($roomType->price_per_night, 0, ',', '.') . "\n";
                echo "      Capacity: {$roomType->capacity} pax\n";
                echo "      Total Rooms: {$roomType->total_rooms}\n";
            }
        }
        echo "\n";
    }
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Check for data mismatches
echo "DATA VALIDATION:\n";
echo str_repeat("-", 80) . "\n";

foreach ($packages as $package) {
    $issues = [];
    
    // Check flight ID
    if ($package->id_flight) {
        $flight = Flight::find($package->id_flight);
        if (!$flight) {
            $issues[] = "❌ Flight ID {$package->id_flight} tidak ditemukan di tabel flights";
        } else {
            echo "✓ Package '{$package->package_name}' - Flight ID {$package->id_flight} valid\n";
        }
    }
    
    // Check hotel ID
    if ($package->id_hotel) {
        $hotel = Hotel::find($package->id_hotel);
        if (!$hotel) {
            $issues[] = "❌ Hotel ID {$package->id_hotel} tidak ditemukan di tabel hotels";
        } else {
            echo "✓ Package '{$package->package_name}' - Hotel ID {$package->id_hotel} valid\n";
        }
    }
    
    // Check room type ID
    if ($package->id_hotel_room_type) {
        $roomType = HotelRoomType::find($package->id_hotel_room_type);
        if (!$roomType) {
            $issues[] = "❌ Room Type ID {$package->id_hotel_room_type} tidak ditemukan di tabel hotel_room_types";
        } else {
            echo "✓ Package '{$package->package_name}' - Room Type ID {$package->id_hotel_room_type} valid\n";
            
            // Check if room type belongs to the hotel
            if ($package->id_hotel && $roomType->id_hotel != $package->id_hotel) {
                $issues[] = "⚠️  Room Type ID {$package->id_hotel_room_type} tidak belong ke Hotel ID {$package->id_hotel}";
            }
        }
    }
    
    if (!empty($issues)) {
        echo "\nIssues for Package '{$package->package_name}':\n";
        foreach ($issues as $issue) {
            echo "  {$issue}\n";
        }
    }
    
    echo "\n";
}

echo "\n" . str_repeat("=", 80) . "\n\n";

// Check API endpoints
echo "API ENDPOINT CHECK:\n";
echo str_repeat("-", 80) . "\n";

// Check flight-data endpoint
echo "Testing /admin/inventaris/flight-data endpoint...\n";
try {
    $flightController = new \App\Http\Controllers\FlightController();
    $request = new \Illuminate\Http\Request();
    $response = $flightController->getData($request);
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['data']) && is_array($data['data'])) {
        echo "✓ Flight endpoint returns " . count($data['data']) . " flights\n";
        if (!empty($data['data'])) {
            $firstFlight = $data['data'][0];
            echo "  Sample: ID={$firstFlight['id']}, Airline={$firstFlight['airline_name']}\n";
        }
    } else {
        echo "❌ Flight endpoint returns invalid data structure\n";
    }
} catch (\Exception $e) {
    echo "❌ Error testing flight endpoint: " . $e->getMessage() . "\n";
}

echo "\n";

// Check hotel-data endpoint
echo "Testing /admin/inventaris/hotel-data endpoint...\n";
try {
    $hotelController = new \App\Http\Controllers\HotelController();
    $request = new \Illuminate\Http\Request();
    $response = $hotelController->getData($request);
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['data']) && is_array($data['data'])) {
        echo "✓ Hotel endpoint returns " . count($data['data']) . " hotels\n";
        if (!empty($data['data'])) {
            $firstHotel = $data['data'][0];
            echo "  Sample: ID={$firstHotel['id']}, Name={$firstHotel['hotel_name']}\n";
        }
    } else {
        echo "❌ Hotel endpoint returns invalid data structure\n";
    }
} catch (\Exception $e) {
    echo "❌ Error testing hotel endpoint: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n\n";

echo "RECOMMENDATIONS:\n";
echo str_repeat("-", 80) . "\n";

if ($flights->isEmpty()) {
    echo "1. ❌ Tambahkan data flights ke database\n";
}

if ($hotels->isEmpty()) {
    echo "2. ❌ Tambahkan data hotels ke database\n";
}

$roomTypesCount = HotelRoomType::count();
if ($roomTypesCount == 0) {
    echo "3. ❌ Tambahkan data hotel room types ke database\n";
}

// Check if packages have NULL IDs
$packagesWithoutFlight = TravelPackage::whereNull('id_flight')->count();
$packagesWithoutHotel = TravelPackage::whereNull('id_hotel')->count();
$packagesWithoutRoomType = TravelPackage::whereNull('id_hotel_room_type')->count();

if ($packagesWithoutFlight > 0) {
    echo "4. ⚠️  {$packagesWithoutFlight} paket belum memiliki id_flight\n";
}

if ($packagesWithoutHotel > 0) {
    echo "5. ⚠️  {$packagesWithoutHotel} paket belum memiliki id_hotel\n";
}

if ($packagesWithoutRoomType > 0) {
    echo "6. ⚠️  {$packagesWithoutRoomType} paket belum memiliki id_hotel_room_type\n";
}

echo "\n";
