<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$booking = \App\Models\JamaahBooking::find(15);
if (!$booking) {
    echo "Booking 15 not found\n";
    exit;
}

echo "Booking 15:\n";
echo "id_member: '{$booking->id_member}'\n";
echo "booking_code: {$booking->booking_code}\n";

// Check if there's jamaah data in the booking itself
$attrs = $booking->getAttributes();
echo "\nAll attributes:\n";
foreach ($attrs as $key => $value) {
    if (str_contains($key, 'jamaah') || str_contains($key, 'member') || str_contains($key, 'nama')) {
        echo "- $key: $value\n";
    }
}

// Check family members
if ($booking->family_members_booking) {
    $family = json_decode($booking->family_members_booking, true);
    echo "\nFamily members: " . count($family) . "\n";
    print_r($family);
}
