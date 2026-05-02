<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ROOM TYPE COLUMNS ===\n\n";

$cols = DB::select("SHOW COLUMNS FROM travel_packages WHERE Field LIKE '%room_type%'");

foreach ($cols as $c) {
    echo "Field: {$c->Field}\n";
    echo "  Type: {$c->Type}\n";
    echo "  Null: {$c->Null}\n";
    echo "  Default: " . ($c->Default ?? 'NULL') . "\n";
    echo "  Key: {$c->Key}\n\n";
}
