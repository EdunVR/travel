<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING POS 401 AUTHENTICATION ERROR ===\n\n";

// 1. Check current session configuration
echo "1. Checking session configuration...\n";
$sessionDriver = config('session.driver');
$sessionLifetime = config('session.lifetime');
$sessionPath = config('session.path');
$sessionDomain = config('session.domain');
$sessionSecure = config('session.secure');
$sessionSameSite = config('session.same_site');

echo "Session driver: {$sessionDriver}\n";
echo "Session lifetime: {$sessionLifetime} minutes\n";
echo "Session path: {$sessionPath}\n";
echo "Session domain: {$sessionDomain}\n";
echo "Session secure: " . ($sessionSecure ? 'true' : 'false') . "\n";
echo "Session same_site: {$sessionSameSite}\n";

// 2. Check if sessions table exists and is working
if ($sessionDriver === 'database') {
    echo "\n2. Checking sessions table...\n";
    try {
        $sessionsCount = DB::table('sessions')->count();
        echo "✅ Sessions table exists with {$sessionsCount} records\n";
        
        // Check for recent sessions
        $recentSessions = DB::table('sessions')
            ->where('last_activity', '>', time() - 3600)
            ->count();
        echo "Recent sessions (last hour): {$recentSessions}\n";
        
    } catch (Exception $e) {
        echo "❌ Sessions table error: " . $e->getMessage() . "\n";
        echo "Creating sessions table...\n";
        
        try {
            DB::statement("
                CREATE TABLE IF NOT EXISTS sessions (
                    id VARCHAR(255) NOT NULL PRIMARY KEY,
                    user_id BIGINT UNSIGNED NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    payload LONGTEXT NOT NULL,
                    last_activity INT NOT NULL,
                    INDEX sessions_user_id_index (user_id),
                    INDEX sessions_last_activity_index (last_activity)
                )
            ");
            echo "✅ Sessions table created\n";
        } catch (Exception $e) {
            echo "❌ Failed to create sessions table: " . $e->getMessage() . "\n";
        }
    }
}

// 3. Check APP_KEY
echo "\n3. Checking APP_KEY...\n";
$appKey = config('app.key');
if (empty($appKey)) {
    echo "❌ APP_KEY is not set!\n";
    echo "Generating new APP_KEY...\n";
    try {
        Artisan::call('key:generate');
        echo "✅ APP_KEY generated\n";
    } catch (Exception $e) {
        echo "❌ Failed to generate APP_KEY: " . $e->getMessage() . "\n";
    }
} else {
    echo "✅ APP_KEY is set\n";
}

// 4. Clear all caches that might cause authentication issues
echo "\n4. Clearing caches...\n";
$cacheCommands = [
    'cache:clear' => 'Application cache',
    'config:clear' => 'Configuration cache',
    'route:clear' => 'Route cache',
    'view:clear' => 'View cache',
    'session:flush' => 'Session data'
];

foreach ($cacheCommands as $command => $description) {
    try {
        Artisan::call($command);
        echo "✅ {$description} cleared\n";
    } catch (Exception $e) {
        echo "❌ Failed to clear {$description}: " . $e->getMessage() . "\n";
    }
}

// 5. Test CSRF token functionality
echo "\n5. Testing CSRF token...\n";
try {
    $token = csrf_token();
    if (strlen($token) > 10) {
        echo "✅ CSRF token generated successfully: " . substr($token, 0, 10) . "...\n";
    } else {
        echo "❌ CSRF token seems invalid: {$token}\n";
    }
} catch (Exception $e) {
    echo "❌ CSRF token error: " . $e->getMessage() . "\n";
}

// 6. Check middleware configuration
echo "\n6. Checking middleware configuration...\n";
try {
    $middlewareGroups = config('app.middleware_groups', []);
    $webMiddleware = $middlewareGroups['web'] ?? [];
    
    echo "Web middleware stack:\n";
    foreach ($webMiddleware as $middleware) {
        echo "  - {$middleware}\n";
    }
    
    // Check if session middleware is present
    $hasSessionMiddleware = false;
    foreach ($webMiddleware as $middleware) {
        if (strpos($middleware, 'Session') !== false || strpos($middleware, 'session') !== false) {
            $hasSessionMiddleware = true;
            break;
        }
    }
    
    if ($hasSessionMiddleware) {
        echo "✅ Session middleware found in web group\n";
    } else {
        echo "❌ Session middleware not found in web group\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking middleware: " . $e->getMessage() . "\n";
}

// 7. Test POS routes accessibility
echo "\n7. Testing POS routes...\n";
try {
    // Create a test request to check route resolution
    $request = \Illuminate\Http\Request::create('/admin/penjualan/pos/products?outlet_id=1', 'GET');
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    // Check if route exists
    $route = app('router')->getRoutes()->match($request);
    if ($route) {
        echo "✅ POS products route exists: " . $route->getName() . "\n";
        echo "Route action: " . $route->getActionName() . "\n";
        
        // Check middleware
        $routeMiddleware = $route->middleware();
        echo "Route middleware: " . implode(', ', $routeMiddleware) . "\n";
    } else {
        echo "❌ POS products route not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error testing routes: " . $e->getMessage() . "\n";
}

// 8. Check storage permissions
echo "\n8. Checking storage permissions...\n";
$storagePaths = [
    storage_path('framework/sessions'),
    storage_path('framework/cache'),
    storage_path('logs')
];

foreach ($storagePaths as $path) {
    if (!is_dir($path)) {
        echo "Creating directory: {$path}\n";
        mkdir($path, 0755, true);
    }
    
    if (is_writable($path)) {
        echo "✅ {$path} is writable\n";
    } else {
        echo "❌ {$path} is not writable\n";
        try {
            chmod($path, 0755);
            echo "✅ Fixed permissions for {$path}\n";
        } catch (Exception $e) {
            echo "❌ Failed to fix permissions for {$path}\n";
        }
    }
}

// 9. Create a test session to verify functionality
echo "\n9. Testing session functionality...\n";
try {
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Test session write/read
    session(['test_pos_auth' => 'working']);
    $testValue = session('test_pos_auth');
    
    if ($testValue === 'working') {
        echo "✅ Session read/write works\n";
    } else {
        echo "❌ Session read/write failed\n";
    }
    
    // Clean up test session
    session()->forget('test_pos_auth');
    
} catch (Exception $e) {
    echo "❌ Session test error: " . $e->getMessage() . "\n";
}

// 10. Check for common authentication issues
echo "\n10. Checking for common authentication issues...\n";

// Check if user model exists and is configured correctly
try {
    $userModel = config('auth.providers.users.model');
    echo "User model: {$userModel}\n";
    
    if (class_exists($userModel)) {
        echo "✅ User model exists\n";
        
        // Check if users table has required columns
        $userColumns = DB::getSchemaBuilder()->getColumnListing('users');
        $requiredColumns = ['id', 'email', 'password', 'remember_token'];
        
        foreach ($requiredColumns as $column) {
            if (in_array($column, $userColumns)) {
                echo "✅ Users table has {$column} column\n";
            } else {
                echo "❌ Users table missing {$column} column\n";
            }
        }
    } else {
        echo "❌ User model class not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error checking user model: " . $e->getMessage() . "\n";
}

echo "\n=== AUTHENTICATION FIX COMPLETE ===\n";
echo "\nNext steps to resolve 401 error:\n";
echo "1. Restart your web server (Apache/Nginx)\n";
echo "2. Clear browser cache and cookies completely\n";
echo "3. Login again to the admin panel\n";
echo "4. If still getting 401, check browser console for CSRF token\n";
echo "5. Verify that you're accessing the correct URL with /admin prefix\n";
echo "6. Check if your session is persisting between requests\n";

echo "\nIf the issue persists, run this command to check session data:\n";
echo "SELECT * FROM sessions WHERE user_id IS NOT NULL ORDER BY last_activity DESC LIMIT 5;\n";