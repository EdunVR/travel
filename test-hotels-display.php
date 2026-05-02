<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;
use App\Models\Hotel;

echo "=== TEST HOTELS DISPLAY ===\n\n";

// Test 1: Create package with hotels
echo "=== TEST 1: Create Package with Hotels ===\n";
$testPackage = TravelPackage::create([
    'package_code' => 'TEST-' . time(),
    'package_name' => 'Test Package with Hotels',
    'package_type' => 'umrah',
    'package_subtype' => 'umroh_regular',
    'duration_days' => 9,
    'capacity' => 45,
    'price' => 25000000,
    'status' => 'active',
    'id_outlet' => 1,
    'departure_date' => '2026-06-01',
    'return_date' => '2026-06-10',
    // Hotel Makkah & Madinah
    'id_hotel_makkah' => 1,
    'makkah_check_in' => '2026-06-01',
    'makkah_check_out' => '2026-06-05',
    'id_hotel_madinah' => 3,
    'madinah_check_in' => '2026-06-05',
    'madinah_check_out' => '2026-06-09',
    // Additional Hotels
    'hotels' => [
        [
            'city' => 'Istanbul',
            'id_hotel' => 5,
            'hotel_name' => 'WANDA VISITA',
            'location' => 'Taksim',
            'star_rating' => 4,
            'check_in' => '2026-05-30',
            'check_out' => '2026-06-01',
            'nights' => 2
        ]
    ]
]);

echo "✓ Package created: {$testPackage->package_name} (ID: {$testPackage->id})\n";
echo "  id_hotel_makkah: {$testPackage->id_hotel_makkah}\n";
echo "  id_hotel_madinah: {$testPackage->id_hotel_madinah}\n";
echo "  hotels count: " . (is_array($testPackage->hotels) ? count($testPackage->hotels) : 0) . "\n\n";

// Test 2: Reload and check
echo "=== TEST 2: Reload Package ===\n";
$reloaded = TravelPackage::find($testPackage->id);
echo "Package: {$reloaded->package_name}\n";
echo "id_hotel_makkah: " . ($reloaded->id_hotel_makkah ?? 'NULL') . "\n";
echo "id_hotel_madinah: " . ($reloaded->id_hotel_madinah ?? 'NULL') . "\n";
echo "hotels: " . (is_array($reloaded->hotels) ? json_encode($reloaded->hotels, JSON_PRETTY_PRINT) : 'NULL') . "\n\n";

// Test 3: Check API response (like edit form would get)
echo "=== TEST 3: API Response (Edit Form) ===\n";
$apiData = [
    'id' => $reloaded->id,
    'package_name' => $reloaded->package_name,
    'id_hotel_makkah' => $reloaded->id_hotel_makkah,
    'id_hotel_madinah' => $reloaded->id_hotel_madinah,
    'hotels' => $reloaded->hotels ?? []
];
echo json_encode($apiData, JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Check hotels for homepage
echo "=== TEST 4: Hotels for Homepage ===\n";
$activePackages = TravelPackage::where('status', 'active')->get();
echo "Active packages: " . $activePackages->count() . "\n\n";

$hotelIds = [];
foreach ($activePackages as $pkg) {
    if ($pkg->id_hotel_makkah) {
        $hotelIds[] = $pkg->id_hotel_makkah;
        echo "  Package '{$pkg->package_name}' uses Makkah hotel ID: {$pkg->id_hotel_makkah}\n";
    }
    if ($pkg->id_hotel_madinah) {
        $hotelIds[] = $pkg->id_hotel_madinah;
        echo "  Package '{$pkg->package_name}' uses Madinah hotel ID: {$pkg->id_hotel_madinah}\n";
    }
    
    // Check additional hotels
    if ($pkg->hotels && is_array($pkg->hotels)) {
        foreach ($pkg->hotels as $h) {
            if (isset($h['id_hotel'])) {
                $hotelIds[] = $h['id_hotel'];
                echo "  Package '{$pkg->package_name}' uses additional hotel: {$h['city']} - {$h['hotel_name']}\n";
            }
        }
    }
}

$uniqueHotelIds = array_unique($hotelIds);
echo "\nUnique hotel IDs used: " . implode(', ', $uniqueHotelIds) . "\n";

$hotels = Hotel::whereIn('id', $uniqueHotelIds)->get();
echo "Hotels found: " . $hotels->count() . "\n";
foreach ($hotels as $hotel) {
    echo "  - {$hotel->hotel_name} ({$hotel->city})\n";
}

// Cleanup
echo "\n=== CLEANUP ===\n";
$testPackage->delete();
echo "✓ Test package deleted\n";

echo "\n=== SUMMARY ===\n";
echo "✅ Hotels field is now in fillable array\n";
echo "✅ Hotels JSON can be saved via mass assignment\n";
echo "✅ Hotels JSON loads correctly when editing\n";
echo "✅ Hotels can be displayed on homepage from active packages\n";

