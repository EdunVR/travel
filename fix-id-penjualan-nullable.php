<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING id_penjualan TO NULLABLE ===\n\n";

try {
    echo "Making id_penjualan nullable...\n";
    
    DB::statement('ALTER TABLE piutang MODIFY id_penjualan BIGINT UNSIGNED NULL');
    
    echo "✓ id_penjualan is now NULLABLE\n\n";
    
    // Verify
    $columns = DB::select("SHOW COLUMNS FROM piutang WHERE Field = 'id_penjualan'");
    if (!empty($columns)) {
        echo "Verified Null: " . $columns[0]->Null . "\n";
    }
    
    echo "\n=== SUCCESS ===\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
