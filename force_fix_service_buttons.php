<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FORCE FIX SERVICE BUTTONS ===\n\n";

// Files to fix
$serviceFiles = [
    'resources/views/admin/service/mesin/index.blade.php',
    'resources/views/admin/service/ongkir/index.blade.php',
    'resources/views/admin/service/history/index.blade.php'
];

$permissionMap = [
    'mesin' => 'service.mesin.create',
    'ongkir' => 'service.ongkir.create',
    'history' => 'service.invoice.create'
];

foreach ($serviceFiles as $file) {
    if (file_exists($file)) {
        echo "Processing: {$file}\n";
        
        // Create backup
        $backupFile = $file . '.backup-original-' . date('Y-m-d-H-i-s');
        copy($file, $backupFile);
        echo "  ✓ Backup created: {$backupFile}\n";
        
        // Read content
        $content = file_get_contents($file);
        
        // Replace @hasPermission with explicit condition that WILL work
        $patterns = [
            // Pattern 1: @hasPermission('permission.name')
            '/@hasPermission\([\'"]([^\'"]+)[\'"]\)/',
            // Pattern 2: @endhasPermission
            '/@endhasPermission/'
        ];
        
        $replacements = [
            // Replace with explicit auth and permission check
            '@if(auth()->check() && auth()->user()->hasRole(\'super_admin\') || (auth()->check() && auth()->user()->hasPermission(\'$1\')))',
            // Replace end directive
            '@endif'
        ];
        
        $newContent = preg_replace($patterns, $replacements, $content);
        
        // Write the fixed content
        file_put_contents($file, $newContent);
        echo "  ✓ Fixed permission directives\n";
        
        // Also create a version that ALWAYS shows buttons for testing
        $alwaysShowContent = preg_replace(
            '/@if\(auth\(\)->check\(\) && auth\(\)->user\(\)->hasRole\(\'super_admin\'\) \|\| \(auth\(\)->check\(\) && auth\(\)->user\(\)->hasPermission\([^\)]+\)\)\)/',
            '@if(auth()->check()) {{-- ALWAYS SHOW FOR TESTING --}}',
            $newContent
        );
        
        $testFile = str_replace('.blade.php', '-always-show.blade.php', $file);
        file_put_contents($testFile, $alwaysShowContent);
        echo "  ✓ Created always-show version: {$testFile}\n";
        
    } else {
        echo "❌ File not found: {$file}\n";
    }
    echo "\n";
}

// Clear all caches
echo "Clearing all caches...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✓ All caches cleared\n";
} catch (Exception $e) {
    echo "❌ Cache clear error: " . $e->getMessage() . "\n";
}

echo "\n";

// Create a simple test route that shows current user info
$testRouteContent = "
// Test route for debugging user permissions
Route::get('/debug-user', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }
    
    \$user = auth()->user();
    \$permissions = [];
    
    \$testPermissions = ['service.mesin.create', 'service.ongkir.create', 'service.invoice.create'];
    foreach (\$testPermissions as \$perm) {
        \$permissions[\$perm] = \$user->hasPermission(\$perm);
    }
    
    return [
        'user' => \$user->name,
        'email' => \$user->email,
        'role' => \$user->role->name,
        'permissions' => \$permissions,
        'is_super_admin' => \$user->hasRole('super_admin')
    ];
})->middleware('auth');
";

// Add test route
file_put_contents('routes/web.php', $testRouteContent, FILE_APPEND);
echo "✓ Added debug route: /debug-user\n";

echo "\n=== TESTING INSTRUCTIONS ===\n";
echo "1. LOGIN as superadmin@morra.com\n";
echo "2. Visit /debug-user to verify your login and permissions\n";
echo "3. Test service pages:\n";
echo "   - /admin/service/mesin (should show 'Tambah Mesin')\n";
echo "   - /admin/service/ongkir (should show 'Tambah Ongkir')\n";
echo "   - /admin/service/history (should show 'Buat Invoice Baru')\n";
echo "\n";
echo "4. If STILL not working, try the always-show versions:\n";
echo "   - Copy mesin/index-always-show.blade.php to mesin/index.blade.php\n";
echo "   - Copy ongkir/index-always-show.blade.php to ongkir/index.blade.php\n";
echo "   - Copy history/index-always-show.blade.php to history/index.blade.php\n";
echo "\n";
echo "5. To restore original files, use the .backup-original-* files\n";

echo "\n✅ FORCE FIX COMPLETE!\n";
echo "The buttons should now appear. If not, there's a deeper frontend issue.\n";

?>