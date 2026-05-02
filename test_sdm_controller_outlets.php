<?php

echo "=== Testing SDM Controller Outlets Variable ===\n";

// Test 1: Check if SdmDashboardController exists and has index method
$controllerPath = 'app/Http/Controllers/SdmDashboardController.php';
if (file_exists($controllerPath)) {
    echo "✓ SdmDashboardController exists\n";
    
    $content = file_get_contents($controllerPath);
    
    // Check if index method exists
    if (strpos($content, 'public function index()') !== false) {
        echo "✓ index() method found\n";
    } else {
        echo "✗ index() method not found\n";
    }
    
    // Check if outlets variable is being passed
    if (strpos($content, "compact('outlets')") !== false) {
        echo "✓ outlets variable is being passed to view\n";
    } else {
        echo "✗ outlets variable is not being passed to view\n";
    }
    
} else {
    echo "✗ SdmDashboardController not found\n";
}

// Test 2: Check route configuration
$routesPath = 'routes/web.php';
if (file_exists($routesPath)) {
    $content = file_get_contents($routesPath);
    
    if (strpos($content, "Route::get('/sdm', [App\Http\Controllers\SdmDashboardController::class, 'index'])") !== false) {
        echo "✓ SDM route is correctly configured to use controller\n";
    } else {
        echo "✗ SDM route configuration issue\n";
    }
} else {
    echo "✗ Routes file not found\n";
}

// Test 3: Check if there are any syntax errors in the view
$viewPath = 'resources/views/admin/sdm/index.blade.php';
if (file_exists($viewPath)) {
    $content = file_get_contents($viewPath);
    
    // Check for proper Blade syntax
    $foreachCount = substr_count($content, '@foreach($outlets');
    $endforeachCount = substr_count($content, '@endforeach');
    
    if ($foreachCount === $endforeachCount) {
        echo "✓ Blade foreach syntax appears correct\n";
    } else {
        echo "✗ Blade foreach syntax mismatch: $foreachCount @foreach vs $endforeachCount @endforeach\n";
    }
    
    // Check for outlets usage
    if (strpos($content, '@json($outlets ?? [])') !== false) {
        echo "✓ Alpine.js outlets initialization found with fallback\n";
    } else {
        echo "⚠ Alpine.js outlets initialization may be missing fallback\n";
    }
    
} else {
    echo "✗ SDM view file not found\n";
}

echo "\n=== SDM Controller Outlets Test Complete ===\n";