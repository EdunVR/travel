<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check if signature_path column exists
    if (Schema::hasColumn('users', 'signature_path')) {
        echo "✓ signature_path column already exists in users table.\n";
    } else {
        echo "✗ signature_path column does not exist in users table.\n";
        echo "Running migration to add signature_path column...\n";
        
        // Add the column manually
        DB::statement('ALTER TABLE users ADD COLUMN signature_path VARCHAR(255) NULL AFTER avatar');
        echo "✓ signature_path column added successfully.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}