<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find package PKG202604-321
$pkg = DB::table('travel_packages')->where('package_code', 'PKG202604-321')->first();
if (!$pkg) { echo "Package not found\n"; exit; }

echo "Package ID: {$pkg->id}, Name: {$pkg->package_name}\n";
echo "id_flight_departure: {$pkg->id_flight_departure}\n\n";

$hpp = DB::table('hpp_calculations')->where('id_travel_package', $pkg->id)->first();
if ($hpp) {
    echo "HPP ID: {$hpp->id}\n";
    echo "flight_cost: {$hpp->flight_cost}\n";
    echo "transportation_cost: {$hpp->transportation_cost}\n";
    echo "meal_cost: {$hpp->meal_cost}\n";
    echo "operational_overhead: {$hpp->operational_overhead}\n";
    echo "custom_components: " . substr($hpp->custom_components ?? 'null', 0, 300) . "\n";
} else {
    echo "No HPP calculation found\n";
}

// Check flight data
if ($pkg->id_flight_departure) {
    $flight = DB::table('flights')->find($pkg->id_flight_departure);
    echo "\nFlight departure: " . json_encode($flight) . "\n";
}
