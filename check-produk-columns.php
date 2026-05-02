<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking produk Table Structure ===\n\n";

try {
    $columns = DB::select("SHOW COLUMNS FROM produk");
    
    echo "Columns in produk table:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type}\n";
    }
    
    echo "\n\nChecking hpp_produk table:\n";
    $hppColumns = DB::select("SHOW COLUMNS FROM hpp_produk");
    
    echo "Columns in hpp_produk table:\n";
    foreach ($hppColumns as $column) {
        echo "  - {$column->Field}: {$column->Type}\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}
