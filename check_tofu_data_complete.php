<?php

/**
 * Check complete tofu_data structure and available fields
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "CHECKING COMPLETE TOFU_DATA STRUCTURE\n";
echo "========================================\n\n";

try {
    // Check sample tofu_data content from multiple records
    echo "[1] Checking tofu_data content from multiple records...\n";
    $tofuProductions = DB::table('productions')
        ->where('business_type', 'tofu')
        ->whereNotNull('tofu_data')
        ->limit(5)
        ->get();
    
    if ($tofuProductions->count() > 0) {
        echo "Found {$tofuProductions->count()} tofu production records with data\n\n";
        
        $allFields = [];
        
        foreach ($tofuProductions as $index => $production) {
            $recordNumber = $index + 1;
            echo "Production #{$recordNumber}: {$production->production_code}\n";
            echo "Start Date: {$production->start_date}\n";
            
            $tofuData = json_decode($production->tofu_data, true);
            if ($tofuData && is_array($tofuData)) {
                echo "tofu_data fields:\n";
                foreach ($tofuData as $key => $value) {
                    echo "  - {$key}: {$value}\n";
                    $allFields[$key] = true;
                }
            } else {
                echo "  tofu_data is not valid JSON or empty\n";
            }
            echo "\n";
        }
        
        echo "========================================\n";
        echo "ALL UNIQUE FIELDS FOUND IN TOFU_DATA:\n";
        echo "========================================\n";
        foreach (array_keys($allFields) as $field) {
            echo "  - {$field}\n";
        }
        
    } else {
        echo "No tofu production with tofu_data found\n";
    }
    
    echo "\n";
    
    // Check CompanySetting table
    echo "[2] Checking CompanySetting table...\n";
    try {
        $companySetting = DB::table('company_settings')->first();
        if ($companySetting) {
            echo "CompanySetting found:\n";
            echo "  - company_name: " . ($companySetting->company_name ?? 'NULL') . "\n";
            echo "  - company_logo: " . ($companySetting->company_logo ?? 'NULL') . "\n";
        } else {
            echo "No company_settings record found\n";
        }
        
    } catch (Exception $e) {
        echo "CompanySetting table error: {$e->getMessage()}\n";
        
        // Try alternative table names
        $alternativeNames = ['company_setting', 'settings', 'company_profiles'];
        foreach ($alternativeNames as $tableName) {
            try {
                $setting = DB::table($tableName)->first();
                if ($setting) {
                    echo "Found alternative table: {$tableName}\n";
                    foreach ($setting as $key => $value) {
                        if (stripos($key, 'name') !== false || stripos($key, 'logo') !== false) {
                            echo "  - {$key}: {$value}\n";
                        }
                    }
                    break;
                }
            } catch (Exception $e2) {
                // Continue to next table name
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

echo "\n========================================\n";
echo "COMPLETE STRUCTURE CHECK FINISHED\n";
echo "========================================\n";

?>