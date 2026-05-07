<?php

/**
 * Test Script: DP dan Commission x Pax
 * 
 * Script ini untuk testing perhitungan DP dan komisi berdasarkan jumlah pax
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JamaahBooking;
use App\Models\Affiliator;
use App\Models\AffiliateReferral;

echo "=== TEST DP DAN COMMISSION x PAX ===\n\n";

// Test 1: DP Calculation
echo "TEST 1: DP CALCULATION\n";
echo str_repeat("-", 50) . "\n";

$testBookings = JamaahBooking::with('jamaah')
    ->whereNotNull('family_members_booking')
    ->limit(3)
    ->get();

if ($testBookings->isEmpty()) {
    echo "⚠️  Tidak ada booking dengan family members untuk testing\n";
    echo "Membuat test case manual...\n\n";
    
    // Manual test cases
    $testCases = [
        ['pax' => 1, 'dp' => 10000000],
        ['pax' => 3, 'dp' => 30000000],
        ['pax' => 5, 'dp' => 50000000],
    ];
    
    foreach ($testCases as $case) {
        echo "Jumlah Pax: {$case['pax']}\n";
        echo "DP Expected: Rp " . number_format($case['dp'], 0, ',', '.') . "\n";
        echo "Formula: Rp 10,000,000 x {$case['pax']} pax\n";
        echo "\n";
    }
} else {
    foreach ($testBookings as $booking) {
        $totalPax = $booking->getTotalPax();
        $dpAmount = $booking->calculateDPAmount();
        
        echo "Booking: {$booking->booking_code}\n";
        echo "Jamaah: {$booking->jamaah->nama}\n";
        echo "Total Pax: {$totalPax}\n";
        echo "DP Amount: Rp " . number_format($dpAmount, 0, ',', '.') . "\n";
        echo "Formula: Rp 10,000,000 x {$totalPax} pax\n";
        echo "\n";
    }
}

// Test 2: Commission Calculation
echo "\nTEST 2: COMMISSION CALCULATION\n";
echo str_repeat("-", 50) . "\n";

$recentReferrals = AffiliateReferral::with(['affiliator', 'booking'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($recentReferrals->isEmpty()) {
    echo "⚠️  Tidak ada referral untuk testing\n";
    echo "Membuat test case manual...\n\n";
    
    // Manual test cases
    $baseCommission = 500000; // Contoh: Rp 500,000 per pax
    $testCases = [
        ['pax' => 1, 'commission' => $baseCommission * 1],
        ['pax' => 3, 'commission' => $baseCommission * 3],
        ['pax' => 5, 'commission' => $baseCommission * 5],
    ];
    
    foreach ($testCases as $case) {
        echo "Jumlah Pax: {$case['pax']}\n";
        echo "Base Commission: Rp " . number_format($baseCommission, 0, ',', '.') . " per pax\n";
        echo "Total Commission: Rp " . number_format($case['commission'], 0, ',', '.') . "\n";
        echo "Formula: Rp {$baseCommission} x {$case['pax']} pax\n";
        echo "\n";
    }
} else {
    foreach ($recentReferrals as $referral) {
        $totalPax = $referral->total_pax ?? 1;
        $commissionPerPax = $totalPax > 0 ? ($referral->commission_amount / $totalPax) : 0;
        
        echo "Referral ID: {$referral->id}\n";
        echo "Affiliator: {$referral->affiliator->full_name}\n";
        echo "Booking: {$referral->order_reference}\n";
        echo "Total Pax: {$totalPax}\n";
        echo "Commission per Pax: Rp " . number_format($commissionPerPax, 0, ',', '.') . "\n";
        echo "Total Commission: Rp " . number_format($referral->commission_amount, 0, ',', '.') . "\n";
        echo "Voucher Discount: Rp " . number_format($referral->voucher_discount ?? 0, 0, ',', '.') . "\n";
        echo "\n";
    }
}

// Test 3: Check Migration Status
echo "\nTEST 3: MIGRATION STATUS\n";
echo str_repeat("-", 50) . "\n";

try {
    $hasColumn = \Schema::hasColumn('affiliate_referrals', 'total_pax');
    
    if ($hasColumn) {
        echo "✅ Column 'total_pax' EXISTS in affiliate_referrals table\n";
        
        // Check data
        $withPax = AffiliateReferral::whereNotNull('total_pax')->where('total_pax', '>', 1)->count();
        $total = AffiliateReferral::count();
        
        echo "Total Referrals: {$total}\n";
        echo "Referrals with Pax > 1: {$withPax}\n";
    } else {
        echo "❌ Column 'total_pax' DOES NOT EXIST in affiliate_referrals table\n";
        echo "⚠️  Please run: php artisan migrate --force\n";
    }
} catch (\Exception $e) {
    echo "❌ Error checking migration: " . $e->getMessage() . "\n";
}

// Test 4: Method Availability
echo "\n\nTEST 4: METHOD AVAILABILITY\n";
echo str_repeat("-", 50) . "\n";

$booking = JamaahBooking::first();
if ($booking) {
    $methods = ['getTotalPax', 'calculateDPAmount', 'getDPAmount'];
    
    foreach ($methods as $method) {
        if (method_exists($booking, $method)) {
            echo "✅ Method '{$method}' exists in JamaahBooking\n";
            
            try {
                $result = $booking->$method();
                echo "   Result: " . (is_numeric($result) ? number_format($result, 0, ',', '.') : $result) . "\n";
            } catch (\Exception $e) {
                echo "   ⚠️  Error calling method: " . $e->getMessage() . "\n";
            }
        } else {
            echo "❌ Method '{$method}' NOT FOUND in JamaahBooking\n";
        }
    }
} else {
    echo "⚠️  No bookings found for testing\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "TEST COMPLETE\n";
echo str_repeat("=", 50) . "\n";
