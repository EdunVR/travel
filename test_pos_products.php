<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Test dengan outlet_id
$outletId = 1; // Ganti sesuai outlet yang ada

$request = Illuminate\Http\Request::create(
    '/admin/penjualan/pos/products?outlet_id=' . $outletId,
    'GET'
);

try {
    echo "Testing POS Products API...\n";
    echo "Outlet ID: {$outletId}\n\n";
    
    $response = $kernel->handle($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content Type: " . $response->headers->get('Content-Type') . "\n\n";
    
    if ($response->getStatusCode() === 200) {
        $content = $response->getContent();
        $data = json_decode($content, true);
        
        if ($data && isset($data['success'])) {
            echo "✓ Response is valid JSON\n";
            echo "✓ Success: " . ($data['success'] ? 'true' : 'false') . "\n";
            
            if (isset($data['data'])) {
                echo "✓ Products count: " . count($data['data']) . "\n";
                
                if (count($data['data']) > 0) {
                    echo "\nSample product:\n";
                    print_r($data['data'][0]);
                }
            }
        } else {
            echo "✗ Invalid JSON structure\n";
            echo "Response: " . substr($content, 0, 200) . "...\n";
        }
    } else {
        echo "✗ HTTP Error: " . $response->getStatusCode() . "\n";
        echo "Response: " . substr($response->getContent(), 0, 500) . "\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

$kernel->terminate($request, $response);
