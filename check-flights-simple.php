<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Flight;

$flights = Flight::all();
echo "Total Flights: " . $flights->count() . "\n\n";

foreach ($flights->take(10) as $f) {
    echo "ID: {$f->id}\n";
    echo "Airline: {$f->airline_name} {$f->flight_number}\n";
    echo "Group Code: " . ($f->flight_group_code ?? 'NULL') . "\n";
    echo "Direction: " . ($f->flight_direction ?? 'NULL') . "\n";
    echo "Transit Info: " . ($f->transit_info ? json_encode($f->transit_info) : 'NULL') . "\n";
    echo str_repeat('-', 40) . "\n";
}
