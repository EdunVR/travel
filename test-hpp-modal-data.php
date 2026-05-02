<?php
/**
 * Test script to verify HPP modal data loading
 * Run: php test-hpp-modal-data.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Flight;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;

echo "=== HPP MODAL DATA TEST ===\n\n";

// Get a package with flight and hotel
$package = TravelPackage::with(['flight', 'hotel', 'hotelRoomType'])
    ->whereNotNull('id_flight')
    ->whereNotNull('id_hotel_room_type')
    ->first();

if (!$package) {
    echo "❌ No package found with flight and hotel data\n";
    exit(1);
}

echo "📦 Package: {$package->package_name}\n";
echo "   ID: {$package->id}\n";
echo "   Flight ID: {$package->id_flight}\n";
echo "   Hotel ID: {$package->id_hotel}\n";
echo "   Room Type ID: {$package->id_hotel_room_type}\n";
echo "   Duration: {$package->duration_days} days\n";
echo "   Outlet ID: {$package->id_outlet}\n\n";

// Test getFlights API
echo "--- Testing getFlights API ---\n";
$flights = Flight::where('id_outlet', $package->id_outlet)
    ->orderBy('airline_name')
    ->orderBy('flight_number')
    ->get()
    ->map(function($flight) {
        return [
            'id' => $flight->id,
            'label' => $flight->airline_name . ' - ' . $flight->flight_number,
            'price_per_person' => $flight->price_per_person
        ];
    });

echo "Available flights: " . $flights->count() . "\n";
foreach ($flights as $flight) {
    $match = $flight['id'] == $package->id_flight ? '✅ MATCH' : '';
    echo "  - ID: {$flight['id']}, Label: {$flight['label']}, Price: {$flight['price_per_person']} {$match}\n";
}

$flightFound = $flights->firstWhere('id', $package->id_flight);
if ($flightFound) {
    echo "✅ Package flight found in available flights\n";
    echo "   Price: {$flightFound['price_per_person']}\n";
} else {
    echo "❌ Package flight NOT found in available flights\n";
}
echo "\n";

// Test getHotels API
echo "--- Testing getHotels API ---\n";
$hotels = Hotel::with('roomTypes')
    ->where('id_outlet', $package->id_outlet)
    ->orderBy('hotel_name')
    ->get()
    ->map(function($hotel) {
        return $hotel->roomTypes->map(function($roomType) use ($hotel) {
            return [
                'id' => $roomType->id,
                'hotel_id' => $hotel->id,
                'label' => $hotel->hotel_name . ' - ' . $roomType->room_type_name,
                'price_per_night' => $roomType->price_per_night
            ];
        });
    })
    ->flatten(1);

echo "Available hotel room types: " . $hotels->count() . "\n";
foreach ($hotels as $hotel) {
    $match = $hotel['id'] == $package->id_hotel_room_type ? '✅ MATCH' : '';
    echo "  - ID: {$hotel['id']}, Label: {$hotel['label']}, Price/night: {$hotel['price_per_night']} {$match}\n";
}

$hotelFound = $hotels->firstWhere('id', $package->id_hotel_room_type);
if ($hotelFound) {
    echo "✅ Package room type found in available hotels\n";
    echo "   Price per night: {$hotelFound['price_per_night']}\n";
    echo "   Total cost (per person): " . ($hotelFound['price_per_night'] * $package->duration_days) . "\n";
} else {
    echo "❌ Package room type NOT found in available hotels\n";
}
echo "\n";

// Test calculation
echo "--- Expected HPP Calculation ---\n";
if ($flightFound && $hotelFound) {
    $flightCost = $flightFound['price_per_person'];
    $hotelCost = $hotelFound['price_per_night'] * $package->duration_days;
    
    echo "Flight cost (per person): Rp " . number_format($flightCost, 0, ',', '.') . "\n";
    echo "Hotel cost (per person): Rp " . number_format($hotelCost, 0, ',', '.') . "\n";
    echo "Total: Rp " . number_format($flightCost + $hotelCost, 0, ',', '.') . "\n";
} else {
    echo "❌ Cannot calculate - missing data\n";
}

echo "\n=== TEST COMPLETE ===\n";
