<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outlet;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
// use Spatie\Permission\Models\Role;

class SistemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:sistem.view')->only(['index']);
        $this->middleware('permission:sistem.backup')->only(['backup', 'restore', 'downloadBackup']);
        $this->middleware('permission:sistem.maintenance')->only(['clearCache', 'runMigration', 'optimizeDatabase']);
    }

    /**
     * Display sistem dashboard.
     */
    public function index()
    {
        try {
            // Get statistics
            $totalOutlets = Outlet::count();
            $totalUsers = User::count();
            $totalRoles = DB::table('roles')->count();
            $totalConfigs = CompanySetting::count();

            return view('admin.sistem.index', compact(
                'totalOutlets',
                'totalUsers', 
                'totalRoles',
                'totalConfigs'
            ));

        } catch (\Exception $e) {
            return view('admin.sistem.index', [
                'totalOutlets' => 0,
                'totalUsers' => 0,
                'totalRoles' => 0,
                'totalConfigs' => 0
            ]);
        }
    }

    /**
     * Get system information.
     */
    public function getSystemInfo()
    {
        try {
            $info = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'environment' => app()->environment(),
                'database_connection' => config('database.default'),
                'database_name' => config('database.connections.mysql.database'),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'disk_free_space' => $this->formatBytes(disk_free_space('.')),
                'disk_total_space' => $this->formatBytes(disk_total_space('.')),
            ];

            return response()->json([
                'success' => true,
                'data' => $info
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil informasi sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        try {
            // Clear various caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run database migrations.
     */
    public function runMigration()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Migrasi database berhasil dijalankan',
                'output' => Artisan::output()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjalankan migrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize database.
     */
    public function optimizeDatabase()
    {
        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = config('database.connections.mysql.database');
            $tableColumn = "Tables_in_{$databaseName}";

            $optimizedTables = [];
            
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $optimizedTables[] = $tableName;
            }

            return response()->json([
                'success' => true,
                'message' => 'Database berhasil dioptimasi',
                'optimized_tables' => $optimizedTables
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengoptimasi database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create database backup.
     */
    public function createBackup()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);

            // Create backups directory if not exists
            if (!Storage::exists('backups')) {
                Storage::makeDirectory('backups');
            }

            // Database configuration
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Create mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($backupPath)
            );

            // Execute backup command
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($backupPath)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup berhasil dibuat',
                    'filename' => $filename,
                    'size' => $this->formatBytes(filesize($backupPath))
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat backup database'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of backups.
     */
    public function getBackups()
    {
        try {
            $backupFiles = Storage::files('backups');
            $backups = [];

            foreach ($backupFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $backups[] = [
                        'filename' => basename($file),
                        'size' => $this->formatBytes(Storage::size($file)),
                        'created_at' => date('Y-m-d H:i:s', Storage::lastModified($file))
                    ];
                }
            }

            // Sort by creation date (newest first)
            usort($backups, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return response()->json([
                'success' => true,
                'data' => $backups
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download backup file.
     */
    public function downloadBackup($filename)
    {
        try {
            $filePath = 'backups/' . $filename;
            
            if (!Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan'
                ], 404);
            }

            return Storage::download($filePath);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete backup file.
     */
    public function deleteBackup($filename)
    {
        try {
            $filePath = 'backups/' . $filename;
            
            if (!Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan'
                ], 404);
            }

            Storage::delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore database from backup.
     */
    public function restoreBackup(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        try {
            $filename = $request->input('filename');
            $filePath = storage_path('app/backups/' . $filename);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan'
                ], 404);
            }

            // Database configuration
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Create mysql restore command
            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );

            // Execute restore command
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Database berhasil direstore dari backup'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal restore database dari backup'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal restore backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application settings.
     */
    public function getAppSettings()
    {
        try {
            $settings = [
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'app_url' => config('app.url'),
                'app_timezone' => config('app.timezone'),
                'app_locale' => config('app.locale'),
                'session_lifetime' => config('session.lifetime'),
                'session_driver' => config('session.driver'),
                'cache_driver' => config('cache.default'),
                'queue_driver' => config('queue.default'),
                'mail_driver' => config('mail.default'),
            ];

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil pengaturan aplikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}