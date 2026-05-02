<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 TESTING INTER OUTLET ROUTE FIX\n";
echo "=================================\n\n";

echo "📋 Checking JavaScript Implementation:\n";

// Check current JavaScript implementation
$jsFile = file_get_contents('public/js/inter-outlet.js');

// Check if it uses window.routes
if (strpos($jsFile, 'window.routes?.interOutletPrint') !== false) {
    echo "   ✅ JavaScript now uses window.routes.interOutletPrint\n";
} else {
    echo "   ❌ JavaScript still uses hardcoded URL\n";
}

// Check if it has fallback
if (strpos($jsFile, "|| '/admin/penjualan/inter-outlet-sale/0/print'") !== false) {
    echo "   ✅ Fallback URL provided for safety\n";
} else {
    echo "   ❌ No fallback URL found\n";
}

// Check if it replaces the ID correctly
if (strpos($jsFile, "baseRoute.replace('/0/', `/${") !== false) {
    echo "   ✅ ID replacement logic implemented\n";
} else {
    echo "   ❌ ID replacement logic missing\n";
}

echo "\n🌐 Checking View File Route Configuration:\n";

// Check view file
$viewFile = file_get_contents('resources/views/admin/penjualan/inter-outlet/index.blade.php');

if (strpos($viewFile, "interOutletPrint: '{{ route('admin.penjualan.inter-outlet-sale.print', 0) }}'") !== false) {
    echo "   ✅ interOutletPrint route correctly configured\n";
} else {
    echo "   ❌ interOutletPrint route configuration incorrect\n";
}

echo "\n🧪 Testing Route Generation:\n";

// Test route generation
try {
    $testId = 21;
    $routeUrl = route('admin.penjualan.inter-outlet-sale.print', $testId);
    echo "   ✅ Route generates: {$routeUrl}\n";
    
    // Check if it includes the base URL correctly
    $baseUrl = config('app.url');
    if (strpos($routeUrl, $baseUrl) === 0) {
        echo "   ✅ Route includes correct base URL\n";
    } else {
        echo "   ❌ Route missing base URL\n";
    }
    
    // Check if it includes the project path
    if (strpos($routeUrl, '/tofu/') !== false) {
        echo "   ✅ Route includes project path (/tofu/)\n";
    } else {
        echo "   ⚠️  Route may not include project path\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Route generation failed: " . $e->getMessage() . "\n";
}

echo "\n💡 SOLUTION EXPLANATION:\n";
echo "   Masalah sebelumnya:\n";
echo "   - JavaScript menggunakan hardcoded URL: /admin/penjualan/...\n";
echo "   - URL tidak menyertakan base URL dan path project (/tofu)\n";
echo "   - Menghasilkan URL yang salah: https://domain/admin/... (tanpa /tofu)\n";
echo "   \n";
echo "   Solusi sekarang:\n";
echo "   - JavaScript menggunakan window.routes.interOutletPrint\n";
echo "   - Route helper Laravel menghasilkan URL lengkap dengan base URL\n";
echo "   - URL benar: https://domain/tofu/admin/penjualan/...\n";

echo "\n🔧 How It Works:\n";
echo "   1. View file: route('admin.penjualan.inter-outlet-sale.print', 0)\n";
echo "   2. Laravel generates: https://domain/tofu/admin/penjualan/inter-outlet-sale/0/print\n";
echo "   3. JavaScript: baseRoute.replace('/0/', '/\${transactionId}/')\n";
echo "   4. Final URL: https://domain/tofu/admin/penjualan/inter-outlet-sale/21/print\n";

echo "\n🧪 TESTING STEPS:\n";
echo "   1. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "   2. Hard refresh the inter-outlet page (Ctrl+F5)\n";
echo "   3. Test print functionality\n";
echo "   4. Check browser console - should show correct full URL\n";
echo "   5. PDF should open successfully\n";

echo "\n✅ EXPECTED RESULT:\n";
echo "   URL yang dihasilkan sekarang akan menyertakan base URL dan path project\n";
echo "   sehingga PDF dapat diakses dengan benar.\n";

echo "\n";