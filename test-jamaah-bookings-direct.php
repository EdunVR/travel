<?php
/**
 * Test Jamaah Bookings Direct Database Query
 * 
 * This script directly queries the database to see jamaah bookings
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DIRECT DATABASE QUERY TEST ===\n\n";

// Get command line arguments
$keberangkatanId = $argv[1] ?? null;
$hotelBookingId = $argv[2] ?? null;

if (!$keberangkatanId) {
    echo "Usage: php test-jamaah-bookings-direct.php [keberangkatan_id] [hotel_booking_id]\n\n";
    
    // Show available keberangkatan
    echo "Available Keberangkatan:\n";
    $keberangkatans = DB::table('keberangkatan')
        ->select('id', 'keberangkatan_code', 'keberangkatan_name', 'status')
        ->get();
    
    foreach ($keberangkatans as $k) {
        echo "ID: {$k->id} | Code: {$k->keberangkatan_code} | Name: {$k->keberangkatan_name} | Status: {$k->status}\n";
    }
    
    echo "\n";
    exit(0);
}

echo "Testing for Keberangkatan ID: {$keberangkatanId}\n";
if ($hotelBookingId) {
    echo "Hotel Booking ID: {$hotelBookingId}\n";
}
echo "\n";

// 1. Check Keberangkatan
echo "=== 1. KEBERANGKATAN INFO ===\n";
$keberangkatan = DB::table('keberangkatan')
    ->where('id', $keberangkatanId)
    ->first();

if (!$keberangkatan) {
    echo "❌ Keberangkatan not found!\n";
    exit(1);
}

echo "Code: {$keberangkatan->keberangkatan_code}\n";
echo "Name: {$keberangkatan->keberangkatan_name}\n";
echo "Status: {$keberangkatan->status}\n";
echo "Package ID: {$keberangkatan->id_travel_package}\n\n";

// 2. Check Jamaah Bookings
echo "=== 2. JAMAAH BOOKINGS ===\n";
$jamaahBookings = DB::table('jamaah_bookings as jb')
    ->join('members as m', 'jb.id_jamaah', '=', 'm.id')
    ->select(
        'jb.id',
        'jb.booking_code',
        'jb.id_jamaah',
        'jb.id_keberangkatan',
        'jb.room_type',
        'jb.status',
        'jb.payment_status',
        'm.nama as jamaah_name',
        'm.ktp_nik',
        'm.passport_nomor'
    )
    ->where('jb.id_keberangkatan', $keberangkatanId)
    ->get();

if ($jamaahBookings->isEmpty()) {
    echo "❌ NO JAMAAH BOOKINGS FOUND!\n";
    echo "\nThis is the problem! You need to create jamaah bookings first.\n";
    echo "Go to: Travel → Booking Jamaah → Tambah Booking\n\n";
} else {
    echo "Found " . $jamaahBookings->count() . " jamaah booking(s):\n\n";
    
    foreach ($jamaahBookings as $jb) {
        echo "┌─────────────────────────────────────────────────────────────\n";
        echo "│ Booking ID: {$jb->id}\n";
        echo "│ Booking Code: {$jb->booking_code}\n";
        echo "│ Jamaah: {$jb->jamaah_name}\n";
        echo "│ Room Type: " . ($jb->room_type ?? '❌ NULL') . "\n";
        echo "│ Status: {$jb->status}\n";
        echo "│ Payment: {$jb->payment_status}\n";
        echo "│ KTP: " . ($jb->ktp_nik ?? '-') . "\n";
        echo "│ Passport: " . ($jb->passport_nomor ?? '-') . "\n";
        echo "└─────────────────────────────────────────────────────────────\n\n";
    }
}

// 3. Check Hotel Bookings
echo "=== 3. HOTEL BOOKINGS ===\n";
$hotelBookings = DB::table('hotel_bookings as hb')
    ->join('hotels as h', 'hb.id_hotel', '=', 'h.id')
    ->select(
        'hb.id',
        'hb.id_keberangkatan',
        'hb.check_in_date',
        'hb.check_out_date',
        'hb.room_count',
        'hb.status',
        'h.hotel_name'
    )
    ->where('hb.id_keberangkatan', $keberangkatanId)
    ->get();

if ($hotelBookings->isEmpty()) {
    echo "No hotel bookings found.\n\n";
} else {
    echo "Found " . $hotelBookings->count() . " hotel booking(s):\n\n";
    
    foreach ($hotelBookings as $hb) {
        echo "Hotel Booking ID: {$hb->id}\n";
        echo "  Hotel: {$hb->hotel_name}\n";
        echo "  Check-in: {$hb->check_in_date}\n";
        echo "  Check-out: {$hb->check_out_date}\n";
        echo "  Room Count: {$hb->room_count}\n";
        echo "  Status: {$hb->status}\n\n";
    }
}

// 4. Check Room Assignments
echo "=== 4. ROOM ASSIGNMENTS ===\n";
$assignments = DB::table('hotel_room_assignments as hra')
    ->join('hotel_bookings as hb', 'hra.id_hotel_booking', '=', 'hb.id')
    ->join('jamaah_bookings as jb', 'hra.id_jamaah_booking', '=', 'jb.id')
    ->join('members as m', 'jb.id_jamaah', '=', 'm.id')
    ->select(
        'hra.id',
        'hra.id_hotel_booking',
        'hra.id_jamaah_booking',
        'hra.room_number',
        'hra.bed_number',
        'hra.room_type',
        'm.nama as jamaah_name'
    )
    ->where('hb.id_keberangkatan', $keberangkatanId)
    ->get();

if ($assignments->isEmpty()) {
    echo "No room assignments yet.\n\n";
} else {
    echo "Found " . $assignments->count() . " assignment(s):\n\n";
    
    foreach ($assignments as $a) {
        echo "Assignment ID: {$a->id}\n";
        echo "  Hotel Booking: {$a->id_hotel_booking}\n";
        echo "  Jamaah: {$a->jamaah_name}\n";
        echo "  Room: {$a->room_number}";
        if ($a->bed_number) {
            echo " / Bed: {$a->bed_number}";
        }
        echo "\n";
        echo "  Type: " . ($a->room_type ?? '-') . "\n\n";
    }
}

// 5. Check Unassigned Jamaah
if (!$jamaahBookings->isEmpty()) {
    echo "=== 5. UNASSIGNED JAMAAH ===\n";
    
    $assignedJamaahIds = $assignments->pluck('id_jamaah_booking')->toArray();
    
    $unassigned = $jamaahBookings->filter(function($jb) use ($assignedJamaahIds) {
        return !in_array($jb->id, $assignedJamaahIds) && $jb->status !== 'cancelled';
    });
    
    if ($unassigned->isEmpty()) {
        echo "All jamaah have been assigned to rooms.\n\n";
    } else {
        echo "Found " . $unassigned->count() . " unassigned jamaah:\n\n";
        
        foreach ($unassigned as $jb) {
            echo "✓ {$jb->jamaah_name}\n";
            echo "  Booking ID: {$jb->id}\n";
            echo "  Booking Code: {$jb->booking_code}\n";
            echo "  Room Type: " . ($jb->room_type ?? '❌ NULL') . "\n\n";
        }
    }
}

// 6. Summary
echo "=== SUMMARY ===\n";
echo "Keberangkatan ID: {$keberangkatanId}\n";
echo "Jamaah Bookings: " . $jamaahBookings->count() . "\n";
echo "Hotel Bookings: " . $hotelBookings->count() . "\n";
echo "Room Assignments: " . $assignments->count() . "\n";

if (!$jamaahBookings->isEmpty()) {
    $unassignedCount = $jamaahBookings->filter(function($jb) use ($assignments) {
        return !$assignments->pluck('id_jamaah_booking')->contains($jb->id) && $jb->status !== 'cancelled';
    })->count();
    
    echo "Unassigned Jamaah: {$unassignedCount}\n";
}

echo "\n";

// 7. Recommendations
if ($jamaahBookings->isEmpty()) {
    echo "⚠️  ACTION REQUIRED:\n";
    echo "1. Create jamaah bookings first!\n";
    echo "2. Go to: Travel → Booking Jamaah → Tambah Booking\n";
    echo "3. Select Keberangkatan ID: {$keberangkatanId}\n";
    echo "4. Fill in room_type field\n";
    echo "5. Save the booking\n";
} else {
    $nullRoomTypes = $jamaahBookings->filter(function($jb) {
        return is_null($jb->room_type);
    });
    
    if ($nullRoomTypes->count() > 0) {
        echo "⚠️  WARNING:\n";
        echo "{$nullRoomTypes->count()} booking(s) have NULL room_type!\n";
        echo "Update them with:\n";
        echo "UPDATE jamaah_bookings SET room_type = 'Standard' WHERE id IN (";
        echo $nullRoomTypes->pluck('id')->implode(', ');
        echo ");\n";
    } else {
        echo "✅ All bookings have room_type set!\n";
        echo "✅ Ready to assign rooms!\n";
    }
}

echo "\n";
