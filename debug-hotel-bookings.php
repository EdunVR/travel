<?php
/**
 * Debug script untuk memeriksa hotel bookings
 * Jalankan: php debug-hotel-bookings.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HotelBooking;
use App\Models\Keberangkatan;

echo "=== DEBUG HOTEL BOOKINGS ===\n\n";

// Get all keberangkatan
$keberangkatans = Keberangkatan::with('travelPackage')->get();

echo "Total Keberangkatan: " . $keberangkatans->count() . "\n\n";

foreach ($keberangkatans as $keberangkatan) {
    echo "Keberangkatan ID: {$keberangkatan->id}\n";
    echo "Nama: {$keberangkatan->keberangkatan_name}\n";
    echo "Code: {$keberangkatan->keberangkatan_code}\n";
    echo "Package: " . ($keberangkatan->travelPackage ? $keberangkatan->travelPackage->package_name : 'N/A') . "\n";
    
    // Get hotel bookings for this keberangkatan
    $hotelBookings = HotelBooking::with(['hotel', 'roomAssignments'])
        ->where('id_keberangkatan', $keberangkatan->id)
        ->get();
    
    echo "Hotel Bookings: " . $hotelBookings->count() . "\n";
    
    if ($hotelBookings->count() > 0) {
        foreach ($hotelBookings as $booking) {
            echo "  - Hotel: " . ($booking->hotel ? $booking->hotel->hotel_name : 'Unknown') . "\n";
            echo "    Check-in: " . ($booking->check_in_date ? $booking->check_in_date->format('d/m/Y') : 'N/A') . "\n";
            echo "    Check-out: " . ($booking->check_out_date ? $booking->check_out_date->format('d/m/Y') : 'N/A') . "\n";
            echo "    Room Count: {$booking->room_count}\n";
            echo "    Status: {$booking->status}\n";
            echo "    Assigned Jamaah: " . $booking->roomAssignments->count() . "\n";
        }
    } else {
        echo "  (Tidak ada hotel booking)\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

// Check if there are any hotel bookings without keberangkatan
$orphanBookings = HotelBooking::whereNull('id_keberangkatan')->get();
if ($orphanBookings->count() > 0) {
    echo "WARNING: Ada {$orphanBookings->count()} hotel booking tanpa keberangkatan!\n";
    foreach ($orphanBookings as $booking) {
        echo "  - ID: {$booking->id}, Hotel: " . ($booking->hotel ? $booking->hotel->hotel_name : 'Unknown') . "\n";
    }
}

echo "\n=== END DEBUG ===\n";
