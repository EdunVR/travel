<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Outlet;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        if (!$superAdminRole) {
            $this->command->error('❌ Super Admin role not found. Run RolePermissionSeeder first!');
            return;
        }

        // Check if super admin already exists
        $existingAdmin = User::where('email', 'admin@system.com')->first();
        if ($existingAdmin) {
            $this->command->warn('⚠️  Super Admin already exists!');
            return;
        }

        // Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@system.com',
            'password' => Hash::make('Admin@123'),
            'phone' => '08123456789',
            'role_id' => $superAdminRole->id,
            'is_active' => true,
            'email_verified_at' => now()
        ]);

        // Assign all outlets to super admin
        $outlets = Outlet::all();
        if ($outlets->count() > 0) {
            foreach ($outlets as $outlet) {
                $superAdmin->outlets()->attach($outlet->id_outlet);
            }
            $this->command->info("   - Assigned {$outlets->count()} outlets");
        }

        $this->command->info('✅ Super Admin user created!');
        $this->command->info('   Email: admin@system.com');
        $this->command->info('   Password: Admin@123');
        $this->command->warn('   ⚠️  Please change password after first login!');
    }
}
