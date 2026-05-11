<?php
/**
 * Check payment verification data
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Payment Verification Data...\n\n";

// Get payments with relationships
$payments = \App\Models\JamaahPayment::with(['booking.jamaah', 'booking.travelPackage', 'verifiedBy'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

echo "Total Payments: " . \App\Models\JamaahPayment::count() . "\n";
echo "Pending Verification: " . \App\Models\JamaahPayment::where('verification_status', 'pending')->count() . "\n\n";

if ($payments->isEmpty()) {
    echo "❌ No payments found\n";
    exit;
}

echo "Sample Payment Data:\n";
echo "===================\n\n";

foreach ($payments as $payment) {
    echo "Payment ID: {$payment->id}\n";
    echo "Amount: Rp " . number_format($payment->amount, 0, ',', '.') . "\n";
    echo "Status: {$payment->verification_status}\n";
    echo "Created: {$payment->created_at}\n";
    
    // Check booking
    if ($payment->booking) {
        echo "Booking Code: {$payment->booking->booking_code}\n";
        
        // Check jamaah
        if ($payment->booking->jamaah) {
            echo "Jamaah Name: {$payment->booking->jamaah->full_name}\n";
            echo "Jamaah Phone: {$payment->booking->jamaah->phone_number}\n";
        } else {
            echo "❌ Jamaah data NOT FOUND\n";
        }
        
        // Check package
        if ($payment->booking->travelPackage) {
            echo "Package: {$payment->booking->travelPackage->package_name}\n";
        } else {
            echo "❌ Package data NOT FOUND\n";
        }
    } else {
        echo "❌ Booking data NOT FOUND\n";
    }
    
    echo "Payment Proof: " . ($payment->payment_proof ? "✅ Yes" : "❌ No") . "\n";
    echo "\n---\n\n";
}

// Check relationships in JamaahBooking model
echo "\nChecking JamaahBooking relationships...\n";
$booking = \App\Models\JamaahBooking::with('jamaah')->first();
if ($booking) {
    echo "Booking ID: {$booking->id}\n";
    echo "Jamaah ID: {$booking->jamaah_id}\n";
    if ($booking->jamaah) {
        echo "✅ Jamaah relationship works\n";
        echo "Jamaah Name: {$booking->jamaah->full_name}\n";
    } else {
        echo "❌ Jamaah relationship NOT working\n";
    }
}
