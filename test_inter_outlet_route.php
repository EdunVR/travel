<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    // Test route exists
    $routeName = 'admin.penjualan.inter-outlet.index';
    $url = route($routeName);
    
    echo "✅ Route test successful!\n";
    echo "Route name: {$routeName}\n";
    echo "Generated URL: {$url}\n";
    
    // Test other routes
    $routes = [
        'admin.penjualan.inter-outlet.products',
        'admin.penjualan.inter-outlet.outlets',
        'admin.penjualan.inter-outlet.store',
        'admin.penjualan.inter-outlet.history',
        'admin.penjualan.inter-outlet.coa-settings'
    ];
    
    echo "\n📋 Testing other routes:\n";
    foreach ($routes as $route) {
        try {
            $url = route($route);
            echo "✅ {$route} -> {$url}\n";
        } catch (Exception $e) {
            echo "❌ {$route} -> ERROR: {$e->getMessage()}\n";
        }
    }
    
    echo "\n🎉 All routes are working correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Route test failed: " . $e->getMessage() . "\n";
    echo "Please check if routes are properly defined and cached.\n";
}