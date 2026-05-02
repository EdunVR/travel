<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING HPP_PRODUK TABLE STRUCTURE ===\n\n";

try {
    // Check table structure
    $columns = DB::select('DESCRIBE hpp_produk');
    
    echo "Columns in hpp_produk table:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    echo "\n";
    
    // Check sample data
    $sampleData = DB::table('hpp_produk')
        ->where('id_produk', 24)
        ->orderBy('created_at', 'asc')
        ->get();
    
    echo "Sample data for produk ID 24:\n";
    foreach ($sampleData as $row) {
        echo "   Row: " . json_encode($row) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";