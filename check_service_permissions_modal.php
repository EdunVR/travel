<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Request::capture();
$response = $kernel->handle($request);

echo "=== SERVICE PERMISSIONS MODAL CHECK ===\n\n";

// Check if service permissions exist in database
$servicePermissions = \App\Models\Permission::where('module', 'service')->get();

echo "1. SERVICE PERMISSIONS IN DATABASE:\n";
foreach ($servicePermissions as $perm) {
    echo "   - {$perm->name} ({$perm->display_name}) - Module: {$perm->module}, Menu: {$perm->menu}, Action: {$perm->action}\n";
}

echo "\n2. CHECKING SUPERADMIN ROLE PERMISSIONS:\n";
$superAdminRole = \App\Models\Role::where('name', 'super_admin')->first();
if ($superAdminRole) {
    $roleServicePermissions = $superAdminRole->permissions()->where('module', 'service')->get();
    echo "   Super Admin has " . $roleServicePermissions->count() . " service permissions:\n";
    foreach ($roleServicePermissions as $perm) {
        $isCreate = str_contains($perm->action, 'create');
        $icon = $isCreate ? '🔥 CREATE' : '   ';
        echo "   {$icon} {$perm->name} - {$perm->display_name}\n";
    }
} else {
    echo "   ❌ Super Admin role not found!\n";
}

echo "\n3. CHECKING SIDEBAR MENU MAPPING:\n";
$sidebarMenus = config('sidebar_menu');
$serviceMenu = $sidebarMenus['Service Management'] ?? null;

if ($serviceMenu) {
    echo "   ✅ Service Management found in sidebar config\n";
    echo "   Module: {$serviceMenu['module']}\n";
    echo "   Items:\n";
    foreach ($serviceMenu['items'] as $item) {
        echo "     - {$item['name']} -> {$item['route']}\n";
        
        // Extract menu identifier like the modal does
        $menuIdentifier = str_replace(['.index', 'admin.', 'admin.service.'], '', $item['route']);
        echo "       Menu Identifier: {$menuIdentifier}\n";
        
        // Check if permissions exist for this menu
        $menuPerms = \App\Models\Permission::where('module', 'service')
                                         ->where('menu', $menuIdentifier)
                                         ->get();
        echo "       Permissions found: " . $menuPerms->count() . "\n";
        foreach ($menuPerms as $perm) {
            echo "         * {$perm->action} ({$perm->name})\n";
        }
        echo "\n";
    }
} else {
    echo "   ❌ Service Management not found in sidebar config!\n";
}

echo "\n4. MODAL PERMISSION MAPPING TEST:\n";
// Simulate the modal's permission mapping logic
$permissionsByModuleMenu = [];
foreach ($servicePermissions as $perm) {
    $module = $perm->module;
    $menu = $perm->menu;
    if (!isset($permissionsByModuleMenu[$module])) {
        $permissionsByModuleMenu[$module] = [];
    }
    if (!isset($permissionsByModuleMenu[$module][$menu])) {
        $permissionsByModuleMenu[$module][$menu] = [];
    }
    $permissionsByModuleMenu[$module][$menu][] = $perm;
}

echo "   Permissions grouped by module and menu:\n";
foreach ($permissionsByModuleMenu as $module => $menus) {
    echo "   Module: {$module}\n";
    foreach ($menus as $menu => $perms) {
        echo "     Menu: {$menu}\n";
        foreach ($perms as $perm) {
            $isCreate = str_contains($perm->action, 'create');
            $icon = $isCreate ? '🔥' : '  ';
            echo "       {$icon} {$perm->action} - {$perm->display_name}\n";
        }
    }
}

echo "\n=== RECOMMENDATION ===\n";
echo "If service create permissions are not showing in the role modal:\n";
echo "1. Check if the sidebar menu mapping is correct\n";
echo "2. Verify the menu identifier extraction logic\n";
echo "3. Ensure permissions exist with correct module and menu values\n";
echo "4. Clear browser cache and try again\n\n";

echo "=== CHECK COMPLETE ===\n";