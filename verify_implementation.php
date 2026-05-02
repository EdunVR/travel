<?php

/**
 * Verifikasi Implementasi Fitur Hapus Jurnal Superadmin
 * Script sederhana untuk memverifikasi file-file yang sudah dimodifikasi
 */

echo "=== VERIFIKASI IMPLEMENTASI HAPUS JURNAL SUPERADMIN ===\n\n";

$checks = [];

// 1. Cek View File
echo "1. CHECKING VIEW FILE...\n";
$viewPath = 'resources/views/admin/finance/jurnal/index.blade.php';

if (file_exists($viewPath)) {
    echo "   ✅ View file exists: {$viewPath}\n";
    
    $viewContent = file_get_contents($viewPath);
    
    // Check for superadmin button
    if (strpos($viewContent, 'deleteSuperadminJournal') !== false) {
        echo "   ✅ deleteSuperadminJournal function found\n";
        $checks['view_function'] = true;
    } else {
        echo "   ❌ deleteSuperadminJournal function not found\n";
        $checks['view_function'] = false;
    }
    
    // Check for role check
    if (strpos($viewContent, "auth()->user()->role->name === 'super_admin'") !== false) {
        echo "   ✅ Superadmin role check found\n";
        $checks['view_role_check'] = true;
    } else {
        echo "   ❌ Superadmin role check not found\n";
        $checks['view_role_check'] = false;
    }
    
    // Check for route
    if (strpos($viewContent, 'finance.journals.delete-superadmin') !== false) {
        echo "   ✅ Superadmin delete route found\n";
        $checks['view_route'] = true;
    } else {
        echo "   ❌ Superadmin delete route not found\n";
        $checks['view_route'] = false;
    }
    
    // Check for confirmation messages
    if (strpos($viewContent, 'PERINGATAN: Anda akan menghapus jurnal yang sudah diposting') !== false) {
        echo "   ✅ Warning confirmation message found\n";
        $checks['view_warning'] = true;
    } else {
        echo "   ❌ Warning confirmation message not found\n";
        $checks['view_warning'] = false;
    }
    
} else {
    echo "   ❌ View file not found: {$viewPath}\n";
    $checks['view_exists'] = false;
}

// 2. Cek Route File
echo "\n2. CHECKING ROUTE FILE...\n";
$routePath = 'routes/web.php';

if (file_exists($routePath)) {
    echo "   ✅ Route file exists: {$routePath}\n";
    
    $routeContent = file_get_contents($routePath);
    
    if (strpos($routeContent, 'deleteSuperadminJournal') !== false) {
        echo "   ✅ deleteSuperadminJournal route found\n";
        $checks['route_method'] = true;
    } else {
        echo "   ❌ deleteSuperadminJournal route not found\n";
        $checks['route_method'] = false;
    }
    
    if (strpos($routeContent, 'journals.delete-superadmin') !== false) {
        echo "   ✅ Route name 'journals.delete-superadmin' found\n";
        $checks['route_name'] = true;
    } else {
        echo "   ❌ Route name 'journals.delete-superadmin' not found\n";
        $checks['route_name'] = false;
    }
    
} else {
    echo "   ❌ Route file not found: {$routePath}\n";
    $checks['route_exists'] = false;
}

// 3. Cek Controller File
echo "\n3. CHECKING CONTROLLER FILE...\n";
$controllerPath = 'app/Http/Controllers/FinanceAccountantController.php';

if (file_exists($controllerPath)) {
    echo "   ✅ Controller file exists: {$controllerPath}\n";
    
    $controllerContent = file_get_contents($controllerPath);
    
    if (strpos($controllerContent, 'function deleteSuperadminJournal') !== false) {
        echo "   ✅ deleteSuperadminJournal method found\n";
        $checks['controller_method'] = true;
    } else {
        echo "   ❌ deleteSuperadminJournal method not found\n";
        $checks['controller_method'] = false;
    }
    
    // Check for role validation
    if (strpos($controllerContent, "auth()->user()->role->name !== 'super_admin'") !== false) {
        echo "   ✅ Role validation found\n";
        $checks['controller_validation'] = true;
    } else {
        echo "   ❌ Role validation not found\n";
        $checks['controller_validation'] = false;
    }
    
    // Check for opening balance deletion
    if (strpos($controllerContent, 'AccountOpeningBalance::where') !== false) {
        echo "   ✅ Opening balance deletion logic found\n";
        $checks['controller_opening_balance'] = true;
    } else {
        echo "   ❌ Opening balance deletion logic not found\n";
        $checks['controller_opening_balance'] = false;
    }
    
    // Check for logging
    if (strpos($controllerContent, 'Log::info') !== false || strpos($controllerContent, '\Log::info') !== false) {
        echo "   ✅ Logging found\n";
        $checks['controller_logging'] = true;
    } else {
        echo "   ❌ Logging not found\n";
        $checks['controller_logging'] = false;
    }
    
} else {
    echo "   ❌ Controller file not found: {$controllerPath}\n";
    $checks['controller_exists'] = false;
}

// 4. Cek Documentation Files
echo "\n4. CHECKING DOCUMENTATION FILES...\n";

$docFiles = [
    'JURNAL_SUPERADMIN_DELETE_IMPLEMENTATION_COMPLETE.md',
    'PANDUAN_HAPUS_JURNAL_SUPERADMIN.md',
    'test_jurnal_superadmin_delete.php',
    'test_jurnal_superadmin_delete.bat'
];

foreach ($docFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ Documentation file exists: {$file}\n";
        $checks["doc_{$file}"] = true;
    } else {
        echo "   ❌ Documentation file missing: {$file}\n";
        $checks["doc_{$file}"] = false;
    }
}

// 5. Summary
echo "\n=== SUMMARY ===\n";

$totalChecks = count($checks);
$passedChecks = count(array_filter($checks));
$failedChecks = $totalChecks - $passedChecks;

echo "Total Checks: {$totalChecks}\n";
echo "Passed: {$passedChecks} ✅\n";
echo "Failed: {$failedChecks} ❌\n";

$percentage = ($passedChecks / $totalChecks) * 100;
echo "Success Rate: " . number_format($percentage, 1) . "%\n\n";

if ($percentage >= 90) {
    echo "🎉 IMPLEMENTATION STATUS: EXCELLENT\n";
    echo "✅ Fitur hapus jurnal superadmin siap digunakan!\n";
} elseif ($percentage >= 75) {
    echo "⚠️ IMPLEMENTATION STATUS: GOOD\n";
    echo "✅ Fitur hampir lengkap, perlu sedikit perbaikan\n";
} elseif ($percentage >= 50) {
    echo "⚠️ IMPLEMENTATION STATUS: NEEDS WORK\n";
    echo "❌ Beberapa komponen masih perlu diperbaiki\n";
} else {
    echo "❌ IMPLEMENTATION STATUS: INCOMPLETE\n";
    echo "❌ Implementasi belum lengkap, perlu banyak perbaikan\n";
}

// Detailed failed checks
if ($failedChecks > 0) {
    echo "\n=== FAILED CHECKS ===\n";
    foreach ($checks as $check => $status) {
        if (!$status) {
            echo "❌ {$check}\n";
        }
    }
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Jalankan aplikasi Laravel\n";
echo "2. Login sebagai superadmin\n";
echo "3. Buka halaman Jurnal Umum\n";
echo "4. Cari jurnal dengan status 'posted'\n";
echo "5. Verifikasi tombol hapus khusus muncul\n";
echo "6. Test fitur dengan hati-hati (gunakan data testing)\n";

echo "\n=== VERIFICATION COMPLETED ===\n";