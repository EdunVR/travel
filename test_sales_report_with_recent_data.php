<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Outlet;
use App\Http\Controllers\SalesReportController;
use Illuminate\Http\Request;

echo "🔍 Testing Sales Report with Recent Data\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Login as Super Administrator
$user = User::where('email', 'superadmin@gmail.com')->first();
auth()->login($user);

echo "👤 Testing with user: {$user->name} ({$user->email})\n";
echo "-" . str_repeat("-", 40) . "\n";

// Create controller instance
$controller = new SalesReportController();

// Test getAccessibleOutletIds method
$reflection = new ReflectionClass($controller);
$getAccessibleOutletIdsMethod = $reflection->getMethod('getAccessibleOutletIds');
$getAccessibleOutletIdsMethod->setAccessible(true);
$accessibleOutletIds = $getAccessibleOutletIdsMethod->invoke($controller);

echo "🔑 Accessible outlet IDs: " . json_encode($accessibleOutletIds) . "\n";

// Test getData with recent date range and empty outlet filter
echo "\n📊 Testing getData with recent date range and empty outlet filter...\n";
$request = new Request([
    'outlet_id' => '', // Empty = all accessible outlets
    'start_date' => '2026-02-01',
    'end_date' => '2026-02-03'
]);

try {
    $response = $controller->getData($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        $salesData = $responseData['data'];
        echo "   ✅ Data loaded successfully: " . count($salesData) . " records\n";
        
        // Check outlet distribution
        $outletDistribution = [];
        $sourceDistribution = [];
        
        foreach ($salesData as $sale) {
            $outletId = $sale['outlet_id'];
            $source = $sale['source'];
            
            if (!isset($outletDistribution[$outletId])) {
                $outletDistribution[$outletId] = 0;
            }
            $outletDistribution[$outletId]++;
            
            if (!isset($sourceDistribution[$source])) {
                $sourceDistribution[$source] = 0;
            }
            $sourceDistribution[$source]++;
        }
        
        echo "\n   📈 Outlet distribution:\n";
        foreach ($outletDistribution as $outletId => $count) {
            $outlet = Outlet::find($outletId);
            $outletName = $outlet ? $outlet->nama_outlet : "Unknown ($outletId)";
            echo "      - $outletName: $count records\n";
            
            // Check if this outlet is in user's accessible outlets
            if (!in_array($outletId, $accessibleOutletIds)) {
                echo "        ❌ WARNING: User should not have access to outlet $outletId!\n";
            } else {
                echo "        ✅ Outlet access OK\n";
            }
        }
        
        echo "\n   📊 Source distribution:\n";
        foreach ($sourceDistribution as $source => $count) {
            echo "      - $source: $count records\n";
        }
        
        // Show sample records
        echo "\n   📋 Sample records:\n";
        $sampleRecords = array_slice($salesData, 0, 3);
        foreach ($sampleRecords as $record) {
            echo "      - {$record['source']}: {$record['invoice_number']} - {$record['outlet']} - " . number_format($record['total_bayar']) . "\n";
        }
        
        // Check if any unauthorized outlets appear
        $unauthorizedOutlets = array_diff(array_keys($outletDistribution), $accessibleOutletIds);
        if (!empty($unauthorizedOutlets)) {
            echo "\n   ❌ CRITICAL: Found data from unauthorized outlets: " . json_encode($unauthorizedOutlets) . "\n";
        } else {
            echo "\n   ✅ All data is from authorized outlets only\n";
        }
    } else {
        echo "   ❌ Error: " . $responseData['message'] . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

// Test with specific outlet filter
echo "\n📊 Testing getData with specific outlet filter (Outlet ID: 2)...\n";
$request = new Request([
    'outlet_id' => '2', // Pelindung Hewan
    'start_date' => '2026-02-01',
    'end_date' => '2026-02-03'
]);

try {
    $response = $controller->getData($request);
    $responseData = json_decode($response->getContent(), true);
    
    if ($responseData['success']) {
        $salesData = $responseData['data'];
        echo "   ✅ Data loaded successfully: " . count($salesData) . " records\n";
        
        // Verify all records are from outlet 2
        $wrongOutletCount = 0;
        foreach ($salesData as $sale) {
            if ($sale['outlet_id'] != 2) {
                $wrongOutletCount++;
            }
        }
        
        if ($wrongOutletCount > 0) {
            echo "   ❌ WARNING: $wrongOutletCount records from wrong outlet!\n";
        } else {
            echo "   ✅ All records are from the correct outlet (ID: 2)\n";
        }
        
        // Show sample records
        echo "   📋 Sample records:\n";
        $sampleRecords = array_slice($salesData, 0, 3);
        foreach ($sampleRecords as $record) {
            echo "      - {$record['source']}: {$record['invoice_number']} - {$record['outlet']} - " . number_format($record['total_bayar']) . "\n";
        }
    } else {
        echo "   ❌ Error: " . $responseData['message'] . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n✅ Sales Report Test Complete!\n";