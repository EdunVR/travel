<?php

/**
 * Test Dashboard API Endpoints
 * 
 * Run: php test_dashboard_api.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Dashboard API Endpoints ===\n\n";

// Test 1: Overview Stats
echo "1. Testing Overview Stats...\n";
try {
    $controller = new App\Http\Controllers\AdminDashboardController();
    $request = new Illuminate\Http\Request();
    $response = $controller->getOverviewStats($request);
    $data = json_decode($response->getContent(), true);
    
    echo "   ✓ Sales: " . number_format($data['sales']['value']) . " (Growth: {$data['sales']['growth']}%)\n";
    echo "   ✓ Orders: {$data['orders']['value']} (Growth: {$data['orders']['growth']}%)\n";
    echo "   ✓ Returns: {$data['returns']['value']} (Growth: {$data['returns']['growth']}%)\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Sales Trend
echo "2. Testing Sales Trend...\n";
try {
    $controller = new App\Http\Controllers\AdminDashboardController();
    $request = new Illuminate\Http\Request();
    $response = $controller->getSalesTrend($request);
    $data = json_decode($response->getContent(), true);
    
    if (is_array($data) && count($data) > 0) {
        echo "   ✓ Got " . count($data) . " days of data\n";
        echo "   ✓ Sample: {$data[0]['day']} = " . number_format($data[0]['value']) . "\n";
    } else {
        echo "   ⚠ No sales trend data (empty array)\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Inventory Status
echo "3. Testing Inventory Status...\n";
try {
    $controller = new App\Http\Controllers\AdminDashboardController();
    $request = new Illuminate\Http\Request();
    $response = $controller->getInventoryStatus($request);
    $data = json_decode($response->getContent(), true);
    
    echo "   ✓ Safe: {$data['stats']['safe']}\n";
    echo "   ✓ Low: {$data['stats']['low']}\n";
    echo "   ✓ Critical: {$data['stats']['critical']}\n";
    echo "   ✓ Items: " . count($data['items']) . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Production Efficiency
echo "4. Testing Production Efficiency...\n";
try {
    $controller = new App\Http\Controllers\AdminDashboardController();
    $request = new Illuminate\Http\Request();
    $response = $controller->getProductionEfficiency($request);
    $data = json_decode($response->getContent(), true);
    
    echo "   ✓ Target Achievement: {$data['target_achievement']}%\n";
    echo "   ✓ Efficiency: {$data['efficiency']}%\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Employee Performance
echo "5. Testing Employee Performance...\n";
try {
    $controller = new App\Http\Controllers\AdminDashboardController();
    $request = new Illuminate\Http\Request();
    $response = $controller->getEmployeePerformance($request);
    $data = json_decode($response->getContent(), true);
    
    if (is_array($data) && count($data) > 0) {
        echo "   ✓ Got " . count($data) . " employees\n";
        echo "   ✓ Sample: {$data[0]['name']} - Performance: {$data[0]['performance']}%, Attendance: {$data[0]['attendance']}%\n";
    } else {
        echo "   ⚠ No employee data (empty array)\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Insights
echo "6. Testing Insights...\n";
try {
    $controller = new App\Http\Controllers\AdminDashboardController();
    $request = new Illuminate\Http\Request();
    $response = $controller->getInsights($request);
    $data = json_decode($response->getContent(), true);
    
    if (is_array($data) && count($data) > 0) {
        echo "   ✓ Got " . count($data) . " insights\n";
        foreach ($data as $insight) {
            echo "   ✓ [{$insight['type']}] {$insight['title']}: {$insight['message']}\n";
        }
    } else {
        echo "   ⚠ No insights (empty array)\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
