<?php

require_once 'vendor/autoload.php';

echo "=== CHECK PERMISSIONS STRUCTURE ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking permissions table...\n";
    $permissions = DB::select('SELECT * FROM permissions WHERE name LIKE "%permintaan%" OR name LIKE "%supply-chain%" LIMIT 10');
    
    if (empty($permissions)) {
        echo "   No permissions found with 'permintaan' or 'supply-chain'\n";
        
        // Check all permissions
        echo "\n   Checking all permissions (first 20)...\n";
        $allPermissions = DB::select('SELECT name FROM permissions LIMIT 20');
        foreach ($allPermissions as $perm) {
            echo "     - {$perm->name}\n";
        }
    } else {
        foreach ($permissions as $perm) {
            echo "   - {$perm->name}\n";
        }
    }
    
    echo "\n2. Checking roles table...\n";
    $roles = DB::select('SELECT * FROM roles LIMIT 5');
    foreach ($roles as $role) {
        echo "   - ID: {$role->id}, Name: {$role->name}\n";
    }
    
    echo "\n3. Checking role_permissions table structure...\n";
    $rolePermColumns = DB::select('DESCRIBE role_permissions');
    foreach ($rolePermColumns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }
    
    echo "\n4. Checking sample role_permissions data...\n";
    $rolePerms = DB::select('SELECT rp.*, r.name as role_name, p.name as permission_name 
                             FROM role_permissions rp 
                             LEFT JOIN roles r ON rp.role_id = r.id 
                             LEFT JOIN permissions p ON rp.permission_id = p.id 
                             LIMIT 10');
    
    foreach ($rolePerms as $rp) {
        echo "   - Role: {$rp->role_name}, Permission: {$rp->permission_name}\n";
    }
    
    echo "\n5. Checking hasPermission function...\n";
    // Check if hasPermission function exists in helpers
    if (function_exists('hasPermission')) {
        echo "   ✓ hasPermission function exists\n";
    } else {
        echo "   ✗ hasPermission function does NOT exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";