<?php
/**
 * Debug script untuk memeriksa room assignments data
 * Jalankan: php debug-room-assignments.php [hotel_booking_id]
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HotelBooking;
use App\Models\JamaahBooking;

echo "=== DEBUG ROOM ASSIGNMENTS ===\n\n";

// Get hotel booking ID from command line or use first available
$hotelBookingId = $argv[1] ?? null;

if (!$hotelBookingId) {
    $firstBooking = HotelBooking::first();
    if ($firstBooking) {
        $hotelBookingId = $firstBooking->id;
        echo "Using first hotel booking ID: {$hotelBookingId}\n\n";
    } else {
        echo "ERROR: No hotel bookings found!\n";
        exit(1);
    }
}

$booking = HotelBooking::with([
    'roomAssignments.jamaahBooking.jamaah',
    'hotel',
    'keberangkatan.travelPackage.hotelMakkah',
    'keberangkatan.travelPackage.hotelMadinah'
])->find($hotelBookingId);

if (!$booking) {
    echo "ERROR: Hotel booking ID {$hotelBookingId} not found!\n";
    exit(1);
}

echo "Hotel Booking ID: {$booking->id}\n";
echo "Hotel: " . ($booking->hotel ? $booking->hotel->hotel_name : 'Unknown') . "\n";
echo "Keberangkatan: " . ($booking->keberangkatan ? $booking->keberangkatan->keberangkatan_name : 'Unknown') . "\n";
echo "\n" . str_repeat("-", 50) . "\n\n";

// Get unassigned jamaah
$unassignedJamaah = $booking->getUnassignedJamaah()->load('jamaah', 'travelPackage');

echo "Total Unassigned Jamaah: " . $unassignedJamaah->count() . "\n\n";

if ($unassignedJamaah->count() > 0) {
    echo "Unassigned Jamaah Details:\n";
    foreach ($unassignedJamaah as $jamaahBooking) {
        echo "\n  Booking ID: {$jamaahBooking->id}\n";
        echo "  Booking Code: {$jamaahBooking->booking_code}\n";
        echo "  Jamaah Name: " . ($jamaahBooking->jamaah->nama ?? 'N/A') . "\n";
        echo "  Room Type: " . ($jamaahBooking->room_type ?? 'N/A') . "\n";
        echo "  KTP: " . ($jamaahBooking->jamaah->ktp_nik ?? 'N/A') . "\n";
        echo "  Passport: " . ($jamaahBooking->jamaah->passport_nomor ?? 'N/A') . "\n";
        echo "  Status: {$jamaahBooking->status}\n";
        echo "  Payment Status: {$jamaahBooking->payment_status}\n";
    }
} else {
    echo "No unassigned jamaah found.\n";
    echo "\nChecking all jamaah bookings for this keberangkatan:\n";
    
    $allJamaah = JamaahBooking::where('id_keberangkatan', $booking->id_keberangkatan)
        ->with('jamaah')
        ->get();
    
    echo "Total Jamaah in Keberangkatan: " . $allJamaah->count() . "\n";
    
    foreach ($allJamaah as $jamaahBooking) {
        $isAssigned = $booking->roomAssignments()->where('id_jamaah_booking', $jamaahBooking->id)->exists();
        echo "\n  - {$jamaahBooking->jamaah->nama ?? 'N/A'} (ID: {$jamaahBooking->id})\n";
        echo "    Status: {$jamaahBooking->status}\n";
        echo "    Assigned: " . ($isAssigned ? 'YES' : 'NO') . "\n";
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Show current assignments
echo "Current Room Assignments: " . $booking->roomAssignments->count() . "\n\n";

if ($booking->roomAssignments->count() > 0) {
    foreach ($booking->roomAssignments as $assignment) {
        echo "  - Room {$assignment->room_number}";
        if ($assignment->bed_number) {
            echo ", Bed {$assignment->bed_number}";
        }
        echo " - " . ($assignment->jamaahBooking->jamaah->nama ?? 'Unknown') . "\n";
        echo "    Room Type: " . ($assignment->room_type ?? 'N/A') . "\n";
    }
}

echo "\n=== END DEBUG ===\n";
