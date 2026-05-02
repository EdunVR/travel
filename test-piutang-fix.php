<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING PIUTANG FIX ===\n\n";

// Test 1: Query piutang travel
echo "1. Testing piutang query (source_type = 'travel'):\n";
$totalPiutang = DB::table('piutang')
    ->where('source_type', 'travel')
    ->where('status', 'belum_lunas')
    ->sum('sisa_piutang');
echo "   Total Piutang Travel (belum lunas): Rp " . number_format($totalPiutang, 0, ',', '.') . "\n\n";

// Test 2: Count piutang records
$countPiutang = DB::table('piutang')
    ->where('source_type', 'travel')
    ->count();
echo "2. Total piutang records dengan source_type='travel': {$countPiutang}\n\n";

// Test 3: Check family_members_booking column
echo "3. Checking family_members_booking column:\n";
$bookingsWithFamily = DB::table('jamaah_bookings')
    ->whereNotNull('family_members_booking')
    ->count();
$bookingsWithoutFamily = DB::table('jamaah_bookings')
    ->whereNull('family_members_booking')
    ->count();
echo "   Bookings WITH family_members_booking: {$bookingsWithFamily}\n";
echo "   Bookings WITHOUT family_members_booking: {$bookingsWithoutFamily}\n\n";

// Test 4: Sample piutang data
echo "4. Sample piutang data (first 3 records):\n";
$samplePiutang = DB::table('piutang')
    ->where('source_type', 'travel')
    ->limit(3)
    ->get(['id_piutang', 'id_jamaah_booking', 'jumlah_piutang', 'jumlah_dibayar', 'sisa_piutang', 'status']);
foreach ($samplePiutang as $p) {
    echo "   ID: {$p->id_piutang}, Booking: {$p->id_jamaah_booking}, ";
    echo "Total: " . number_format($p->jumlah_piutang, 0) . ", ";
    echo "Dibayar: " . number_format($p->jumlah_dibayar, 0) . ", ";
    echo "Sisa: " . number_format($p->sisa_piutang, 0) . ", ";
    echo "Status: {$p->status}\n";
}

echo "\n=== TEST COMPLETE ===\n";
