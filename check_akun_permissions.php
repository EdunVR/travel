<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "=== CEK PERMISSION UNTUK DAFTAR AKUN ===\n\n";

// Cek semua permission finance
echo "1. Semua Permission Finance:\n";
echo str_repeat("-", 80) . "\n";
$financePerms = Permission::where('module', 'finance')->orderBy('menu')->orderBy('action')->get();

if ($financePerms->isEmpty()) {
    echo "❌ TIDAK ADA permission finance di database!\n\n";
} else {
    foreach ($financePerms as $perm) {
        echo sprintf("   %-30s | %-15s | %-10s | %s\n", 
            $perm->name, 
            $perm->menu, 
            $perm->action,
            $perm->display_name
        );
    }
    echo "\nTotal: " . $financePerms->count() . " permissions\n\n";
}

// Cek khusus permission untuk menu 'akun' atau 'buku'
echo "2. Permission untuk menu 'akun':\n";
echo str_repeat("-", 80) . "\n";
$akunPerms = Permission::where('module', 'finance')
    ->where('menu', 'akun')
    ->get();

if ($akunPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission dengan menu='akun'\n\n";
} else {
    foreach ($akunPerms as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name} ({$perm->action})\n";
    }
    echo "\n";
}

// Cek permission untuk menu 'buku'
echo "3. Permission untuk menu 'buku' (Chart of Accounts):\n";
echo str_repeat("-", 80) . "\n";
$bukuPerms = Permission::where('module', 'finance')
    ->where('menu', 'buku')
    ->get();

if ($bukuPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission dengan menu='buku'\n\n";
} else {
    foreach ($bukuPerms as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name} ({$perm->action})\n";
    }
    echo "\n";
}

// Cek permission yang mengandung kata 'akun' atau 'account'
echo "4. Permission yang mengandung 'akun' atau 'account':\n";
echo str_repeat("-", 80) . "\n";
$searchPerms = Permission::where('module', 'finance')
    ->where(function($q) {
        $q->where('name', 'like', '%akun%')
          ->orWhere('name', 'like', '%account%')
          ->orWhere('display_name', 'like', '%akun%')
          ->orWhere('display_name', 'like', '%account%');
    })
    ->get();

if ($searchPerms->isEmpty()) {
    echo "❌ TIDAK ADA permission yang mengandung 'akun' atau 'account'\n\n";
} else {
    foreach ($searchPerms as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name} (menu: {$perm->menu}, action: {$perm->action})\n";
    }
    echo "\n";
}

// Rekomendasi
echo "\n=== REKOMENDASI ===\n";
echo str_repeat("=", 80) . "\n";

if ($akunPerms->isEmpty() && $bukuPerms->isEmpty()) {
    echo "❌ MASALAH: Permission untuk 'Daftar Akun' TIDAK DITEMUKAN!\n\n";
    echo "SOLUSI: Perlu membuat permission baru untuk menu 'akun':\n";
    echo "   - finance.akun.view\n";
    echo "   - finance.akun.create\n";
    echo "   - finance.akun.edit\n";
    echo "   - finance.akun.delete\n\n";
    echo "Atau gunakan permission 'buku' yang sudah ada jika sesuai.\n";
} elseif ($akunPerms->isNotEmpty()) {
    echo "✓ Permission untuk menu 'akun' sudah ada!\n";
    echo "  Config sidebar sudah benar menggunakan permission ini.\n";
} elseif ($bukuPerms->isNotEmpty()) {
    echo "✓ Permission untuk menu 'buku' sudah ada!\n";
    echo "  Config sidebar menggunakan 'finance.buku.view' - ini sudah benar.\n";
}

echo "\n";
