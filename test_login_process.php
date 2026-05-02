<?php
/**
 * Test Login Process - Simulasi POST request login
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Test Login Process ===\n\n";

// 1. Test manual authentication
echo "1. Manual authentication test:\n";
try {
    $credentials = ['email' => 'superadmin@morra.com', 'password' => 'password'];
    
    if (Auth::attempt($credentials)) {
        echo "   ✅ Auth::attempt() successful\n";
        echo "   User: " . Auth::user()->name . "\n";
        echo "   ID: " . Auth::id() . "\n";
        
        // Check if user is active
        $user = Auth::user();
        echo "   Is Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
        
        Auth::logout(); // Clean up
    } else {
        echo "   ❌ Auth::attempt() failed\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 2. Test session regeneration
echo "\n2. Session regeneration test:\n";
try {
    $session = app('session');
    $oldId = $session->getId();
    echo "   Old session ID: " . substr($oldId, 0, 10) . "...\n";
    
    $session->regenerate();
    $newId = $session->getId();
    echo "   New session ID: " . substr($newId, 0, 10) . "...\n";
    echo "   Regeneration: " . ($oldId !== $newId ? 'Success' : 'Failed') . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 3. Test redirect route
echo "\n3. Redirect route test:\n";
try {
    $adminDashboard = route('admin.dashboard');
    echo "   Admin dashboard route: $adminDashboard\n";
    
    // Test if route is accessible
    $request = \Illuminate\Http\Request::create('/admin', 'GET');
    $route = app('router')->getRoutes()->match($request);
    echo "   Route name: " . $route->getName() . "\n";
    echo "   Middleware: " . implode(', ', $route->middleware()) . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 4. Test UserActivityLog
echo "\n4. UserActivityLog test:\n";
try {
    // Check if UserActivityLog model exists
    if (class_exists('App\Models\UserActivityLog')) {
        echo "   ✅ UserActivityLog model exists\n";
        
        // Check if table exists
        $tableExists = DB::getSchemaBuilder()->hasTable('user_activity_logs');
        echo "   Table exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
        
        if (!$tableExists) {
            echo "   ⚠️  WARNING: user_activity_logs table missing - this might cause login to fail\n";
        }
    } else {
        echo "   ❌ UserActivityLog model not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 5. Test User model methods
echo "\n5. User model methods test:\n";
try {
    $user = App\Models\User::where('email', 'superadmin@morra.com')->first();
    
    // Check if updateLastLogin method exists
    if (method_exists($user, 'updateLastLogin')) {
        echo "   ✅ updateLastLogin method exists\n";
        
        // Try to call it
        $user->updateLastLogin();
        echo "   ✅ updateLastLogin executed successfully\n";
    } else {
        echo "   ❌ updateLastLogin method not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 6. Check recent login attempts in logs
echo "\n6. Recent login attempts:\n";
try {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        
        // Look for login-related logs
        $loginLogs = array_filter($lines, function($line) {
            return strpos($line, 'login') !== false || 
                   strpos($line, 'Login') !== false ||
                   strpos($line, 'attempt') !== false ||
                   strpos($line, 'auth') !== false;
        });
        
        $recentLogs = array_slice($loginLogs, -5);
        if (count($recentLogs) > 0) {
            echo "   Recent login-related logs:\n";
            foreach ($recentLogs as $log) {
                echo "     - " . substr($log, 0, 100) . "...\n";
            }
        } else {
            echo "   No recent login logs found\n";
        }
    } else {
        echo "   Log file not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nJika login masih gagal, kemungkinan:\n";
echo "1. UserActivityLog table missing\n";
echo "2. User model method missing\n";
echo "3. Session tidak tersimpan di browser\n";
echo "4. CSRF token mismatch\n";
echo "5. Middleware redirect loop\n";