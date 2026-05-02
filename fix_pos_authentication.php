<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING POS AUTHENTICATION ISSUES ===\n\n";

// 1. Check session configuration
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

// 2. Check if sessions table exists (if using database driver)
if ($sessionDriver === 'database') {
    echo "\n2. Checking sessions table...\n";
    try {
        $sessionsCount = DB::table('sessions')->count();
        echo "Sessions table exists with {$sessionsCount} records\n";
    } catch (Exception $e) {
        echo "❌ Sessions table error: " . $e->getMessage() . "\n";
        echo "Creating sessions table...\n";
        
        // Create sessions table
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

// 3. Check cache configuration (for session storage)
echo "\n3. Checking cache configuration...\n";
$cacheDriver = config('cache.default');
echo "Cache driver: {$cacheDriver}\n";

if ($cacheDriver === 'file') {
    $cachePath = storage_path('framework/cache');
    if (!is_dir($cachePath)) {
        echo "Creating cache directory: {$cachePath}\n";
        mkdir($cachePath, 0755, true);
    }
    echo "Cache directory exists: {$cachePath}\n";
}

// 4. Clear all caches
echo "\n4. Clearing caches...\n";
try {
    Artisan::call('cache:clear');
    echo "✅ Application cache cleared\n";
} catch (Exception $e) {
    echo "❌ Failed to clear cache: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('config:clear');
    echo "✅ Configuration cache cleared\n";
} catch (Exception $e) {
    echo "❌ Failed to clear config: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('route:clear');
    echo "✅ Route cache cleared\n";
} catch (Exception $e) {
    echo "❌ Failed to clear routes: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('view:clear');
    echo "✅ View cache cleared\n";
} catch (Exception $e) {
    echo "❌ Failed to clear views: " . $e->getMessage() . "\n";
}

// 5. Clear POS specific cache
echo "\n5. Clearing POS specific cache...\n";
$cacheKeys = [
    'pos_products_outlet_1',
    'pos_products_outlet_2',
    'pos_products_outlet_3',
    'pos_customers_all'
];

foreach ($cacheKeys as $key) {
    try {
        Cache::forget($key);
        echo "✅ Cleared cache key: {$key}\n";
    } catch (Exception $e) {
        echo "❌ Failed to clear {$key}: " . $e->getMessage() . "\n";
    }
}

// 6. Check APP_KEY
echo "\n6. Checking APP_KEY...\n";
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

// 7. Check CSRF token functionality
echo "\n7. Testing CSRF token...\n";
try {
    $token = csrf_token();
    echo "✅ CSRF token generated: " . substr($token, 0, 10) . "...\n";
} catch (Exception $e) {
    echo "❌ CSRF token error: " . $e->getMessage() . "\n";
}

// 8. Test session functionality
echo "\n8. Testing session functionality...\n";
try {
    session()->put('test_key', 'test_value');
    $value = session()->get('test_key');
    if ($value === 'test_value') {
        echo "✅ Session read/write works\n";
    } else {
        echo "❌ Session read/write failed\n";
    }
    session()->forget('test_key');
} catch (Exception $e) {
    echo "❌ Session error: " . $e->getMessage() . "\n";
}

echo "\n=== AUTHENTICATION FIX COMPLETE ===\n";
echo "\nNext steps:\n";
echo "1. Restart your web server\n";
echo "2. Clear browser cache and cookies\n";
echo "3. Login again to the admin panel\n";
echo "4. Test POS products loading\n";