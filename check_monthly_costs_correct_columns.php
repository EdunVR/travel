<?php

/**
 * Check monthly costs data with correct column names
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "CHECKING MONTHLY COSTS WITH CORRECT COLUMNS\n";
echo "========================================\n\n";

try {
    // Check current month data with correct column names
    echo "[1] Checking current month data (January 2026)...\n";
    $currentYear = now()->year;
    $currentMonth = now()->month;
    echo "Current year: {$currentYear}, month: {$currentMonth}\n";
    
    $monthlyData = DB::table('monthly_production_costs')
        ->where('year', $currentYear)
        ->where('month', $currentMonth)
        ->get();
    
    if ($monthlyData->count() > 0) {
        echo "✓ Found {$monthlyData->count()} records for current month\n";
        foreach ($monthlyData as $data) {
            echo "Record details:\n";
            echo "  - ID: {$data->id}\n";
            echo "  - Year: {$data->year}\n";
            echo "  - Month: {$data->month}\n";
            echo "  - Outlet ID: {$data->outlet_id}\n";
            echo "  - Electricity Cost: {$data->electricity_cost}\n";
            echo "  - Water Cost: {$data->water_cost}\n";
            echo "  - Fuel Cost: {$data->fuel_cost}\n";
            echo "  - Office Salary Cost: {$data->office_salary_cost}\n";
            echo "  - Other Costs: {$data->other_costs}\n";
            echo "  - Total Cost: {$data->total_cost}\n";
            echo "  - Created: {$data->created_at}\n";
            echo "\n";
        }
    } else {
        echo "❌ No data found for current month ({$currentYear}-{$currentMonth})\n";
        
        // Check what data is available
        echo "\n[2] Checking all available data...\n";
        $allData = DB::table('monthly_production_costs')
            ->select('year', 'month', 'outlet_id', 'electricity_cost', 'water_cost', 'fuel_cost', 'office_salary_cost')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(10)
            ->get();
        
        if ($allData->count() > 0) {
            echo "Available monthly cost records:\n";
            foreach ($allData as $data) {
                echo "  - {$data->year}-{$data->month} | Outlet: {$data->outlet_id} | " .
                     "Electricity: {$data->electricity_cost} | " .
                     "Water: {$data->water_cost} | " .
                     "Fuel: {$data->fuel_cost} | " .
                     "Office Salary: {$data->office_salary_cost}\n";
            }
        } else {
            echo "❌ No monthly cost data found at all\n";
        }
    }
    
    echo "\n";
    
    // Check outlets table structure
    echo "[3] Checking outlets table structure...\n";
    try {
        $outletColumns = DB::select("DESCRIBE outlets");
        echo "Outlets table columns:\n";
        foreach ($outletColumns as $column) {
            echo "  - {$column->Field} ({$column->Type})\n";
        }
        
        // Get sample outlet data
        $outlets = DB::table('outlets')->limit(3)->get();
        if ($outlets->count() > 0) {
            echo "\nSample outlets:\n";
            foreach ($outlets as $outlet) {
                $outletArray = (array) $outlet;
                $idField = isset($outletArray['id']) ? $outletArray['id'] : 
                          (isset($outletArray['outlet_id']) ? $outletArray['outlet_id'] : 'N/A');
                $nameField = isset($outletArray['nama_outlet']) ? $outletArray['nama_outlet'] : 
                            (isset($outletArray['name']) ? $outletArray['name'] : 'N/A');
                echo "  - ID: {$idField}, Name: {$nameField}\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error checking outlets: {$e->getMessage()}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

echo "\n========================================\n";
echo "MONTHLY COSTS CHECK COMPLETE\n";
echo "========================================\n";

?>