<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== JAMAAH PAYMENTS WITH BUKTI ===\n";
$payments = DB::table('jamaah_payments')
    ->where('id_jamaah_booking', 31)
    ->get();

foreach ($payments as $payment) {
    echo "ID: {$payment->id}\n";
    echo "Booking: {$payment->id_jamaah_booking}\n";
    echo "Jumlah: Rp " . number_format($payment->amount ?? 0, 0, ',', '.') . "\n";
    echo "Bukti Transfer: {$payment->bukti_transfer}\n";
    echo "Payment Method: {$payment->payment_method}\n";
    echo "---\n";
}

echo "\n=== CHECK FILE EXISTS ===\n";
foreach ($payments as $payment) {
    if ($payment->bukti_transfer) {
        $fullPath = storage_path('app/public/' . $payment->bukti_transfer);
        $publicPath = public_path('storage/' . $payment->bukti_transfer);
        
        echo "Database path: {$payment->bukti_transfer}\n";
        echo "Storage path: {$fullPath}\n";
        echo "Storage exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        echo "Public path: {$publicPath}\n";
        echo "Public exists: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
        echo "---\n";
    }
}
