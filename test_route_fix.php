<?php

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING ROUTE FIX ===\n\n";

try {
    // Test the route directly
    echo "1. Testing Route Generation\n";
    echo "==========================\n";
    
    $routeUrl = route('finance.fixed-assets.assets.all');
    echo "✅ Route generated successfully: {$routeUrl}\n\n";
    
    // Test the API endpoint
    echo "2. Testing API Endpoint\n";
    echo "=======================\n";
    
    $controller = app('App\Http\Controllers\FinanceAccountantController');
    $request = new Illuminate\Http\Request(['outlet_id' => 3]);
    
    $response = $controller->getAllFixedAssets($request);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ API endpoint working successfully\n";
        echo "📊 Total assets returned: " . count($data['data']) . "\n";
        
        // Show first few assets
        echo "\n📋 Sample assets:\n";
        foreach (array_slice($data['data'], 0, 3) as $asset) {
            echo "  • {$asset['display_name']}\n";
        }
        
        echo "\n✅ Route fix is working correctly!\n";
        echo "✅ Frontend should now be able to load all assets for dropdown\n";
        
    } else {
        echo "❌ API endpoint failed: " . $data['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}