<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CREATING HPP PERMISSION ===\n";

try {
    // Check if permission already exists
    $existingPermission = \App\Models\Permission::where('name', 'inventaris.produk.hpp')->first();
    
    if ($existingPermission) {
        echo "Permission 'inventaris.produk.hpp' already exists with ID: {$existingPermission->id}\n";
    } else {
        // Create new permission
        $permission = \App\Models\Permission::create([
            'name' => 'inventaris.produk.hpp',
            'display_name' => 'Kelola HPP Produk',
            'description' => 'Dapat melihat dan mengelola HPP (Harga Pokok Penjualan) produk',
            'module' => 'inventaris',
            'menu' => 'produk',
            'action' => 'hpp'
        ]);
        
        echo "✅ Permission created successfully with ID: {$permission->id}\n";
    }
    
    // Find superadmin role (try different variations)
    $superadminRole = \App\Models\Role::whereIn('name', ['superadmin', 'super_admin', 'Super Admin'])->first();
    
    if (!$superadminRole) {
        // Try to find admin role
        $superadminRole = \App\Models\Role::where('name', 'like', '%admin%')->first();
    }
    
    if ($superadminRole) {
        echo "Found admin role: {$superadminRole->name} (ID: {$superadminRole->id})\n";
        
        // Get the permission
        $hppPermission = \App\Models\Permission::where('name', 'inventaris.produk.hpp')->first();
        
        // Check if role already has this permission
        $hasPermission = \DB::table('role_permissions')
            ->where('role_id', $superadminRole->id)
            ->where('permission_id', $hppPermission->id)
            ->exists();
            
        if (!$hasPermission) {
            // Assign permission to superadmin role
            \DB::table('role_permissions')->insert([
                'role_id' => $superadminRole->id,
                'permission_id' => $hppPermission->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "✅ Permission assigned to {$superadminRole->name} role\n";
        } else {
            echo "Permission already assigned to {$superadminRole->name} role\n";
        }
    } else {
        echo "⚠️ No admin role found. Please assign the permission manually.\n";
    }
    
    echo "\n=== FINAL CHECK ===\n";
    
    // List all produk permissions
    $produkPermissions = \App\Models\Permission::where('module', 'inventaris')
        ->where('menu', 'produk')
        ->orderBy('action')
        ->get(['id', 'name', 'action', 'display_name']);
    
    echo "All produk permissions:\n";
    foreach ($produkPermissions as $perm) {
        echo "- {$perm->action}: {$perm->name} ({$perm->display_name})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}