<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\InterOutletSaleController;
use Illuminate\Http\Request;

echo "=== Testing Inter Outlet Sale DataTables Fix ===\n";

try {
    // Create controller instance
    $controller = new InterOutletSaleController();
    
    // Create a mock request
    $request = new Request();
    
    // Test the historyData method
    echo "Testing historyData method...\n";
    $response = $controller->historyData($request);
    
    echo "✓ historyData method executed successfully\n";
    echo "Response type: " . get_class($response) . "\n";
    
    // Test if it's a DataTables response
    if (method_exists($response, 'getData')) {
        $data = $response->getData();
        echo "✓ DataTables response generated successfully\n";
        echo "Data structure: " . (is_object($data) ? 'Object' : gettype($data)) . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";