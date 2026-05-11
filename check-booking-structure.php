<?php
/**
 * Check booking structure and jamaah data
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Booking Structure...\n\n";

// Get a booking with all data
$booking = \App\Models\JamaahBooking::first();

if (!$booking) {
    echo "❌ No bookings found\n";
    exit;
}

echo "Booking Data:\n";
echo "=============\n";
echo "ID: {$booking->id}\n";
echo "Booking Code: {$booking->booking_code}\n";
echo "Jamaah ID: {$booking->jamaah_id}\n";

// Show all attributes
echo "\nAll Booking Attributes:\n";
print_r($booking->getAttributes());

// Check if jamaah_name exists in booking
if (isset($booking->jamaah_name)) {
    echo "\n✅ jamaah_name exists in booking: {$booking->jamaah_name}\n";
} else {
    echo "\n❌ jamaah_name NOT in booking table\n";
}

// Check jamaah relationship
echo "\n\nJamaah Relationship:\n";
echo "===================\n";
if ($booking->jamaah_id) {
    $jamaah = \App\Models\Member::find($booking->jamaah_id);
    if ($jamaah) {
        echo "✅ Jamaah found in members table\n";
        echo "Full Name: {$jamaah->full_name}\n";
        echo "Phone: {$jamaah->phone_number}\n";
    } else {
        echo "❌ Jamaah NOT found in members table\n";
    }
} else {
    echo "❌ No jamaah_id in booking\n";
}

// Check payments
echo "\n\nPayments for this booking:\n";
echo "==========================\n";
$payments = \App\Models\JamaahPayment::where('booking_id', $booking->id)->get();
foreach ($payments as $payment) {
    echo "Payment ID: {$payment->id}\n";
    echo "Amount: Rp " . number_format($payment->amount, 0, ',', '.') . "\n";
    echo "Status: {$payment->verification_status}\n";
    
    // Try to access jamaah through booking
    $paymentBooking = $payment->booking;
    if ($paymentBooking) {
        echo "Booking loaded: ✅\n";
        $jamaah = $paymentBooking->jamaah;
        if ($jamaah) {
            echo "Jamaah loaded: ✅\n";
            echo "Jamaah Name: {$jamaah->full_name}\n";
        } else {
            echo "Jamaah loaded: ❌\n";
        }
    }
    echo "\n";
}
