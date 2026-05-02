<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransportPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'travel.transport.view',
            'travel.transport.create',
            'travel.transport.update',
            'travel.transport.delete',
        ];

        $now = now();

        foreach ($permissions as $perm) {
            [$module, $menu, $action] = explode('.', $perm);
            DB::table('permissions')->updateOrInsert(
                ['name' => $perm],
                [
                    'name'         => $perm,
                    'display_name' => ucfirst($action) . ' Transportasi Saudi',
                    'module'       => 'travel',
                    'menu'         => 'transport',
                    'action'       => $action,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]
            );
        }

        // Assign all transport permissions to super_admin and admin roles
        $roles = DB::table('roles')
            ->whereIn('name', ['super_admin', 'admin', 'travel_admin', 'travel_manager'])
            ->pluck('id');

        foreach ($permissions as $perm) {
            $permId = DB::table('permissions')->where('name', $perm)->value('id');
            if (!$permId) continue;

            foreach ($roles as $roleId) {
                DB::table('role_permissions')->updateOrInsert(
                    ['permission_id' => $permId, 'role_id' => $roleId]
                );
            }
        }

        echo "Transport permissions seeded successfully.\n";
    }
}
