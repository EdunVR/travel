<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Request::capture();
$response = $kernel->handle($request);

echo "=== FINAL SERVICE BUTTONS TEST ===\n\n";

// Test user authentication
$user = \App\Models\User::where('email', 'superadmin@morra.com')->first();
if (!$user) {
    echo "❌ ERROR: superadmin@morra.com user not found!\n";
    exit(1);
}

echo "✅ User found: {$user->name} ({$user->email})\n";
echo "✅ User role: {$user->roles->first()->name}\n\n";

// Test permissions
$permissions = [
    'service.mesin.create' => 'Mesin Customer Create',
    'service.ongkir.create' => 'Ongkir Create', 
    'service.invoice.create' => 'Service Invoice Create'
];

echo "=== PERMISSION TESTS ===\n";
foreach ($permissions as $permission => $description) {
    $hasPermission = $user->hasPermission($permission);
    $status = $hasPermission ? '✅' : '❌';
    echo "{$status} {$description}: " . ($hasPermission ? 'HAS PERMISSION' : 'NO PERMISSION') . "\n";
}

echo "\n=== BLADE DIRECTIVE SIMULATION ===\n";

// Simulate @hasPermission directive
foreach ($permissions as $permission => $description) {
    $hasPermission = $user->hasPermission($permission);
    echo "Directive @hasPermission('{$permission}'): " . ($hasPermission ? 'SHOW BUTTON' : 'HIDE BUTTON') . "\n";
}

echo "\n=== VIEW FILE STATUS ===\n";

$viewFiles = [
    'resources/views/admin/service/mesin/index.blade.php' => 'service.mesin.create',
    'resources/views/admin/service/ongkir/index.blade.php' => 'service.ongkir.create', 
    'resources/views/admin/service/history/index.blade.php' => 'service.invoice.create'
];

foreach ($viewFiles as $file => $permission) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $hasDirective = strpos($content, "@hasPermission('{$permission}')") !== false;
        $status = $hasDirective ? '✅' : '❌';
        echo "{$status} {$file}: " . ($hasDirective ? 'USES CORRECT DIRECTIVE' : 'MISSING DIRECTIVE') . "\n";
    } else {
        echo "❌ {$file}: FILE NOT FOUND\n";
    }
}

echo "\n=== FINAL RECOMMENDATION ===\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Visit the service pages:\n";
echo "   - /admin/service/mesin\n";
echo "   - /admin/service/ongkir\n";
echo "   - /admin/service/history\n";
echo "3. Create buttons should now be visible!\n\n";

echo "=== TEST COMPLETE ===\n";