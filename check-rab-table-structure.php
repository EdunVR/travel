<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CEK STRUKTUR TABEL RAB_TEMPLATE ===\n\n";

$columns = DB::select("SHOW COLUMNS FROM rab_template");

echo "Kolom-kolom di tabel rab_template:\n";
foreach ($columns as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

echo "\n=== CEK DATA RAB_TEMPLATE ===\n\n";

$rabs = DB::table('rab_template')->get();

if ($rabs->isEmpty()) {
    echo "Tidak ada data di tabel rab_template\n";
} else {
    echo "Total RAB: " . $rabs->count() . "\n\n";
    foreach ($rabs as $rab) {
        echo "RAB:\n";
        foreach ($rab as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
        echo "\n";
    }
}

echo "=== SELESAI ===\n";
