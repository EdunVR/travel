<?php
/**
 * Script untuk test permission sales.laporan.delete
 * Run: php test_sales_laporan_delete_permission.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\Role;

echo "Testing Sales Laporan Delete Permission...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Check if permission exists
$permission = Permission::where('name', 'sales.laporan.delete')->first();

if ($permission) {
    echo "✅ Permission found:\n";
    echo "   Name: {$permission->name}\n";
    echo "   Display Name: {$permission->display_name}\n";
    echo "   Module: {$permission->module}\n";
    echo "   Menu: {$permission->menu}\n";
    echo "   Action: {$permission->action}\n";
    echo "   Description: {$permission->description}\n\n";
    
    // Check all sales.laporan permissions
    echo "📋 All Sales Laporan Permissions:\n";
    $salesLaporanPerms = Permission::where('module', 'sales')
                                  ->where('menu', 'laporan')
                                  ->get();
    
    foreach ($salesLaporanPerms as $perm) {
        echo "   • {$perm->name} - {$perm->display_name}\n";
    }
    
    echo "\n🔐 Roles that can be assigned this permission:\n";
    $roles = Role::all();
    foreach ($roles as $role) {
        echo "   • {$role->name} ({$role->display_name})\n";
    }
    
} else {
    echo "❌ Permission 'sales.laporan.delete' not found!\n";
    echo "Please run: php create_sales_laporan_delete_permission.php\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Test completed!\n";
echo "\nNext steps:\n";
echo "1. Clear cache: php artisan config:clear && php artisan view:clear\n";
echo "2. Open Role Management modal in admin panel\n";
echo "3. Look for 'Penjualan > Laporan Penjualan > Delete' permission\n";
echo "4. Assign to appropriate roles (e.g., super_admin)\n";
echo "5. Test delete functionality in Laporan Penjualan page\n";