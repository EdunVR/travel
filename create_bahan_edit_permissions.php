<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CREATING BAHAN EDIT PERMISSIONS ===\n";

$permissions = [
    [
        'name' => 'inventaris.bahan.edit-stock',
        'display_name' => 'Edit Stock Bahan',
        'module' => 'inventaris',
        'menu' => 'bahan',
        'action' => 'edit-stock',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'name' => 'inventaris.bahan.edit-price',
        'display_name' => 'Edit Harga Beli Bahan',
        'module' => 'inventaris',
        'menu' => 'bahan',
        'action' => 'edit-price',
        'created_at' => now(),
        'updated_at' => now()
    ]
];

foreach($permissions as $permission) {
    // Check if permission already exists
    $exists = DB::table('permissions')->where('name', $permission['name'])->exists();
    
    if (!$exists) {
        DB::table('permissions')->insert($permission);
        echo "✅ Created permission: {$permission['name']}\n";
    } else {
        echo "⚠️  Permission already exists: {$permission['name']}\n";
    }
}

echo "\n=== ASSIGNING PERMISSIONS TO SUPERADMIN ===\n";

// Get superadmin role
$superadminRole = DB::table('roles')->where('name', 'superadmin')->first();

if ($superadminRole) {
    foreach($permissions as $permission) {
        $permissionRecord = DB::table('permissions')->where('name', $permission['name'])->first();
        
        if ($permissionRecord) {
            // Check if role already has permission
            $hasPermission = DB::table('role_has_permissions')
                ->where('role_id', $superadminRole->id)
                ->where('permission_id', $permissionRecord->id)
                ->exists();
            
            if (!$hasPermission) {
                DB::table('role_has_permissions')->insert([
                    'role_id' => $superadminRole->id,
                    'permission_id' => $permissionRecord->id
                ]);
                echo "✅ Assigned {$permission['name']} to superadmin\n";
            } else {
                echo "⚠️  Superadmin already has permission: {$permission['name']}\n";
            }
        }
    }
} else {
    echo "❌ Superadmin role not found\n";
}

echo "\n=== VERIFICATION ===\n";
$newPermissions = DB::table('permissions')->whereIn('name', [
    'inventaris.bahan.edit-stock',
    'inventaris.bahan.edit-price'
])->get();

foreach($newPermissions as $perm) {
    echo "✅ Permission exists: {$perm->name} (ID: {$perm->id})\n";
}

echo "\nPermissions created successfully!\n";
?>