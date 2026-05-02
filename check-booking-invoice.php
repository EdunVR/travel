<?php
/**
 * Check if booking has invoice
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JamaahBooking;

$bookingCode = $argv[1] ?? 'BKG-20260416-960E';

$booking = JamaahBooking::where('booking_code', $bookingCode)
    ->with('invoice')
    ->first();

if (!$booking) {
    echo "❌ Booking not found: {$bookingCode}\n";
    exit(1);
}

echo "=== Booking Details ===\n";
echo "Booking Code: {$booking->booking_code}\n";
echo "Jamaah: {$booking->jamaah->nama}\n";
echo "Total Price: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
echo "Paid Amount: Rp " . number_format($booking->paid_amount ?? 0, 0, ',', '.') . "\n";
echo "\n";

echo "=== Invoice Status ===\n";
echo "Has id_invoice: " . ($booking->id_invoice ? "YES (ID: {$booking->id_invoice})" : "NO") . "\n";
echo "Invoice loaded: " . ($booking->invoice ? "YES" : "NO") . "\n";

if ($booking->invoice) {
    echo "Invoice Number: {$booking->invoice->no_invoice}\n";
    echo "Invoice Status: {$booking->invoice->status}\n";
    echo "Invoice Total: Rp " . number_format($booking->invoice->total, 0, ',', '.') . "\n";
    echo "Invoice Paid: Rp " . number_format($booking->invoice->total_dibayar, 0, ',', '.') . "\n";
    echo "\n";
    echo "✅ Download buttons WILL appear on booking detail page\n";
    echo "✅ 'Buat Invoice' button WILL be hidden\n";
} else {
    echo "\n";
    echo "⚠️ Download buttons WILL NOT appear\n";
    echo "⚠️ 'Buat Invoice' button WILL be shown\n";
}
