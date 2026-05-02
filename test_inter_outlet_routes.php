<?php

/**
 * Test Inter Outlet Routes
 * Memeriksa apakah routes inter-outlet berfungsi dengan benar
 */

echo "🧪 Testing Inter Outlet Routes...\n\n";

// Test route list
echo "1. Memeriksa route list...\n";

if (function_exists('exec')) {
    exec('php artisan route:list --name=inter-outlet 2>&1', $output, $return_var);
    if ($return_var === 0 && !empty($output)) {
        echo "   ✅ Routes ditemukan:\n";
        foreach ($output as $line) {
            if (strpos($line, 'inter-outlet') !== false) {
                echo "      $line\n";
            }
        }
    } else {
        echo "   ❌ Tidak dapat mengambil route list\n";
    }
} else {
    echo "   ⚠️  Exec function tidak tersedia\n";
}

echo "\n2. Memeriksa file routes/web.php...\n";

$routeFile = 'routes/web.php';
if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    
    // Cari section inter-outlet
    if (preg_match('/\/\/ Inter Outlet Sale Routes.*?(?=\/\/|\?>\s*$)/s', $content, $matches)) {
        echo "   ✅ Section Inter Outlet Sale Routes ditemukan\n";
        
        // Hitung jumlah route
        $routeCount = substr_count($matches[0], 'Route::');
        echo "   📊 Jumlah routes: $routeCount\n";
        
        // Cek duplikasi
        $duplicates = [];
        if (preg_match_all('/Route::(get|post|put|delete)\([\'"]([^\'"]+)[\'"].*?->name\([\'"]([^\'"]+)[\'"]\)/', $matches[0], $routeMatches, PREG_SET_ORDER)) {
            $routeNames = [];
            foreach ($routeMatches as $match) {
                $routeName = $match[3];
                if (isset($routeNames[$routeName])) {
                    $duplicates[] = $routeName;
                } else {
                    $routeNames[$routeName] = true;
                }
            }
        }
        
        if (!empty($duplicates)) {
            echo "   ❌ Ditemukan duplikasi route names:\n";
            foreach ($duplicates as $duplicate) {
                echo "      - $duplicate\n";
            }
        } else {
            echo "   ✅ Tidak ada duplikasi route names\n";
        }
        
    } else {
        echo "   ❌ Section Inter Outlet Sale Routes tidak ditemukan\n";
    }
} else {
    echo "   ❌ File routes/web.php tidak ditemukan\n";
}

echo "\n3. Memeriksa controller...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';
if (file_exists($controllerFile)) {
    echo "   ✅ Controller ditemukan\n";
    
    $content = file_get_contents($controllerFile);
    
    // Cek method yang diperlukan
    $requiredMethods = ['index', 'getProducts', 'getOutlets', 'store', 'history'];
    $foundMethods = [];
    
    foreach ($requiredMethods as $method) {
        if (preg_match('/public function ' . $method . '\s*\(/', $content)) {
            $foundMethods[] = $method;
            echo "   ✅ Method $method ditemukan\n";
        } else {
            echo "   ❌ Method $method tidak ditemukan\n";
        }
    }
    
} else {
    echo "   ❌ Controller tidak ditemukan\n";
}

echo "\n4. Memeriksa middleware dan authentication...\n";

// Cek apakah ada middleware yang mungkin memblokir
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    if (strpos($content, "middleware('auth')") !== false) {
        echo "   ✅ Auth middleware ditemukan\n";
    } else {
        echo "   ⚠️  Auth middleware tidak ditemukan\n";
    }
    
    if (strpos($content, 'HasOutletFilter') !== false) {
        echo "   ✅ HasOutletFilter trait ditemukan\n";
    } else {
        echo "   ⚠️  HasOutletFilter trait tidak ditemukan\n";
    }
}

echo "\n5. Memeriksa JavaScript dan view...\n";

$jsFile = 'public/js/inter-outlet.js';
if (file_exists($jsFile)) {
    echo "   ✅ JavaScript file ditemukan\n";
    
    $content = file_get_contents($jsFile);
    
    // Cek apakah ada error syntax
    if (strpos($content, 'interOutletSaleApp') !== false) {
        echo "   ✅ Function interOutletSaleApp ditemukan\n";
    } else {
        echo "   ❌ Function interOutletSaleApp tidak ditemukan\n";
    }
    
    // Cek window assignment
    if (strpos($content, 'window.interOutletSaleApp') !== false) {
        echo "   ✅ Window assignment ditemukan\n";
    } else {
        echo "   ❌ Window assignment tidak ditemukan\n";
    }
    
} else {
    echo "   ❌ JavaScript file tidak ditemukan\n";
}

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($viewFile)) {
    echo "   ✅ View file ditemukan\n";
    
    $content = file_get_contents($viewFile);
    
    // Cek x-data
    if (strpos($content, 'x-data="interOutletSaleApp()"') !== false) {
        echo "   ✅ Alpine.js x-data ditemukan\n";
    } else {
        echo "   ❌ Alpine.js x-data tidak ditemukan atau salah\n";
    }
    
    // Cek route definitions
    if (strpos($content, 'window.routes') !== false) {
        echo "   ✅ Window routes definition ditemukan\n";
    } else {
        echo "   ❌ Window routes definition tidak ditemukan\n";
    }
    
} else {
    echo "   ❌ View file tidak ditemukan\n";
}

echo "\n📋 Kesimpulan:\n";
echo "   Jika ada error 404, kemungkinan penyebab:\n";
echo "   1. Route tidak terdaftar dengan benar\n";
echo "   2. Controller method tidak ada\n";
echo "   3. Middleware memblokir akses\n";
echo "   4. Authentication issue\n";
echo "   5. Outlet access control\n\n";

echo "   Jika ada error 'ALL is not defined':\n";
echo "   1. Periksa JavaScript syntax\n";
echo "   2. Pastikan Alpine.js loaded\n";
echo "   3. Periksa console browser untuk error lain\n\n";

echo "🔧 Langkah perbaikan yang disarankan:\n";
echo "   1. php artisan route:clear\n";
echo "   2. php artisan config:clear\n";
echo "   3. php artisan view:clear\n";
echo "   4. Periksa browser console (F12)\n";
echo "   5. Test dengan user yang memiliki akses outlet\n\n";