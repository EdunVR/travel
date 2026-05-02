<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Attendance;

echo "=== Checking Attendance Table Name ===\n\n";

// Method 1: Check what table the Attendance model uses
echo "1. Checking Attendance model table name...\n";
$attendance = new Attendance();
$tableName = $attendance->getTable();
echo "   Attendance model uses table: '$tableName'\n";

// Method 2: Check if table exists
echo "\n2. Checking if table exists...\n";
try {
    $exists = DB::select("SHOW TABLES LIKE '$tableName'");
    if (count($exists) > 0) {
        echo "   ✅ Table '$tableName' exists\n";
    } else {
        echo "   ❌ Table '$tableName' does not exist\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking table: " . $e->getMessage() . "\n";
}

// Method 3: Show all tables with 'attendance' or similar names
echo "\n3. Looking for attendance-related tables...\n";
try {
    $tables = DB::select('SHOW TABLES');
    $found = false;
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (stripos($tableName, 'attendance') !== false || 
            stripos($tableName, 'absen') !== false ||
            stripos($tableName, 'kehadiran') !== false) {
            echo "   - $tableName\n";
            $found = true;
        }
    }
    if (!$found) {
        echo "   No attendance-related tables found\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// Method 4: Check table structure if it exists
echo "\n4. Checking table structure...\n";
try {
    $columns = DB::select("DESCRIBE {$attendance->getTable()}");
    echo "   Table '{$attendance->getTable()}' has " . count($columns) . " columns:\n";
    foreach ($columns as $column) {
        $hasPhoto = stripos($column->Field, 'photo') !== false;
        $marker = $hasPhoto ? ' 📸' : '';
        echo "   - {$column->Field} ({$column->Type}){$marker}\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== Analysis Complete ===\n";