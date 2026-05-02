<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Keberangkatan;
use App\Models\HotelBooking;
use App\Models\JamaahBooking;

echo "=== DEBUG ROOMLIST DATA ===\n\n";

$keberangkatanId = 2; // BATCH 1

$keberangkatan = Keberangkatan::with(['travelPackage'])->findOrFail($keberangkatanId);

echo "Keberangkatan: {$keberangkatan->keberangkatan_name}\n";
echo "Package ID: {$keberangkatan->id_travel_package}\n\n";

// Check hotel bookings
echo "=== HOTEL BOOKINGS ===\n";
$hotelBookings = HotelBooking::where('id_keberangkatan', $keberangkatan->id)->get();
echo "Total hotel bookings (by keberangkatan): {$hotelBookings->count()}\n\n";

if ($hotelBookings->count() > 0) {
    foreach ($hotelBookings as $booking) {
        echo "Hotel Booking ID: {$booking->id}\n";
        echo "Hotel: " . ($booking->hotel ? $booking->hotel->hotel_name : 'N/A') . "\n";
        echo "Room Assignments: " . $booking->roomAssignments->count() . "\n";
        echo "---\n";
    }
} else {
    echo "Tidak ada hotel bookings untuk keberangkatan ini.\n";
}

// Check jamaah bookings
echo "\n=== JAMAAH BOOKINGS ===\n";
$jamaahBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
    ->with('jamaah')
    ->whereNotIn('status', ['cancelled'])
    ->get();

echo "Total jamaah bookings: {$jamaahBookings->count()}\n\n";

foreach ($jamaahBookings as $booking) {
    $jamaah = $booking->jamaah;
    echo "Jamaah: " . ($jamaah->nama ?? '-') . "\n";
    echo "Gender: " . ($jamaah->gender ?? '-') . "\n";
    echo "Room Preference: " . ($jamaah->room_preference ?? '-') . "\n";
    echo "---\n";
}

echo "\n=== KESIMPULAN ===\n";
echo "Jika hotel bookings = 0, maka roomlist akan kosong.\n";
echo "Roomlist memerlukan data hotel booking dan room assignment.\n";
echo "Alternatif: Tampilkan jamaah list dengan room preference.\n";

echo "\n=== SELESAI ===\n";
