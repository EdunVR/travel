<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING ROLES AND DIRECTIVE ===\n\n";

// 1. Check all roles
echo "1. All Roles in Database:\n";
$roles = DB::table('roles')->get();
foreach ($roles as $role) {
    echo "   - {$role->name} (ID: {$role->id})\n";
}

echo "\n";

// 2. Check AppServiceProvider for hasPermission directive
echo "2. Checking AppServiceProvider.php:\n";
$appServiceProvider = 'app/Providers/AppServiceProvider.php';
if (file_exists($appServiceProvider)) {
    $content = file_get_contents($appServiceProvider);
    echo "   File exists. Checking for hasPermission directive...\n";
    
    if (strpos($content, 'hasPermission') !== false) {
        echo "   ✓ hasPermission found in file\n";
    } else {
        echo "   ❌ hasPermission NOT found in file\n";
        echo "   Need to add @hasPermission directive\n";
    }
    
    // Check if boot method exists
    if (strpos($content, 'public function boot()') !== false) {
        echo "   ✓ boot() method exists\n";
    } else {
        echo "   ❌ boot() method not found\n";
    }
} else {
    echo "   ❌ AppServiceProvider.php not found\n";
}

echo "\n";

// 3. Check if User model has hasPermission method
echo "3. Checking User Model:\n";
$userModel = 'app/Models/User.php';
if (file_exists($userModel)) {
    $content = file_get_contents($userModel);
    if (strpos($content, 'hasPermission') !== false) {
        echo "   ✓ hasPermission method found in User model\n";
    } else {
        echo "   ❌ hasPermission method NOT found in User model\n";
    }
} else {
    echo "   ❌ User.php not found\n";
}

echo "\n=== SOLUTION ===\n";
echo "Need to:\n";
echo "1. Register @hasPermission Blade directive\n";
echo "2. Ensure User model has hasPermission method\n";
echo "3. Check if superadmin role exists with correct name\n";

?>