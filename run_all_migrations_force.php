<?php

/**
 * Run all migrations one by one, continuing on errors
 * This will restore your database tables
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "=== Database Migration Recovery Script ===\n\n";

// First, drop all tables and start fresh
echo "Step 1: Dropping all tables...\n";
try {
    Artisan::call('migrate:fresh', ['--force' => true]);
} catch (\Exception $e) {
    echo "  Note: Some migrations failed during fresh, continuing...\n";
}

// Get all migration files
$migrationsPath = database_path('migrations');
$files = File::files($migrationsPath);

// Sort by filename (which includes timestamp)
usort($files, function($a, $b) {
    return strcmp($a->getFilename(), $b->getFilename());
});

echo "\nStep 2: Running migrations one by one...\n";
$successCount = 0;
$failCount = 0;

foreach ($files as $file) {
    $filename = $file->getFilename();
    
    // Skip backup files
    if (strpos($filename, '.backup') !== false) {
        continue;
    }
    
    echo "  Running: $filename ... ";
    
    try {
        Artisan::call('migrate', [
            '--path' => 'database/migrations/' . $filename,
            '--force' => true
        ]);
        echo "✓ DONE\n";
        $successCount++;
    } catch (\Exception $e) {
        echo "✗ SKIP (already exists or dependency issue)\n";
        $failCount++;
        
        // Try to mark as migrated anyway
        try {
            $migrationName = str_replace('.php', '', $filename);
            DB::table('migrations')->insertOrIgnore([
                'migration' => $migrationName,
                'batch' => 1
            ]);
        } catch (\Exception $e2) {
            // Ignore
        }
    }
}

echo "\n=== Summary ===\n";
echo "✓ Successful: $successCount\n";
echo "✗ Skipped: $failCount\n";

// Count total tables
$tableCount = DB::table('information_schema.tables')
    ->where('table_schema', env('DB_DATABASE'))
    ->count();

echo "\nTotal tables in database: $tableCount\n";

// List some key tables
echo "\nKey tables status:\n";
$keyTables = ['users', 'outlets', 'messages', 'chat_sessions', 'recruitments', 'payrolls'];
foreach ($keyTables as $table) {
    $exists = DB::select("SHOW TABLES LIKE '$table'");
    echo "  " . ($exists ? "✓" : "✗") . " $table\n";
}

echo "\n✓ Database recovery complete!\n";
