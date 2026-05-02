<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PosPermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $permissions = [
            // POS Permissions
            [
                'name' => 'POS',
                'slug' => 'pos',
                'description' => 'Akses ke Point of Sales',
                'module' => 'penjualan',
                'is_active' => true,
            ],
            [
                'name' => 'POS View',
                'slug' => 'pos.view',
                'description' => 'Melihat transaksi POS',
                'module' => 'penjualan',
                'is_active' => true,
            ],
            [
                'name' => 'POS Create',
                'slug' => 'pos.create',
                'description' => 'Membuat transaksi POS',
                'module' => 'penjualan',
                'is_active' => true,
            ],
            [
                'name' => 'POS History',
                'slug' => 'pos.history',
                'description' => 'Melihat riwayat transaksi POS',
                'module' => 'penjualan',
                'is_active' => true,
            ],
            [
                'name' => 'POS Settings',
                'slug' => 'pos.settings',
                'description' => 'Mengatur COA POS',
                'module' => 'penjualan',
                'is_active' => true,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $this->command->info('POS permissions seeded successfully!');
    }
}
