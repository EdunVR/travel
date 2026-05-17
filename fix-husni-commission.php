<?php

/**
 * Fix husni commission - should be Rp 1 juta per pax, not Rp 1.5 juta
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Affiliator;
use App\Models\AffiliateReferral;
use App\Models\JamaahBooking;
use Illuminate\Support\Facades\DB;

echo "=== FIX HUSNI COMMISSION ===\n\n";

try {
    // 1. Cek referral husni
    $referral = AffiliateReferral::where('affiliator_id', 8)->first();
    
    if (!$referral) {
        echo "❌ Referral tidak ditemukan!\n";
        exit(1);
    }
    
    echo "=== CURRENT DATA ===\n";
    echo "Referral ID: {$referral->id}\n";
    echo "Booking ID: {$referral->booking_id}\n";
    echo "Total Pax: {$referral->total_pax}\n";
    echo "Commission: Rp " . number_format($referral->commission_amount, 0, ',', '.') . "\n";
    echo "Termin 1: Rp " . number_format($referral->termin_1_amount, 0, ',', '.') . "\n";
    echo "Termin 2: Rp " . number_format($referral->termin_2_amount, 0, ',', '.') . "\n";
    echo "Termin 1 Released: " . ($referral->termin_1_released ? 'YES' : 'NO') . "\n\n";
    
    // 2. Calculate correct values
    $correctCommissionPerPax = 1000000; // Rp 1 juta (HM Seller)
    $totalPax = $referral->total_pax;
    $correctTotalCommission = $correctCommissionPerPax * $totalPax;
    $correctTermin1 = round($correctTotalCommission * 0.5, 2);
    $correctTermin2 = $correctTotalCommission - $correctTermin1;
    
    echo "=== CORRECT VALUES ===\n";
    echo "Commission Per Pax: Rp " . number_format($correctCommissionPerPax, 0, ',', '.') . "\n";
    echo "Total Pax: {$totalPax}\n";
    echo "Total Commission: Rp " . number_format($correctTotalCommission, 0, ',', '.') . "\n";
    echo "Termin 1: Rp " . number_format($correctTermin1, 0, ',', '.') . "\n";
    echo "Termin 2: Rp " . number_format($correctTermin2, 0, ',', '.') . "\n\n";
    
    // 3. Calculate difference
    $diffTotal = $referral->commission_amount - $correctTotalCommission;
    $diffTermin1 = $referral->termin_1_amount - $correctTermin1;
    
    echo "=== DIFFERENCE (OVERPAID) ===\n";
    echo "Total: Rp " . number_format($diffTotal, 0, ',', '.') . "\n";
    echo "Termin 1: Rp " . number_format($diffTermin1, 0, ',', '.') . "\n\n";
    
    // 4. Fix database
    echo "=== FIXING DATABASE ===\n";
    
    DB::beginTransaction();
    
    // Update referral
    $referral->update([
        'commission_amount' => $correctTotalCommission,
        'termin_1_amount' => $correctTermin1,
        'termin_2_amount' => $correctTermin2,
    ]);
    
    echo "✅ Referral updated\n";
    
    // Update mitra pending_balance (kurangi kelebihan)
    if ($referral->termin_1_released) {
        $husni = Affiliator::find(8);
        $husni->decrement('pending_balance', $diffTermin1);
        echo "✅ Mitra pending_balance corrected (reduced by Rp " . number_format($diffTermin1, 0, ',', '.') . ")\n";
    }
    
    DB::commit();
    
    echo "\n=== VERIFICATION ===\n";
    $referral->refresh();
    echo "New Commission: Rp " . number_format($referral->commission_amount, 0, ',', '.') . "\n";
    echo "New Termin 1: Rp " . number_format($referral->termin_1_amount, 0, ',', '.') . "\n";
    echo "New Termin 2: Rp " . number_format($referral->termin_2_amount, 0, ',', '.') . "\n";
    
    $husni = Affiliator::find(8);
    echo "Mitra Pending Balance: Rp " . number_format($husni->pending_balance, 0, ',', '.') . "\n";
    
    echo "\n✅ DONE!\n";
    
} catch (\Exception $e) {
    if (isset($referral)) {
        DB::rollBack();
    }
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
