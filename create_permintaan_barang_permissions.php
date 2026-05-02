<?php

require_once 'vendor/autoload.php';

echo "=== CREATE PERMINTAAN BARANG PERMISSIONS ===\n\n";

try {
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Creating permissions for Permintaan Barang...\n";
    
    $permissions = [
        [
            'name' => 'supply-chain.permintaan-barang.view',
            'display_name' => 'View Permintaan Barang',
            'module' => 'supply-chain',
            'menu' => 'permintaan-barang',
            'action' => 'view'
        ],
        [
            'name' => 'supply-chain.permintaan-barang.create',
            'display_name' => 'Create Permintaan Barang',
            'module' => 'supply-chain',
            'menu' => 'permintaan-barang',
            'action' => 'create'
        ],
        [
            'name' => 'supply-chain.permintaan-barang.update',
            'display_name' => 'Update Permintaan Barang',
            'module' => 'supply-chain',
            'menu' => 'permintaan-barang',
            'action' => 'update'
        ],
        [
            'name' => 'supply-chain.permintaan-barang.delete',
            'display_name' => 'Delete Permintaan Barang',
            'module' => 'supply-chain',
            'menu' => 'permintaan-barang',
            'action' => 'delete'
        ],
        [
            'name' => 'supply-chain.permintaan-barang.approve',
            'display_name' => 'Approve Permintaan Barang',
            'module' => 'supply-chain',
            'menu' => 'permintaan-barang',
            'action' => 'approve'
        ],
        [
            'name' => 'supply-chain.permintaan-barang.reject',
            'display_name' => 'Reject Permintaan Barang',
            'module' => 'supply-chain',
            'menu' => 'permintaan-barang',
            'action' => 'reject'
        ],
    ];
    
    foreach ($permissions as $perm) {
        // Check if permission already exists
        $existing = DB::select('SELECT id FROM permissions WHERE name = ?', [$perm['name']]);
        
        if (empty($existing)) {
            DB::insert('INSERT INTO permissions (name, display_name, module, menu, action, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())', 
                      [$perm['name'], $perm['display_name'], $perm['module'], $perm['menu'], $perm['action']]);
            echo "   ✓ Created: {$perm['name']}\n";
        } else {
            echo "   - Already exists: {$perm['name']}\n";
        }
    }
    
    echo "\n2. Assigning permissions to Super Admin role...\n";
    
    // Get super admin role
    $superAdminRole = DB::select('SELECT id FROM roles WHERE name = ? OR name = ?', ['super_admin', 'Super Admin']);
    
    if (!empty($superAdminRole)) {
        $roleId = $superAdminRole[0]->id;
        echo "   Found Super Admin role ID: {$roleId}\n";
        
        foreach ($permissions as $perm) {
            // Get permission ID
            $permission = DB::select('SELECT id FROM permissions WHERE name = ?', [$perm['name']]);
            
            if (!empty($permission)) {
                $permissionId = $permission[0]->id;
                
                // Check if role already has this permission
                $existing = DB::select('SELECT id FROM role_permissions WHERE role_id = ? AND permission_id = ?', 
                                     [$roleId, $permissionId]);
                
                if (empty($existing)) {
                    DB::insert('INSERT INTO role_permissions (role_id, permission_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())', 
                              [$roleId, $permissionId]);
                    echo "   ✓ Assigned: {$perm['name']} to Super Admin\n";
                } else {
                    echo "   - Already assigned: {$perm['name']}\n";
                }
            }
        }
    } else {
        echo "   ✗ Super Admin role not found\n";
    }
    
    echo "\n3. Verifying created permissions...\n";
    $createdPermissions = DB::select('SELECT name, display_name FROM permissions WHERE name LIKE "supply-chain.permintaan-barang.%"');
    
    echo "   Created permissions:\n";
    foreach ($createdPermissions as $perm) {
        echo "     - {$perm->name} ({$perm->display_name})\n";
    }
    
    echo "\n✅ PERMISSIONS CREATED SUCCESSFULLY!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== CREATION COMPLETED ===\n";