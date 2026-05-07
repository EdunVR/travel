<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompanySettings;

class JamaahBooking extends Model
{
    use HasFactory;
    use HasCompanySettings;

    protected $fillable = [
        'booking_code',
        'id_travel_package',
        'id_member',
        'id_keberangkatan',
        'booking_date',
        'status',
        'total_price',
        'payment_status',
        'paid_amount',
        'remaining_amount',
        'custom_payment_amount',
        'id_invoice',
        'id_outlet',
        'closed_by_user_id',
        'closing_source',
        'room_type',
        'price_package_name',
        'price_variant',
        'equipment_cost',
        'upgrade_cost',
        'discount_amount',
        'equipment_notes',
        'upgrade_notes',
        'terms_conditions',
        'seller_name',
        'family_members_booking',
        'payment_type',
        'dp_option',
        'bukti_pembayaran',
        'voucher_code',
        'voucher_discount',
        'admin_discount',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'custom_payment_amount' => 'decimal:2'
    ];

    /**
     * Get the travel package for this booking
     */
    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'id_travel_package');
    }

    /**
     * Get the jamaah (member) for this booking
     */
    public function jamaah()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    /**
     * Alias for jamaah() - for consistency
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    /**
     * Get the keberangkatan for this booking
     */
    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }

    /**
     * Get all payments for this booking
     */
    public function payments()
    {
        return $this->hasMany(JamaahPayment::class, 'id_jamaah_booking');
    }

    /**
     * Get add-ons for this booking
     */
    public function addons()
    {
        return $this->hasMany(\App\Models\BookingAddon::class, 'id_jamaah_booking');
    }

    /**
     * Get hotel bookings for this jamaah
     */
    public function hotelBookings()
    {
        return $this->hasMany(\App\Models\JamaahHotelBooking::class, 'id_jamaah_booking');
    }

    /**
     * Get all documents for this booking
     */
    public function documents()
    {
        return $this->hasMany(JamaahDocument::class, 'id_jamaah_booking');
    }

    /**
     * Get the outlet for this booking
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    /**
     * Get the sales invoice for this booking
     */
    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'id_invoice', 'id_sales_invoice');
    }

    /**
     * Get the user who closed this booking
     */
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    /**
     * Get the voucher used for this booking
     */
    public function voucher()
    {
        return $this->belongsTo(AffiliateVoucher::class, 'id_voucher');
    }

    /**
     * Get voucher usage record
     */
    public function voucherUsage()
    {
        return $this->hasOne(VoucherUsage::class, 'id_jamaah_booking');
    }

    /**
     * Update payment status based on paid amount
     */
    public function updatePaymentStatus()
    {
        if ($this->paid_amount == 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->paid_amount >= $this->total_price) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }
        
        $this->remaining_amount = $this->total_price - $this->paid_amount;
        $this->save();
    }

    /**
     * Generate unique booking code
     */
    public static function generateBookingCode()
    {
        $prefix = 'BKG';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Hitung grand total termasuk anggota keluarga dengan diskon usia
     */
    public function getGrandTotal(): float
    {
        // Ambil unit price dari price_packages
        $unitPrice = 0;
        $roomType = $this->price_variant ?? $this->room_type ?? 'double';
        $pkgName = $this->price_package_name ?? null;
        $pricePackages = $this->travelPackage->price_packages ?? [];
        if (is_string($pricePackages)) $pricePackages = json_decode($pricePackages, true);

        if (!empty($pricePackages) && is_array($pricePackages)) {
            $targetPkg = null;
            if ($pkgName) {
                foreach ($pricePackages as $pp) {
                    if (strtolower($pp['name'] ?? '') === strtolower($pkgName)) { $targetPkg = $pp; break; }
                }
            }
            if (!$targetPkg) $targetPkg = $pricePackages[0] ?? null;
            if ($targetPkg) {
                foreach ($targetPkg['variants'] ?? [] as $v) {
                    if (strtolower($v['type'] ?? '') === strtolower($roomType)) { $unitPrice = (float)($v['price'] ?? 0); break; }
                }
                if ($unitPrice == 0) {
                    foreach ($targetPkg['variants'] ?? [] as $v) {
                        if (strtolower($v['type'] ?? '') === 'double') { $unitPrice = (float)($v['price'] ?? 0); break; }
                    }
                }
            }
        }
        if ($unitPrice == 0) $unitPrice = (float)$this->total_price;

        // Proses anggota keluarga - ambil dari booking, bukan member
        $familyMembers = $this->family_members_booking;
        if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
        if (!is_array($familyMembers)) $familyMembers = [];

        $familyNormalCount = 0;
        $familyDiscountTotal = 0.0;
        foreach ($familyMembers as $fm) {
            if (empty($fm['tanggal_lahir'])) {
                $familyNormalCount++;
            } else {
                $age = \Carbon\Carbon::parse($fm['tanggal_lahir'])->age;
                if ($age < 2) {
                    $familyDiscountTotal += 18000000;
                } elseif ($age <= 8) {
                    $familyDiscountTotal += $unitPrice * 0.85;
                } else {
                    $familyNormalCount++;
                }
            }
        }

        $mainPax = 1 + $familyNormalCount;
        $grandTotal = ($unitPrice * $mainPax) + $familyDiscountTotal
            + ($this->equipment_cost ?? 0) + ($this->upgrade_cost ?? 0)
            - ($this->discount_amount ?? 0);

        // Add handling fee if enabled
        if ($this->travelPackage && $this->travelPackage->include_handling_lounge_fee && $this->travelPackage->handling_lounge_fee_amount > 0) {
            $grandTotal += $this->travelPackage->handling_lounge_fee_amount;
        }

        return max(0, $grandTotal);
    }

    /**
     * Hitung sisa pembayaran berdasarkan grand total
     */
    public function getRemainingBalance(): float
    {
        return max(0, $this->getGrandTotal() - (float)$this->paid_amount);
    }

    /**
     * Get final total after all discounts (voucher + admin)
     */
    public function getFinalTotal(): float
    {
        $grandTotal = $this->getGrandTotal();
        $voucherDiscount = $this->voucher_discount ?? 0;
        $adminDiscount = $this->admin_discount ?? 0;
        return max(0, $grandTotal - $voucherDiscount - $adminDiscount);
    }

    /**
     * Get remaining balance after all discounts
     */
    public function getRemainingBalanceAfterDiscounts(): float
    {
        $finalTotal = $this->getFinalTotal();
        return max(0, $finalTotal - (float)$this->paid_amount);
    }

    /**
     * Check if booking has voucher
     */
    public function hasVoucher(): bool
    {
        return !empty($this->voucher_code) && ($this->voucher_discount ?? 0) > 0;
    }

    /**
     * Check if booking has admin discount
     */
    public function hasAdminDiscount(): bool
    {
        return ($this->admin_discount ?? 0) > 0;
    }

    /**
     * Get total discount (voucher + admin)
     */
    public function getTotalDiscount(): float
    {
        return ($this->voucher_discount ?? 0) + ($this->admin_discount ?? 0);
    }

    /**
     * Override to use booking's outlet ID
     */
    protected function getCurrentOutletId()
    {
        // Use the booking's outlet ID if available
        if ($this->id_outlet) {
            return (int) $this->id_outlet;
        }
        
        // Fallback to parent trait method
        return parent::getCurrentOutletId();
    }
}
