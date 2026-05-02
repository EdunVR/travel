<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING AVAILABLE ROLES ===\n";
$roles = DB::table('roles')->get();
foreach($roles as $role) {
    echo "- {$role->name} (ID: {$role->id})\n";
}

echo "\n=== ASSIGNING PERMISSIONS TO ADMIN ROLES ===\n";

$permissions = [
    'inventaris.bahan.edit-stock',
    'inventaris.bahan.edit-price'
];

// Try to find admin-like roles
$adminRoles = DB::table('roles')->whereIn('name', ['admin', 'super_admin', 'Super Admin'])->get();

if ($adminRoles->isEmpty()) {
    // If no admin roles found, get the first role (usually admin)
    $adminRoles = DB::table('roles')->limit(1)->get();
}

foreach($adminRoles as $role) {
    echo "\nAssigning permissions to role: {$role->name}\n";
    
    foreach($permissions as $permissionName) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        
        if ($permission) {
            // Check if role already has permission
            $hasPermission = DB::table('role_permissions')
                ->where('role_id', $role->id)
                ->where('permission_id', $permission->id)
                ->exists();
            
            if (!$hasPermission) {
                DB::table('role_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "✅ Assigned {$permissionName} to {$role->name}\n";
            } else {
                echo "⚠️  {$role->name} already has permission: {$permissionName}\n";
            }
        }
    }
}

echo "\n=== VERIFICATION ===\n";
foreach($adminRoles as $role) {
    $rolePermissions = DB::table('role_permissions')
        ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
        ->where('role_permissions.role_id', $role->id)
        ->whereIn('permissions.name', $permissions)
        ->pluck('permissions.name');
    
    echo "Role {$role->name} has permissions:\n";
    foreach($rolePermissions as $perm) {
        echo "  - {$perm}\n";
    }
}

echo "\nPermissions assigned successfully!\n";
?>