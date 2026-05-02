<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Outlet Prefix untuk POS ===\n\n";

$outlets = DB::table('outlets')->where('is_active', true)->get(['id_outlet', 'nama_outlet']);

echo "Outlets dan prefix yang akan digunakan:\n";
foreach($outlets as $outlet) {
    $prefix = strtoupper(substr($outlet->nama_outlet, 0, 3));
    echo "- ID: {$outlet->id_outlet} | Name: {$outlet->nama_outlet} | Prefix: {$prefix}\n";
}

echo "\nContoh nomor transaksi yang akan dibuat:\n";
foreach($outlets as $outlet) {
    $prefix = strtoupper(substr($outlet->nama_outlet, 0, 3));
    $date = now();
    $month = $date->format('m');
    $year = $date->format('Y');
    $example = sprintf('0001/%s/POS/%s/%s', $prefix, $month, $year);
    echo "- Outlet {$outlet->nama_outlet}: {$example}\n";
}

echo "\n=== Test Complete ===\n";