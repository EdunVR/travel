<?php

require_once 'vendor/autoload.php';

echo "=== CHECK DATABASE TABLES ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking database tables related to permissions/roles/users...\n";
    $tables = DB::select('SHOW TABLES');
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (strpos($tableName, 'permission') !== false || 
            strpos($tableName, 'role') !== false || 
            strpos($tableName, 'user') !== false ||
            strpos($tableName, 'akses') !== false) {
            echo "   - {$tableName}\n";
        }
    }
    
    echo "\n2. Checking users table structure...\n";
    $userColumns = DB::select('DESCRIBE users');
    foreach ($userColumns as $col) {
        if (strpos($col->Field, 'akses') !== false || strpos($col->Field, 'permission') !== false) {
            echo "   - {$col->Field} ({$col->Type})\n";
        }
    }
    
    echo "\n3. Checking sample user data...\n";
    $sampleUser = DB::select('SELECT id, name, akses FROM users LIMIT 1');
    if ($sampleUser) {
        $user = $sampleUser[0];
        echo "   User: {$user->name}\n";
        echo "   Akses: {$user->akses}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";