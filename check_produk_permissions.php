<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING PRODUK PERMISSIONS ===\n";

try {
    // Check existing permissions for produk
    $permissions = \App\Models\Permission::where('module', 'inventaris')
        ->where('menu', 'produk')
        ->get(['id', 'name', 'module', 'menu', 'action']);
    
    echo "Current produk permissions:\n";
    foreach ($permissions as $perm) {
        echo "- ID: {$perm->id}, Name: {$perm->name}, Action: {$perm->action}\n";
    }
    
    echo "\n=== CHECKING SUPERADMIN ROLE ===\n";
    
    // Check superadmin role
    $superadmin = \App\Models\Role::where('name', 'superadmin')->first();
    if ($superadmin) {
        echo "Superadmin role found: ID {$superadmin->id}\n";
    } else {
        echo "Superadmin role not found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}