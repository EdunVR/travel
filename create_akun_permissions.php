<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== MEMBUAT PERMISSION UNTUK DAFTAR AKUN ===\n\n";

DB::beginTransaction();

try {
    $permissions = [
        [
            'name' => 'finance.akun.view',
            'display_name' => 'View Daftar Akun',
            'module' => 'finance',
            'menu' => 'akun',
            'action' => 'view'
        ],
        [
            'name' => 'finance.akun.create',
            'display_name' => 'Create Daftar Akun',
            'module' => 'finance',
            'menu' => 'akun',
            'action' => 'create'
        ],
        [
            'name' => 'finance.akun.edit',
            'display_name' => 'Edit Daftar Akun',
            'module' => 'finance',
            'menu' => 'akun',
            'action' => 'edit'
        ],
        [
            'name' => 'finance.akun.delete',
            'display_name' => 'Delete Daftar Akun',
            'module' => 'finance',
            'menu' => 'akun',
            'action' => 'delete'
        ],
    ];

    $created = 0;
    $skipped = 0;

    foreach ($permissions as $permData) {
        $existing = Permission::where('name', $permData['name'])->first();
        
        if ($existing) {
            echo "⚠️  SKIP: {$permData['name']} sudah ada\n";
            $skipped++;
        } else {
            Permission::create($permData);
            echo "✓ CREATED: {$permData['name']} - {$permData['display_name']}\n";
            $created++;
        }
    }

    DB::commit();

    echo "\n" . str_repeat("=", 80) . "\n";
    echo "SELESAI!\n";
    echo "  - Dibuat: {$created} permissions\n";
    echo "  - Dilewati: {$skipped} permissions\n";
    echo "\n";

    // Verifikasi
    echo "Verifikasi permission yang baru dibuat:\n";
    echo str_repeat("-", 80) . "\n";
    $akunPerms = Permission::where('module', 'finance')
        ->where('menu', 'akun')
        ->orderBy('action')
        ->get();

    foreach ($akunPerms as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name} ({$perm->action})\n";
    }

    echo "\n✅ Permission untuk 'Daftar Akun' sekarang sudah tersedia di modal!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
