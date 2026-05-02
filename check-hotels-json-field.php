<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== CHECK HOTELS JSON FIELD ===\n\n";

$package = TravelPackage::first();

if (!$package) {
    echo "No package found!\n";
    exit;
}

echo "Package ID: {$package->id}\n";
echo "Package Name: {$package->package_name}\n\n";

echo "=== DATABASE RAW DATA ===\n";
$raw = DB::table('travel_packages')->where('id', $package->id)->first();
echo "hotels column (raw): " . ($raw->hotels ?? 'NULL') . "\n";
echo "hotels column type: " . gettype($raw->hotels) . "\n\n";

echo "=== MODEL DATA ===\n";
echo "hotels property: " . ($package->hotels ? json_encode($package->hotels) : 'NULL') . "\n";
echo "hotels type: " . gettype($package->hotels) . "\n\n";

echo "=== TEST SAVE ===\n";
$testHotels = [
    [
        'city' => 'Istanbul',
        'id_hotel' => 5,
        'hotel_name' => 'WANDA VISITA',
        'id_room_type' => null,
        'check_in' => '2026-05-15',
        'check_out' => '2026-05-17',
        'nights' => 2
    ]
];

echo "Attempting to save hotels JSON...\n";
try {
    DB::table('travel_packages')
        ->where('id', $package->id)
        ->update([
            'hotels' => json_encode($testHotels),
            'updated_at' => now()
        ]);
    
    echo "✓ Save successful\n\n";
    
    // Verify
    $raw = DB::table('travel_packages')->where('id', $package->id)->first();
    echo "After save:\n";
    echo "hotels: " . ($raw->hotels ?? 'NULL') . "\n";
    
    $decoded = json_decode($raw->hotels, true);
    echo "Decoded:\n";
    print_r($decoded);
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COLUMN EXISTS ===\n";
$columns = DB::select("SHOW COLUMNS FROM travel_packages WHERE Field = 'hotels'");
if (count($columns) > 0) {
    echo "✓ Column 'hotels' exists\n";
    foreach ($columns as $col) {
        echo "  Type: {$col->Type}\n";
        echo "  Null: {$col->Null}\n";
        echo "  Default: " . ($col->Default ?? 'NULL') . "\n";
    }
} else {
    echo "✗ Column 'hotels' does NOT exist!\n";
}
