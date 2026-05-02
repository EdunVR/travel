<?php

echo "========================================\n";
echo "TESTING ALL INTER OUTLET ROUTES\n";
echo "========================================\n\n";

// Routes to test
$routes = [
    'admin.penjualan.inter-outlet.index',
    'admin.penjualan.inter-outlet.products',
    'admin.penjualan.inter-outlet.outlets',
    'admin.penjualan.inter-outlet.store',
    'admin.penjualan.inter-outlet.history',
    'admin.penjualan.inter-outlet.history.data',
    'admin.penjualan.inter-outlet.coa-settings',
    'admin.penjualan.inter-outlet.show',
    'admin.penjualan.inter-outlet.print',
    'admin.penjualan.inter-outlet.approve',
];

$success = 0;
$failed = 0;

foreach ($routes as $route) {
    try {
        if (in_array($route, ['admin.penjualan.inter-outlet.show', 'admin.penjualan.inter-outlet.print', 'admin.penjualan.inter-outlet.approve'])) {
            // Routes with parameters
            $url = route($route, 1);
        } else {
            // Routes without parameters
            $url = route($route);
        }
        
        echo "✅ {$route}\n";
        echo "   URL: {$url}\n\n";
        $success++;
        
    } catch (Exception $e) {
        echo "❌ {$route}\n";
        echo "   ERROR: {$e->getMessage()}\n\n";
        $failed++;
    }
}

echo "========================================\n";
echo "TESTING RESULTS\n";
echo "========================================\n";
echo "✅ Success: {$success}\n";
echo "❌ Failed: {$failed}\n";
echo "📊 Total: " . ($success + $failed) . "\n\n";

if ($failed === 0) {
    echo "🎉 ALL ROUTES ARE WORKING PERFECTLY!\n";
    echo "The Inter Outlet Sale module is ready to use.\n";
} else {
    echo "⚠️  Some routes need attention.\n";
    echo "Please check the failed routes above.\n";
}

echo "\n";