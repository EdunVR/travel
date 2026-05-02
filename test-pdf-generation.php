<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST PDF GENERATION ===\n\n";

$keberangkatan = \App\Models\Keberangkatan::with([
    'travelPackage.hotelMadinah',
    'travelPackage.hotelMakkah',
    'hotelBookings.hotel',
    'hotelBookings.roomAssignments.jamaahBooking.jamaah'
])->first();

if (!$keberangkatan) {
    echo "❌ Tidak ada keberangkatan\n";
    exit;
}

echo "✓ Keberangkatan: {$keberangkatan->keberangkatan_code}\n\n";

// Simulate PDF data preparation
$hotelBookings = $keberangkatan->hotelBookings;

echo "1. Hotel Bookings: " . $hotelBookings->count() . "\n";

// Group assignments by room number
$allAssignments = collect();
foreach($hotelBookings as $hotelBooking) {
    foreach($hotelBooking->roomAssignments as $assignment) {
        $allAssignments->push($assignment);
    }
}

echo "2. Initial Assignments: " . $allAssignments->count() . "\n";

// FALLBACK: If no room assignments, create from jamaah bookings
if ($allAssignments->isEmpty()) {
    echo "3. FALLBACK ACTIVATED\n";
    
    $jamaahBookings = \App\Models\JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
        ->whereNotIn('status', ['cancelled'])
        ->with('jamaah')
        ->get();
    
    echo "   Jamaah Bookings: " . $jamaahBookings->count() . "\n";
    
    // Auto-generate room assignments (2 people per room for double)
    $roomNumber = 101;
    $peoplePerRoom = 2; // Default double room
    $currentRoomPeople = 0;
    
    foreach ($jamaahBookings as $booking) {
        if ($currentRoomPeople >= $peoplePerRoom) {
            $roomNumber++;
            $currentRoomPeople = 0;
        }
        
        $fakeAssignment = (object)[
            'room_number' => $roomNumber,
            'room_type' => 'DOUBLE ROOM',
            'room_position' => '',
            'notes' => '',
            'jamaahBooking' => $booking
        ];
        
        $allAssignments->push($fakeAssignment);
        $currentRoomPeople++;
        
        $jamaah = $booking->jamaah;
        echo "   - Room {$roomNumber}: " . ($jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama ?? 'N/A') : 'N/A') . "\n";
    }
}

echo "\n4. Final Assignments: " . $allAssignments->count() . "\n";

// Group by room number
$groupedByRoom = $allAssignments->groupBy('room_number')->sortKeys();

echo "5. Grouped Rooms: " . $groupedByRoom->count() . "\n\n";

// Test rendering each row
echo "6. RENDERING TEST:\n";
foreach($groupedByRoom as $roomNumber => $assignments) {
    echo "   Room {$roomNumber}:\n";
    foreach($assignments as $index => $assignment) {
        $jamaah = is_object($assignment->jamaahBooking) && isset($assignment->jamaahBooking->jamaah) 
            ? $assignment->jamaahBooking->jamaah 
            : null;
        
        $jamaahName = $jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama ?? '-') : '-';
        $roomType = $assignment->room_type ?? 'DOUBLE ROOM';
        $roomPosition = $assignment->room_position ?? '';
        $bookingStatus = is_object($assignment->jamaahBooking) && isset($assignment->jamaahBooking->status)
            ? $assignment->jamaahBooking->status
            : 'confirmed';
        
        echo "     [{$index}] {$jamaahName} | Status: {$bookingStatus} | Type: {$roomType}\n";
    }
}

echo "\n=== KESIMPULAN ===\n";
if ($groupedByRoom->isEmpty()) {
    echo "❌ Tidak ada data untuk PDF\n";
} else {
    echo "✓ PDF akan menampilkan {$allAssignments->count()} jamaah di {$groupedByRoom->count()} kamar\n";
    echo "✓ Data siap untuk di-render!\n";
}

echo "\n=== SELESAI ===\n";
