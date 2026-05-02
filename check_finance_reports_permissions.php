<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "=== CEK PERMISSION UNTUK LAPORAN KEUANGAN ===\n\n";

// Cek permission untuk Neraca Saldo
echo "1. Permission untuk Neraca Saldo:\n";
echo str_repeat("-", 80) . "\n";
$neracaSaldoPerms = Permission::where('module', 'finance')
    ->where(function($q) {
        $q->where('menu', 'neraca-saldo')
          ->orWhere('menu', 'neraca_saldo')
          ->orWhere('name', 'like', '%neraca-saldo%')
          ->orWhere('name', 'like', '%neraca_saldo%');
    })
    ->get();

if ($neracaSaldoPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission untuk 'neraca-saldo'\n\n";
} else {
    foreach ($neracaSaldoPerms as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name} (menu: {$perm->menu}, action: {$perm->action})\n";
    }
    echo "\n";
}

// Cek permission untuk Laporan Laba Rugi
echo "2. Permission untuk Laporan Laba Rugi:\n";
echo str_repeat("-", 80) . "\n";
$labaRugiPerms = Permission::where('module', 'finance')
    ->where(function($q) {
        $q->where('menu', 'laba-rugi')
          ->orWhere('menu', 'profit-loss')
          ->orWhere('name', 'like', '%laba-rugi%')
          ->orWhere('name', 'like', '%profit-loss%');
    })
    ->get();

if ($labaRugiPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission untuk 'laba-rugi' atau 'profit-loss'\n\n";
} else {
    foreach ($labaRugiPerms as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name} (menu: {$perm->menu}, action: {$perm->action})\n";
    }
    echo "\n";
}

// Cek permission untuk Arus Kas
echo "3. Permission untuk Arus Kas:\n";
echo str_repeat("-", 80) . "\n";
$arusKasPerms = Permission::where('module', 'finance')
    ->where(function($q) {
        $q->where('menu', 'arus-kas')
          ->orWhere('menu', 'cashflow')
          ->orWhere('name', 'like', '%arus-kas%')
          ->orWhere('name', 'like', '%cashflow%');
    })
    ->get();

if ($arusKasPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission untuk 'arus-kas' atau 'cashflow'\n\n";
} else {
    foreach ($arusKasPerms as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name} (menu: {$perm->menu}, action: {$perm->action})\n";
    }
    echo "\n";
}

// Cek semua permission finance untuk referensi
echo "4. Semua menu finance yang ada:\n";
echo str_repeat("-", 80) . "\n";
$allMenus = Permission::where('module', 'finance')
    ->select('menu')
    ->distinct()
    ->orderBy('menu')
    ->pluck('menu');

foreach ($allMenus as $menu) {
    $count = Permission::where('module', 'finance')->where('menu', $menu)->count();
    echo "   - {$menu} ({$count} permissions)\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "KESIMPULAN:\n";
echo str_repeat("=", 80) . "\n";

$issues = [];
if ($neracaSaldoPerms->isEmpty()) {
    $issues[] = "Neraca Saldo";
}
if ($labaRugiPerms->isEmpty()) {
    $issues[] = "Laporan Laba Rugi";
}
if ($arusKasPerms->isEmpty()) {
    $issues[] = "Arus Kas";
}

if (count($issues) > 0) {
    echo "❌ Permission TIDAK DITEMUKAN untuk:\n";
    foreach ($issues as $issue) {
        echo "   - {$issue}\n";
    }
    echo "\nPerlu membuat permission baru untuk menu-menu tersebut.\n";
} else {
    echo "✓ Semua permission sudah ada!\n";
}

echo "\n";
