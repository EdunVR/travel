<?php

/**
 * Check husni commission issue
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Affiliator;
use App\Models\AffiliateSetting;
use App\Models\AffiliateReferral;
use App\Models\PartnershipProgram;

echo "=== CHECK HUSNI COMMISSION ISSUE ===\n\n";

try {
    // 1. Cek data husni
    $husni = Affiliator::find(8);
    
    if (!$husni) {
        echo "❌ Husni tidak ditemukan!\n";
        exit(1);
    }
    
    echo "=== HUSNI DATA ===\n";
    echo "ID: {$husni->id}\n";
    echo "Name: {$husni->full_name}\n";
    echo "Username: {$husni->username}\n";
    echo "Partnership Program ID: {$husni->partnership_program_id}\n";
    
    if ($husni->partnershipProgram) {
        echo "Partnership Program: {$husni->partnershipProgram->name}\n";
        echo "Program Slug: {$husni->partnershipProgram->slug}\n";
        echo "Program Commission: Rp " . number_format($husni->partnershipProgram->commission_amount, 0, ',', '.') . "\n";
    }
    
    echo "Min Sale Commission: Rp " . number_format($husni->min_sale_commission ?? 0, 0, ',', '.') . "\n\n";
    
    // 2. Cek affiliate settings
    echo "=== AFFILIATE SETTINGS ===\n";
    $commissionPerPax = AffiliateSetting::getValue('commission_per_pax', 1000000);
    echo "Commission Per Pax (Global): Rp " . number_format($commissionPerPax, 0, ',', '.') . "\n\n";
    
    // 3. Cek referral yang salah
    $referral = AffiliateReferral::where('booking_id', 1) // BKG-20260514-C7B6
        ->where('affiliator_id', 8)
        ->first();
    
    if ($referral) {
        echo "=== REFERRAL DATA (WRONG) ===\n";
        echo "Referral ID: {$referral->id}\n";
        echo "Booking ID: {$referral->booking_id}\n";
        echo "Total Pax: {$referral->total_pax}\n";
        echo "Commission Amount: Rp " . number_format($referral->commission_amount, 0, ',', '.') . "\n";
        echo "Commission Type: {$referral->commission_type}\n";
        echo "Commission Rate: {$referral->commission_rate}\n";
        echo "Termin 1: Rp " . number_format($referral->termin_1_amount, 0, ',', '.') . "\n";
        echo "Termin 2: Rp " . number_format($referral->termin_2_amount, 0, ',', '.') . "\n\n";
        
        // Calculate what it should be
        echo "=== SHOULD BE ===\n";
        $correctCommissionPerPax = $husni->partnershipProgram->commission_amount ?? 1000000;
        $correctTotalCommission = $correctCommissionPerPax * $referral->total_pax;
        $correctTermin1 = round($correctTotalCommission * 0.5, 2);
        $correctTermin2 = $correctTotalCommission - $correctTermin1;
        
        echo "Commission Per Pax: Rp " . number_format($correctCommissionPerPax, 0, ',', '.') . "\n";
        echo "Total Pax: {$referral->total_pax}\n";
        echo "Total Commission: Rp " . number_format($correctTotalCommission, 0, ',', '.') . "\n";
        echo "Termin 1: Rp " . number_format($correctTermin1, 0, ',', '.') . "\n";
        echo "Termin 2: Rp " . number_format($correctTermin2, 0, ',', '.') . "\n\n";
        
        // Calculate difference
        $diff = $referral->commission_amount - $correctTotalCommission;
        echo "=== DIFFERENCE ===\n";
        echo "Overpaid: Rp " . number_format($diff, 0, ',', '.') . "\n\n";
    }
    
    // 4. Cek partnership programs
    echo "=== ALL PARTNERSHIP PROGRAMS ===\n";
    $programs = PartnershipProgram::where('is_active', true)->get();
    foreach ($programs as $prog) {
        echo "- {$prog->name} ({$prog->slug}): Rp " . number_format($prog->commission_amount, 0, ',', '.') . "\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
