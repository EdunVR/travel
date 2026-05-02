<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ROLE_PERMISSIONS TABLE STRUCTURE ===\n";
try {
    $columns = DB::select('DESCRIBE role_permissions');
    foreach($columns as $col) {
        echo $col->Field . ' - ' . $col->Type . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== SAMPLE ROLE_PERMISSIONS RECORD ===\n";
try {
    $sample = DB::table('role_permissions')->first();
    if ($sample) {
        foreach($sample as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "No data in role_permissions table\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>