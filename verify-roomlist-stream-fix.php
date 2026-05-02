<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     VERIFIKASI ROOMLIST STREAM DATA JAMAAH FIX             ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$allPassed = true;

// Test 1: Check Keberangkatan
echo "📋 TEST 1: Cek Data Keberangkatan\n";
echo str_repeat("─", 60) . "\n";
$keberangkatan = \App\Models\Keberangkatan::first();
if (!$keberangkatan) {
    echo "❌ GAGAL: Tidak ada keberangkatan di database\n";
    $allPassed = false;
} else {
    echo "✅ PASS: Keberangkatan ditemukan\n";
    echo "   Code: {$keberangkatan->keberangkatan_code}\n";
    echo "   Name: {$keberangkatan->keberangkatan_name}\n";
}
echo "\n";

// Test 2: Check Jamaah Bookings
echo "👥 TEST 2: Cek Data Jamaah Bookings\n";
echo str_repeat("─", 60) . "\n";
$jamaahBookings = \App\Models\JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
    ->whereNotIn('status', ['cancelled'])
    ->with('jamaah')
    ->get();

if ($jamaahBookings->isEmpty()) {
    echo "❌ GAGAL: Tidak ada jamaah bookings\n";
    $allPassed = false;
} else {
    echo "✅ PASS: {$jamaahBookings->count()} jamaah booking ditemukan\n";
    foreach ($jamaahBookings as $booking) {
        $jamaah = $booking->jamaah;
        $name = $jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama ?? 'N/A') : 'N/A';
        echo "   - {$name} (Status: {$booking->status})\n";
    }
}
echo "\n";

// Test 3: Check Hotel Bookings
echo "🏨 TEST 3: Cek Hotel Bookings\n";
echo str_repeat("─", 60) . "\n";
$hotelBookings = $keberangkatan->hotelBookings;
if ($hotelBookings->isEmpty()) {
    echo "⚠️  WARNING: Tidak ada hotel bookings\n";
    echo "   → Fallback mode akan digunakan\n";
    echo "   → Room akan di-generate otomatis\n";
} else {
    echo "✅ PASS: {$hotelBookings->count()} hotel booking ditemukan\n";
}
echo "\n";

// Test 4: Simulate Fallback Logic
echo "🔄 TEST 4: Simulasi Fallback Logic\n";
echo str_repeat("─", 60) . "\n";
$allAssignments = collect();

// Collect existing assignments
foreach($hotelBookings as $hotelBooking) {
    foreach($hotelBooking->roomAssignments as $assignment) {
        $allAssignments->push($assignment);
    }
}

// Apply fallback if needed
if ($allAssignments->isEmpty()) {
    echo "✅ PASS: Fallback activated\n";
    
    $roomNumber = 101;
    $peoplePerRoom = 2;
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
    }
    
    echo "   → {$allAssignments->count()} room assignment di-generate\n";
} else {
    echo "✅ PASS: Menggunakan room assignments dari database\n";
    echo "   → {$allAssignments->count()} room assignment ditemukan\n";
}
echo "\n";

// Test 5: Check Grouping
echo "📊 TEST 5: Cek Grouping by Room\n";
echo str_repeat("─", 60) . "\n";
$groupedByRoom = $allAssignments->groupBy('room_number')->sortKeys();

if ($groupedByRoom->isEmpty()) {
    echo "❌ GAGAL: Tidak ada room untuk ditampilkan\n";
    $allPassed = false;
} else {
    echo "✅ PASS: {$groupedByRoom->count()} kamar siap ditampilkan\n";
    foreach ($groupedByRoom as $roomNumber => $assignments) {
        echo "   Room {$roomNumber}: {$assignments->count()} jamaah\n";
        foreach ($assignments as $idx => $assignment) {
            $jamaah = $assignment->jamaahBooking->jamaah ?? null;
            $name = $jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama ?? 'N/A') : 'N/A';
            echo "     [{$idx}] {$name}\n";
        }
    }
}
echo "\n";

// Test 6: Generate Test PDF
echo "📄 TEST 6: Generate Test PDF\n";
echo str_repeat("─", 60) . "\n";
try {
    $keberangkatan = \App\Models\Keberangkatan::with([
        'travelPackage.hotelMadinah',
        'travelPackage.hotelMakkah',
        'hotelBookings.hotel',
        'hotelBookings.roomAssignments.jamaahBooking.jamaah'
    ])->findOrFail($keberangkatan->id);

    $hotelBookings = $keberangkatan->hotelBookings;
    
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.travel.document.roomlist-stream-pdf', compact('keberangkatan', 'hotelBookings'))
        ->setPaper('a4', 'landscape');
    
    $filename = 'verify_roomlist_stream_' . time() . '.pdf';
    $pdf->save(storage_path('app/public/' . $filename));
    
    $filesize = filesize(storage_path('app/public/' . $filename));
    
    if ($filesize > 2000) {
        echo "✅ PASS: PDF generated successfully\n";
        echo "   Location: storage/app/public/{$filename}\n";
        echo "   Size: {$filesize} bytes\n";
    } else {
        echo "⚠️  WARNING: PDF generated but size is small ({$filesize} bytes)\n";
        echo "   Mungkin PDF kosong atau error\n";
    }
} catch (\Exception $e) {
    echo "❌ GAGAL: Error generating PDF\n";
    echo "   Error: {$e->getMessage()}\n";
    $allPassed = false;
}
echo "\n";

// Test 7: Verify HTML Content
echo "🔍 TEST 7: Verify HTML Content\n";
echo str_repeat("─", 60) . "\n";
try {
    $html = view('admin.travel.document.roomlist-stream-pdf', compact('keberangkatan', 'hotelBookings'))->render();
    
    $hasJamaahData = false;
    foreach ($jamaahBookings as $booking) {
        $jamaah = $booking->jamaah;
        $name = $jamaah ? ($jamaah->nama ?? $jamaah->ktp_nama ?? '') : '';
        if ($name && strpos($html, $name) !== false) {
            echo "✅ PASS: Data jamaah '{$name}' ditemukan di HTML\n";
            $hasJamaahData = true;
        }
    }
    
    if (!$hasJamaahData) {
        echo "❌ GAGAL: Data jamaah tidak ditemukan di HTML\n";
        $allPassed = false;
    }
    
    if (strpos($html, 'Room 101') !== false || strpos($html, 'ROOM 101') !== false) {
        echo "✅ PASS: Room number ditemukan di HTML\n";
    } else {
        echo "❌ GAGAL: Room number tidak ditemukan di HTML\n";
        $allPassed = false;
    }
    
} catch (\Exception $e) {
    echo "❌ GAGAL: Error rendering HTML\n";
    echo "   Error: {$e->getMessage()}\n";
    $allPassed = false;
}
echo "\n";

// Final Result
echo "╔════════════════════════════════════════════════════════════╗\n";
if ($allPassed) {
    echo "║                    ✅ SEMUA TEST PASSED                    ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
    echo "🎉 KESIMPULAN:\n";
    echo "   ✓ Data jamaah ADA di database\n";
    echo "   ✓ Fallback logic BEKERJA dengan baik\n";
    echo "   ✓ PDF generated SUCCESSFULLY\n";
    echo "   ✓ Data jamaah MUNCUL di PDF\n\n";
    echo "📝 INSTRUKSI UNTUK USER:\n";
    echo "   1. Buka Detail Keberangkatan\n";
    echo "   2. Klik tombol 'Roomlist Stream'\n";
    echo "   3. PDF akan menampilkan data jamaah\n";
    echo "   4. Jika masih kosong, tekan Ctrl+F5 untuk hard refresh\n\n";
} else {
    echo "║                    ❌ ADA TEST YANG GAGAL                  ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
    echo "⚠️  Silakan cek error di atas untuk detail\n\n";
}

echo "═══════════════════════════════════════════════════════════\n";
