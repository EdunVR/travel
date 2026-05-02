<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PosController;
use Illuminate\Http\Request;
use App\Models\User;

echo "=== TESTING POS IMAGE URL FIX ===\n\n";

// Login
$user = User::first();
if ($user) {
    Auth::login($user);
    
    // Test POS API
    $controller = new PosController(app(\App\Services\JournalEntryService::class));
    $request = new Request(['outlet_id' => 1]);
    
    // Clear cache first to get fresh data
    Cache::forget("pos_products_optimized_outlet_1");
    
    $response = $controller->getProducts($request);
    $data = $response->getData(true);
    
    echo "API Response:\n";
    echo "- Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "- Products count: " . count($data['data']) . "\n";
    
    if (count($data['data']) > 0) {
        echo "\nProduct with image:\n";
        foreach ($data['data'] as $product) {
            if ($product['image']) {
                echo "- Product: {$product['name']}\n";
                echo "- Image URL: {$product['image']}\n";
                
                // Test if URL is accessible
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $product['image']);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                echo "- HTTP Status: {$httpCode}\n";
                
                if ($httpCode == 200) {
                    echo "✅ Image URL is accessible\n";
                } else {
                    echo "❌ Image URL not accessible\n";
                }
                break; // Test only first image
            }
        }
        
        if (!isset($product['image']) || !$product['image']) {
            echo "ℹ️ No products with images found\n";
        }
    } else {
        echo "❌ No products returned\n";
    }
    
} else {
    echo "❌ No user found for testing\n";
}

echo "\n=== TEST COMPLETE ===\n";