<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Recruitment;

echo "=== Checking Recruitment Table Name ===\n\n";

// Method 1: Check what table the Recruitment model uses
echo "1. Checking Recruitment model table name...\n";
$recruitment = new Recruitment();
$tableName = $recruitment->getTable();
echo "   Recruitment model uses table: '$tableName'\n";

// Method 2: Show all tables with 'recruitment' or similar names
echo "\n2. Looking for recruitment-related tables...\n";
try {
    $tables = DB::select('SHOW TABLES');
    $found = false;
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (stripos($tableName, 'recruitment') !== false || 
            stripos($tableName, 'employee') !== false ||
            stripos($tableName, 'karyawan') !== false) {
            echo "   - $tableName\n";
            $found = true;
        }
    }
    if (!$found) {
        echo "   No recruitment-related tables found\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== Analysis Complete ===\n";