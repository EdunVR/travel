<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Outlet;

$outlets = Outlet::all();
echo "Available Outlets:\n";
foreach ($outlets as $outlet) {
    echo "  ID: {$outlet->id_outlet} - {$outlet->nama_outlet}\n";
}
