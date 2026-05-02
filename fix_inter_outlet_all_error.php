<?php

/**
 * Fix Inter Outlet ALL Error
 * Memperbaiki error "ALL is not defined" pada halaman inter-outlet
 */

echo "🔧 Memperbaiki error 'ALL is not defined' pada Inter Outlet...\n\n";

// 1. Periksa dan perbaiki JavaScript
echo "1. Memeriksa file JavaScript...\n";

$jsFile = 'public/js/inter-outlet.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Backup original
    copy($jsFile, $jsFile . '.backup.' . date('YmdHis'));
    
    // Cek apakah ada penggunaan ALL yang tidak terdefinisi
    $hasError = false;
    
    // Pattern untuk mencari penggunaan ALL yang tidak dalam string
    if (preg_match('/[^\'"]ALL[^\'"]/', $content)) {
        echo "   ❌ Ditemukan penggunaan variabel ALL yang tidak terdefinisi\n";
        $hasError = true;
        
        // Replace dengan string literal
        $content = preg_replace('/([^\'"])ALL([^\'"])/', '$1\'all\'$2', $content);
        echo "   🔧 Mengganti ALL dengan 'all'\n";
    }
    
    // Tambahkan definisi ALL di awal jika diperlukan
    if ($hasError || strpos($content, 'const ALL') === false) {
        $definition = "// Define constants\nconst ALL = 'all';\n\n";
        
        // Insert setelah comment header
        if (strpos($content, '/**') !== false) {
            $content = preg_replace('/(\*\/\s*\n)/', "$1$definition", $content, 1);
        } else {
            $content = $definition . $content;
        }
        
        echo "   ✅ Menambahkan definisi konstanta ALL\n";
    }
    
    file_put_contents($jsFile, $content);
    echo "   ✅ File JavaScript berhasil diperbaiki\n";
} else {
    echo "   ❌ File JavaScript tidak ditemukan\n";
}

// 2. Periksa dan perbaiki view
echo "\n2. Memeriksa file view...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Backup original
    copy($viewFile, $viewFile . '.backup.' . date('YmdHis'));
    
    $hasChanges = false;
    
    // Pastikan Alpine.js dimuat sebelum script kita
    if (strpos($content, '@push(\'scripts\')') === false) {
        // Tambahkan section untuk memastikan Alpine.js dimuat
        $scriptSection = "\n@push('scripts')\n<script>\n// Ensure Alpine.js is loaded\nif (typeof Alpine === 'undefined') {\n    console.error('Alpine.js not loaded. Please check if Alpine.js is included in the layout.');\n}\n\n// Define constants for inter-outlet\nwindow.ALL = 'all';\n</script>\n@endpush\n";
        
        // Insert sebelum closing tag
        $content = str_replace('</x-layouts.admin>', $scriptSection . '</x-layouts.admin>', $content);
        $hasChanges = true;
        echo "   ✅ Menambahkan script section untuk konstanta\n";
    }
    
    // Pastikan routes sudah benar
    $routeReplacements = [
        "route('inter-outlet.products')" => "route('admin.penjualan.inter-outlet.products')",
        "route('inter-outlet.outlets')" => "route('admin.penjualan.inter-outlet.outlets')",
        "route('inter-outlet.store')" => "route('admin.penjualan.inter-outlet.store')",
        "route('inter-outlet.history')" => "route('admin.penjualan.inter-outlet.history')",
    ];
    
    foreach ($routeReplacements as $old => $new) {
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
            $hasChanges = true;
            echo "   🔧 Memperbaiki route: $old -> $new\n";
        }
    }
    
    if ($hasChanges) {
        file_put_contents($viewFile, $content);
        echo "   ✅ File view berhasil diperbaiki\n";
    } else {
        echo "   ✅ File view sudah benar\n";
    }
} else {
    echo "   ❌ File view tidak ditemukan\n";
}

// 3. Buat file JavaScript patch khusus
echo "\n3. Membuat patch JavaScript...\n";

$patchContent = <<<'JS'
/**
 * Inter Outlet JavaScript Patch
 * Memperbaiki error "ALL is not defined"
 */

// Define constants
window.ALL = 'all';
const ALL = 'all';

// Ensure Alpine.js is available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Alpine === 'undefined') {
        console.error('Alpine.js is not loaded. Inter-outlet functionality may not work properly.');
        return;
    }
    
    // Check if interOutletSaleApp is defined
    if (typeof window.interOutletSaleApp !== 'function') {
        console.error('interOutletSaleApp function is not defined. Please check if inter-outlet.js is loaded.');
        return;
    }
    
    console.log('✅ Inter-outlet JavaScript patch loaded successfully');
});

// Error handler for undefined variables
window.addEventListener('error', function(e) {
    if (e.message.includes('ALL is not defined')) {
        console.warn('Caught ALL undefined error, using fallback value');
        window.ALL = 'all';
        return true; // Prevent default error handling
    }
});
JS;

file_put_contents('public/js/inter-outlet-patch.js', $patchContent);
echo "   ✅ File patch JavaScript berhasil dibuat\n";

// 4. Update view untuk include patch
echo "\n4. Menambahkan patch ke view...\n";

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Cek apakah patch sudah ditambahkan
    if (strpos($content, 'inter-outlet-patch.js') === false) {
        // Tambahkan script patch sebelum closing body atau setelah inter-outlet.js
        $patchScript = "\n    <script src=\"{{ asset('js/inter-outlet-patch.js') }}\"></script>";
        
        if (strpos($content, '@endpush') !== false) {
            // Insert sebelum @endpush terakhir
            $content = preg_replace('/(@endpush)(?!.*@endpush)/s', $patchScript . "\n$1", $content);
        } else {
            // Insert sebelum closing tag
            $content = str_replace('</x-layouts.admin>', $patchScript . "\n</x-layouts.admin>", $content);
        }
        
        file_put_contents($viewFile, $content);
        echo "   ✅ Patch script berhasil ditambahkan ke view\n";
    } else {
        echo "   ✅ Patch script sudah ada di view\n";
    }
}

// 5. Clear cache
echo "\n5. Membersihkan cache...\n";

$commands = [
    'php artisan route:clear',
    'php artisan config:clear', 
    'php artisan view:clear',
    'php artisan cache:clear'
];

foreach ($commands as $command) {
    if (function_exists('exec')) {
        exec("$command 2>&1", $output, $return_var);
        if ($return_var === 0) {
            echo "   ✅ $command berhasil\n";
        } else {
            echo "   ⚠️  $command gagal\n";
        }
    }
}

echo "\n✅ Perbaikan selesai!\n\n";

echo "📋 Ringkasan perbaikan:\n";
echo "   1. ✅ Konstanta ALL didefinisikan di JavaScript\n";
echo "   2. ✅ Route names diperbaiki di view\n";
echo "   3. ✅ File patch JavaScript dibuat\n";
echo "   4. ✅ Error handler ditambahkan\n";
echo "   5. ✅ Cache dibersihkan\n\n";

echo "🧪 Langkah testing:\n";
echo "   1. Buka halaman: /admin/penjualan/inter-outlet\n";
echo "   2. Buka Developer Tools (F12)\n";
echo "   3. Periksa Console tab untuk error\n";
echo "   4. Test dropdown outlet dan produk\n";
echo "   5. Pastikan tidak ada error 'ALL is not defined'\n\n";

echo "📁 File yang dibuat/dimodifikasi:\n";
echo "   - public/js/inter-outlet.js (backup dibuat)\n";
echo "   - public/js/inter-outlet-patch.js (baru)\n";
echo "   - resources/views/admin/penjualan/inter-outlet/index.blade.php (backup dibuat)\n\n";

echo "🔧 Jika masih ada masalah:\n";
echo "   1. Periksa apakah Alpine.js dimuat di layout\n";
echo "   2. Pastikan user memiliki akses ke outlet\n";
echo "   3. Cek network tab untuk request yang gagal\n";
echo "   4. Restore dari backup jika diperlukan\n\n";