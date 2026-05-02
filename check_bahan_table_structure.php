<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== STRUKTUR TABEL BAHAN ===\n";
    $columns = DB::select('DESCRIBE bahan');
    foreach ($columns as $column) {
        echo $column->Field . ' - ' . $column->Type . ' - ' . $column->Null . ' - ' . $column->Key . "\n";
    }
    
    echo "\n=== SAMPLE DATA BAHAN ===\n";
    $samples = DB::table('bahan')->limit(3)->get();
    foreach ($samples as $sample) {
        echo json_encode($sample, JSON_PRETTY_PRINT) . "\n";
    }
    
    echo "\n=== STRUKTUR TABEL BAHAN_DETAIL (HARGA) ===\n";
    $detailColumns = DB::select('DESCRIBE bahan_detail');
    foreach ($detailColumns as $column) {
        echo $column->Field . ' - ' . $column->Type . ' - ' . $column->Null . ' - ' . $column->Key . "\n";
    }
    
    echo "\n=== SAMPLE DATA BAHAN_DETAIL ===\n";
    $detailSamples = DB::table('bahan_detail')->limit(3)->get();
    foreach ($detailSamples as $sample) {
        echo json_encode($sample, JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}