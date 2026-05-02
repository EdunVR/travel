<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $result = DB::select("SHOW COLUMNS FROM sales_invoice WHERE Field = 'status'");
    
    if (!empty($result)) {
        echo "Column 'status' details:\n";
        echo "Type: " . $result[0]->Type . "\n";
        echo "Null: " . $result[0]->Null . "\n";
        echo "Default: " . $result[0]->Default . "\n";
        
        // Extract enum values
        if (preg_match("/^enum\((.+)\)$/", $result[0]->Type, $matches)) {
            $enumValues = str_getcsv($matches[1], ',', "'");
            echo "\nValid enum values:\n";
            foreach ($enumValues as $value) {
                echo "  - '$value'\n";
            }
        }
    } else {
        echo "Column 'status' not found\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
