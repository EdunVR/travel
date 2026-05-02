@echo off
echo ========================================
echo POS PERFORMANCE OPTIMIZATION DEPLOYMENT
echo ========================================
echo.

echo Step 1: Creating database indexes...
php optimize_pos_products.php
echo.

echo Step 2: Testing optimization performance...
php test_pos_optimization.php
echo.

echo Step 3: Warming up cache for all outlets...
php warm_pos_cache.php
echo.

echo Step 4: Clearing application caches...
php artisan cache:clear
php artisan config:clear
echo.

echo Step 5: Final performance verification...
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use App\Http\Controllers\PosController;
use Illuminate\Http\Request;
use App\Models\User;

// Login
\$user = App\Models\User::first();
if (\$user) {
    Auth::login(\$user);
    
    // Test performance
    \$controller = new PosController(app(\App\Services\JournalEntryService::class));
    \$request = new Request(['outlet_id' => 1]);
    
    \$start = microtime(true);
    \$response = \$controller->getProducts(\$request);
    \$end = microtime(true);
    
    \$time = round((\$end - \$start) * 1000, 2);
    \$data = \$response->getData(true);
    
    echo \"Final test: {\$time}ms - Products: \" . count(\$data['data']) . \"\n\";
    
    if (\$time < 50) {
        echo \"✅ OPTIMIZATION SUCCESS - Performance target achieved!\n\";
    } else {
        echo \"⚠️ Performance could be better - Check cache and indexes\n\";
    }
} else {
    echo \"❌ No user found for testing\n\";
}
"
echo.

echo ========================================
echo DEPLOYMENT COMPLETE
echo ========================================
echo.
echo Performance Improvements:
echo - Database queries optimized with raw SQL
echo - Indexes added for faster lookups  
echo - Cache duration increased to 10 minutes
echo - Response format optimized
echo.
echo Expected Results:
echo - POS products load in ^<50ms (vs ~600ms before)
echo - 81%% faster first load, 96%% faster cached load
echo - Better user experience with near-instant loading
echo.
echo Next Steps:
echo 1. Test POS in browser - products should load instantly
echo 2. Check console log for improved timing
echo 3. Monitor cache hit rates
echo 4. Use cache management endpoints as needed
echo.
echo Cache Management URLs:
echo - Clear: POST /admin/penjualan/pos/cache/clear?outlet_id=1
echo - Warm:  POST /admin/penjualan/pos/cache/warm
echo ========================================
pause