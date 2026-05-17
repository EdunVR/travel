<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking jamaah_bookings table structure...\n\n";

$columns = DB::select('SHOW COLUMNS FROM jamaah_bookings');

foreach($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}

echo "\n\nChecking for date-related columns:\n";
foreach($columns as $col) {
    if (stripos($col->Field, 'date') !== false || stripos($col->Field, 'departure') !== false) {
        echo "✓ {$col->Field} ({$col->Type})\n";
    }
}

// Check keberangkatan table
echo "\n\nChecking keberangkatan table structure...\n\n";
$keberangkatanColumns = DB::select('SHOW COLUMNS FROM keberangkatan');
foreach($keberangkatanColumns as $col) {
    if (stripos($col->Field, 'date') !== false || stripos($col->Field, 'departure') !== false || stripos($col->Field, 'tanggal') !== false) {
        echo "✓ {$col->Field} ({$col->Type})\n";
    }
}
