<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== PERBAIKAN LENGKAP PERMISSION PRODUKSI ===\n\n";

DB::beginTransaction();

try {
    // 1. Buat permission untuk menu "produksi" (Data Produksi)
    echo "1. Membuat permission untuk Data Produksi:\n";
    echo str_repeat("-", 80) . "\n";
    
    $produksiPermissions = [
        ['name' => 'production.produksi.view', 'display_name' => 'View Data Produksi', 'module' => 'production', 'menu' => 'produksi', 'action' => 'view'],
        ['name' => 'production.produksi.create', 'display_name' => 'Create Data Produksi', 'module' => 'production', 'menu' => 'produksi', 'action' => 'create'],
        ['name' => 'production.produksi.edit', 'display_name' => 'Edit Data Produksi', 'module' => 'production', 'menu' => 'produksi', 'action' => 'edit'],
        ['name' => 'production.produksi.update', 'display_name' => 'Update Data Produksi', 'module' => 'production', 'menu' => 'produksi', 'action' => 'update'],
        ['name' => 'production.produksi.delete', 'display_name' => 'Delete Data Produksi', 'module' => 'production', 'menu' => 'produksi', 'action' => 'delete'],
        ['name' => 'production.produksi.approve', 'display_name' => 'Approve Data Produksi', 'module' => 'production', 'menu' => 'produksi', 'action' => 'approve'],
    ];
    
    $created = 0;
    $skipped = 0;
    
    foreach ($produksiPermissions as $permData) {
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
    
    echo "\nHasil: {$created} dibuat, {$skipped} dilewati\n";
    
    // 2. Tambahkan permission 'edit' untuk menu lain yang hanya punya 'update'
    echo "\n2. Menambahkan permission 'edit' untuk menu lain:\n";
    echo str_repeat("-", 80) . "\n";
    
    $menusNeedEdit = ['bom', 'production-plan', 'work-order'];
    $editCreated = 0;
    
    foreach ($menusNeedEdit as $menu) {
        $updatePerm = Permission::where('module', 'production')
            ->where('menu', $menu)
            ->where('action', 'update')
            ->first();
        
        if ($updatePerm) {
            $editName = str_replace('.update', '.edit', $updatePerm->name);
            $existing = Permission::where('name', $editName)->first();
            
            if (!$existing) {
                Permission::create([
                    'name' => $editName,
                    'display_name' => str_replace('Update', 'Edit', $updatePerm->display_name),
                    'module' => 'production',
                    'menu' => $menu,
                    'action' => 'edit'
                ]);
                echo "✓ CREATED: {$editName}\n";
                $editCreated++;
            } else {
                echo "⚠️  SKIP: {$editName} sudah ada\n";
            }
        }
    }
    
    echo "\nHasil: {$editCreated} permission 'edit' ditambahkan\n";
    
    DB::commit();
    
    // 3. Verifikasi hasil
    echo "\n3. Verifikasi Permission Produksi:\n";
    echo str_repeat("-", 80) . "\n";
    
    $allPerms = Permission::where('module', 'production')->orderBy('menu')->orderBy('action')->get();
    $groupedPerms = $allPerms->groupBy('menu');
    
    foreach ($groupedPerms as $menu => $perms) {
        echo "\nMenu: {$menu}\n";
        foreach ($perms as $perm) {
            echo "   ✓ {$perm->name} ({$perm->action})\n";
        }
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "✅ SELESAI! Total permission: " . $allPerms->count() . "\n";
    echo "\nLangkah selanjutnya:\n";
    echo "1. Update config sidebar untuk menggunakan permission yang benar\n";
    echo "2. Tambahkan @can check di view actions.blade.php\n";
    echo "3. Clear cache: php artisan config:clear\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
