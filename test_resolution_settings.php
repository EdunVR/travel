<?php

/**
 * Quick Test - Resolution Settings Routes
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing Resolution Settings Routes\n";
echo "=====================================\n\n";

// Test 1: Check if routes are registered
echo "Test 1: Checking routes...\n";
try {
    $routes = [
        'admin.sistem.resolusi.index',
        'admin.sistem.resolusi.get',
        'admin.sistem.resolusi.store',
        'admin.sistem.resolusi.reset',
    ];
    
    foreach ($routes as $routeName) {
        try {
            $url = route($routeName);
            echo "  ✅ {$routeName} → {$url}\n";
        } catch (Exception $e) {
            echo "  ❌ {$routeName} → NOT FOUND\n";
        }
    }
} catch (Exception $e) {
    echo "  ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check if controller exists
echo "Test 2: Checking controller...\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/Admin/ResolutionSettingController.php';
if (file_exists($controllerPath)) {
    echo "  ✅ Controller exists\n";
    
    // Check if class can be loaded
    try {
        $controller = new \App\Http\Controllers\Admin\ResolutionSettingController();
        echo "  ✅ Controller can be instantiated\n";
    } catch (Exception $e) {
        echo "  ❌ Controller error: " . $e->getMessage() . "\n";
    }
} else {
    echo "  ❌ Controller NOT found\n";
}

echo "\n";

// Test 3: Check if view exists
echo "Test 3: Checking view...\n";
$viewPath = __DIR__ . '/resources/views/admin/sistem/resolusi/index.blade.php';
if (file_exists($viewPath)) {
    echo "  ✅ View exists\n";
    
    // Check view size
    $size = filesize($viewPath);
    echo "  ℹ️  View size: " . number_format($size) . " bytes\n";
} else {
    echo "  ❌ View NOT found\n";
}

echo "\n";

// Test 4: Check if JS exists
echo "Test 4: Checking JavaScript...\n";
$jsPath = __DIR__ . '/public/js/resolution-settings.js';
if (file_exists($jsPath)) {
    echo "  ✅ JavaScript exists\n";
} else {
    echo "  ❌ JavaScript NOT found\n";
}

echo "\n";

// Test 5: Check if CSS exists
echo "Test 5: Checking CSS...\n";
$cssPath = __DIR__ . '/public/css/resolution-settings.css';
if (file_exists($cssPath)) {
    echo "  ✅ CSS exists\n";
} else {
    echo "  ❌ CSS NOT found\n";
}

echo "\n";

// Test 6: Check menu configuration
echo "Test 6: Checking menu configuration...\n";
$menuConfig = config('sidebar_menu');
$sistemMenu = $menuConfig['Sistem'] ?? null;

if ($sistemMenu) {
    echo "  ✅ Sistem menu exists\n";
    
    $hasResolusiMenu = false;
    foreach ($sistemMenu['items'] as $item) {
        if ($item['name'] === 'Setting Resolusi') {
            $hasResolusiMenu = true;
            echo "  ✅ Setting Resolusi menu item found\n";
            echo "  ℹ️  Route: {$item['route']}\n";
            break;
        }
    }
    
    if (!$hasResolusiMenu) {
        echo "  ❌ Setting Resolusi menu item NOT found\n";
    }
} else {
    echo "  ❌ Sistem menu NOT found\n";
}

echo "\n";
echo "=====================================\n";
echo "✅ All tests completed!\n";
echo "\n";
echo "Next steps:\n";
echo "1. Refresh browser (Ctrl+F5)\n";
echo "2. Login to application\n";
echo "3. Click Sistem → Setting Resolusi\n";
echo "4. Test all features\n";
echo "\n";
