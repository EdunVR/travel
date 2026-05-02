<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Penjualan;

echo "=== CHECK PENJUALAN FIELDS ===\n\n";

$penjualan = Penjualan::find(1507);

if ($penjualan) {
    echo "Penjualan ID: 1507\n";
    echo "Fields:\n";
    foreach ($penjualan->getAttributes() as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
} else {
    echo "Penjualan not found\n";
}
