<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payment = \App\Models\JamaahPayment::find(15);
echo "Payment 15 booking_id: {$payment->id_jamaah_booking}\n";

$booking = \App\Models\JamaahBooking::find($payment->id_jamaah_booking);
echo "Booking found: " . ($booking ? "Yes" : "No") . "\n";

if ($booking) {
    echo "Booking code: {$booking->booking_code}\n";
    echo "id_member: '{$booking->id_member}'\n";
    echo "id_member is null: " . (is_null($booking->id_member) ? "YES" : "NO") . "\n";
    echo "id_member is empty: " . (empty($booking->id_member) ? "YES" : "NO") . "\n";
    
    // Try to get jamaah
    $jamaah = $booking->jamaah;
    echo "Jamaah loaded: " . ($jamaah ? "Yes" : "No") . "\n";
    
    if (!$jamaah && $booking->id_member) {
        echo "Trying to find member manually...\n";
        $member = \App\Models\Member::find($booking->id_member);
        echo "Member found: " . ($member ? "Yes - {$member->full_name}" : "No") . "\n";
    }
}
