<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TESTING SDM PERMISSIONS IN MODAL ===\n\n";

// Simulate modal logic
$sidebarMenus = config('sidebar_menu');
$permissions = DB::table('permissions')->get();

// Group permissions by module and menu
$permissionsByModuleMenu = [];
foreach ($permissions as $perm) {
    $module = $perm->module;
    $menu = $perm->menu;
    if (!isset($permissionsByModuleMenu[$module])) {
        $permissionsByModuleMenu[$module] = [];
    }
    if (!isset($permissionsByModuleMenu[$module][$menu])) {
        $permissionsByModuleMenu[$module][$menu] = [];
    }
    $permissionsByModuleMenu[$module][$menu][] = $perm;
}

// Check SDM menu
if (isset($sidebarMenus['SDM'])) {
    $menuData = $sidebarMenus['SDM'];
    $module = $menuData['module'];
    
    echo "SDM Module (module: {$module}):\n\n";
    
    foreach ($menuData['items'] as $item) {
        // Extract menu identifier (same logic as modal)
        $menuIdentifier = str_replace(['.index', 'admin.', 'finance.', 'sdm.', 'pembelian.', 'admin.penjualan.', 'admin.crm.', 'admin.inventaris.', 'admin.service.', 'admin.investor.', 'admin.produksi.produksi.'], '', $item['route']);
        $menuIdentifier = str_replace('.', '-', $menuIdentifier);
        
        // Special mappings
        if ($item['route'] === 'sdm.kepegawaian.index') {
            $menuIdentifier = 'karyawan';
        }
        if ($item['route'] === 'sdm.attendance.index') {
            $menuIdentifier = 'absensi';
        }
        
        echo "  📄 {$item['name']}\n";
        echo "     Route: {$item['route']}\n";
        echo "     Menu Identifier: {$menuIdentifier}\n";
        
        // Find permissions
        $submenuPerms = [];
        if (isset($permissionsByModuleMenu[$module][$menuIdentifier])) {
            $submenuPerms = $permissionsByModuleMenu[$module][$menuIdentifier];
        }
        
        if (count($submenuPerms) > 0) {
            echo "     ✓ Permissions Found: " . count($submenuPerms) . "\n";
            foreach ($submenuPerms as $perm) {
                echo "       - {$perm->name} ({$perm->display_name})\n";
            }
        } else {
            echo "     ✗ NO PERMISSIONS FOUND\n";
        }
        echo "\n";
    }
} else {
    echo "✗ SDM Menu NOT FOUND\n";
}

echo "=== TEST COMPLETE ===\n";
