<?php
/**
 * Test payment verification data with correct relationships
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Payment Verification Data (Fixed)...\n\n";

// Get payments with correct relationships
$payments = \App\Models\JamaahPayment::with(['booking.jamaah', 'booking.travelPackage', 'verifiedBy'])
    ->where('verification_status', 'pending_verification')
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get();

echo "Pending Payments: " . $payments->count() . "\n\n";

if ($payments->isEmpty()) {
    echo "❌ No pending payments found\n";
    
    // Show all payments
    $allPayments = \App\Models\JamaahPayment::all();
    echo "\nAll Payments Status:\n";
    foreach ($allPayments as $p) {
        echo "- Payment {$p->id}: {$p->verification_status}\n";
    }
    exit;
}

echo "Payment Data:\n";
echo "=============\n\n";

foreach ($payments as $payment) {
    echo "Payment ID: {$payment->id}\n";
    echo "Amount: Rp " . number_format($payment->amount, 0, ',', '.') . "\n";
    echo "Status: {$payment->verification_status}\n";
    echo "Created: {$payment->created_at->format('d M Y H:i')}\n";
    
    // Check booking
    if ($payment->booking) {
        echo "✅ Booking: {$payment->booking->booking_code}\n";
        
        // Check jamaah
        if ($payment->booking->jamaah) {
            echo "✅ Jamaah: {$payment->booking->jamaah->full_name}\n";
            echo "   Phone: {$payment->booking->jamaah->phone_number}\n";
        } else {
            echo "❌ Jamaah: NOT FOUND (id_member: {$payment->booking->id_member})\n";
        }
        
        // Check package
        if ($payment->booking->travelPackage) {
            echo "✅ Package: {$payment->booking->travelPackage->package_name}\n";
        } else {
            echo "❌ Package: NOT FOUND\n";
        }
    } else {
        echo "❌ Booking: NOT FOUND\n";
    }
    
    echo "Bukti Transfer: " . ($payment->bukti_transfer ? "✅ {$payment->bukti_transfer}" : "❌ No") . "\n";
    echo "\n---\n\n";
}

// Test if we can update status
echo "\nTesting status values:\n";
echo "- 'pending' payments: " . \App\Models\JamaahPayment::where('verification_status', 'pending')->count() . "\n";
echo "- 'pending_verification' payments: " . \App\Models\JamaahPayment::where('verification_status', 'pending_verification')->count() . "\n";
echo "- 'verified' payments: " . \App\Models\JamaahPayment::where('verification_status', 'verified')->count() . "\n";
