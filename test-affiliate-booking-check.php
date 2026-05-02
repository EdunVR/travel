<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Affiliate Booking Check Fix\n";
echo "====================================\n\n";

// Test phone number
$phoneNumber = '085795483498';

echo "1. Checking for bookings with phone number: {$phoneNumber}\n";

// Query yang benar menggunakan whereHas
$hasBooking = \App\Models\JamaahBooking::whereHas('member', function($query) use ($phoneNumber) {
        $query->where('telepon', $phoneNumber);
    })
    ->where('status', 'confirmed')
    ->exists();

echo "   Has confirmed booking: " . ($hasBooking ? 'YES' : 'NO') . "\n\n";

// Tampilkan detail bookings jika ada
$bookings = \App\Models\JamaahBooking::whereHas('member', function($query) use ($phoneNumber) {
        $query->where('telepon', $phoneNumber);
    })
    ->with(['member', 'travelPackage'])
    ->get();

echo "2. Total bookings found: " . $bookings->count() . "\n\n";

if ($bookings->count() > 0) {
    echo "3. Booking details:\n";
    foreach ($bookings as $booking) {
        echo "   - Booking Code: {$booking->booking_code}\n";
        echo "     Member: {$booking->member->nama}\n";
        echo "     Phone: {$booking->member->telepon}\n";
        echo "     Package: {$booking->travelPackage->nama_paket}\n";
        echo "     Status: {$booking->status}\n";
        echo "     Date: {$booking->booking_date}\n\n";
    }
}

echo "✓ Test completed successfully!\n";
