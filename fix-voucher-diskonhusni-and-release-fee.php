<?php

/**
 * Script untuk:
 * 1. Update booking dengan voucher DISKONHUSNI agar ter-link ke mitra pemilik voucher
 * 2. Release fee untuk booking yang sudah lunas
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JamaahBooking;
use App\Models\AffiliateVoucher;
use App\Models\AffiliateReferral;
use App\Models\Affiliator;
use App\Services\AffiliateTrackingService;
use Illuminate\Support\Facades\DB;

echo "=== FIX VOUCHER DISKONHUSNI & RELEASE FEE ===\n\n";

try {
    DB::beginTransaction();
    
    // 1. Cari voucher DISKONHUSNI
    echo "1. Mencari voucher DISKONHUSNI...\n";
    $voucher = AffiliateVoucher::where('code', 'DISKONHUSNI')->first();
    
    if (!$voucher) {
        echo "   ❌ Voucher DISKONHUSNI tidak ditemukan!\n";
        exit(1);
    }
    
    echo "   ✅ Voucher ditemukan!\n";
    echo "   - ID: {$voucher->id}\n";
    echo "   - Code: {$voucher->code}\n";
    echo "   - Affiliator ID: {$voucher->id_affiliator}\n";
    
    $affiliator = $voucher->affiliator;
    if (!$affiliator) {
        echo "   ❌ Affiliator tidak ditemukan untuk voucher ini!\n";
        exit(1);
    }
    
    echo "   - Affiliator: {$affiliator->full_name} (ID: {$affiliator->id})\n\n";
    
    // 2. Cari semua booking yang menggunakan voucher ini
    echo "2. Mencari booking dengan voucher DISKONHUSNI...\n";
    $bookings = JamaahBooking::where('voucher_code', 'DISKONHUSNI')
        ->orWhere('id_voucher', $voucher->id)
        ->with(['travelPackage', 'jamaah', 'payments'])
        ->get();
    
    echo "   Ditemukan: {$bookings->count()} booking\n\n";
    
    if ($bookings->count() === 0) {
        echo "   ℹ️  Tidak ada booking yang menggunakan voucher ini.\n";
        DB::commit();
        exit(0);
    }
    
    $affiliateService = new AffiliateTrackingService();
    $processedCount = 0;
    $feeReleasedCount = 0;
    
    foreach ($bookings as $booking) {
        echo "3. Processing Booking: {$booking->booking_code}\n";
        echo "   - Jamaah: {$booking->jamaah->nama}\n";
        echo "   - Package: {$booking->travelPackage->package_name}\n";
        echo "   - Total Price: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
        echo "   - Paid Amount: Rp " . number_format($booking->paid_amount, 0, ',', '.') . "\n";
        echo "   - Payment Status: {$booking->payment_status}\n";
        echo "   - Voucher Discount: Rp " . number_format($booking->voucher_discount ?? 0, 0, ',', '.') . "\n";
        
        // Update booking dengan id_voucher jika belum ada
        if (!$booking->id_voucher) {
            echo "   → Updating id_voucher...\n";
            $booking->update([
                'id_voucher' => $voucher->id,
                'voucher_code' => $voucher->code,
            ]);
            echo "   ✅ id_voucher updated\n";
        } else {
            echo "   ℹ️  id_voucher sudah ada\n";
        }
        
        // Cek apakah sudah ada referral
        $existingReferral = AffiliateReferral::where('booking_id', $booking->id)->first();
        
        if ($existingReferral) {
            echo "   ℹ️  Referral sudah ada (ID: {$existingReferral->id})\n";
            echo "   - Affiliator: {$existingReferral->affiliator->full_name}\n";
            echo "   - Commission: Rp " . number_format($existingReferral->commission_amount, 0, ',', '.') . "\n";
            echo "   - Status: {$existingReferral->status}\n";
            echo "   - Termin 1 Released: " . ($existingReferral->termin_1_released ? 'Yes' : 'No') . "\n";
            echo "   - Termin 2 Released: " . ($existingReferral->termin_2_released ? 'Yes' : 'No') . "\n";
            
            // Jika sudah lunas dan termin 1 belum released, release sekarang
            if ($booking->payment_status === 'paid' && !$existingReferral->termin_1_released) {
                echo "   → Releasing Termin 1...\n";
                $released = $affiliator->releaseTermin1($existingReferral->id);
                if ($released) {
                    echo "   ✅ Termin 1 released! Amount: Rp " . number_format($existingReferral->termin_1_amount, 0, ',', '.') . "\n";
                    $feeReleasedCount++;
                } else {
                    echo "   ❌ Failed to release Termin 1\n";
                }
            } elseif ($booking->payment_status === 'paid' && $existingReferral->termin_1_released) {
                echo "   ℹ️  Termin 1 sudah released sebelumnya\n";
            } else {
                echo "   ℹ️  Booking belum lunas, termin 1 tidak dirilis\n";
            }
        } else {
            echo "   → Creating new referral...\n";
            
            // Hitung total pax
            $totalPax = $booking->getTotalPax();
            echo "   - Total Pax: {$totalPax}\n";
            
            // Create referral
            $voucherDiscount = $booking->voucher_discount ?? 0;
            $referral = $affiliator->addReferral(
                $booking->id,
                $booking->id_travel_package,
                $booking->total_price,
                $booking->booking_code,
                $voucherDiscount,
                $totalPax
            );
            
            if ($referral) {
                echo "   ✅ Referral created!\n";
                echo "   - Commission: Rp " . number_format($referral->commission_amount, 0, ',', '.') . "\n";
                echo "   - Termin 1: Rp " . number_format($referral->termin_1_amount, 0, ',', '.') . "\n";
                echo "   - Termin 2: Rp " . number_format($referral->termin_2_amount, 0, ',', '.') . "\n";
                
                // Jika sudah lunas, langsung release termin 1
                if ($booking->payment_status === 'paid') {
                    echo "   → Booking sudah lunas, releasing Termin 1...\n";
                    $released = $affiliator->releaseTermin1($referral->id);
                    if ($released) {
                        echo "   ✅ Termin 1 released!\n";
                        $feeReleasedCount++;
                    } else {
                        echo "   ❌ Failed to release Termin 1\n";
                    }
                } else {
                    echo "   ℹ️  Booking belum lunas, termin 1 akan dirilis saat pelunasan\n";
                }
                
                $processedCount++;
            } else {
                echo "   ❌ Failed to create referral\n";
            }
        }
        
        echo "\n";
    }
    
    DB::commit();
    
    echo "=== SUMMARY ===\n";
    echo "Total Bookings Found: {$bookings->count()}\n";
    echo "New Referrals Created: {$processedCount}\n";
    echo "Fees Released (Termin 1): {$feeReleasedCount}\n";
    echo "\n✅ DONE!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
