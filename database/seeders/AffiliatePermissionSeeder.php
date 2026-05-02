<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AffiliatePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Cek apakah tabel permissions ada kolom yang dibutuhkan
            $permissions = [
                ['name' => 'travel.affiliate.view',     'display_name' => 'View Affiliate',     'module' => 'travel', 'menu' => 'affiliate', 'action' => 'view'],
                ['name' => 'travel.affiliate.create',   'display_name' => 'Create Affiliate',   'module' => 'travel', 'menu' => 'affiliate', 'action' => 'create'],
                ['name' => 'travel.affiliate.update',   'display_name' => 'Update Affiliate',   'module' => 'travel', 'menu' => 'affiliate', 'action' => 'update'],
                ['name' => 'travel.affiliate.delete',   'display_name' => 'Delete Affiliate',   'module' => 'travel', 'menu' => 'affiliate', 'action' => 'delete'],
                ['name' => 'travel.affiliate.approve',  'display_name' => 'Approve Affiliate',  'module' => 'travel', 'menu' => 'affiliate', 'action' => 'approve'],
                ['name' => 'travel.affiliate.payout',   'display_name' => 'Manage Payout',      'module' => 'travel', 'menu' => 'affiliate', 'action' => 'payout'],
                ['name' => 'travel.affiliate.settings', 'display_name' => 'Affiliate Settings', 'module' => 'travel', 'menu' => 'affiliate', 'action' => 'settings'],
            ];

            foreach ($permissions as $perm) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $perm['name']],
                    array_merge($perm, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }

            // Assign ke super_admin
            $superAdmin = DB::table('roles')->where('name', 'super_admin')->first();
            if ($superAdmin) {
                $permIds = DB::table('permissions')
                    ->where('module', 'travel')
                    ->where('menu', 'affiliate')
                    ->pluck('id');

                foreach ($permIds as $permId) {
                    DB::table('role_permissions')->updateOrInsert(
                        ['role_id' => $superAdmin->id, 'permission_id' => $permId],
                        ['role_id' => $superAdmin->id, 'permission_id' => $permId]
                    );
                }
            }

            // Assign view ke admin & manager
            foreach (['admin', 'manager'] as $roleName) {
                $role = DB::table('roles')->where('name', $roleName)->first();
                if ($role) {
                    $viewPerm = DB::table('permissions')
                        ->where('name', 'travel.affiliate.view')
                        ->first();
                    if ($viewPerm) {
                        DB::table('role_permissions')->updateOrInsert(
                            ['role_id' => $role->id, 'permission_id' => $viewPerm->id],
                            ['role_id' => $role->id, 'permission_id' => $viewPerm->id]
                        );
                    }
                }
            }

            DB::commit();
            $this->command->info('✅ Affiliate permissions seeded & assigned to roles.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
