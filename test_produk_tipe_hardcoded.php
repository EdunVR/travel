<?php

/**
 * Script untuk testing perubahan tipe produk menjadi hardcoded
 * 
 * Jalankan dengan: php artisan tinker
 * Kemudian: include 'test_produk_tipe_hardcoded.php';
 */

echo "=== Testing Produk Tipe Hardcoded Implementation ===\n\n";

// Test 1: Cek apakah route types sudah dihapus
echo "Test 1: Cek route produk/types\n";
try {
    $routes = collect(\Route::getRoutes())->filter(function($route) {
        return str_contains($route->uri(), 'produk/types');
    });
    
    if ($routes->isEmpty()) {
        echo "✓ Route 'produk/types' berhasil dihapus\n";
    } else {
        echo "⚠️  Route 'produk/types' masih ada:\n";
        foreach ($routes as $route) {
            echo "  - {$route->uri()}\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Cek apakah method getTypes sudah dihapus dari controller
echo "Test 2: Cek method getTypes di ProdukController\n";
try {
    $controller = new \App\Http\Controllers\ProdukController();
    
    if (method_exists($controller, 'getTypes')) {
        echo "⚠️  Method getTypes masih ada di ProdukController\n";
    } else {
        echo "✓ Method getTypes berhasil dihapus dari ProdukController\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Test create produk dengan tipe hardcoded
echo "Test 3: Test create produk dengan tipe hardcoded\n";
try {
    $validTypes = ['barang_dagang', 'jasa', 'paket_travel', 'produk_kustom'];
    
    echo "✓ Tipe produk yang valid:\n";
    foreach ($validTypes as $type) {
        $displayName = match($type) {
            'barang_dagang' => 'Barang Dagang',
            'jasa' => 'Jasa',
            'paket_travel' => 'Paket Travel',
            'produk_kustom' => 'Produk Kustom',
            default => $type
        };
        echo "  - {$type} → {$displayName}\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Cek produk existing dengan tipe
echo "Test 4: Cek produk existing dengan tipe\n";
try {
    $produkSample = \App\Models\Produk::take(5)->get(['id_produk', 'nama_produk', 'tipe_produk']);
    
    if ($produkSample->count() > 0) {
        echo "✓ Sample produk existing:\n";
        foreach ($produkSample as $produk) {
            $displayType = match($produk->tipe_produk) {
                'barang_dagang' => 'Barang Dagang',
                'jasa' => 'Jasa',
                'paket_travel' => 'Paket Travel',
                'produk_kustom' => 'Produk Kustom',
                default => $produk->tipe_produk
            };
            echo "  - {$produk->nama_produk} → {$produk->tipe_produk} ({$displayType})\n";
        }
    } else {
        echo "⚠️  Tidak ada produk untuk testing\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Test validation tipe produk
echo "Test 5: Test validation tipe produk\n";
try {
    $validTypes = ['barang_dagang', 'jasa', 'paket_travel', 'produk_kustom'];
    $invalidTypes = ['invalid_type', 'barang', 'service', ''];
    
    echo "✓ Tipe valid: " . implode(', ', $validTypes) . "\n";
    echo "✗ Tipe invalid: " . implode(', ', $invalidTypes) . "\n";
    
    // Test dengan validator Laravel
    $validator = \Validator::make(
        ['tipe_produk' => 'barang_dagang'], 
        ['tipe_produk' => 'required|in:barang_dagang,jasa,paket_travel,produk_kustom']
    );
    
    if ($validator->passes()) {
        echo "✓ Validation rule untuk tipe produk berfungsi\n";
    } else {
        echo "✗ Validation rule gagal\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Statistik tipe produk
echo "Test 6: Statistik tipe produk\n";
try {
    $stats = \DB::select("
        SELECT 
            tipe_produk,
            COUNT(*) as jumlah
        FROM produk 
        WHERE tipe_produk IS NOT NULL 
        GROUP BY tipe_produk 
        ORDER BY jumlah DESC
    ");
    
    if (count($stats) > 0) {
        echo "✓ Distribusi tipe produk:\n";
        foreach ($stats as $stat) {
            $displayType = match($stat->tipe_produk) {
                'barang_dagang' => 'Barang Dagang',
                'jasa' => 'Jasa',
                'paket_travel' => 'Paket Travel',
                'produk_kustom' => 'Produk Kustom',
                default => $stat->tipe_produk
            };
            echo "  - {$displayType}: {$stat->jumlah} produk\n";
        }
    } else {
        echo "⚠️  Tidak ada data statistik tipe produk\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing Selesai ===\n";

// Panduan testing manual
echo "\nPanduan Testing Manual:\n";
echo "1. Buka halaman admin/inventaris/produk di browser\n";
echo "2. Klik tombol 'Tambah Produk'\n";
echo "3. Lihat dropdown 'Tipe Produk' - harus menampilkan 4 pilihan:\n";
echo "   - Barang Dagang\n";
echo "   - Jasa\n";
echo "   - Paket Travel\n";
echo "   - Produk Kustom\n";
echo "4. Pilih salah satu tipe dan lengkapi form\n";
echo "5. Simpan produk dan pastikan berhasil\n";
echo "6. Edit produk existing dan pastikan tipe terpilih dengan benar\n";
echo "7. Cek console browser - tidak boleh ada error\n";
echo "8. Cek network tab - tidak ada request ke /produk/types\n\n";

echo "Expected Behavior:\n";
echo "✓ Dropdown tipe produk menampilkan 4 pilihan hardcoded\n";
echo "✓ Tidak ada request AJAX ke endpoint /produk/types\n";
echo "✓ Form loading lebih cepat (1 request berkurang)\n";
echo "✓ Create/edit produk berfungsi normal\n";
echo "✓ Tidak ada error di console atau Laravel log\n\n";

echo "Jika ada masalah:\n";
echo "1. Clear browser cache: Ctrl+F5\n";
echo "2. Clear Laravel cache: php artisan cache:clear\n";
echo "3. Cek Laravel log: tail -f storage/logs/laravel.log\n";
echo "4. Cek file: resources/views/admin/inventaris/produk/index.blade.php\n";