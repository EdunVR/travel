<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TravelPackage;

echo "===========================================\n";
echo "CHECK ALL PACKAGES HANDLING FEE STATUS\n";
echo "===========================================\n\n";

$packages = TravelPackage::all();

echo "Total packages: " . $packages->count() . "\n\n";

$enabledCount = 0;
$disabledCount = 0;

foreach ($packages as $package) {
    $enabled = $package->include_handling_lounge_fee;
    $amount = $package->handling_lounge_fee_amount ?? 0;
    
    if ($enabled) {
        $enabledCount++;
        $status = "✅ ENABLED";
    } else {
        $disabledCount++;
        $status = "❌ DISABLED";
    }
    
    echo "{$package->id}. {$package->package_name}\n";
    echo "   Status: {$status}\n";
    echo "   Amount: Rp " . number_format($amount, 0, ',', '.') . "\n";
    echo "   Description: " . ($package->handling_lounge_fee_description ?? 'NULL') . "\n";
    
    if ($enabled && $amount > 0) {
        echo "   📦 Box kuning AKAN MUNCUL\n";
    } else {
        echo "   ⚠️  Box kuning TIDAK MUNCUL\n";
    }
    echo "\n";
}

echo "===========================================\n";
echo "SUMMARY:\n";
echo "===========================================\n";
echo "Total packages: " . $packages->count() . "\n";
echo "Enabled: {$enabledCount}\n";
echo "Disabled: {$disabledCount}\n\n";

if ($disabledCount > 0) {
    echo "⚠️  Ada {$disabledCount} paket yang handling fee-nya belum diaktifkan\n";
    echo "Untuk mengaktifkan:\n";
    echo "1. Buka halaman edit paket di admin\n";
    echo "2. Scroll ke bagian 'Handling & Lounge Fee'\n";
    echo "3. Centang checkbox 'Aktifkan Handling & Lounge Fee'\n";
    echo "4. Isi nominal dan deskripsi\n";
    echo "5. Simpan\n";
}
