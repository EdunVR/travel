<?php

require_once 'vendor/autoload.php';

echo "=== CHECK USER STRUCTURE ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "User table structure:\n";
    $columns = DB::select('DESCRIBE users');
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
    
    echo "\nSample user data:\n";
    $user = DB::select('SELECT id, name, role_id FROM users LIMIT 1');
    if ($user) {
        $u = $user[0];
        echo "  - ID: {$u->id}, Name: {$u->name}, Role ID: " . ($u->role_id ?? 'null') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";