<?php

/**
 * Check tofu_data structure and CompanySetting table
 */

require_once 'vendor/autoload.php';

echo "========================================\n";
echo "CHECKING TOFU_DATA STRUCTURE & COMPANY SETTINGS\n";
echo "========================================\n\n";

try {
    // Check productions table structure
    echo "[1] Checking productions table structure...\n";
    $productionColumns = DB::select("DESCRIBE productions");
    
    echo "Productions table columns:\n";
    foreach ($productionColumns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
        if ($column->Field === 'tofu_data') {
            echo "    ✓ tofu_data column found (type: {$column->Type})\n";
        }
    }
    
    echo "\n";
    
    // Check sample tofu_data content
    echo "[2] Checking sample tofu_data content...\n";
    $sampleProduction = DB::table('productions')
        ->where('business_type', 'tofu')
        ->whereNotNull('tofu_data')
        ->first();
    
    if ($sampleProduction) {
        echo "Sample production found: {$sampleProduction->production_code}\n";
        echo "tofu_data content:\n";
        $tofuData = json_decode($sampleProduction->tofu_data, true);
        if ($tofuData) {
            foreach ($tofuData as $key => $value) {
                echo "  - {$key}: {$value}\n";
            }
        } else {
            echo "  tofu_data is not valid JSON or empty\n";
        }
    } else {
        echo "No tofu production with tofu_data found\n";
    }
    
    echo "\n";
    
    // Check CompanySetting table
    echo "[3] Checking CompanySetting table...\n";
    try {
        $companyColumns = DB::select("DESCRIBE company_settings");
        echo "CompanySetting table columns:\n";
        foreach ($companyColumns as $column) {
            echo "  - {$column->Field} ({$column->Type})\n";
        }
        
        // Get sample company setting
        $companySetting = DB::table('company_settings')->first();
        if ($companySetting) {
            echo "\nSample company setting:\n";
            echo "  - company_name: " . ($companySetting->company_name ?? 'NULL') . "\n";
            echo "  - company_logo: " . ($companySetting->company_logo ?? 'NULL') . "\n";
        }
        
    } catch (Exception $e) {
        echo "CompanySetting table not found or error: {$e->getMessage()}\n";
        
        // Try alternative table names
        $alternativeNames = ['company_setting', 'settings', 'company_profiles'];
        foreach ($alternativeNames as $tableName) {
            try {
                $columns = DB::select("DESCRIBE {$tableName}");
                echo "Found alternative table: {$tableName}\n";
                foreach ($columns as $column) {
                    echo "  - {$column->Field} ({$column->Type})\n";
                }
                break;
            } catch (Exception $e2) {
                // Continue to next table name
            }
        }
    }
    
    echo "\n";
    
    // Show all tables to find company-related tables
    echo "[4] Looking for company-related tables...\n";
    $tables = DB::select("SHOW TABLES");
    $companyTables = [];
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (stripos($tableName, 'company') !== false || 
            stripos($tableName, 'setting') !== false ||
            stripos($tableName, 'profile') !== false) {
            $companyTables[] = $tableName;
        }
    }
    
    if (!empty($companyTables)) {
        echo "Found company-related tables:\n";
        foreach ($companyTables as $table) {
            echo "  - {$table}\n";
        }
    } else {
        echo "No company-related tables found\n";
    }
    
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n========================================\n";
echo "STRUCTURE CHECK COMPLETE\n";
echo "========================================\n";

?>