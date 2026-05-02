<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

echo "=== COMPREHENSIVE PERMISSION AUDIT ===\n\n";

// 1. Check Role Management System
echo "1. ROLE MANAGEMENT AUDIT:\n";
echo "   Checking all roles and their permissions...\n\n";

$roles = Role::with('permissions')->get();
foreach ($roles as $role) {
    echo "   ROLE: {$role->name} (ID: {$role->id})\n";
    echo "   Display Name: {$role->display_name}\n";
    echo "   Active: " . ($role->is_active ? 'YES' : 'NO') . "\n";
    
    // Get service permissions for this role
    $servicePermissions = $role->permissions()
        ->where('name', 'like', 'service.%')
        ->orderBy('name')
        ->get();
    
    if ($servicePermissions->count() > 0) {
        echo "   Service Permissions ({$servicePermissions->count()}):\n";
        foreach ($servicePermissions as $perm) {
            $isCreate = strpos($perm->name, '.create') !== false;
            $marker = $isCreate ? '🔥 CREATE' : '   ';
            echo "     {$marker} {$perm->name} - {$perm->display_name}\n";
        }
    } else {
        echo "   ❌ NO SERVICE PERMISSIONS\n";
    }
    echo "\n";
}

// 2. Check specific CREATE permissions
echo "2. SERVICE CREATE PERMISSIONS AUDIT:\n";
$createPermissions = [
    'service.mesin.create' => 'Create Mesin Customer',
    'service.ongkir.create' => 'Create Ongkir Service', 
    'service.invoice.create' => 'Create Invoice Service'
];

foreach ($createPermissions as $permName => $permDesc) {
    echo "   Permission: {$permName}\n";
    
    // Check if permission exists
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        echo "     ✓ EXISTS in database (ID: {$permission->id})\n";
        echo "     Display Name: {$permission->display_name}\n";
        
        // Check which roles have this permission
        $rolesWithPerm = Role::whereHas('permissions', function($q) use ($permName) {
            $q->where('name', $permName);
        })->get();
        
        if ($rolesWithPerm->count() > 0) {
            echo "     Assigned to roles:\n";
            foreach ($rolesWithPerm as $role) {
                echo "       - {$role->name} (ID: {$role->id})\n";
            }
        } else {
            echo "     ❌ NOT ASSIGNED to any role\n";
        }
    } else {
        echo "     ❌ DOES NOT EXIST in database\n";
    }
    echo "\n";
}

// 3. Check current user and their permissions
echo "3. CURRENT USER AUDIT:\n";
$superadminUser = User::where('email', 'superadmin@morra.com')->first();
if ($superadminUser) {
    echo "   User: {$superadminUser->name} (ID: {$superadminUser->id})\n";
    echo "   Email: {$superadminUser->email}\n";
    echo "   Role: {$superadminUser->role->name} (ID: {$superadminUser->role_id})\n";
    echo "   Active: " . ($superadminUser->is_active ? 'YES' : 'NO') . "\n";
    
    echo "   Testing CREATE permissions:\n";
    foreach ($createPermissions as $permName => $permDesc) {
        $hasPermission = $superadminUser->hasPermission($permName);
        $status = $hasPermission ? '✅ HAS PERMISSION' : '❌ NO PERMISSION';
        echo "     {$permName}: {$status}\n";
    }
    
    // Test role check
    $isSuperAdmin = $superadminUser->hasRole('super_admin');
    echo "   Is Super Admin: " . ($isSuperAdmin ? 'YES' : 'NO') . "\n";
    
} else {
    echo "   ❌ Superadmin user not found!\n";
}

echo "\n";

// 4. Check the actual view files for permission directives
echo "4. VIEW FILES AUDIT:\n";
$serviceViews = [
    'resources/views/admin/service/mesin/index.blade.php' => 'service.mesin.create',
    'resources/views/admin/service/ongkir/index.blade.php' => 'service.ongkir.create',
    'resources/views/admin/service/history/index.blade.php' => 'service.invoice.create'
];

foreach ($serviceViews as $viewFile => $expectedPerm) {
    echo "   Checking: {$viewFile}\n";
    
    if (file_exists($viewFile)) {
        $content = file_get_contents($viewFile);
        
        // Check for permission directives
        if (strpos($content, '@hasPermission') !== false) {
            echo "     ✓ Contains @hasPermission directive\n";
            
            // Extract the permission being checked
            preg_match('/@hasPermission\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);
            if (isset($matches[1])) {
                $foundPerm = $matches[1];
                echo "     Permission checked: {$foundPerm}\n";
                
                if ($foundPerm === $expectedPerm) {
                    echo "     ✅ CORRECT permission\n";
                } else {
                    echo "     ⚠️  DIFFERENT permission (expected: {$expectedPerm})\n";
                }
            }
        } elseif (strpos($content, '@if(auth()') !== false) {
            echo "     ✓ Contains @if(auth()) directive (new format)\n";
        } else {
            echo "     ❌ NO permission directive found\n";
        }
        
        // Check for create button
        if (strpos($content, 'Tambah') !== false || strpos($content, 'Buat Invoice') !== false) {
            echo "     ✓ Contains create button\n";
        } else {
            echo "     ❌ NO create button found\n";
        }
        
    } else {
        echo "     ❌ FILE NOT FOUND\n";
    }
    echo "\n";
}

// 5. Test the permission system with actual conditions
echo "5. PERMISSION SYSTEM TEST:\n";
if ($superadminUser) {
    echo "   Testing conditions that would show/hide buttons:\n";
    
    foreach ($createPermissions as $permName => $permDesc) {
        echo "   \n   Testing: {$permName}\n";
        
        // Test condition 1: hasRole('super_admin')
        $condition1 = $superadminUser->hasRole('super_admin');
        echo "     hasRole('super_admin'): " . ($condition1 ? 'TRUE' : 'FALSE') . "\n";
        
        // Test condition 2: hasPermission(specific)
        $condition2 = $superadminUser->hasPermission($permName);
        echo "     hasPermission('{$permName}'): " . ($condition2 ? 'TRUE' : 'FALSE') . "\n";
        
        // Test combined condition (OR)
        $finalResult = $condition1 || $condition2;
        echo "     FINAL RESULT (show button): " . ($finalResult ? '✅ YES' : '❌ NO') . "\n";
    }
}

echo "\n";

// 6. Database integrity check
echo "6. DATABASE INTEGRITY CHECK:\n";

// Check role_permissions table
$rolePermissionCount = DB::table('role_permissions')->count();
echo "   Total role-permission assignments: {$rolePermissionCount}\n";

// Check super_admin role permissions
$superAdminRole = Role::where('name', 'super_admin')->first();
if ($superAdminRole) {
    $superAdminPermCount = DB::table('role_permissions')
        ->where('role_id', $superAdminRole->id)
        ->count();
    echo "   Super admin total permissions: {$superAdminPermCount}\n";
    
    $superAdminServicePermCount = DB::table('role_permissions')
        ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
        ->where('role_permissions.role_id', $superAdminRole->id)
        ->where('permissions.name', 'like', 'service.%')
        ->count();
    echo "   Super admin service permissions: {$superAdminServicePermCount}\n";
}

// 7. Generate fix recommendations
echo "\n=== RECOMMENDATIONS ===\n";

$issues = [];
$fixes = [];

// Check if permissions exist
foreach ($createPermissions as $permName => $permDesc) {
    $exists = Permission::where('name', $permName)->exists();
    if (!$exists) {
        $issues[] = "Permission '{$permName}' does not exist";
        $fixes[] = "Create permission: {$permName}";
    }
}

// Check if super_admin has permissions
if ($superadminUser && $superAdminRole) {
    foreach ($createPermissions as $permName => $permDesc) {
        $hasPermission = $superadminUser->hasPermission($permName);
        if (!$hasPermission) {
            $issues[] = "Super admin missing permission: {$permName}";
            $fixes[] = "Assign {$permName} to super_admin role";
        }
    }
}

if (count($issues) > 0) {
    echo "❌ ISSUES FOUND:\n";
    foreach ($issues as $i => $issue) {
        echo "   " . ($i + 1) . ". {$issue}\n";
    }
    echo "\n";
    
    echo "🔧 FIXES NEEDED:\n";
    foreach ($fixes as $i => $fix) {
        echo "   " . ($i + 1) . ". {$fix}\n";
    }
} else {
    echo "✅ ALL PERMISSIONS ARE CORRECTLY CONFIGURED\n";
    echo "\nIf buttons still don't show, the issue is likely:\n";
    echo "1. Browser cache - Clear with Ctrl+F5\n";
    echo "2. User not logged in as superadmin\n";
    echo "3. JavaScript errors - Check browser console\n";
    echo "4. View cache - Already cleared by previous scripts\n";
}

echo "\n=== AUDIT COMPLETE ===\n";

?>