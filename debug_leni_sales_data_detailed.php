<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Http\Controllers\SalesReportController;
use Illuminate\Http\Request;

echo "🔍 Debug Leni Sales Data (Detailed)\n";
echo "=" . str_repeat("=", 40) . "\n\n";

$user = User::where('email', 'Leni@gmail.com')->first();
auth()->login($user);

echo "👤 User: {$user->name}\n";

$controller = new SalesReportController();

// Get accessible outlet IDs
$reflection = new ReflectionClass($controller);
$getAccessibleOutletIdsMethod = $reflection->getMethod('getAccessibleOutletIds');
$getAccessibleOutletIdsMethod->setAccessible(true);
$accessibleOutletIds = $getAccessibleOutletIdsMethod->invoke($controller);

echo "🔑 Accessible Outlets: " . json_encode($accessibleOutletIds) . "\n\n";

// Test getData
$request = new Request([
    'outlet_id' => '',
    'start_date' => '2026-02-01',
    'end_date' => '2026-02-03'
]);

$response = $controller->getData($request);
$responseData = json_decode($response->getContent(), true);

if ($responseData['success']) {
    $salesData = $responseData['data'];
    echo "📊 Total Records: " . count($salesData) . "\n\n";
    
    // Group by outlet and source
    $outletSourceData = [];
    foreach ($salesData as $sale) {
        $outletId = $sale['outlet_id'];
        $source = $sale['source'];
        
        if (!isset($outletSourceData[$outletId])) {
            $outletSourceData[$outletId] = [];
        }
        if (!isset($outletSourceData[$outletId][$source])) {
            $outletSourceData[$outletId][$source] = [];
        }
        $outletSourceData[$outletId][$source][] = $sale;
    }
    
    echo "📈 Data by Outlet and Source:\n";
    foreach ($outletSourceData as $outletId => $sources) {
        $outlet = \App\Models\Outlet::find($outletId);
        $outletName = $outlet ? $outlet->nama_outlet : "Unknown ($outletId)";
        
        echo "\n🏢 Outlet: $outletName (ID: $outletId)\n";
        
        // Check if user should have access
        if (!in_array($outletId, $accessibleOutletIds)) {
            echo "   ❌ WARNING: User should NOT have access to this outlet!\n";
        } else {
            echo "   ✅ User has access to this outlet\n";
        }
        
        foreach ($sources as $source => $records) {
            echo "   📋 $source: " . count($records) . " records\n";
            
            // Show sample records
            $sampleRecords = array_slice($records, 0, 3);
            foreach ($sampleRecords as $record) {
                echo "      - {$record['invoice_number']} | {$record['customer']} | " . number_format($record['total_bayar']) . "\n";
            }
            
            if (count($records) > 3) {
                echo "      ... and " . (count($records) - 3) . " more\n";
            }
        }
    }
    
    // Check for inter outlet sales specifically
    echo "\n🔄 Inter Outlet Sales Analysis:\n";
    $interOutletSales = array_filter($salesData, function($sale) {
        return $sale['source'] === 'inter_outlet';
    });
    
    if (!empty($interOutletSales)) {
        echo "   Found " . count($interOutletSales) . " inter outlet sales:\n";
        foreach ($interOutletSales as $sale) {
            echo "      - {$sale['invoice_number']} | Outlet: {$sale['outlet']} (ID: {$sale['outlet_id']}) | Customer: {$sale['customer']}\n";
        }
    } else {
        echo "   No inter outlet sales found\n";
    }
    
} else {
    echo "❌ Error: " . $responseData['message'] . "\n";
}

echo "\n✅ Debug Complete!\n";