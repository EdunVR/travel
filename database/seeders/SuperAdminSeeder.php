<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Create Super Admin Role (if not exists)
        $superAdminRoleId = DB::table('roles')->where('name', 'Super Admin')->value('id');
        
        if (!$superAdminRoleId) {
            $superAdminRoleId = DB::table('roles')->insertGetId([
                'name' => 'Super Admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Get all permissions
        $allPermissions = DB::table('permissions')->pluck('id')->toArray();

        // 3. Assign all permissions to Super Admin role
        if (!empty($allPermissions)) {
            foreach ($allPermissions as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $superAdminRoleId, 'permission_id' => $permissionId],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }

        // 4. Create Super Admin User
        $superAdminEmail = 'superadmin@morra.com';
        
        $userId = DB::table('users')->where('email', $superAdminEmail)->value('id');
        
        if (!$userId) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Super Administrator',
                'email' => $superAdminEmail,
                'password' => Hash::make('SuperAdmin@123'),
                'phone' => '081234567890',
                'role_id' => $superAdminRoleId,
                'is_active' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            echo "✅ Super Admin user created!\n";
            echo "   Email: {$superAdminEmail}\n";
            echo "   Password: SuperAdmin@123\n\n";
        } else {
            // Update existing user
            DB::table('users')->where('id', $userId)->update([
                'name' => 'Super Administrator',
                'password' => Hash::make('SuperAdmin@123'),
                'role_id' => $superAdminRoleId,
                'is_active' => true,
                'updated_at' => $now,
            ]);

            echo "✅ Super Admin user updated!\n";
            echo "   Email: {$superAdminEmail}\n";
            echo "   Password: SuperAdmin@123\n\n";
        }

        // 5. Give access to all outlets (if outlets table exists)
        if (DB::getSchemaBuilder()->hasTable('outlets')) {
            $outlets = DB::table('outlets')->pluck('id_outlet')->toArray();
            
            if (!empty($outlets)) {
                foreach ($outlets as $outletId) {
                    DB::table('user_outlets')->updateOrInsert(
                        ['user_id' => $userId, 'outlet_id' => $outletId],
                        ['created_at' => $now, 'updated_at' => $now]
                    );
                }
                echo "✅ Super Admin assigned to " . count($outlets) . " outlets\n";
            }
        }

        echo "\n🎉 Super Admin setup complete!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Login Credentials:\n";
        echo "Email: {$superAdminEmail}\n";
        echo "Password: SuperAdmin@123\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
