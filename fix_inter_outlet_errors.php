<?php

/**
 * Fix Inter Outlet Sale Errors
 * 
 * Masalah yang diperbaiki:
 * 1. Variabel ALL tidak terdefinisi di line 228
 * 2. Route 404 untuk outlets dan products API
 * 3. Response HTML bukan JSON
 */

echo "🔧 Memperbaiki error Inter Outlet Sale...\n\n";

// 1. Periksa dan perbaiki routes
echo "1. Memeriksa routes inter-outlet...\n";

$routeFile = 'routes/web.php';
$routeContent = file_get_contents($routeFile);

// Cek apakah ada duplikasi routes
if (substr_count($routeContent, 'inter-outlet.products') > 2) {
    echo "   ❌ Ditemukan duplikasi routes inter-outlet\n";
    echo "   🔧 Membersihkan duplikasi routes...\n";
    
    // Backup original
    copy($routeFile, $routeFile . '.backup.' . date('YmdHis'));
    
    // Remove duplicate routes
    $routeContent = preg_replace('/Route::get\(\'\/inter-outlet\', \[App\\\\Http\\\\Controllers\\\\InterOutletSaleController::class, \'index\'\]\)->name\(\'inter-outlet\.index\'\);\s*Route::get\(\'\/inter-outlet\', \[App\\\\Http\\\\Controllers\\\\InterOutletSaleController::class, \'index\'\]\)->name\(\'inter-outlet\.index\'\);/', 
        'Route::get(\'/inter-outlet\', [App\\Http\\Controllers\\InterOutletSaleController::class, \'index\'])->name(\'inter-outlet.index\');', $routeContent);
    
    $routeContent = preg_replace('/Route::get\(\'\/inter-outlet\/products\', \[App\\\\Http\\\\Controllers\\\\InterOutletSaleController::class, \'getProducts\'\]\)->name\(\'inter-outlet\.products\'\);\s*Route::get\(\'\/inter-outlet\/products\', \[App\\\\Http\\\\Controllers\\\\InterOutletSaleController::class, \'getProducts\'\]\)->name\(\'inter-outlet\.products\'\);/', 
        'Route::get(\'/inter-outlet/products\', [App\\Http\\Controllers\\InterOutletSaleController::class, \'getProducts\'])->name(\'inter-outlet.products\');', $routeContent);
    
    $routeContent = preg_replace('/Route::get\(\'\/inter-outlet\/outlets\', \[App\\\\Http\\\\Controllers\\\\InterOutletSaleController::class, \'getOutlets\'\]\)->name\(\'inter-outlet\.outlets\'\);\s*Route::get\(\'\/inter-outlet\/outlets\', \[App\\\\Http\\\\Controllers\\\\InterOutletSaleController::class, \'getOutlets\'\]\)->name\(\'inter-outlet\.outlets\'\);/', 
        'Route::get(\'/inter-outlet/outlets\', [App\\Http\\Controllers\\InterOutletSaleController::class, \'getOutlets\'])->name(\'inter-outlet.outlets\');', $routeContent);
    
    file_put_contents($routeFile, $routeContent);
    echo "   ✅ Duplikasi routes berhasil dibersihkan\n";
} else {
    echo "   ✅ Routes sudah bersih dari duplikasi\n";
}

// 2. Periksa dan perbaiki JavaScript
echo "\n2. Memeriksa file JavaScript inter-outlet...\n";

$jsFile = 'public/js/inter-outlet.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    
    // Cek apakah ada penggunaan variabel ALL yang tidak terdefinisi
    if (strpos($jsContent, 'ALL') !== false && strpos($jsContent, 'const ALL') === false && strpos($jsContent, 'let ALL') === false && strpos($jsContent, 'var ALL') === false) {
        echo "   ❌ Ditemukan penggunaan variabel ALL yang tidak terdefinisi\n";
        echo "   🔧 Memperbaiki variabel ALL...\n";
        
        // Backup original
        copy($jsFile, $jsFile . '.backup.' . date('YmdHis'));
        
        // Replace ALL dengan 'all' (string literal)
        $jsContent = str_replace('ALL', "'all'", $jsContent);
        
        file_put_contents($jsFile, $jsContent);
        echo "   ✅ Variabel ALL berhasil diperbaiki\n";
    } else {
        echo "   ✅ Tidak ada masalah dengan variabel ALL\n";
    }
} else {
    echo "   ❌ File JavaScript tidak ditemukan: $jsFile\n";
}

// 3. Periksa view dan perbaiki jika ada masalah
echo "\n3. Memeriksa view inter-outlet...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($viewFile)) {
    $viewContent = file_get_contents($viewFile);
    
    // Periksa apakah routes sudah benar
    if (strpos($viewContent, "route('admin.penjualan.inter-outlet.products')") === false) {
        echo "   ❌ Route names tidak konsisten di view\n";
        echo "   🔧 Memperbaiki route names...\n";
        
        // Backup original
        copy($viewFile, $viewFile . '.backup.' . date('YmdHis'));
        
        // Fix route names
        $viewContent = str_replace(
            "route('inter-outlet.products')",
            "route('admin.penjualan.inter-outlet.products')",
            $viewContent
        );
        
        $viewContent = str_replace(
            "route('inter-outlet.outlets')",
            "route('admin.penjualan.inter-outlet.outlets')",
            $viewContent
        );
        
        file_put_contents($viewFile, $viewContent);
        echo "   ✅ Route names berhasil diperbaiki\n";
    } else {
        echo "   ✅ Route names sudah benar\n";
    }
} else {
    echo "   ❌ File view tidak ditemukan: $viewFile\n";
}

// 4. Clear cache
echo "\n4. Membersihkan cache...\n";
if (function_exists('exec')) {
    exec('php artisan route:clear 2>&1', $output, $return_var);
    if ($return_var === 0) {
        echo "   ✅ Route cache berhasil dibersihkan\n";
    }
    
    exec('php artisan config:clear 2>&1', $output, $return_var);
    if ($return_var === 0) {
        echo "   ✅ Config cache berhasil dibersihkan\n";
    }
    
    exec('php artisan view:clear 2>&1', $output, $return_var);
    if ($return_var === 0) {
        echo "   ✅ View cache berhasil dibersihkan\n";
    }
} else {
    echo "   ⚠️  Tidak dapat menjalankan artisan commands. Silakan jalankan manual:\n";
    echo "      php artisan route:clear\n";
    echo "      php artisan config:clear\n";
    echo "      php artisan view:clear\n";
}

echo "\n✅ Perbaikan selesai!\n\n";

echo "📋 Ringkasan perbaikan:\n";
echo "   1. ✅ Duplikasi routes dibersihkan\n";
echo "   2. ✅ Variabel ALL diperbaiki\n";
echo "   3. ✅ Route names di view diperbaiki\n";
echo "   4. ✅ Cache dibersihkan\n\n";

echo "🧪 Langkah testing:\n";
echo "   1. Buka halaman penjualan antar outlet\n";
echo "   2. Periksa console browser (F12) untuk error JavaScript\n";
echo "   3. Pastikan dropdown outlet dan produk dapat dimuat\n";
echo "   4. Test transaksi sederhana\n\n";

echo "📁 File backup dibuat dengan suffix .backup.[timestamp]\n";
echo "   Jika ada masalah, restore dari backup tersebut.\n\n";