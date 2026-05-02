<?php
/**
 * Verify HPP Custom Components Columns
 * 
 * This script checks if the required columns exist in hpp_calculations table
 * Run: php verify-hpp-columns.php
 */

require __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "========================================\n";
echo "HPP Custom Components - Column Verification\n";
echo "========================================\n\n";

try {
    // Get table columns
    $columns = DB::select("DESCRIBE hpp_calculations");
    
    $requiredColumns = [
        'custom_components',
        'component_payment_status',
        'component_hutang_amount'
    ];
    
    $existingColumns = array_column($columns, 'Field');
    
    echo "Checking required columns...\n\n";
    
    $allExist = true;
    foreach ($requiredColumns as $col) {
        $exists = in_array($col, $existingColumns);
        $status = $exists ? '✓ EXISTS' : '✗ MISSING';
        $color = $exists ? "\033[32m" : "\033[31m";
        echo "{$color}{$status}\033[0m - {$col}\n";
        
        if (!$exists) {
            $allExist = false;
        }
    }
    
    echo "\n========================================\n";
    
    if ($allExist) {
        echo "\033[32m✓ SUCCESS!\033[0m All required columns exist.\n";
        echo "========================================\n\n";
        
        echo "You can now:\n";
        echo "1. Open HPP modal and add custom components\n";
        echo "2. Save HPP without errors\n";
        echo "3. Custom components will be saved with original names\n";
        echo "4. Generate RAB with individual items for each component\n\n";
        
        // Test if we can query the columns
        echo "Testing column access...\n";
        $test = DB::table('hpp_calculations')->first();
        if ($test) {
            echo "✓ Can read custom_components: " . ($test->custom_components ?? 'NULL') . "\n";
            echo "✓ Can read component_payment_status: " . ($test->component_payment_status ?? 'NULL') . "\n";
            echo "✓ Can read component_hutang_amount: " . ($test->component_hutang_amount ?? 'NULL') . "\n";
        } else {
            echo "ℹ No HPP records found (table is empty)\n";
        }
        
    } else {
        echo "\033[31m✗ FAILED!\033[0m Some columns are missing.\n";
        echo "========================================\n\n";
        
        echo "Please run the SQL migration:\n";
        echo "1. Open phpMyAdmin or MySQL client\n";
        echo "2. Run: add-custom-components-column.sql\n";
        echo "   OR\n";
        echo "3. Run: run-hpp-migration.bat\n\n";
        
        echo "SQL to run:\n";
        echo "---\n";
        echo "ALTER TABLE `hpp_calculations` \n";
        echo "ADD COLUMN `custom_components` JSON NULL AFTER `contingency`,\n";
        echo "ADD COLUMN `component_payment_status` JSON NULL AFTER `custom_components`,\n";
        echo "ADD COLUMN `component_hutang_amount` JSON NULL AFTER `component_payment_status`;\n";
        echo "---\n\n";
    }
    
    // Show all columns for reference
    echo "\nAll columns in hpp_calculations table:\n";
    echo "---\n";
    foreach ($columns as $col) {
        $type = $col->Type;
        $null = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
        $key = $col->Key ? " [{$col->Key}]" : '';
        echo "- {$col->Field} ({$type}) {$null}{$key}\n";
    }
    echo "---\n\n";
    
} catch (\Exception $e) {
    echo "\033[31m✗ ERROR!\033[0m " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "1. Database connection is working\n";
    echo "2. hpp_calculations table exists\n";
    echo "3. You have SELECT permissions\n\n";
}

echo "========================================\n";
echo "Verification complete.\n";
echo "========================================\n";
