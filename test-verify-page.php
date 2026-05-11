<?php
/**
 * Test script to check if payment verification page loads
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Payment Verification Page...\n\n";

// Check if route exists
try {
    $route = route('admin.inventaris.travel.payment.verify');
    echo "✅ Route exists: $route\n";
} catch (Exception $e) {
    echo "❌ Route error: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if controller method exists
try {
    $controller = app(\App\Http\Controllers\PaymentController::class);
    if (method_exists($controller, 'verifyIndex')) {
        echo "✅ Controller method 'verifyIndex' exists\n";
    } else {
        echo "❌ Controller method 'verifyIndex' NOT found\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Controller error: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if view exists
$viewPath = 'resources/views/admin/travel/payment/verify-payments.blade.php';
if (file_exists($viewPath)) {
    echo "✅ View file exists: $viewPath\n";
} else {
    echo "❌ View file NOT found: $viewPath\n";
    exit(1);
}

// Check pending payments count
try {
    $pendingCount = \App\Models\JamaahPayment::where('verification_status', 'pending')->count();
    echo "✅ Pending payments count: $pendingCount\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ All checks passed! Page should load correctly.\n";
echo "\nAccess the page at: " . route('admin.inventaris.travel.payment.verify') . "\n";
