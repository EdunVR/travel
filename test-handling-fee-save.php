<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TravelPackage;

echo "===========================================\n";
echo "TEST HANDLING FEE SAVE\n";
echo "===========================================\n\n";

// Get first package
$package = TravelPackage::first();

if (!$package) {
    echo "❌ No packages found in database\n";
    exit(1);
}

echo "Testing with package: {$package->package_name} (ID: {$package->id})\n\n";

// Show current state
echo "BEFORE UPDATE:\n";
echo "  include_handling_lounge_fee: " . ($package->include_handling_lounge_fee ? 'true' : 'false') . "\n";
echo "  handling_lounge_fee_amount: Rp " . number_format($package->handling_lounge_fee_amount ?? 0, 0, ',', '.') . "\n";
echo "  handling_lounge_fee_description: " . ($package->handling_lounge_fee_description ?? 'NULL') . "\n\n";

// Update handling fee
echo "UPDATING...\n";
$package->update([
    'include_handling_lounge_fee' => true,
    'handling_lounge_fee_amount' => 750000,
    'handling_lounge_fee_description' => 'Handling & Lounge Fee Wajib (Test)',
]);

// Reload from database
$package->refresh();

echo "AFTER UPDATE:\n";
echo "  include_handling_lounge_fee: " . ($package->include_handling_lounge_fee ? 'true' : 'false') . "\n";
echo "  handling_lounge_fee_amount: Rp " . number_format($package->handling_lounge_fee_amount ?? 0, 0, ',', '.') . "\n";
echo "  handling_lounge_fee_description: " . ($package->handling_lounge_fee_description ?? 'NULL') . "\n\n";

// Check if it will display
if ($package->include_handling_lounge_fee && $package->handling_lounge_fee_amount > 0) {
    echo "✅ SUCCESS! Box kuning AKAN MUNCUL di halaman detail paket\n";
} else {
    echo "❌ FAILED! Box kuning TIDAK AKAN MUNCUL\n";
}

echo "\n===========================================\n";
echo "NEXT STEPS:\n";
echo "===========================================\n";
echo "1. Buka halaman detail paket: /paket/{$package->id}\n";
echo "2. Scroll ke bawah untuk melihat box kuning\n";
echo "3. Jika muncul, berarti fix berhasil!\n\n";
