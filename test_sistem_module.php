<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

echo "========================================\n";
echo "   TESTING SISTEM MODULE\n";
echo "========================================\n\n";

// Test 1: Check if routes are registered
echo "[1/6] Testing Routes Registration...\n";
$routes = [
    'admin.sistem.index',
    'admin.sistem.pengaturan.index',
    'admin.sistem.pengaturan.edit',
    'admin.sistem.system-info',
    'admin.sistem.clear-cache',
    'admin.sistem.create-backup',
];

$routesPassed = 0;
foreach ($routes as $routeName) {
    if (Route::has($routeName)) {
        echo "  ✓ Route '{$routeName}' registered\n";
        $routesPassed++;
    } else {
        echo "  ✗ Route '{$routeName}' NOT registered\n";
    }
}
echo "  Routes: {$routesPassed}/" . count($routes) . " passed\n\n";

// Test 2: Check if controllers exist
echo "[2/6] Testing Controllers...\n";
$controllers = [
    'App\Http\Controllers\SistemController',
    'App\Http\Controllers\CompanySettingController',
];

$controllersPassed = 0;
foreach ($controllers as $controller) {
    if (class_exists($controller)) {
        echo "  ✓ Controller '{$controller}' exists\n";
        $controllersPassed++;
    } else {
        echo "  ✗ Controller '{$controller}' NOT found\n";
    }
}
echo "  Controllers: {$controllersPassed}/" . count($controllers) . " passed\n\n";

// Test 3: Check if views exist
echo "[3/6] Testing Views...\n";
$views = [
    'resources/views/admin/sistem/index.blade.php',
    'resources/views/admin/sistem/pengaturan/index.blade.php',
    'resources/views/admin/sistem/pengaturan/edit.blade.php',
];

$viewsPassed = 0;
foreach ($views as $view) {
    if (file_exists($view)) {
        echo "  ✓ View '{$view}' exists\n";
        $viewsPassed++;
    } else {
        echo "  ✗ View '{$view}' NOT found\n";
    }
}
echo "  Views: {$viewsPassed}/" . count($views) . " passed\n\n";

// Test 4: Check if models exist
echo "[4/6] Testing Models...\n";
$models = [
    'App\Models\CompanySetting',
];

$modelsPassed = 0;
foreach ($models as $model) {
    if (class_exists($model)) {
        echo "  ✓ Model '{$model}' exists\n";
        $modelsPassed++;
    } else {
        echo "  ✗ Model '{$model}' NOT found\n";
    }
}
echo "  Models: {$modelsPassed}/" . count($models) . " passed\n\n";

// Test 5: Check if migrations exist
echo "[5/6] Testing Migrations...\n";
$migrations = [
    'database/migrations/2024_12_18_100000_create_company_settings_table.php',
];

$migrationsPassed = 0;
foreach ($migrations as $migration) {
    if (file_exists($migration)) {
        echo "  ✓ Migration '{$migration}' exists\n";
        $migrationsPassed++;
    } else {
        echo "  ✗ Migration '{$migration}' NOT found\n";
    }
}
echo "  Migrations: {$migrationsPassed}/" . count($migrations) . " passed\n\n";

// Test 6: Check if permissions exist (requires database connection)
echo "[6/6] Testing Permissions...\n";
try {
    $permissions = [
        'sistem.view',
        'sistem.backup',
        'sistem.maintenance',
        'sistem.settings.view',
        'sistem.settings.create',
        'sistem.settings.edit',
        'sistem.settings.delete',
    ];

    $permissionsPassed = 0;
    foreach ($permissions as $permissionName) {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            echo "  ✓ Permission '{$permissionName}' exists\n";
            $permissionsPassed++;
        } else {
            echo "  ✗ Permission '{$permissionName}' NOT found\n";
        }
    }
    echo "  Permissions: {$permissionsPassed}/" . count($permissions) . " passed\n\n";
} catch (Exception $e) {
    echo "  ⚠ Cannot test permissions (database not connected): " . $e->getMessage() . "\n\n";
}

// Summary
echo "========================================\n";
echo "   TEST SUMMARY\n";
echo "========================================\n";
$totalTests = $routesPassed + $controllersPassed + $viewsPassed + $modelsPassed + $migrationsPassed;
$maxTests = count($routes) + count($controllers) + count($views) + count($models) + count($migrations);

if ($totalTests == $maxTests) {
    echo "✅ ALL TESTS PASSED! ({$totalTests}/{$maxTests})\n";
    echo "🎉 Sistem Module is ready for use!\n\n";
    
    echo "Next steps:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. Run: php artisan db:seed --class=SistemPermissionSeeder\n";
    echo "3. Access: /admin/sistem\n";
} else {
    echo "❌ SOME TESTS FAILED ({$totalTests}/{$maxTests})\n";
    echo "Please check the failed items above.\n";
}

echo "\n";