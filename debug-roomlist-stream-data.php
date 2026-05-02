<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG ROOMLIST STREAM DATA ===\n\n";

// Get keberangkatan
$keberangkatan = \App\Models\Keberangkatan::first();

if (!$keberangkatan) {
    echo "❌ Tidak ada keberangkatan di database\n";
    exit;
}

echo "✓ Keberangkatan: {$keberangkatan->keberangkatan_code} - {$keberangkatan->keberangkatan_name}\n\n";

// Check jamaah bookings
echo "1. CEK JAMAAH BOOKINGS:\n";
$jamaahBookings = \App\Models\JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
    ->whereNotIn('status', ['cancelled'])
    ->with('jamaah')
    ->get();

echo "   Total jamaah bookings: " . $jamaahBookings->count() . "\n";
foreach ($jamaahBookings as $booking) {
    $jamaah = $booking->jamaah;
    echo "   - Booking ID: {$booking->id}, Jamaah: " . ($jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama) : 'N/A') . "\n";
}

// Check hotel bookings
echo "\n2. CEK HOTEL BOOKINGS:\n";
$hotelBookings = $keberangkatan->hotelBookings;
echo "   Total hotel bookings: " . $hotelBookings->count() . "\n";

if ($hotelBookings->isEmpty()) {
    echo "   ❌ TIDAK ADA HOTEL BOOKINGS!\n";
    echo "   ⚠️  Roomlist Stream membutuhkan hotel bookings dan room assignments\n";
    echo "\n   SOLUSI:\n";
    echo "   1. Buat hotel booking untuk keberangkatan ini\n";
    echo "   2. Assign jamaah ke kamar hotel\n";
} else {
    foreach ($hotelBookings as $hotelBooking) {
        echo "   - Hotel Booking ID: {$hotelBooking->id}\n";
        echo "     Hotel: " . ($hotelBooking->hotel->hotel_name ?? 'N/A') . "\n";
        echo "     Check-in: " . ($hotelBooking->check_in_date ? $hotelBooking->check_in_date->format('d/m/Y') : 'N/A') . "\n";
        echo "     Check-out: " . ($hotelBooking->check_out_date ? $hotelBooking->check_out_date->format('d/m/Y') : 'N/A') . "\n";
        
        // Check room assignments
        $roomAssignments = $hotelBooking->roomAssignments;
        echo "     Room assignments: " . $roomAssignments->count() . "\n";
        
        if ($roomAssignments->isEmpty()) {
            echo "     ❌ TIDAK ADA ROOM ASSIGNMENTS!\n";
        } else {
            foreach ($roomAssignments as $assignment) {
                $jamaah = $assignment->jamaahBooking->jamaah ?? null;
                echo "       * Room {$assignment->room_number} - " . ($jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama) : 'N/A') . "\n";
            }
        }
    }
}

// Check what data is sent to PDF
echo "\n3. CEK DATA YANG DIKIRIM KE PDF:\n";
$keberangkatan = \App\Models\Keberangkatan::with([
    'travelPackage.hotelMadinah',
    'travelPackage.hotelMakkah',
    'hotelBookings.hotel',
    'hotelBookings.roomAssignments.jamaahBooking.jamaah'
])->find($keberangkatan->id);

$hotelBookings = $keberangkatan->hotelBookings;

echo "   Hotel bookings untuk PDF: " . $hotelBookings->count() . "\n";

$allAssignments = collect();
foreach($hotelBookings as $hotelBooking) {
    foreach($hotelBooking->roomAssignments as $assignment) {
        $allAssignments->push($assignment);
    }
}

echo "   Total room assignments: " . $allAssignments->count() . "\n";

$groupedByRoom = $allAssignments->groupBy('room_number')->sortKeys();
echo "   Total rooms: " . $groupedByRoom->count() . "\n";

if ($groupedByRoom->isEmpty()) {
    echo "\n   ❌ TIDAK ADA DATA UNTUK DITAMPILKAN DI PDF!\n";
    echo "   PDF akan menampilkan pesan: 'Belum ada penempatan kamar untuk keberangkatan ini'\n";
} else {
    echo "\n   ✓ Data siap untuk PDF:\n";
    foreach ($groupedByRoom as $roomNumber => $assignments) {
        echo "   - Room {$roomNumber}: " . $assignments->count() . " jamaah\n";
        foreach ($assignments as $assignment) {
            $jamaah = $assignment->jamaahBooking->jamaah ?? null;
            echo "     * " . ($jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama) : 'N/A') . "\n";
        }
    }
}

echo "\n=== KESIMPULAN ===\n";
if ($hotelBookings->isEmpty()) {
    echo "❌ Tidak ada hotel bookings\n";
    echo "   Solusi: Buat hotel booking di halaman Hotel Booking\n";
} elseif ($allAssignments->isEmpty()) {
    echo "❌ Tidak ada room assignments\n";
    echo "   Solusi: Assign jamaah ke kamar di halaman Hotel Booking\n";
} else {
    echo "✓ Data lengkap, PDF seharusnya menampilkan data\n";
}

echo "\n=== SELESAI ===\n";
