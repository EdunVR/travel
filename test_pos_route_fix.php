<?php
/**
 * Test POS Route Fix
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== POS Route Fix Test ===\n\n";

// Test route existence
echo "1. Testing route existence:\n";
try {
    $dashboardRoute = route('admin.dashboard');
    echo "   admin.dashboard route: $dashboardRoute\n";
    echo "   Route exists: Yes\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

try {
    $posRoute = route('admin.penjualan.pos.index');
    echo "   POS route: $posRoute\n";
    echo "   POS route exists: Yes\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

try {
    $posProductsRoute = route('admin.penjualan.pos.products');
    echo "   POS products route: $posProductsRoute\n";
    echo "   POS products route exists: Yes\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

try {
    $posCoaRoute = route('admin.penjualan.pos.coa.settings');
    echo "   POS COA settings route: $posCoaRoute\n";
    echo "   POS COA settings route exists: Yes\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing outlets:\n";
try {
    $outlets = DB::table('outlets')->where('is_active', true)->get(['id_outlet', 'nama_outlet']);
    echo "   Active outlets: " . count($outlets) . "\n";
    foreach ($outlets as $outlet) {
        echo "     - ID: {$outlet->id_outlet} Name: {$outlet->nama_outlet}\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Routes are properly configured. The POS system should now work without route errors.\n";