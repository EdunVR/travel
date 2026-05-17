<?php

/**
 * Check all husni referrals
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Affiliator;
use App\Models\AffiliateReferral;

echo "=== ALL HUSNI REFERRALS ===\n\n";

try {
    $husni = Affiliator::find(8);
    
    echo "Mitra: {$husni->full_name}\n";
    echo "Pending Balance: Rp " . number_format($husni->pending_balance, 0, ',', '.') . "\n";
    echo "Available Balance: Rp " . number_format($husni->available_balance, 0, ',', '.') . "\n\n";
    
    $referrals = AffiliateReferral::where('affiliator_id', 8)->get();
    
    echo "Total Referrals: " . $referrals->count() . "\n\n";
    
    $totalPending = 0;
    
    foreach ($referrals as $ref) {
        echo "--- Referral #{$ref->id} ---\n";
        echo "Booking ID: {$ref->booking_id}\n";
        echo "Status: {$ref->status}\n";
        echo "Total Pax: {$ref->total_pax}\n";
        echo "Commission: Rp " . number_format($ref->commission_amount, 0, ',', '.') . "\n";
        echo "Termin 1: Rp " . number_format($ref->termin_1_amount, 0, ',', '.') . " - " . ($ref->termin_1_released ? 'RELEASED' : 'PENDING') . "\n";
        echo "Termin 2: Rp " . number_format($ref->termin_2_amount, 0, ',', '.') . " - " . ($ref->termin_2_released ? 'RELEASED' : 'PENDING') . "\n";
        
        $refPending = 0;
        if ($ref->termin_1_released) $refPending += $ref->termin_1_amount;
        if ($ref->termin_2_released) $refPending += $ref->termin_2_amount;
        
        echo "Should Add to Pending: Rp " . number_format($refPending, 0, ',', '.') . "\n\n";
        
        $totalPending += $refPending;
    }
    
    echo "=== SUMMARY ===\n";
    echo "Total Should Be in Pending: Rp " . number_format($totalPending, 0, ',', '.') . "\n";
    echo "Actual Pending Balance: Rp " . number_format($husni->pending_balance, 0, ',', '.') . "\n";
    echo "Difference: Rp " . number_format($husni->pending_balance - $totalPending, 0, ',', '.') . "\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
