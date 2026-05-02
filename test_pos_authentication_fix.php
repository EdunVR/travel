<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING POS AUTHENTICATION FIX ===\n\n";

// 1. Test route resolution
echo "1. Testing route resolution...\n";
try {
    $request = \Illuminate\Http\Request::create('/admin/penjualan/pos/products?outlet_id=1', 'GET');
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    $route = app('router')->getRoutes()->match($request);
    if ($route) {
        echo "✅ Route found: " . $route->getName() . "\n";
        echo "Controller: " . $route->getActionName() . "\n";
        echo "Middleware: " . implode(', ', $route->middleware()) . "\n";
    } else {
        echo "❌ Route not found\n";
    }
} catch (Exception $e) {
    echo "❌ Route test error: " . $e->getMessage() . "\n";
}

// 2. Test CSRF token generation
echo "\n2. Testing CSRF token...\n";
try {
    $token = csrf_token();
    if (strlen($token) > 10) {
        echo "✅ CSRF token generated: " . substr($token, 0, 10) . "...\n";
    } else {
        echo "❌ CSRF token invalid: {$token}\n";
    }
} catch (Exception $e) {
    echo "❌ CSRF error: " . $e->getMessage() . "\n";
}

// 3. Test session functionality
echo "\n3. Testing session...\n";
try {
    session(['test_pos' => 'working']);
    $value = session('test_pos');
    if ($value === 'working') {
        echo "✅ Session read/write works\n";
    } else {
        echo "❌ Session failed\n";
    }
    session()->forget('test_pos');
} catch (Exception $e) {
    echo "❌ Session error: " . $e->getMessage() . "\n";
}

// 4. Test database connection
echo "\n4. Testing database connection...\n";
try {
    $userCount = DB::table('users')->count();
    echo "✅ Database connected. Users: {$userCount}\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// 5. Test POS controller instantiation
echo "\n5. Testing POS controller...\n";
try {
    $journalService = app(\App\Services\JournalEntryService::class);
    $controller = new \App\Http\Controllers\PosController($journalService);
    echo "✅ POS controller instantiated successfully\n";
} catch (Exception $e) {
    echo "❌ Controller error: " . $e->getMessage() . "\n";
}

// 6. Check if sessions table exists
echo "\n6. Checking sessions table...\n";
try {
    $sessionDriver = config('session.driver');
    echo "Session driver: {$sessionDriver}\n";
    
    if ($sessionDriver === 'database') {
        $sessionsCount = DB::table('sessions')->count();
        echo "✅ Sessions table exists with {$sessionsCount} records\n";
    } else {
        echo "✅ Using {$sessionDriver} session driver\n";
    }
} catch (Exception $e) {
    echo "❌ Sessions table error: " . $e->getMessage() . "\n";
}

// 7. Test outlet data
echo "\n7. Testing outlet data...\n";
try {
    $outletCount = DB::table('outlets')->where('is_active', true)->count();
    echo "✅ Active outlets: {$outletCount}\n";
    
    if ($outletCount > 0) {
        $firstOutlet = DB::table('outlets')->where('is_active', true)->first();
        echo "First outlet: {$firstOutlet->nama_outlet} (ID: {$firstOutlet->id_outlet})\n";
    }
} catch (Exception $e) {
    echo "❌ Outlet data error: " . $e->getMessage() . "\n";
}

// 8. Test product data for outlet 1
echo "\n8. Testing product data...\n";
try {
    $productCount = DB::table('produk')->where('id_outlet', 1)->where('is_active', 1)->count();
    echo "✅ Active products in outlet 1: {$productCount}\n";
} catch (Exception $e) {
    echo "❌ Product data error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "\nIf all tests pass, the authentication fix should work.\n";
echo "If you still get 401 errors:\n";
echo "1. Make sure you're logged in to the admin panel\n";
echo "2. Clear browser cache and cookies\n";
echo "3. Check browser console for CSRF token issues\n";
echo "4. Verify the session is persisting between requests\n";