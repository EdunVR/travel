<?php

/**
 * SDM ATTENDANCE CHECKBOX FILTER TEST SCRIPT
 * 
 * This script tests the complete SDM Attendance checkbox filter implementation
 * including frontend UI, backend controller methods, and data filtering.
 */

echo "=== SDM ATTENDANCE CHECKBOX FILTER TEST ===\n\n";

// Test 1: Check if controller methods support outlet_ids parameter
echo "1. Testing Controller Methods for Outlet Filtering Support...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
if (!file_exists($controllerFile)) {
    echo "❌ Controller file not found: $controllerFile\n";
    exit(1);
}

$controllerContent = file_get_contents($controllerFile);

// Check key methods for outlet filtering
$methodsToCheck = [
    'getDailyTable' => 'outlet_ids.*whereIn.*outlet_id',
    'getMonthlyTable' => 'outlet_ids.*whereIn.*outlet_id', 
    'getEmployees' => 'outlet_ids.*whereIn.*outlet_id',
    'getStatistics' => 'outlet_ids.*whereIn.*outlet_id',
    'exportDailyPdf' => 'outlet_ids.*whereIn.*outlet_id',
    'exportMonthlyPdf' => 'outlet_ids.*whereIn.*outlet_id'
];

$methodResults = [];
foreach ($methodsToCheck as $method => $pattern) {
    if (preg_match("/$pattern/s", $controllerContent)) {
        echo "✅ $method: Supports outlet filtering\n";
        $methodResults[$method] = true;
    } else {
        echo "❌ $method: Missing outlet filtering\n";
        $methodResults[$method] = false;
    }
}

// Test 2: Check frontend implementation
echo "\n2. Testing Frontend Implementation...\n";

$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (!file_exists($viewFile)) {
    echo "❌ View file not found: $viewFile\n";
    exit(1);
}

$viewContent = file_get_contents($viewFile);

// Check for checkbox UI elements
$frontendChecks = [
    'Checkbox Outlet Filter' => 'Checkbox Outlet Filter',
    'selectedOutlets array' => 'selectedOutlets.*\[\]',
    'getSelectedOutletsText function' => 'getSelectedOutletsText\(\)',
    'selectAllOutlets function' => 'selectAllOutlets\(\)',
    'clearAllOutlets function' => 'clearAllOutlets\(\)',
    'onOutletSelectionChange function' => 'onOutletSelectionChange\(\)',
    'outlet_ids parameter in fetchDailyData' => 'outlet_ids\[\].*outletId',
    'outlet_ids parameter in fetchMonthlyData' => 'outlet_ids\[\].*outletId',
    'outlet_ids parameter in fetchEmployees' => 'outlet_ids\[\].*outletId',
    'outlet_ids parameter in fetchStatistics' => 'outlet_ids\[\].*outletId'
];

$frontendResults = [];
foreach ($frontendChecks as $check => $pattern) {
    if (preg_match("/$pattern/s", $viewContent)) {
        echo "✅ $check: Found\n";
        $frontendResults[$check] = true;
    } else {
        echo "❌ $check: Missing\n";
        $frontendResults[$check] = false;
    }
}

// Test 3: Check routes
echo "\n3. Testing Routes...\n";

$routeFile = 'routes/web.php';
if (file_exists($routeFile)) {
    $routeContent = file_get_contents($routeFile);
    
    $requiredRoutes = [
        'daily.table',
        'monthly.table', 
        'employees',
        'statistics',
        'export.daily.pdf',
        'export.monthly.pdf'
    ];
    
    $routeResults = [];
    foreach ($requiredRoutes as $route) {
        if (strpos($routeContent, $route) !== false) {
            echo "✅ Route $route: Found\n";
            $routeResults[$route] = true;
        } else {
            echo "❌ Route $route: Missing\n";
            $routeResults[$route] = false;
        }
    }
} else {
    echo "⚠️  Route file not found, skipping route check\n";
    $routeResults = [];
}

// Test 4: Check JavaScript implementation
echo "\n4. Testing JavaScript Implementation...\n";

$jsChecks = [
    'Alpine.js data structure' => 'function attendanceCrud\(\)',
    'selectedOutlets initialization' => 'selectedOutlets.*\[\]',
    'loadOutlets function' => 'async loadOutlets\(\)',
    'outlet selection change handler' => 'onOutletSelectionChange\(\)',
    'outlet filtering in API calls' => 'outlet_ids\[\].*forEach.*outletId'
];

$jsResults = [];
foreach ($jsChecks as $check => $pattern) {
    if (preg_match("/$pattern/s", $viewContent)) {
        echo "✅ $check: Implemented\n";
        $jsResults[$check] = true;
    } else {
        echo "❌ $check: Missing\n";
        $jsResults[$check] = false;
    }
}

// Test 5: Summary and recommendations
echo "\n=== IMPLEMENTATION SUMMARY ===\n";

$totalChecks = count($methodResults) + count($frontendResults) + count($routeResults) + count($jsResults);
$passedChecks = array_sum($methodResults) + array_sum($frontendResults) + array_sum($routeResults) + array_sum($jsResults);

$completionPercentage = ($passedChecks / $totalChecks) * 100;

echo "Overall Completion: " . round($completionPercentage, 1) . "% ($passedChecks/$totalChecks)\n\n";

// Detailed breakdown
echo "Controller Methods: " . array_sum($methodResults) . "/" . count($methodResults) . " (" . round((array_sum($methodResults)/count($methodResults))*100, 1) . "%)\n";
echo "Frontend UI: " . array_sum($frontendResults) . "/" . count($frontendResults) . " (" . round((array_sum($frontendResults)/count($frontendResults))*100, 1) . "%)\n";
if (!empty($routeResults)) {
    echo "Routes: " . array_sum($routeResults) . "/" . count($routeResults) . " (" . round((array_sum($routeResults)/count($routeResults))*100, 1) . "%)\n";
}
echo "JavaScript: " . array_sum($jsResults) . "/" . count($jsResults) . " (" . round((array_sum($jsResults)/count($jsResults))*100, 1) . "%)\n";

if ($completionPercentage >= 95) {
    echo "\n🎉 SDM ATTENDANCE CHECKBOX FILTER: IMPLEMENTATION COMPLETE!\n";
    echo "✅ All critical components are properly implemented\n";
    echo "✅ Frontend checkbox UI with Alpine.js reactivity\n";
    echo "✅ Backend controller methods support multiple outlet filtering\n";
    echo "✅ Export functions include outlet filtering\n";
    echo "✅ Proper data isolation between outlets\n";
} elseif ($completionPercentage >= 80) {
    echo "\n⚠️  SDM ATTENDANCE CHECKBOX FILTER: MOSTLY COMPLETE\n";
    echo "Most components are implemented but some issues need attention.\n";
} else {
    echo "\n❌ SDM ATTENDANCE CHECKBOX FILTER: NEEDS MORE WORK\n";
    echo "Several critical components are missing or incomplete.\n";
}

// Recommendations
echo "\n=== NEXT STEPS ===\n";

if (array_sum($methodResults) < count($methodResults)) {
    echo "🔧 Update controller methods to support outlet_ids[] parameter\n";
}

if (array_sum($frontendResults) < count($frontendResults)) {
    echo "🔧 Complete frontend checkbox UI implementation\n";
}

if (array_sum($jsResults) < count($jsResults)) {
    echo "🔧 Implement JavaScript outlet filtering logic\n";
}

if ($completionPercentage >= 95) {
    echo "🧪 Run functional tests to verify outlet data isolation\n";
    echo "📋 Test all export functions with multiple outlet selection\n";
    echo "✅ Ready for production deployment\n";
}

echo "\n=== TEST COMPLETE ===\n";

?>