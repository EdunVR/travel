<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

try {
    echo "🔍 Checking investor permissions...\n";
    
    // Get all investor permissions
    $investorPermissions = DB::table('permissions')->where('name', 'like', 'investor.%')->get();
    
    if ($investorPermissions->isEmpty()) {
        echo "❌ No investor permissions found. Running seeder first...\n";
        
        // Run the investor permission seeder
        Artisan::call('db:seed', ['--class' => 'InvestorPermissionSeeder']);
        echo "✅ Investor permissions seeded\n";
        
        // Reload permissions
        $investorPermissions = DB::table('permissions')->where('name', 'like', 'investor.%')->get();
    }
    
    echo "📋 Found " . $investorPermissions->count() . " investor permissions:\n";
    foreach ($investorPermissions as $permission) {
        echo "   - {$permission->name}\n";
    }
    
    // Get super admin role
    $superAdminRole = DB::table('roles')->where('name', 'super-admin')->first();
    
    if (!$superAdminRole) {
        echo "❌ Super admin role not found!\n";
        exit(1);
    }
    
    echo "\n🔧 Assigning permissions to super admin role...\n";
    
    $assignedCount = 0;
    foreach ($investorPermissions as $permission) {
        // Check if permission is already assigned
        $existing = DB::table('role_has_permissions')
            ->where('role_id', $superAdminRole->id)
            ->where('permission_id', $permission->id)
            ->first();
            
        if (!$existing) {
            DB::table('role_has_permissions')->insert([
                'role_id' => $superAdminRole->id,
                'permission_id' => $permission->id
            ]);
            $assignedCount++;
            echo "   ✅ Assigned: {$permission->name}\n";
        } else {
            echo "   ⏭️  Already has: {$permission->name}\n";
        }
    }
    
    echo "\n✅ Successfully assigned {$assignedCount} new permissions to super admin role\n";
    
    // Verify assignment
    echo "\n🔍 Verifying permissions...\n";
    $superAdminPermissionCount = DB::table('role_has_permissions')
        ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
        ->where('role_has_permissions.role_id', $superAdminRole->id)
        ->where('permissions.name', 'like', 'investor.%')
        ->count();
    echo "Super admin now has {$superAdminPermissionCount} investor permissions\n";
    
    // Clear cache
    Artisan::call('cache:clear');
    echo "🧹 Cache cleared\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n🎉 All done! Super admin should now have access to investor module.\n";