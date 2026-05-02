<?php

/**
 * Test Outlet Filtering Implementation
 * Verify that queries are properly filtered by outlet access
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Outlet;

echo "=== OUTLET FILTERING TEST ===\n\n";

// Test configuration
$testControllers = [
    'FinanceDashboardController',
    'CrmDashboardController',
    'SalesDashboardController',
    'SalesManagementController',
    'PosController',
];

$results = [
    'passed' => [],
    'failed' => [],
    'warnings' => []
];

// Helper function to check if controller has outlet filtering
function hasOutletFiltering($controllerPath) {
    if (!file_exists($controllerPath)) {
        return ['exists' => false];
    }
    
    $content = file_get_contents($controllerPath);
    
    $checks = [
        'has_trait' => strpos($content, 'use HasOutletFilter') !== false,
        'has_get_outlet_ids' => strpos($content, 'getAccessibleOutletIds()') !== false,
        'has_wherein_outlet' => strpos($content, "whereIn('outlet_id'") !== false,
        'has_validate_outlet' => strpos($content, 'validateOutletAccess') !== false,
    ];
    
    return array_merge(['exists' => true], $checks);
}

// Helper function to count queries without outlet filter
function countUnfilteredQueries($controllerPath) {
    if (!file_exists($controllerPath)) {
        return -1;
    }
    
    $content = file_get_contents($controllerPath);
    
    // Remove comments
    $content = preg_replace('/\/\*.*?\*\//s', '', $content);
    $content = preg_replace('/\/\/.*$/m', '', $content);
    
    $patterns = [
        '/\w+::where\([^)]+\)(?!.*whereIn\([\'"]outlet_id[\'"]\))/',
        '/\w+::all\(\)/',
        '/\w+::get\(\)(?!.*whereIn\([\'"]outlet_id[\'"]\))/',
    ];
    
    $count = 0;
    foreach ($patterns as $pattern) {
        preg_match_all($pattern, $content, $matches);
        $count += count($matches[0]);
    }
    
    return $count;
}

echo "Testing Controllers...\n\n";

foreach ($testControllers as $controller) {
    $controllerPath = "app/Http/Controllers/{$controller}.php";
    $controllerName = $controller;
    
    echo "Testing: $controllerName\n";
    echo str_repeat('-', 50) . "\n";
    
    $checks = hasOutletFiltering($controllerPath);
    
    if (!$checks['exists']) {
        echo "  ❌ Controller file not found\n";
        $results['failed'][] = $controllerName;
        echo "\n";
        continue;
    }
    
    $passed = true;
    $warnings = [];
    
    // Check 1: Has HasOutletFilter trait
    if ($checks['has_trait']) {
        echo "  ✅ Has HasOutletFilter trait\n";
    } else {
        echo "  ❌ Missing HasOutletFilter trait\n";
        $passed = false;
    }
    
    // Check 2: Uses getAccessibleOutletIds()
    if ($checks['has_get_outlet_ids']) {
        echo "  ✅ Uses getAccessibleOutletIds()\n";
    } else {
        echo "  ⚠️  Not using getAccessibleOutletIds()\n";
        $warnings[] = "Not using getAccessibleOutletIds()";
    }
    
    // Check 3: Has whereIn outlet_id
    if ($checks['has_wherein_outlet']) {
        echo "  ✅ Has whereIn('outlet_id') filtering\n";
    } else {
        echo "  ⚠️  No whereIn('outlet_id') found\n";
        $warnings[] = "No whereIn outlet filtering found";
    }
    
    // Check 4: Has validateOutletAccess
    if ($checks['has_validate_outlet']) {
        echo "  ✅ Uses validateOutletAccess()\n";
    } else {
        echo "  ℹ️  No validateOutletAccess() found (may not be needed)\n";
    }
    
    // Check 5: Count unfiltered queries
    $unfilteredCount = countUnfilteredQueries($controllerPath);
    if ($unfilteredCount > 0) {
        echo "  ⚠️  Found $unfilteredCount potentially unfiltered queries\n";
        $warnings[] = "$unfilteredCount potentially unfiltered queries";
    } else if ($unfilteredCount === 0) {
        echo "  ✅ No obviously unfiltered queries found\n";
    }
    
    // Summary
    if ($passed && empty($warnings)) {
        echo "  \n  ✅ PASSED - All checks passed\n";
        $results['passed'][] = $controllerName;
    } else if ($passed && !empty($warnings)) {
        echo "  \n  ⚠️  PASSED WITH WARNINGS\n";
        foreach ($warnings as $warning) {
            echo "     - $warning\n";
        }
        $results['warnings'][] = ['controller' => $controllerName, 'warnings' => $warnings];
    } else {
        echo "  \n  ❌ FAILED - Critical issues found\n";
        $results['failed'][] = $controllerName;
    }
    
    echo "\n";
}

// Database checks
echo "\n" . str_repeat('=', 50) . "\n";
echo "DATABASE CHECKS\n";
echo str_repeat('=', 50) . "\n\n";

try {
    // Check if outlets table exists
    $outletsExist = DB::select("SHOW TABLES LIKE 'outlets'");
    if (!empty($outletsExist)) {
        echo "✅ Outlets table exists\n";
        
        $outletCount = DB::table('outlets')->count();
        echo "   Total outlets: $outletCount\n";
    } else {
        echo "❌ Outlets table not found\n";
    }
    
    // Check if users have outlet relationships
    $usersWithOutlets = DB::table('user_outlets')->count();
    echo "✅ User-Outlet relationships: $usersWithOutlets\n";
    
    // Check tables with outlet_id column
    $tables = DB::select("SHOW TABLES");
    $tablesWithOutletId = [];
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        
        $columns = DB::select("SHOW COLUMNS FROM `$tableName` LIKE 'outlet_id'");
        if (!empty($columns)) {
            $tablesWithOutletId[] = $tableName;
        }
    }
    
    echo "\n✅ Tables with outlet_id column: " . count($tablesWithOutletId) . "\n";
    foreach ($tablesWithOutletId as $table) {
        echo "   - $table\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Database check error: " . $e->getMessage() . "\n";
}

// Final Summary
echo "\n" . str_repeat('=', 50) . "\n";
echo "TEST SUMMARY\n";
echo str_repeat('=', 50) . "\n\n";

echo "Total Controllers Tested: " . count($testControllers) . "\n";
echo "✅ Passed: " . count($results['passed']) . "\n";
echo "⚠️  Warnings: " . count($results['warnings']) . "\n";
echo "❌ Failed: " . count($results['failed']) . "\n\n";

if (!empty($results['passed'])) {
    echo "PASSED:\n";
    foreach ($results['passed'] as $controller) {
        echo "  ✅ $controller\n";
    }
    echo "\n";
}

if (!empty($results['warnings'])) {
    echo "WARNINGS:\n";
    foreach ($results['warnings'] as $item) {
        echo "  ⚠️  {$item['controller']}\n";
        foreach ($item['warnings'] as $warning) {
            echo "     - $warning\n";
        }
    }
    echo "\n";
}

if (!empty($results['failed'])) {
    echo "FAILED:\n";
    foreach ($results['failed'] as $controller) {
        echo "  ❌ $controller\n";
    }
    echo "\n";
}

// Recommendations
echo str_repeat('=', 50) . "\n";
echo "RECOMMENDATIONS\n";
echo str_repeat('=', 50) . "\n\n";

if (!empty($results['failed'])) {
    echo "1. Fix failed controllers first:\n";
    foreach ($results['failed'] as $controller) {
        echo "   - Add HasOutletFilter trait to $controller\n";
        echo "   - Implement outlet filtering in queries\n";
    }
    echo "\n";
}

if (!empty($results['warnings'])) {
    echo "2. Review controllers with warnings:\n";
    foreach ($results['warnings'] as $item) {
        echo "   - Review {$item['controller']} for:\n";
        foreach ($item['warnings'] as $warning) {
            echo "     * $warning\n";
        }
    }
    echo "\n";
}

echo "3. Manual testing recommended:\n";
echo "   - Test with different user roles\n";
echo "   - Test with single outlet users\n";
echo "   - Test with multi-outlet users\n";
echo "   - Test with superadmin\n";
echo "   - Test outlet switching\n\n";

echo "4. Performance testing:\n";
echo "   - Check query execution time\n";
echo "   - Verify indexes on outlet_id columns\n";
echo "   - Monitor database load\n\n";

$overallStatus = empty($results['failed']) ? 'PASSED' : 'FAILED';
$statusIcon = empty($results['failed']) ? '✅' : '❌';

echo str_repeat('=', 50) . "\n";
echo "$statusIcon OVERALL STATUS: $overallStatus\n";
echo str_repeat('=', 50) . "\n";

exit(empty($results['failed']) ? 0 : 1);
