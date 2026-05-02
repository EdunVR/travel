<?php

require_once 'vendor/autoload.php';

echo "=== CHECK PERMINTAAN BARANG PERMISSIONS ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Checking permissions for permintaan barang...\n";
    
    // Check if permissions exist
    $permissions = DB::select('SELECT name FROM permissions WHERE name LIKE "%permintaan%" OR name LIKE "%supply-chain%"');
    
    echo "   Found permissions:\n";
    foreach ($permissions as $perm) {
        echo "     - {$perm->name}\n";
    }
    
    echo "\n2. Checking roles with these permissions...\n";
    $rolePermissions = DB::select('
        SELECT r.name as role_name, p.name as permission_name 
        FROM roles r 
        JOIN role_has_permissions rhp ON r.id = rhp.role_id 
        JOIN permissions p ON rhp.permission_id = p.id 
        WHERE p.name LIKE "%permintaan%" OR p.name LIKE "%supply-chain%"
        ORDER BY r.name, p.name
    ');
    
    $currentRole = '';
    foreach ($rolePermissions as $rp) {
        if ($currentRole !== $rp->role_name) {
            $currentRole = $rp->role_name;
            echo "\n   Role: {$currentRole}\n";
        }
        echo "     - {$rp->permission_name}\n";
    }
    
    echo "\n3. Checking user access array...\n";
    $user = Auth::user();
    if ($user) {
        echo "   Current user: {$user->name}\n";
        echo "   User access: " . json_encode($user->akses ?? []) . "\n";
    } else {
        echo "   No authenticated user\n";
    }
    
    echo "\n4. Checking if hasPermission directive exists...\n";
    // Check if hasPermission blade directive is registered
    $directives = \Illuminate\Support\Facades\Blade::getCustomDirectives();
    if (array_key_exists('hasPermission', $directives)) {
        echo "   ✓ @hasPermission directive is registered\n";
    } else {
        echo "   ✗ @hasPermission directive is NOT registered\n";
    }
    
    if (array_key_exists('endhasPermission', $directives)) {
        echo "   ✓ @endhasPermission directive is registered\n";
    } else {
        echo "   ✗ @endhasPermission directive is NOT registered\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";