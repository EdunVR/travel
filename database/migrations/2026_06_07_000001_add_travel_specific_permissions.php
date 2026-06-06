<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Permission baru yang belum ada di TravelPermissionSeeder
        $newPermissions = [
            // Maskapai (sebelumnya tidak ada permission)
            ['name' => 'travel.airline.view',   'display_name' => 'Lihat Maskapai',          'module' => 'travel', 'menu' => 'airline',           'action' => 'view'],
            ['name' => 'travel.airline.create', 'display_name' => 'Tambah Maskapai',          'module' => 'travel', 'menu' => 'airline',           'action' => 'create'],
            ['name' => 'travel.airline.update', 'display_name' => 'Edit Maskapai',            'module' => 'travel', 'menu' => 'airline',           'action' => 'update'],
            ['name' => 'travel.airline.delete', 'display_name' => 'Hapus Maskapai',           'module' => 'travel', 'menu' => 'airline',           'action' => 'delete'],

            // Bandara (sebelumnya tidak ada permission)
            ['name' => 'travel.airport.view',   'display_name' => 'Lihat Bandara',            'module' => 'travel', 'menu' => 'airport',           'action' => 'view'],
            ['name' => 'travel.airport.create', 'display_name' => 'Tambah Bandara',           'module' => 'travel', 'menu' => 'airport',           'action' => 'create'],
            ['name' => 'travel.airport.update', 'display_name' => 'Edit Bandara',             'module' => 'travel', 'menu' => 'airport',           'action' => 'update'],
            ['name' => 'travel.airport.delete', 'display_name' => 'Hapus Bandara',            'module' => 'travel', 'menu' => 'airport',           'action' => 'delete'],

            // Pengingat Pembayaran (sebelumnya pakai travel.booking.view)
            ['name' => 'travel.payment-reminder.view',   'display_name' => 'Lihat Pengingat Pembayaran',  'module' => 'travel', 'menu' => 'payment-reminder', 'action' => 'view'],
            ['name' => 'travel.payment-reminder.update', 'display_name' => 'Edit Pengaturan Pengingat',   'module' => 'travel', 'menu' => 'payment-reminder', 'action' => 'update'],
            ['name' => 'travel.payment-reminder.send',   'display_name' => 'Kirim Pengingat Manual',      'module' => 'travel', 'menu' => 'payment-reminder', 'action' => 'send'],

            // Affiliate - lebih spesifik (sebelumnya hanya travel.affiliate.view)
            ['name' => 'travel.affiliate.payout',    'display_name' => 'Proses Withdraw Mitra',   'module' => 'travel', 'menu' => 'affiliate', 'action' => 'payout'],
            ['name' => 'travel.affiliate.settings',  'display_name' => 'Pengaturan Affiliate',    'module' => 'travel', 'menu' => 'affiliate', 'action' => 'settings'],
            ['name' => 'travel.affiliate.approve',   'display_name' => 'Approve Mitra',           'module' => 'travel', 'menu' => 'affiliate', 'action' => 'approve'],
        ];

        foreach ($newPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        // Assign semua permission baru ke super_admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $newIds = Permission::whereIn('name', array_column($newPermissions, 'name'))->pluck('id');
            $superAdmin->permissions()->syncWithoutDetaching($newIds);
        }

        // Assign view permissions ke manager
        $manager = Role::where('name', 'manager')->first();
        if ($manager) {
            $viewIds = Permission::whereIn('name', [
                'travel.airline.view',
                'travel.airport.view',
                'travel.payment-reminder.view',
                'travel.affiliate.view',
            ])->pluck('id');
            $manager->permissions()->syncWithoutDetaching($viewIds);
        }

        // Assign view+create+update ke admin
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $adminIds = Permission::whereIn('name', [
                'travel.airline.view', 'travel.airline.create', 'travel.airline.update',
                'travel.airport.view', 'travel.airport.create', 'travel.airport.update',
                'travel.payment-reminder.view', 'travel.payment-reminder.update', 'travel.payment-reminder.send',
                'travel.affiliate.view', 'travel.affiliate.payout', 'travel.affiliate.approve',
            ])->pluck('id');
            $admin->permissions()->syncWithoutDetaching($adminIds);
        }
    }

    public function down(): void
    {
        $names = [
            'travel.airline.view', 'travel.airline.create', 'travel.airline.update', 'travel.airline.delete',
            'travel.airport.view', 'travel.airport.create', 'travel.airport.update', 'travel.airport.delete',
            'travel.payment-reminder.view', 'travel.payment-reminder.update', 'travel.payment-reminder.send',
            'travel.affiliate.payout', 'travel.affiliate.settings', 'travel.affiliate.approve',
        ];
        Permission::whereIn('name', $names)->delete();
    }
};
