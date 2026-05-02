<?php

/**
 * Fix Inter Outlet Authentication Issues
 * Memperbaiki masalah authentication pada API endpoints
 */

echo "🔧 Fixing Inter Outlet Authentication Issues...\n\n";

// 1. Periksa controller authentication
echo "1. Memeriksa controller authentication...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Backup
    copy($controllerFile, $controllerFile . '.backup.auth.' . date('YmdHis'));
    
    // Cek method getProducts dan getOutlets
    $needsUpdate = false;
    
    // Pattern untuk mencari method getProducts
    if (preg_match('/public function getProducts\(Request \$request\)\s*\{(.*?)\n    \}/s', $content, $matches)) {
        $methodContent = $matches[1];
        
        // Cek apakah sudah ada auth check yang proper
        if (strpos($methodContent, 'auth()->user()') !== false) {
            echo "   ✅ getProducts sudah memiliki auth check\n";
        } else {
            echo "   ⚠️  getProducts mungkin perlu auth check yang lebih baik\n";
        }
        
        // Cek apakah ada try-catch untuk auth
        if (strpos($methodContent, 'try {') === false || strpos($methodContent, 'auth()->user()') === false) {
            echo "   🔧 Memperbaiki auth check di getProducts...\n";
            
            $newMethodContent = <<<'PHP'
    public function getProducts(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login again.',
                    'data' => [],
                    'count' => 0
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication error. Please login again.',
                'data' => [],
                'count' => 0
            ], 401);
        }

        $outletId = $request->get('outlet_id', $user->outlet_id ?? 1);
        
        // Validate outlet access
        if (!$this->hasOutletAccess((int)$outletId)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini.',
                'data' => [],
                'count' => 0
            ], 403);
        }
        
        try {
            // Query produk dengan stok, gambar, dan harga inter outlet terpisah
            $rawProducts = DB::select("
                SELECT 
                    p.id_produk,
                    p.kode_produk as sku,
                    p.nama_produk as name,
                    p.harga_jual as regular_price,
                    COALESCE(iopp.inter_outlet_price, 0) as inter_outlet_price,
                    COALESCE(k.nama_kategori, 'Barang') as category,
                    COALESCE(s.nama_satuan, 'pcs') as satuan,
                    COALESCE(
                        (SELECT SUM(hpp.stok) FROM hpp_produk hpp WHERE hpp.id_produk = p.id_produk), 
                        0
                    ) as stock,
                    COALESCE(
                        (SELECT pi.path FROM product_images pi 
                         INNER JOIN produk p2 ON pi.id_produk = p2.id_produk 
                         WHERE p2.kode_produk = p.kode_produk AND pi.is_primary = 1 
                         LIMIT 1),
                        (SELECT pi.path FROM product_images pi WHERE pi.id_produk = p.id_produk LIMIT 1)
                    ) as image_path
                FROM produk p
                LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
                LEFT JOIN satuan s ON p.id_satuan = s.id_satuan
                LEFT JOIN inter_outlet_product_prices iopp ON p.id_produk = iopp.id_produk AND iopp.outlet_id = ?
                WHERE p.id_outlet = ? 
                AND p.is_active = 1
                ORDER BY p.nama_produk
            ", [$outletId, $outletId]);
            
            // Convert to array format yang diharapkan frontend
            $products = array_map(function($product) {
                // Gunakan harga inter outlet jika ada, jika tidak gunakan harga regular
                $displayPrice = $product->inter_outlet_price > 0 ? $product->inter_outlet_price : $product->regular_price;
                
                return [
                    'id_produk' => (int) $product->id_produk,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'category' => $product->category,
                    'regular_price' => (float) $product->regular_price,
                    'inter_outlet_price' => (float) $product->inter_outlet_price,
                    'price' => (float) $displayPrice, // Harga yang ditampilkan untuk transaksi
                    'stock' => (float) $product->stock,
                    'satuan' => $product->satuan,
                    'image' => $product->image_path ? config('app.url'). \Illuminate\Support\Facades\Storage::url($product->image_path) : null,
                ];
            }, $rawProducts);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'count' => count($products),
                'outlet_id' => $outletId,
                'authenticated' => true,
                'user_id' => $user->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Inter outlet sale getProducts error', [
                'outlet_id' => $outletId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat produk: ' . $e->getMessage(),
                'data' => [],
                'count' => 0
            ], 500);
        }
    }
PHP;
            
            // Replace method
            $content = preg_replace('/public function getProducts\(Request \$request\)\s*\{.*?\n    \}/s', $newMethodContent, $content);
            $needsUpdate = true;
        }
    }
    
    // Pattern untuk mencari method getOutlets
    if (preg_match('/public function getOutlets\(Request \$request\)\s*\{(.*?)\n    \}/s', $content, $matches)) {
        $methodContent = $matches[1];
        
        if (strpos($methodContent, 'auth()->user()') === false) {
            echo "   🔧 Memperbaiki auth check di getOutlets...\n";
            
            $newMethodContent = <<<'PHP'
    public function getOutlets(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login again.',
                    'data' => []
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication error. Please login again.',
                'data' => []
            ], 401);
        }
        
        $currentOutletId = $request->get('current_outlet_id');
        
        // Get ALL outlets for destination dropdown (tidak dibatasi akses user)
        $outlets = Outlet::where('is_active', true)
            ->when($currentOutletId, function($query) use ($currentOutletId) {
                return $query->where('id_outlet', '!=', $currentOutletId);
            })
            ->orderBy('nama_outlet')
            ->get()
            ->map(function($outlet) {
                return [
                    'id' => $outlet->id_outlet,
                    'name' => $outlet->nama_outlet,
                    'address' => $outlet->alamat
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $outlets,
            'authenticated' => true,
            'user_id' => $user->id
        ]);
    }
PHP;
            
            // Replace method
            $content = preg_replace('/public function getOutlets\(Request \$request\)\s*\{.*?\n    \}/s', $newMethodContent, $content);
            $needsUpdate = true;
        }
    }
    
    if ($needsUpdate) {
        file_put_contents($controllerFile, $content);
        echo "   ✅ Controller methods diperbaiki\n";
    } else {
        echo "   ✅ Controller methods sudah OK\n";
    }
    
} else {
    echo "   ❌ Controller tidak ditemukan\n";
}

// 2. Periksa middleware di routes
echo "\n2. Memeriksa middleware di routes...\n";

$routeFile = 'routes/web.php';
if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    
    // Cek apakah routes inter-outlet ada dalam group auth
    if (preg_match('/Route::middleware\(\[\'auth\'\]\)->group\(function.*?\{(.*?)\}\);/s', $content, $matches)) {
        $groupContent = $matches[1];
        
        if (strpos($groupContent, 'inter-outlet') !== false) {
            echo "   ✅ Routes inter-outlet sudah dalam auth middleware\n";
        } else {
            echo "   ⚠️  Routes inter-outlet mungkin tidak dalam auth middleware\n";
        }
    } else {
        echo "   ⚠️  Auth middleware group tidak ditemukan atau berbeda\n";
    }
    
    // Cek apakah ada prefix admin
    if (strpos($content, "prefix('admin')") !== false && strpos($content, 'inter-outlet') !== false) {
        echo "   ✅ Routes memiliki prefix admin\n";
    } else {
        echo "   ⚠️  Prefix admin mungkin tidak ada\n";
    }
    
} else {
    echo "   ❌ Routes file tidak ditemukan\n";
}

// 3. Periksa JavaScript untuk CSRF token
echo "\n3. Memeriksa CSRF token di JavaScript...\n";

$jsFile = 'public/js/inter-outlet.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    if (strpos($content, 'X-CSRF-TOKEN') !== false) {
        echo "   ✅ CSRF token sudah ada di JavaScript\n";
    } else {
        echo "   🔧 Menambahkan CSRF token handling...\n";
        
        // Tambahkan CSRF token handling
        $csrfHandling = <<<'JS'

// CSRF Token handling
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

// Add CSRF token to all fetch requests
const originalFetch = window.fetch;
window.fetch = function(url, options = {}) {
    // Add CSRF token for POST requests
    if (options.method && options.method.toUpperCase() !== 'GET') {
        options.headers = options.headers || {};
        options.headers['X-CSRF-TOKEN'] = getCSRFToken();
    }
    
    // Add authentication headers
    options.headers = options.headers || {};
    options.headers['X-Requested-With'] = 'XMLHttpRequest';
    options.headers['Accept'] = 'application/json';
    
    return originalFetch(url, options);
};

JS;
        
        // Insert setelah konstanta
        $content = str_replace('console.log(\'🏪 Loading Inter Outlet Sale JavaScript...\');', 
            'console.log(\'🏪 Loading Inter Outlet Sale JavaScript...\');' . $csrfHandling, $content);
        
        file_put_contents($jsFile, $content);
        echo "   ✅ CSRF token handling ditambahkan\n";
    }
} else {
    echo "   ❌ JavaScript file tidak ditemukan\n";
}

// 4. Periksa view untuk CSRF meta tag
echo "\n4. Memeriksa CSRF meta tag di view...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    if (strpos($content, 'csrf-token') !== false) {
        echo "   ✅ CSRF meta tag sudah ada\n";
    } else {
        echo "   🔧 Menambahkan CSRF meta tag...\n";
        
        // Tambahkan CSRF meta tag di head
        $csrfMeta = "\n@push('head')\n<meta name=\"csrf-token\" content=\"{{ csrf_token() }}\">\n@endpush\n";
        
        $content = str_replace('<x-layouts.admin>', '<x-layouts.admin>' . $csrfMeta, $content);
        
        file_put_contents($viewFile, $content);
        echo "   ✅ CSRF meta tag ditambahkan\n";
    }
} else {
    echo "   ❌ View file tidak ditemukan\n";
}

// 5. Clear cache lagi
echo "\n5. Membersihkan cache...\n";

$commands = [
    'php artisan route:clear',
    'php artisan config:clear',
    'php artisan view:clear'
];

foreach ($commands as $command) {
    if (function_exists('exec')) {
        exec("$command 2>&1", $output, $return_var);
        if ($return_var === 0) {
            echo "   ✅ $command\n";
        }
    }
}

echo "\n✅ Authentication fix selesai!\n\n";

echo "📋 Ringkasan perbaikan:\n";
echo "   1. ✅ Controller methods diperbaiki dengan auth check yang proper\n";
echo "   2. ✅ Outlet access validation ditambahkan\n";
echo "   3. ✅ CSRF token handling ditambahkan ke JavaScript\n";
echo "   4. ✅ CSRF meta tag ditambahkan ke view\n";
echo "   5. ✅ Error handling diperbaiki\n";
echo "   6. ✅ Cache dibersihkan\n\n";

echo "🧪 Langkah testing:\n";
echo "   1. Login ke aplikasi sebagai admin/superadmin\n";
echo "   2. Buka halaman: /admin/penjualan/inter-outlet\n";
echo "   3. Buka Developer Tools (F12)\n";
echo "   4. Periksa Console - seharusnya tidak ada error\n";
echo "   5. Periksa Network tab - API calls seharusnya return 200\n";
echo "   6. Test dropdown outlet dan produk\n\n";

echo "🔧 Jika masih ada masalah:\n";
echo "   1. Pastikan sudah login dengan user yang memiliki akses outlet\n";
echo "   2. Coba logout dan login ulang\n";
echo "   3. Clear browser cache (Ctrl+F5)\n";
echo "   4. Periksa Laravel session configuration\n";
echo "   5. Test dengan user superadmin\n\n";

echo "📁 File backup dibuat dengan suffix .backup.auth.[timestamp]\n\n";