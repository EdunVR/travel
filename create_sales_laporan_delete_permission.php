<?php
/**
 * Script untuk menambahkan permission delete untuk Laporan Penjualan
 * Run: php create_sales_laporan_delete_permission.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

$permission = [
    'name' => 'sales.laporan.delete',
    'display_name' => 'Delete Sales Report',
    'module' => 'sales',
    'menu' => 'laporan',
    'action' => 'delete',
    'description' => 'Delete Sales Report Transaction'
];

echo "Creating Sales Report Delete permission...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$permissionModel = Permission::firstOrCreate(
    ['name' => $permission['name']],
    [
        'display_name' => $permission['display_name'],
        'module' => $permission['module'],
        'menu' => $permission['menu'],
        'action' => $permission['action'],
        'description' => $permission['description']
    ]
);

if ($permissionModel->wasRecentlyCreated) {
    echo "✅ Created: {$permission['name']}\n";
} else {
    echo "⏭️  Already exists: {$permission['name']}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Done! Sales Report Delete permission is ready.\n";
echo "\nNext steps:\n";
echo "1. Clear cache: php artisan config:clear && php artisan view:clear\n";
echo "2. Open Role Management modal\n";
echo "3. Assign permission to appropriate roles\n";
echo "4. Test the delete functionality\n";