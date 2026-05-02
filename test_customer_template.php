<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing CustomerTemplateExport...\n\n";
    
    // Check if class exists
    if (class_exists('App\Exports\CustomerTemplateExport')) {
        echo "✓ CustomerTemplateExport class exists\n";
        
        // Try to instantiate
        $export = new \App\Exports\CustomerTemplateExport();
        echo "✓ CustomerTemplateExport can be instantiated\n";
        
        // Check methods
        if (method_exists($export, 'collection')) {
            echo "✓ collection() method exists\n";
        }
        if (method_exists($export, 'headings')) {
            echo "✓ headings() method exists\n";
        }
        if (method_exists($export, 'styles')) {
            echo "✓ styles() method exists\n";
        }
        if (method_exists($export, 'columnWidths')) {
            echo "✓ columnWidths() method exists\n";
        }
        
        echo "\n✓ All checks passed!\n";
        
    } else {
        echo "✗ CustomerTemplateExport class NOT found\n";
        echo "File location should be: app/Exports/CustomerTemplateExport.php\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
