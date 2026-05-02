<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class InterOutletSalePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create permissions for Inter Outlet Sale
        $permissions = [
            [
                'name' => 'sales.inter-outlet.view',
                'display_name' => 'View Inter Outlet Sale',
                'module' => 'sales',
                'menu' => 'inter-outlet',
                'action' => 'view'
            ],
            [
                'name' => 'sales.inter-outlet.create',
                'display_name' => 'Create Inter Outlet Sale',
                'module' => 'sales',
                'menu' => 'inter-outlet',
                'action' => 'create'
            ],
            [
                'name' => 'sales.inter-outlet.edit',
                'display_name' => 'Edit Inter Outlet Sale',
                'module' => 'sales',
                'menu' => 'inter-outlet',
                'action' => 'edit'
            ],
            [
                'name' => 'sales.inter-outlet.delete',
                'display_name' => 'Delete Inter Outlet Sale',
                'module' => 'sales',
                'menu' => 'inter-outlet',
                'action' => 'delete'
            ],
            [
                'name' => 'sales.inter-outlet.approve',
                'display_name' => 'Approve Inter Outlet Sale',
                'module' => 'sales',
                'menu' => 'inter-outlet',
                'action' => 'approve'
            ],
            [
                'name' => 'sales.inter-outlet.print',
                'display_name' => 'Print Inter Outlet Sale',
                'module' => 'sales',
                'menu' => 'inter-outlet',
                'action' => 'print'
            ],
            [
                'name' => 'sales.inter-outlet.export',
                'display_name' => 'Export Inter Outlet Sale',
                'module' => 'sales',
                'menu' => 'inter-outlet',
                'action' => 'export'
            ],
            [
                'name' => 'sales.inter-outlet.coa-settings',
                'display_name' => 'COA Settings Inter Outlet Sale',
                'module' => 'sales',
                'menu' => 'inter-outlet',
                'action' => 'coa-settings'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Assign permissions to super_admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            foreach ($permissions as $permission) {
                $permissionModel = Permission::where('name', $permission['name'])->first();
                if ($permissionModel && !$superAdminRole->permissions()->where('permission_id', $permissionModel->id)->exists()) {
                    $superAdminRole->permissions()->attach($permissionModel->id);
                }
            }
        }

        // You can also assign to other roles as needed
        // Example: assign basic permissions to sales role
        $salesRole = Role::where('name', 'sales')->first();
        if ($salesRole) {
            $basicPermissions = [
                'sales.inter-outlet.view',
                'sales.inter-outlet.create',
                'sales.inter-outlet.print',
            ];
            
            foreach ($basicPermissions as $permissionName) {
                $permissionModel = Permission::where('name', $permissionName)->first();
                if ($permissionModel && !$salesRole->permissions()->where('permission_id', $permissionModel->id)->exists()) {
                    $salesRole->permissions()->attach($permissionModel->id);
                }
            }
        }

        $this->command->info('Inter Outlet Sale permissions created and assigned successfully!');
    }
}