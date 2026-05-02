<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermintaanBarangPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for Permintaan Barang
        $permissions = [
            [
                'name' => 'supply-chain.permintaan-barang.view',
                'display_name' => 'Lihat Permintaan Barang',
                'module' => 'supply-chain',
                'menu' => 'permintaan-barang',
                'action' => 'view'
            ],
            [
                'name' => 'supply-chain.permintaan-barang.create',
                'display_name' => 'Buat Permintaan Barang',
                'module' => 'supply-chain',
                'menu' => 'permintaan-barang',
                'action' => 'create'
            ],
            [
                'name' => 'supply-chain.permintaan-barang.update',
                'display_name' => 'Edit Permintaan Barang',
                'module' => 'supply-chain',
                'menu' => 'permintaan-barang',
                'action' => 'update'
            ],
            [
                'name' => 'supply-chain.permintaan-barang.delete',
                'display_name' => 'Hapus Permintaan Barang',
                'module' => 'supply-chain',
                'menu' => 'permintaan-barang',
                'action' => 'delete'
            ],
            [
                'name' => 'supply-chain.permintaan-barang.approve',
                'display_name' => 'Setujui/Tolak Permintaan Barang',
                'module' => 'supply-chain',
                'menu' => 'permintaan-barang',
                'action' => 'approve'
            ],
            [
                'name' => 'supply-chain.permintaan-barang.export',
                'display_name' => 'Export Permintaan Barang',
                'module' => 'supply-chain',
                'menu' => 'permintaan-barang',
                'action' => 'export'
            ]
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        // Assign permissions to super_admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $permissionNames = collect($permissions)->pluck('name')->toArray();
            $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id')->toArray();
            
            foreach ($permissionIds as $permissionId) {
                $superAdminRole->permissions()->syncWithoutDetaching([$permissionId]);
            }
        }

        $this->command->info('Permintaan Barang permissions created and assigned to super_admin role.');
    }
}
