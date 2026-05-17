<?php

/**
 * Script untuk cek dan fix payment status booking
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JamaahBooking;
use App\Services\AffiliateTrackingService;
use Illuminate\Support\Facades\DB;

echo "=== CHECK & FIX PAYMENT STATUS ===\n\n";

try {
    // Cari booking dengan voucher DISKONHUSNI
    $booking = JamaahBooking::where('voucher_code', 'DISKONHUSNI')
        ->with(['travelPackage', 'jamaah', 'payments'])
        ->first();
    
    if (!$booking) {
        echo "❌ Booking tidak ditemukan!\n";
        exit(1);
    }
    
    echo "Booking: {$booking->booking_code}\n";
    echo "Jamaah: {$booking->jamaah->nama}\n\n";
    
    echo "=== CURRENT STATUS ===\n";
    echo "Total Price (DB): Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
    echo "Paid Amount: Rp " . number_format($booking->paid_amount, 0, ',', '.') . "\n";
    echo "Remaining Amount (DB): Rp " . number_format($booking->remaining_amount, 0, ',', '.') . "\n";
    echo "Payment Status: {$booking->payment_status}\n";
    echo "Booking Status: {$booking->status}\n\n";
    
    echo "=== CALCULATED VALUES ===\n";
    $grandTotal = $booking->getGrandTotal();
    echo "Grand Total (calculated): Rp " . number_format($grandTotal, 0, ',', '.') . "\n";
    
    $voucherDiscount = $booking->voucher_discount ?? 0;
    echo "Voucher Discount: Rp " . number_format($voucherDiscount, 0, ',', '.') . "\n";
    
    $adminDiscount = $booking->admin_discount ?? 0;
    echo "Admin Discount: Rp " . number_format($adminDiscount, 0, ',', '.') . "\n";
    
    $finalTotal = $grandTotal - $voucherDiscount - $adminDiscount;
    echo "Final Total: Rp " . number_format($finalTotal, 0, ',', '.') . "\n";
    
    $remaining = $finalTotal - $booking->paid_amount;
    echo "Remaining (calculated): Rp " . number_format($remaining, 0, ',', '.') . "\n\n";
    
    // Cek apakah sudah lunas
    if ($remaining <= 0) {
        echo "✅ BOOKING SUDAH LUNAS!\n\n";
        
        echo "=== UPDATING STATUS ===\n";
        DB::beginTransaction();
        
        // Update payment status
        $booking->updatePaymentStatus();
        $booking->refresh();
        
        echo "✅ Payment status updated to: {$booking->payment_status}\n";
        echo "✅ Booking status updated to: {$booking->status}\n";
        echo "✅ Remaining amount updated to: Rp " . number_format($booking->remaining_amount, 0, ',', '.') . "\n\n";
        
        // Release termin 1
        echo "=== RELEASING FEE ===\n";
        $affiliateService = new AffiliateTrackingService();
        $released = $affiliateService->verifySale($booking->id);
        
        if ($released) {
            echo "✅ Termin 1 released to mitra!\n";
        } else {
            echo "ℹ️  Termin 1 already released or no referral found\n";
        }
        
        DB::commit();
        echo "\n✅ DONE!\n";
    } else {
        echo "ℹ️  Booking belum lunas. Sisa: Rp " . number_format($remaining, 0, ',', '.') . "\n";
        echo "ℹ️  Tidak ada perubahan yang dilakukan.\n";
    }
    
} catch (\Exception $e) {
    if (isset($booking)) {
        DB::rollBack();
    }
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
