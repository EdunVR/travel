<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JamaahBooking;
use App\Models\AffiliateReferral;

echo "=== DEBUG AFFILIATE COMMISSION ===\n\n";

// Ambil booking terbaru yang ada affiliate referral
$referrals = AffiliateReferral::with(['booking.jamaah', 'affiliator'])
    ->latest()
    ->take(5)
    ->get();

if ($referrals->isEmpty()) {
    echo "❌ Tidak ada referral ditemukan\n";
    exit;
}

foreach ($referrals as $referral) {
    $booking = $referral->booking;
    
    echo "Booking: {$booking->booking_code}\n";
    echo "Jamaah: {$booking->jamaah->nama}\n";
    echo "Affiliator: {$referral->affiliator->full_name}\n";
    
    // Hitung total pax
    $totalPax = $booking->getTotalPax();
    echo "Total Pax (calculated): {$totalPax}\n";
    echo "Total Pax (saved in referral): {$referral->total_pax}\n";
    
    // Hitung family members
    $familyMembers = $booking->family_members_booking;
    if (is_string($familyMembers)) {
        $familyMembers = json_decode($familyMembers, true);
    }
    $familyCount = is_array($familyMembers) ? count($familyMembers) : 0;
    echo "Family Members Count: {$familyCount}\n";
    
    // Commission info
    echo "Order Amount: Rp " . number_format($referral->order_amount, 0, ',', '.') . "\n";
    echo "Commission Amount: Rp " . number_format($referral->commission_amount, 0, ',', '.') . "\n";
    echo "Commission Type: {$referral->commission_type}\n";
    echo "Commission Rate: {$referral->commission_rate}\n";
    
    // Expected commission
    $program = $referral->affiliator->partnershipProgram;
    if ($program) {
        $expectedPerPax = 0;
        if ($referral->commission_type === 'fixed') {
            $expectedPerPax = $referral->commission_rate;
        } else {
            $expectedPerPax = ($referral->order_amount * $referral->commission_rate / 100);
        }
        $expectedTotal = $expectedPerPax * $totalPax;
        
        echo "Expected Commission Per Pax: Rp " . number_format($expectedPerPax, 0, ',', '.') . "\n";
        echo "Expected Total Commission ({$totalPax} pax): Rp " . number_format($expectedTotal, 0, ',', '.') . "\n";
        
        if (abs($expectedTotal - $referral->commission_amount) > 1) {
            echo "⚠️  MISMATCH! Commission tidak sesuai dengan total pax!\n";
        } else {
            echo "✅ Commission sudah benar\n";
        }
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

echo "\n=== SELESAI ===\n";
