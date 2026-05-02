<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CREATING SERVICE MANAGEMENT PERMISSIONS ===\n\n";

$permissions = [
    // Invoice Service
    ['name' => 'service.invoice.view', 'display_name' => 'View Invoice Service', 'module' => 'service', 'menu' => 'invoice', 'action' => 'view'],
    ['name' => 'service.invoice.create', 'display_name' => 'Create Invoice Service', 'module' => 'service', 'menu' => 'invoice', 'action' => 'create'],
    ['name' => 'service.invoice.edit', 'display_name' => 'Edit Invoice Service', 'module' => 'service', 'menu' => 'invoice', 'action' => 'edit'],
    ['name' => 'service.invoice.delete', 'display_name' => 'Delete Invoice Service', 'module' => 'service', 'menu' => 'invoice', 'action' => 'delete'],
    ['name' => 'service.invoice.export', 'display_name' => 'Export Invoice Service', 'module' => 'service', 'menu' => 'invoice', 'action' => 'export'],
    
    // History Service
    ['name' => 'service.history.view', 'display_name' => 'View History Service', 'module' => 'service', 'menu' => 'history', 'action' => 'view'],
    ['name' => 'service.history.create', 'display_name' => 'Create History Service', 'module' => 'service', 'menu' => 'history', 'action' => 'create'],
    ['name' => 'service.history.edit', 'display_name' => 'Edit History Service', 'module' => 'service', 'menu' => 'history', 'action' => 'edit'],
    ['name' => 'service.history.delete', 'display_name' => 'Delete History Service', 'module' => 'service', 'menu' => 'history', 'action' => 'delete'],
    ['name' => 'service.history.export', 'display_name' => 'Export History Service', 'module' => 'service', 'menu' => 'history', 'action' => 'export'],
    
    // Ongkir Service
    ['name' => 'service.ongkir.view', 'display_name' => 'View Ongkir Service', 'module' => 'service', 'menu' => 'ongkir', 'action' => 'view'],
    ['name' => 'service.ongkir.create', 'display_name' => 'Create Ongkir Service', 'module' => 'service', 'menu' => 'ongkir', 'action' => 'create'],
    ['name' => 'service.ongkir.edit', 'display_name' => 'Edit Ongkir Service', 'module' => 'service', 'menu' => 'ongkir', 'action' => 'edit'],
    ['name' => 'service.ongkir.delete', 'display_name' => 'Delete Ongkir Service', 'module' => 'service', 'menu' => 'ongkir', 'action' => 'delete'],
    ['name' => 'service.ongkir.export', 'display_name' => 'Export Ongkir Service', 'module' => 'service', 'menu' => 'ongkir', 'action' => 'export'],
    
    // Mesin Customer
    ['name' => 'service.mesin.view', 'display_name' => 'View Mesin Customer', 'module' => 'service', 'menu' => 'mesin', 'action' => 'view'],
    ['name' => 'service.mesin.create', 'display_name' => 'Create Mesin Customer', 'module' => 'service', 'menu' => 'mesin', 'action' => 'create'],
    ['name' => 'service.mesin.edit', 'display_name' => 'Edit Mesin Customer', 'module' => 'service', 'menu' => 'mesin', 'action' => 'edit'],
    ['name' => 'service.mesin.delete', 'display_name' => 'Delete Mesin Customer', 'module' => 'service', 'menu' => 'mesin', 'action' => 'delete'],
    ['name' => 'service.mesin.export', 'display_name' => 'Export Mesin Customer', 'module' => 'service', 'menu' => 'mesin', 'action' => 'export'],
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
