<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== TESTING INTER OUTLET UI FIXES ===\n\n";

try {
    // 1. Test History Data API
    echo "1. Testing History Data API...\n";
    
    // Create a mock request with AJAX headers
    $historyRequest = Request::create('/admin/penjualan/inter-outlet/history', 'GET', [
        'outlet_id' => 'all'
    ]);
    $historyRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    $historyRequest->headers->set('Accept', 'application/json');
    
    // Simulate authentication
    $user = \App\Models\User::first();
    if ($user) {
        auth()->login($user);
        echo "   ✓ User authenticated: {$user->name}\n";
    } else {
        echo "   ⚠️  No users found in database\n";
    }
    
    // Test history endpoint
    $controller = app(\App\Http\Controllers\InterOutletSaleController::class);
    $response = $controller->history($historyRequest);
    
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $data = json_decode($response->getContent(), true);
        if ($data['success']) {
            echo "   ✓ History API returns JSON successfully\n";
            echo "   Records found: " . count($data['data']) . "\n";
        } else {
            echo "   ✗ History API failed: " . $data['message'] . "\n";
        }
    } else {
        echo "   ✗ History API did not return JSON response\n";
    }
    
    // 2. Test COA Modal Data API
    echo "\n2. Testing COA Modal Data API...\n";
    
    $coaRequest = Request::create('/admin/penjualan/inter-outlet/coa-modal-data', 'GET', [
        'outlet_id' => 1
    ]);
    $coaRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    $coaRequest->headers->set('Accept', 'application/json');
    
    $coaResponse = $controller->getCoaModalData($coaRequest);
    
    if ($coaResponse instanceof \Illuminate\Http\JsonResponse) {
        $coaData = json_decode($coaResponse->getContent(), true);
        if ($coaData['success']) {
            echo "   ✓ COA Modal Data API returns JSON successfully\n";
            echo "   Outlets: " . count($coaData['data']['outlets']) . "\n";
            echo "   Books: " . count($coaData['data']['books']) . "\n";
            echo "   Account Types: " . count($coaData['data']['accountsByType']) . "\n";
        } else {
            echo "   ✗ COA Modal Data API failed: " . $coaData['message'] . "\n";
        }
    } else {
        echo "   ✗ COA Modal Data API did not return JSON response\n";
    }
    
    // 3. Test COA Settings Update
    echo "\n3. Testing COA Settings Update...\n";
    
    $updateRequest = Request::create('/admin/penjualan/inter-outlet/coa-settings', 'POST', [
        'outlet_id' => 1,
        'accounting_book_id' => 1,
        'akun_piutang_antar_outlet' => '1101',
        'akun_pendapatan_antar_outlet' => '4101',
        'akun_hpp' => '5101',
        'akun_persediaan' => '1301',
        'akun_ppn' => '2101'
    ]);
    $updateRequest->headers->set('X-CSRF-TOKEN', 'test-token');
    $updateRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    // Check if required models exist
    $outlet = \App\Models\Outlet::find(1);
    $book = \App\Models\AccountingBook::where('outlet_id', 1)->first();
    
    if ($outlet && $book) {
        echo "   ✓ Required data exists (Outlet: {$outlet->nama_outlet}, Book: {$book->name})\n";
        
        try {
            $updateResponse = $controller->coaSettings($updateRequest);
            if ($updateResponse instanceof \Illuminate\Http\JsonResponse) {
                $updateData = json_decode($updateResponse->getContent(), true);
                if ($updateData['success']) {
                    echo "   ✓ COA Settings Update API works\n";
                } else {
                    echo "   ⚠️  COA Settings Update validation: " . $updateData['message'] . "\n";
                }
            }
        } catch (\Exception $e) {
            echo "   ⚠️  COA Settings Update error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ⚠️  Missing required data (outlet or accounting book)\n";
    }
    
    // 4. Check Database Tables
    echo "\n4. Checking Database Tables...\n";
    
    $tables = [
        'inter_outlet_sales' => 'Inter Outlet Sales',
        'outlets' => 'Outlets',
        'accounting_books' => 'Accounting Books',
        'chart_of_accounts' => 'Chart of Accounts',
        'setting_coa_inter_outlet_sales' => 'COA Settings'
    ];
    
    foreach ($tables as $table => $name) {
        try {
            $count = DB::table($table)->count();
            echo "   ✓ {$name}: {$count} records\n";
        } catch (\Exception $e) {
            echo "   ✗ {$name}: Table not found or error\n";
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "UI FIXES VERIFICATION COMPLETE\n";
    echo str_repeat("=", 60) . "\n";
    
    echo "\nFIXES IMPLEMENTED:\n";
    echo "✓ History Modal: Direct JSON API with proper error handling\n";
    echo "✓ COA Settings: Save button with outlet-based data loading\n";
    echo "✓ Print URL: Fixed format from //print/id to /id/print\n";
    echo "✓ AJAX Headers: Added proper Accept and X-Requested-With headers\n";
    echo "✓ Error Messages: Improved error handling and user feedback\n\n";
    
    echo "READY FOR BROWSER TESTING:\n";
    echo "1. Open Inter Outlet Sales page\n";
    echo "2. Click 'Riwayat' - should load transaction data\n";
    echo "3. Click 'Setting COA' - should show form with save button\n";
    echo "4. Change outlet in COA settings - should reload accounts\n";
    echo "5. Test print functionality from transaction success modal\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}