<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use Illuminate\Http\Request;
use App\Models\Production;

try {
    echo "Testing direct route call...\n";
    
    // Get a production record
    $production = Production::first();
    if (!$production) {
        echo "❌ No production records found\n";
        exit(1);
    }
    
    echo "✅ Testing with production ID: {$production->id}\n";
    
    // Create a request to the PDF route
    $request = Request::create("/admin/produksi/produksi/{$production->id}/pdf", 'GET');
    
    // Process the request
    $response = $kernel->handle($request);
    
    echo "✅ Response status: " . $response->getStatusCode() . "\n";
    echo "✅ Response headers: " . json_encode($response->headers->all()) . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "🎉 Route works successfully!\n";
    } else {
        echo "❌ Route failed with status: " . $response->getStatusCode() . "\n";
        echo "Response content: " . $response->getContent() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}