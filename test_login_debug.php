<?php
/**
 * Test Login Debug - Mencari tahu kenapa login tidak berhasil
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Test Login Debug ===\n\n";

// 1. Test user credentials
echo "1. Testing user credentials:\n";
$user = DB::table('users')->where('email', 'superadmin@morra.com')->first();
if ($user) {
    echo "   ✅ User found: {$user->name} ({$user->email})\n";
    
    // Test password
    if (Hash::check('password', $user->password)) {
        echo "   ✅ Password 'password' is correct\n";
    } else {
        echo "   ❌ Password 'password' is incorrect\n";
        
        // Try other common passwords
        $testPasswords = ['123456', 'admin', 'superadmin', 'morra123', 'password123'];
        foreach ($testPasswords as $testPass) {
            if (Hash::check($testPass, $user->password)) {
                echo "   ✅ Correct password is: '$testPass'\n";
                break;
            }
        }
    }
} else {
    echo "   ❌ User not found\n";
}

// 2. Test session configuration
echo "\n2. Session configuration:\n";
echo "   Driver: " . config('session.driver') . "\n";
echo "   Table: " . config('session.table') . "\n";
echo "   Domain: " . (config('session.domain') ?: 'null (current domain)') . "\n";
echo "   Secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "   HTTP Only: " . (config('session.http_only') ? 'true' : 'false') . "\n";
echo "   Same Site: " . config('session.same_site') . "\n";
echo "   Lifetime: " . config('session.lifetime') . " minutes\n";

// 3. Test session table
echo "\n3. Session table:\n";
try {
    $sessionTable = config('session.table', 'sessions');
    
    // Check if table exists
    $tableExists = DB::getSchemaBuilder()->hasTable($sessionTable);
    echo "   Table '$sessionTable' exists: " . ($tableExists ? 'Yes' : 'No') . "\n";
    
    if ($tableExists) {
        $sessionCount = DB::table($sessionTable)->count();
        echo "   Total sessions: $sessionCount\n";
        
        // Check table structure
        $columns = DB::getSchemaBuilder()->getColumnListing($sessionTable);
        echo "   Columns: " . implode(', ', $columns) . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 4. Test login route
echo "\n4. Login route:\n";
try {
    $loginRoute = route('login');
    echo "   Login route: $loginRoute\n";
    
    // Check if login route exists
    $request = \Illuminate\Http\Request::create('/login', 'GET');
    $route = app('router')->getRoutes()->match($request);
    echo "   Route name: " . $route->getName() . "\n";
    echo "   Controller: " . $route->getActionName() . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 5. Test auth configuration
echo "\n5. Auth configuration:\n";
echo "   Default guard: " . config('auth.defaults.guard') . "\n";
echo "   Web guard driver: " . config('auth.guards.web.driver') . "\n";
echo "   Web guard provider: " . config('auth.guards.web.provider') . "\n";
echo "   Users provider driver: " . config('auth.providers.users.driver') . "\n";
echo "   Users provider model: " . config('auth.providers.users.model') . "\n";

// 6. Test APP_KEY
echo "\n6. Application key:\n";
$appKey = config('app.key');
echo "   APP_KEY exists: " . (!empty($appKey) ? 'Yes' : 'No') . "\n";
echo "   APP_KEY format: " . (str_starts_with($appKey, 'base64:') ? 'base64' : 'plain') . "\n";
echo "   APP_KEY length: " . strlen($appKey) . "\n";

// 7. Simulate login attempt
echo "\n7. Simulating login attempt:\n";
try {
    // Start session
    $session = app('session');
    $session->start();
    
    echo "   Session started: " . ($session->isStarted() ? 'Yes' : 'No') . "\n";
    echo "   Session ID: " . $session->getId() . "\n";
    
    // Try to authenticate
    $credentials = ['email' => 'superadmin@morra.com', 'password' => 'password'];
    
    if (Auth::attempt($credentials)) {
        echo "   ✅ Authentication successful\n";
        echo "   Authenticated user: " . Auth::user()->name . "\n";
        echo "   User ID: " . Auth::id() . "\n";
    } else {
        echo "   ❌ Authentication failed\n";
        
        // Check why it failed
        $user = User::where('email', 'superadmin@morra.com')->first();
        if (!$user) {
            echo "   Reason: User not found\n";
        } elseif (!Hash::check('password', $user->password)) {
            echo "   Reason: Password mismatch\n";
        } else {
            echo "   Reason: Unknown (possibly session/guard issue)\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ❌ Error during login simulation: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
echo "\nJika login masih gagal, kemungkinan masalah:\n";
echo "1. Session tidak bisa disimpan (permission issue)\n";
echo "2. CSRF token tidak valid\n";
echo "3. Browser tidak menerima cookies\n";
echo "4. Redirect loop di middleware\n";
echo "5. Database connection issue\n";