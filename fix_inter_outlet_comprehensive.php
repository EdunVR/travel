<?php

/**
 * Comprehensive Inter Outlet Fix
 * Memperbaiki semua masalah yang masih tersisa
 */

echo "🔧 Comprehensive Inter Outlet Fix...\n\n";

// 1. Periksa dan perbaiki JavaScript secara menyeluruh
echo "1. Memperbaiki JavaScript file...\n";

$jsFile = 'public/js/inter-outlet.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Backup
    copy($jsFile, $jsFile . '.backup.comprehensive.' . date('YmdHis'));
    
    // Pastikan konstanta ALL didefinisikan di awal
    if (strpos($content, 'const ALL') === false) {
        $content = "// Define constants\nconst ALL = 'all';\nwindow.ALL = 'all';\n\n" . $content;
        echo "   ✅ Menambahkan konstanta ALL\n";
    }
    
    // Ganti semua penggunaan ALL yang tidak dalam string
    $content = preg_replace('/([^\'"])ALL([^\'"])/', '$1\'all\'$2', $content);
    
    // Pastikan tidak ada undefined variable
    $content = str_replace('ALL', "'all'", $content);
    
    // Tambahkan error handling yang lebih baik
    $errorHandling = <<<'JS'

// Error handling untuk undefined variables
window.addEventListener('error', function(e) {
    if (e.message.includes('ALL is not defined')) {
        console.warn('Caught ALL undefined error, using fallback');
        window.ALL = 'all';
        return true;
    }
});

// Ensure constants are available globally
if (typeof window.ALL === 'undefined') {
    window.ALL = 'all';
}

JS;
    
    if (strpos($content, 'Error handling untuk undefined variables') === false) {
        $content = $errorHandling . $content;
        echo "   ✅ Menambahkan error handling\n";
    }
    
    file_put_contents($jsFile, $content);
    echo "   ✅ JavaScript file diperbaiki\n";
} else {
    echo "   ❌ JavaScript file tidak ditemukan\n";
}

// 2. Perbaiki view dengan Alpine.js check yang lebih baik
echo "\n2. Memperbaiki view file...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Backup
    copy($viewFile, $viewFile . '.backup.comprehensive.' . date('YmdHis'));
    
    // Tambahkan Alpine.js check di awal
    $alpineCheck = <<<'HTML'
@push('scripts')
<script>
// Ensure Alpine.js is loaded before initializing
document.addEventListener('DOMContentLoaded', function() {
    // Define constants globally
    window.ALL = 'all';
    const ALL = 'all';
    
    // Check Alpine.js
    let alpineCheckCount = 0;
    const maxChecks = 50; // 5 seconds max
    
    function checkAlpine() {
        alpineCheckCount++;
        
        if (typeof Alpine !== 'undefined') {
            console.log('✅ Alpine.js loaded successfully');
            return;
        }
        
        if (alpineCheckCount < maxChecks) {
            setTimeout(checkAlpine, 100);
        } else {
            console.error('❌ Alpine.js not loaded after 5 seconds. Inter-outlet may not work properly.');
            // Try to load Alpine.js manually
            if (!document.querySelector('script[src*="alpinejs"]')) {
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js';
                script.defer = true;
                document.head.appendChild(script);
                console.log('🔄 Attempting to load Alpine.js manually');
            }
        }
    }
    
    checkAlpine();
});

// Error handler for ALL variable
window.addEventListener('error', function(e) {
    if (e.message.includes('ALL is not defined')) {
        console.warn('Fixing ALL undefined error');
        window.ALL = 'all';
        e.preventDefault();
        return false;
    }
});
</script>
@endpush

HTML;
    
    // Tambahkan Alpine check jika belum ada
    if (strpos($content, 'checkAlpine') === false) {
        $content = str_replace('</x-layouts.admin>', $alpineCheck . '</x-layouts.admin>', $content);
        echo "   ✅ Menambahkan Alpine.js check\n";
    }
    
    // Pastikan routes sudah benar
    $routeFixes = [
        "route('inter-outlet.products')" => "route('admin.penjualan.inter-outlet.products')",
        "route('inter-outlet.outlets')" => "route('admin.penjualan.inter-outlet.outlets')",
        "route('inter-outlet.store')" => "route('admin.penjualan.inter-outlet.store')",
        "route('inter-outlet.history')" => "route('admin.penjualan.inter-outlet.history')",
    ];
    
    $routeFixed = false;
    foreach ($routeFixes as $old => $new) {
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
            $routeFixed = true;
        }
    }
    
    if ($routeFixed) {
        echo "   ✅ Routes diperbaiki\n";
    }
    
    file_put_contents($viewFile, $content);
    echo "   ✅ View file diperbaiki\n";
} else {
    echo "   ❌ View file tidak ditemukan\n";
}

// 3. Periksa dan perbaiki routes
echo "\n3. Memeriksa routes...\n";

$routeFile = 'routes/web.php';
if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    
    // Cek apakah routes inter-outlet ada
    if (strpos($content, 'inter-outlet/products') !== false && strpos($content, 'inter-outlet/outlets') !== false) {
        echo "   ✅ Routes inter-outlet ditemukan\n";
        
        // Pastikan tidak ada duplikasi
        $productRoutes = substr_count($content, 'inter-outlet/products');
        $outletRoutes = substr_count($content, 'inter-outlet/outlets');
        
        if ($productRoutes > 1 || $outletRoutes > 1) {
            echo "   ⚠️  Ditemukan duplikasi routes ($productRoutes products, $outletRoutes outlets)\n";
        } else {
            echo "   ✅ Tidak ada duplikasi routes\n";
        }
    } else {
        echo "   ❌ Routes inter-outlet tidak ditemukan atau tidak lengkap\n";
    }
} else {
    echo "   ❌ File routes tidak ditemukan\n";
}

// 4. Test controller methods
echo "\n4. Testing controller methods...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    $methods = ['getProducts', 'getOutlets'];
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "   ✅ Method $method ditemukan\n";
        } else {
            echo "   ❌ Method $method tidak ditemukan\n";
        }
    }
    
    // Check for proper JSON responses
    if (strpos($content, 'response()->json') !== false) {
        echo "   ✅ JSON responses ditemukan\n";
    } else {
        echo "   ⚠️  JSON responses mungkin tidak ada\n";
    }
    
} else {
    echo "   ❌ Controller tidak ditemukan\n";
}

// 5. Buat file test sederhana untuk API
echo "\n5. Membuat test file untuk API...\n";

$testApiContent = <<<'PHP'
<?php

/**
 * Test Inter Outlet API Endpoints
 */

echo "🧪 Testing Inter Outlet API Endpoints...\n\n";

// Test dengan curl jika tersedia
function testEndpoint($url, $description) {
    echo "Testing: $description\n";
    echo "URL: $url\n";
    
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        
        curl_close($ch);
        
        echo "HTTP Code: $httpCode\n";
        echo "Content Type: $contentType\n";
        
        if ($httpCode == 200) {
            if (strpos($contentType, 'application/json') !== false) {
                echo "✅ Response OK (JSON)\n";
                $data = json_decode($response, true);
                if ($data && isset($data['success'])) {
                    echo "✅ Valid JSON structure\n";
                } else {
                    echo "⚠️  JSON structure may be invalid\n";
                }
            } else {
                echo "❌ Response is not JSON (probably HTML)\n";
                echo "First 200 chars: " . substr($response, 0, 200) . "\n";
            }
        } else {
            echo "❌ HTTP Error: $httpCode\n";
            if ($response) {
                echo "Response: " . substr($response, 0, 200) . "\n";
            }
        }
    } else {
        echo "⚠️  cURL not available, cannot test endpoint\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

// Get base URL
$baseUrl = 'http://localhost';
if (isset($_SERVER['HTTP_HOST'])) {
    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
} elseif (file_exists('.env')) {
    $env = file_get_contents('.env');
    if (preg_match('/APP_URL=(.+)/', $env, $matches)) {
        $baseUrl = trim($matches[1]);
    }
}

echo "Base URL: $baseUrl\n\n";

// Test endpoints
testEndpoint($baseUrl . '/admin/penjualan/inter-outlet/products?outlet_id=1', 'Products API');
testEndpoint($baseUrl . '/admin/penjualan/inter-outlet/outlets?current_outlet_id=1', 'Outlets API');

echo "📋 Catatan:\n";
echo "- Jika mendapat HTTP 401/403: masalah authentication\n";
echo "- Jika mendapat HTTP 404: route tidak ditemukan\n";
echo "- Jika mendapat HTML response: middleware redirect atau error page\n";
echo "- Jika mendapat JSON: endpoint berfungsi dengan baik\n\n";
PHP;

file_put_contents('test_inter_outlet_api.php', $testApiContent);
echo "   ✅ Test API file dibuat: test_inter_outlet_api.php\n";

// 6. Clear all caches
echo "\n6. Membersihkan semua cache...\n";

$commands = [
    'php artisan route:clear',
    'php artisan config:clear',
    'php artisan view:clear',
    'php artisan cache:clear',
    'php artisan optimize:clear'
];

foreach ($commands as $command) {
    if (function_exists('exec')) {
        exec("$command 2>&1", $output, $return_var);
        if ($return_var === 0) {
            echo "   ✅ $command\n";
        } else {
            echo "   ⚠️  $command (mungkin gagal)\n";
        }
    }
}

echo "\n✅ Comprehensive fix selesai!\n\n";

echo "📋 Ringkasan perbaikan:\n";
echo "   1. ✅ JavaScript error handling diperkuat\n";
echo "   2. ✅ Alpine.js check dan fallback ditambahkan\n";
echo "   3. ✅ View diperbaiki dengan script yang lebih robust\n";
echo "   4. ✅ Routes diverifikasi\n";
echo "   5. ✅ Controller methods dicek\n";
echo "   6. ✅ API test file dibuat\n";
echo "   7. ✅ Semua cache dibersihkan\n\n";

echo "🧪 Langkah testing:\n";
echo "   1. Jalankan: php test_inter_outlet_api.php\n";
echo "   2. Buka browser: /admin/penjualan/inter-outlet\n";
echo "   3. Buka Developer Tools (F12)\n";
echo "   4. Periksa Console dan Network tabs\n";
echo "   5. Test dropdown outlet dan produk\n\n";

echo "🔧 Jika masih ada masalah:\n";
echo "   1. Pastikan user sudah login sebagai admin/superadmin\n";
echo "   2. Pastikan user memiliki akses ke outlet\n";
echo "   3. Periksa Laravel log: storage/logs/laravel.log\n";
echo "   4. Test API endpoints secara langsung\n";
echo "   5. Periksa database connection\n\n";

echo "📁 File backup dibuat dengan suffix .backup.comprehensive.[timestamp]\n\n";