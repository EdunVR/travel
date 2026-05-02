<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING booking_addons TABLE STRUCTURE ===\n\n";

try {
    $columns = DB::select('DESCRIBE booking_addons');
    
    echo "Columns in booking_addons table:\n";
    echo str_repeat('-', 80) . "\n";
    printf("%-20s %-15s %-10s %-10s %-10s\n", "Field", "Type", "Null", "Key", "Extra");
    echo str_repeat('-', 80) . "\n";
    
    foreach ($columns as $column) {
        printf("%-20s %-15s %-10s %-10s %-10s\n", 
            $column->Field, 
            $column->Type, 
            $column->Null, 
            $column->Key ?? '', 
            $column->Extra ?? ''
        );
    }
    
    echo "\n=== CHECKING IF TABLE EXISTS ===\n";
    $tableExists = DB::select("SHOW TABLES LIKE 'booking_addons'");
    echo $tableExists ? "✓ Table exists\n" : "✗ Table does NOT exist\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
