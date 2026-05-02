<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING OUTLETS ===\n\n";

$outlets = DB::table('outlets')->select('id_outlet', 'nama_outlet')->get();

if ($outlets->isEmpty()) {
    echo "No outlets found in database!\n";
} else {
    echo "Found " . $outlets->count() . " outlets:\n";
    foreach ($outlets as $outlet) {
        echo "- ID: {$outlet->id_outlet}, Name: {$outlet->nama_outlet}\n";
    }
}

echo "\n=== CHECKING COMPANY SETTINGS ===\n\n";

$settings = DB::table('company_settings')->select('id', 'outlet_id', 'company_name')->get();

if ($settings->isEmpty()) {
    echo "No company settings found in database!\n";
} else {
    echo "Found " . $settings->count() . " company settings:\n";
    foreach ($settings as $setting) {
        echo "- ID: {$setting->id}, Outlet ID: {$setting->outlet_id}, Company: {$setting->company_name}\n";
    }
}
