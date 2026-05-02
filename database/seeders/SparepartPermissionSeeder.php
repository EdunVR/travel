<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class SparepartPermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            [
                'name' => 'inventaris.sparepart.adjust-stock',
                'display_name' => 'Adjust Stock Sparepart',
                'module' => 'inventaris',
                'menu' => 'sparepart',
                'action' => 'adjust-stock'
            ],
            [
                'name' => 'inventaris.sparepart.adjust-price',
                'display_name' => 'Adjust Price Sparepart',
                'module' => 'inventaris',
                'menu' => 'sparepart',
                'action' => 'adjust-price'
            ],
            [
                'name' => 'inventaris.sparepart.export',
                'display_name' => 'Export Sparepart',
                'module' => 'inventaris',
                'menu' => 'sparepart',
                'action' => 'export'
            ],
            [
                'name' => 'inventaris.sparepart.bulk-delete',
                'display_name' => 'Bulk Delete Sparepart',
                'module' => 'inventaris',
                'menu' => 'sparepart',
                'action' => 'bulk-delete'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Get permission names
        $permissionNames = array_column($permissions, 'name');

        // Assign permissions to superadmin role
        $superadminRole = Role::where('name', 'superadmin')->first();
        if ($superadminRole) {
            $superadminRole->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', $permissionNames)->pluck('id')
            );
        }

        // Assign basic permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', [
                    'inventaris.sparepart.adjust-stock',
                    'inventaris.sparepart.export',
                ])->pluck('id')
            );
        }

        $this->command->info('Sparepart permissions created successfully!');
    }
}