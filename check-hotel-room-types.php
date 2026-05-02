<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== HOTEL ROOM TYPES DATA ===\n\n";

$hotels = App\Models\Hotel::with('roomTypes')->get();

echo "Total Hotels: " . $hotels->count() . "\n";
echo "Total Room Types: " . DB::table('hotel_room_types')->count() . "\n\n";

foreach ($hotels as $hotel) {
    echo "Hotel ID: {$hotel->id} | {$hotel->hotel_name}\n";
    if ($hotel->roomTypes && $hotel->roomTypes->count() > 0) {
        foreach ($hotel->roomTypes as $rt) {
            echo "  - Room Type ID: {$rt->id} | {$rt->room_type_name} | Price: {$rt->price_per_night}\n";
        }
    } else {
        echo "  - No room types\n";
    }
    echo "\n";
}
