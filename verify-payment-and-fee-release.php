<?php

/**
 * Verify payment status update and fee release
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JamaahBooking;
use App\Models\AffiliateReferral;
use App\Models\Affiliator;

echo "=== VERIFICATION: PAYMENT STATUS & FEE RELEASE ===\n\n";

try {
    // 1. Cek booking
    $booking = JamaahBooking::where('voucher_code', 'DISKONHUSNI')
        ->with(['travelPackage', 'jamaah', 'payments', 'voucher'])
        ->first();
    
    if (!$booking) {
        echo "❌ Booking tidak ditemukan!\n";
        exit(1);
    }
    
    echo "=== BOOKING STATUS ===\n";
    echo "Booking Code: {$booking->booking_code}\n";
    echo "Jamaah: {$booking->jamaah->nama}\n";
    echo "Payment Status: {$booking->payment_status}\n";
    echo "Booking Status: {$booking->status}\n";
    echo "Total Price: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
    echo "Paid Amount: Rp " . number_format($booking->paid_amount, 0, ',', '.') . "\n";
    echo "Remaining Amount: Rp " . number_format($booking->remaining_amount, 0, ',', '.') . "\n";
    echo "Voucher Code: {$booking->voucher_code}\n";
    echo "Voucher Discount: Rp " . number_format($booking->voucher_discount ?? 0, 0, ',', '.') . "\n\n";
    
    // 2. Cek referral
    $referral = AffiliateReferral::where('booking_id', $booking->id)
        ->with('affiliator')
        ->first();
    
    if (!$referral) {
        echo "ℹ️  Referral tidak ditemukan untuk booking ini\n";
        echo "Mencoba cari referral untuk mitra husni...\n\n";
        
        $referral = AffiliateReferral::where('affiliator_id', 8)
            ->with('affiliator')
            ->first();
            
        if (!$referral) {
            echo "❌ Referral tidak ditemukan!\n";
            exit(1);
        }
    }
    
    echo "=== AFFILIATE REFERRAL ===\n";
    echo "Referral ID: {$referral->id}\n";
    echo "Mitra: {$referral->affiliator->full_name} (ID: {$referral->affiliator->id})\n";
    echo "Status: {$referral->status}\n";
    echo "Order Amount: Rp " . number_format($referral->order_amount, 0, ',', '.') . "\n";
    echo "Commission Amount: Rp " . number_format($referral->commission_amount, 0, ',', '.') . "\n\n";
    
    echo "=== TERMIN STATUS ===\n";
    echo "Termin 1 Amount: Rp " . number_format($referral->termin_1_amount, 0, ',', '.') . "\n";
    echo "Termin 1 Released: " . ($referral->termin_1_released ? '✅ YES' : '❌ NO') . "\n";
    echo "Termin 1 Released At: " . ($referral->termin_1_released_at ? $referral->termin_1_released_at->format('Y-m-d H:i:s') : '-') . "\n\n";
    
    echo "Termin 2 Amount: Rp " . number_format($referral->termin_2_amount, 0, ',', '.') . "\n";
    echo "Termin 2 Released: " . ($referral->termin_2_released ? '✅ YES' : '❌ NO') . "\n";
    echo "Termin 2 Released At: " . ($referral->termin_2_released_at ? $referral->termin_2_released_at->format('Y-m-d H:i:s') : '-') . "\n\n";
    
    // 3. Cek balance mitra
    $mitra = $referral->affiliator;
    echo "=== MITRA BALANCE ===\n";
    echo "Pending Balance: Rp " . number_format($mitra->pending_balance, 0, ',', '.') . "\n";
    echo "Available Balance: Rp " . number_format($mitra->available_balance, 0, ',', '.') . "\n";
    echo "Total Withdrawn: Rp " . number_format($mitra->total_withdrawn, 0, ',', '.') . "\n\n";
    
    // 4. Summary
    echo "=== SUMMARY ===\n";
    if ($booking->payment_status === 'paid' && $booking->status === 'confirmed') {
        echo "✅ Booking status: LUNAS & CONFIRMED\n";
    } else {
        echo "❌ Booking status: NOT FULLY PAID\n";
    }
    
    if ($referral->termin_1_released) {
        echo "✅ Termin 1: RELEASED (Rp " . number_format($referral->termin_1_amount, 0, ',', '.') . ")\n";
    } else {
        echo "❌ Termin 1: NOT RELEASED\n";
    }
    
    if ($mitra->pending_balance > 0) {
        echo "✅ Mitra has pending balance: Rp " . number_format($mitra->pending_balance, 0, ',', '.') . "\n";
    } else {
        echo "ℹ️  Mitra pending balance: Rp 0\n";
    }
    
    echo "\n✅ VERIFICATION COMPLETE!\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
