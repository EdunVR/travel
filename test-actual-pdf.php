<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST ACTUAL PDF GENERATION ===\n\n";

try {
    $keberangkatan = \App\Models\Keberangkatan::first();
    
    if (!$keberangkatan) {
        echo "❌ Tidak ada keberangkatan\n";
        exit;
    }
    
    echo "✓ Keberangkatan: {$keberangkatan->keberangkatan_code}\n";
    echo "  ID: {$keberangkatan->id}\n\n";
    
    // Simulate controller method
    $keberangkatan = \App\Models\Keberangkatan::with([
        'travelPackage.hotelMadinah',
        'travelPackage.hotelMakkah',
        'hotelBookings.hotel',
        'hotelBookings.roomAssignments.jamaahBooking.jamaah'
    ])->findOrFail($keberangkatan->id);

    // Get hotel bookings for this keberangkatan
    $hotelBookings = $keberangkatan->hotelBookings;
    
    echo "Hotel Bookings Count: " . $hotelBookings->count() . "\n";
    echo "Hotel Bookings Type: " . get_class($hotelBookings) . "\n\n";
    
    // Try to generate PDF
    echo "Generating PDF...\n";
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.travel.document.roomlist-stream-pdf', compact('keberangkatan', 'hotelBookings'))
        ->setPaper('a4', 'landscape');
    
    $filename = 'test_roomlist_stream_' . time() . '.pdf';
    $pdf->save(storage_path('app/public/' . $filename));
    
    echo "✓ PDF generated successfully!\n";
    echo "  Location: storage/app/public/{$filename}\n";
    echo "  Size: " . filesize(storage_path('app/public/' . $filename)) . " bytes\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== SELESAI ===\n";
