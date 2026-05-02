<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "🔍 Checking available roles...\n";
    
    $roles = DB::table('roles')->get();
    
    if ($roles->isEmpty()) {
        echo "❌ No roles found!\n";
        exit(1);
    }
    
    echo "📋 Found " . $roles->count() . " roles:\n";
    foreach ($roles as $role) {
        echo "   - ID: {$role->id}, Name: {$role->name}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}