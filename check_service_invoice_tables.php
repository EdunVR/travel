<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SERVICE INVOICE TABLES CHECK ===\n\n";

try {
    // Check if service_invoices table exists
    echo "1. Checking service_invoices table...\n";
    if (Schema::hasTable('service_invoices')) {
        echo "✅ service_invoices table EXISTS\n";
        
        $columns = Schema::getColumnListing('service_invoices');
        echo "   Columns: " . implode(', ', $columns) . "\n";
        
        $count = DB::table('service_invoices')->count();
        echo "   Records: {$count}\n";
    } else {
        echo "❌ service_invoices table MISSING\n";
    }
    
    echo "\n2. Checking service_invoice_items table...\n";
    if (Schema::hasTable('service_invoice_items')) {
        echo "✅ service_invoice_items table EXISTS\n";
        
        $columns = Schema::getColumnListing('service_invoice_items');
        echo "   Columns: " . implode(', ', $columns) . "\n";
        
        $count = DB::table('service_invoice_items')->count();
        echo "   Records: {$count}\n";
    } else {
        echo "❌ service_invoice_items table MISSING\n";
    }
    
    echo "\n3. Checking migration status...\n";
    $migrations = DB::table('migrations')
        ->where('migration', 'like', '%service_invoice%')
        ->get();
    
    if ($migrations->count() > 0) {
        echo "✅ Service invoice migrations found:\n";
        foreach ($migrations as $migration) {
            echo "   - {$migration->migration} (batch: {$migration->batch})\n";
        }
    } else {
        echo "❌ No service invoice migrations found in migrations table\n";
    }
    
    echo "\n4. Checking all tables in database...\n";
    $tables = DB::select('SHOW TABLES');
    $tableNames = [];
    foreach ($tables as $table) {
        $tableArray = (array) $table;
        $tableNames[] = array_values($tableArray)[0];
    }
    
    $serviceRelatedTables = array_filter($tableNames, function($table) {
        return strpos($table, 'service') !== false;
    });
    
    if (!empty($serviceRelatedTables)) {
        echo "✅ Service-related tables found:\n";
        foreach ($serviceRelatedTables as $table) {
            echo "   - {$table}\n";
        }
    } else {
        echo "❌ No service-related tables found\n";
    }
    
    echo "\n5. Checking if migration file exists...\n";
    $migrationFile = 'database/migrations/2024_12_04_200000_create_service_invoice_tables.php';
    if (file_exists($migrationFile)) {
        echo "✅ Migration file exists: {$migrationFile}\n";
    } else {
        echo "❌ Migration file missing: {$migrationFile}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}