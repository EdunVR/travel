<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = \App\Models\JamaahPayment::where('verification_status', 'pending_verification')->first();
if ($p) {
    $b = $p->booking;
    echo "Payment: {$p->id}\n";
    echo "Booking: {$b->booking_code} (ID: {$b->id})\n";
    echo "Package ID: {$b->id_travel_package}\n";
    echo "\nTest URL: https://poshan.my.id/hm/paket/{$b->id_travel_package}/invoice/{$b->id}\n";
    echo "Should redirect to: https://poshan.my.id/hm/paket/{$b->id_travel_package}/booking/{$b->id}/pending\n";
} else {
    echo "No pending payment found\n";
}
