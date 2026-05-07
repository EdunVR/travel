<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TravelPackage;

echo "=== UPDATE DATA USTADZ ===\n\n";

// Daftar ustadz yang bisa dipilih
$ustadzList = [
    'Ustadz Heykal Syaban',
    'Ustadz Ahmad Zainuddin',
    'Ustadz Muhammad Ridwan',
    'Ustadz Abdul Rahman',
    'Ustadz Faisal Hakim',
];

// Ambil paket yang belum ada ustadz
$packagesWithoutUstadz = TravelPackage::where(function($q) {
    $q->whereNull('ustadz_name')
      ->orWhere('ustadz_name', '');
})->get();

echo "Ditemukan {$packagesWithoutUstadz->count()} paket tanpa ustadz\n\n";

$updated = 0;
foreach ($packagesWithoutUstadz as $package) {
    // Pilih ustadz secara acak
    $randomUstadz = $ustadzList[array_rand($ustadzList)];
    
    $package->ustadz_name = $randomUstadz;
    $package->save();
    
    echo "✓ Updated: {$package->package_name} → {$randomUstadz}\n";
    $updated++;
}

echo "\n=== SELESAI ===\n";
echo "Total paket yang diupdate: {$updated}\n";

// Verifikasi
$remaining = TravelPackage::where(function($q) {
    $q->whereNull('ustadz_name')
      ->orWhere('ustadz_name', '');
})->count();

echo "Paket yang masih kosong: {$remaining}\n";

if ($remaining == 0) {
    echo "\n✅ Semua paket sudah memiliki data ustadz!\n";
    echo "Silakan refresh halaman homepage untuk melihat hasilnya.\n";
}
