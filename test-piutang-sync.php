<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING PIUTANG SYNC ===\n\n";

// Test 1: Check existing bookings
echo "1. Checking existing bookings:\n";
$bookings = DB::table('jamaah_bookings')
    ->select('id', 'booking_code', 'total_price', 'paid_amount', 'remaining_amount', 'payment_status')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($bookings as $b) {
    echo "   Booking #{$b->id} ({$b->booking_code}):\n";
    echo "     Total: Rp " . number_format($b->total_price, 0) . "\n";
    echo "     Paid: Rp " . number_format($b->paid_amount, 0) . "\n";
    echo "     Remaining: Rp " . number_format($b->remaining_amount, 0) . "\n";
    echo "     Status: {$b->payment_status}\n";
    
    // Check if piutang exists
    $piutang = DB::table('piutang')
        ->where('id_jamaah_booking', $b->id)
        ->where('source_type', 'travel')
        ->first();
    
    if ($piutang) {
        echo "     ✅ Piutang EXISTS:\n";
        echo "        ID: {$piutang->id_piutang}\n";
        echo "        Jumlah: Rp " . number_format($piutang->jumlah_piutang, 0) . "\n";
        echo "        Dibayar: Rp " . number_format($piutang->jumlah_dibayar, 0) . "\n";
        echo "        Sisa: Rp " . number_format($piutang->sisa_piutang, 0) . "\n";
        echo "        Status: {$piutang->status}\n";
    } else {
        echo "     ❌ Piutang NOT FOUND\n";
    }
    echo "\n";
}

// Test 2: Total piutang travel
echo "2. Total piutang travel (belum lunas):\n";
$totalPiutang = DB::table('piutang')
    ->where('source_type', 'travel')
    ->where('status', 'belum_lunas')
    ->sum('sisa_piutang');
echo "   Total: Rp " . number_format($totalPiutang, 0, ',', '.') . "\n\n";

// Test 3: Count piutang records
$countAll = DB::table('piutang')->where('source_type', 'travel')->count();
$countLunas = DB::table('piutang')->where('source_type', 'travel')->where('status', 'lunas')->count();
$countBelumLunas = DB::table('piutang')->where('source_type', 'travel')->where('status', 'belum_lunas')->count();

echo "3. Piutang statistics:\n";
echo "   Total records: {$countAll}\n";
echo "   Lunas: {$countLunas}\n";
echo "   Belum Lunas: {$countBelumLunas}\n\n";

echo "=== TEST COMPLETE ===\n";
