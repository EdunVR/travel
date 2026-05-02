<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "=== CEK PERMISSION BUKU BESAR ===\n\n";

// Cek permission ledger
$ledgerPerms = Permission::where('module', 'finance')
    ->where('menu', 'ledger')
    ->get();

if ($ledgerPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission untuk menu 'ledger' (Buku Besar)\n\n";
} else {
    echo "✓ Permission untuk Buku Besar:\n";
    foreach ($ledgerPerms as $perm) {
        echo "   - {$perm->name} ({$perm->action})\n";
    }
    echo "\n";
}

// Cek permission buku-besar
$bukuBesarPerms = Permission::where('module', 'finance')
    ->where('menu', 'buku-besar')
    ->get();

if ($bukuBesarPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission untuk menu 'buku-besar'\n\n";
} else {
    echo "✓ Permission untuk menu 'buku-besar':\n";
    foreach ($bukuBesarPerms as $perm) {
        echo "   - {$perm->name} ({$perm->action})\n";
    }
    echo "\n";
}

// Cek user permission
$user = auth()->user();
if ($user) {
    echo "User: {$user->name}\n";
    echo "Role: " . ($user->hasRole('super_admin') ? 'super_admin' : 'regular user') . "\n";
    
    if ($user->hasRole('super_admin')) {
        echo "✓ Super admin memiliki akses ke semua menu\n";
    } else {
        $hasLedgerPerm = $user->hasPermission('finance.ledger.view');
        echo "Permission finance.ledger.view: " . ($hasLedgerPerm ? '✓ ADA' : '❌ TIDAK ADA') . "\n";
    }
}

echo "\n";
