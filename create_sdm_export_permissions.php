<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CREATING SDM EXPORT PERMISSIONS ===\n\n";

$permissions = [
    // Kepegawaian - Export
    ['name' => 'hrm.karyawan.export', 'display_name' => 'Export Kepegawaian', 'module' => 'hrm', 'menu' => 'karyawan', 'action' => 'export'],
    
    // Absensi - Export
    ['name' => 'hrm.absensi.export', 'display_name' => 'Export Absensi', 'module' => 'hrm', 'menu' => 'absensi', 'action' => 'export'],
];

$created = 0;
$skipped = 0;

foreach ($permissions as $permission) {
    $exists = DB::table('permissions')->where('name', $permission['name'])->exists();
    
    if (!$exists) {
        DB::table('permissions')->insert([
            'name' => $permission['name'],
            'display_name' => $permission['display_name'],
            'module' => $permission['module'],
            'menu' => $permission['menu'],
            'action' => $permission['action'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ CREATED: {$permission['name']}\n";
        $created++;
    } else {
        echo "⊘ SKIPPED: {$permission['name']} (already exists)\n";
        $skipped++;
    }
}

echo "\n=== ASSIGNING TO SUPERADMIN ROLE ===\n";

$superadminRole = DB::table('roles')->where('name', 'superadmin')->first();

if ($superadminRole) {
    $allPermissions = DB::table('permissions')->pluck('id');
    
    foreach ($allPermissions as $permissionId) {
        $exists = DB::table('role_permissions')
            ->where('role_id', $superadminRole->id)
            ->where('permission_id', $permissionId)
            ->exists();
            
        if (!$exists) {
            DB::table('role_permissions')->insert([
                'role_id' => $superadminRole->id,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    echo "✓ All permissions assigned to superadmin role\n";
} else {
    echo "✗ Superadmin role not found\n";
}

echo "\n=== SUMMARY ===\n";
echo "Created: {$created}\n";
echo "Skipped: {$skipped}\n";
echo "Total: " . count($permissions) . "\n";

echo "\n=== DONE ===\n";
