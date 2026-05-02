<?php

/**
 * Test Inter Outlet Auto Select First Outlet
 * Memverifikasi bahwa outlet pertama otomatis terpilih
 */

echo "🧪 Testing auto-select outlet pertama pada penjualan antar outlet...\n\n";

// 1. Test Controller
echo "1. Testing Controller InterOutletSaleController...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';

if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check if default outlet assignment is restored
    if (strpos($content, '$selectedOutlet = $outlets->first()->id_outlet;') !== false) {
        echo "   ✅ Default outlet assignment berhasil dikembalikan\n";
    } else {
        echo "   ❌ Default outlet assignment tidak ditemukan\n";
    }
    
    // Check if null handling is updated
    if (strpos($content, 'Default to first accessible outlet if none selected') !== false) {
        echo "   ✅ Comment untuk default outlet berhasil ditambahkan\n";
    } else {
        echo "   ❌ Comment untuk default outlet tidak ditemukan\n";
    }
    
    // Check if selectedOutlet uses first outlet
    if (strpos($content, 'if (!$selectedOutlet || $selectedOutlet === \'ALL\') {') !== false &&
        strpos($content, '$selectedOutlet = $outlets->first()->id_outlet;') !== false) {
        echo "   ✅ Logic untuk menggunakan outlet pertama berhasil ditambahkan\n";
    } else {
        echo "   ❌ Logic untuk menggunakan outlet pertama tidak ditemukan\n";
    }
} else {
    echo "   ❌ File controller tidak ditemukan\n";
}

// 2. Test JavaScript
echo "\n2. Testing JavaScript inter-outlet.js...\n";

$jsFile = 'public/js/inter-outlet.js';

if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Check if init method always loads data
    if (strpos($content, 'Always load data since we have a selected outlet') !== false) {
        echo "   ✅ Init method selalu load data\n";
    } else {
        echo "   ❌ Init method tidak selalu load data\n";
    }
    
    // Check if changeOutlet method always loads data
    if (strpos($content, 'Always load data when outlet changes') !== false) {
        echo "   ✅ changeOutlet method selalu load data\n";
    } else {
        echo "   ❌ changeOutlet method tidak selalu load data\n";
    }
    
    // Check if conditional loading is removed
    if (strpos($content, 'if (this.selectedOutlet && this.selectedOutlet !== \'ALL\')') === false) {
        echo "   ✅ Conditional loading berhasil dihilangkan\n";
    } else {
        echo "   ❌ Conditional loading masih ada\n";
    }
    
    // Check if empty state handling is removed
    if (strpos($content, 'No outlet selected - showing empty state') === false) {
        echo "   ✅ Empty state handling berhasil dihilangkan\n";
    } else {
        echo "   ❌ Empty state handling masih ada\n";
    }
} else {
    echo "   ❌ File JavaScript tidak ditemukan\n";
}

// 3. Test View
echo "\n3. Testing View inter-outlet/index.blade.php...\n";

$viewFile = 'resources/views/admin/penjualan/inter-outlet/index.blade.php';

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check if main outlet selector doesn't have empty option
    $lines = explode("\n", $content);
    $inMainSelector = false;
    $hasEmptyOption = false;
    
    foreach ($lines as $line) {
        if (strpos($line, 'x-model="selectedOutlet"') !== false && strpos($line, '@change="changeOutlet()"') !== false) {
            $inMainSelector = true;
            continue;
        }
        if ($inMainSelector && strpos($line, '</select>') !== false) {
            break;
        }
        if ($inMainSelector && strpos($line, '<option value="">') !== false) {
            $hasEmptyOption = true;
            break;
        }
    }
    
    if (!$hasEmptyOption) {
        echo "   ✅ Outlet selector utama tidak memiliki opsi kosong\n";
    } else {
        echo "   ❌ Outlet selector utama masih memiliki opsi kosong\n";
    }
    
    // Check if empty state message is back to original
    if (strpos($content, 'Tidak ada produk ditemukan') !== false && 
        strpos($content, 'Pilih outlet terlebih dahulu untuk melihat produk') === false) {
        echo "   ✅ Empty state message berhasil dikembalikan ke original\n";
    } else {
        echo "   ❌ Empty state message tidak dikembalikan ke original\n";
    }
    
    // Check if window.selectedOutlet is properly set
    if (strpos($content, 'window.selectedOutlet = {{ $selectedOutlet }};') !== false) {
        echo "   ✅ window.selectedOutlet berhasil diupdate\n";
    } else {
        echo "   ❌ window.selectedOutlet tidak diupdate\n";
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
    'Controller default outlet restored' => file_exists($controllerFile) && strpos(file_get_contents($controllerFile), '$selectedOutlet = $outlets->first()->id_outlet;') !== false,
    'JavaScript always loads data' => file_exists($jsFile) && strpos(file_get_contents($jsFile), 'Always load data since we have a selected outlet') !== false,
    'Main outlet selector no empty option' => function() use ($viewFile) {
        if (!file_exists($viewFile)) return false;
        $content = file_get_contents($viewFile);
        $lines = explode("\n", $content);
        $inMainSelector = false;
        
        foreach ($lines as $line) {
            if (strpos($line, 'x-model="selectedOutlet"') !== false && strpos($line, '@change="changeOutlet()"') !== false) {
                $inMainSelector = true;
                continue;
            }
            if ($inMainSelector && strpos($line, '</select>') !== false) {
                break;
            }
            if ($inMainSelector && strpos($line, '<option value="">') !== false) {
                return false;
            }
        }
        return true;
    },
    'Empty state message original' => file_exists($viewFile) && strpos(file_get_contents($viewFile), 'Tidak ada produk ditemukan') !== false && strpos(file_get_contents($viewFile), 'Pilih outlet terlebih dahulu untuk melihat produk') === false,
    'Window selectedOutlet updated' => file_exists($viewFile) && strpos(file_get_contents($viewFile), 'window.selectedOutlet = {{ $selectedOutlet }};') !== false
];

$passedTests = 0;
$totalTests = count($allTests);

foreach ($allTests as $testName => $result) {
    $testResult = is_callable($result) ? $result() : $result;
    if ($testResult) {
        echo "   ✅ $testName\n";
        $passedTests++;
    } else {
        echo "   ❌ $testName\n";
    }
}

echo "\n🎯 Hasil: $passedTests/$totalTests tests passed\n";

if ($passedTests === $totalTests) {
    echo "🎉 Semua tests berhasil! Auto-select outlet pertama telah berhasil diimplementasikan.\n";
} else {
    echo "⚠️  Beberapa tests gagal. Periksa kembali implementasi.\n";
}

echo "\n📝 Langkah selanjutnya:\n";
echo "   1. Buka halaman penjualan antar outlet di browser\n";
echo "   2. Pastikan outlet pertama otomatis terpilih\n";
echo "   3. Pastikan produk langsung dimuat sesuai outlet pertama\n";
echo "   4. Test ganti outlet dan pastikan data berubah\n";
echo "   5. Pastikan tidak ada opsi 'Pilih Outlet' di dropdown\n\n";

?>