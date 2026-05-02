<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== DEBUG HOTELS JSON SAVE ===\n\n";

// Get package
$package = TravelPackage::first();

if (!$package) {
    echo "❌ No package found!\n";
    exit;
}

echo "Package: {$package->package_name} (ID: {$package->id})\n\n";

// Check current hotels data
echo "=== CURRENT DATA ===\n";
echo "id_hotel_makkah: " . ($package->id_hotel_makkah ?? 'NULL') . "\n";
echo "id_hotel_madinah: " . ($package->id_hotel_madinah ?? 'NULL') . "\n";

$raw = DB::table('travel_packages')->where('id', $package->id)->first();
echo "\nRAW hotels column: " . ($raw->hotels ?? 'NULL') . "\n";

$hotelsArray = $package->hotels;
echo "Model hotels property: " . ($hotelsArray ? json_encode($hotelsArray, JSON_PRETTY_PRINT) : 'NULL') . "\n\n";

// Test 1: Save via Model
echo "=== TEST 1: Save via Model ===\n";
$testData = [
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
    ]
];

try {
    $package->hotels = $testData;
    $saved = $package->save();
    echo "Save result: " . ($saved ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Reload
    $package->refresh();
    $reloaded = $package->hotels;
    
    if ($reloaded && is_array($reloaded) && count($reloaded) > 0) {
        echo "✓ Data reloaded successfully\n";
        echo "  Count: " . count($reloaded) . "\n";
        echo "  First hotel: {$reloaded[0]['city']} - {$reloaded[0]['hotel_name']}\n";
    } else {
        echo "❌ Data NOT reloaded\n";
        echo "  Reloaded value: " . json_encode($reloaded) . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST 2: Save via Update Method ===\n";
$testData2 = [
    [
        'city' => 'Dubai',
        'id_hotel' => 6,
        'hotel_name' => 'WYNDHAM DUBAI MARINA',
        'check_in' => '2026-05-18',
        'check_out' => '2026-05-20',
        'nights' => 2
    ]
];

try {
    $updated = $package->update(['hotels' => $testData2]);
    echo "Update result: " . ($updated ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Reload
    $package->refresh();
    $reloaded = $package->hotels;
    
    if ($reloaded && is_array($reloaded) && count($reloaded) > 0) {
        echo "✓ Data reloaded successfully\n";
        echo "  Count: " . count($reloaded) . "\n";
        echo "  First hotel: {$reloaded[0]['city']} - {$reloaded[0]['hotel_name']}\n";
    } else {
        echo "❌ Data NOT reloaded\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST 3: Check Model Casts ===\n";
$casts = $package->getCasts();
echo "hotels cast: " . ($casts['hotels'] ?? 'NOT DEFINED') . "\n";

echo "\n=== TEST 4: Check if hotels is in fillable ===\n";
$fillable = $package->getFillable();
$inFillable = in_array('hotels', $fillable);
echo "hotels in fillable: " . ($inFillable ? 'YES' : 'NO') . "\n";

if (!$inFillable) {
    echo "⚠️  WARNING: 'hotels' is NOT in fillable array!\n";
    echo "This means mass assignment won't work.\n";
}

echo "\n=== RECOMMENDATION ===\n";
if (!$inFillable) {
    echo "Add 'hotels' to \$fillable array in TravelPackage model:\n";
    echo "protected \$fillable = [..., 'hotels'];\n";
}

