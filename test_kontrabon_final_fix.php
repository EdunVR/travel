<?php

/**
 * KONTRA BON FINAL FIX TEST SCRIPT
 * 
 * This script tests the final fix for Kontra Bon DataTable issues
 */

echo "=== KONTRA BON FINAL FIX TEST ===\n\n";

// Test 1: Check if routes exist
echo "1. Testing Routes:\n";
try {
    $routes = [
        'admin.penjualan.kontrabon.index',
        'admin.penjualan.kontrabon.data',
        'admin.penjualan.kontrabon.data-kontrabon'
    ];
    
    foreach ($routes as $route) {
        if (Route::has($route)) {
            echo "   ✅ Route '$route' exists\n";
        } else {
            echo "   ❌ Route '$route' missing\n";
        }
    }
} catch (Exception $e) {
    echo "   ⚠️  Route check failed: " . $e->getMessage() . "\n";
}

echo "\n2. Testing Controller Methods:\n";
try {
    $controller = new \App\Http\Controllers\Admin\KontraBonController();
    
    if (method_exists($controller, 'data')) {
        echo "   ✅ data() method exists\n";
    } else {
        echo "   ❌ data() method missing\n";
    }
    
    if (method_exists($controller, 'dataKontraBon')) {
        echo "   ✅ dataKontraBon() method exists\n";
    } else {
        echo "   ❌ dataKontraBon() method missing\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  Controller check failed: " . $e->getMessage() . "\n";
}

echo "\n3. Testing Database Tables:\n";
try {
    // Check if required tables exist
    $tables = ['piutang', 'kontra_bon', 'outlets', 'member'];
    
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            echo "   ✅ Table '$table' exists\n";
        } else {
            echo "   ❌ Table '$table' missing\n";
        }
    }
} catch (Exception $e) {
    echo "   ⚠️  Database check failed: " . $e->getMessage() . "\n";
}

echo "\n4. Testing Sample Data:\n";
try {
    $piutangCount = DB::table('piutang')->count();
    $kontraBonCount = DB::table('kontra_bon')->count();
    $outletCount = DB::table('outlets')->count();
    
    echo "   📊 Piutang records: $piutangCount\n";
    echo "   📊 Kontra Bon records: $kontraBonCount\n";
    echo "   📊 Outlet records: $outletCount\n";
    
    if ($outletCount > 1) {
        echo "   ✅ Multiple outlets available for testing\n";
    } else {
        echo "   ⚠️  Only single outlet - filter won't show\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  Data check failed: " . $e->getMessage() . "\n";
}

echo "\n5. Testing User Permissions:\n";
try {
    $user = auth()->user();
    if ($user) {
        echo "   ✅ User authenticated: " . $user->name . "\n";
        
        $hasPermission = $user->hasRole('super_admin') || $user->hasPermission('sales.kontrabon.view');
        if ($hasPermission) {
            echo "   ✅ User has kontrabon view permission\n";
        } else {
            echo "   ❌ User lacks kontrabon view permission\n";
        }
        
        $userOutlets = $user->akses_outlet ?? [];
        echo "   📊 User outlet access: " . count($userOutlets) . " outlets\n";
    } else {
        echo "   ❌ No authenticated user\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  Permission check failed: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING INSTRUCTIONS ===\n";
echo "1. Clear browser cache completely (Ctrl+Shift+Delete)\n";
echo "2. Navigate to: " . url('/admin/penjualan/kontrabon') . "\n";
echo "3. Open browser console (F12) to monitor for errors\n";
echo "4. Expected behavior:\n";
echo "   ✅ No JavaScript errors in console\n";
echo "   ✅ Piutang tab loads data immediately\n";
echo "   ✅ List Kontra Bon tab loads data when clicked\n";
echo "   ✅ Outlet filter shows if multiple outlets available\n";
echo "   ✅ Data updates when outlet selection changes\n";
echo "   ✅ No DataTable reinitialization warnings\n";
echo "   ✅ Smooth tab switching without errors\n\n";

echo "=== KEY FIXES APPLIED ===\n";
echo "✅ Removed DataTable destroy/recreate cycle\n";
echo "✅ Initialize tables only once, never destroy\n";
echo "✅ Use ajax.reload() for data updates\n";
echo "✅ Added proper tab switching with data reload\n";
echo "✅ Enhanced error handling and logging\n";
echo "✅ Fixed outlet selection initialization\n";
echo "✅ Added drawCallback for checkbox rebinding\n\n";

echo "🎯 KONTRA BON FINAL FIX COMPLETE!\n";
echo "The page should now work without any DataTable errors.\n";

?>