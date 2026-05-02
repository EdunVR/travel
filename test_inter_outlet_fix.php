<?php

/**
 * Test Inter Outlet Fix
 * Memverifikasi bahwa perbaikan error inter-outlet berhasil
 */

echo "🧪 Testing Inter Outlet Fix...\n\n";

// 1. Test JavaScript file
echo "1. Memeriksa file JavaScript...\n";

$jsFile = 'public/js/inter-outlet.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    if (strpos($content, 'const ALL') !== false) {
        echo "   ✅ Konstanta ALL ditemukan\n";
    } else {
        echo "   ❌ Konstanta ALL tidak ditemukan\n";
    }
    
    if (strpos($content, 'window.interOutletSaleApp') !== false) {
        echo "   ✅ Window assignment ditemukan\n";
    } else {
        echo "   ❌ Window assignment tidak ditemukan\n";
    }
    
    // Check for syntax errors (basic)
    if (substr_count($content, '{') === substr_count($content, '}')) {
        echo "   ✅ Bracket balance OK\n";
    } else {
        echo "   ❌ Bracket tidak seimbang\n";
    }
    
} else {
    echo "   ❌ File JavaScript tidak ditemukan\n";
}

// 2. Test patch file
echo "\n2. Memeriksa file patch...\n";

$patchFile = 'public/js/inter-outlet-patch.js';
if (file_exists($patchFile)) {
    echo "   ✅ File patch ditemukan\n";
    
    $content = file_get_contents($patchFile);
    
    if (strpos($content, 'window.ALL') !== false) {
        echo "   ✅ Window.ALL definition ditemukan\n";
    } else {
        echo "   ❌ Window.ALL definition tidak ditemukan\n";
    }
    
    if (strpos($content, 'error handler') !== false || strpos($content, 'addEventListener') !== false) {
        echo "   ✅ Error handler ditemukan\n";
    } else {
        echo "   ❌ Error handler tidak ditemukan\n";
    }
    
} else {
    echo "   ❌ File patch tidak ditemukan\n";
}

// 3. Test view file
echo "\n3. Memeriksa file view...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    if (strpos($content, 'inter-outlet-patch.js') !== false) {
        echo "   ✅ Patch script include ditemukan\n";
    } else {
        echo "   ❌ Patch script include tidak ditemukan\n";
    }
    
    if (strpos($content, 'window.ALL') !== false) {
        echo "   ✅ Window.ALL definition ditemukan di view\n";
    } else {
        echo "   ⚠️  Window.ALL definition tidak ditemukan di view\n";
    }
    
    if (strpos($content, 'x-data="interOutletSaleApp()"') !== false) {
        echo "   ✅ Alpine.js x-data ditemukan\n";
    } else {
        echo "   ❌ Alpine.js x-data tidak ditemukan\n";
    }
    
    // Check route names
    $correctRoutes = [
        'admin.penjualan.inter-outlet.products',
        'admin.penjualan.inter-outlet.outlets',
        'admin.penjualan.inter-outlet.store'
    ];
    
    $routeErrors = 0;
    foreach ($correctRoutes as $route) {
        if (strpos($content, $route) === false) {
            $routeErrors++;
        }
    }
    
    if ($routeErrors === 0) {
        echo "   ✅ Semua route names sudah benar\n";
    } else {
        echo "   ⚠️  $routeErrors route names mungkin belum benar\n";
    }
    
} else {
    echo "   ❌ File view tidak ditemukan\n";
}

// 4. Test routes
echo "\n4. Memeriksa routes...\n";

if (function_exists('exec')) {
    // Test specific routes
    $testRoutes = [
        'admin.penjualan.inter-outlet.index',
        'admin.penjualan.inter-outlet.products', 
        'admin.penjualan.inter-outlet.outlets'
    ];
    
    foreach ($testRoutes as $route) {
        exec("php artisan route:list --name=$route 2>&1", $output, $return_var);
        if ($return_var === 0 && !empty($output)) {
            $found = false;
            foreach ($output as $line) {
                if (strpos($line, $route) !== false) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                echo "   ✅ Route $route ditemukan\n";
            } else {
                echo "   ❌ Route $route tidak ditemukan\n";
            }
        }
        $output = []; // Reset output
    }
} else {
    echo "   ⚠️  Tidak dapat test routes (exec disabled)\n";
}

// 5. Test controller methods
echo "\n5. Memeriksa controller methods...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $methods = ['getProducts', 'getOutlets', 'store', 'history'];
    foreach ($methods as $method) {
        if (preg_match('/public function ' . $method . '\s*\(/', $content)) {
            echo "   ✅ Method $method ditemukan\n";
        } else {
            echo "   ❌ Method $method tidak ditemukan\n";
        }
    }
    
    // Check for authentication
    if (strpos($content, "middleware('auth')") !== false) {
        echo "   ✅ Auth middleware ditemukan\n";
    } else {
        echo "   ⚠️  Auth middleware tidak ditemukan\n";
    }
    
} else {
    echo "   ❌ Controller tidak ditemukan\n";
}

echo "\n📋 Hasil Test:\n";

$jsOk = file_exists('public/js/inter-outlet.js') && strpos(file_get_contents('public/js/inter-outlet.js'), 'const ALL') !== false;
$patchOk = file_exists('public/js/inter-outlet-patch.js');
$viewOk = file_exists($viewFile) && strpos(file_get_contents($viewFile), 'inter-outlet-patch.js') !== false;
$controllerOk = file_exists($controllerFile);

if ($jsOk && $patchOk && $viewOk && $controllerOk) {
    echo "   ✅ Semua komponen utama OK\n";
    echo "   ✅ Perbaikan berhasil diterapkan\n\n";
    
    echo "🎯 Langkah selanjutnya:\n";
    echo "   1. Buka browser dan akses: /admin/penjualan/inter-outlet\n";
    echo "   2. Buka Developer Tools (F12)\n";
    echo "   3. Periksa Console tab - seharusnya tidak ada error 'ALL is not defined'\n";
    echo "   4. Test dropdown outlet dan produk - seharusnya bisa dimuat\n";
    echo "   5. Jika masih error 404, periksa:\n";
    echo "      - User sudah login\n";
    echo "      - User memiliki akses ke outlet\n";
    echo "      - Database connection OK\n\n";
    
} else {
    echo "   ❌ Ada komponen yang bermasalah:\n";
    if (!$jsOk) echo "      - JavaScript file\n";
    if (!$patchOk) echo "      - Patch file\n";
    if (!$viewOk) echo "      - View file\n";
    if (!$controllerOk) echo "      - Controller file\n";
    echo "\n   🔧 Jalankan ulang fix_inter_outlet_all_error.php\n\n";
}

echo "📁 File backup tersedia dengan suffix .backup.[timestamp]\n";
echo "   Jika ada masalah, restore dari backup tersebut.\n\n";

echo "🔍 Debug tips jika masih ada masalah:\n";
echo "   1. Periksa browser console untuk error JavaScript\n";
echo "   2. Periksa Network tab untuk request yang gagal (404/500)\n";
echo "   3. Periksa Laravel log: storage/logs/laravel.log\n";
echo "   4. Test dengan user superadmin\n";
echo "   5. Pastikan database connection OK\n\n";