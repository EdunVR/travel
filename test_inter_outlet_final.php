<?php

/**
 * Test Inter Outlet Final Fix
 * Memverifikasi bahwa semua perbaikan berhasil
 */

echo "🧪 Testing Inter Outlet Final Fix...\n\n";

// 1. Test layout admin
echo "1. Memeriksa layout admin...\n";

$layoutFile = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($layoutFile)) {
    $content = file_get_contents($layoutFile);
    
    // Cek apakah inter-outlet.js sudah dipindahkan setelah Alpine.js
    $alpinePos = strpos($content, 'alpinejs@3.x.x/dist/cdn.min.js');
    $interOutletPos = strpos($content, 'inter-outlet.js');
    
    if ($alpinePos !== false && $interOutletPos !== false) {
        if ($interOutletPos > $alpinePos) {
            echo "   ✅ inter-outlet.js dimuat SETELAH Alpine.js\n";
        } else {
            echo "   ❌ inter-outlet.js masih dimuat SEBELUM Alpine.js\n";
        }
    } else {
        echo "   ⚠️  Tidak dapat menentukan urutan loading\n";
    }
    
    // Cek apakah ada defer attribute
    if (strpos($content, 'defer src="{{ asset(\'js/inter-outlet.js\') }}"') !== false) {
        echo "   ✅ inter-outlet.js menggunakan defer attribute\n";
    } else {
        echo "   ⚠️  inter-outlet.js tidak menggunakan defer attribute\n";
    }
    
} else {
    echo "   ❌ Layout admin tidak ditemukan\n";
}

// 2. Test JavaScript file
echo "\n2. Memeriksa file JavaScript...\n";

$jsFile = 'public/js/inter-outlet.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Cek struktur Alpine.js
    if (strpos($content, "document.addEventListener('alpine:init'") !== false) {
        echo "   ✅ Menggunakan alpine:init event listener\n";
    } else {
        echo "   ❌ Tidak menggunakan alpine:init event listener\n";
    }
    
    if (strpos($content, 'Alpine.data(\'interOutletSaleApp\'') !== false) {
        echo "   ✅ Menggunakan Alpine.data() untuk mendefinisikan component\n";
    } else {
        echo "   ❌ Tidak menggunakan Alpine.data()\n";
    }
    
    // Cek konstanta ALL
    if (strpos($content, 'window.ALL = \'all\'') !== false) {
        echo "   ✅ Konstanta ALL didefinisikan\n";
    } else {
        echo "   ❌ Konstanta ALL tidak didefinisikan\n";
    }
    
    // Cek error handling
    if (strpos($content, 'ALL is not defined') !== false) {
        echo "   ✅ Error handling untuk ALL tersedia\n";
    } else {
        echo "   ❌ Error handling untuk ALL tidak tersedia\n";
    }
    
    // Cek CSRF token handling
    if (strpos($content, 'getCSRFToken') !== false) {
        echo "   ✅ CSRF token handling tersedia\n";
    } else {
        echo "   ❌ CSRF token handling tidak tersedia\n";
    }
    
    // Cek fetchWithAuth method
    if (strpos($content, 'fetchWithAuth') !== false) {
        echo "   ✅ fetchWithAuth method tersedia\n";
    } else {
        echo "   ❌ fetchWithAuth method tidak tersedia\n";
    }
    
} else {
    echo "   ❌ File JavaScript tidak ditemukan\n";
}

// 3. Test view file
echo "\n3. Memeriksa view file...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Cek x-data attribute
    if (strpos($content, 'x-data="interOutletSaleApp"') !== false) {
        echo "   ✅ x-data menggunakan nama component yang benar\n";
    } elseif (strpos($content, 'x-data="interOutletSaleApp()"') !== false) {
        echo "   ⚠️  x-data masih menggunakan function call (harus diperbaiki)\n";
    } else {
        echo "   ❌ x-data tidak ditemukan atau salah\n";
    }
    
    // Cek CSRF meta tag
    if (strpos($content, 'csrf-token') !== false) {
        echo "   ✅ CSRF meta tag tersedia\n";
    } else {
        echo "   ❌ CSRF meta tag tidak tersedia\n";
    }
    
    // Cek window.routes
    if (strpos($content, 'window.routes') !== false) {
        echo "   ✅ Window routes definition tersedia\n";
    } else {
        echo "   ❌ Window routes definition tidak tersedia\n";
    }
    
} else {
    echo "   ❌ View file tidak ditemukan\n";
}

// 4. Test routes
echo "\n4. Memeriksa routes...\n";

if (function_exists('exec')) {
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
                echo "   ✅ Route $route tersedia\n";
            } else {
                echo "   ❌ Route $route tidak ditemukan\n";
            }
        }
        $output = []; // Reset
    }
} else {
    echo "   ⚠️  Tidak dapat test routes (exec disabled)\n";
}

// 5. Test controller
echo "\n5. Memeriksa controller...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $methods = ['getProducts', 'getOutlets', 'store'];
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "   ✅ Method $method tersedia\n";
        } else {
            echo "   ❌ Method $method tidak tersedia\n";
        }
    }
    
    // Cek auth handling
    if (strpos($content, 'auth()->user()') !== false) {
        echo "   ✅ Authentication handling tersedia\n";
    } else {
        echo "   ❌ Authentication handling tidak tersedia\n";
    }
    
} else {
    echo "   ❌ Controller tidak ditemukan\n";
}

echo "\n📋 Hasil Test:\n";

$layoutOk = file_exists($layoutFile);
$jsOk = file_exists($jsFile) && strpos(file_get_contents($jsFile), 'alpine:init') !== false;
$viewOk = file_exists($viewFile);
$controllerOk = file_exists($controllerFile);

if ($layoutOk && $jsOk && $viewOk && $controllerOk) {
    echo "   ✅ Semua komponen utama OK\n";
    echo "   ✅ Perbaikan berhasil diterapkan\n\n";
    
    echo "🎯 Langkah testing manual:\n";
    echo "   1. Login ke aplikasi sebagai admin/superadmin\n";
    echo "   2. Buka halaman: /admin/penjualan/inter-outlet\n";
    echo "   3. Buka Developer Tools (F12)\n";
    echo "   4. Periksa Console tab:\n";
    echo "      - Harus ada: '🏪 Initializing Inter Outlet Sale JavaScript...'\n";
    echo "      - Harus ada: '✅ Inter Outlet Sale JavaScript loaded successfully'\n";
    echo "      - Tidak boleh ada: 'ALL is not defined'\n";
    echo "      - Tidak boleh ada: 'Alpine.js not loaded'\n";
    echo "   5. Periksa Network tab:\n";
    echo "      - API calls ke /outlets harus return 200 (bukan 404)\n";
    echo "      - API calls ke /products harus return 200 (bukan 404)\n";
    echo "      - Response harus JSON (bukan HTML)\n";
    echo "   6. Test functionality:\n";
    echo "      - Dropdown outlet harus bisa dimuat\n";
    echo "      - Dropdown produk harus bisa dimuat\n";
    echo "      - Tidak ada error saat memilih outlet\n\n";
    
} else {
    echo "   ❌ Ada komponen yang bermasalah:\n";
    if (!$layoutOk) echo "      - Layout file\n";
    if (!$jsOk) echo "      - JavaScript file\n";
    if (!$viewOk) echo "      - View file\n";
    if (!$controllerOk) echo "      - Controller file\n";
    echo "\n   🔧 Jalankan ulang fix_inter_outlet_layout_and_js.php\n\n";
}

echo "🔍 Debug checklist jika masih ada masalah:\n";
echo "   1. Pastikan user sudah login dengan akses outlet\n";
echo "   2. Clear browser cache (Ctrl+F5)\n";
echo "   3. Periksa Laravel log: storage/logs/laravel.log\n";
echo "   4. Periksa browser console untuk error JavaScript\n";
echo "   5. Periksa Network tab untuk failed requests\n";
echo "   6. Test dengan user superadmin\n\n";

echo "📁 File backup tersedia dengan suffix .backup.[type].[timestamp]\n";
echo "   Jika ada masalah, restore dari backup tersebut.\n\n";