<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Test History Modal Height Fix
echo "=== TESTING HISTORY MODAL HEIGHT FIX ===\n\n";

try {
    // Test 1: Check if history route exists
    echo "1. Testing history route...\n";
    try {
        $url = route('admin.penjualan.inter-outlet.history');
        echo "   ✓ History route exists: {$url}\n";
    } catch (Exception $e) {
        echo "   ✗ History route not found\n";
        return;
    }
    
    // Test 2: Check if history data route exists
    echo "\n2. Testing history data route...\n";
    try {
        $url = route('admin.penjualan.inter-outlet.history.data');
        echo "   ✓ History data route exists: {$url}\n";
    } catch (Exception $e) {
        echo "   ✗ History data route not found\n";
        return;
    }
    
    // Test 3: Check if there's sample data
    echo "\n3. Checking for sample transaction data...\n";
    $transactionCount = DB::table('inter_outlet_sales')->count();
    echo "   Found {$transactionCount} inter outlet sale transactions\n";
    
    if ($transactionCount > 0) {
        $sampleTransaction = DB::table('inter_outlet_sales')
            ->select('no_transaksi', 'tanggal', 'total', 'status')
            ->first();
        
        echo "   Sample transaction:\n";
        echo "   - No: {$sampleTransaction->no_transaksi}\n";
        echo "   - Date: {$sampleTransaction->tanggal}\n";
        echo "   - Total: Rp " . number_format($sampleTransaction->total, 0, ',', '.') . "\n";
        echo "   - Status: {$sampleTransaction->status}\n";
    } else {
        echo "   ⚠ No sample data found - create some transactions to test\n";
    }
    
    // Test 4: Check outlets for filter
    echo "\n4. Checking outlets for filter...\n";
    $outletCount = DB::table('outlets')->where('is_active', true)->count();
    echo "   Found {$outletCount} active outlets\n";
    
    if ($outletCount > 0) {
        $outlets = DB::table('outlets')
            ->where('is_active', true)
            ->select('nama_outlet')
            ->limit(3)
            ->get();
        
        echo "   Sample outlets:\n";
        foreach ($outlets as $outlet) {
            echo "   - {$outlet->nama_outlet}\n";
        }
    }
    
    // Test 5: Verify file structure
    echo "\n5. Verifying file structure...\n";
    $historyFile = 'resources/views/admin/penjualan/inter-outlet/history.blade.php';
    
    if (file_exists($historyFile)) {
        echo "   ✓ History view file exists\n";
        
        $content = file_get_contents($historyFile);
        
        // Check for key improvements
        $checks = [
            'main-container' => 'Main container with flexbox layout',
            'table-section' => 'Table section with flex layout',
            'scrollY:' => 'DataTable scroll configuration',
            'adjustTableHeight' => 'Dynamic height adjustment function',
            'compact-spacing' => 'Compact spacing for iframe'
        ];
        
        foreach ($checks as $search => $description) {
            if (strpos($content, $search) !== false) {
                echo "   ✓ {$description} found\n";
            } else {
                echo "   ✗ {$description} missing\n";
            }
        }
    } else {
        echo "   ✗ History view file not found\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    echo "\nMANUAL TESTING STEPS:\n";
    echo "1. Go to: http://localhost/MORRA/admin/penjualan/inter-outlet\n";
    echo "2. Click 'Riwayat' button\n";
    echo "3. Modal should open with full screen height\n";
    echo "4. Table should utilize full available height\n";
    echo "5. No scrolling issues or cut-off content\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}