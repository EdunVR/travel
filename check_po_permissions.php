<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "=== CEK PERMISSION PURCHASE ORDER ===\n\n";

$poPerms = Permission::where('module', 'procurement')
    ->where('menu', 'purchase-order')
    ->orderBy('action')
    ->get();

if ($poPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission untuk Purchase Order\n\n";
} else {
    echo "✓ Permission Purchase Order:\n";
    foreach ($poPerms as $perm) {
        echo "   - {$perm->name} ({$perm->action})\n";
    }
    echo "\nTotal: " . $poPerms->count() . " permissions\n\n";
}

// Cek apakah ada permission edit/update
$hasEdit = $poPerms->where('action', 'edit')->count() > 0;
$hasUpdate = $poPerms->where('action', 'update')->count() > 0;

echo "Permission Edit: " . ($hasEdit ? '✓ ADA' : '❌ TIDAK ADA') . "\n";
echo "Permission Update: " . ($hasUpdate ? '✓ ADA' : '❌ TIDAK ADA') . "\n";

echo "\n";
