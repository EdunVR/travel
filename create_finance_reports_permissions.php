<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== MEMBUAT PERMISSION UNTUK NERACA SALDO ===\n\n";

DB::beginTransaction();

try {
    $permissions = [
        [
            'name' => 'finance.neraca-saldo.view',
            'display_name' => 'View Neraca Saldo',
            'module' => 'finance',
            'menu' => 'neraca-saldo',
            'action' => 'view'
        ],
        [
            'name' => 'finance.neraca-saldo.create',
            'display_name' => 'Create Neraca Saldo',
            'module' => 'finance',
            'menu' => 'neraca-saldo',
            'action' => 'create'
        ],
        [
            'name' => 'finance.neraca-saldo.edit',
            'display_name' => 'Edit Neraca Saldo',
            'module' => 'finance',
            'menu' => 'neraca-saldo',
            'action' => 'edit'
        ],
        [
            'name' => 'finance.neraca-saldo.delete',
            'display_name' => 'Delete Neraca Saldo',
            'module' => 'finance',
            'menu' => 'neraca-saldo',
            'action' => 'delete'
        ],
        [
            'name' => 'finance.neraca-saldo.export',
            'display_name' => 'Export Neraca Saldo',
            'module' => 'finance',
            'menu' => 'neraca-saldo',
            'action' => 'export'
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
    $neracaSaldoPerms = Permission::where('module', 'finance')
        ->where('menu', 'neraca-saldo')
        ->orderBy('action')
        ->get();

    foreach ($neracaSaldoPerms as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name} ({$perm->action})\n";
    }

    echo "\n✅ Permission untuk 'Neraca Saldo' sekarang sudah tersedia!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
