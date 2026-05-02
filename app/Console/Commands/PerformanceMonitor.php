<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\CacheService;

class PerformanceMonitor extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'performance:monitor {--clear-cache : Clear all cache} {--warm-cache : Warm up cache} {--stats : Show performance stats}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor and optimize application performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Performance Monitor - ERP System');
        $this->info('====================================');

        if ($this->option('clear-cache')) {
            $this->clearCache();
        }

        if ($this->option('warm-cache')) {
            $this->warmCache();
        }

        if ($this->option('stats')) {
            $this->showStats();
        }

        if (!$this->option('clear-cache') && !$this->option('warm-cache') && !$this->option('stats')) {
            $this->showMenu();
        }

        return 0;
    }

    private function showMenu()
    {
        $choice = $this->choice('What would you like to do?', [
            'Show performance stats',
            'Clear all cache',
            'Warm up cache',
            'Run database optimization',
            'Check slow queries',
            'Exit'
        ]);

        switch ($choice) {
            case 'Show performance stats':
                $this->showStats();
                break;
            case 'Clear all cache':
                $this->clearCache();
                break;
            case 'Warm up cache':
                $this->warmCache();
                break;
            case 'Run database optimization':
                $this->optimizeDatabase();
                break;
            case 'Check slow queries':
                $this->checkSlowQueries();
                break;
            case 'Exit':
                $this->info('👋 Goodbye!');
                return;
        }
    }

    private function showStats()
    {
        $this->info('📊 Performance Statistics');
        $this->info('-------------------------');

        // Database stats
        try {
            $dbSize = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb,
                    COUNT(*) as table_count
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ")[0];

            $this->info("Database Size: {$dbSize->size_mb} MB ({$dbSize->table_count} tables)");

            // Connection count
            $connections = DB::select("SHOW STATUS LIKE 'Threads_connected'")[0];
            $this->info("Active Connections: {$connections->Value}");

            // Query cache stats
            $queryCache = DB::select("SHOW STATUS LIKE 'Qcache%'");
            foreach ($queryCache as $stat) {
                if ($stat->Variable_name === 'Qcache_hits') {
                    $this->info("Query Cache Hits: {$stat->Value}");
                }
            }

        } catch (\Exception $e) {
            $this->error("Database stats error: " . $e->getMessage());
        }

        // Cache stats
        $cacheStats = CacheService::getStats();
        $this->info("Cache Driver: {$cacheStats['driver']}");
        $this->info("Cache Status: {$cacheStats['status']}");
        if (isset($cacheStats['entries'])) {
            $this->info("Cache Entries: {$cacheStats['entries']}");
        }

        // Memory usage
        $memoryUsage = memory_get_usage(true) / 1024 / 1024;
        $memoryPeak = memory_get_peak_usage(true) / 1024 / 1024;
        $this->info("Memory Usage: " . round($memoryUsage, 2) . " MB");
        $this->info("Memory Peak: " . round($memoryPeak, 2) . " MB");

        // Laravel stats
        $this->info("Laravel Version: " . app()->version());
        $this->info("PHP Version: " . PHP_VERSION);
        $this->info("Environment: " . app()->environment());
    }

    private function clearCache()
    {
        $this->info('🧹 Clearing Cache');
        $this->info('----------------');

        $this->info('Clearing application cache...');
        $this->call('cache:clear');

        $this->info('Clearing config cache...');
        $this->call('config:clear');

        $this->info('Clearing view cache...');
        $this->call('view:clear');

        $this->info('Clearing route cache...');
        $this->call('route:clear');

        // Clear custom caches
        $this->info('Clearing custom caches...');
        CacheService::clearProductCache();
        CacheService::clearCustomerCache();
        CacheService::clearOutletCache();
        CacheService::clearFinanceCache();
        CacheService::clearSettingsCache();

        $this->info('✅ All caches cleared successfully!');
    }

    private function warmCache()
    {
        $this->info('🔥 Warming Up Cache');
        $this->info('------------------');

        $results = CacheService::warmUp();
        
        foreach ($results as $result) {
            if (strpos($result, 'error') !== false) {
                $this->error($result);
            } else {
                $this->info("✅ $result");
            }
        }

        $this->info('🎉 Cache warm-up completed!');
    }

    private function optimizeDatabase()
    {
        $this->info('🔧 Database Optimization');
        $this->info('------------------------');

        $tables = ['produk', 'penjualan', 'piutang', 'hpp_produk', 'pos_sales', 'member'];

        foreach ($tables as $table) {
            try {
                $this->info("Optimizing table: $table");
                DB::statement("OPTIMIZE TABLE `$table`");
                $this->info("✅ $table optimized");
            } catch (\Exception $e) {
                $this->error("❌ Failed to optimize $table: " . $e->getMessage());
            }
        }

        $this->info('🎉 Database optimization completed!');
    }

    private function checkSlowQueries()
    {
        $this->info('🐌 Checking Slow Queries');
        $this->info('------------------------');

        try {
            // Check if slow query log is enabled
            $slowLogStatus = DB::select("SHOW VARIABLES LIKE 'slow_query_log'")[0];
            $this->info("Slow Query Log: {$slowLogStatus->Value}");

            if ($slowLogStatus->Value === 'OFF') {
                $this->warn('Slow query log is disabled. Enable it to track slow queries.');
                return;
            }

            // Get slow query log file location
            $slowLogFile = DB::select("SHOW VARIABLES LIKE 'slow_query_log_file'")[0];
            $this->info("Log File: {$slowLogFile->Value}");

            // Get long query time threshold
            $longQueryTime = DB::select("SHOW VARIABLES LIKE 'long_query_time'")[0];
            $this->info("Long Query Time: {$longQueryTime->Value} seconds");

            // Test some potentially slow queries
            $this->info('Running performance tests...');
            
            $testQueries = [
                'Products with stock' => "SELECT COUNT(*) FROM produk p LEFT JOIN hpp_produk h ON p.id_produk = h.id_produk WHERE h.stok > 0",
                'Recent sales' => "SELECT COUNT(*) FROM penjualan WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
                'Outstanding piutang' => "SELECT COUNT(*) FROM piutang WHERE status = 'belum_lunas'"
            ];

            foreach ($testQueries as $name => $query) {
                $start = microtime(true);
                DB::select($query);
                $duration = (microtime(true) - $start) * 1000;
                
                $status = $duration > 100 ? '🐌' : ($duration > 50 ? '⚡' : '✅');
                $this->info("$status $name: " . round($duration, 2) . " ms");
            }

        } catch (\Exception $e) {
            $this->error("Error checking slow queries: " . $e->getMessage());
        }
    }
}