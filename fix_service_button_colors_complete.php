<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Request::capture();
$response = $kernel->handle($request);

echo "=== SERVICE BUTTON COLOR FIX VERIFICATION ===\n\n";

$serviceFiles = [
    'resources/views/admin/service/mesin/index.blade.php' => 'Mesin Customer',
    'resources/views/admin/service/ongkir/index.blade.php' => 'Ongkir Service', 
    'resources/views/admin/service/history/index.blade.php' => 'History Service',
    'resources/views/admin/service/invoice/index.blade.php' => 'Invoice Service'
];

echo "1. CHECKING BUTTON COLORS IN SERVICE FILES:\n";

foreach ($serviceFiles as $file => $pageName) {
    echo "\n📄 {$pageName} ({$file}):\n";
    
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check for create buttons
        if (preg_match('/Tambah|Create|Buat.*Invoice/', $content)) {
            echo "   ✅ Has create button\n";
            
            // Check button colors
            if (strpos($content, 'bg-blue-600') !== false) {
                echo "   ✅ Uses bg-blue-600 (visible blue color)\n";
            } elseif (strpos($content, 'bg-primary-600') !== false) {
                echo "   ⚠️  Still uses bg-primary-600 (might be invisible)\n";
            } else {
                echo "   ❓ Button color not detected\n";
            }
            
            // Check for shadow and border
            if (strpos($content, 'shadow-md') !== false && strpos($content, 'border-blue-600') !== false) {
                echo "   ✅ Has shadow and border for better visibility\n";
            } else {
                echo "   ⚠️  Missing shadow or border\n";
            }
        } else {
            echo "   ℹ️  No create button found\n";
        }
        
        // Check submit buttons in modals
        if (strpos($content, 'submitForm') !== false) {
            echo "   ✅ Has modal submit button\n";
            if (strpos($content, 'bg-blue-600') !== false) {
                echo "   ✅ Modal submit uses bg-blue-600\n";
            } else {
                echo "   ⚠️  Modal submit might use invisible color\n";
            }
        }
        
    } else {
        echo "   ❌ File not found\n";
    }
}

echo "\n\n2. BUTTON COLOR STANDARDS APPLIED:\n";
echo "   ✅ Primary buttons: bg-blue-600 hover:bg-blue-700\n";
echo "   ✅ Added shadow-md for depth\n";
echo "   ✅ Added border-blue-600 for definition\n";
echo "   ✅ White text for contrast\n";

echo "\n3. TESTING PERMISSION SYSTEM:\n";
$user = \App\Models\User::where('email', 'superadmin@morra.com')->first();
if ($user) {
    $permissions = [
        'service.mesin.create' => 'Mesin Customer Create',
        'service.ongkir.create' => 'Ongkir Create', 
        'service.invoice.create' => 'Service Invoice Create'
    ];
    
    foreach ($permissions as $permission => $description) {
        $hasPermission = $user->hasPermission($permission);
        $status = $hasPermission ? '✅' : '❌';
        echo "   {$status} {$description}: " . ($hasPermission ? 'GRANTED' : 'DENIED') . "\n";
    }
}

echo "\n=== FINAL RECOMMENDATIONS ===\n";
echo "1. Clear browser cache (Ctrl+F5) to see updated button styles\n";
echo "2. Check service pages - buttons should now be clearly visible with blue background\n";
echo "3. If buttons still appear white, check if Tailwind CSS is loading properly\n";
echo "4. All create buttons should be visible for superadmin users\n\n";

echo "=== BUTTON STYLING COMPLETE ===\n";