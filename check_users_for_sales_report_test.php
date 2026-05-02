<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Outlet;

echo "🔍 Checking Users and Outlets for Sales Report Test\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check users
echo "👥 Available Users:\n";
$users = User::with('roles')->take(10)->get();

if ($users->isEmpty()) {
    echo "❌ No users found in database\n";
} else {
    foreach ($users as $user) {
        $roles = $user->roles->pluck('name')->join(', ');
        echo "   - {$user->name} ({$user->email}) - Roles: {$roles}\n";
        
        // Check outlet access
        if (method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets;
            echo "     Outlets: " . $userOutlets->pluck('nama_outlet')->join(', ') . "\n";
        }
        
        if (isset($user->akses_outlet) && is_array($user->akses_outlet)) {
            echo "     Akses Outlet (legacy): " . json_encode($user->akses_outlet) . "\n";
        }
    }
}

echo "\n🏢 Available Outlets:\n";
$outlets = Outlet::where('is_active', true)->get();

if ($outlets->isEmpty()) {
    echo "❌ No active outlets found in database\n";
} else {
    foreach ($outlets as $outlet) {
        echo "   - {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
    }
}

echo "\n✅ Check Complete!\n";