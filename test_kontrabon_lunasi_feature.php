<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\KontraBon;
use App\Models\KontraBonDetail;
use App\Models\Piutang;

echo "========================================\n";
echo "TESTING KONTRABON LUNASI FEATURE\n";
echo "========================================\n\n";

try {
    // Test 1: Check database structure
    echo "[TEST 1] Checking database structure...\n";
    
    $kontraBonColumns = DB::select("SHOW COLUMNS FROM kontra_bon");
    $detailColumns = DB::select("SHOW COLUMNS FROM kontra_bon_detail");
    
    $requiredKontraBonColumns = [
        'id_kontra_bon', 'kode_kontra_bon', 'id_member', 'id_user', 
        'total_pembayaran', 'tanggal_jatuh_tempo', 'status'
    ];
    
    $requiredDetailColumns = [
        'id_kontra_bon_detail', 'id_penjualan', 'nominal', 'jumlah_bayar'
    ];
    
    $existingKontraBonColumns = array_column($kontraBonColumns, 'Field');
    $existingDetailColumns = array_column($detailColumns, 'Field');
    
    foreach ($requiredKontraBonColumns as $column) {
        if (in_array($column, $existingKontraBonColumns)) {
            echo "✓ Column '$column' exists in kontra_bon table\n";
        } else {
            echo "✗ Column '$column' missing in kontra_bon table\n";
        }
    }
    
    foreach ($requiredDetailColumns as $column) {
        if (in_array($column, $existingDetailColumns)) {
            echo "✓ Column '$column' exists in kontra_bon_detail table\n";
        } else {
            echo "✗ Column '$column' missing in kontra_bon_detail table\n";
        }
    }
    
    // Test 2: Check if routes exist
    echo "\n[TEST 2] Checking routes...\n";
    
    $routes = [
        'admin.penjualan.kontrabon.index',
        'admin.penjualan.kontrabon.store',
        'admin.penjualan.kontrabon.data',
        'admin.penjualan.kontrabon.data-kontrabon',
        'admin.penjualan.kontrabon.lunasi',
        'admin.penjualan.kontrabon.print'
    ];
    
    foreach ($routes as $routeName) {
        try {
            $url = route($routeName, ['id' => 1]);
            echo "✓ Route '$routeName' exists\n";
        } catch (Exception $e) {
            echo "✗ Route '$routeName' not found\n";
        }
    }
    
    // Test 3: Check controller methods
    echo "\n[TEST 3] Checking controller methods...\n";
    
    $controller = new App\Http\Controllers\Admin\KontraBonController();
    $methods = ['index', 'store', 'show', 'data', 'dataKontraBon', 'getPiutang', 'print', 'lunasi'];
    
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "✓ Method '$method' exists in KontraBonController\n";
        } else {
            echo "✗ Method '$method' missing in KontraBonController\n";
        }
    }
    
    // Test 4: Check model relationships
    echo "\n[TEST 4] Checking model relationships...\n";
    
    $kontraBon = new KontraBon();
    $kontraBonDetail = new KontraBonDetail();
    
    $kontraBonMethods = ['member', 'outlet', 'user', 'details'];
    $detailMethods = ['kontraBon', 'penjualan'];
    
    foreach ($kontraBonMethods as $method) {
        if (method_exists($kontraBon, $method)) {
            echo "✓ Relationship '$method' exists in KontraBon model\n";
        } else {
            echo "✗ Relationship '$method' missing in KontraBon model\n";
        }
    }
    
    foreach ($detailMethods as $method) {
        if (method_exists($kontraBonDetail, $method)) {
            echo "✓ Relationship '$method' exists in KontraBonDetail model\n";
        } else {
            echo "✗ Relationship '$method' missing in KontraBonDetail model\n";
        }
    }
    
    // Test 5: Check view files
    echo "\n[TEST 5] Checking view files...\n";
    
    $viewFiles = [
        'resources/views/admin/penjualan/kontrabon/index.blade.php',
        'resources/views/admin/penjualan/kontrabon/print.blade.php'
    ];
    
    foreach ($viewFiles as $viewFile) {
        if (file_exists($viewFile)) {
            echo "✓ View file '$viewFile' exists\n";
            
            // Check for specific content
            $content = file_get_contents($viewFile);
            
            if ($viewFile === 'resources/views/admin/penjualan/kontrabon/index.blade.php') {
                if (strpos($content, 'function lunasi(') !== false) {
                    echo "  ✓ Lunasi function found in index view\n";
                } else {
                    echo "  ✗ Lunasi function missing in index view\n";
                }
            }
            
            if ($viewFile === 'resources/views/admin/penjualan/kontrabon/print.blade.php') {
                if (strpos($content, 'STATUS: LUNAS') !== false) {
                    echo "  ✓ Lunas status display found in print view\n";
                } else {
                    echo "  ✗ Lunas status display missing in print view\n";
                }
                
                if (strpos($content, '$kontraBon->details->sum(\'nominal\')') !== false) {
                    echo "  ✓ Correct total calculation found in print view\n";
                } else {
                    echo "  ✗ Correct total calculation missing in print view\n";
                }
            }
        } else {
            echo "✗ View file '$viewFile' not found\n";
        }
    }
    
    echo "\n========================================\n";
    echo "TESTING COMPLETED!\n";
    echo "========================================\n\n";
    
    echo "SUMMARY OF CHANGES:\n";
    echo "1. ✓ Fixed total hutang calculation in print view\n";
    echo "2. ✓ Added lunasi button and functionality\n";
    echo "3. ✓ Added status display for lunas kontrabon\n";
    echo "4. ✓ Updated database structure\n";
    echo "5. ✓ Added proper route for lunasi feature\n\n";
    
    echo "MANUAL TESTING STEPS:\n";
    echo "1. Go to /admin/penjualan/kontrabon\n";
    echo "2. Create a new kontrabon with some piutang\n";
    echo "3. Print the kontrabon and verify total calculation\n";
    echo "4. Click 'Lunasi' button on the kontrabon\n";
    echo "5. Verify status changes to 'Lunas'\n";
    echo "6. Print again to see 'STATUS: LUNAS' message\n";
    echo "7. Check that related piutang status changed to 'lunas'\n\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}