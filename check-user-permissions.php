<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get first user
$user = App\Models\User::first();

if (!$user) {
    echo "❌ No users found in database\n";
    exit(1);
}

echo "👤 User: {$user->name} (ID: {$user->id})\n";
echo "📧 Email: {$user->email}\n\n";

// Check role
$role = $user->role;
if ($role) {
    echo "🎭 Role: {$role->name}\n\n";
    
    // Check if role has travel permissions
    $travelPermissions = $role->permissions()->where('module', 'travel')->get();
    echo "✈️ Travel Permissions in Role: {$travelPermissions->count()}\n";
    
    if ($travelPermissions->count() > 0) {
        echo "\nFirst 5 travel permissions:\n";
        foreach ($travelPermissions->take(5) as $perm) {
            echo "  - {$perm->name}\n";
        }
        
        echo "\n✅ User has travel permissions via role!\n";
        echo "   If menu still not showing, try:\n";
        echo "   1. Clear cache: php artisan cache:clear\n";
        echo "   2. Logout and login again\n";
        echo "   3. Check browser console for errors\n";
    } else {
        echo "\n⚠️ Role has NO travel permissions!\n";
        echo "\n📝 Assigning travel permissions to role...\n";
        
        // Get all travel permissions
        $allTravelPerms = App\Models\Permission::where('module', 'travel')->pluck('id');
        
        if ($allTravelPerms->count() > 0) {
            $role->permissions()->syncWithoutDetaching($allTravelPerms);
            echo "✅ Assigned {$allTravelPerms->count()} travel permissions to role: {$role->name}\n";
            echo "\n🔄 Please logout and login again to see the changes.\n";
        } else {
            echo "❌ No travel permissions found in database!\n";
            echo "   Run: php artisan db:seed --class=TravelPermissionSeeder\n";
        }
    }
} else {
    echo "❌ User has no role assigned!\n";
}

echo "\n✅ Check complete!\n";
