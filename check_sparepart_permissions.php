<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Sparepart Permissions ===\n\n";

// Check if permissions exist
$permissions = \App\Models\Permission::where('module', 'inventaris')
    ->where('menu', 'sparepart')
    ->get();

if ($permissions->isEmpty()) {
    echo "❌ NO SPAREPART PERMISSIONS FOUND!\n\n";
    echo "Creating sparepart permissions...\n\n";
    
    $permsToCreate = [
        [
            'name' => 'inventaris.sparepart.view',
            'module' => 'inventaris',
            'menu' => 'sparepart',
            'action' => 'view',
            'display_name' => 'Lihat Sparepart',
            'description' => 'Dapat melihat halaman sparepart'
        ],
        [
            'name' => 'inventaris.sparepart.create',
            'module' => 'inventaris',
            'menu' => 'sparepart',
            'action' => 'create',
            'display_name' => 'Tambah Sparepart',
            'description' => 'Dapat menambah sparepart baru'
        ],
        [
            'name' => 'inventaris.sparepart.edit',
            'module' => 'inventaris',
            'menu' => 'sparepart',
            'action' => 'edit',
            'display_name' => 'Edit Sparepart',
            'description' => 'Dapat mengedit sparepart'
        ],
        [
            'name' => 'inventaris.sparepart.delete',
            'module' => 'inventaris',
            'menu' => 'sparepart',
            'action' => 'delete',
            'display_name' => 'Hapus Sparepart',
            'description' => 'Dapat menghapus sparepart'
        ],
    ];
    
    foreach ($permsToCreate as $perm) {
        \App\Models\Permission::create($perm);
        echo "✅ Created: {$perm['name']}\n";
    }
    
    echo "\n✅ All sparepart permissions created!\n\n";
} else {
    echo "✅ Found {$permissions->count()} sparepart permissions:\n\n";
    foreach ($permissions as $perm) {
        echo "  - {$perm->name} ({$perm->action})\n";
    }
}

echo "\n=== Checking All Inventaris Permissions ===\n\n";
$allInventaris = \App\Models\Permission::where('module', 'inventaris')
    ->orderBy('menu')
    ->orderBy('action')
    ->get();

$grouped = $allInventaris->groupBy('menu');
foreach ($grouped as $menu => $perms) {
    echo "📄 {$menu}:\n";
    foreach ($perms as $perm) {
        echo "   - {$perm->action}\n";
    }
    echo "\n";
}

echo "Done!\n";
