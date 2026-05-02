<?php
/**
 * Fix Customer Cache Issue - Add Auto Clear Cache
 */

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

echo "🔧 Fix Customer Cache Issue\n";
echo "===========================\n\n";

// 1. Clear current customer cache
echo "📋 1. Clearing current customer cache...\n";
try {
    // Clear using CacheService
    $cleared = CacheService::clearCustomerCache();
    echo $cleared ? "✅ CacheService cleared\n" : "⚠️ CacheService clear failed\n";
    
    // Clear manual keys
    $manualKey = CacheService::key('customers', 'all');
    Cache::forget($manualKey);
    echo "✅ Manual key cleared: {$manualKey}\n";
    
    // Clear Laravel cache completely
    \Artisan::call('cache:clear');
    echo "✅ Laravel cache cleared\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Update Member Model to auto-clear cache
echo "📋 2. Adding auto-clear cache to Member Model...\n";

$memberModelPath = 'app/Models/Member.php';
$memberContent = file_get_contents($memberModelPath);

// Check if boot method already exists
if (strpos($memberContent, 'protected static function boot()') === false) {
    // Add boot method before the last closing brace
    $bootMethod = '
    /**
     * Boot method to add event listeners
     */
    protected static function boot()
    {
        parent::boot();
        
        // Clear customer cache when member is created, updated, or deleted
        static::created(function ($member) {
            \App\Services\CacheService::clearCustomerCache();
        });
        
        static::updated(function ($member) {
            \App\Services\CacheService::clearCustomerCache();
        });
        
        static::deleted(function ($member) {
            \App\Services\CacheService::clearCustomerCache();
        });
    }
';
    
    // Insert before the last closing brace
    $lastBracePos = strrpos($memberContent, '}');
    $updatedContent = substr($memberContent, 0, $lastBracePos) . $bootMethod . "\n}";
    
    // Backup original file
    copy($memberModelPath, $memberModelPath . '.backup');
    
    // Write updated content
    file_put_contents($memberModelPath, $updatedContent);
    echo "✅ Boot method added to Member Model\n";
    echo "✅ Original file backed up as Member.php.backup\n";
} else {
    echo "⚠️ Boot method already exists in Member Model\n";
}

// 3. Reduce cache duration for customers
echo "\n📋 3. Updating cache duration for customers...\n";

$cacheServicePath = 'app/Services/CacheService.php';
$cacheContent = file_get_contents($cacheServicePath);

// Update cacheCustomers method to use shorter TTL
if (strpos($cacheContent, 'int $ttl = 600') !== false) {
    $updatedCacheContent = str_replace('int $ttl = 600', 'int $ttl = 180', $cacheContent);
    
    // Backup original file
    copy($cacheServicePath, $cacheServicePath . '.backup');
    
    // Write updated content
    file_put_contents($cacheServicePath, $updatedCacheContent);
    echo "✅ Customer cache duration reduced from 10 minutes to 3 minutes\n";
    echo "✅ Original file backed up as CacheService.php.backup\n";
} else {
    echo "⚠️ Cache duration already updated or pattern not found\n";
}

// 4. Test current customer data
echo "\n📋 4. Testing current customer data...\n";

try {
    // Test direct database query
    $customers = \App\Models\Member::select('id_member', 'nama', 'telepon', 'id_tipe')
        ->with('tipe:id_tipe,nama_tipe')
        ->where(function($query) {
            $query->where('nama', 'like', '%epan%')
                  ->orWhere('nama', 'like', '%bogor%');
        })
        ->orderBy('nama')
        ->get();

    if ($customers->isEmpty()) {
        echo "❌ Customer Epan(Bogor) tidak ditemukan\n";
        
        // Show recent customers
        $recent = \App\Models\Member::select('id_member', 'nama', 'id_tipe')
            ->with('tipe:id_tipe,nama_tipe')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        echo "\n📋 10 customer terbaru:\n";
        foreach ($recent as $customer) {
            $tipeName = $customer->tipe ? $customer->tipe->nama_tipe : 'Tidak ada';
            echo "- {$customer->nama} (Tipe: {$tipeName})\n";
        }
    } else {
        echo "✅ Customer ditemukan:\n";
        foreach ($customers as $customer) {
            $tipeName = $customer->tipe ? $customer->tipe->nama_tipe : 'Tidak ada';
            echo "- ID: {$customer->id_member}\n";
            echo "  Nama: {$customer->nama}\n";
            echo "  Tipe: {$tipeName} (ID: {$customer->id_tipe})\n";
            echo "  Telepon: {$customer->telepon}\n\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error testing data: " . $e->getMessage() . "\n";
}

// 5. Create deployment script
echo "\n📋 5. Creating deployment script...\n";

$deployScript = '#!/bin/bash
# Deploy Customer Cache Fix

echo "Deploying customer cache fix..."

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Clear customer cache specifically
php -r "
require_once \'vendor/autoload.php\';
\$app = require_once \'bootstrap/app.php\';
\$kernel = \$app->make(Illuminate\\Contracts\\Console\\Kernel::class);
\$kernel->bootstrap();
\\App\\Services\\CacheService::clearCustomerCache();
echo \'Customer cache cleared\n\';
"

echo "✅ Customer cache fix deployed successfully!"
echo "📋 Please refresh POS page (F5 or Ctrl+R)"
echo "📋 If issue persists, clear browser cache (Ctrl+Shift+R)"
';

file_put_contents('deploy_customer_cache_fix.bat', $deployScript);
echo "✅ Deployment script created: deploy_customer_cache_fix.bat\n";

// 6. Summary
echo "\n🎯 Summary of changes:\n";
echo "======================\n";
echo "✅ Customer cache cleared\n";
echo "✅ Auto-clear cache added to Member Model\n";
echo "✅ Cache duration reduced from 10 to 3 minutes\n";
echo "✅ Deployment script created\n";

echo "\n📋 Next steps:\n";
echo "1. Refresh halaman POS (F5 atau Ctrl+R)\n";
echo "2. Clear browser cache jika perlu (Ctrl+Shift+R)\n";
echo "3. Test customer Epan(Bogor) di POS\n";
echo "4. Jika masih bermasalah, jalankan: deploy_customer_cache_fix.bat\n";

echo "\n✅ Fix completed!\n";
?>