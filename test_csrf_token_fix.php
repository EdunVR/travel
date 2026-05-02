<?php
/**
 * Test CSRF Token Generation
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== CSRF Token Test ===\n\n";

// Test CSRF token generation
echo "1. Testing CSRF token generation:\n";
try {
    $token = csrf_token();
    echo "   Token generated: " . (strlen($token) > 0 ? "Yes" : "No") . "\n";
    echo "   Token length: " . strlen($token) . "\n";
    echo "   Token: " . $token . "\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing Laravel session:\n";
try {
    $session = app('session');
    echo "   Laravel session driver: " . $session->getDriverName() . "\n";
    echo "   Session started: " . ($session->isStarted() ? "Yes" : "No") . "\n";
    
    // Try to start session if not started
    if (!$session->isStarted()) {
        $session->start();
        echo "   Session started manually: Yes\n";
    }
    
    // Generate token again
    $token2 = csrf_token();
    echo "   Token after session start: " . $token2 . "\n";
    echo "   Token length: " . strlen($token2) . "\n";
    
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";