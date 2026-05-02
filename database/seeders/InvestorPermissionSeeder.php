<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class InvestorPermissionSeeder extends Seeder
{
    public function run()
    {
        // Investor permissions
        $permissions = [
            // Profil Investor
            [
                'name' => 'investor.profil.view',
                'display_name' => 'Lihat Profil Investor',
                'module' => 'investor',
                'menu' => 'profil',
                'action' => 'view'
            ],
            [
                'name' => 'investor.profil.create',
                'display_name' => 'Tambah Profil Investor',
                'module' => 'investor',
                'menu' => 'profil',
                'action' => 'create'
            ],
            [
                'name' => 'investor.profil.edit',
                'display_name' => 'Edit Profil Investor',
                'module' => 'investor',
                'menu' => 'profil',
                'action' => 'edit'
            ],
            [
                'name' => 'investor.profil.delete',
                'display_name' => 'Hapus Profil Investor',
                'module' => 'investor',
                'menu' => 'profil',
                'action' => 'delete'
            ],
            [
                'name' => 'investor.profil.export',
                'display_name' => 'Export Profil Investor',
                'module' => 'investor',
                'menu' => 'profil',
                'action' => 'export'
            ],
            
            // Bagi Hasil
            [
                'name' => 'investor.bagi-hasil.view',
                'display_name' => 'Lihat Bagi Hasil',
                'module' => 'investor',
                'menu' => 'bagi-hasil',
                'action' => 'view'
            ],
            [
                'name' => 'investor.bagi-hasil.create',
                'display_name' => 'Tambah Bagi Hasil',
                'module' => 'investor',
                'menu' => 'bagi-hasil',
                'action' => 'create'
            ],
            [
                'name' => 'investor.bagi-hasil.edit',
                'display_name' => 'Edit Bagi Hasil',
                'module' => 'investor',
                'menu' => 'bagi-hasil',
                'action' => 'edit'
            ],
            [
                'name' => 'investor.bagi-hasil.delete',
                'display_name' => 'Hapus Bagi Hasil',
                'module' => 'investor',
                'menu' => 'bagi-hasil',
                'action' => 'delete'
            ],
            [
                'name' => 'investor.bagi-hasil.approve',
                'display_name' => 'Setujui Bagi Hasil',
                'module' => 'investor',
                'menu' => 'bagi-hasil',
                'action' => 'approve'
            ],
            [
                'name' => 'investor.bagi-hasil.export',
                'display_name' => 'Export Bagi Hasil',
                'module' => 'investor',
                'menu' => 'bagi-hasil',
                'action' => 'export'
            ],
            
            // Pencairan
            [
                'name' => 'investor.pencairan.view',
                'display_name' => 'Lihat Pencairan',
                'module' => 'investor',
                'menu' => 'pencairan',
                'action' => 'view'
            ],
            [
                'name' => 'investor.pencairan.create',
                'display_name' => 'Tambah Pencairan',
                'module' => 'investor',
                'menu' => 'pencairan',
                'action' => 'create'
            ],
            [
                'name' => 'investor.pencairan.edit',
                'display_name' => 'Edit Pencairan',
                'module' => 'investor',
                'menu' => 'pencairan',
                'action' => 'edit'
            ],
            [
                'name' => 'investor.pencairan.delete',
                'display_name' => 'Hapus Pencairan',
                'module' => 'investor',
                'menu' => 'pencairan',
                'action' => 'delete'
            ],
            [
                'name' => 'investor.pencairan.approve',
                'display_name' => 'Setujui Pencairan',
                'module' => 'investor',
                'menu' => 'pencairan',
                'action' => 'approve'
            ],
            [
                'name' => 'investor.pencairan.export',
                'display_name' => 'Export Pencairan',
                'module' => 'investor',
                'menu' => 'pencairan',
                'action' => 'export'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Assign permissions to superadmin role
        $superadminRole = Role::where('name', 'superadmin')->first();
        if ($superadminRole) {
            foreach ($permissions as $permission) {
                $permissionModel = Permission::where('name', $permission['name'])->first();
                if ($permissionModel && !$superadminRole->permissions->contains($permissionModel)) {
                    $superadminRole->permissions()->attach($permissionModel);
                }
            }
        }

        $this->command->info('Investor permissions created successfully!');
    }
}