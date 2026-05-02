<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Login as first user
$user = App\Models\User::first();
Auth::login($user);

echo "============================================\n";
echo "Testing Blade Directive @hasModuleAccess\n";
echo "============================================\n\n";

echo "👤 Logged in as: {$user->name}\n";
echo "🎭 Role: {$user->role->name}\n\n";

// Test the directive logic manually
echo "Testing hasModuleAccess('travel')...\n\n";

if (!auth()->check()) {
    echo "❌ User not authenticated\n";
    exit(1);
}

$authUser = auth()->user();

// Check if super admin
if ($authUser->hasRole('super_admin')) {
    echo "✅ User is super_admin - should have access to ALL modules\n";
    echo "   Result: TRUE (menu should show)\n\n";
} else {
    // Check if user has any permission in travel module
    $permissions = App\Models\Permission::where('module', 'travel')->pluck('name')->toArray();
    
    echo "📋 Checking {count($permissions)} travel permissions...\n";
    
    $hasAccess = false;
    foreach ($permissions as $permission) {
        if ($authUser->hasPermission($permission)) {
            $hasAccess = true;
            echo "✅ Found permission: {$permission}\n";
            break;
        }
    }
    
    if ($hasAccess) {
        echo "\n✅ User has travel module access\n";
        echo "   Result: TRUE (menu should show)\n\n";
    } else {
        echo "\n❌ User has NO travel module access\n";
        echo "   Result: FALSE (menu will NOT show)\n\n";
    }
}

// Test hasPermission for specific permissions
echo "Testing specific permissions:\n";
$testPermissions = [
    'travel.flight.view',
    'travel.hotel.view',
    'travel.package.view',
];

foreach ($testPermissions as $perm) {
    $has = $authUser->hasPermission($perm);
    $icon = $has ? '✅' : '❌';
    echo "  {$icon} {$perm}: " . ($has ? 'TRUE' : 'FALSE') . "\n";
}

echo "\n============================================\n";
echo "Conclusion:\n";
echo "============================================\n\n";

if ($authUser->hasRole('super_admin') || $authUser->hasPermission('travel.flight.view')) {
    echo "✅ Travel Management menu SHOULD be visible\n\n";
    echo "If menu is NOT showing:\n";
    echo "  1. Clear browser cache completely\n";
    echo "  2. Close ALL browser tabs\n";
    echo "  3. Open in NEW incognito/private window\n";
    echo "  4. Login again\n";
    echo "  5. Check browser console (F12) for errors\n";
    echo "  6. Verify you're on correct URL\n";
} else {
    echo "❌ Travel Management menu will NOT be visible\n";
    echo "   User needs travel permissions assigned\n";
}

echo "\n";
