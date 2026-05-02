<?php

/**
 * Test Inter Outlet Default Outlet Removal
 * Memverifikasi bahwa default outlet "ALL" telah dihilangkan
 */

echo "🧪 Testing perbaikan default outlet pada penjualan antar outlet...\n\n";

// 1. Test Controller
echo "1. Testing Controller InterOutletSaleController...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';

if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check if default outlet assignment is removed
    if (strpos($content, '$selectedOutlet = $outlets->first()->id_outlet;') === false) {
        echo "   ✅ Default outlet assignment berhasil dihilangkan\n";
    } else {
        echo "   ❌ Default outlet assignment masih ada\n";
    }
    
    // Check if null handling is added
    if (strpos($content, 'if (!$selectedOutlet || $selectedOutlet === \'ALL\') {') !== false) {
        echo "   ✅ Null handling untuk selectedOutlet berhasil ditambahkan\n";
    } else {
        echo "   ❌ Null handling untuk selectedOutlet tidak ditemukan\n";
    }
    
    // Check if selectedOutlet can be null
    if (strpos($content, '$selectedOutlet = null;') !== false) {
        echo "   ✅ selectedOutlet dapat diset ke null\n";
    } else {
        echo "   ❌ selectedOutlet tidak dapat diset ke null\n";
    }
} else {
    echo "   ❌ File controller tidak ditemukan\n";
}

// 2. Test JavaScript
echo "\n2. Testing JavaScript inter-outlet.js...\n";

$jsFile = 'public/js/inter-outlet.js';

if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Check if init method has conditional loading
    if (strpos($content, 'if (this.selectedOutlet && this.selectedOutlet !== \'ALL\')') !== false) {
        echo "   ✅ Init method memiliki conditional loading\n";
    } else {
        echo "   ❌ Init method tidak memiliki conditional loading\n";
    }
    
    // Check if changeOutlet method has conditional loading
    if (strpos($content, 'changeOutlet() {') !== false && 
        strpos($content, 'if (this.selectedOutlet && this.selectedOutlet !== \'ALL\')') !== false) {
        echo "   ✅ changeOutlet method memiliki conditional loading\n";
    } else {
        echo "   ❌ changeOutlet method tidak memiliki conditional loading\n";
    }
    
    // Check if empty state handling is added
    if (strpos($content, 'No outlet selected - showing empty state') !== false) {
        echo "   ✅ Empty state handling berhasil ditambahkan\n";
    } else {
        echo "   ❌ Empty state handling tidak ditemukan\n";
    }
} else {
    echo "   ❌ File JavaScript tidak ditemukan\n";
}

// 3. Test View
echo "\n3. Testing View inter-outlet/index.blade.php...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check if "Pilih Outlet" option is added
    if (strpos($content, '<option value="">Pilih Outlet</option>') !== false) {
        echo "   ✅ Opsi 'Pilih Outlet' berhasil ditambahkan\n";
    } else {
        echo "   ❌ Opsi 'Pilih Outlet' tidak ditemukan\n";
    }
    
    // Check if empty state message is updated
    if (strpos($content, 'Pilih outlet terlebih dahulu untuk melihat produk') !== false) {
        echo "   ✅ Empty state message berhasil diupdate\n";
    } else {
        echo "   ❌ Empty state message tidak diupdate\n";
    }
    
    // Check if window.selectedOutlet handles null
    if (strpos($content, '{{ $selectedOutlet ? $selectedOutlet : "null" }}') !== false) {
        echo "   ✅ window.selectedOutlet dapat handle null value\n";
    } else {
        echo "   ❌ window.selectedOutlet tidak dapat handle null value\n";
    }
} else {
    echo "   ❌ File view tidak ditemukan\n";
}

// 4. Test Route
echo "\n4. Testing Route...\n";

try {
    // Check if route exists
    $routeExists = false;
    
    if (function_exists('route')) {
        $url = route('admin.penjualan.inter-outlet.index');
        echo "   ✅ Route 'admin.penjualan.inter-outlet.index' tersedia: $url\n";
        $routeExists = true;
    } else {
        echo "   ⚠️  Function route() tidak tersedia dalam context ini\n";
    }
} catch (Exception $e) {
    echo "   ❌ Route error: " . $e->getMessage() . "\n";
}

// 5. Test File Structure
echo "\n5. Testing File Structure...\n";

$requiredFiles = [
    'app/Http/Controllers/InterOutletSaleController.php' => 'Controller',
    'public/js/inter-outlet.js' => 'JavaScript',
    'resources/views/admin/penjualan/inter-outlet/index.blade.php' => 'View'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ $description file exists: $file\n";
    } else {
        echo "   ❌ $description file missing: $file\n";
    }
}

// 6. Summary
echo "\n📊 Ringkasan Testing:\n";

$allTests = [
    'Controller default outlet removal' => file_exists($controllerFile) && strpos(file_get_contents($controllerFile), '$selectedOutlet = $outlets->first()->id_outlet;') === false,
    'JavaScript conditional loading' => file_exists($jsFile) && strpos(file_get_contents($jsFile), 'if (this.selectedOutlet && this.selectedOutlet !== \'ALL\')') !== false,
    'View Pilih Outlet option' => file_exists($viewFile) && strpos(file_get_contents($viewFile), '<option value="">Pilih Outlet</option>') !== false,
    'Empty state message' => file_exists($viewFile) && strpos(file_get_contents($viewFile), 'Pilih outlet terlebih dahulu untuk melihat produk') !== false,
    'Null value handling' => file_exists($viewFile) && strpos(file_get_contents($viewFile), '{{ $selectedOutlet ? $selectedOutlet : "null" }}') !== false
];

$passedTests = 0;
$totalTests = count($allTests);

foreach ($allTests as $testName => $result) {
    if ($result) {
        echo "   ✅ $testName\n";
        $passedTests++;
    } else {
        echo "   ❌ $testName\n";
    }
}

echo "\n🎯 Hasil: $passedTests/$totalTests tests passed\n";

if ($passedTests === $totalTests) {
    echo "🎉 Semua tests berhasil! Perbaikan default outlet telah berhasil diimplementasikan.\n";
} else {
    echo "⚠️  Beberapa tests gagal. Periksa kembali implementasi.\n";
}

echo "\n📝 Langkah selanjutnya:\n";
echo "   1. Buka halaman penjualan antar outlet di browser\n";
echo "   2. Pastikan dropdown outlet menampilkan 'Pilih Outlet' sebagai default\n";
echo "   3. Pastikan tidak ada produk yang ditampilkan sampai outlet dipilih\n";
echo "   4. Pilih outlet dan verifikasi produk dimuat sesuai outlet\n";
echo "   5. Test ganti outlet dan pastikan data berubah\n\n";

?>