<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST ROOMLIST STREAM FALLBACK ===\n\n";

$keberangkatan = \App\Models\Keberangkatan::first();

if (!$keberangkatan) {
    echo "❌ Tidak ada keberangkatan\n";
    exit;
}

echo "✓ Keberangkatan: {$keberangkatan->keberangkatan_code}\n\n";

// Simulate PDF logic
$hotelBookings = $keberangkatan->hotelBookings;
$allAssignments = collect();

foreach($hotelBookings as $hotelBooking) {
    foreach($hotelBooking->roomAssignments as $assignment) {
        $allAssignments->push($assignment);
    }
}

echo "1. CEK ROOM ASSIGNMENTS:\n";
echo "   Total: " . $allAssignments->count() . "\n\n";

// FALLBACK: If no room assignments, create from jamaah bookings
if ($allAssignments->isEmpty()) {
    echo "2. FALLBACK ACTIVATED - Creating fake assignments from jamaah bookings:\n";
    
    $jamaahBookings = \App\Models\JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
        ->whereNotIn('status', ['cancelled'])
        ->with('jamaah')
        ->get();
    
    echo "   Total jamaah bookings: " . $jamaahBookings->count() . "\n";
    
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
        echo "   - Room {$roomNumber}: " . ($jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama) : 'N/A') . "\n";
    }
}

// Group by room number
$groupedByRoom = $allAssignments->groupBy('room_number')->sortKeys();

echo "\n3. HASIL GROUPING:\n";
echo "   Total rooms: " . $groupedByRoom->count() . "\n";
foreach ($groupedByRoom as $roomNumber => $assignments) {
    echo "   - Room {$roomNumber}: " . $assignments->count() . " jamaah\n";
}

echo "\n4. KESIMPULAN:\n";
if ($groupedByRoom->isEmpty()) {
    echo "   ❌ Tidak ada data untuk ditampilkan\n";
} else {
    echo "   ✓ PDF akan menampilkan " . $allAssignments->count() . " jamaah di " . $groupedByRoom->count() . " kamar\n";
    echo "   ✓ Fallback logic berhasil!\n";
}

echo "\n=== SELESAI ===\n";
