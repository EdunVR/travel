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

echo "=== DEBUGGING PERMISSION SYSTEM ===\n\n";

// 1. Check all users and their roles
echo "1. All Users and Their Roles:\n";
$users = DB::table('users')
    ->join('roles', 'users.role_id', '=', 'roles.id')
    ->select('users.id', 'users.name', 'users.email', 'roles.name as role_name', 'roles.id as role_id')
    ->get();

foreach ($users as $user) {
    echo "   User: {$user->name} ({$user->email}) - Role: {$user->role_name} (ID: {$user->role_id})\n";
}

echo "\n";

// 2. Test hasPermission method for each user
echo "2. Testing hasPermission Method:\n";
foreach ($users as $userData) {
    $user = User::find($userData->id);
    echo "   User: {$user->name} (Role: {$user->role->name})\n";
    
    // Test specific service permissions
    $testPermissions = [
        'service.mesin.create',
        'service.ongkir.create', 
        'service.invoice.create'
    ];
    
    foreach ($testPermissions as $perm) {
        $hasPermission = $user->hasPermission($perm);
        $status = $hasPermission ? '✓' : '❌';
        echo "     {$status} {$perm}\n";
    }
    echo "\n";
}

// 3. Test Blade directive simulation
echo "3. Testing Blade Directive Logic:\n";
foreach ($users as $userData) {
    $user = User::find($userData->id);
    echo "   User: {$user->name}\n";
    
    // Simulate what @hasPermission does
    $testPermissions = [
        'service.mesin.create',
        'service.ongkir.create', 
        'service.invoice.create'
    ];
    
    foreach ($testPermissions as $perm) {
        // This is exactly what the Blade directive does
        $result = $user->hasPermission($perm);
        $status = $result ? '✓ SHOW BUTTON' : '❌ HIDE BUTTON';
        echo "     @hasPermission('{$perm}') -> {$status}\n";
    }
    echo "\n";
}

// 4. Check role permissions in detail
echo "4. Role Permissions Detail:\n";
$roles = Role::with('permissions')->get();
foreach ($roles as $role) {
    echo "   Role: {$role->name} (ID: {$role->id})\n";
    
    $servicePermissions = $role->permissions()
        ->where('name', 'like', 'service.%')
        ->pluck('name')
        ->toArray();
    
    if (count($servicePermissions) > 0) {
        echo "     Service permissions: " . count($servicePermissions) . "\n";
        foreach ($servicePermissions as $perm) {
            echo "       - {$perm}\n";
        }
    } else {
        echo "     ❌ No service permissions\n";
    }
    echo "\n";
}

// 5. Test specific user by ID (if you know superadmin user ID)
echo "5. Testing Specific Superadmin User:\n";
$superadminUsers = User::whereHas('role', function($q) {
    $q->where('name', 'super_admin')->orWhere('name', 'Super Admin');
})->get();

foreach ($superadminUsers as $user) {
    echo "   Superadmin: {$user->name} (ID: {$user->id}, Role: {$user->role->name})\n";
    
    // Test the exact permissions used in Blade templates
    $testPermissions = [
        'service.mesin.create',
        'service.ongkir.create', 
        'service.invoice.create'
    ];
    
    foreach ($testPermissions as $perm) {
        $hasPermission = $user->hasPermission($perm);
        $roleCheck = $user->role->name === 'super_admin';
        $directPermissionCheck = $user->role->hasPermission($perm);
        
        echo "     Permission: {$perm}\n";
        echo "       hasPermission(): " . ($hasPermission ? 'TRUE' : 'FALSE') . "\n";
        echo "       is super_admin: " . ($roleCheck ? 'TRUE' : 'FALSE') . "\n";
        echo "       role hasPermission: " . ($directPermissionCheck ? 'TRUE' : 'FALSE') . "\n";
        echo "       Final result: " . ($hasPermission ? '✓ SHOW BUTTON' : '❌ HIDE BUTTON') . "\n";
        echo "\n";
    }
}

echo "=== RECOMMENDATIONS ===\n";

// Check if there are any issues
$issues = [];

// Check if super_admin role exists
$superAdminRole = Role::where('name', 'super_admin')->first();
if (!$superAdminRole) {
    $issues[] = "Role 'super_admin' not found - check role names";
}

// Check if there are superadmin users
if ($superadminUsers->count() == 0) {
    $issues[] = "No users with superadmin role found";
}

if (count($issues) > 0) {
    echo "❌ Issues found:\n";
    foreach ($issues as $issue) {
        echo "   - {$issue}\n";
    }
} else {
    echo "✅ Permission system structure looks correct\n";
    echo "✅ If buttons still don't show, check:\n";
    echo "   1. Clear browser cache (Ctrl+F5)\n";
    echo "   2. Check Laravel cache (php artisan cache:clear)\n";
    echo "   3. Verify you're logged in as the correct user\n";
}

?>