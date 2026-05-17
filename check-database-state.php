<?php

/**
 * Check database state
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Affiliator;
use App\Models\AffiliateReferral;
use App\Models\JamaahBooking;

echo "=== DATABASE STATE CHECK ===\n\n";

try {
    // 1. Check affiliators
    echo "=== AFFILIATORS ===\n";
    $affiliators = Affiliator::all();
    echo "Total: " . $affiliators->count() . "\n";
    foreach ($affiliators as $aff) {
        echo "- ID: {$aff->id}, Name: {$aff->full_name}, Program: " . ($aff->partnershipProgram ? $aff->partnershipProgram->name : 'None') . "\n";
    }
    echo "\n";
    
    // 2. Check referrals
    echo "=== AFFILIATE REFERRALS ===\n";
    $referrals = AffiliateReferral::with('affiliator')->get();
    echo "Total: " . $referrals->count() . "\n";
    foreach ($referrals as $ref) {
        echo "- ID: {$ref->id}, Booking: {$ref->booking_id}, Mitra: " . ($ref->affiliator ? $ref->affiliator->full_name : 'None') . ", Commission: Rp " . number_format($ref->commission_amount, 0, ',', '.') . "\n";
    }
    echo "\n";
    
    // 3. Check bookings with voucher
    echo "=== BOOKINGS WITH VOUCHER ===\n";
    $bookings = JamaahBooking::whereNotNull('voucher_code')->get();
    echo "Total: " . $bookings->count() . "\n";
    foreach ($bookings as $booking) {
        echo "- ID: {$booking->id}, Code: {$booking->booking_code}, Voucher: {$booking->voucher_code}\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
