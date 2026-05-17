<?php

/**
 * Recreate husni referral with correct commission
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Affiliator;
use App\Models\AffiliateReferral;
use App\Models\JamaahBooking;
use App\Models\AffiliateVoucher;
use Illuminate\Support\Facades\DB;

echo "=== RECREATE HUSNI REFERRAL ===\n\n";

try {
    // 1. Find husni
    $husni = Affiliator::where('full_name', 'husni')->first();
    
    if (!$husni) {
        echo "❌ Husni tidak ditemukan!\n";
        exit(1);
    }
    
    echo "Husni ID: {$husni->id}\n";
    echo "Partnership Program: " . ($husni->partnershipProgram ? $husni->partnershipProgram->name : 'None') . "\n";
    echo "Min Sale Commission: Rp " . number_format($husni->min_sale_commission ?? 0, 0, ',', '.') . "\n\n";
    
    // 2. Find booking with DISKONHUSNI
    $booking = JamaahBooking::where('voucher_code', 'DISKONHUSNI')->first();
    
    if (!$booking) {
        echo "❌ Booking dengan voucher DISKONHUSNI tidak ditemukan!\n";
        exit(1);
    }
    
    echo "Booking ID: {$booking->id}\n";
    echo "Booking Code: {$booking->booking_code}\n";
    echo "Total Price: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
    echo "Voucher Discount: Rp " . number_format($booking->voucher_discount ?? 0, 0, ',', '.') . "\n\n";
    
    // 3. Check if referral already exists
    $existingReferral = AffiliateReferral::where('booking_id', $booking->id)->first();
    
    if ($existingReferral) {
        echo "ℹ️  Referral sudah ada (ID: {$existingReferral->id})\n";
        echo "Akan diupdate...\n\n";
    }
    
    // 4. Calculate correct commission
    $totalPax = $booking->getTotalPax();
    $commissionPerPax = $husni->min_sale_commission ?? 1000000; // Rp 1 juta
    $totalCommission = $commissionPerPax * $totalPax;
    $termin1 = round($totalCommission * 0.5, 2);
    $termin2 = $totalCommission - $termin1;
    
    echo "=== CORRECT CALCULATION ===\n";
    echo "Commission Per Pax: Rp " . number_format($commissionPerPax, 0, ',', '.') . "\n";
    echo "Total Pax: {$totalPax}\n";
    echo "Total Commission: Rp " . number_format($totalCommission, 0, ',', '.') . "\n";
    echo "Termin 1: Rp " . number_format($termin1, 0, ',', '.') . "\n";
    echo "Termin 2: Rp " . number_format($termin2, 0, ',', '.') . "\n\n";
    
    // 5. Create or update referral
    DB::beginTransaction();
    
    if ($existingReferral) {
        $existingReferral->update([
            'affiliator_id' => $husni->id,
            'commission_amount' => $totalCommission,
            'termin_1_amount' => $termin1,
            'termin_2_amount' => $termin2,
            'total_pax' => $totalPax,
        ]);
        $referral = $existingReferral;
        echo "✅ Referral updated (ID: {$referral->id})\n";
    } else {
        $referral = AffiliateReferral::create([
            'affiliator_id' => $husni->id,
            'booking_id' => $booking->id,
            'package_id' => $booking->id_travel_package,
            'order_reference' => $booking->booking_code,
            'order_amount' => $booking->total_price,
            'commission_amount' => $totalCommission,
            'commission_type' => 'flat',
            'commission_rate' => $commissionPerPax,
            'total_pax' => $totalPax,
            'voucher_discount' => $booking->voucher_discount ?? 0,
            'order_date' => now(),
            'termin_1_amount' => $termin1,
            'termin_2_amount' => $termin2,
            'status' => 'pending',
        ]);
        echo "✅ Referral created (ID: {$referral->id})\n";
    }
    
    // 6. Release termin 1 if booking is paid
    if ($booking->payment_status === 'paid' && !$referral->termin_1_released) {
        $husni->releaseTermin1($referral->id);
        echo "✅ Termin 1 released\n";
    }
    
    DB::commit();
    
    echo "\n=== VERIFICATION ===\n";
    $referral->refresh();
    $husni->refresh();
    
    echo "Referral ID: {$referral->id}\n";
    echo "Commission: Rp " . number_format($referral->commission_amount, 0, ',', '.') . "\n";
    echo "Termin 1: Rp " . number_format($referral->termin_1_amount, 0, ',', '.') . " - " . ($referral->termin_1_released ? 'RELEASED' : 'PENDING') . "\n";
    echo "Termin 2: Rp " . number_format($referral->termin_2_amount, 0, ',', '.') . " - " . ($referral->termin_2_released ? 'RELEASED' : 'PENDING') . "\n";
    echo "Mitra Pending Balance: Rp " . number_format($husni->pending_balance, 0, ',', '.') . "\n";
    
    echo "\n✅ DONE!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
