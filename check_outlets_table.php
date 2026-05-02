<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING OUTLETS TABLE STRUCTURE ===\n\n";

try {
    // Check outlets table columns
    $columns = DB::select('SHOW COLUMNS FROM outlets');
    echo "Outlets table columns:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\nSample outlets data:\n";
    $outlets = DB::table('outlets')->limit(3)->get();
    foreach ($outlets as $outlet) {
        echo "- ID: " . (isset($outlet->id) ? $outlet->id : 'N/A') . 
             ", ID_OUTLET: " . (isset($outlet->id_outlet) ? $outlet->id_outlet : 'N/A') . 
             ", Name: " . (isset($outlet->nama_outlet) ? $outlet->nama_outlet : 'N/A') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}