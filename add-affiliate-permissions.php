<?php
/**
 * Script: Tambah permission affiliate ke database
 * Jalankan: php add-affiliate-permissions.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Affiliate Permission Seeder ===\n\n";

$permissions = [
    ['name' => 'travel.affiliate.view',     'display_name' => 'View Affiliate',     'module' => 'travel', 'menu' => 'affiliate', 'action' => 'view'],
    ['name' => 'travel.affiliate.create',   'display_name' => 'Create Affiliate',   'module' => 'travel', 'menu' => 'affiliate', 'action' => 'create'],
    ['name' => 'travel.affiliate.update',   'display_name' => 'Update Affiliate',   'module' => 'travel', 'menu' => 'affiliate', 'action' => 'update'],
    ['name' => 'travel.affiliate.delete',   'display_name' => 'Delete Affiliate',   'module' => 'travel', 'menu' => 'affiliate', 'action' => 'delete'],
    ['name' => 'travel.affiliate.approve',  'display_name' => 'Approve Affiliate',  'module' => 'travel', 'menu' => 'affiliate', 'action' => 'approve'],
    ['name' => 'travel.affiliate.payout',   'display_name' => 'Manage Payout',      'module' => 'travel', 'menu' => 'affiliate', 'action' => 'payout'],
    ['name' => 'travel.affiliate.settings', 'display_name' => 'Affiliate Settings', 'module' => 'travel', 'menu' => 'affiliate', 'action' => 'settings'],
];

// Insert permissions
foreach ($permissions as $perm) {
    $exists = DB::table('permissions')->where('name', $perm['name'])->first();
    if (!$exists) {
        DB::table('permissions')->insert(array_merge($perm, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
        echo "  [+] Created: {$perm['name']}\n";
    } else {
        echo "  [=] Exists:  {$perm['name']}\n";
    }
}

// Cek tabel role_permissions (bisa juga bernama permission_role)
$rpTable = 'role_permissions';
if (!DB::getSchemaBuilder()->hasTable($rpTable)) {
    $rpTable = 'permission_role';
}
echo "\nUsing pivot table: {$rpTable}\n";

// Assign semua ke super_admin
$superAdmin = DB::table('roles')->where('name', 'super_admin')->first();
if ($superAdmin) {
    $permIds = DB::table('permissions')->where('menu', 'affiliate')->pluck('id');
    foreach ($permIds as $permId) {
        DB::table($rpTable)->updateOrInsert(
            ['role_id' => $superAdmin->id, 'permission_id' => $permId],
            ['role_id' => $superAdmin->id, 'permission_id' => $permId]
        );
    }
    echo "  [+] Assigned all affiliate permissions to super_admin (id={$superAdmin->id})\n";
} else {
    echo "  [!] super_admin role not found\n";
}

// Assign view ke admin & manager
foreach (['admin', 'manager'] as $roleName) {
    $role = DB::table('roles')->where('name', $roleName)->first();
    if ($role) {
        $viewPerm = DB::table('permissions')->where('name', 'travel.affiliate.view')->first();
        if ($viewPerm) {
            DB::table($rpTable)->updateOrInsert(
                ['role_id' => $role->id, 'permission_id' => $viewPerm->id],
                ['role_id' => $role->id, 'permission_id' => $viewPerm->id]
            );
            echo "  [+] Assigned view to {$roleName} (id={$role->id})\n";
        }
    } else {
        echo "  [!] Role '{$roleName}' not found\n";
    }
}

// Juga assign ke user yang punya role super_admin langsung (user_permissions jika ada)
$userPermTable = 'user_permissions';
if (DB::getSchemaBuilder()->hasTable($userPermTable)) {
    // Cari semua user dengan role super_admin
    $superAdminUsers = DB::table('user_roles')
        ->where('role_id', $superAdmin->id ?? 0)
        ->pluck('user_id');
    
    if ($superAdminUsers->count() > 0) {
        $permIds = DB::table('permissions')->where('menu', 'affiliate')->pluck('id');
        foreach ($superAdminUsers as $userId) {
            foreach ($permIds as $permId) {
                DB::table($userPermTable)->updateOrInsert(
                    ['user_id' => $userId, 'permission_id' => $permId],
                    ['user_id' => $userId, 'permission_id' => $permId]
                );
            }
        }
        echo "  [+] Assigned to {$superAdminUsers->count()} super_admin user(s)\n";
    }
}

echo "\n=== Done! ===\n";
echo "Jalankan: php artisan config:clear && php artisan view:clear\n";
