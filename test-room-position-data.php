<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST ROOM POSITION DATA ===\n\n";

$keberangkatan = \App\Models\Keberangkatan::first();

if (!$keberangkatan) {
    echo "❌ Tidak ada keberangkatan\n";
    exit;
}

echo "✓ Keberangkatan: {$keberangkatan->keberangkatan_code}\n";
echo "  ID: {$keberangkatan->id}\n\n";

// Check hotel bookings
echo "1. CEK HOTEL BOOKINGS:\n";
$hotelBookings = $keberangkatan->hotelBookings;
echo "   Total: " . $hotelBookings->count() . "\n";

if ($hotelBookings->isEmpty()) {
    echo "   ❌ TIDAK ADA HOTEL BOOKINGS!\n\n";
    echo "   SOLUSI:\n";
    echo "   Halaman 'Kelola Room Position' membutuhkan hotel bookings.\n";
    echo "   Untuk menggunakan fitur ini:\n";
    echo "   1. Buka menu Hotel Booking\n";
    echo "   2. Buat hotel booking untuk keberangkatan ini\n";
    echo "   3. Assign jamaah ke kamar hotel\n";
    echo "   4. Baru bisa mengatur room position\n\n";
    
    echo "   ALTERNATIF:\n";
    echo "   Jika hanya ingin melihat roomlist dengan data jamaah:\n";
    echo "   - Gunakan tombol 'Roomlist Stream' di Detail Keberangkatan\n";
    echo "   - PDF akan otomatis menampilkan jamaah dengan fallback logic\n";
    exit;
}

// Check room assignments
echo "\n2. CEK ROOM ASSIGNMENTS:\n";
$totalAssignments = 0;
foreach ($hotelBookings as $hotelBooking) {
    $assignments = $hotelBooking->roomAssignments;
    echo "   Hotel: " . ($hotelBooking->hotel->hotel_name ?? 'N/A') . "\n";
    echo "   Room assignments: " . $assignments->count() . "\n";
    $totalAssignments += $assignments->count();
    
    if ($assignments->isNotEmpty()) {
        foreach ($assignments as $assignment) {
            $jamaah = $assignment->jamaahBooking->jamaah ?? null;
            $name = $jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama ?? 'N/A') : 'N/A';
            echo "     - Room {$assignment->room_number}: {$name}\n";
        }
    }
}

echo "\n3. SIMULATE API RESPONSE:\n";
$keberangkatan = \App\Models\Keberangkatan::with([
    'travelPackage',
    'hotelBookings.hotel',
    'hotelBookings.roomAssignments.jamaahBooking.jamaah'
])->find($keberangkatan->id);

$hotels = [];

foreach ($keberangkatan->hotelBookings as $hotelBooking) {
    $roomAssignments = $hotelBooking->roomAssignments->groupBy('room_number');
    $rooms = [];

    foreach ($roomAssignments as $roomNumber => $assignments) {
        $jamaahList = [];
        $assignmentIds = [];

        foreach ($assignments as $assignment) {
            $jamaah = $assignment->jamaahBooking->jamaah ?? null;
            if ($jamaah) {
                $jamaahList[] = [
                    'name' => $jamaah->nama ?? $jamaah->ktp_nama ?? '-',
                    'gender' => $jamaah->gender ?? '-'
                ];
            }
            $assignmentIds[] = [
                'id' => $assignment->id
            ];
        }

        $rooms[] = [
            'room_number' => $roomNumber,
            'room_type' => $assignments->first()->room_type ?? 'Standard',
            'room_position' => $assignments->first()->room_position ?? '',
            'jamaah_list' => $jamaahList,
            'assignments' => $assignmentIds
        ];
    }

    $hotels[] = [
        'id' => $hotelBooking->id,
        'hotel_name' => $hotelBooking->hotel->hotel_name ?? 'Hotel',
        'check_in' => $hotelBooking->check_in_date ? $hotelBooking->check_in_date->format('d/m/Y') : '-',
        'check_out' => $hotelBooking->check_out_date ? $hotelBooking->check_out_date->format('d/m/Y') : '-',
        'rooms' => $rooms
    ];
}

$totalJamaah = 0;
foreach ($keberangkatan->hotelBookings as $hotelBooking) {
    $totalJamaah += $hotelBooking->roomAssignments->count();
}

$response = [
    'success' => true,
    'keberangkatan' => [
        'id' => $keberangkatan->id,
        'keberangkatan_code' => $keberangkatan->keberangkatan_code,
        'keberangkatan_name' => $keberangkatan->keberangkatan_name,
        'departure_date' => $keberangkatan->departure_date->format('Y-m-d'),
        'departure_date_formatted' => $keberangkatan->departure_date->format('d F Y'),
        'return_date' => $keberangkatan->return_date ? $keberangkatan->return_date->format('Y-m-d') : null,
        'return_date_formatted' => $keberangkatan->return_date ? $keberangkatan->return_date->format('d F Y') : '-',
        'total_jamaah' => $totalJamaah
    ],
    'hotels' => $hotels
];

echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

echo "\n=== KESIMPULAN ===\n";
if ($totalAssignments > 0) {
    echo "✓ Data siap untuk halaman Kelola Room Position\n";
    echo "  Total hotel bookings: " . $hotelBookings->count() . "\n";
    echo "  Total room assignments: {$totalAssignments}\n";
} else {
    echo "❌ Tidak ada data untuk halaman Kelola Room Position\n";
    echo "   Silakan buat hotel bookings dan room assignments terlebih dahulu\n";
}

echo "\n=== SELESAI ===\n";
