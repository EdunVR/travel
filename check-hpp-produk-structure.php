<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking hpp_produk Table Structure ===\n\n";

try {
    // Get table structure
    $columns = DB::select("SHOW COLUMNS FROM hpp_produk");
    
    echo "Columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field}: {$column->Type} | Key: {$column->Key} | Extra: {$column->Extra}\n";
    }
    
    echo "\n";
    
    // Get indexes
    $indexes = DB::select("SHOW INDEXES FROM hpp_produk");
    
    echo "Indexes:\n";
    foreach ($indexes as $index) {
        echo "  - {$index->Key_name}: Column={$index->Column_name}, Unique={$index->Non_unique}\n";
    }
    
    echo "\n";
    
    // Get CREATE TABLE statement
    $result = DB::select("SHOW CREATE TABLE hpp_produk");
    echo "CREATE TABLE statement:\n";
    echo $result[0]->{'Create Table'} . "\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}
