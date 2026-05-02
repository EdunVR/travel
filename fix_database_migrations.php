<?php

/**
 * Script to fix database migrations order
 * Run with: php fix_database_migrations.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting database migration fix...\n\n";

// Drop all tables
echo "Dropping all tables...\n";
Artisan::call('migrate:fresh', ['--force' => true]);

// Run migrations in correct order
$orderedMigrations = [
    // First: Create base tables without foreign keys
    '2025_11_16_154241_create_users_table.php',
    '2025_11_16_154242_create_outlets_table.php',
    
    // Then: Create tables that depend on users/outlets
    '2024_12_02_000001_create_recruitments_table.php',
    
    // Skip the problematic one for now
    // '2024_12_02_000002_add_outlet_to_recruitments_table.php',
];

foreach ($orderedMigrations as $migration) {
    echo "Running: $migration\n";
    try {
        Artisan::call('migrate', [
            '--path' => 'database/migrations/' . $migration,
            '--force' => true
        ]);
        echo "  ✓ Success\n";
    } catch (\Exception $e) {
        echo "  ✗ Failed: " . $e->getMessage() . "\n";
    }
}

// Now run all remaining migrations
echo "\nRunning all remaining migrations...\n";
Artisan::call('migrate', ['--force' => true]);

echo "\n✓ Database migration fix complete!\n";
echo "Total tables: " . DB::table('information_schema.tables')
    ->where('table_schema', env('DB_DATABASE'))
    ->count() . "\n";
