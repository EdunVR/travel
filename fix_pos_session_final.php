<?php
/**
 * Fix POS Session Issues - Final Solution
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Fix POS Session Issues - Final Solution ===\n\n";

// 1. Clear all sessions
echo "1. Membersihkan semua session lama...\n";
try {
    $deletedSessions = DB::table('sessions')->delete();
    echo "   ✅ Berhasil menghapus $deletedSessions session lama\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 2. Reset password superadmin untuk memastikan
echo "\n2. Memastikan password superadmin...\n";
try {
    $updated = DB::table('users')
        ->where('email', 'superadmin@morra.com')
        ->update(['password' => Hash::make('password')]);
    
    if ($updated) {
        echo "   ✅ Password superadmin di-reset ke 'password'\n";
    } else {
        echo "   ⚠️  User superadmin tidak ditemukan\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 3. Test session configuration
echo "\n3. Testing session configuration...\n";
echo "   Driver: " . config('session.driver') . "\n";
echo "   Domain: " . (config('session.domain') ?: 'null (default)') . "\n";
echo "   Secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "   Lifetime: " . config('session.lifetime') . " minutes\n";

// 4. Test CSRF token generation
echo "\n4. Testing CSRF token...\n";
try {
    $session = app('session');
    $session->start();
    $token = $session->token();
    echo "   ✅ CSRF token generated: " . substr($token, 0, 10) . "...\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 5. Test routes
echo "\n5. Testing POS routes...\n";
$routes = [
    'admin.dashboard' => 'Admin Dashboard',
    'admin.penjualan.pos.index' => 'POS Index',
    'admin.penjualan.pos.products' => 'POS Products',
    'admin.penjualan.pos.coa.settings' => 'POS COA Settings'
];

foreach ($routes as $routeName => $description) {
    try {
        $url = route($routeName);
        echo "   ✅ $description: $url\n";
    } catch (Exception $e) {
        echo "   ❌ $description: Error - " . $e->getMessage() . "\n";
    }
}

echo "\n=== Perbaikan Selesai ===\n";
echo "\nLangkah selanjutnya:\n";
echo "1. Clear cache: php artisan optimize:clear\n";
echo "2. Login dengan: superadmin@morra.com / password\n";
echo "3. Test POS dengan berganti outlet\n";
echo "4. Periksa browser console untuk error\n";
echo "\nJika masih bermasalah:\n";
echo "- Pastikan mengakses dari domain yang benar\n";
echo "- Clear browser cache dan cookies\n";
echo "- Periksa network tab di developer tools\n";