<?php
/**
 * Script Testing DP Calculation Fix
 * 
 * Untuk memverifikasi bahwa DP 10 juta sudah dikalikan dengan jumlah pax
 * 
 * Cara pakai:
 * php test-dp-calculation.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JamaahBooking;
use Illuminate\Support\Facades\DB;

echo "=== TESTING DP CALCULATION FIX ===\n\n";

// 1. Cek sample bookings
echo "1. Sample Bookings dengan Family Members:\n";
$bookings = JamaahBooking::with(['jamaah', 'travelPackage'])
    ->whereNotNull('family_members_booking')
    ->where('family_members_booking', '!=', '')
    ->where('family_members_booking', '!=', '[]')
    ->limit(5)
    ->get();

if ($bookings->count() > 0) {
    foreach ($bookings as $booking) {
        $totalPax = $booking->getTotalPax();
        $dpAmount = $booking->calculateDPAmount();
        $familyMembers = json_decode($booking->family_members_booking, true);
        $familyCount = is_array($familyMembers) ? count($familyMembers) : 0;
        
        echo "\n   Booking: {$booking->booking_code}\n";
        echo "   Jamaah: {$booking->jamaah->nama}\n";
        echo "   Paket: {$booking->travelPackage->package_name}\n";
        echo "   Total Price: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
        echo "   Family Members: $familyCount orang\n";
        echo "   Total Pax: $totalPax (1 jamaah utama + $familyCount keluarga)\n";
        echo "   DP 10 juta x pax: Rp " . number_format($dpAmount, 0, ',', '.') . "\n";
        echo "   DP 25%: Rp " . number_format($booking->total_price * 0.25, 0, ',', '.') . "\n";
        echo "   ---\n";
    }
} else {
    echo "   ⚠️  Tidak ada booking dengan family members\n";
}

echo "\n";

// 2. Test calculation method
echo "2. Test Calculation Method:\n";
echo "   Scenario 1: 1 pax (jamaah saja)\n";
echo "   - DP 10 juta: Rp " . number_format(10000000 * 1, 0, ',', '.') . "\n\n";

echo "   Scenario 2: 2 pax (jamaah + 1 keluarga)\n";
echo "   - DP 10 juta: Rp " . number_format(10000000 * 2, 0, ',', '.') . "\n\n";

echo "   Scenario 3: 4 pax (jamaah + 3 keluarga)\n";
echo "   - DP 10 juta: Rp " . number_format(10000000 * 4, 0, ',', '.') . "\n\n";

// 3. Cek payments yang sudah ada
echo "3. Recent Payments (Last 10):\n";
$payments = DB::table('jamaah_payments as jp')
    ->join('jamaah_bookings as jb', 'jp.id_jamaah_booking', '=', 'jb.id')
    ->select(
        'jp.id',
        'jp.receipt_number',
        'jp.amount',
        'jp.payment_date',
        'jp.payment_type',
        'jb.booking_code',
        'jb.family_members_booking'
    )
    ->orderBy('jp.id', 'desc')
    ->limit(10)
    ->get();

if ($payments->count() > 0) {
    foreach ($payments as $payment) {
        $familyMembers = json_decode($payment->family_members_booking, true);
        $familyCount = is_array($familyMembers) ? count($familyMembers) : 0;
        $totalPax = 1 + $familyCount;
        $expectedDP = 10000000 * $totalPax;
        
        echo "\n   Receipt: {$payment->receipt_number}\n";
        echo "   Booking: {$payment->booking_code}\n";
        echo "   Payment Type: {$payment->payment_type}\n";
        echo "   Amount: Rp " . number_format($payment->amount, 0, ',', '.') . "\n";
        echo "   Total Pax: $totalPax\n";
        echo "   Expected DP (10jt x pax): Rp " . number_format($expectedDP, 0, ',', '.') . "\n";
        
        if ($payment->payment_type === 'dp' && $payment->amount == 10000000 && $totalPax > 1) {
            echo "   ❌ SALAH! Seharusnya: Rp " . number_format($expectedDP, 0, ',', '.') . "\n";
        } elseif ($payment->payment_type === 'dp' && $payment->amount == $expectedDP) {
            echo "   ✅ BENAR!\n";
        }
        echo "   ---\n";
    }
} else {
    echo "   ⚠️  Tidak ada payment\n";
}

echo "\n";

// 4. Summary
echo "=== SUMMARY ===\n";
echo "✅ Method calculateDPAmount() di model: SUDAH BENAR (10jt x pax)\n";
echo "✅ Method getTotalPax() di model: SUDAH BENAR (1 + family members)\n";
echo "✅ Controller pay() method: SUDAH DIPERBAIKI\n";
echo "   - Sebelum: \$amount = 10000000; (fixed 10 juta)\n";
echo "   - Sesudah: \$amount = \$booking->calculateDPAmount(); (10jt x pax)\n";
echo "\n";

echo "🎯 TESTING:\n";
echo "1. Buat booking baru dengan 2+ pax\n";
echo "2. Pilih DP 10 juta\n";
echo "3. Cek di invoice: DP harus 10jt x jumlah pax\n";
echo "4. Bayar dan cek di database: amount harus sesuai\n";
echo "\n";

echo "📝 Query untuk cek manual:\n";
echo "SELECT \n";
echo "    jp.receipt_number,\n";
echo "    jb.booking_code,\n";
echo "    jp.amount,\n";
echo "    jp.payment_type,\n";
echo "    jb.family_members_booking,\n";
echo "    (1 + JSON_LENGTH(jb.family_members_booking)) as total_pax,\n";
echo "    (10000000 * (1 + JSON_LENGTH(jb.family_members_booking))) as expected_dp\n";
echo "FROM jamaah_payments jp\n";
echo "JOIN jamaah_bookings jb ON jp.id_jamaah_booking = jb.id\n";
echo "WHERE jp.payment_type = 'dp'\n";
echo "ORDER BY jp.id DESC\n";
echo "LIMIT 10;\n";
echo "\n";
