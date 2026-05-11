<?php
/**
 * Test invoice redirect logic
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Invoice Redirect Logic...\n\n";

// Test Case 1: Booking dengan payment pending_verification
$booking1 = \App\Models\JamaahBooking::find(48);
if ($booking1) {
    echo "Test Case 1: Booking {$booking1->booking_code}\n";
    echo "Package ID: {$booking1->id_travel_package}\n";
    
    $payment = \App\Models\JamaahPayment::where('id_jamaah_booking', $booking1->id)
        ->latest()
        ->first();
    
    if ($payment) {
        echo "Latest Payment ID: {$payment->id}\n";
        echo "Status: {$payment->verification_status}\n";
        
        if ($payment->verification_status === 'pending_verification') {
            echo "✅ SHOULD REDIRECT to pending page\n";
            echo "URL: /paket/{$booking1->id_travel_package}/booking/{$booking1->id}/pending\n";
        } else {
            echo "✅ SHOULD SHOW invoice page\n";
            echo "URL: /paket/{$booking1->id_travel_package}/invoice/{$booking1->id}\n";
        }
    } else {
        echo "❌ No payment found\n";
        echo "✅ SHOULD SHOW invoice page (no pending payment)\n";
    }
    echo "\n";
}

// Test Case 2: Booking tanpa payment
echo "Test Case 2: Booking without payment\n";
$bookingNoPayment = \App\Models\JamaahBooking::whereDoesntHave('payments')->first();
if ($bookingNoPayment) {
    echo "Booking: {$bookingNoPayment->booking_code}\n";
    echo "✅ SHOULD SHOW invoice page (no payment)\n";
    echo "URL: /paket/{$bookingNoPayment->id_travel_package}/invoice/{$bookingNoPayment->id}\n";
} else {
    echo "No booking without payment found\n";
}
echo "\n";

// Test Case 3: Booking dengan payment verified
echo "Test Case 3: Booking with verified payment\n";
$verifiedPayment = \App\Models\JamaahPayment::where('verification_status', 'verified')->first();
if ($verifiedPayment) {
    $booking3 = $verifiedPayment->booking;
    echo "Booking: {$booking3->booking_code}\n";
    echo "Payment Status: {$verifiedPayment->verification_status}\n";
    echo "✅ SHOULD SHOW invoice page (payment verified)\n";
    echo "URL: /paket/{$booking3->id_travel_package}/invoice/{$booking3->id}\n";
} else {
    echo "No verified payment found\n";
}
echo "\n";

// Summary
echo "===================\n";
echo "REDIRECT LOGIC:\n";
echo "===================\n";
echo "1. IF latest payment status = 'pending_verification' → REDIRECT to pending page\n";
echo "2. IF no payment OR payment status = 'verified' → SHOW invoice page\n";
echo "3. IF payment status = 'rejected' → SHOW invoice page (can upload again)\n";
