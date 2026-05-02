<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Outlet;
use App\Http\Controllers\SalesReportController;
use Illuminate\Http\Request;

echo "🔍 Testing Sales Report Outlet Filter Debug\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test with different users
$testUsers = [
    ['email' => 'superadmin@example.com', 'name' => 'Super Admin'],
    ['email' => 'admin@example.com', 'name' => 'Admin'],
    ['email' => 'user@example.com', 'name' => 'Regular User']
];

foreach ($testUsers as $userData) {
    $user = User::where('email', $userData['email'])->first();
    
    if (!$user) {
        echo "⚠️ User {$userData['email']} not found, skipping...\n\n";
        continue;
    }
    
    echo "👤 Testing with user: {$user->name} ({$user->email})\n";
    echo "-" . str_repeat("-", 40) . "\n";
    
    // Login as this user
    auth()->login($user);
    
    // Create controller instance
    $controller = new SalesReportController();
    
    // Test getUserOutlets method
    $reflection = new ReflectionClass($controller);
    $getUserOutletsMethod = $reflection->getMethod('getUserOutlets');
    $getUserOutletsMethod->setAccessible(true);
    $userOutlets = $getUserOutletsMethod->invoke($controller);
    
    echo "🏢 User outlets: " . $userOutlets->count() . " outlets\n";
    foreach ($userOutlets as $outlet) {
        echo "   - {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
    }
    
    // Test getAccessibleOutletIds method
    $getAccessibleOutletIdsMethod = $reflection->getMethod('getAccessibleOutletIds');
    $getAccessibleOutletIdsMethod->setAccessible(true);
    $accessibleOutletIds = $getAccessibleOutletIdsMethod->invoke($controller);
    
    echo "🔑 Accessible outlet IDs: " . json_encode($accessibleOutletIds) . "\n";
    
    // Test getData with empty outlet filter
    echo "📊 Testing getData with empty outlet filter...\n";
    $request = new Request([
        'outlet_id' => '', // Empty = all accessible outlets
        'start_date' => '2024-01-01',
        'end_date' => '2024-12-31'
    ]);
    
    try {
        $response = $controller->getData($request);
        $responseData = json_decode($response->getContent(), true);
        
        if ($responseData['success']) {
            $salesData = $responseData['data'];
            echo "   ✅ Data loaded successfully: " . count($salesData) . " records\n";
            
            // Check outlet distribution
            $outletDistribution = [];
            foreach ($salesData as $sale) {
                $outletId = $sale['outlet_id'];
                if (!isset($outletDistribution[$outletId])) {
                    $outletDistribution[$outletId] = 0;
                }
                $outletDistribution[$outletId]++;
            }
            
            echo "   📈 Outlet distribution:\n";
            foreach ($outletDistribution as $outletId => $count) {
                $outlet = Outlet::find($outletId);
                $outletName = $outlet ? $outlet->nama_outlet : "Unknown ($outletId)";
                echo "      - $outletName: $count records\n";
                
                // Check if this outlet is in user's accessible outlets
                if (!in_array($outletId, $accessibleOutletIds)) {
                    echo "      ❌ WARNING: User should not have access to outlet $outletId!\n";
                }
            }
        } else {
            echo "   ❌ Error: " . $responseData['message'] . "\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
    }
    
    // Test getData with specific outlet filter
    if (!empty($accessibleOutletIds)) {
        $testOutletId = $accessibleOutletIds[0];
        echo "📊 Testing getData with specific outlet filter (ID: $testOutletId)...\n";
        
        $request = new Request([
            'outlet_id' => $testOutletId,
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31'
        ]);
        
        try {
            $response = $controller->getData($request);
            $responseData = json_decode($response->getContent(), true);
            
            if ($responseData['success']) {
                $salesData = $responseData['data'];
                echo "   ✅ Data loaded successfully: " . count($salesData) . " records\n";
                
                // Verify all records are from the specified outlet
                $wrongOutletCount = 0;
                foreach ($salesData as $sale) {
                    if ($sale['outlet_id'] != $testOutletId) {
                        $wrongOutletCount++;
                    }
                }
                
                if ($wrongOutletCount > 0) {
                    echo "   ❌ WARNING: $wrongOutletCount records from wrong outlet!\n";
                } else {
                    echo "   ✅ All records are from the correct outlet\n";
                }
            } else {
                echo "   ❌ Error: " . $responseData['message'] . "\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Exception: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
}

// Test with no authentication
echo "🚫 Testing without authentication...\n";
echo "-" . str_repeat("-", 40) . "\n";
auth()->logout();

try {
    $controller = new SalesReportController();
    $request = new Request([
        'outlet_id' => '',
        'start_date' => '2024-01-01',
        'end_date' => '2024-12-31'
    ]);
    
    $response = $controller->getData($request);
    echo "❌ Should have failed without authentication!\n";
} catch (Exception $e) {
    echo "✅ Correctly failed without authentication: " . $e->getMessage() . "\n";
}

echo "\n✅ Sales Report Outlet Filter Debug Test Complete!\n";