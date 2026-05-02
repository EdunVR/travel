<?php
/**
 * Check Jamaah Bookings for Keberangkatan
 * 
 * This script checks if there are jamaah bookings for a specific keberangkatan
 * and shows their details including room_type
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Keberangkatan;
use App\Models\JamaahBooking;
use App\Models\HotelBooking;

echo "=== CHECKING JAMAAH BOOKINGS FOR KEBERANGKATAN ===\n\n";

// Get keberangkatan ID from command line or use default
$keberangkatanId = $argv[1] ?? null;

if (!$keberangkatanId) {
    echo "Usage: php check-jamaah-bookings.php [keberangkatan_id]\n\n";
    
    // Show available keberangkatan
    echo "Available Keberangkatan:\n";
    $keberangkatans = Keberangkatan::with('travelPackage')->get();
    
    if ($keberangkatans->isEmpty()) {
        echo "No keberangkatan found!\n";
        exit(1);
    }
    
    foreach ($keberangkatans as $k) {
        echo "ID: {$k->id} | Code: {$k->keberangkatan_code} | Package: {$k->travelPackage->package_name}\n";
    }
    
    exit(0);
}

// Find keberangkatan
$keberangkatan = Keberangkatan::with('travelPackage')->find($keberangkatanId);

if (!$keberangkatan) {
    echo "Keberangkatan with ID {$keberangkatanId} not found!\n";
    exit(1);
}

echo "Keberangkatan: {$keberangkatan->keberangkatan_name}\n";
echo "Code: {$keberangkatan->keberangkatan_code}\n";
echo "Package: {$keberangkatan->travelPackage->package_name}\n";
echo "Status: {$keberangkatan->status}\n\n";

// Check jamaah bookings
echo "=== JAMAAH BOOKINGS ===\n";
$jamaahBookings = JamaahBooking::with('jamaah')
    ->where('id_keberangkatan', $keberangkatanId)
    ->get();

if ($jamaahBookings->isEmpty()) {
    echo "❌ NO JAMAAH BOOKINGS FOUND!\n\n";
    echo "This is why the modal is empty. You need to:\n";
    echo "1. Go to 'Booking Jamaah' menu\n";
    echo "2. Create a new booking\n";
    echo "3. Select this keberangkatan (ID: {$keberangkatanId})\n";
    echo "4. Choose a room_type (Standard, Deluxe, Suite, etc.)\n";
    echo "5. Complete the booking\n\n";
} else {
    echo "Found " . $jamaahBookings->count() . " jamaah booking(s):\n\n";
    
    foreach ($jamaahBookings as $booking) {
        echo "Booking ID: {$booking->id}\n";
        echo "  Booking Code: {$booking->booking_code}\n";
        echo "  Jamaah: {$booking->jamaah->nama}\n";
        echo "  Room Type: " . ($booking->room_type ?? 'NOT SET') . "\n";
        echo "  Status: {$booking->status}\n";
        echo "  Payment Status: {$booking->payment_status}\n";
        echo "  Created: {$booking->created_at}\n";
        echo "\n";
    }
}

// Check hotel bookings
echo "=== HOTEL BOOKINGS ===\n";
$hotelBookings = HotelBooking::with('hotel')
    ->where('id_keberangkatan', $keberangkatanId)
    ->get();

if ($hotelBookings->isEmpty()) {
    echo "No hotel bookings found for this keberangkatan.\n\n";
} else {
    echo "Found " . $hotelBookings->count() . " hotel booking(s):\n\n";
    
    foreach ($hotelBookings as $hb) {
        echo "Hotel Booking ID: {$hb->id}\n";
        echo "  Hotel: {$hb->hotel->hotel_name}\n";
        echo "  Check-in: {$hb->check_in_date}\n";
        echo "  Check-out: {$hb->check_out_date}\n";
        echo "  Room Count: {$hb->room_count}\n";
        echo "  Status: {$hb->status}\n";
        
        // Check assignments
        $assignments = $hb->roomAssignments()->with('jamaahBooking.jamaah')->get();
        if ($assignments->isEmpty()) {
            echo "  Assignments: None\n";
        } else {
            echo "  Assignments: {$assignments->count()}\n";
            foreach ($assignments as $assignment) {
                echo "    - {$assignment->jamaahBooking->jamaah->nama} (Room: {$assignment->room_number}";
                if ($assignment->bed_number) {
                    echo ", Bed: {$assignment->bed_number}";
                }
                echo ")\n";
            }
        }
        echo "\n";
    }
}

echo "=== SUMMARY ===\n";
echo "Total Jamaah Bookings: " . $jamaahBookings->count() . "\n";
echo "Total Hotel Bookings: " . $hotelBookings->count() . "\n";

if ($jamaahBookings->isEmpty()) {
    echo "\n⚠️  ACTION REQUIRED:\n";
    echo "Create jamaah bookings first before assigning rooms!\n";
} else {
    $unassigned = $jamaahBookings->filter(function($booking) use ($keberangkatanId) {
        return !$booking->hotelRoomAssignment()->exists();
    });
    
    echo "Unassigned Jamaah: " . $unassigned->count() . "\n";
    
    if ($unassigned->count() > 0) {
        echo "\n✅ Ready to assign rooms for:\n";
        foreach ($unassigned as $booking) {
            echo "  - {$booking->jamaah->nama} (Room Type: " . ($booking->room_type ?? 'Standard') . ")\n";
        }
    }
}

echo "\n";
