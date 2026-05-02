<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Create Roles
            $superAdmin = Role::create([
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Has full access to all features',
                'is_active' => true
            ]);

            $admin = Role::create([
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Has access to most features',
                'is_active' => true
            ]);

            $manager = Role::create([
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Can view and approve',
                'is_active' => true
            ]);

            $staff = Role::create([
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Basic access',
                'is_active' => true
            ]);

            // Create Permissions
            $modules = [
                'finance' => ['jurnal', 'buku-besar', 'neraca', 'laba-rugi', 'arus-kas', 'aktiva', 'biaya', 'rab', 'hutang', 'piutang', 'rekonsiliasi'],
                'inventaris' => ['outlet', 'kategori', 'satuan', 'produk', 'bahan', 'inventori', 'transfer-gudang'],
                'crm' => ['tipe', 'pelanggan'],
                'penjualan' => ['invoice', 'pos', 'laporan'],
                'pembelian' => ['purchase-order', 'supplier'],
                'sistem' => ['users', 'roles', 'settings']
            ];

            $actions = ['view', 'create', 'update', 'delete'];
            $permissionCount = 0;

            foreach ($modules as $module => $menus) {
                foreach ($menus as $menu) {
                    foreach ($actions as $action) {
                        Permission::create([
                            'name' => "{$module}.{$menu}.{$action}",
                            'display_name' => ucfirst($action) . ' ' . ucfirst(str_replace('-', ' ', $menu)),
                            'module' => $module,
                            'menu' => $menu,
                            'action' => $action
                        ]);
                        $permissionCount++;
                    }
                }
            }

            // Assign all permissions to super admin
            $superAdmin->permissions()->attach(Permission::all()->pluck('id'));

            // Assign most permissions to admin (except user management)
            $adminPermissions = Permission::where('module', '!=', 'sistem')->pluck('id');
            $admin->permissions()->attach($adminPermissions);

            // Assign view permissions to manager
            $managerPermissions = Permission::where('action', 'view')->pluck('id');
            $manager->permissions()->attach($managerPermissions);

            // Assign basic permissions to staff
            $staffPermissions = Permission::whereIn('module', ['inventaris', 'penjualan'])
                ->whereIn('action', ['view', 'create'])
                ->pluck('id');
            $staff->permissions()->attach($staffPermissions);

            DB::commit();
            
            $this->command->info('✅ Roles and Permissions created successfully!');
            $this->command->info("   - {$permissionCount} permissions created");
            $this->command->info("   - 4 roles created");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
        }
    }
}
