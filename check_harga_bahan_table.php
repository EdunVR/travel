<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== CHECKING HARGA_BAHAN TABLE ===\n";
    $tables = ['harga_bahan', 'bahan_detail'];
    
    foreach ($tables as $table) {
        try {
            $columns = DB::select("DESCRIBE $table");
            echo "\n=== STRUKTUR TABEL $table ===\n";
            foreach ($columns as $column) {
                echo $column->Field . ' - ' . $column->Type . ' - ' . $column->Null . ' - ' . $column->Key . "\n";
            }
            
            echo "\n=== SAMPLE DATA $table ===\n";
            $samples = DB::table($table)->limit(3)->get();
            foreach ($samples as $sample) {
                echo json_encode($sample, JSON_PRETTY_PRINT) . "\n";
            }
        } catch (Exception $e) {
            echo "Table $table not found or error: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}