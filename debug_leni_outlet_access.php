<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Outlet;

echo "🔍 Debug Leni Outlet Access\n";
echo "=" . str_repeat("=", 40) . "\n\n";

$user = User::where('email', 'Leni@gmail.com')->first();

if (!$user) {
    echo "❌ User not found\n";
    exit;
}

echo "👤 User: {$user->name} ({$user->email})\n";
echo "🆔 User ID: {$user->id}\n";

// Check akses_outlet property
echo "\n🔑 Akses Outlet Property:\n";
if (isset($user->akses_outlet)) {
    echo "   Type: " . gettype($user->akses_outlet) . "\n";
    echo "   Value: " . json_encode($user->akses_outlet) . "\n";
} else {
    echo "   ❌ No akses_outlet property\n";
}

// Check outlets relation
echo "\n🏢 Outlets Relation:\n";
try {
    if (method_exists($user, 'outlets')) {
        $outlets = $user->outlets;
        echo "   Count: " . $outlets->count() . "\n";
        foreach ($outlets as $outlet) {
            echo "   - {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
        }
    } else {
        echo "   ❌ No outlets relation method\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Check roles
echo "\n👥 User Roles:\n";
try {
    if (method_exists($user, 'hasRole')) {
        $isSuperAdmin = $user->hasRole('super_admin');
        echo "   Super Admin: " . ($isSuperAdmin ? 'Yes' : 'No') . "\n";
    }
    
    if (method_exists($user, 'roles')) {
        $roles = $user->roles;
        echo "   Roles: " . $roles->pluck('name')->join(', ') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking roles: " . $e->getMessage() . "\n";
}

// Test HasOutletFilter methods
echo "\n🧪 Testing HasOutletFilter Methods:\n";
auth()->login($user);

$controller = new \App\Http\Controllers\SalesReportController();
$reflection = new ReflectionClass($controller);

// Test getUserOutletIds
try {
    $getUserOutletIdsMethod = $reflection->getMethod('getUserOutletIds');
    $getUserOutletIdsMethod->setAccessible(true);
    $userOutletIds = $getUserOutletIdsMethod->invoke($controller);
    echo "   getUserOutletIds(): " . json_encode($userOutletIds) . "\n";
} catch (Exception $e) {
    echo "   ❌ getUserOutletIds() error: " . $e->getMessage() . "\n";
}

// Test getAccessibleOutletIds
try {
    $getAccessibleOutletIdsMethod = $reflection->getMethod('getAccessibleOutletIds');
    $getAccessibleOutletIdsMethod->setAccessible(true);
    $accessibleOutletIds = $getAccessibleOutletIdsMethod->invoke($controller);
    echo "   getAccessibleOutletIds(): " . json_encode($accessibleOutletIds) . "\n";
} catch (Exception $e) {
    echo "   ❌ getAccessibleOutletIds() error: " . $e->getMessage() . "\n";
}

// Test isSuperAdmin
try {
    $isSuperAdminMethod = $reflection->getMethod('isSuperAdmin');
    $isSuperAdminMethod->setAccessible(true);
    $isSuperAdmin = $isSuperAdminMethod->invoke($controller);
    echo "   isSuperAdmin(): " . ($isSuperAdmin ? 'true' : 'false') . "\n";
} catch (Exception $e) {
    echo "   ❌ isSuperAdmin() error: " . $e->getMessage() . "\n";
}

echo "\n✅ Debug Complete!\n";