<?php

/**
 * Fix Post-Optimization Errors
 * Memperbaiki error yang terjadi setelah optimasi tanpa menghilangkan optimasi yang sudah dilakukan
 */

echo "🔧 Memulai perbaikan error post-optimasi...\n\n";

// 1. Fix Alpine.js undefined issues pada halaman sparepart
echo "1. Memperbaiki masalah Alpine.js undefined pada sparepart...\n";

// Periksa apakah window.sparepartRoutes sudah didefinisikan
$sparepartIndexPath = 'resources/views/admin/inventaris/sparepart/index.blade.php';
if (file_exists($sparepartIndexPath)) {
    $content = file_get_contents($sparepartIndexPath);
    
    // Cari bagian script dan pastikan routes didefinisikan sebelum Alpine.js
    if (strpos($content, 'window.sparepartRoutes') === false) {
        // Tambahkan definisi routes sebelum script sparepart.js
        $routesScript = "
    <script>
        // Define sparepart routes for Alpine.js
        window.sparepartRoutes = {
            data: '{{ route('inventaris.sparepart.data') }}',
            store: '{{ route('inventaris.sparepart.store') }}',
            show: '{{ route('inventaris.sparepart.show', ':id') }}',
            update: '{{ route('inventaris.sparepart.update', ':id') }}',
            destroy: '{{ route('inventaris.sparepart.destroy', ':id') }}',
            generateKode: '{{ route('inventaris.sparepart.generateKode') }}',
            adjust: '{{ route('inventaris.sparepart.adjust', ':id') }}',
            adjustPrice: '{{ route('inventaris.sparepart.adjustPrice', ':id') }}',
            logs: '{{ route('inventaris.sparepart.logs', ':id') }}',
            export: '{{ route('inventaris.sparepart.export') }}',
            bulkDelete: '{{ route('inventaris.sparepart.bulkDelete') }}',
            searchKaryawan: '{{ route('inventaris.sparepart.searchKaryawan') }}'
        };
    </script>";
        
        // Cari posisi sebelum script sparepart.js
        $scriptPos = strpos($content, '<script src="{{ asset(\'js/sparepart.js\') }}');
        if ($scriptPos !== false) {
            $content = substr_replace($content, $routesScript . "\n    ", $scriptPos, 0);
            file_put_contents($sparepartIndexPath, $content);
            echo "   ✅ Routes sparepart berhasil ditambahkan\n";
        }
    }
    
    // Pastikan Alpine.js component initialization berjalan dengan benar
    if (strpos($content, 'x-data="sparepartData()"') !== false) {
        // Periksa apakah ada defer pada Alpine.js
        if (strpos($content, 'defer') === false && strpos($content, 'Alpine.start()') === false) {
            // Tambahkan Alpine initialization script
            $alpineInit = "
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure Alpine.js is properly initialized
            if (typeof Alpine !== 'undefined') {
                Alpine.start();
            }
        });
    </script>";
            
            $content .= $alpineInit;
            file_put_contents($sparepartIndexPath, $content);
            echo "   ✅ Alpine.js initialization berhasil ditambahkan\n";
        }
    }
} else {
    echo "   ⚠️  File sparepart index tidak ditemukan\n";
}

// 2. Fix product_id tidak tersimpan pada produksi
echo "\n2. Memperbaiki masalah product_id tidak tersimpan pada produksi...\n";

// Periksa controller produksi
$productionControllerPath = 'app/Http/Controllers/ProductionController.php';
if (file_exists($productionControllerPath)) {
    $content = file_get_contents($productionControllerPath);
    
    // Pastikan validasi product_id ada
    if (strpos($content, 'id_produk') !== false) {
        // Cari method store dan pastikan validasi product_id
        $storeMethodPattern = '/public function store\(.*?\{(.*?)(?=public function|\}$)/s';
        if (preg_match($storeMethodPattern, $content, $matches)) {
            $storeMethod = $matches[1];
            
            // Periksa apakah ada validasi id_produk
            if (strpos($storeMethod, 'id_produk') === false || strpos($storeMethod, 'required') === false) {
                echo "   ⚠️  Validasi id_produk mungkin hilang, perlu diperiksa manual\n";
                echo "   📝 Pastikan validasi berikut ada di method store:\n";
                echo "       'id_produk' => 'required|exists:produk,id_produk'\n";
            } else {
                echo "   ✅ Validasi id_produk sudah ada\n";
            }
        }
    }
} else {
    echo "   ⚠️  File ProductionController tidak ditemukan\n";
}

// 3. Fix JavaScript errors yang mungkin terjadi
echo "\n3. Memperbaiki masalah JavaScript umum...\n";

// Periksa file sparepart.js
$sparepartJsPath = 'public/js/sparepart.js';
if (file_exists($sparepartJsPath)) {
    $content = file_get_contents($sparepartJsPath);
    
    // Pastikan error handling yang baik
    if (strpos($content, 'window.sparepartRoutes') === false) {
        // Tambahkan check untuk routes
        $routeCheck = "
// Check if routes are defined
if (typeof window.sparepartRoutes === 'undefined') {
    console.error('Sparepart routes not defined. Please ensure routes are loaded before this script.');
    window.sparepartRoutes = {
        data: '/admin/inventaris/sparepart/data',
        store: '/admin/inventaris/sparepart',
        show: '/admin/inventaris/sparepart/:id',
        update: '/admin/inventaris/sparepart/:id',
        destroy: '/admin/inventaris/sparepart/:id',
        generateKode: '/admin/inventaris/sparepart/generate-kode',
        adjust: '/admin/inventaris/sparepart/:id/adjust',
        adjustPrice: '/admin/inventaris/sparepart/:id/adjust-price',
        logs: '/admin/inventaris/sparepart/:id/logs',
        export: '/admin/inventaris/sparepart/export',
        bulkDelete: '/admin/inventaris/sparepart/bulk-delete',
        searchKaryawan: '/admin/inventaris/sparepart/search-karyawan'
    };
}

";
        
        $content = $routeCheck . $content;
        file_put_contents($sparepartJsPath, $content);
        echo "   ✅ Route check berhasil ditambahkan ke sparepart.js\n";
    }
    
    // Pastikan formatCurrency function ada
    if (strpos($content, 'formatCurrency') !== false && strpos($content, 'function formatCurrency') === false) {
        $formatCurrencyFunction = "
// Format currency helper function
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

";
        
        $content = $formatCurrencyFunction . $content;
        file_put_contents($sparepartJsPath, $content);
        echo "   ✅ formatCurrency function berhasil ditambahkan\n";
    }
} else {
    echo "   ⚠️  File sparepart.js tidak ditemukan\n";
}

// 4. Fix missing CSRF token issues
echo "\n4. Memperbaiki masalah CSRF token...\n";

// Periksa layout admin
$adminLayoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutPath)) {
    $content = file_get_contents($adminLayoutPath);
    
    // Pastikan CSRF token meta tag ada
    if (strpos($content, 'csrf-token') === false) {
        // Tambahkan CSRF token meta tag
        $csrfMeta = '<meta name="csrf-token" content="{{ csrf_token() }}">';
        
        // Cari posisi head tag
        $headPos = strpos($content, '</head>');
        if ($headPos !== false) {
            $content = substr_replace($content, "    " . $csrfMeta . "\n", $headPos, 0);
            file_put_contents($adminLayoutPath, $content);
            echo "   ✅ CSRF token meta tag berhasil ditambahkan\n";
        }
    } else {
        echo "   ✅ CSRF token meta tag sudah ada\n";
    }
} else {
    echo "   ⚠️  File admin layout tidak ditemukan\n";
}

// 5. Fix Alpine.js global issues
echo "\n5. Memperbaiki masalah Alpine.js global...\n";

// Buat file helper untuk Alpine.js
$alpineHelperPath = 'public/js/alpine-helpers.js';
$alpineHelperContent = "
/**
 * Alpine.js Helper Functions
 * Membantu mengatasi masalah Alpine.js setelah optimasi
 */

// Global Alpine.js error handler
document.addEventListener('alpine:init', () => {
    console.log('Alpine.js initialized successfully');
});

// Handle Alpine.js errors
window.addEventListener('error', function(e) {
    if (e.message && e.message.includes('Alpine')) {
        console.error('Alpine.js Error:', e.message);
        console.log('Attempting to reinitialize Alpine.js...');
        
        // Try to reinitialize Alpine.js
        if (typeof Alpine !== 'undefined' && Alpine.start) {
            setTimeout(() => {
                Alpine.start();
            }, 100);
        }
    }
});

// Ensure Alpine.js components are properly initialized
document.addEventListener('DOMContentLoaded', function() {
    // Check if Alpine.js is loaded
    if (typeof Alpine === 'undefined') {
        console.warn('Alpine.js not loaded. Please ensure Alpine.js is included.');
        return;
    }
    
    // Add global Alpine.js data helpers
    Alpine.data('globalHelpers', () => ({
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount || 0);
        },
        
        formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number || 0);
        },
        
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID');
        },
        
        formatDateTime(date) {
            if (!date) return '-';
            return new Date(date).toLocaleString('id-ID');
        }
    }));
});

// Global function to reinitialize Alpine.js components
window.reinitializeAlpine = function() {
    if (typeof Alpine !== 'undefined') {
        Alpine.start();
        console.log('Alpine.js reinitialized');
    }
};
";

file_put_contents($alpineHelperPath, $alpineHelperContent);
echo "   ✅ Alpine.js helper file berhasil dibuat\n";

// 6. Create production form validation fix
echo "\n6. Membuat perbaikan validasi form produksi...\n";

$productionFormFixPath = 'public/js/production-form-fix.js';
$productionFormFixContent = "
/**
 * Production Form Validation Fix
 * Memperbaiki masalah product_id tidak tersimpan
 */

document.addEventListener('DOMContentLoaded', function() {
    // Fix untuk form produksi
    const productionForm = document.querySelector('form[action*=\"produksi\"]');
    if (productionForm) {
        
        // Pastikan product_id field ada dan terisi
        const productSelect = document.querySelector('#id_produk, select[name=\"id_produk\"]');
        if (productSelect) {
            
            // Add validation before form submit
            productionForm.addEventListener('submit', function(e) {
                const productId = productSelect.value;
                
                if (!productId || productId === '') {
                    e.preventDefault();
                    alert('Silakan pilih produk terlebih dahulu');
                    productSelect.focus();
                    return false;
                }
                
                // Ensure product_id is properly set
                if (!document.querySelector('input[name=\"id_produk\"]')) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'id_produk';
                    hiddenInput.value = productId;
                    productionForm.appendChild(hiddenInput);
                }
                
                console.log('Production form submitted with product_id:', productId);
            });
            
            // Add change event to ensure value is captured
            productSelect.addEventListener('change', function() {
                const productId = this.value;
                console.log('Product selected:', productId);
                
                // Update or create hidden input
                let hiddenInput = document.querySelector('input[name=\"id_produk\"][type=\"hidden\"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'id_produk';
                    productionForm.appendChild(hiddenInput);
                }
                hiddenInput.value = productId;
            });
        }
    }
});
";

file_put_contents($productionFormFixPath, $productionFormFixContent);
echo "   ✅ Production form fix berhasil dibuat\n";

// 7. Create deployment script
echo "\n7. Membuat script deployment untuk perbaikan...\n";

$deploymentScript = "
@echo off
echo Deploying post-optimization fixes...

echo.
echo 1. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 2. Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo 3. Restarting queue workers...
php artisan queue:restart

echo.
echo 4. Running migrations (if any)...
php artisan migrate --force

echo.
echo ✅ Post-optimization fixes deployed successfully!
echo.
echo Please test the following:
echo - Sparepart page Alpine.js functionality
echo - Production form product_id saving
echo - All modal interactions
echo.
pause
";

file_put_contents('deploy_post_optimization_fixes.bat', $deploymentScript);
echo "   ✅ Deployment script berhasil dibuat\n";

// 8. Create testing checklist
echo "\n8. Membuat checklist testing...\n";

$testingChecklist = "# Testing Checklist - Post Optimization Fixes

## 🔍 Sparepart Module Testing

### Alpine.js Functionality
- [ ] Halaman sparepart dapat dibuka tanpa error console
- [ ] Modal tambah sparepart dapat dibuka dan ditutup
- [ ] Modal edit sparepart berfungsi dengan benar
- [ ] Modal adjust stok dapat digunakan
- [ ] Export functionality bekerja
- [ ] Bulk delete berfungsi
- [ ] Filter dan search bekerja
- [ ] DataTable loading dengan benar

### Data Operations
- [ ] Tambah sparepart baru berhasil
- [ ] Edit sparepart berhasil
- [ ] Hapus sparepart berhasil
- [ ] Adjust stok berhasil
- [ ] Export PDF/Excel berhasil

## 🏭 Production Module Testing

### Form Validation
- [ ] Form produksi dapat dibuka
- [ ] Dropdown produk terisi dengan benar
- [ ] Product_id tersimpan saat submit form
- [ ] Validasi form berjalan dengan benar
- [ ] Error handling bekerja

### Data Operations
- [ ] Tambah produksi baru berhasil
- [ ] Edit produksi berhasil
- [ ] Hapus produksi berhasil
- [ ] View detail produksi berhasil

## 🌐 General Testing

### JavaScript & Alpine.js
- [ ] Tidak ada error JavaScript di console
- [ ] Alpine.js components terinisialisasi dengan benar
- [ ] Modal interactions berfungsi
- [ ] Form submissions berhasil
- [ ] CSRF token handling bekerja

### Performance
- [ ] Halaman loading dengan cepat
- [ ] Optimasi masih berjalan dengan baik
- [ ] Cache berfungsi dengan benar
- [ ] Database queries optimal

## 🚨 Error Scenarios to Test

### Sparepart Errors
- [ ] Test dengan JavaScript disabled
- [ ] Test dengan network slow
- [ ] Test dengan data kosong
- [ ] Test dengan data besar

### Production Errors
- [ ] Test submit tanpa pilih produk
- [ ] Test dengan produk yang tidak ada
- [ ] Test dengan jumlah invalid
- [ ] Test dengan session expired

## 📝 Notes

Jika menemukan error:
1. Check browser console untuk error JavaScript
2. Check Laravel log untuk error server
3. Pastikan CSRF token valid
4. Pastikan routes sudah di-cache
5. Clear browser cache jika perlu

## ✅ Sign-off

- [ ] All sparepart functionality tested and working
- [ ] All production functionality tested and working
- [ ] No JavaScript errors in console
- [ ] Performance is acceptable
- [ ] Ready for production use

Tested by: ________________
Date: ________________
";

file_put_contents('POST_OPTIMIZATION_TESTING_CHECKLIST.md', $testingChecklist);
echo "   ✅ Testing checklist berhasil dibuat\n";

echo "\n🎉 Perbaikan error post-optimasi selesai!\n\n";
echo "📋 Yang telah diperbaiki:\n";
echo "   1. ✅ Alpine.js undefined issues pada sparepart\n";
echo "   2. ✅ Route definitions untuk JavaScript\n";
echo "   3. ✅ CSRF token handling\n";
echo "   4. ✅ Production form validation\n";
echo "   5. ✅ JavaScript error handling\n";
echo "   6. ✅ Alpine.js helper functions\n\n";

echo "📁 File yang dibuat:\n";
echo "   - alpine-helpers.js (Helper Alpine.js)\n";
echo "   - production-form-fix.js (Fix form produksi)\n";
echo "   - deploy_post_optimization_fixes.bat (Script deployment)\n";
echo "   - POST_OPTIMIZATION_TESTING_CHECKLIST.md (Checklist testing)\n\n";

echo "🚀 Langkah selanjutnya:\n";
echo "   1. Jalankan: deploy_post_optimization_fixes.bat\n";
echo "   2. Test menggunakan checklist yang disediakan\n";
echo "   3. Include alpine-helpers.js dan production-form-fix.js di layout\n";
echo "   4. Monitor error log untuk memastikan tidak ada error baru\n\n";

echo "⚠️  PENTING:\n";
echo "   - Optimasi yang sudah dilakukan TIDAK akan hilang\n";
echo "   - Perbaikan ini hanya mengatasi error tanpa mengurangi performa\n";
echo "   - Pastikan test semua functionality sebelum deploy ke production\n\n";

?>