<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\PreOrderController;
use App\Services\PreOrderJournalService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING PRE ORDER HTTP REQUEST ===\n\n";

try {
    // Create a mock request
    $requestData = [
        'customer_id' => 1,
        'tanggal' => '2024-12-11',
        'items' => [
            [
                'produk_id' => null,
                'deskripsi' => 'Test Product 1',
                'qty' => 2,
                'harga' => 100000
            ]
        ],
        'diskon' => 0,
        'pajak' => 0,
        'catatan' => 'Test pre order'
    ];

    // Create request instance
    $request = new Request();
    $request->merge($requestData);
    $request->setMethod('POST');

    // Create controller instance
    $journalService = new PreOrderJournalService();
    $controller = new PreOrderController($journalService);

    echo "1. Testing controller store method...\n";
    $response = $controller->store($request);
    
    if ($response->getStatusCode() === 200) {
        $data = json_decode($response->getContent(), true);
        if ($data['success']) {
            echo "   ✓ Pre Order created successfully\n";
            echo "   Response: " . $response->getContent() . "\n";
        } else {
            echo "   ✗ Pre Order creation failed\n";
            echo "   Error: " . $data['message'] . "\n";
        }
    } else {
        echo "   ✗ HTTP Error: " . $response->getStatusCode() . "\n";
        echo "   Response: " . $response->getContent() . "\n";
    }

} catch (Exception $e) {
    echo "   ✗ Exception: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";