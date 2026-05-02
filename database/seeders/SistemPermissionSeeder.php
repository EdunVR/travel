<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sistem permissions
        $permissions = [
            [
                'name' => 'sistem.view',
                'display_name' => 'Lihat halaman sistem',
                'module' => 'sistem',
                'menu' => 'sistem',
                'action' => 'view'
            ],
            [
                'name' => 'sistem.backup',
                'display_name' => 'Kelola backup database',
                'module' => 'sistem',
                'menu' => 'sistem',
                'action' => 'backup'
            ],
            [
                'name' => 'sistem.maintenance',
                'display_name' => 'Maintenance sistem',
                'module' => 'sistem',
                'menu' => 'sistem',
                'action' => 'maintenance'
            ],
            [
                'name' => 'sistem.settings.view',
                'display_name' => 'Lihat pengaturan perusahaan',
                'module' => 'sistem',
                'menu' => 'pengaturan',
                'action' => 'view'
            ],
            [
                'name' => 'sistem.settings.create',
                'display_name' => 'Buat pengaturan perusahaan',
                'module' => 'sistem',
                'menu' => 'pengaturan',
                'action' => 'create'
            ],
            [
                'name' => 'sistem.settings.edit',
                'display_name' => 'Edit pengaturan perusahaan',
                'module' => 'sistem',
                'menu' => 'pengaturan',
                'action' => 'edit'
            ],
            [
                'name' => 'sistem.settings.delete',
                'display_name' => 'Hapus pengaturan perusahaan',
                'module' => 'sistem',
                'menu' => 'pengaturan',
                'action' => 'delete'
            ]
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $exists = DB::table('permissions')->where('name', $permission['name'])->exists();
            if (!$exists) {
                try {
                    DB::table('permissions')->insert([
                        'name' => $permission['name'],
                        'display_name' => $permission['display_name'],
                        'module' => $permission['module'],
                        'menu' => $permission['menu'],
                        'action' => $permission['action'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $this->command->info("Created permission: {$permission['name']}");
                } catch (\Exception $e) {
                    $this->command->error("Failed to create permission {$permission['name']}: " . $e->getMessage());
                }
            } else {
                $this->command->info("Permission already exists: {$permission['name']}");
            }
        }

        // Try to assign permissions to superadmin role if it exists
        try {
            $superadminRole = DB::table('roles')->where('name', 'superadmin')->first();
            if ($superadminRole) {
                foreach ($permissions as $permission) {
                    $permissionRecord = DB::table('permissions')->where('name', $permission['name'])->first();
                    if ($permissionRecord) {
                        $exists = DB::table('role_has_permissions')
                            ->where('role_id', $superadminRole->id)
                            ->where('permission_id', $permissionRecord->id)
                            ->exists();
                        
                        if (!$exists) {
                            DB::table('role_has_permissions')->insert([
                                'role_id' => $superadminRole->id,
                                'permission_id' => $permissionRecord->id
                            ]);
                        }
                    }
                }
                $this->command->info("Assigned permissions to superadmin role");
            }
        } catch (\Exception $e) {
            $this->command->warn("Could not assign permissions to roles: " . $e->getMessage());
        }

        $this->command->info('Sistem permissions created successfully!');
    }
}