<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TravelPackage;

echo "===========================================\n";
echo "CHECK HANDLING FEE DISPLAY\n";
echo "===========================================\n\n";

$packages = TravelPackage::all();

echo "Total packages: " . $packages->count() . "\n\n";

foreach ($packages as $package) {
    echo "Package: {$package->package_name}\n";
    echo "  ID: {$package->id}\n";
    echo "  handling_fee_enabled: " . ($package->handling_fee_enabled ? 'YES' : 'NO') . "\n";
    echo "  handling_fee_amount: Rp " . number_format($package->handling_fee_amount ?? 0, 0, ',', '.') . "\n";
    echo "  handling_fee_description: " . ($package->handling_fee_description ?? 'NULL') . "\n";
    
    if ($package->handling_fee_enabled && $package->handling_fee_amount > 0) {
        echo "  ✓ Box kuning AKAN MUNCUL\n";
    } else {
        echo "  ✗ Box kuning TIDAK MUNCUL (handling fee tidak aktif)\n";
    }
    echo "\n";
}

echo "===========================================\n";
echo "CARA MENGAKTIFKAN:\n";
echo "===========================================\n";
echo "1. Buka halaman edit paket di admin\n";
echo "2. Scroll ke bagian 'Handling & Lounge Fee'\n";
echo "3. Centang checkbox 'Aktifkan Handling Fee'\n";
echo "4. Isi nominal (contoh: 500000)\n";
echo "5. Isi deskripsi (opsional)\n";
echo "6. Simpan\n\n";
