<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Outlet;

echo "🔍 Checking Users and Outlets (Simple)\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Check users
echo "👥 Available Users:\n";
$users = User::take(5)->get();

if ($users->isEmpty()) {
    echo "❌ No users found in database\n";
} else {
    foreach ($users as $user) {
        echo "   - {$user->name} ({$user->email})\n";
        
        // Check if user has hasRole method
        if (method_exists($user, 'hasRole')) {
            try {
                $isSuperAdmin = $user->hasRole('super_admin');
                echo "     Super Admin: " . ($isSuperAdmin ? 'Yes' : 'No') . "\n";
            } catch (Exception $e) {
                echo "     Role check failed: " . $e->getMessage() . "\n";
            }
        } else {
            echo "     No hasRole method available\n";
        }
        
        // Check outlet access
        if (isset($user->akses_outlet)) {
            echo "     Akses Outlet: " . json_encode($user->akses_outlet) . "\n";
        }
    }
}

echo "\n🏢 Available Outlets:\n";
$outlets = Outlet::where('is_active', true)->take(5)->get();

if ($outlets->isEmpty()) {
    echo "❌ No active outlets found in database\n";
} else {
    foreach ($outlets as $outlet) {
        echo "   - {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
    }
}

echo "\n✅ Check Complete!\n";