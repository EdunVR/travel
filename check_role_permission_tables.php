<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING AVAILABLE TABLES ===\n";
$tables = DB::select('SHOW TABLES');
$tableColumn = 'Tables_in_' . env('DB_DATABASE');

foreach($tables as $table) {
    $tableName = $table->$tableColumn;
    if (strpos($tableName, 'role') !== false || strpos($tableName, 'permission') !== false) {
        echo "- {$tableName}\n";
    }
}

echo "\n=== CHECKING PERMISSION ROLE TABLE STRUCTURE ===\n";
try {
    $columns = DB::select('DESCRIBE permission_role');
    foreach($columns as $col) {
        echo $col->Field . ' - ' . $col->Type . "\n";
    }
} catch (Exception $e) {
    echo "permission_role table not found\n";
}

echo "\n=== SAMPLE PERMISSION ROLE RECORD ===\n";
try {
    $sample = DB::table('permission_role')->first();
    if ($sample) {
        foreach($sample as $key => $value) {
            echo "$key: $value\n";
        }
    }
} catch (Exception $e) {
    echo "No permission_role table or data\n";
}
?>