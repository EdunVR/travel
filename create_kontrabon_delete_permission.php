<?php

/**
 * Script untuk membuat permission delete kontra bon
 * 
 * Cara menjalankan:
 * php artisan tinker
 * include 'create_kontrabon_delete_permission.php';
 */

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // Cek apakah permission sudah ada
    $existingPermission = DB::table('permissions')
        ->where('name', 'sales.kontrabon.delete')
        ->first();

    if ($existingPermission) {
        echo "✅ Permission 'sales.kontrabon.delete' sudah ada.\n";
        echo "ID: {$existingPermission->id}\n";
        echo "Name: {$existingPermission->name}\n";
        echo "Display Name: {$existingPermission->display_name}\n";
    } else {
        // Buat permission baru
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => 'sales.kontrabon.delete',
            'display_name' => 'Hapus Kontra Bon',
            'description' => 'Dapat menghapus kontra bon dan mengembalikan status piutang',
            'group' => 'sales',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✅ Permission 'sales.kontrabon.delete' berhasil dibuat.\n";
        echo "ID: {$permissionId}\n";
        
        // Assign ke super_admin role
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        
        if ($superAdminRole) {
            // Cek apakah sudah di-assign
            $existingRolePermission = DB::table('role_permission')
                ->where('role_id', $superAdminRole->id)
                ->where('permission_id', $permissionId)
                ->first();
            
            if (!$existingRolePermission) {
                DB::table('role_permission')->insert([
                    'role_id' => $superAdminRole->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                echo "✅ Permission berhasil di-assign ke role 'super_admin'.\n";
            } else {
                echo "✅ Permission sudah di-assign ke role 'super_admin'.\n";
            }
        } else {
            echo "⚠️ Role 'super_admin' tidak ditemukan.\n";
        }
    }

    DB::commit();
    
    echo "\n=== SUMMARY ===\n";
    echo "Permission Name: sales.kontrabon.delete\n";
    echo "Display Name: Hapus Kontra Bon\n";
    echo "Group: sales\n";
    echo "Status: ✅ Ready to use\n";
    
    echo "\n=== CARA ASSIGN KE ROLE LAIN ===\n";
    echo "1. Masuk ke menu User Management > Roles\n";
    echo "2. Edit role yang ingin diberi akses\n";
    echo "3. Centang permission 'Hapus Kontra Bon' di grup Sales\n";
    echo "4. Simpan\n";

} catch (\Exception $e) {
    DB::rollback();
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
