<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Default cache duration in seconds (15 minutes)
     */
    const DEFAULT_TTL = 900;

    /**
     * Cache prefixes for different data types
     */
    const PREFIXES = [
        'products' => 'products',
        'customers' => 'customers',
        'outlets' => 'outlets',
        'reports' => 'reports',
        'settings' => 'settings',
        'pos' => 'pos',
        'finance' => 'finance'
    ];

    /**
     * Remember a value in cache with automatic key generation
     */
    public static function remember(string $key, callable $callback, int $ttl = self::DEFAULT_TTL)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning('Cache remember failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to direct callback execution
            return $callback();
        }
    }

    /**
     * Store a value in cache
     */
    public static function put(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        try {
            return Cache::put($key, $value, $ttl);
        } catch (\Exception $e) {
            Log::warning('Cache put failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get a value from cache
     */
    public static function get(string $key, $default = null)
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::warning('Cache get failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Forget a cache key
     */
    public static function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::warning('Cache forget failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clear cache by prefix/pattern
     */
    public static function clearByPrefix(string $prefix): bool
    {
        try {
            // For database cache driver, we need to clear manually
            if (config('cache.default') === 'database') {
                return \DB::table('cache')
                    ->where('key', 'like', config('cache.prefix') . ':' . $prefix . '%')
                    ->delete() > 0;
            }
            
            // For other drivers, use tags if supported
            if (method_exists(Cache::store(), 'tags')) {
                return Cache::tags($prefix)->flush();
            }
            
            // Fallback: clear all cache (not ideal but works)
            return Cache::flush();
            
        } catch (\Exception $e) {
            Log::warning('Cache clear by prefix failed', [
                'prefix' => $prefix,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generate cache key with prefix
     */
    public static function key(string $prefix, ...$parts): string
    {
        $keyParts = array_merge([self::PREFIXES[$prefix] ?? $prefix], $parts);
        return implode(':', array_map('strval', $keyParts));
    }

    /**
     * Cache products for POS
     */
    public static function cacheProducts(int $outletId, callable $callback, int $ttl = 300)
    {
        $key = self::key('products', 'pos', $outletId);
        return self::remember($key, $callback, $ttl);
    }

    /**
     * Cache customers
     */
    public static function cacheCustomers(callable $callback, int $ttl = 180)
    {
        $key = self::key('customers', 'all');
        return self::remember($key, $callback, $ttl);
    }

    /**
     * Cache outlets for user
     */
    public static function cacheUserOutlets(int $userId, callable $callback, int $ttl = 1800)
    {
        $key = self::key('outlets', 'user', $userId);
        return self::remember($key, $callback, $ttl);
    }

    /**
     * Cache financial reports
     */
    public static function cacheFinanceReport(string $reportType, array $params, callable $callback, int $ttl = 3600)
    {
        $key = self::key('finance', $reportType, md5(serialize($params)));
        return self::remember($key, $callback, $ttl);
    }

    /**
     * Cache settings
     */
    public static function cacheSettings(string $settingType, int $outletId, callable $callback, int $ttl = 1800)
    {
        $key = self::key('settings', $settingType, $outletId);
        return self::remember($key, $callback, $ttl);
    }

    /**
     * Clear product cache when products are updated
     */
    public static function clearProductCache(int $outletId = null): bool
    {
        if ($outletId) {
            return self::forget(self::key('products', 'pos', $outletId));
        }
        
        return self::clearByPrefix('products');
    }

    /**
     * Clear customer cache when customers are updated
     */
    public static function clearCustomerCache(): bool
    {
        return self::clearByPrefix('customers');
    }

    /**
     * Clear outlet cache when outlets are updated
     */
    public static function clearOutletCache(int $userId = null): bool
    {
        if ($userId) {
            return self::forget(self::key('outlets', 'user', $userId));
        }
        
        return self::clearByPrefix('outlets');
    }

    /**
     * Clear finance report cache
     */
    public static function clearFinanceCache(): bool
    {
        return self::clearByPrefix('finance');
    }

    /**
     * Clear settings cache
     */
    public static function clearSettingsCache(string $settingType = null, int $outletId = null): bool
    {
        if ($settingType && $outletId) {
            return self::forget(self::key('settings', $settingType, $outletId));
        }
        
        return self::clearByPrefix('settings');
    }

    /**
     * Get cache statistics (if supported)
     */
    public static function getStats(): array
    {
        try {
            $stats = [
                'driver' => config('cache.default'),
                'prefix' => config('cache.prefix'),
                'status' => 'active'
            ];

            // Add driver-specific stats if available
            if (config('cache.default') === 'database') {
                $count = \DB::table('cache')->count();
                $stats['entries'] = $count;
            }

            return $stats;
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Warm up cache with commonly used data
     */
    public static function warmUp(): array
    {
        $results = [];
        
        try {
            // Warm up outlets cache for active users
            $activeUsers = \App\Models\User::where('is_active', true)->limit(10)->get();
            foreach ($activeUsers as $user) {
                $key = self::key('outlets', 'user', $user->id);
                if (!Cache::has($key)) {
                    self::cacheUserOutlets($user->id, function() use ($user) {
                        return $user->outlets()->where('is_active', true)->get();
                    });
                    $results[] = "Warmed outlets cache for user {$user->id}";
                }
            }

            // Warm up customers cache
            $key = self::key('customers', 'all');
            if (!Cache::has($key)) {
                self::cacheCustomers(function() {
                    return \App\Models\Member::select('id_member', 'nama', 'telepon', 'id_tipe')
                        ->with('tipe:id_tipe,nama_tipe')
                        ->orderBy('nama')
                        ->get();
                });
                $results[] = "Warmed customers cache";
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Cache warm up failed', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}