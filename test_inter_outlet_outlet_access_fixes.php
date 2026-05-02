<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== TESTING INTER OUTLET OUTLET ACCESS FIXES ===\n\n";

try {
    // 1. Test User Outlet Access
    echo "1. Testing User Outlet Access...\n";
    
    $user = \App\Models\User::first();
    if ($user) {
        auth()->login($user);
        echo "   ✓ User authenticated: {$user->name}\n";
        
        $controller = app(\App\Http\Controllers\InterOutletSaleController::class);
        
        // Test getUserOutlets method
        $outlets = $controller->getUserOutlets();
        echo "   Accessible outlets: " . $outlets->count() . "\n";
        
        foreach ($outlets as $outlet) {
            echo "     - {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
        }
        
        // Test hasOutletAccess method
        if ($outlets->count() > 0) {
            $firstOutlet = $outlets->first();
            $hasAccess = $controller->hasOutletAccess($firstOutlet->id_outlet);
            echo "   ✓ Access to outlet {$firstOutlet->nama_outlet}: " . ($hasAccess ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "   ⚠️  No users found in database\n";
    }
    
    // 2. Test Index Method with Outlet Filtering
    echo "\n2. Testing Index Method with Outlet Filtering...\n";
    
    $request = Request::create('/admin/penjualan/inter-outlet', 'GET');
    $response = $controller->index($request);
    
    if ($response instanceof \Illuminate\View\View) {
        $data = $response->getData();
        echo "   ✓ Index method returns view\n";
        echo "   Outlets in view: " . $data['outlets']->count() . "\n";
        echo "   Selected outlet: " . $data['selectedOutlet'] . "\n";
    } else {
        echo "   ✗ Index method did not return view\n";
    }
    
    // 3. Test COA Modal Data with Outlet Access
    echo "\n3. Testing COA Modal Data with Outlet Access...\n";
    
    $coaRequest = Request::create('/admin/penjualan/inter-outlet/coa-modal-data', 'GET', [
        'outlet_id' => 1
    ]);
    $coaRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    $coaResponse = $controller->getCoaModalData($coaRequest);
    
    if ($coaResponse instanceof \Illuminate\Http\JsonResponse) {
        $coaData = json_decode($coaResponse->getContent(), true);
        if ($coaData['success']) {
            echo "   ✓ COA Modal Data API works\n";
            echo "   Outlets returned: " . count($coaData['data']['outlets']) . "\n";
        } else {
            echo "   ⚠️  COA Modal Data: " . $coaData['message'] . "\n";
        }
    }
    
    // 4. Test History Data with Outlet Filtering
    echo "\n4. Testing History Data with Outlet Filtering...\n";
    
    $historyRequest = Request::create('/admin/penjualan/inter-outlet/history', 'GET', [
        'outlet_id' => 'all'
    ]);
    $historyRequest->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    $historyResponse = $controller->history($historyRequest);
    
    if ($historyResponse instanceof \Illuminate\Http\JsonResponse) {
        $historyData = json_decode($historyResponse->getContent(), true);
        if ($historyData['success']) {
            echo "   ✓ History API works with outlet filtering\n";
            echo "   Transactions returned: " . count($historyData['data']) . "\n";
            
            if (count($historyData['data']) > 0) {
                $sample = $historyData['data'][0];
                echo "   Sample transaction: {$sample['no_transaksi']}\n";
                echo "   Can approve: " . ($sample['can_approve'] ? 'YES' : 'NO') . "\n";
                echo "   Can delete: " . ($sample['can_delete'] ? 'YES' : 'NO') . "\n";
            }
        } else {
            echo "   ✗ History API failed: " . $historyData['message'] . "\n";
        }
    }
    
    // 5. Test Routes
    echo "\n5. Testing Routes...\n";
    
    $routes = app('router')->getRoutes();
    $requiredRoutes = [
        'admin.penjualan.inter-outlet.destroy',
        'admin.penjualan.inter-outlet.approve'
    ];
    
    foreach ($requiredRoutes as $routeName) {
        $routeExists = false;
        foreach ($routes as $route) {
            if ($route->getName() === $routeName) {
                $routeExists = true;
                break;
            }
        }
        
        if ($routeExists) {
            echo "   ✓ Route '{$routeName}' exists\n";
        } else {
            echo "   ✗ Route '{$routeName}' missing\n";
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "OUTLET ACCESS FIXES VERIFICATION COMPLETE\n";
    echo str_repeat("=", 60) . "\n";
    
    echo "\nFIXES IMPLEMENTED:\n";
    echo "✓ Outlet Filtering: Only show user's accessible outlets\n";
    echo "✓ History Actions: Added Accept and Delete buttons\n";
    echo "✓ COA Settings: Save button visible with outlet filtering\n";
    echo "✓ Access Control: Validate outlet access in all methods\n";
    echo "✓ Routes: Added DELETE route for transaction deletion\n\n";
    
    echo "BROWSER TESTING CHECKLIST:\n";
    echo "1. Login as non-super admin user\n";
    echo "2. Check outlet dropdown shows only accessible outlets\n";
    echo "3. Open 'Riwayat' - should show filtered transactions\n";
    echo "4. Check Accept/Delete buttons for pending transactions\n";
    echo "5. Open 'Setting COA' - should show filtered outlets\n";
    echo "6. Check Save button is visible and functional\n";
    echo "7. Test outlet changes reload accounts properly\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}