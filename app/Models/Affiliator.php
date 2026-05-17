<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AffiliateHierarchySetting;
use App\Models\AffiliateFeeDistribution;

class Affiliator extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'phone_number',
        'username',
        'password',
        'full_name',
        'email',
        'photo',
        'partnership_program_id',
        'upline_master_id',
        'upline_leader_id',
        'upline_partner_id',
        'recruited_by',
        'payment_proof',
        'payment_verified_at',
        'ppc_commission',
        'min_sale_commission',
        'cookie_lifetime',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'total_earnings',
        'available_balance',
        'pending_balance',
        'total_clicks',
        'total_sales',
        'approved_at',
    ];

    protected $casts = [
        'total_earnings' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'ppc_commission' => 'decimal:2',
        'min_sale_commission' => 'decimal:2',
        'approved_at' => 'datetime',
        'payment_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partnershipProgram()
    {
        return $this->belongsTo(PartnershipProgram::class);
    }

    public function clicks()
    {
        return $this->hasMany(AffiliateClick::class);
    }

    public function referrals()
    {
        return $this->hasMany(AffiliateReferral::class);
    }

    public function payouts()
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    public function cookies()
    {
        return $this->hasMany(AffiliateCookie::class);
    }

    public function vouchers()
    {
        return $this->hasMany(AffiliateVoucher::class, 'id_affiliator');
    }

    // Jenjang upline
    public function uplineMaster()
    {
        return $this->belongsTo(Affiliator::class, 'upline_master_id');
    }

    public function uplineLeader()
    {
        return $this->belongsTo(Affiliator::class, 'upline_leader_id');
    }

    public function uplinePartner()
    {
        return $this->belongsTo(Affiliator::class, 'upline_partner_id');
    }

    // Agency fee relationships
    public function recruiter()
    {
        return $this->belongsTo(Affiliator::class, 'recruited_by');
    }

    public function recruits()
    {
        return $this->hasMany(Affiliator::class, 'recruited_by');
    }

    // Downline relationships
    public function downlineSellers()
    {
        return $this->hasMany(Affiliator::class, 'upline_partner_id');
    }

    public function downlinePartners()
    {
        return $this->hasMany(Affiliator::class, 'upline_leader_id');
    }

    public function downlineLeaders()
    {
        return $this->hasMany(Affiliator::class, 'upline_master_id');
    }

    // Fee distributions received
    public function feeDistributionsReceived()
    {
        return $this->hasMany(AffiliateFeeDistribution::class, 'to_affiliator_id');
    }

    // Fee distributions given (from downline)
    public function feeDistributionsGiven()
    {
        return $this->hasMany(AffiliateFeeDistribution::class, 'from_affiliator_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessors
    public function getReferralLinkAttribute()
    {
        return url('/?ref=' . $this->username);
    }

    public function getConversionRateAttribute()
    {
        if ($this->total_clicks == 0) {
            return 0;
        }
        return round(($this->total_sales / $this->total_clicks) * 100, 2);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/hm-tour-logo.png'); // Default logo
    }

    // Methods
    public function addClick($packageId, $ipAddress, $userAgent, $referrerUrl, $landingPage)
    {
        $commission = $this->getClickCommission($packageId);
        
        $click = $this->clicks()->create([
            'package_id' => $packageId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'referrer_url' => $referrerUrl,
            'landing_page' => $landingPage,
            'commission_amount' => $commission,
            'clicked_at' => now(),
        ]);

        // Increment total clicks
        $this->increment('total_clicks');
        
        // Langsung masukkan komisi PPC ke available_balance
        $this->increment('available_balance', $commission);
        $this->increment('total_earnings', $commission);
        
        return $click;
    }

    public function addReferral($bookingId, $packageId, $orderAmount, $orderReference = null, $voucherDiscount = 0, $totalPax = 1)
    {
        $commissionData = $this->getSaleCommission($packageId, $orderAmount);
        
        // MULTIPLY by total pax
        $baseCommission = $commissionData['amount'] * $totalPax;
        
        // IMPORTANT: Voucher discount REDUCES affiliate commission
        // Commission is calculated after voucher discount is applied
        $finalCommission = $baseCommission - $voucherDiscount; // SUBTRACT voucher discount
        
        // Ensure commission is not negative
        if ($finalCommission < 0) {
            $finalCommission = 0;
        }
        
        $referral = $this->referrals()->create([
            'booking_id'        => $bookingId,
            'package_id'        => $packageId,
            'order_reference'   => $orderReference,
            'order_amount'      => $orderAmount,
            'commission_amount' => $finalCommission,
            'commission_type'   => $commissionData['type'],
            'commission_rate'   => $commissionData['rate'],
            'total_pax'         => $totalPax, // Save total pax
            'voucher_discount'  => $voucherDiscount, // Save for reference only
            'order_date'        => now(),
            'status'            => 'pending', // Pending until payment complete
        ]);

        // Pending balance tidak ditambahkan di sini
        // Tunggu event pelunasan untuk menambahkan ke pending_balance

        // Buat distribusi fee ke upline
        $this->createFeeDistributions($referral, $finalCommission);

        return $referral;
    }

    /**
     * Buat distribusi fee ke upline berdasarkan jenjang
     */
    public function createFeeDistributions(AffiliateReferral $referral, float $baseCommission): void
    {
        $programSlug = $this->partnershipProgram?->slug ?? '';
        $matrix = AffiliateHierarchySetting::getMatrix();
        $uplines = [
            'hm-partner' => $this->upline_partner_id ? $this->uplinePartner : null,
            'hm-leader'  => $this->upline_leader_id  ? $this->uplineLeader  : null,
            'hm-master'  => $this->upline_master_id  ? $this->uplineMaster  : null,
        ];

        foreach ($uplines as $toLevel => $uplineAff) {
            if (!$uplineAff) continue;
            $setting = $matrix[$programSlug][$toLevel] ?? null;
            if (!$setting) continue;

            $feeType  = $setting['fee_type']  ?? 'percentage';
            $feeValue = $setting['fee_value'] ?? ($setting['percentage'] ?? 0);
            if ($feeValue <= 0) continue;

            $totalFee = AffiliateHierarchySetting::calculateFee($baseCommission, $feeType, $feeValue);
            if ($totalFee <= 0) continue;

            // Create single distribution (no more termin split)
            AffiliateFeeDistribution::create([
                'referral_id'        => $referral->id,
                'from_affiliator_id' => $this->id,
                'to_affiliator_id'   => $uplineAff->id,
                'level_type'         => $toLevel,
                'amount'             => $totalFee,
                'percentage'         => $feeType === 'percentage' ? $feeValue : 0,
                'status'             => 'pending',
            ]);
        }
    }

    /**
     * Release termin 1 (saat pelunasan booking)
     */
    public function releaseTermin1(int $referralId): bool
    {
        $referral = $this->referrals()->find($referralId);
        if (!$referral || $referral->termin_1_released) return false;

        $referral->update([
            'termin_1_released' => true,
            'termin_1_paid_at'  => now(),
            'status'            => 'verified',
            'verified_at'       => now(),
        ]);

        // Tambah ke pending_balance affiliator utama
        $this->increment('pending_balance', $referral->termin_1_amount);

        // Release distribusi termin 1 ke upline
        AffiliateFeeDistribution::where('referral_id', $referral->id)
            ->where('termin', 'termin_1')
            ->where('status', 'pending')
            ->get()
            ->each(function ($dist) {
                $dist->update(['status' => 'released', 'released_at' => now()]);
                $dist->toAffiliator->increment('pending_balance', $dist->amount);
            });

        return true;
    }

    /**
     * Release termin 2 (saat tanggal keberangkatan)
     */
    public function releaseTermin2(int $referralId): bool
    {
        $referral = $this->referrals()->find($referralId);
        if (!$referral || $referral->termin_2_released) return false;

        $referral->update([
            'termin_2_released' => true,
            'termin_2_paid_at'  => now(),
        ]);

        // Tambah ke pending_balance affiliator utama
        $this->increment('pending_balance', $referral->termin_2_amount);

        // Release distribusi termin 2 ke upline
        AffiliateFeeDistribution::where('referral_id', $referral->id)
            ->where('termin', 'termin_2')
            ->where('status', 'pending')
            ->get()
            ->each(function ($dist) {
                $dist->update(['status' => 'released', 'released_at' => now()]);
                $dist->toAffiliator->increment('pending_balance', $dist->amount);
            });

        return true;
    }

    public function verifyReferral($referralId)
    {
        $referral = $this->referrals()->find($referralId);
        
        if ($referral && $referral->status === 'pending') {
            $referral->update([
                'status' => 'verified',
                'verified_at' => now(),
            ]);

            // Pindahkan termin yang sudah released ke available_balance
            $releasedAmount = 0;
            if ($referral->termin_1_released) $releasedAmount += $referral->termin_1_amount;
            if ($referral->termin_2_released) $releasedAmount += $referral->termin_2_amount;

            if ($releasedAmount > 0) {
                $this->decrement('pending_balance', $releasedAmount);
                $this->increment('available_balance', $releasedAmount);
                $this->increment('total_earnings', $releasedAmount);
            }
            $this->increment('total_sales');
            
            return true;
        }
        
        return false;
    }

    private function getClickCommission($packageId)
    {
        // Gunakan komisi PPC per affiliator, bukan global
        return $this->ppc_commission ?? 50;
    }

    private function getSaleCommission($packageId, $orderAmount)
    {
        // Priority 1: Cek package-specific commission
        $packageCommission = AffiliatePackageCommission::where('package_id', $packageId)
            ->where('affiliator_id', $this->id)
            ->where('is_active', true)
            ->first();
            
        if ($packageCommission) {
            $type = $packageCommission->sale_commission_type;
            $value = $packageCommission->sale_commission_value;
            
            $amount = $type === 'percentage' 
                ? ($orderAmount * $value / 100)
                : $value;
                
            return [
                'amount' => $amount,
                'type' => $type,
                'rate' => $value,
            ];
        }
        
        // Priority 2: Gunakan commission dari partnership program
        if ($this->partnershipProgram && $this->partnershipProgram->commission_amount > 0) {
            return [
                'amount' => $this->partnershipProgram->commission_amount,
                'type' => 'flat',
                'rate' => $this->partnershipProgram->commission_amount,
            ];
        }
        
        // Priority 3: Gunakan min_sale_commission dari affiliator
        if ($this->min_sale_commission > 0) {
            return [
                'amount' => $this->min_sale_commission,
                'type' => 'flat',
                'rate' => $this->min_sale_commission,
            ];
        }
        
        // Priority 4: Fallback ke global setting
        $globalCommission = AffiliateSetting::getValue('commission_per_pax', 1000000);
        return [
            'amount' => $globalCommission,
            'type' => 'flat',
            'rate' => $globalCommission,
        ];
    }
}
