<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TESTING HISTORY DATA LOADING ===\n\n";

try {
    // 1. Check if inter_outlet_sales table has data
    echo "1. Checking inter_outlet_sales table...\n";
    $salesCount = DB::table('inter_outlet_sales')->count();
    echo "   Total records: {$salesCount}\n";
    
    if ($salesCount > 0) {
        $sampleSale = DB::table('inter_outlet_sales')->first();
        echo "   Sample record ID: {$sampleSale->id}\n";
        echo "   Sample transaction: {$sampleSale->no_transaksi}\n";
    }
    
    // 2. Test the historyData method query
    echo "\n2. Testing historyData query...\n";
    
    $query = DB::table('inter_outlet_sales as ios')
        ->select([
            'ios.id',
            'ios.no_transaksi',
            'ios.tanggal',
            'ios.outlet_asal',
            'ios.outlet_tujuan',
            'ios.total',
            'ios.status',
            'oa.nama_outlet as outlet_asal_name',
            'ot.nama_outlet as outlet_tujuan_name',
            'u.name as user_name'
        ])
        ->leftJoin('outlets as oa', 'ios.outlet_asal', '=', 'oa.id_outlet')
        ->leftJoin('outlets as ot', 'ios.outlet_tujuan', '=', 'ot.id_outlet')
        ->leftJoin('users as u', 'ios.user_id', '=', 'u.id')
        ->orderBy('ios.tanggal', 'desc')
        ->limit(5);
    
    $results = $query->get();
    echo "   Query results: " . $results->count() . " records\n";
    
    foreach ($results as $result) {
        echo "   - {$result->no_transaksi} | {$result->outlet_asal_name} -> {$result->outlet_tujuan_name} | {$result->total}\n";
    }
    
    // 3. Test route accessibility
    echo "\n3. Testing route configuration...\n";
    
    // Check if route exists
    $routes = app('router')->getRoutes();
    $historyDataRoute = null;
    
    foreach ($routes as $route) {
        if ($route->getName() === 'admin.penjualan.inter-outlet.history.data') {
            $historyDataRoute = $route;
            break;
        }
    }
    
    if ($historyDataRoute) {
        echo "   ✓ Route 'admin.penjualan.inter-outlet.history.data' exists\n";
        echo "   URI: " . $historyDataRoute->uri() . "\n";
        echo "   Methods: " . implode(', ', $historyDataRoute->methods()) . "\n";
    } else {
        echo "   ✗ Route 'admin.penjualan.inter-outlet.history.data' not found\n";
    }
    
    // 4. Test controller method exists
    echo "\n4. Testing controller method...\n";
    
    $controller = new \App\Http\Controllers\InterOutletSaleController();
    if (method_exists($controller, 'historyData')) {
        echo "   ✓ historyData method exists in InterOutletSaleController\n";
    } else {
        echo "   ✗ historyData method not found in InterOutletSaleController\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "DIAGNOSIS COMPLETE\n";
    echo str_repeat("=", 60) . "\n";
    
    if ($salesCount === 0) {
        echo "⚠️  WARNING: No data in inter_outlet_sales table\n";
        echo "   Create some test transactions first\n";
    }
    
    if (!$historyDataRoute) {
        echo "❌ ERROR: History data route not found\n";
        echo "   Check routes/web.php for the route definition\n";
    }
    
    if ($salesCount > 0 && $historyDataRoute) {
        echo "✅ History data loading should work\n";
        echo "   Check browser console for JavaScript errors\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}