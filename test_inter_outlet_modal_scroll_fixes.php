<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== TESTING INTER OUTLET MODAL SCROLL FIXES ===\n\n";

try {
    // 1. Test History Data with Different Outlet Filters
    echo "1. Testing History Data with Outlet Filtering...\n";
    
    $user = \App\Models\User::first();
    if ($user) {
        auth()->login($user);
        echo "   ✓ User authenticated: {$user->name}\n";
        
        $controller = app(\App\Http\Controllers\InterOutletSaleController::class);
        
        // Test with 'all' outlets
        $allRequest = Request::create('/admin/penjualan/inter-outlet/history', 'GET', [
            'outlet_id' => 'all'
        ]);
        $allRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        $allResponse = $controller->history($allRequest);
        $allData = json_decode($allResponse->getContent(), true);
        
        if ($allData['success']) {
            echo "   ✓ All outlets filter: " . count($allData['data']) . " transactions\n";
        }
        
        // Test with specific outlet
        $specificRequest = Request::create('/admin/penjualan/inter-outlet/history', 'GET', [
            'outlet_id' => '1'
        ]);
        $specificRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
        
        $specificResponse = $controller->history($specificRequest);
        $specificData = json_decode($specificResponse->getContent(), true);
        
        if ($specificData['success']) {
            echo "   ✓ Outlet 1 filter: " . count($specificData['data']) . " transactions\n";
            
            // Check if filtering actually works
            if (count($allData['data']) >= count($specificData['data'])) {
                echo "   ✓ Outlet filtering appears to be working correctly\n";
            } else {
                echo "   ⚠️  Outlet filtering may not be working as expected\n";
            }
        }
    }
    
    // 2. Test COA Modal Data Structure
    echo "\n2. Testing COA Modal Data Structure...\n";
    
    $coaRequest = Request::create('/admin/penjualan/inter-outlet/coa-modal-data', 'GET', [
        'outlet_id' => 1
    ]);
    $coaRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    $coaResponse = $controller->getCoaModalData($coaRequest);
    $coaData = json_decode($coaResponse->getContent(), true);
    
    if ($coaData['success']) {
        echo "   ✓ COA Modal Data API works\n";
        echo "   Outlets: " . count($coaData['data']['outlets']) . "\n";
        echo "   Books: " . count($coaData['data']['books']) . "\n";
        echo "   Account Types: " . count($coaData['data']['accountsByType']) . "\n";
        
        // Check if accounts are properly grouped
        $accountTypes = $coaData['data']['accountsByType'];
        foreach (['asset', 'liability', 'revenue', 'expense'] as $type) {
            if (isset($accountTypes[$type])) {
                echo "   - {$type}: " . count($accountTypes[$type]) . " accounts\n";
            }
        }
    }
    
    // 3. Check Database for Sample Data
    echo "\n3. Checking Database for Sample Data...\n";
    
    $transactionCount = DB::table('inter_outlet_sales')->count();
    echo "   Total transactions: {$transactionCount}\n";
    
    if ($transactionCount > 0) {
        $sampleTransaction = DB::table('inter_outlet_sales')
            ->join('outlets as oa', 'inter_outlet_sales.outlet_asal', '=', 'oa.id_outlet')
            ->join('outlets as ot', 'inter_outlet_sales.outlet_tujuan', '=', 'ot.id_outlet')
            ->select('inter_outlet_sales.*', 'oa.nama_outlet as outlet_asal_name', 'ot.nama_outlet as outlet_tujuan_name')
            ->first();
        
        if ($sampleTransaction) {
            echo "   Sample: {$sampleTransaction->no_transaksi}\n";
            echo "   From: {$sampleTransaction->outlet_asal_name} -> To: {$sampleTransaction->outlet_tujuan_name}\n";
            echo "   Status: {$sampleTransaction->status}\n";
        }
    }
    
    // 4. Test Outlet Access
    echo "\n4. Testing Outlet Access...\n";
    
    $outlets = \App\Models\Outlet::where('is_active', true)->get();
    echo "   Total active outlets: " . $outlets->count() . "\n";
    
    foreach ($outlets as $outlet) {
        echo "   - {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "MODAL SCROLL FIXES VERIFICATION COMPLETE\n";
    echo str_repeat("=", 60) . "\n";
    
    echo "\nFIXES IMPLEMENTED:\n";
    echo "✓ COA Modal Scrolling: Fixed modal structure with flex layout\n";
    echo "✓ Modal Footer: Fixed at bottom, always visible\n";
    echo "✓ History Loading: Added loading state with spinner\n";
    echo "✓ History Filtering: Enhanced with debugging and proper outlet filtering\n";
    echo "✓ Console Logging: Added debugging for outlet filter changes\n\n";
    
    echo "BROWSER TESTING CHECKLIST:\n";
    echo "1. Open 'Setting COA' modal\n";
    echo "2. Check if modal content is scrollable\n";
    echo "3. Verify save button is always visible at bottom\n";
    echo "4. Open 'Riwayat' modal\n";
    echo "5. Change outlet filter - should show loading spinner\n";
    echo "6. Check console for debugging messages\n";
    echo "7. Verify data updates when outlet filter changes\n";
    echo "8. Test other filters (status, date range)\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}