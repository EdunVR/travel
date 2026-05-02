<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use App\Models\User;

echo "=== DEEP DEBUG PERMISSION ISSUE ===\n\n";

// 1. Check current logged in user (simulate session)
echo "1. Checking Current User Session:\n";
try {
    // Get the user that should be logged in
    $user = User::where('email', 'superadmin@morra.com')->first();
    if ($user) {
        echo "   Found user: {$user->name} (ID: {$user->id})\n";
        echo "   Role: {$user->role->name} (ID: {$user->role_id})\n";
        echo "   Active: " . ($user->is_active ? 'YES' : 'NO') . "\n";
        
        // Test hasPermission directly
        $testPermissions = ['service.mesin.create', 'service.ongkir.create', 'service.invoice.create'];
        foreach ($testPermissions as $perm) {
            $result = $user->hasPermission($perm);
            echo "   hasPermission('{$perm}'): " . ($result ? 'TRUE' : 'FALSE') . "\n";
        }
    } else {
        echo "   ❌ User superadmin@morra.com not found!\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Test Blade directive registration
echo "2. Testing Blade Directive Registration:\n";
try {
    $directives = Blade::getCustomDirectives();
    if (isset($directives['hasPermission'])) {
        echo "   ✓ @hasPermission directive is registered\n";
        
        // Try to execute the directive function
        $directiveFunction = $directives['hasPermission'];
        echo "   ✓ Directive function exists\n";
        
    } else {
        echo "   ❌ @hasPermission directive NOT registered\n";
        echo "   Available directives: " . implode(', ', array_keys($directives)) . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking directives: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Check if there are any middleware or guards blocking
echo "3. Checking Auth Configuration:\n";
try {
    $guards = config('auth.guards');
    $defaultGuard = config('auth.defaults.guard');
    echo "   Default guard: {$defaultGuard}\n";
    echo "   Available guards: " . implode(', ', array_keys($guards)) . "\n";
    
    // Check if web guard is properly configured
    if (isset($guards['web'])) {
        echo "   Web guard driver: {$guards['web']['driver']}\n";
        echo "   Web guard provider: {$guards['web']['provider']}\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error checking auth config: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Create a test to simulate the exact Blade condition
echo "4. Simulating Exact Blade Condition:\n";
if ($user) {
    // Simulate what happens in the Blade template
    echo "   Testing: auth()->check() && auth()->user()->hasPermission('service.mesin.create')\n";
    
    // Part 1: auth()->check() simulation
    $authCheck = true; // Assume user is logged in
    echo "   auth()->check(): " . ($authCheck ? 'TRUE' : 'FALSE') . "\n";
    
    // Part 2: auth()->user()->hasPermission() simulation
    $hasPermission = $user->hasPermission('service.mesin.create');
    echo "   auth()->user()->hasPermission('service.mesin.create'): " . ($hasPermission ? 'TRUE' : 'FALSE') . "\n";
    
    // Combined result
    $finalResult = $authCheck && $hasPermission;
    echo "   Final result: " . ($finalResult ? 'TRUE (SHOW BUTTON)' : 'FALSE (HIDE BUTTON)') . "\n";
}

echo "\n";

// 5. Check if there are any view caching issues
echo "5. Checking View Cache:\n";
$viewCachePath = storage_path('framework/views');
if (is_dir($viewCachePath)) {
    $cachedFiles = glob($viewCachePath . '/*');
    echo "   Cached view files: " . count($cachedFiles) . "\n";
    
    // Look for service-related cached views
    $serviceViews = array_filter($cachedFiles, function($file) {
        return strpos(basename($file), 'service') !== false;
    });
    
    if (count($serviceViews) > 0) {
        echo "   Service-related cached views found: " . count($serviceViews) . "\n";
        foreach ($serviceViews as $view) {
            echo "     - " . basename($view) . "\n";
        }
        echo "   ⚠️  These might contain old cached versions\n";
    } else {
        echo "   No service-related cached views found\n";
    }
} else {
    echo "   View cache directory not found\n";
}

echo "\n";

// 6. Test the actual view compilation
echo "6. Testing View Compilation:\n";
try {
    // Create a simple test view content
    $testViewContent = '@hasPermission(\'service.mesin.create\')
<div>BUTTON SHOULD SHOW</div>
@else
<div>BUTTON HIDDEN - NO PERMISSION</div>
@endhasPermission';
    
    // Try to compile it
    $compiled = Blade::compileString($testViewContent);
    echo "   ✓ Blade compilation successful\n";
    echo "   Compiled code preview:\n";
    echo "   " . substr($compiled, 0, 200) . "...\n";
    
} catch (Exception $e) {
    echo "   ❌ Blade compilation error: " . $e->getMessage() . "\n";
}

echo "\n";

// 7. Check for any conflicting directives or middleware
echo "7. Checking for Conflicts:\n";

// Check if there are any other permission-related directives
$allDirectives = Blade::getCustomDirectives();
$permissionDirectives = array_filter(array_keys($allDirectives), function($key) {
    return strpos(strtolower($key), 'permission') !== false || 
           strpos(strtolower($key), 'can') !== false ||
           strpos(strtolower($key), 'role') !== false;
});

if (count($permissionDirectives) > 0) {
    echo "   Permission-related directives found:\n";
    foreach ($permissionDirectives as $directive) {
        echo "     - @{$directive}\n";
    }
} else {
    echo "   No other permission-related directives found\n";
}

echo "\n";

// 8. Final recommendations
echo "=== FINAL ANALYSIS ===\n";

$issues = [];
$solutions = [];

if (!$user) {
    $issues[] = "Superadmin user not found";
    $solutions[] = "Create or fix superadmin user";
}

if (!isset($directives['hasPermission'])) {
    $issues[] = "@hasPermission directive not registered";
    $solutions[] = "Fix AppServiceProvider registration";
}

if (count($serviceViews) > 0) {
    $issues[] = "Cached views might contain old versions";
    $solutions[] = "Clear view cache completely";
}

if (count($issues) > 0) {
    echo "❌ ISSUES FOUND:\n";
    foreach ($issues as $i => $issue) {
        echo "   " . ($i + 1) . ". {$issue}\n";
    }
    echo "\n";
    
    echo "🔧 SOLUTIONS:\n";
    foreach ($solutions as $i => $solution) {
        echo "   " . ($i + 1) . ". {$solution}\n";
    }
} else {
    echo "✅ No obvious issues found in backend\n";
    echo "🔍 The problem might be:\n";
    echo "   1. Frontend JavaScript errors\n";
    echo "   2. Browser cache\n";
    echo "   3. User not actually logged in as expected\n";
    echo "   4. View template syntax issues\n";
}

echo "\n";
echo "🚀 NEXT STEPS:\n";
echo "1. Clear ALL caches (Laravel + Browser)\n";
echo "2. Login as superadmin@morra.com\n";
echo "3. Check browser console for errors\n";
echo "4. Test /test-permission route\n";
echo "5. If still failing, use the -test.blade.php versions\n";

?>