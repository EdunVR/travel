<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TravelPackage;

echo "===========================================\n";
echo "TEST HANDLING FEE IN PRICE DISPLAY\n";
echo "===========================================\n\n";

// Get first package with price_packages
$package = TravelPackage::whereNotNull('price_packages')->first();

if (!$package) {
    echo "❌ No packages with price_packages found\n";
    exit(1);
}

echo "Testing with package: {$package->package_name} (ID: {$package->id})\n\n";

// Show handling fee status
echo "HANDLING FEE:\n";
echo "  Enabled: " . ($package->include_handling_lounge_fee ? 'YES' : 'NO') . "\n";
echo "  Amount: Rp " . number_format($package->handling_lounge_fee_amount ?? 0, 0, ',', '.') . "\n";
echo "  Description: " . ($package->handling_lounge_fee_description ?? 'Handling & Lounge Fee Wajib') . "\n\n";

// Show price packages
echo "PRICE PACKAGES:\n";
$pricePackages = $package->price_packages ?? [];
if (is_array($pricePackages) && count($pricePackages) > 0) {
    foreach ($pricePackages as $pkg) {
        echo "  Package: " . ($pkg['name'] ?? 'Unknown') . "\n";
        if (!empty($pkg['variants'])) {
            foreach ($pkg['variants'] as $variant) {
                $basePrice = $variant['price'] ?? 0;
                $handlingFee = $package->include_handling_lounge_fee ? ($package->handling_lounge_fee_amount ?? 0) : 0;
                $totalPrice = $basePrice + $handlingFee;
                
                echo "    - " . ($variant['type'] ?? 'Unknown') . ":\n";
                echo "      Base Price: Rp " . number_format($basePrice, 0, ',', '.') . "\n";
                echo "      Handling Fee: Rp " . number_format($handlingFee, 0, ',', '.') . "\n";
                echo "      TOTAL DISPLAYED: Rp " . number_format($totalPrice, 0, ',', '.') . "\n";
            }
        }
        echo "\n";
    }
} else {
    echo "  No price packages found\n\n";
    
    // Show single price
    $basePrice = $package->price ?? 0;
    $handlingFee = $package->include_handling_lounge_fee ? ($package->handling_lounge_fee_amount ?? 0) : 0;
    $totalPrice = $basePrice + $handlingFee;
    
    echo "SINGLE PRICE:\n";
    echo "  Base Price: Rp " . number_format($basePrice, 0, ',', '.') . "\n";
    echo "  Handling Fee: Rp " . number_format($handlingFee, 0, ',', '.') . "\n";
    echo "  TOTAL DISPLAYED: Rp " . number_format($totalPrice, 0, ',', '.') . "\n\n";
}

echo "===========================================\n";
echo "EXPECTED BEHAVIOR:\n";
echo "===========================================\n";
echo "✅ Harga yang ditampilkan di website = Base Price + Handling Fee\n";
echo "✅ User melihat harga final langsung, tidak ada biaya tersembunyi\n";
echo "✅ Box kuning menjelaskan bahwa handling fee SUDAH TERMASUK\n\n";

echo "TESTING:\n";
echo "1. Buka halaman detail paket: /paket/{$package->id}\n";
echo "2. Lihat harga yang ditampilkan\n";
echo "3. Scroll ke bawah, lihat box kuning handling fee\n";
echo "4. Pastikan teks mengatakan 'sudah termasuk dalam harga'\n\n";
