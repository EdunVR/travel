<?php

/**
 * Test script untuk memverifikasi fix login page expired
 */

echo "=== Testing Login Page Expired Fix ===\n\n";

// Test 1: Check session configuration
echo "1. Testing Session Configuration...\n";
$sessionConfig = include 'config/session.php';

echo "   - Session Lifetime: " . $sessionConfig['lifetime'] . " minutes\n";
echo "   - Session Path: " . $sessionConfig['path'] . "\n";
echo "   - Session Domain: " . ($sessionConfig['domain'] ?: 'auto') . "\n";
echo "   - Secure Cookie: " . ($sessionConfig['secure'] ? 'true' : 'false') . "\n";
echo "   - Lottery: " . implode('/', $sessionConfig['lottery']) . "\n";

// Test 2: Check if CSRF middleware exists
echo "\n2. Testing CSRF Middleware...\n";
if (file_exists('app/Http/Middleware/VerifyCsrfToken.php')) {
    echo "   ✅ Custom CSRF middleware created\n";
    
    $csrfContent = file_get_contents('app/Http/Middleware/VerifyCsrfToken.php');
    if (strpos($csrfContent, 'TokenMismatchException') !== false) {
        echo "   ✅ CSRF exception handling implemented\n";
    } else {
        echo "   ❌ CSRF exception handling missing\n";
    }
} else {
    echo "   ❌ Custom CSRF middleware not found\n";
}

// Test 3: Check login view enhancements
echo "\n3. Testing Login View Enhancements...\n";
if (file_exists('resources/views/auth/login.blade.php')) {
    $loginContent = file_get_contents('resources/views/auth/login.blade.php');
    
    $checks = [
        'Cache-Control meta tag' => 'Cache-Control',
        'CSRF token refresh' => 'refreshCSRFToken',
        'Session status indicator' => 'sessionStatus',
        'Enhanced error handling' => 'clearCacheAndRefresh',
        'Activity tracking' => 'resetActivity',
        'Idle detection' => 'checkIdleStatus'
    ];
    
    foreach ($checks as $feature => $searchString) {
        if (strpos($loginContent, $searchString) !== false) {
            echo "   ✅ $feature implemented\n";
        } else {
            echo "   ❌ $feature missing\n";
        }
    }
} else {
    echo "   ❌ Login view not found\n";
}

// Test 4: Check AuthController improvements
echo "\n4. Testing AuthController Improvements...\n";
if (file_exists('app/Http/Controllers/AuthController.php')) {
    $authContent = file_get_contents('app/Http/Controllers/AuthController.php');
    
    $checks = [
        'Cache headers in showLoginForm' => 'Cache-Control',
        'Session regeneration' => 'regenerateToken',
        'Enhanced logging' => 'csrf_token_valid',
        'Session start check' => 'isStarted'
    ];
    
    foreach ($checks as $feature => $searchString) {
        if (strpos($authContent, $searchString) !== false) {
            echo "   ✅ $feature implemented\n";
        } else {
            echo "   ❌ $feature missing\n";
        }
    }
} else {
    echo "   ❌ AuthController not found\n";
}

// Test 5: Check .env configuration
echo "\n5. Testing Environment Configuration...\n";
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    
    $checks = [
        'SESSION_LIFETIME' => '720',
        'SESSION_PATH' => '/tofu',
        'SESSION_SECURE_COOKIE' => 'false'
    ];
    
    foreach ($checks as $key => $expectedValue) {
        if (strpos($envContent, "$key=$expectedValue") !== false) {
            echo "   ✅ $key correctly set to $expectedValue\n";
        } else {
            echo "   ⚠️  $key might need adjustment\n";
        }
    }
} else {
    echo "   ❌ .env file not found\n";
}

echo "\n=== Test Summary ===\n";
echo "✅ = Implemented correctly\n";
echo "❌ = Missing or needs attention\n";
echo "⚠️  = May need manual verification\n";

echo "\n=== Next Steps ===\n";
echo "1. Clear browser cache completely\n";
echo "2. Test login with fresh browser session\n";
echo "3. Test idle timeout (leave page open for 30+ minutes)\n";
echo "4. Monitor Laravel logs for CSRF token issues\n";
echo "5. Test with different browsers\n";

echo "\n=== Manual Testing Commands ===\n";
echo "# Clear Laravel cache\n";
echo "php artisan cache:clear\n";
echo "php artisan config:clear\n";
echo "php artisan session:table # if using database sessions\n";
echo "php artisan migrate # if session table needs updating\n";

echo "\nTesting completed!\n";