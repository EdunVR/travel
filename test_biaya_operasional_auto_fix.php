<?php

/**
 * Test Biaya Operasional Auto Fix
 */

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\MonthlyProductionCostController;
use App\Http\Controllers\ProductionController;
use Illuminate\Http\Request;

echo "========================================\n";
echo "TESTING BIAYA OPERASIONAL AUTO FIX\n";
echo "========================================\n\n";

try {
    // Test 1: Check monthly costs data availability
    echo "[TEST 1] Checking monthly costs data availability...\n";
    $currentYear = now()->year;
    $currentMonth = now()->month;
    
    $monthlyData = DB::table('monthly_production_costs')
        ->where('year', $currentYear)
        ->where('month', $currentMonth)
        ->first();
    
    if ($monthlyData) {
        echo "✓ Monthly data found for {$currentYear}-{$currentMonth}\n";
        echo "  - Outlet ID: {$monthlyData->outlet_id}\n";
        echo "  - Electricity Cost: " . number_format($monthlyData->electricity_cost, 0, ',', '.') . "\n";
        echo "  - Water Cost: " . number_format($monthlyData->water_cost, 0, ',', '.') . "\n";
        echo "  - Fuel Cost: " . number_format($monthlyData->fuel_cost, 0, ',', '.') . "\n";
        echo "  - Office Salary Cost: " . number_format($monthlyData->office_salary_cost, 0, ',', '.') . "\n";
        echo "  - Total Cost: " . number_format($monthlyData->total_cost, 0, ',', '.') . "\n";
    } else {
        echo "❌ No monthly data found for current month\n";
        exit(1);
    }
    
    echo "\n";
    
    // Test 2: Test MonthlyProductionCostController data method
    echo "[TEST 2] Testing MonthlyProductionCostController data method...\n";
    $controller = new MonthlyProductionCostController();
    $request = new Request(['outlet_id' => $monthlyData->outlet_id]);
    
    $response = $controller->data($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        echo "✓ Controller data method working\n";
        $current = $responseData['current'];
        
        echo "Current data returned:\n";
        foreach ($current as $key => $value) {
            if (is_numeric($value)) {
                echo "  - {$key}: " . number_format($value, 0, ',', '.') . "\n";
            } else {
                echo "  - {$key}: {$value}\n";
            }
        }
        
        // Check if detailed costs are included
        $requiredFields = ['electricity_cost', 'water_cost', 'fuel_cost', 'office_salary_cost'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($current[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (empty($missingFields)) {
            echo "✅ All required detailed cost fields are present\n";
        } else {
            echo "❌ Missing fields: " . implode(', ', $missingFields) . "\n";
        }
        
    } else {
        echo "❌ Controller data method failed: " . $responseData['message'] . "\n";
    }
    
    echo "\n";
    
    // Test 3: Test ProductionController getMonthlyCosts method
    echo "[TEST 3] Testing ProductionController getMonthlyCosts method...\n";
    $productionController = new ProductionController();
    $request = new Request(['outlet_id' => $monthlyData->outlet_id, 'limit' => 1]);
    
    $response = $productionController->getMonthlyCosts($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success'] && !empty($responseData['data'])) {
        echo "✓ ProductionController getMonthlyCosts working\n";
        $data = $responseData['data'][0];
        
        echo "Monthly costs data returned:\n";
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                echo "  - {$key}: " . number_format($value, 0, ',', '.') . "\n";
            } else {
                echo "  - {$key}: {$value}\n";
            }
        }
        
        // Check if detailed costs are included
        $requiredFields = ['electricity_cost', 'water_cost', 'fuel_cost', 'office_salary_cost'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (empty($missingFields)) {
            echo "✅ All required detailed cost fields are present\n";
        } else {
            echo "❌ Missing fields: " . implode(', ', $missingFields) . "\n";
        }
        
    } else {
        echo "❌ ProductionController getMonthlyCosts failed\n";
    }
    
    echo "\n";
    
    // Test 4: Simulate auto calculation
    echo "[TEST 4] Simulating auto calculation...\n";
    
    if (isset($current) && isset($current['electricity_cost'])) {
        $workingDays = 25;
        $officePercentage = 30;
        
        echo "Simulation parameters:\n";
        echo "  - Working days: {$workingDays}\n";
        echo "  - Office percentage: {$officePercentage}%\n\n";
        
        $dailyElectricity = $current['electricity_cost'] / $workingDays;
        $dailyWater = $current['water_cost'] / $workingDays;
        $dailyFuel = $current['fuel_cost'] / $workingDays;
        $dailyOfficeBase = $current['office_salary_cost'] / $workingDays;
        $dailyOffice = $dailyOfficeBase * ($officePercentage / 100);
        
        $totalDaily = $dailyElectricity + $dailyWater + $dailyFuel + $dailyOffice;
        
        echo "Daily operational costs calculation:\n";
        echo "  - Daily Electricity: Rp " . number_format($dailyElectricity, 0, ',', '.') . "\n";
        echo "  - Daily Water: Rp " . number_format($dailyWater, 0, ',', '.') . "\n";
        echo "  - Daily Fuel: Rp " . number_format($dailyFuel, 0, ',', '.') . "\n";
        echo "  - Daily Office (base): Rp " . number_format($dailyOfficeBase, 0, ',', '.') . "\n";
        echo "  - Daily Office ({$officePercentage}%): Rp " . number_format($dailyOffice, 0, ',', '.') . "\n";
        echo "  - TOTAL DAILY: Rp " . number_format($totalDaily, 0, ',', '.') . "\n";
        
        if ($totalDaily > 0) {
            echo "✅ Auto calculation simulation successful\n";
        } else {
            echo "❌ Auto calculation simulation failed - total is 0\n";
        }
    } else {
        echo "❌ Cannot simulate - missing detailed cost data\n";
    }
    
    echo "\n";
    
    // Test 5: Check routes
    echo "[TEST 5] Checking routes...\n";
    try {
        $route1 = route('admin.produksi.produksi.monthly-production-costs.data');
        echo "✓ Route 1 available: {$route1}\n";
    } catch (Exception $e) {
        echo "❌ Route 1 not available: {$e->getMessage()}\n";
    }
    
    try {
        $route2 = route('admin.produksi.produksi.monthly-costs.get');
        echo "✓ Route 2 available: {$route2}\n";
    } catch (Exception $e) {
        echo "❌ Route 2 not available: {$e->getMessage()}\n";
    }
    
    echo "\n========================================\n";
    echo "✅ BIAYA OPERASIONAL AUTO FIX TEST COMPLETE\n";
    echo "========================================\n";
    
    echo "\nFIXES APPLIED:\n";
    echo "✓ Updated MonthlyProductionCostController->data() to return detailed costs\n";
    echo "✓ Updated ProductionController->getMonthlyCosts() to return detailed costs\n";
    echo "✓ Both methods now return electricity_cost, water_cost, fuel_cost, office_salary_cost\n";
    
    echo "\nREADY TO TEST:\n";
    echo "1. Visit production page\n";
    echo "2. Click 'Buat Produksi Baru'\n";
    echo "3. Select outlet with monthly cost data (Outlet ID: {$monthlyData->outlet_id})\n";
    echo "4. In Biaya Operasional section, click 'Auto dari Biaya Bulanan'\n";
    echo "5. Verify that Listrik, Air, Bahan Bakar, and Gaji Office show correct values\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: {$e->getMessage()}\n";
    echo "Trace: {$e->getTraceAsString()}\n";
}

?>