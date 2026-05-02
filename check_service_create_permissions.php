<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING SERVICE CREATE PERMISSIONS ===\n\n";

// 1. Check if service permissions exist
echo "1. Checking Service Permissions in Database:\n";
$servicePermissions = DB::table('permissions')
    ->where('name', 'like', 'service.%')
    ->orderBy('name')
    ->get();

if ($servicePermissions->count() > 0) {
    foreach ($servicePermissions as $perm) {
        echo "   ✓ {$perm->name} - {$perm->display_name}\n";
    }
} else {
    echo "   ❌ No service permissions found!\n";
}

echo "\n";

// 2. Check specific create permissions
echo "2. Checking Specific Create Permissions:\n";
$createPermissions = [
    'service.mesin.create',
    'service.ongkir.create', 
    'service.invoice.create'
];

foreach ($createPermissions as $permName) {
    $exists = DB::table('permissions')->where('name', $permName)->first();
    if ($exists) {
        echo "   ✓ {$permName} exists\n";
    } else {
        echo "   ❌ {$permName} NOT FOUND\n";
    }
}

echo "\n";

// 3. Check superadmin role and permissions
echo "3. Checking Superadmin Role:\n";
$superadminRole = DB::table('roles')->where('name', 'superadmin')->first();
if ($superadminRole) {
    echo "   ✓ Superadmin role exists (ID: {$superadminRole->id})\n";
    
    // Check if superadmin has service permissions
    $rolePermissions = DB::table('role_permissions')
        ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
        ->where('role_permissions.role_id', $superadminRole->id)
        ->where('permissions.name', 'like', 'service.%')
        ->pluck('permissions.name')
        ->toArray();
    
    if (count($rolePermissions) > 0) {
        echo "   ✓ Superadmin has " . count($rolePermissions) . " service permissions:\n";
        foreach ($rolePermissions as $perm) {
            echo "     - {$perm}\n";
        }
    } else {
        echo "   ❌ Superadmin has NO service permissions!\n";
    }
} else {
    echo "   ❌ Superadmin role not found!\n";
}

echo "\n";

// 4. Check current user permissions (if logged in)
echo "4. Checking Current User (if any):\n";
try {
    if (auth()->check()) {
        $user = auth()->user();
        echo "   Current user: {$user->name} (ID: {$user->id})\n";
        echo "   Role: {$user->role->name}\n";
        
        // Check if user has service create permissions
        foreach ($createPermissions as $permName) {
            if ($user->hasPermission($permName)) {
                echo "   ✓ Has {$permName}\n";
            } else {
                echo "   ❌ Missing {$permName}\n";
            }
        }
    } else {
        echo "   No user logged in\n";
    }
} catch (Exception $e) {
    echo "   Error checking user: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Check @hasPermission directive
echo "5. Checking @hasPermission Directive:\n";
$directiveFile = 'app/Providers/AppServiceProvider.php';
if (file_exists($directiveFile)) {
    $content = file_get_contents($directiveFile);
    if (strpos($content, 'hasPermission') !== false) {
        echo "   ✓ @hasPermission directive is registered\n";
    } else {
        echo "   ❌ @hasPermission directive not found in AppServiceProvider\n";
    }
} else {
    echo "   ❌ AppServiceProvider.php not found\n";
}

echo "\n=== RECOMMENDATIONS ===\n";

// Check what needs to be created
$missingPermissions = [];
foreach ($createPermissions as $permName) {
    $exists = DB::table('permissions')->where('name', $permName)->first();
    if (!$exists) {
        $missingPermissions[] = $permName;
    }
}

if (count($missingPermissions) > 0) {
    echo "❌ Missing permissions need to be created:\n";
    foreach ($missingPermissions as $perm) {
        echo "   - {$perm}\n";
    }
    echo "\n";
}

if ($superadminRole && count($rolePermissions) == 0) {
    echo "❌ Superadmin needs to be assigned service permissions\n\n";
}

echo "✅ Run: php create_service_create_permissions.php to fix missing permissions\n";

?>