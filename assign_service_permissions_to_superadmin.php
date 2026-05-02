<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ASSIGNING SERVICE PERMISSIONS TO SUPERADMIN ===\n\n";

// 1. Find superadmin role
$superadminRole = DB::table('roles')->where('name', 'super_admin')->first();
if (!$superadminRole) {
    $superadminRole = DB::table('roles')->where('name', 'Super Admin')->first();
}

if (!$superadminRole) {
    echo "❌ Superadmin role not found!\n";
    exit(1);
}

echo "✓ Found superadmin role: {$superadminRole->name} (ID: {$superadminRole->id})\n\n";

// 2. Get all service permissions
$servicePermissions = DB::table('permissions')
    ->where('name', 'like', 'service.%')
    ->get();

echo "Found " . $servicePermissions->count() . " service permissions:\n";
foreach ($servicePermissions as $perm) {
    echo "   - {$perm->name}\n";
}

echo "\n";

// 3. Check which permissions superadmin already has
$existingPermissions = DB::table('role_permissions')
    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
    ->where('role_permissions.role_id', $superadminRole->id)
    ->where('permissions.name', 'like', 'service.%')
    ->pluck('permissions.name')
    ->toArray();

echo "Superadmin already has " . count($existingPermissions) . " service permissions:\n";
foreach ($existingPermissions as $perm) {
    echo "   ✓ {$perm}\n";
}

echo "\n";

// 4. Assign missing permissions
$assignedCount = 0;
foreach ($servicePermissions as $permission) {
    if (!in_array($permission->name, $existingPermissions)) {
        // Check if assignment already exists
        $exists = DB::table('role_permissions')
            ->where('role_id', $superadminRole->id)
            ->where('permission_id', $permission->id)
            ->exists();
        
        if (!$exists) {
            DB::table('role_permissions')->insert([
                'role_id' => $superadminRole->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✓ Assigned: {$permission->name}\n";
            $assignedCount++;
        }
    }
}

if ($assignedCount > 0) {
    echo "\n✅ Successfully assigned {$assignedCount} new service permissions to superadmin!\n";
} else {
    echo "\n✅ Superadmin already has all service permissions!\n";
}

// 5. Verify final state
echo "\nFinal verification:\n";
$finalPermissions = DB::table('role_permissions')
    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
    ->where('role_permissions.role_id', $superadminRole->id)
    ->where('permissions.name', 'like', 'service.%')
    ->pluck('permissions.name')
    ->toArray();

echo "Superadmin now has " . count($finalPermissions) . " service permissions:\n";
foreach ($finalPermissions as $perm) {
    echo "   ✓ {$perm}\n";
}

echo "\n=== COMPLETE ===\n";
echo "✅ @hasPermission directive added to AppServiceProvider\n";
echo "✅ Superadmin has all service permissions\n";
echo "✅ Create buttons should now appear for superadmin\n";

?>