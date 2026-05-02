<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Available outlets with fixed assets:\n";
$outlets = DB::table('fixed_assets')
    ->join('outlets', 'fixed_assets.outlet_id', '=', 'outlets.id_outlet')
    ->select('outlets.id_outlet', 'outlets.nama_outlet', DB::raw('COUNT(*) as asset_count'))
    ->groupBy('outlets.id_outlet', 'outlets.nama_outlet')
    ->get();

foreach ($outlets as $outlet) {
    echo "Outlet ID: {$outlet->id_outlet}, Name: {$outlet->nama_outlet}, Assets: {$outlet->asset_count}\n";
}