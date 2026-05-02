<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING SDM PERMISSIONS IN DATABASE ===\n\n";

// Check Kepegawaian permissions
echo "KEPEGAWAIAN & REKRUTMEN:\n";
$kepegawaianPerms = DB::table('permissions')
    ->where('module', 'hrm')
    ->where('menu', 'karyawan')
    ->orderBy('action')
    ->get();

if ($kepegawaianPerms->count() > 0) {
    foreach ($kepegawaianPerms as $perm) {
        echo "  ✓ {$perm->name} ({$perm->display_name}) - module: {$perm->module}, menu: {$perm->menu}\n";
    }
} else {
    echo "  ✗ NO PERMISSIONS FOUND\n";
}

echo "\nMANAJEMEN ABSENSI:\n";
$absensiPerms = DB::table('permissions')
    ->where('module', 'hrm')
    ->where('menu', 'absensi')
    ->orderBy('action')
    ->get();

if ($absensiPerms->count() > 0) {
    foreach ($absensiPerms as $perm) {
        echo "  ✓ {$perm->name} ({$perm->display_name}) - module: {$perm->module}, menu: {$perm->menu}\n";
    }
} else {
    echo "  ✗ NO PERMISSIONS FOUND\n";
}

echo "\n=== CHECKING SIDEBAR CONFIG ===\n\n";

$sidebarMenus = config('sidebar_menu');

if (isset($sidebarMenus['SDM'])) {
    echo "SDM Menu Found:\n";
    echo "  Module: {$sidebarMenus['SDM']['module']}\n";
    echo "  Items:\n";
    foreach ($sidebarMenus['SDM']['items'] as $item) {
        echo "    - {$item['name']} (route: {$item['route']})\n";
        if (isset($item['permissions'])) {
            echo "      Permissions: " . implode(', ', $item['permissions']) . "\n";
        }
    }
} else {
    echo "✗ SDM Menu NOT FOUND in config\n";
}

echo "\n=== SUMMARY ===\n";
echo "Kepegawaian Permissions: " . $kepegawaianPerms->count() . "\n";
echo "Absensi Permissions: " . $absensiPerms->count() . "\n";

echo "\n=== DONE ===\n";
