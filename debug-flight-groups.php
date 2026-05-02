<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Flight;

echo "=== DEBUG FLIGHT GROUPS ===\n\n";

// Get all flights with group codes
$groupedFlights = Flight::whereNotNull('flight_group_code')
    ->orderBy('flight_group_code')
    ->orderBy('flight_direction')
    ->get();

echo "Total flights with group codes: " . $groupedFlights->count() . "\n\n";

// Group by flight_group_code
$groups = $groupedFlights->groupBy('flight_group_code');

foreach ($groups as $groupCode => $flights) {
    echo "Group Code: $groupCode\n";
    echo str_repeat('-', 50) . "\n";
    
    foreach ($flights as $flight) {
        echo "  ID: {$flight->id}\n";
        echo "  Direction: {$flight->flight_direction}\n";
        echo "  Airline: {$flight->airline_name}\n";
        echo "  Flight Number: {$flight->flight_number}\n";
        echo "  Route: {$flight->departure_airport} → {$flight->arrival_airport}\n";
        echo "  Departure: " . ($flight->departure_time ? $flight->departure_time->format('Y-m-d H:i') : 'N/A') . "\n";
        echo "  Arrival: " . ($flight->arrival_time ? $flight->arrival_time->format('Y-m-d H:i') : 'N/A') . "\n";
        echo "  Transit Info: " . ($flight->transit_info ? json_encode($flight->transit_info) : 'None') . "\n";
        echo "  Has Transit: " . ($flight->hasTransit() ? 'Yes' : 'No') . "\n";
        echo "\n";
    }
    echo "\n";
}

echo "\n=== TESTING getFlightGroups API ===\n\n";

// Simulate the API call
$groups = Flight::whereNotNull('flight_group_code')
    ->where('flight_direction', 'departure')
    ->orderBy('flight_group_code')
    ->get()
    ->map(function($flight) {
        $returnFlight = Flight::where('flight_group_code', $flight->flight_group_code)
            ->where('flight_direction', 'return')
            ->first();

        return [
            'code' => $flight->flight_group_code,
            'label' => $flight->flight_group_code . ' - ' . 
                      $flight->airline_name . ' ' . $flight->flight_number . 
                      ' (' . $flight->departure_airport . ' → ' . $flight->arrival_airport . ')' .
                      ($returnFlight ? ' & ' . $returnFlight->flight_number . 
                      ' (' . $returnFlight->departure_airport . ' → ' . $returnFlight->arrival_airport . ')' : ''),
            'departure_flight_id' => $flight->id,
            'return_flight_id' => $returnFlight ? $returnFlight->id : null,
            'departure_has_transit' => $flight->hasTransit(),
            'return_has_transit' => $returnFlight ? $returnFlight->hasTransit() : false,
        ];
    });

echo "Flight Groups for Dropdown:\n";
echo json_encode($groups, JSON_PRETTY_PRINT);
echo "\n";
