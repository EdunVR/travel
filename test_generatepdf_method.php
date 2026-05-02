<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\ProductionController;
use App\Models\Production;

try {
    echo "Testing ProductionController::generatePdf method...\n";
    
    // Check if method exists
    if (method_exists(ProductionController::class, 'generatePdf')) {
        echo "✅ Method exists in class\n";
    } else {
        echo "❌ Method does not exist in class\n";
        exit(1);
    }
    
    // Get a production record to test with
    $production = Production::first();
    if (!$production) {
        echo "❌ No production records found for testing\n";
        exit(1);
    }
    
    echo "✅ Found production record: {$production->id}\n";
    
    // Try to instantiate controller
    $controller = new ProductionController();
    echo "✅ Controller instantiated successfully\n";
    
    // Check if method is callable
    if (is_callable([$controller, 'generatePdf'])) {
        echo "✅ Method is callable\n";
    } else {
        echo "❌ Method is not callable\n";
        exit(1);
    }
    
    echo "🎉 All checks passed! Method should work.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}