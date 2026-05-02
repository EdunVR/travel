<?php
/**
 * Debug Login dan Session Issues
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Debug Login dan Session Issues ===\n\n";

// 1. Periksa konfigurasi session
echo "1. Konfigurasi Session:\n";
echo "   Driver: " . config('session.driver') . "\n";
echo "   Lifetime: " . config('session.lifetime') . " menit\n";
echo "   Domain: " . config('session.domain') . "\n";
echo "   Path: " . config('session.path') . "\n";
echo "   Secure: " . (config('session.secure') ? 'Ya' : 'Tidak') . "\n";
echo "   HTTP Only: " . (config('session.http_only') ? 'Ya' : 'Tidak') . "\n";
echo "   Same Site: " . config('session.same_site') . "\n\n";

// 2. Periksa tabel sessions
echo "2. Tabel Sessions:\n";
try {
    $sessionTable = config('session.table', 'sessions');
    $totalSessions = DB::table($sessionTable)->count();
    echo "   Total sessions: $totalSessions\n";
    
    // Sessions aktif (dalam 1 jam terakhir)
    $activeSessions = DB::table($sessionTable)
        ->where('last_activity', '>', time() - 3600)
        ->count();
    echo "   Sessions aktif (1 jam): $activeSessions\n";
    
    // Sessions dengan user_id
    $userSessions = DB::table($sessionTable)
        ->whereNotNull('user_id')
        ->where('last_activity', '>', time() - 3600)
        ->get(['id', 'user_id', 'last_activity', 'ip_address']);
    
    echo "   Sessions dengan user:\n";
    foreach ($userSessions as $session) {
        $lastActivity = date('Y-m-d H:i:s', $session->last_activity);
        echo "     - User ID: {$session->user_id}, IP: {$session->ip_address}, Last: $lastActivity\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Periksa users dan password
echo "3. Data Users:\n";
try {
    $users = DB::table('users')->get(['id', 'name', 'email', 'created_at']);
    echo "   Total users: " . count($users) . "\n";
    
    foreach ($users as $user) {
        echo "     - ID: {$user->id}, Email: {$user->email}, Name: {$user->name}\n";
    }
    
    // Test password untuk superadmin
    $superadmin = DB::table('users')->where('email', 'superadmin@morra.com')->first();
    if ($superadmin) {
        echo "\n   Testing superadmin password:\n";
        
        // Test beberapa password umum
        $testPasswords = ['password', '123456', 'admin', 'superadmin', 'morra123'];
        
        foreach ($testPasswords as $testPass) {
            if (Hash::check($testPass, $superadmin->password)) {
                echo "     ✅ Password yang benar: '$testPass'\n";
                break;
            } else {
                echo "     ❌ Bukan password: '$testPass'\n";
            }
        }
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Periksa middleware dan routes
echo "4. Authentication Middleware:\n";
try {
    // Test route POS
    $request = \Illuminate\Http\Request::create('/admin/penjualan/pos', 'GET');
    $route = app('router')->getRoutes()->match($request);
    
    echo "   POS Route: " . $route->getName() . "\n";
    echo "   Middleware: " . implode(', ', $route->middleware()) . "\n";
    
    // Test route products
    $request2 = \Illuminate\Http\Request::create('/admin/penjualan/pos/products', 'GET');
    $route2 = app('router')->getRoutes()->match($request2);
    
    echo "   POS Products Route: " . $route2->getName() . "\n";
    echo "   Middleware: " . implode(', ', $route2->middleware()) . "\n";
    
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Periksa APP_KEY
echo "5. Application Key:\n";
$appKey = config('app.key');
echo "   APP_KEY exists: " . (!empty($appKey) ? 'Ya' : 'Tidak') . "\n";
echo "   APP_KEY length: " . strlen($appKey) . "\n";
if (strlen($appKey) < 32) {
    echo "   ⚠️  WARNING: APP_KEY terlalu pendek!\n";
}
echo "\n";

// 6. Periksa CSRF token dalam session
echo "6. CSRF Token Test:\n";
try {
    // Simulasi session start
    $session = app('session');
    if (!$session->isStarted()) {
        $session->start();
    }
    
    $token = $session->token();
    echo "   CSRF Token generated: " . (!empty($token) ? 'Ya' : 'Tidak') . "\n";
    echo "   Token length: " . strlen($token) . "\n";
    
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== Rekomendasi Perbaikan ===\n";
echo "1. Jika password tidak diketahui, reset dengan: php artisan tinker\n";
echo "   User::where('email', 'superadmin@morra.com')->update(['password' => Hash::make('password123')]);\n";
echo "2. Jika session bermasalah, coba hapus semua session: DELETE FROM sessions;\n";
echo "3. Pastikan APP_KEY sudah di-generate: php artisan key:generate\n";
echo "4. Clear semua cache: php artisan optimize:clear\n";
echo "5. Periksa permission folder storage/framework/sessions\n";