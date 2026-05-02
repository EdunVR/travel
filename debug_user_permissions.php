<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug User Permissions ===\n\n";

// Get current logged in user (you need to provide user ID)
echo "Enter User ID to check: ";
$userId = trim(fgets(STDIN));

$user = \App\Models\User::find($userId);

if (!$user) {
    echo "❌ User not found!\n";
    exit;
}

echo "User: {$user->name} ({$user->email})\n\n";

// Get user roles
echo "=== Roles ===\n";
$roles = $user->roles;
foreach ($roles as $role) {
    echo "  - {$role->name} ({$role->display_name})\n";
}
echo "\n";

// Get all permissions
echo "=== All Permissions ===\n";
$allPermissions = [];
foreach ($roles as $role) {
    foreach ($role->permissions as $perm) {
        $allPermissions[$perm->name] = $perm;
    }
}

foreach ($allPermissions as $perm) {
    echo "  ✅ {$perm->name}\n";
}
echo "\nTotal: " . count($allPermissions) . " permissions\n\n";

// Check sparepart permissions specifically
echo "=== Sparepart Permissions ===\n";
$sparepartPerms = array_filter($allPermissions, function($perm) {
    return str_contains($perm->name, 'sparepart');
});

if (empty($sparepartPerms)) {
    echo "❌ NO SPAREPART PERMISSIONS!\n";
} else {
    foreach ($sparepartPerms as $perm) {
        echo "  ✅ {$perm->name}\n";
    }
}
echo "\n";

// Test hasPermission method
echo "=== Testing hasPermission() ===\n";
$testPerms = [
    'inventaris.sparepart.view',
    'inventaris.sparepart.create',
    'inventaris.sparepart.edit',
    'inventaris.sparepart.delete',
];

foreach ($testPerms as $permName) {
    $has = $user->hasPermission($permName);
    $icon = $has ? '✅' : '❌';
    echo "  {$icon} {$permName}: " . ($has ? 'YES' : 'NO') . "\n";
}
echo "\n";

// Test @can directive
echo "=== Testing Gate::allows() ===\n";
foreach ($testPerms as $permName) {
    $allows = \Illuminate\Support\Facades\Gate::allows($permName);
    $icon = $allows ? '✅' : '❌';
    echo "  {$icon} Gate::allows('{$permName}'): " . ($allows ? 'YES' : 'NO') . "\n";
}

echo "\nDone!\n";
