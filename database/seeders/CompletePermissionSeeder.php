<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class CompletePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // Define all permissions by module
            $permissions = [
                // Finance Module
                'finance' => [
                    'biaya' => ['view', 'create', 'update', 'delete', 'export'],
                    'jurnal' => ['view', 'create', 'update', 'delete', 'export'],
                    'rab' => ['view', 'create', 'update', 'delete', 'approve', 'export'],
                    'realisasi' => ['view', 'create', 'update', 'delete'],
                    'rekonsiliasi' => ['view', 'create', 'update', 'delete', 'export'],
                    'buku' => ['view', 'create', 'update', 'delete'],
                    'aktiva' => ['view', 'create', 'update', 'delete'],
                    'ledger' => ['view', 'export'],
                    'neraca' => ['view', 'export'],
                    'laba-rugi' => ['view', 'export'],
                    'arus-kas' => ['view', 'export'],
                ],
                
                // CRM Module
                'crm' => [
                    'pelanggan' => ['view', 'create', 'update', 'delete', 'import', 'export'],
                    'tipe' => ['view', 'create', 'update', 'delete'],
                    'leads' => ['view', 'create', 'update', 'delete'],
                    'opportunities' => ['view', 'create', 'update', 'delete'],
                ],
                
                // Inventaris Module (Master/Inventaris)
                'inventaris' => [
                    'outlet' => ['view', 'create', 'update', 'delete', 'import', 'export'],
                    'kategori' => ['view', 'create', 'update', 'delete', 'import', 'export'],
                    'satuan' => ['view', 'create', 'update', 'delete', 'import', 'export'],
                    'produk' => ['view', 'create', 'update', 'delete', 'import', 'export'],
                    'bahan' => ['view', 'create', 'update', 'delete', 'import', 'export'],
                    'inventori' => ['view', 'create', 'update', 'delete', 'import', 'export'],
                    'transfer-gudang' => ['view', 'create', 'update', 'delete'],
                ],
                
                // Procurement Module
                'procurement' => [
                    'supplier' => ['view', 'create', 'update', 'delete'],
                    'purchase-order' => ['view', 'create', 'update', 'delete', 'approve'],
                    'purchase-request' => ['view', 'create', 'update', 'delete', 'approve'],
                    'receiving' => ['view', 'create', 'update'],
                ],
                
                // Sales Module
                'sales' => [
                    'quotation' => ['view', 'create', 'update', 'delete', 'approve'],
                    'sales-order' => ['view', 'create', 'update', 'delete', 'approve'],
                    'invoice' => ['view', 'create', 'update', 'delete', 'print'],
                    'delivery' => ['view', 'create', 'update'],
                ],
                
                // HRM Module
                'hrm' => [
                    'karyawan' => ['view', 'create', 'update', 'delete'],
                    'absensi' => ['view', 'create', 'update', 'export'],
                    'payroll' => ['view', 'create', 'approve', 'export'],
                    'cuti' => ['view', 'create', 'approve'],
                    'lembur' => ['view', 'create', 'approve'],
                ],
                
                // Production Module
                'production' => [
                    'work-order' => ['view', 'create', 'update', 'delete', 'approve'],
                    'bom' => ['view', 'create', 'update', 'delete'],
                    'production-plan' => ['view', 'create', 'update', 'delete'],
                    'quality-control' => ['view', 'create', 'approve'],
                ],
                
                // Project Management Module
                'project' => [
                    'projects' => ['view', 'create', 'update', 'delete'],
                    'tasks' => ['view', 'create', 'update', 'delete'],
                    'milestones' => ['view', 'create', 'update', 'delete'],
                    'timesheet' => ['view', 'create', 'update'],
                ],
                
                // POS Module
                'pos' => [
                    'kasir' => ['view', 'create'],
                    'shift' => ['view', 'create', 'close'],
                    'retur' => ['view', 'create', 'approve'],
                ],
                
                // System Module
                'sistem' => [
                    'users' => ['view', 'create', 'update', 'delete'],
                    'roles' => ['view', 'create', 'update', 'delete'],
                    'permissions' => ['view', 'update'],
                    'outlets' => ['view', 'create', 'update', 'delete'],
                    'settings' => ['view', 'update'],
                    'logs' => ['view', 'export'],
                ],
            ];

            // Create permissions
            foreach ($permissions as $module => $menus) {
                foreach ($menus as $menu => $actions) {
                    foreach ($actions as $action) {
                        $name = "{$module}.{$menu}.{$action}";
                        $displayName = ucfirst($action) . ' ' . ucwords(str_replace('-', ' ', $menu));
                        
                        Permission::updateOrCreate(
                            ['name' => $name],
                            [
                                'display_name' => $displayName,
                                'module' => $module,
                                'menu' => $menu,
                                'action' => $action
                            ]
                        );
                    }
                }
            }

            // Assign all permissions to Super Admin role
            $superAdminRole = Role::where('name', 'super_admin')
                ->orWhere('name', 'Super Admin')
                ->first();
            
            if ($superAdminRole) {
                $allPermissions = Permission::all()->pluck('id');
                $superAdminRole->permissions()->sync($allPermissions);
                $this->command->info('✅ All permissions assigned to Super Admin');
            }

            // Assign basic permissions to Admin role
            $adminRole = Role::where('name', 'admin')
                ->orWhere('name', 'Admin')
                ->first();
            
            if ($adminRole) {
                $adminPermissions = Permission::whereIn('action', ['view', 'create', 'update', 'export'])
                    ->whereNotIn('module', ['sistem']) // Admin tidak bisa manage sistem
                    ->pluck('id');
                $adminRole->permissions()->sync($adminPermissions);
                $this->command->info('✅ Basic permissions assigned to Admin');
            }

            // Assign view-only permissions to User role
            $userRole = Role::where('name', 'user')
                ->orWhere('name', 'User')
                ->first();
            
            if ($userRole) {
                $userPermissions = Permission::where('action', 'view')
                    ->whereNotIn('module', ['sistem'])
                    ->pluck('id');
                $userRole->permissions()->sync($userPermissions);
                $this->command->info('✅ View permissions assigned to User');
            }

            DB::commit();
            $this->command->info('✅ Complete permissions seeded successfully!');
            $this->command->info('Total permissions: ' . Permission::count());
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
        }
    }
}
