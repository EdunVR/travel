<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST PDF HTML OUTPUT ===\n\n";

try {
    $keberangkatan = \App\Models\Keberangkatan::first();
    
    if (!$keberangkatan) {
        echo "❌ Tidak ada keberangkatan\n";
        exit;
    }
    
    echo "✓ Keberangkatan: {$keberangkatan->keberangkatan_code}\n\n";
    
    // Simulate controller method
    $keberangkatan = \App\Models\Keberangkatan::with([
        'travelPackage.hotelMadinah',
        'travelPackage.hotelMakkah',
        'hotelBookings.hotel',
        'hotelBookings.roomAssignments.jamaahBooking.jamaah'
    ])->findOrFail($keberangkatan->id);

    // Get hotel bookings for this keberangkatan
    $hotelBookings = $keberangkatan->hotelBookings;
    
    echo "Rendering HTML...\n";
    
    // Render the view as HTML
    $html = view('admin.travel.document.roomlist-stream-pdf', compact('keberangkatan', 'hotelBookings'))->render();
    
    // Save to file
    $filename = 'test_roomlist_stream_' . time() . '.html';
    file_put_contents(storage_path('app/public/' . $filename), $html);
    
    echo "✓ HTML generated successfully!\n";
    echo "  Location: storage/app/public/{$filename}\n";
    echo "  Size: " . strlen($html) . " bytes\n\n";
    
    // Check if data is in HTML
    if (strpos($html, 'Aan') !== false) {
        echo "✓ Data jamaah 'Aan' ditemukan di HTML!\n";
    } else {
        echo "❌ Data jamaah 'Aan' TIDAK ditemukan di HTML!\n";
    }
    
    if (strpos($html, 'Room 101') !== false || strpos($html, 'ROOM 101') !== false) {
        echo "✓ Room 101 ditemukan di HTML!\n";
    } else {
        echo "❌ Room 101 TIDAK ditemukan di HTML!\n";
    }
    
    if (strpos($html, 'Tidak ada data jamaah') !== false || strpos($html, 'Belum ada penempatan') !== false) {
        echo "⚠️  Pesan 'tidak ada data' ditemukan di HTML!\n";
    }
    
    // Count table rows
    $rowCount = substr_count($html, '<tr>');
    echo "\nTotal <tr> tags: {$rowCount}\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
}

echo "\n=== SELESAI ===\n";
