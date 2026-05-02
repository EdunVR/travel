<?php

/**
 * Check monthly costs data availability and structure
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "CHECKING MONTHLY COSTS DATA\n";
echo "========================================\n\n";

try {
    // Check monthly production costs table structure
    echo "[1] Checking monthly_production_costs table structure...\n";
    try {
        $columns = DB::select("DESCRIBE monthly_production_costs");
        echo "✓ monthly_production_costs table found\n";
        echo "Columns:\n";
        foreach ($columns as $column) {
            echo "  - {$column->Field} ({$column->Type})\n";
        }
    } catch (Exception $e) {
        echo "❌ monthly_production_costs table not found: {$e->getMessage()}\n";
        
        // Try alternative table names
        $alternativeNames = ['monthly_costs', 'biaya_bulanan', 'production_monthly_costs'];
        foreach ($alternativeNames as $tableName) {
            try {
                $columns = DB::select("DESCRIBE {$tableName}");
                echo "✓ Found alternative table: {$tableName}\n";
                echo "Columns:\n";
                foreach ($columns as $column) {
                    echo "  - {$column->Field} ({$column->Type})\n";
                }
                break;
            } catch (Exception $e2) {
                continue;
            }
        }
    }
    
    echo "\n";
    
    // Check current month data
    echo "[2] Checking current month data...\n";
    $currentMonth = now()->format('Y-m');
    echo "Current month: {$currentMonth}\n";
    
    try {
        $monthlyData = DB::table('monthly_production_costs')
            ->whereRaw("DATE_FORMAT(month, '%Y-%m') = ?", [$currentMonth])
            ->get();
        
        if ($monthlyData->count() > 0) {
            echo "✓ Found {$monthlyData->count()} records for current month\n";
            foreach ($monthlyData as $data) {
                echo "Record details:\n";
                echo "  - ID: {$data->id}\n";
                echo "  - Month: {$data->month}\n";
                echo "  - Outlet ID: " . ($data->outlet_id ?? 'NULL') . "\n";
                echo "  - Listrik: " . ($data->listrik ?? 'NULL') . "\n";
                echo "  - Air: " . ($data->air ?? 'NULL') . "\n";
                echo "  - Bahan Bakar: " . ($data->bahan_bakar ?? 'NULL') . "\n";
                echo "  - Gaji Office: " . ($data->gaji_office ?? 'NULL') . "\n";
                echo "  - Created: " . ($data->created_at ?? 'NULL') . "\n";
                echo "\n";
            }
        } else {
            echo "❌ No data found for current month ({$currentMonth})\n";
            
            // Check if there's any data at all
            $anyData = DB::table('monthly_production_costs')->limit(5)->get();
            if ($anyData->count() > 0) {
                echo "Available data (latest 5 records):\n";
                foreach ($anyData as $data) {
                    echo "  - Month: {$data->month}, Outlet: " . ($data->outlet_id ?? 'NULL') . "\n";
                }
            } else {
                echo "❌ No data found in monthly_production_costs table at all\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error querying monthly_production_costs: {$e->getMessage()}\n";
    }
    
    echo "\n";
    
    // Check outlets for context
    echo "[3] Checking available outlets...\n";
    try {
        $outlets = DB::table('outlets')->select('id', 'nama_outlet')->get();
        echo "Available outlets:\n";
        foreach ($outlets as $outlet) {
            echo "  - ID: {$outlet->id}, Name: {$outlet->nama_outlet}\n";
        }
    } catch (Exception $e) {
        echo "❌ Error querying outlets: {$e->getMessage()}\n";
    }
    
    echo "\n";
    
    // Check if there are any monthly costs for any month
    echo "[4] Checking all monthly costs data...\n";
    try {
        $allMonthlyData = DB::table('monthly_production_costs')
            ->select('month', 'outlet_id', 'listrik', 'air', 'bahan_bakar', 'gaji_office')
            ->orderBy('month', 'desc')
            ->limit(10)
            ->get();
        
        if ($allMonthlyData->count() > 0) {
            echo "Latest 10 monthly cost records:\n";
            foreach ($allMonthlyData as $data) {
                echo "  - {$data->month} | Outlet: " . ($data->outlet_id ?? 'NULL') . 
                     " | Listrik: " . ($data->listrik ?? '0') . 
                     " | Air: " . ($data->air ?? '0') . 
                     " | Bahan Bakar: " . ($data->bahan_bakar ?? '0') . 
                     " | Gaji Office: " . ($data->gaji_office ?? '0') . "\n";
            }
        } else {
            echo "❌ No monthly cost data found\n";
        }
    } catch (Exception $e) {
        echo "❌ Error querying all monthly costs: {$e->getMessage()}\n";
    }
    
} catch (Exception $e) {
    echo "❌ General error: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

echo "\n========================================\n";
echo "MONTHLY COSTS DATA CHECK COMPLETE\n";
echo "========================================\n";

?>