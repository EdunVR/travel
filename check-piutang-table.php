<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING piutang TABLE STRUCTURE ===\n\n";

try {
    $columns = DB::select('DESCRIBE piutang');
    
    echo "Columns in piutang table:\n";
    echo str_repeat('-', 80) . "\n";
    printf("%-25s %-20s %-10s %-10s %-10s\n", "Field", "Type", "Null", "Key", "Extra");
    echo str_repeat('-', 80) . "\n";
    
    foreach ($columns as $column) {
        printf("%-25s %-20s %-10s %-10s %-10s\n", 
            $column->Field, 
            $column->Type, 
            $column->Null, 
            $column->Key ?? '', 
            $column->Extra ?? ''
        );
    }
    
    echo "\n=== CHECKING IF id_jamaah_booking EXISTS ===\n";
    $hasColumn = DB::select("SHOW COLUMNS FROM piutang LIKE 'id_jamaah_booking'");
    echo $hasColumn ? "✓ Column EXISTS\n" : "✗ Column does NOT exist\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
