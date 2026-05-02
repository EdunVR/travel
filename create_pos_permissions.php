<?php
/**
 * Script untuk membuat permissions Point of Sales
 * Run: php create_pos_permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

$permissions = [
    // Point of Sales
    [
        'name' => 'sales.pos.view',
        'display_name' => 'View POS',
        'module' => 'sales',
        'menu' => 'pos',
        'action' => 'view',
        'description' => 'View Point of Sales'
    ],
    [
        'name' => 'sales.pos.create',
        'display_name' => 'Create POS',
        'module' => 'sales',
        'menu' => 'pos',
        'action' => 'create',
        'description' => 'Create POS Transaction'
    ],
    [
        'name' => 'sales.pos.edit',
        'display_name' => 'Edit POS',
        'module' => 'sales',
        'menu' => 'pos',
        'action' => 'edit',
        'description' => 'Edit POS Transaction'
    ],
    [
        'name' => 'sales.pos.delete',
        'display_name' => 'Delete POS',
        'module' => 'sales',
        'menu' => 'pos',
        'action' => 'delete',
        'description' => 'Delete POS Transaction'
    ],
    
    // Laporan Penjualan
    [
        'name' => 'sales.laporan.view',
        'display_name' => 'View Sales Report',
        'module' => 'sales',
        'menu' => 'laporan',
        'action' => 'view',
        'description' => 'View Sales Report'
    ],
    [
        'name' => 'sales.laporan.export',
        'display_name' => 'Export Sales Report',
        'module' => 'sales',
        'menu' => 'laporan',
        'action' => 'export',
        'description' => 'Export Sales Report'
    ],
    
    // Laporan Margin
    [
        'name' => 'sales.margin.view',
        'display_name' => 'View Margin Report',
        'module' => 'sales',
        'menu' => 'margin',
        'action' => 'view',
        'description' => 'View Margin Report'
    ],
    [
        'name' => 'sales.margin.export',
        'display_name' => 'Export Margin Report',
        'module' => 'sales',
        'menu' => 'margin',
        'action' => 'export',
        'description' => 'Export Margin Report'
    ],
];

echo "Creating POS and Sales Report permissions...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$created = 0;
$existing = 0;

foreach ($permissions as $permData) {
    $permission = Permission::firstOrCreate(
        ['name' => $permData['name']],
        [
            'display_name' => $permData['display_name'],
            'module' => $permData['module'],
            'menu' => $permData['menu'],
            'action' => $permData['action'],
            'description' => $permData['description']
        ]
    );
    
    if ($permission->wasRecentlyCreated) {
        echo "✅ Created: {$permData['name']}\n";
        $created++;
    } else {
        echo "⏭️  Exists: {$permData['name']}\n";
        $existing++;
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Summary:\n";
echo "  Created: {$created}\n";
echo "  Existing: {$existing}\n";
echo "  Total: " . ($created + $existing) . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n✅ Done! POS permissions are ready.\n";
echo "\nNext steps:\n";
echo "1. Clear cache: php artisan config:clear && php artisan view:clear\n";
echo "2. Open Role Management modal\n";
echo "3. Check if 'Point of Sales' appears under Penjualan menu\n";
