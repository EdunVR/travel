<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "=== CEK LENGKAP MENU PRODUKSI ===\n\n";

// 1. Cek permission di database
echo "1. Permission di Database:\n";
echo str_repeat("-", 80) . "\n";

$productionPerms = Permission::where('module', 'production')->orderBy('menu')->orderBy('action')->get();

if ($productionPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission untuk module 'production'\n\n";
} else {
    $groupedPerms = $productionPerms->groupBy('menu');
    
    foreach ($groupedPerms as $menu => $perms) {
        echo "\nMenu: {$menu}\n";
        foreach ($perms as $perm) {
            echo "   ✓ {$perm->name} ({$perm->action}) - {$perm->display_name}\n";
        }
    }
    
    echo "\nTotal: " . $productionPerms->count() . " permissions\n";
}

// 2. Cek config sidebar
echo "\n2. Config Sidebar:\n";
echo str_repeat("-", 80) . "\n";

$sidebarMenus = config('sidebar_menu');
$productionMenu = null;

foreach ($sidebarMenus as $menuName => $menuData) {
    if ($menuData['module'] === 'production') {
        $productionMenu = $menuData;
        echo "✓ Menu Produksi ditemukan: {$menuName}\n";
        echo "  Route: {$menuData['route']}\n";
        echo "  Icon: {$menuData['icon']}\n";
        echo "\n  Submenu:\n";
        
        foreach ($menuData['items'] as $item) {
            $perms = implode(', ', $item['permissions'] ?? []);
            echo "    - {$item['name']}\n";
            echo "      Route: {$item['route']}\n";
            echo "      Permissions: {$perms}\n";
        }
        break;
    }
}

if (!$productionMenu) {
    echo "❌ Menu Produksi TIDAK DITEMUKAN di config sidebar\n";
}

// 3. Analisis masalah
echo "\n3. Analisis:\n";
echo str_repeat("-", 80) . "\n";

$issues = [];

// Cek apakah ada permission dengan action standar
$hasView = $productionPerms->where('action', 'view')->count() > 0;
$hasCreate = $productionPerms->where('action', 'create')->count() > 0;
$hasEdit = $productionPerms->where('action', 'edit')->count() > 0;
$hasUpdate = $productionPerms->where('action', 'update')->count() > 0;
$hasDelete = $productionPerms->where('action', 'delete')->count() > 0;

echo "Permission standar:\n";
echo "  View: " . ($hasView ? '✓ ADA' : '❌ TIDAK ADA') . "\n";
echo "  Create: " . ($hasCreate ? '✓ ADA' : '❌ TIDAK ADA') . "\n";
echo "  Edit: " . ($hasEdit ? '✓ ADA' : '❌ TIDAK ADA') . "\n";
echo "  Update: " . ($hasUpdate ? '✓ ADA' : '❌ TIDAK ADA') . "\n";
echo "  Delete: " . ($hasDelete ? '✓ ADA' : '❌ TIDAK ADA') . "\n";

if (!$hasEdit && $hasUpdate) {
    $issues[] = "Permission 'edit' tidak ada, hanya 'update' (modal tidak akan menampilkan checkbox Edit)";
}

if (!$hasView) {
    $issues[] = "Permission 'view' tidak ada (menu tidak akan muncul di sidebar)";
}

if (count($issues) > 0) {
    echo "\n❌ MASALAH DITEMUKAN:\n";
    foreach ($issues as $i => $issue) {
        echo "   " . ($i + 1) . ". {$issue}\n";
    }
} else {
    echo "\n✓ Tidak ada masalah ditemukan\n";
}

echo "\n";
