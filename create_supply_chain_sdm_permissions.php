<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CREATING SUPPLY CHAIN & SDM PERMISSIONS ===\n\n";

$permissions = [
    // Supply Chain - Transfer Gudang
    ['name' => 'inventaris.transfer-gudang.edit', 'display_name' => 'Edit Transfer Gudang', 'module' => 'inventaris', 'menu' => 'transfer-gudang', 'action' => 'edit'],
    ['name' => 'inventaris.transfer-gudang.approve', 'display_name' => 'Approve Transfer Gudang', 'module' => 'inventaris', 'menu' => 'transfer-gudang', 'action' => 'approve'],
    
    // SDM - Kepegawaian & Rekrutmen
    ['name' => 'hrm.karyawan.edit', 'display_name' => 'Edit Kepegawaian', 'module' => 'hrm', 'menu' => 'karyawan', 'action' => 'edit'],
    
    // SDM - Payroll
    ['name' => 'hrm.payroll.edit', 'display_name' => 'Edit Payroll', 'module' => 'hrm', 'menu' => 'payroll', 'action' => 'edit'],
    ['name' => 'hrm.payroll.delete', 'display_name' => 'Delete Payroll', 'module' => 'hrm', 'menu' => 'payroll', 'action' => 'delete'],
    
    // SDM - Absensi
    ['name' => 'hrm.absensi.edit', 'display_name' => 'Edit Absensi', 'module' => 'hrm', 'menu' => 'absensi', 'action' => 'edit'],
    ['name' => 'hrm.absensi.delete', 'display_name' => 'Delete Absensi', 'module' => 'hrm', 'menu' => 'absensi', 'action' => 'delete'],
    ['name' => 'hrm.absensi.approve', 'display_name' => 'Approve Absensi', 'module' => 'hrm', 'menu' => 'absensi', 'action' => 'approve'],
    
    // SDM - Kinerja
    ['name' => 'hrm.kinerja.view', 'display_name' => 'View Kinerja', 'module' => 'hrm', 'menu' => 'kinerja', 'action' => 'view'],
    ['name' => 'hrm.kinerja.create', 'display_name' => 'Create Kinerja', 'module' => 'hrm', 'menu' => 'kinerja', 'action' => 'create'],
    ['name' => 'hrm.kinerja.edit', 'display_name' => 'Edit Kinerja', 'module' => 'hrm', 'menu' => 'kinerja', 'action' => 'edit'],
    ['name' => 'hrm.kinerja.delete', 'display_name' => 'Delete Kinerja', 'module' => 'hrm', 'menu' => 'kinerja', 'action' => 'delete'],
    
    // SDM - Kontrak & Dokumen
    ['name' => 'hrm.kontrak.view', 'display_name' => 'View Kontrak & Dokumen', 'module' => 'hrm', 'menu' => 'kontrak', 'action' => 'view'],
    ['name' => 'hrm.kontrak.create', 'display_name' => 'Create Kontrak & Dokumen', 'module' => 'hrm', 'menu' => 'kontrak', 'action' => 'create'],
    ['name' => 'hrm.kontrak.edit', 'display_name' => 'Edit Kontrak & Dokumen', 'module' => 'hrm', 'menu' => 'kontrak', 'action' => 'edit'],
    ['name' => 'hrm.kontrak.delete', 'display_name' => 'Delete Kontrak & Dokumen', 'module' => 'hrm', 'menu' => 'kontrak', 'action' => 'delete'],
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
