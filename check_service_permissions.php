<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING SERVICE MANAGEMENT PERMISSIONS ===\n\n";

$serviceMenus = [
    'invoice' => 'Invoice Service',
    'history' => 'History Service',
    'ongkir' => 'Ongkir Service',
    'mesin' => 'Mesin Customer',
];

$requiredActions = ['view', 'create', 'edit', 'delete', 'export'];

$allMissing = [];

foreach ($serviceMenus as $menu => $menuName) {
    echo "{$menuName}:\n";
    
    $perms = DB::table('permissions')
        ->where('module', 'service')
        ->where('menu', $menu)
        ->orderBy('action')
        ->get();
    
    if ($perms->count() > 0) {
        foreach ($perms as $perm) {
            echo "  ✓ {$perm->name} ({$perm->display_name})\n";
        }
    } else {
        echo "  ✗ NO PERMISSIONS FOUND\n";
    }
    
    // Check for missing actions
    $existingActions = $perms->pluck('action')->toArray();
    $missing = array_diff($requiredActions, $existingActions);
    
    if (count($missing) > 0) {
        echo "  ⚠ Missing actions: " . implode(', ', $missing) . "\n";
        foreach ($missing as $action) {
            $allMissing[] = [
                'menu' => $menu,
                'menuName' => $menuName,
                'action' => $action
            ];
        }
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total Missing Permissions: " . count($allMissing) . "\n";

if (count($allMissing) > 0) {
    echo "\n=== MISSING PERMISSIONS LIST ===\n";
    foreach ($allMissing as $miss) {
        echo "- service.{$miss['menu']}.{$miss['action']} ({$miss['menuName']} - " . ucfirst($miss['action']) . ")\n";
    }
}

echo "\n=== DONE ===\n";
