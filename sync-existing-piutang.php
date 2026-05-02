<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SYNCING PIUTANG FOR EXISTING BOOKINGS ===\n\n";

$bookings = \App\Models\JamaahBooking::with('travelPackage')->get();

echo "Found {$bookings->count()} bookings\n\n";

foreach ($bookings as $booking) {
    echo "Processing Booking #{$booking->id} ({$booking->booking_code})...\n";
    
    try {
        // Check if piutang already exists
        $existingPiutang = \App\Models\Piutang::where('id_jamaah_booking', $booking->id)
            ->where('source_type', 'travel')
            ->first();
        
        if ($existingPiutang) {
            echo "  ⚠️  Piutang already exists (ID: {$existingPiutang->id_piutang}), updating...\n";
        }
        
        // Sync piutang
        $piutang = \App\Models\Piutang::updateOrCreate(
            [
                'id_jamaah_booking' => $booking->id,
                'source_type' => 'travel'
            ],
            [
                'id_member' => $booking->id_member,
                'id_outlet' => $booking->id_outlet,
                'tanggal_piutang' => $booking->booking_date,
                'jumlah_piutang' => $booking->total_price,
                'jumlah_dibayar' => $booking->paid_amount ?? 0,
                'sisa_piutang' => $booking->remaining_amount ?? $booking->total_price,
                'status' => ($booking->paid_amount >= $booking->total_price) ? 'lunas' : 'belum_lunas',
                'keterangan' => 'Piutang Travel - ' . $booking->booking_code . ' - ' . ($booking->travelPackage->package_name ?? ''),
            ]
        );
        
        echo "  ✅ Piutang synced (ID: {$piutang->id_piutang})\n";
        echo "     Jumlah: Rp " . number_format($piutang->jumlah_piutang, 0) . "\n";
        echo "     Dibayar: Rp " . number_format($piutang->jumlah_dibayar, 0) . "\n";
        echo "     Sisa: Rp " . number_format($piutang->sisa_piutang, 0) . "\n";
        echo "     Status: {$piutang->status}\n";
        
    } catch (\Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== SYNC COMPLETE ===\n";
