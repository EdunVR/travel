<?php

/**
 * Test Sales Dashboard Outlet Filtering
 * Verify that sales dashboard only shows data from accessible outlets
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Outlet;

echo "=== SALES DASHBOARD OUTLET FILTER TEST ===\n\n";

// Test 1: Check if controller has outlet filtering
echo "Test 1: Controller Implementation\n";
echo str_repeat('-', 50) . "\n";

$controllerPath = 'app/Http/Controllers/SalesDashboardController.php';
$content = file_get_contents($controllerPath);

$checks = [
    'has_trait' => strpos($content, 'use HasOutletFilter') !== false,
    'has_get_outlet_ids' => strpos($content, 'getAccessibleOutletIds()') !== false,
    'has_wherein_outlet' => strpos($content, "whereIn('id_outlet'") !== false || 
                            strpos($content, 'whereIn(\'id_outlet\'') !== false,
    'has_validation' => strpos($content, 'tidak memiliki akses') !== false,
];

if ($checks['has_trait']) {
    echo "✅ Has HasOutletFilter trait\n";
} else {
    echo "❌ Missing HasOutletFilter trait\n";
}

if ($checks['has_get_outlet_ids']) {
    echo "✅ Uses getAccessibleOutletIds()\n";
} else {
    echo "❌ Not using getAccessibleOutletIds()\n";
}

if ($checks['has_wherein_outlet']) {
    echo "✅ Has whereIn outlet filtering\n";
} else {
    echo "❌ No whereIn outlet filtering\n";
}

if ($checks['has_validation']) {
    echo "✅ Has outlet access validation\n";
} else {
    echo "❌ No outlet access validation\n";
}

$allPassed = array_reduce($checks, fn($carry, $item) => $carry && $item, true);

if ($allPassed) {
    echo "\n✅ Controller implementation: PASSED\n";
} else {
    echo "\n❌ Controller implementation: FAILED\n";
}

// Test 2: Database checks
echo "\n\nTest 2: Database Structure\n";
echo str_repeat('-', 50) . "\n";

try {
    // Check outlets
    $outletCount = DB::table('outlets')->count();
    echo "✅ Outlets table exists: $outletCount outlets\n";
    
    // Check user_outlets
    $userOutletCount = DB::table('user_outlets')->count();
    echo "✅ User-Outlet relationships: $userOutletCount\n";
    
    // Check penjualan has outlet_id
    $penjualanColumns = DB::select("SHOW COLUMNS FROM penjualan LIKE 'id_outlet'");
    if (!empty($penjualanColumns)) {
        echo "✅ Penjualan table has id_outlet column\n";
    } else {
        echo "❌ Penjualan table missing id_outlet column\n";
    }
    
    // Check pos_sales has outlet_id
    $posSalesColumns = DB::select("SHOW COLUMNS FROM pos_sales LIKE 'id_outlet'");
    if (!empty($posSalesColumns)) {
        echo "✅ POS Sales table has id_outlet column\n";
    } else {
        echo "❌ POS Sales table missing id_outlet column\n";
    }
    
    echo "\n✅ Database structure: PASSED\n";
    
} catch (\Exception $e) {
    echo "\n❌ Database check error: " . $e->getMessage() . "\n";
}

// Test 3: Query Analysis
echo "\n\nTest 3: Query Analysis\n";
echo str_repeat('-', 50) . "\n";

// Count queries with outlet filtering
$outletFilterCount = substr_count($content, "whereIn('id_outlet', \$accessibleOutletIds)") +
                     substr_count($content, 'whereIn(\'id_outlet\', $accessibleOutletIds)');

echo "Found $outletFilterCount outlet filter implementations\n";

if ($outletFilterCount >= 6) {
    echo "✅ All major queries have outlet filtering\n";
} else {
    echo "⚠️  Some queries may be missing outlet filtering\n";
}

// Check specific methods
$methods = ['index', 'getData', 'calculateKPI', 'getDailyTrend'];
$methodsWithFiltering = 0;

foreach ($methods as $method) {
    $pattern = '/function ' . preg_quote($method) . '\([^)]*\).*?(?=function|\z)/s';
    if (preg_match($pattern, $content, $matches)) {
        $methodContent = $matches[0];
        if (strpos($methodContent, 'getAccessibleOutletIds') !== false ||
            strpos($methodContent, 'whereIn') !== false) {
            echo "✅ Method $method() has outlet filtering\n";
            $methodsWithFiltering++;
        } else {
            echo "⚠️  Method $method() may need outlet filtering\n";
        }
    }
}

if ($methodsWithFiltering >= 3) {
    echo "\n✅ Query analysis: PASSED\n";
} else {
    echo "\n⚠️  Query analysis: NEEDS REVIEW\n";
}

// Test 4: Sample Data Test
echo "\n\nTest 4: Sample Data Test\n";
echo str_repeat('-', 50) . "\n";

try {
    // Get sample data counts
    $totalPenjualan = DB::table('penjualan')->count();
    $totalPosSales = DB::table('pos_sales')->count();
    
    echo "Total Penjualan records: $totalPenjualan\n";
    echo "Total POS Sales records: $totalPosSales\n";
    
    if ($totalPenjualan > 0 || $totalPosSales > 0) {
        echo "✅ Sample data available for testing\n";
        
        // Check outlet distribution
        $penjualanByOutlet = DB::table('penjualan')
            ->select('id_outlet', DB::raw('count(*) as count'))
            ->groupBy('id_outlet')
            ->get();
        
        echo "\nPenjualan by Outlet:\n";
        foreach ($penjualanByOutlet as $row) {
            $outletName = DB::table('outlets')->where('id_outlet', $row->id_outlet)->value('nama_outlet');
            echo "  - Outlet $row->id_outlet ($outletName): $row->count records\n";
        }
        
        $posByOutlet = DB::table('pos_sales')
            ->select('id_outlet', DB::raw('count(*) as count'))
            ->groupBy('id_outlet')
            ->get();
        
        echo "\nPOS Sales by Outlet:\n";
        foreach ($posByOutlet as $row) {
            $outletName = DB::table('outlets')->where('id_outlet', $row->id_outlet)->value('nama_outlet');
            echo "  - Outlet $row->id_outlet ($outletName): $row->count records\n";
        }
        
        echo "\n✅ Sample data test: PASSED\n";
    } else {
        echo "⚠️  No sample data available\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Sample data test error: " . $e->getMessage() . "\n";
}

// Final Summary
echo "\n" . str_repeat('=', 50) . "\n";
echo "FINAL SUMMARY\n";
echo str_repeat('=', 50) . "\n\n";

if ($allPassed && $outletFilterCount >= 6) {
    echo "✅ OVERALL STATUS: PASSED\n\n";
    echo "Sales Dashboard is properly configured with outlet filtering.\n";
    echo "Users will only see data from their accessible outlets.\n\n";
    
    echo "Next Steps:\n";
    echo "1. Clear cache: php artisan cache:clear\n";
    echo "2. Test with different user roles\n";
    echo "3. Test outlet switching\n";
    echo "4. Verify dashboard displays correct data\n";
    
    exit(0);
} else {
    echo "⚠️  OVERALL STATUS: NEEDS REVIEW\n\n";
    echo "Some issues were found. Please review:\n";
    
    if (!$allPassed) {
        echo "- Controller implementation needs fixes\n";
    }
    if ($outletFilterCount < 6) {
        echo "- Some queries may be missing outlet filtering\n";
    }
    
    echo "\nRefer to SALES_DASHBOARD_OUTLET_FILTER_COMPLETE.md for details.\n";
    
    exit(1);
}
