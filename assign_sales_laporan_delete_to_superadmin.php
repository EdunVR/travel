<?php
/**
 * Script untuk assign permission sales.laporan.delete ke super_admin
 * Run: php assign_sales_laporan_delete_to_superadmin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\Role;

echo "Assigning Sales Laporan Delete Permission to Super Admin...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Find super_admin role
$superAdminRole = Role::where('name', 'super_admin')->first();

if (!$superAdminRole) {
    echo "❌ Super Admin role not found!\n";
    exit(1);
}

// Find the permission
$permission = Permission::where('name', 'sales.laporan.delete')->first();

if (!$permission) {
    echo "❌ Permission 'sales.laporan.delete' not found!\n";
    echo "Please run: php create_sales_laporan_delete_permission.php\n";
    exit(1);
}

// Check if already assigned
if ($superAdminRole->permissions()->where('permission_id', $permission->id)->exists()) {
    echo "⏭️  Permission already assigned to Super Admin\n";
} else {
    // Assign permission
    $superAdminRole->permissions()->attach($permission->id);
    echo "✅ Permission assigned to Super Admin\n";
}

// Show all sales.laporan permissions for super_admin
echo "\n📋 All Sales Laporan Permissions for Super Admin:\n";
$salesLaporanPerms = $superAdminRole->permissions()
                                   ->where('module', 'sales')
                                   ->where('menu', 'laporan')
                                   ->get();

foreach ($salesLaporanPerms as $perm) {
    echo "   • {$perm->name} - {$perm->display_name}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Done! Super Admin now has sales.laporan.delete permission.\n";
echo "\nNext steps:\n";
echo "1. Login as super_admin user\n";
echo "2. Go to Laporan Penjualan page\n";
echo "3. Verify that 'Hapus' button is visible\n";
echo "4. Test delete functionality\n";