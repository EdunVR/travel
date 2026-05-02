<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING PIUTANG AUTO INCREMENT ===\n\n";

try {
    // Check current structure
    echo "Checking current structure...\n";
    $columns = DB::select("SHOW COLUMNS FROM piutang WHERE Field = 'id_piutang'");
    
    if (!empty($columns)) {
        $column = $columns[0];
        echo "Current Extra: " . ($column->Extra ?? 'none') . "\n";
        
        if (strpos($column->Extra ?? '', 'auto_increment') === false) {
            echo "\n✗ AUTO_INCREMENT is MISSING\n";
            echo "Adding AUTO_INCREMENT...\n\n";
            
            // Add AUTO_INCREMENT
            DB::statement('ALTER TABLE piutang MODIFY id_piutang BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
            
            echo "✓ AUTO_INCREMENT added successfully!\n\n";
            
            // Verify
            $verify = DB::select("SHOW COLUMNS FROM piutang WHERE Field = 'id_piutang'");
            if (!empty($verify)) {
                echo "Verified Extra: " . ($verify[0]->Extra ?? 'none') . "\n";
            }
        } else {
            echo "✓ AUTO_INCREMENT already exists\n";
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
