<?php
/**
 * Test POS Authentication and Session Issues
 * Run this to debug the 401 authentication errors in POS
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

echo "=== POS Authentication Session Test ===\n\n";

// Test 1: Check session configuration
echo "1. Session Configuration:\n";
echo "   Driver: " . config('session.driver') . "\n";
echo "   Lifetime: " . config('session.lifetime') . " minutes\n";
echo "   Domain: " . config('session.domain') . "\n";
echo "   Secure: " . (config('session.secure') ? 'Yes' : 'No') . "\n";
echo "   HTTP Only: " . (config('session.http_only') ? 'Yes' : 'No') . "\n";
echo "   Same Site: " . config('session.same_site') . "\n\n";

// Test 2: Check if sessions table exists
echo "2. Session Storage:\n";
try {
    if (config('session.driver') === 'database') {
        $sessionTable = config('session.table', 'sessions');
        $sessionCount = DB::table($sessionTable)->count();
        echo "   Sessions table exists: Yes\n";
        echo "   Active sessions: $sessionCount\n";
        
        // Show recent sessions
        $recentSessions = DB::table($sessionTable)
            ->orderBy('last_activity', 'desc')
            ->limit(3)
            ->get(['id', 'user_id', 'last_activity']);
            
        echo "   Recent sessions:\n";
        foreach ($recentSessions as $session) {
            $lastActivity = date('Y-m-d H:i:s', $session->last_activity);
            echo "     - ID: " . substr($session->id, 0, 10) . "... User: {$session->user_id} Last: $lastActivity\n";
        }
    }
} catch (Exception $e) {
    echo "   Error checking sessions: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check users and authentication
echo "3. User Authentication:\n";
try {
    $userCount = DB::table('users')->count();
    echo "   Total users: $userCount\n";
    
    $superadmin = DB::table('users')->where('email', 'superadmin@morra.com')->first();
    if ($superadmin) {
        echo "   Superadmin exists: Yes (ID: {$superadmin->id})\n";
        echo "   Superadmin outlet: {$superadmin->outlet_id}\n";
    } else {
        echo "   Superadmin exists: No\n";
    }
} catch (Exception $e) {
    echo "   Error checking users: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Check outlets
echo "4. Outlets:\n";
try {
    $outlets = DB::table('outlets')->where('is_active', true)->get(['id_outlet', 'nama_outlet']);
    echo "   Active outlets: " . count($outlets) . "\n";
    foreach ($outlets as $outlet) {
        echo "     - ID: {$outlet->id_outlet} Name: {$outlet->nama_outlet}\n";
    }
} catch (Exception $e) {
    echo "   Error checking outlets: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Check POS routes
echo "5. POS Routes Test:\n";
try {
    // Simulate a request to POS products endpoint
    $request = \Illuminate\Http\Request::create('/admin/penjualan/pos/products?outlet_id=1', 'GET');
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    echo "   POS products route exists: ";
    $route = app('router')->getRoutes()->match($request);
    echo ($route ? "Yes" : "No") . "\n";
    
    if ($route) {
        echo "   Route name: " . $route->getName() . "\n";
        echo "   Controller: " . $route->getActionName() . "\n";
        echo "   Middleware: " . implode(', ', $route->middleware()) . "\n";
    }
} catch (Exception $e) {
    echo "   Error checking routes: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Check CSRF token generation
echo "6. CSRF Token:\n";
try {
    $token = csrf_token();
    echo "   CSRF token generated: " . (strlen($token) > 0 ? "Yes" : "No") . "\n";
    echo "   Token length: " . strlen($token) . "\n";
    echo "   Token sample: " . substr($token, 0, 10) . "...\n";
} catch (Exception $e) {
    echo "   Error generating CSRF token: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7: Check recent Laravel logs for authentication errors
echo "7. Recent Authentication Errors:\n";
try {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        $authErrors = array_filter($lines, function($line) {
            return strpos($line, 'Unauthenticated') !== false || 
                   strpos($line, '401') !== false ||
                   strpos($line, 'AuthenticationException') !== false;
        });
        
        $recentErrors = array_slice($authErrors, -5);
        if (count($recentErrors) > 0) {
            echo "   Recent authentication errors found:\n";
            foreach ($recentErrors as $error) {
                echo "     - " . substr($error, 0, 100) . "...\n";
            }
        } else {
            echo "   No recent authentication errors found\n";
        }
    } else {
        echo "   Log file not found\n";
    }
} catch (Exception $e) {
    echo "   Error reading logs: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== Test Complete ===\n";
echo "If you're getting 401 errors in POS:\n";
echo "1. Check if your session is expiring too quickly\n";
echo "2. Verify CSRF tokens are being sent correctly\n";
echo "3. Check if the domain/subdomain settings match\n";
echo "4. Ensure cookies are being set properly\n";
echo "5. Check browser developer tools for cookie issues\n";