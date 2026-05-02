<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\ProductionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING PRODUCTION STORE AFTER FIX ===\n\n";

try {
    // Create a test user session
    $user = DB::table('users')->first();
    if (!$user) {
        echo "❌ No users found in database\n";
        exit(1);
    }
    
    Auth::loginUsingId($user->id);
    echo "✅ Authenticated as user: {$user->name}\n\n";
    
    // Get test data
    $outlet = DB::table('outlets')->first();
    $product = DB::table('produk')->first();
    $bahan = DB::table('bahan')->first();
    
    if (!$outlet || !$product || !$bahan) {
        echo "❌ Missing test data (outlet, product, or bahan)\n";
        exit(1);
    }
    
    echo "📋 Test Data:\n";
    echo "   Outlet: {$outlet->nama_outlet} (ID: {$outlet->id_outlet})\n";
    echo "   Product: {$product->nama_produk} (ID: {$product->id_produk})\n";
    echo "   Material: {$bahan->nama_bahan} (ID: {$bahan->id_bahan})\n\n";
    
    // Create test request data (similar to the error log)
    $requestData = [
        '_token' => 'test_token',
        'outlet_id' => $outlet->id_outlet,
        'production_code' => null,
        'products' => [
            [
                'product_id' => $product->id_produk,
                'target_quantity' => 10,
                'sample_quantity' => 3
            ]
        ],
        'production_line' => 'Lini A',
        'target_quantity' => 10,
        'start_date' => '2026-01-18',
        'end_date' => '2026-01-18',
        'expiry_date' => '2026-01-18',
        'priority' => 'normal',
        'business_type' => null,
        'materials' => [
            [
                'material_type' => 'bahan',
                'material_id' => $bahan->id_bahan,
                'quantity' => 10,
                'unit' => 'Unit'
            ]
        ],
        'labor_costs' => [
            'worker_count' => 10,
            'cost_per_worker' => 10000,
            'total_cost' => 100000
        ],
        'operational_costs' => [
            [
                'cost_type' => 'listrik',
                'amount' => 10000,
                'description' => null
            ]
        ]
    ];
    
    echo "🧪 Testing production creation...\n";
    
    // Create request object
    $request = new Request();
    $request->merge($requestData);
    
    // Create controller and call store method
    $controller = new ProductionController();
    $response = $controller->store($request);
    
    // Check response
    $responseData = json_decode($response->getContent(), true);
    
    if ($response->getStatusCode() === 200 && $responseData['success']) {
        echo "✅ SUCCESS: Production created successfully!\n";
        echo "   Production ID: {$responseData['data']['id']}\n";
        echo "   Production Code: {$responseData['data']['production_code']}\n";
        echo "   Target Quantity: {$responseData['data']['target_quantity']}\n";
        echo "   Status: {$responseData['data']['status']}\n\n";
        
        // Verify database records
        $production = DB::table('productions')->where('id', $responseData['data']['id'])->first();
        echo "🔍 Database Verification:\n";
        echo "   Production record created: " . ($production ? "✅ YES" : "❌ NO") . "\n";
        echo "   Product ID in production: " . ($production->product_id ?? 'NULL') . " (should be NULL)\n";
        
        $hppRecords = DB::table('hpp_produk')->where('production_id', $responseData['data']['id'])->count();
        echo "   HPP records created: {$hppRecords}\n";
        
        $materialRecords = DB::table('production_materials')->where('production_id', $responseData['data']['id'])->count();
        echo "   Material records created: {$materialRecords}\n";
        
        $laborRecords = DB::table('production_labor_costs')->where('production_id', $responseData['data']['id'])->count();
        echo "   Labor cost records created: {$laborRecords}\n";
        
        $operationalRecords = DB::table('production_operational_costs')->where('production_id', $responseData['data']['id'])->count();
        echo "   Operational cost records created: {$operationalRecords}\n";
        
    } else {
        echo "❌ FAILED: Production creation failed\n";
        echo "   Status Code: {$response->getStatusCode()}\n";
        echo "   Response: " . $response->getContent() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";