<?php

/**
 * Implementation Script for:
 * 1. DP 10 juta x pax
 * 2. Commission x pax
 * 
 * This script contains the code changes needed.
 * Apply these changes manually to the respective files.
 */

echo "=== IMPLEMENTATION GUIDE ===\n\n";

echo "TASK 1: DP 10 JUTA X PAX\n";
echo "========================\n\n";

echo "FILE 1: app/Models/JamaahBooking.php\n";
echo "ADD this method after the relationships:\n\n";

echo <<<'PHP'
    /**
     * Calculate DP amount based on total pax
     * DP = 10 juta x jumlah pax
     * 
     * @return float
     */
    public function calculateDPAmount()
    {
        $totalPax = $this->getTotalPax();
        return 10000000 * $totalPax; // 10 juta per pax
    }
    
    /**
     * Get total pax (jamaah utama + anggota keluarga)
     * 
     * @return int
     */
    public function getTotalPax()
    {
        // Jamaah utama = 1
        $total = 1;
        
        // Hitung anggota keluarga dari booking
        $familyMembers = $this->family_members_booking;
        if (is_string($familyMembers)) {
            $familyMembers = json_decode($familyMembers, true);
        }
        
        if (is_array($familyMembers)) {
            $total += count($familyMembers);
        }
        
        return $total;
    }
    
    /**
     * Get DP amount based on dp_option
     * 
     * @return float
     */
    public function getDPAmount()
    {
        if ($this->dp_option === '10_million') {
            return $this->calculateDPAmount(); // 10 juta x pax
        } elseif ($this->dp_option === '25_percent') {
            return $this->total_price * 0.25; // 25% dari total
        }
        
        // Default: 10 juta x pax
        return $this->calculateDPAmount();
    }

PHP;

echo "\n\n";
echo "FILE 2: app/Http/Controllers/PublicPackageController.php\n";
echo "UPDATE the invoice() method to use new DP calculation:\n\n";

echo <<<'PHP'
    // Around line 430, REPLACE:
    $dp5Million = 10000000;
    
    // WITH:
    $dpAmount = $booking->getDPAmount(); // Uses new method
    $dp5Million = $dpAmount; // For backward compatibility

PHP;

echo "\n\n";
echo "FILE 3: resources/views/public/invoice-booking.blade.php\n";
echo "UPDATE to display correct DP amount:\n\n";

echo <<<'PHP'
    <!-- Around line where DP is displayed -->
    @php
        $dpAmount = $booking->getDPAmount();
        $totalPax = $booking->getTotalPax();
    @endphp
    
    <div class="text-sm text-gray-600 mb-2">
        DP: Rp {{ number_format($dpAmount, 0, ',', '.') }}
        <span class="text-xs text-gray-500">(Rp 10 juta x {{ $totalPax }} pax)</span>
    </div>

PHP;

echo "\n\n";
echo "========================================\n\n";

echo "TASK 2: COMMISSION X PAX\n";
echo "=========================\n\n";

echo "FILE 1: app/Models/Affiliator.php\n";
echo "UPDATE the addReferral() method signature and calculation:\n\n";

echo <<<'PHP'
    // Around line 197, UPDATE method signature:
    public function addReferral($bookingId, $packageId, $orderAmount, $orderReference = null, $voucherDiscount = 0, $totalPax = 1)
    {
        $commissionData = $this->getSaleCommission($packageId, $orderAmount);
        
        // MULTIPLY by total pax
        $baseCommission = $commissionData['amount'] * $totalPax;
        
        // Kurangi komisi dengan diskon voucher
        $finalCommission = max(0, $baseCommission - $voucherDiscount);

        // Hitung termin: 50% saat pelunasan, 50% saat keberangkatan
        $termin1 = round($finalCommission * 0.5, 2);
        $termin2 = $finalCommission - $termin1;
        
        $referral = $this->referrals()->create([
            'booking_id'        => $bookingId,
            'package_id'        => $packageId,
            'order_reference'   => $orderReference,
            'order_amount'      => $orderAmount,
            'commission_amount' => $finalCommission,
            'commission_type'   => $commissionData['type'],
            'commission_rate'   => $commissionData['rate'],
            'voucher_discount'  => $voucherDiscount,
            'order_date'        => now(),
            'termin_1_amount'   => $termin1,
            'termin_2_amount'   => $termin2,
            'total_pax'         => $totalPax, // NEW: Store pax count
        ]);

        // ... rest of the code
    }

PHP;

echo "\n\n";
echo "FILE 2: database/migrations/xxxx_add_total_pax_to_affiliate_referrals.php\n";
echo "CREATE new migration:\n\n";

echo <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->integer('total_pax')->default(1)->after('commission_rate');
        });
    }

    public function down()
    {
        Schema::table('affiliate_referrals', function (Blueprint $table) {
            $table->dropColumn('total_pax');
        });
    }
};

PHP;

echo "\n\n";
echo "FILE 3: app/Services/AffiliateTrackingService.php\n";
echo "UPDATE trackSale() method to pass totalPax:\n\n";

echo <<<'PHP'
    // Find the trackSale method and update it:
    public function trackSale($bookingId, $packageId, $orderAmount, $orderReference = null, $voucherDiscount = 0)
    {
        // ... existing code to get affiliator ...
        
        if ($affiliator) {
            // Get booking to calculate total pax
            $booking = \App\Models\JamaahBooking::find($bookingId);
            $totalPax = $booking ? $booking->getTotalPax() : 1;
            
            // Pass totalPax to addReferral
            $referral = $affiliator->addReferral(
                $bookingId,
                $packageId,
                $orderAmount,
                $orderReference,
                $voucherDiscount,
                $totalPax // NEW PARAMETER
            );
            
            return $referral;
        }
        
        return null;
    }

PHP;

echo "\n\n";
echo "FILE 4: app/Http/Controllers/PublicPackageController.php\n";
echo "UPDATE submitBooking() method around line 1005:\n\n";

echo <<<'PHP'
    // Around line 1005, UPDATE the trackSale call:
    $voucherDiscount = $bookingData['voucher_discount'] ?? 0;
    $referral = $affiliateService->trackSale(
        $booking->id,
        $validated['package_id'],
        $validated['total_price'],
        $booking->booking_code,
        $voucherDiscount
        // totalPax will be calculated inside trackSale from booking
    );

PHP;

echo "\n\n";
echo "========================================\n\n";

echo "TESTING CHECKLIST:\n";
echo "==================\n\n";

echo "DP 10 JUTA X PAX:\n";
echo "- [ ] Create booking with 1 pax → DP should be Rp 10,000,000\n";
echo "- [ ] Create booking with 3 pax → DP should be Rp 30,000,000\n";
echo "- [ ] Create booking with 5 pax → DP should be Rp 50,000,000\n";
echo "- [ ] Check invoice page displays correct DP amount\n";
echo "- [ ] Check payment page shows correct DP\n\n";

echo "COMMISSION X PAX:\n";
echo "- [ ] Run migration: php artisan migrate\n";
echo "- [ ] Create booking with 1 pax via affiliate link → Check commission\n";
echo "- [ ] Create booking with 3 pax via affiliate link → Commission should be 3x\n";
echo "- [ ] Create booking with 5 pax via affiliate link → Commission should be 5x\n";
echo "- [ ] Check affiliate dashboard shows correct earnings\n";
echo "- [ ] Check admin affiliate panel shows correct commissions\n\n";

echo "========================================\n\n";

echo "COMMANDS TO RUN:\n";
echo "================\n\n";
echo "1. Create migration:\n";
echo "   php artisan make:migration add_total_pax_to_affiliate_referrals\n\n";
echo "2. Run migration:\n";
echo "   php artisan migrate\n\n";
echo "3. Clear cache:\n";
echo "   php artisan cache:clear\n";
echo "   php artisan config:clear\n";
echo "   php artisan view:clear\n\n";

echo "========================================\n\n";

echo "DONE! Apply the changes above to implement both fixes.\n";

