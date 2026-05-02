<?php

/**
 * Debug raw tofu_data content
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "DEBUG RAW TOFU_DATA CONTENT\n";
echo "========================================\n\n";

try {
    // Get raw tofu_data content
    $tofuProductions = DB::table('productions')
        ->select('production_code', 'start_date', 'tofu_data', 'business_type')
        ->where('business_type', 'tofu')
        ->limit(3)
        ->get();
    
    if ($tofuProductions->count() > 0) {
        echo "Found {$tofuProductions->count()} tofu production records\n\n";
        
        foreach ($tofuProductions as $index => $production) {
            $recordNumber = $index + 1;
            echo "Production #{$recordNumber}:\n";
            echo "  - Code: {$production->production_code}\n";
            echo "  - Date: {$production->start_date}\n";
            echo "  - Business Type: {$production->business_type}\n";
            echo "  - tofu_data (raw): ";
            var_dump($production->tofu_data);
            echo "  - tofu_data (length): " . strlen($production->tofu_data ?? '') . "\n";
            echo "  - tofu_data (type): " . gettype($production->tofu_data) . "\n";
            
            if ($production->tofu_data) {
                echo "  - JSON decode attempt: ";
                // First decode to remove outer quotes
                $firstDecode = json_decode($production->tofu_data, true);
                if (is_string($firstDecode)) {
                    // Double encoded - decode again
                    $decoded = json_decode($firstDecode, true);
                    echo "SUCCESS (double-encoded)\n";
                } else {
                    $decoded = $firstDecode;
                    echo "SUCCESS (single-encoded)\n";
                }
                
                if ($decoded && is_array($decoded)) {
                    foreach ($decoded as $key => $value) {
                        echo "    * {$key}: {$value}\n";
                    }
                } else {
                    echo "FAILED - JSON Error: " . json_last_error_msg() . "\n";
                }
            }
            echo "\n";
        }
        
    } else {
        echo "No tofu production records found\n";
    }
    
    // Also check all productions with any tofu_data
    echo "========================================\n";
    echo "CHECKING ALL PRODUCTIONS WITH tofu_data\n";
    echo "========================================\n";
    
    $allWithTofuData = DB::table('productions')
        ->select('production_code', 'business_type', 'tofu_data')
        ->whereNotNull('tofu_data')
        ->where('tofu_data', '!=', '')
        ->limit(5)
        ->get();
    
    if ($allWithTofuData->count() > 0) {
        echo "Found {$allWithTofuData->count()} productions with tofu_data\n\n";
        
        foreach ($allWithTofuData as $production) {
            echo "Code: {$production->production_code} (Type: {$production->business_type})\n";
            echo "tofu_data: ";
            var_dump($production->tofu_data);
            echo "\n";
        }
    } else {
        echo "No productions with tofu_data found\n";
    }
    
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

echo "\n========================================\n";
echo "DEBUG FINISHED\n";
echo "========================================\n";

?>