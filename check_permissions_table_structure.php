<?php

require_once 'vendor/autoload.php';

echo "=== CHECK PERMISSIONS TABLE STRUCTURE ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "Permissions table structure:\n";
    $columns = DB::select('DESCRIBE permissions');
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";