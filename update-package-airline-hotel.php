<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== UPDATE PACKAGE AIRLINE & HOTEL ===\n\n";

$packages = TravelPackage::all();

foreach ($packages as $package) {
    echo "Package: {$package->package_name}\n";
    
    // Set default airline and hotel based on package type
    if ($package->package_type === 'umrah') {
        $airlines = ['Garuda Indonesia', 'Saudi Arabian Airlines', 'Emirates', 'Qatar Airways'];
        $hotels = ['Hilton Makkah Convention', 'Pullman Zamzam Madinah', 'Swissotel Makkah', 'Dar Al Eiman Royal'];
    } else {
        $airlines = ['Garuda Indonesia', 'Saudi Arabian Airlines', 'Emirates'];
        $hotels = ['Hilton Makkah Convention', 'Pullman Zamzam Madinah', 'Swissotel Makkah'];
    }
    
    $package->airline = $airlines[array_rand($airlines)];
    $package->hotel_name = $hotels[array_rand($hotels)];
    $package->save();
    
    echo "  ✓ Airline: {$package->airline}\n";
    echo "  ✓ Hotel: {$package->hotel_name}\n\n";
}

echo "=== DONE ===\n";
echo "Total packages updated: " . $packages->count() . "\n";
