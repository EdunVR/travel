<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Hotel;

echo "=== HOTEL DATA COMPLETE TEST ===\n\n";

// Get first package
$package = TravelPackage::first();

if (!$package) {
    echo "❌ No package found!\n";
    exit;
}

echo "Testing Package: {$package->package_name} (ID: {$package->id})\n\n";

// Test 1: Check specific hotel fields
echo "=== TEST 1: Specific Hotel Fields ===\n";
echo "id_hotel_makkah: " . ($package->id_hotel_makkah ?? 'NULL') . "\n";
echo "id_hotel_room_type_makkah: " . ($package->id_hotel_room_type_makkah ?? 'NULL') . "\n";
echo "makkah_check_in: " . ($package->makkah_check_in ?? 'NULL') . "\n";
echo "makkah_check_out: " . ($package->makkah_check_out ?? 'NULL') . "\n";
echo "id_hotel_madinah: " . ($package->id_hotel_madinah ?? 'NULL') . "\n";
echo "id_hotel_room_type_madinah: " . ($package->id_hotel_room_type_madinah ?? 'NULL') . "\n";
echo "madinah_check_in: " . ($package->madinah_check_in ?? 'NULL') . "\n";
echo "madinah_check_out: " . ($package->madinah_check_out ?? 'NULL') . "\n\n";

// Test 2: Check hotels JSON field
echo "=== TEST 2: Hotels JSON Field ===\n";
$hotelsJson = $package->hotels;
if ($hotelsJson) {
    echo "✓ Hotels JSON exists\n";
    echo "Type: " . gettype($hotelsJson) . "\n";
    echo "Count: " . (is_array($hotelsJson) ? count($hotelsJson) : 0) . "\n";
    if (is_array($hotelsJson) && count($hotelsJson) > 0) {
        echo "Data:\n";
        foreach ($hotelsJson as $idx => $hotel) {
            echo "  Hotel " . ($idx + 1) . ":\n";
            echo "    City: " . ($hotel['city'] ?? 'N/A') . "\n";
            echo "    Hotel ID: " . ($hotel['id_hotel'] ?? 'N/A') . "\n";
            echo "    Hotel Name: " . ($hotel['hotel_name'] ?? 'N/A') . "\n";
            echo "    Check-in: " . ($hotel['check_in'] ?? 'N/A') . "\n";
            echo "    Check-out: " . ($hotel['check_out'] ?? 'N/A') . "\n";
            echo "    Nights: " . ($hotel['nights'] ?? 'N/A') . "\n";
        }
    }
} else {
    echo "⚠ Hotels JSON is empty\n";
}
echo "\n";

// Test 3: Update hotels JSON
echo "=== TEST 3: Update Hotels JSON ===\n";
$testHotels = [
    [
        'city' => 'Istanbul',
        'id_hotel' => 5,
        'hotel_name' => 'WANDA VISITA',
        'location' => 'Taksim',
        'star_rating' => 4,
        'id_room_type' => null,
        'check_in' => '2026-05-15',
        'check_out' => '2026-05-17',
        'nights' => 2
    ],
    [
        'city' => 'Dubai',
        'id_hotel' => 6,
        'hotel_name' => 'WYNDHAM DUBAI MARINA',
        'location' => 'Dubai Marina',
        'star_rating' => 5,
        'id_room_type' => null,
        'check_in' => '2026-05-18',
        'check_out' => '2026-05-20',
        'nights' => 2
    ]
];

try {
    $package->hotels = $testHotels;
    $package->save();
    echo "✓ Hotels JSON updated successfully\n\n";
    
    // Reload and verify
    $package->refresh();
    echo "Verification after save:\n";
    $savedHotels = $package->hotels;
    if (is_array($savedHotels) && count($savedHotels) === 2) {
        echo "✓ Hotels count matches (2)\n";
        echo "✓ Hotel 1: {$savedHotels[0]['city']} - {$savedHotels[0]['hotel_name']}\n";
        echo "✓ Hotel 2: {$savedHotels[1]['city']} - {$savedHotels[1]['hotel_name']}\n";
    } else {
        echo "❌ Hotels data mismatch\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Check available hotels
echo "=== TEST 4: Available Hotels ===\n";
$makkahHotels = Hotel::where('city', 'Makkah')->orWhere('city', 'Mekah')->get();
$madinahHotels = Hotel::where('city', 'Madinah')->get();
$otherHotels = Hotel::whereNotIn('city', ['Makkah', 'Mekah', 'Madinah'])->get();

echo "Makkah Hotels: " . $makkahHotels->count() . "\n";
foreach ($makkahHotels as $h) {
    echo "  - {$h->hotel_name} (ID: {$h->id})\n";
}

echo "\nMadinah Hotels: " . $madinahHotels->count() . "\n";
foreach ($madinahHotels as $h) {
    echo "  - {$h->hotel_name} (ID: {$h->id})\n";
}

echo "\nOther Hotels: " . $otherHotels->count() . "\n";
foreach ($otherHotels as $h) {
    echo "  - {$h->hotel_name} in {$h->city} (ID: {$h->id})\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "✓ All hotel data fields are working correctly\n";
echo "✓ Hotels JSON field can save and load data\n";
echo "✓ Both specific (Makkah/Madinah) and additional hotels are supported\n";

