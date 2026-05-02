<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PERMISSIONS TABLE STRUCTURE ===\n";
$columns = DB::select('DESCRIBE permissions');
foreach($columns as $col) {
    echo $col->Field . ' - ' . $col->Type . "\n";
}

echo "\n=== SAMPLE PERMISSION RECORD ===\n";
$sample = DB::table('permissions')->first();
if ($sample) {
    foreach($sample as $key => $value) {
        echo "$key: $value\n";
    }
}
?>