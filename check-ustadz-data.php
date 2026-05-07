<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== CEK DATA USTADZ DI PAKET ===\n\n";

$packages = TravelPackage::orderBy('id', 'desc')->limit(10)->get();

echo "Total paket (10 terakhir): " . $packages->count() . "\n\n";

foreach ($packages as $package) {
    echo "ID: {$package->id}\n";
    echo "Nama Paket: {$package->package_name}\n";
    echo "Ustadz: " . ($package->ustadz_name ?: '(KOSONG)') . "\n";
    echo "---\n";
}

echo "\n=== STATISTIK ===\n";
$totalPackages = TravelPackage::count();
$packagesWithUstadz = TravelPackage::whereNotNull('ustadz_name')->where('ustadz_name', '!=', '')->count();
$packagesWithoutUstadz = $totalPackages - $packagesWithUstadz;

echo "Total semua paket: {$totalPackages}\n";
echo "Paket dengan ustadz: {$packagesWithUstadz}\n";
echo "Paket tanpa ustadz: {$packagesWithoutUstadz}\n";

if ($packagesWithoutUstadz > 0) {
    echo "\n⚠️ Ada {$packagesWithoutUstadz} paket yang belum memiliki data ustadz!\n";
    echo "Silakan isi data ustadz melalui admin panel atau jalankan script update.\n";
}
