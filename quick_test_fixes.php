<?php

/**
 * Quick Test untuk Memverifikasi Perbaikan Post-Optimasi
 */

echo "🧪 Memulai Quick Test Perbaikan...\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Test file JavaScript helpers ada
echo "1. Testing JavaScript Helper Files...\n";

$jsFiles = [
    'public/js/alpine-helpers.js' => 'Alpine.js Helper',
    'public/js/production-form-fix.js' => 'Production Form Fix',
    'public/js/sparepart.js' => 'Sparepart Main Script'
];

foreach ($jsFiles as $file => $description) {
    if (file_exists($file)) {
        $success[] = "✅ $description file exists";
        
        // Check file content
        $content = file_get_contents($file);
        if (strlen($content) > 100) {
            $success[] = "✅ $description has content (" . number_format(strlen($content)) . " bytes)";
        } else {
            $warnings[] = "⚠️  $description file seems too small";
        }
    } else {
        $errors[] = "❌ $description file missing: $file";
    }
}

// 2. Test layout admin sudah include helper scripts
echo "\n2. Testing Admin Layout Integration...\n";

$adminLayoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutPath)) {
    $content = file_get_contents($adminLayoutPath);
    
    if (strpos($content, 'alpine-helpers.js') !== false) {
        $success[] = "✅ Alpine helpers included in admin layout";
    } else {
        $errors[] = "❌ Alpine helpers not included in admin layout";
    }
    
    if (strpos($content, 'production-form-fix.js') !== false) {
        $success[] = "✅ Production form fix included in admin layout";
    } else {
        $errors[] = "❌ Production form fix not included in admin layout";
    }
    
    if (strpos($content, 'csrf-token') !== false) {
        $success[] = "✅ CSRF token meta tag exists";
    } else {
        $warnings[] = "⚠️  CSRF token meta tag might be missing";
    }
} else {
    $errors[] = "❌ Admin layout file not found";
}

// 3. Test sparepart view sudah ada routes definition
echo "\n3. Testing Sparepart View Routes...\n";

$sparepartViewPath = 'resources/views/admin/inventaris/sparepart/index.blade.php';
if (file_exists($sparepartViewPath)) {
    $content = file_get_contents($sparepartViewPath);
    
    if (strpos($content, 'window.sparepartRoutes') !== false) {
        $success[] = "✅ Sparepart routes defined in view";
    } else {
        $warnings[] = "⚠️  Sparepart routes might not be defined in view";
    }
    
    if (strpos($content, 'x-data="sparepartData()"') !== false) {
        $success[] = "✅ Alpine.js component properly initialized";
    } else {
        $errors[] = "❌ Alpine.js component not found in sparepart view";
    }
    
    if (strpos($content, 'sparepart.js') !== false) {
        $success[] = "✅ Sparepart.js script included";
    } else {
        $errors[] = "❌ Sparepart.js script not included";
    }
} else {
    $errors[] = "❌ Sparepart view file not found";
}

// 4. Test production view ada
echo "\n4. Testing Production Views...\n";

$productionPaths = [
    'resources/views/produksi/index.blade.php' => 'Production Index',
    'resources/views/produksi/create.blade.php' => 'Production Create'
];

foreach ($productionPaths as $path => $description) {
    if (file_exists($path)) {
        $success[] = "✅ $description view exists";
        
        $content = file_get_contents($path);
        if (strpos($content, 'id_produk') !== false) {
            $success[] = "✅ $description has product_id field";
        } else {
            $warnings[] = "⚠️  $description might not have product_id field";
        }
    } else {
        $warnings[] = "⚠️  $description view not found: $path";
    }
}

// 5. Test cache dan config
echo "\n5. Testing Application Configuration...\n";

// Check if Laravel is properly configured
if (file_exists('.env')) {
    $success[] = "✅ Environment file exists";
} else {
    $errors[] = "❌ Environment file missing";
}

if (file_exists('bootstrap/cache/config.php')) {
    $success[] = "✅ Configuration cached";
} else {
    $warnings[] = "⚠️  Configuration not cached (run php artisan config:cache)";
}

if (file_exists('bootstrap/cache/routes-v7.php')) {
    $success[] = "✅ Routes cached";
} else {
    $warnings[] = "⚠️  Routes not cached (run php artisan route:cache)";
}

// 6. Test dokumentasi
echo "\n6. Testing Documentation...\n";

$docFiles = [
    'POST_OPTIMIZATION_TESTING_CHECKLIST.md' => 'Testing Checklist',
    'PERBAIKAN_ERROR_POST_OPTIMASI_SELESAI.md' => 'Fix Summary'
];

foreach ($docFiles as $file => $description) {
    if (file_exists($file)) {
        $success[] = "✅ $description documentation exists";
    } else {
        $warnings[] = "⚠️  $description documentation missing";
    }
}

// 7. Generate test report
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 QUICK TEST REPORT\n";
echo str_repeat("=", 60) . "\n\n";

if (!empty($success)) {
    echo "✅ SUCCESSES (" . count($success) . "):\n";
    foreach ($success as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

// Overall status
$totalIssues = count($errors);
$totalWarnings = count($warnings);
$totalSuccess = count($success);

echo str_repeat("-", 60) . "\n";
echo "📈 OVERALL STATUS:\n";
echo "   ✅ Successes: $totalSuccess\n";
echo "   ⚠️  Warnings: $totalWarnings\n";
echo "   ❌ Errors: $totalIssues\n\n";

if ($totalIssues === 0) {
    echo "🎉 ALL CRITICAL TESTS PASSED!\n";
    echo "   Aplikasi siap untuk testing manual.\n\n";
    
    echo "🚀 NEXT STEPS:\n";
    echo "   1. Buka browser dan test halaman sparepart\n";
    echo "   2. Test form produksi dengan pilih produk\n";
    echo "   3. Check browser console untuk JavaScript errors\n";
    echo "   4. Verify semua modal berfungsi dengan baik\n\n";
    
    $exitCode = 0;
} else {
    echo "🚨 CRITICAL ISSUES FOUND!\n";
    echo "   Perbaiki error di atas sebelum melanjutkan.\n\n";
    
    echo "🔧 RECOMMENDED ACTIONS:\n";
    echo "   1. Jalankan ulang: php fix_post_optimization_errors.php\n";
    echo "   2. Pastikan semua file ter-copy dengan benar\n";
    echo "   3. Check file permissions\n";
    echo "   4. Restart web server jika perlu\n\n";
    
    $exitCode = 1;
}

echo "📝 DETAILED TESTING:\n";
echo "   Gunakan checklist: POST_OPTIMIZATION_TESTING_CHECKLIST.md\n";
echo "   Baca dokumentasi: PERBAIKAN_ERROR_POST_OPTIMASI_SELESAI.md\n\n";

echo "⏰ Test completed at: " . date('Y-m-d H:i:s') . "\n";

exit($exitCode);

?>