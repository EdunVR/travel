<?php

/**
 * Script untuk assign permission delete kontra bon ke role tertentu
 * 
 * Cara menjalankan:
 * php artisan tinker
 * $roleName = 'admin'; // Ganti dengan nama role yang diinginkan
 * include 'assign_kontrabon_delete_to_role.php';
 */

use Illuminate\Support\Facades\DB;

// Ganti dengan nama role yang diinginkan
if (!isset($roleName)) {
    echo "❌ Error: Variable \$roleName tidak ditemukan.\n";
    echo "\nCara menggunakan:\n";
    echo "php artisan tinker\n";
    echo "\$roleName = 'admin'; // Ganti dengan nama role\n";
    echo "include 'assign_kontrabon_delete_to_role.php';\n";
    exit;
}

try {
    DB::beginTransaction();

    echo "=== ASSIGN KONTRA BON DELETE PERMISSION ===\n\n";
    echo "Target Role: {$roleName}\n\n";

    // 1. Cek permission
    $permission = DB::table('permissions')
        ->where('name', 'sales.kontrabon.delete')
        ->first();

    if (!$permission) {
        echo "❌ Permission 'sales.kontrabon.delete' tidak ditemukan.\n";
        echo "Jalankan dulu: include 'create_kontrabon_delete_permission.php';\n";
        DB::rollback();
        exit;
    }

    echo "✅ Permission found:\n";
    echo "   ID: {$permission->id}\n";
    echo "   Name: {$permission->name}\n";
    echo "   Display Name: {$permission->display_name}\n\n";

    // 2. Cek role
    $role = DB::table('roles')
        ->where('name', $roleName)
        ->first();

    if (!$role) {
        echo "❌ Role '{$roleName}' tidak ditemukan.\n";
        echo "\nAvailable roles:\n";
        $roles = DB::table('roles')->get();
        foreach ($roles as $r) {
            echo "   - {$r->name}\n";
        }
        DB::rollback();
        exit;
    }

    echo "✅ Role found:\n";
    echo "   ID: {$role->id}\n";
    echo "   Name: {$role->name}\n";
    echo "   Display Name: {$role->display_name}\n\n";

    // 3. Cek apakah sudah di-assign
    $existingRolePermission = DB::table('role_permission')
        ->where('role_id', $role->id)
        ->where('permission_id', $permission->id)
        ->first();

    if ($existingRolePermission) {
        echo "⚠️ Permission sudah di-assign ke role '{$roleName}'.\n";
        echo "   Tidak perlu assign lagi.\n";
    } else {
        // 4. Assign permission ke role
        DB::table('role_permission')->insert([
            'role_id' => $role->id,
            'permission_id' => $permission->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "✅ Permission berhasil di-assign ke role '{$roleName}'!\n";
    }

    DB::commit();

    // 5. Verifikasi
    echo "\n=== VERIFICATION ===\n";
    $rolePermissions = DB::table('role_permission')
        ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
        ->where('role_permission.role_id', $role->id)
        ->where('permissions.group', 'sales')
        ->select('permissions.name', 'permissions.display_name')
        ->get();

    echo "Sales permissions for role '{$roleName}':\n";
    foreach ($rolePermissions as $rp) {
        $indicator = ($rp->name === 'sales.kontrabon.delete') ? '✅' : '  ';
        echo "{$indicator} {$rp->display_name} ({$rp->name})\n";
    }

    // 6. Cek users dengan role ini
    echo "\n=== USERS WITH THIS ROLE ===\n";
    $users = DB::table('users')
        ->join('role_user', 'users.id', '=', 'role_user.user_id')
        ->where('role_user.role_id', $role->id)
        ->select('users.id', 'users.name', 'users.email')
        ->limit(10)
        ->get();

    if ($users->count() > 0) {
        echo "Users yang akan mendapat akses hapus kontra bon:\n";
        foreach ($users as $user) {
            echo "   - {$user->name} ({$user->email})\n";
        }
        if ($users->count() >= 10) {
            echo "   ... dan lainnya\n";
        }
    } else {
        echo "Tidak ada user dengan role '{$roleName}'.\n";
    }

    echo "\n=== SUCCESS ===\n";
    echo "Permission 'sales.kontrabon.delete' berhasil di-assign ke role '{$roleName}'.\n";
    echo "User dengan role ini sekarang bisa menghapus kontra bon.\n";

} catch (\Exception $e) {
    DB::rollback();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== CARA ASSIGN KE ROLE LAIN ===\n";
echo "php artisan tinker\n";
echo "\$roleName = 'nama_role'; // Ganti dengan nama role\n";
echo "include 'assign_kontrabon_delete_to_role.php';\n";
