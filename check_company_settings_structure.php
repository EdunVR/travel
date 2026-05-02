<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CHECKING COMPANY SETTINGS STRUCTURE ===\n\n";

try {
    // Check if table exists
    if (Schema::hasTable('company_settings')) {
        echo "✓ company_settings table exists\n";
        
        // Get table structure
        $columns = Schema::getColumnListing('company_settings');
        echo "\nTable columns:\n";
        foreach ($columns as $column) {
            echo "  - $column\n";
        }
        
        // Get sample data
        $settings = DB::table('company_settings')->limit(5)->get();
        echo "\nSample data:\n";
        foreach ($settings as $setting) {
            echo "  " . json_encode($setting) . "\n";
        }
        
    } else {
        echo "✗ company_settings table does not exist\n";
    }
    
    // Check for alternative table names
    $tables = DB::select('SHOW TABLES');
    echo "\nAll tables containing 'company' or 'setting':\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (strpos(strtolower($tableName), 'company') !== false || 
            strpos(strtolower($tableName), 'setting') !== false) {
            echo "  - $tableName\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}