<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== MEMBUAT PERMISSION EDIT UNTUK PURCHASE ORDER ===\n\n";

DB::beginTransaction();

try {
    // Cek apakah permission edit sudah ada
    $existing = Permission::where('name', 'procurement.purchase-order.edit')->first();
    
    if ($existing) {
        echo "⚠️  Permission 'procurement.purchase-order.edit' sudah ada\n";
        echo "   ID: {$existing->id}\n";
        echo "   Display Name: {$existing->display_name}\n\n";
    } else {
        // Buat permission edit
        $permission = Permission::create([
            'name' => 'procurement.purchase-order.edit',
            'display_name' => 'Edit Purchase Order',
            'module' => 'procurement',
            'menu' => 'purchase-order',
            'action' => 'edit'
        ]);
        
        echo "✓ CREATED: procurement.purchase-order.edit - Edit Purchase Order\n\n";
    }

    DB::commit();

    // Verifikasi semua permission PO
    echo "Verifikasi semua permission Purchase Order:\n";
    echo str_repeat("-", 80) . "\n";
    
    $poPerms = Permission::where('module', 'procurement')
        ->where('menu', 'purchase-order')
        ->orderBy('action')
        ->get();

    foreach ($poPerms as $perm) {
        echo "   ✓ {$perm->name} ({$perm->action})\n";
    }
    
    echo "\nTotal: " . $poPerms->count() . " permissions\n";
    
    // Cek apakah edit dan update ada
    $hasEdit = $poPerms->where('action', 'edit')->count() > 0;
    $hasUpdate = $poPerms->where('action', 'update')->count() > 0;
    
    echo "\n";
    echo "Permission Edit: " . ($hasEdit ? '✓ ADA' : '❌ TIDAK ADA') . "\n";
    echo "Permission Update: " . ($hasUpdate ? '✓ ADA' : '❌ TIDAK ADA') . "\n";
    
    echo "\n✅ Permission 'edit' sekarang tersedia untuk Purchase Order!\n";
    echo "   Modal akan menampilkan checkbox 'Edit' di submenu Purchase Order.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
