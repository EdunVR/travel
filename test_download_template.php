<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Simulate HTTP request
$request = Illuminate\Http\Request::create(
    '/admin/crm/pelanggan/download-template',
    'GET'
);

try {
    echo "Testing download template route...\n\n";
    
    $response = $kernel->handle($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content Type: " . $response->headers->get('Content-Type') . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "✓ Route is accessible\n";
        
        $contentDisposition = $response->headers->get('Content-Disposition');
        if ($contentDisposition) {
            echo "✓ File download header present: " . $contentDisposition . "\n";
        }
        
        $contentLength = $response->headers->get('Content-Length');
        if ($contentLength) {
            echo "✓ File size: " . number_format($contentLength) . " bytes\n";
        }
        
        echo "\n✓ Download template is working!\n";
    } else {
        echo "✗ Route returned status: " . $response->getStatusCode() . "\n";
        echo "Response: " . $response->getContent() . "\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

$kernel->terminate($request, $response);
