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
        // ═══════════════════════════════════════════════════════════════════
        // SISTEM KOMISI BARU: Budget tetap Rp2.000.000 per penjualan
        // Struktur: HM Member → HM Seller → HM Master
        // ═══════════════════════════════════════════════════════════════════
        
        $commissionData = $this->getSaleCommission($packageId, $orderAmount);
        $programSlug = $this->partnershipProgram?->slug ?? '';
        
        // Hitung distribusi komisi berdasarkan budget tetap
        $distribution = $this->calculateCommissionDistribution($programSlug, $totalPax);
        
        // Komisi untuk mitra yang closing (sudah dikalikan totalPax di dalam method)
        $closerCommission = $distribution['closer_amount'];
        
        // IMPORTANT: Voucher discount REDUCES affiliate commission dari mitra yang closing
        $finalCommission = $closerCommission - $voucherDiscount;
        
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
            'commission_type'   => 'flat',
            'commission_rate'   => $closerCommission,
            'total_pax'         => $totalPax,
            'voucher_discount'  => $voucherDiscount,
            'order_date'        => now(),
            'status'            => 'pending',
        ]);

        // Tambahkan ke pending_balance mitra yang closing
        if ($finalCommission > 0) {
            $this->increment('pending_balance', $finalCommission);
            $this->increment('total_earnings', $finalCommission);
            $this->increment('total_sales');
        }

        // Buat distribusi fee ke upline berdasarkan budget tetap
        $this->createFeeDistributions($referral, $distribution);

        return $referral;
    }

    /**
     * Hitung distribusi komisi berdasarkan budget tetap Rp2.000.000
     * 
     * Aturan:
     * - HM Member closing: Member Rp500K, Seller Rp500K, Master Rp1.000K
     * - HM Seller closing: Seller Rp1.000K, Master Rp1.000K
     * - HM Master closing: Master Rp2.000K
     * 
     * Jika tidak punya upline: hanya terima sesuai hak, bagian upline TIDAK dialihkan
     * Jika jenjang tengah kosong: bagian jenjang kosong dialihkan ke upline di atasnya
     */
    public function calculateCommissionDistribution(string $closerSlug, int $totalPax = 1): array
    {
        $budget = 2000000; // Total budget komisi per penjualan
        
        // Tentukan pembagian default berdasarkan siapa yang closing
        switch ($closerSlug) {
            case 'hm-member':
                // HM Member closing: Member 500K, Seller 500K, Master 1.000K
                $closerShare = 500000;
                $sellerShare = 500000;
                $masterShare = 1000000;
                break;
                
            case 'hm-seller':
                // HM Seller closing: Seller 1.000K, Master 1.000K
                $closerShare = 1000000;
                $sellerShare = 0; // Seller IS the closer
                $masterShare = 1000000;
                break;
                
            case 'hm-master':
                // HM Master closing: Master 2.000K
                $closerShare = 2000000;
                $sellerShare = 0;
                $masterShare = 0; // Master IS the closer
                break;
                
            default:
                // Fallback: semua ke closer
                $closerShare = $budget;
                $sellerShare = 0;
                $masterShare = 0;
                break;
        }
        
        // Cek keberadaan upline dan terapkan aturan jenjang kosong
        $uplineSeller = null;
        $uplineMaster = null;
        $distributions = [];
        
        if ($closerSlug === 'hm-member') {
            // HM Member: upline langsung = HM Seller (via upline_partner_id)
            // HM Seller di atasnya, lalu HM Master di paling atas
            $uplineSeller = $this->upline_partner_id ? $this->uplinePartner : null;
            
            if ($uplineSeller) {
                // Cek apakah seller punya upline master
                $uplineMaster = $uplineSeller->upline_master_id ? $uplineSeller->uplineMaster : null;
            } else {
                // Jika tidak ada seller, cek langsung ke master
                $uplineMaster = $this->upline_master_id ? $this->uplineMaster : null;
            }
            
            // Terapkan aturan:
            // - Jika seller TIDAK ada dan master ADA → bagian seller dialihkan ke master
            // - Jika seller TIDAK ada dan master TIDAK ada → bagian seller & master hilang (tidak dialihkan)
            // - Jika seller ADA dan master TIDAK ada → seller dapat bagiannya, master punya hilang
            
            if (!$uplineSeller && $uplineMaster) {
                // Jenjang tengah (seller) kosong → bagian seller dialihkan ke master
                $masterShare = $masterShare + $sellerShare; // 1.000K + 500K = 1.500K
                $sellerShare = 0;
            } elseif (!$uplineSeller && !$uplineMaster) {
                // Tidak ada upline sama sekali → bagian upline tidak dialihkan
                $sellerShare = 0;
                $masterShare = 0;
            } elseif ($uplineSeller && !$uplineMaster) {
                // Ada seller tapi tidak ada master → master punya hilang
                $masterShare = 0;
            }
            
            // Buat distribusi
            if ($uplineSeller && $sellerShare > 0) {
                $distributions[] = [
                    'to_affiliator' => $uplineSeller,
                    'level_type'    => 'hm-seller',
                    'amount'        => $sellerShare * $totalPax,
                ];
            }
            if ($uplineMaster && $masterShare > 0) {
                $distributions[] = [
                    'to_affiliator' => $uplineMaster,
                    'level_type'    => 'hm-master',
                    'amount'        => $masterShare * $totalPax,
                ];
            }
            
        } elseif ($closerSlug === 'hm-seller') {
            // HM Seller closing: upline = HM Master
            $uplineMaster = $this->upline_master_id ? $this->uplineMaster : null;
            
            if (!$uplineMaster) {
                // Tidak ada master → bagian master tidak dialihkan
                $masterShare = 0;
            }
            
            if ($uplineMaster && $masterShare > 0) {
                $distributions[] = [
                    'to_affiliator' => $uplineMaster,
                    'level_type'    => 'hm-master',
                    'amount'        => $masterShare * $totalPax,
                ];
            }
        }
        // HM Master closing: tidak ada distribusi ke upline (semua milik master)
        
        return [
            'closer_amount'  => $closerShare * $totalPax,
            'budget_per_pax' => $budget,
            'total_budget'   => $budget * $totalPax,
            'distributions'  => $distributions,
        ];
    }

    /**
     * Buat distribusi fee ke upline berdasarkan budget tetap
     */
    public function createFeeDistributions(AffiliateReferral $referral, array $distribution): void
    {
        foreach ($distribution['distributions'] as $dist) {
            $uplineAff = $dist['to_affiliator'];
            $amount    = $dist['amount'];
            
            if ($amount <= 0 || !$uplineAff) continue;

            // Create distribution record
            AffiliateFeeDistribution::create([
                'referral_id'        => $referral->id,
                'from_affiliator_id' => $this->id,
                'to_affiliator_id'   => $uplineAff->id,
                'level_type'         => $dist['level_type'],
                'amount'             => $amount,
                'percentage'         => 0, // Tidak pakai persentase lagi, pakai nominal tetap
                'status'             => 'pending',
            ]);
            
            // Tambahkan ke pending_balance upline
            $uplineAff->increment('pending_balance', $amount);
            $uplineAff->increment('total_earnings', $amount);
        }
    }

    /**
     * Release to Available Balance
     * Simplified flow: pending_balance → available_balance
     * Conditions: booking full paid + departure date reached (or force release by admin)
     */
    public function releaseToAvailable(int $referralId, bool $force = false): bool
    {
        $referral = $this->referrals()->find($referralId);
        if (!$referral) return false;
        
        // Already released
        if ($referral->termin_2_released) return false;

        // Check conditions (unless force release by admin)
        if (!$force) {
            // Condition 1: Booking must be full paid
            if (!$referral->termin_1_released) {
                return false;
            }
            
            // Condition 2: Departure date must be reached
            $booking = $referral->booking;
            if ($booking && $booking->keberangkatan) {
                $departureDate = $booking->keberangkatan->departure_date ?? null;
                if ($departureDate && now()->lt($departureDate)) {
                    return false; // Departure date not yet reached
                }
            }
        }

        // Mark termin_1 as released if not yet (for force release cases)
        if (!$referral->termin_1_released) {
            $referral->update([
                'termin_1_released' => true,
                'termin_1_paid_at'  => now(),
                'status'            => 'verified',
                'verified_at'       => now(),
            ]);
        }

        // Move from pending_balance to available_balance
        $commission = $referral->commission_amount;
        $this->decrement('pending_balance', $commission);
        $this->increment('available_balance', $commission);

        $referral->update([
            'termin_2_released' => true,
            'termin_2_paid_at'  => now(),
            'status'            => 'paid',
            'paid_at'           => now(),
        ]);

        // Release fee distributions to upline (move to available)
        AffiliateFeeDistribution::where('referral_id', $referral->id)
            ->where('status', 'pending')
            ->get()
            ->each(function ($dist) {
                $dist->update(['status' => 'released', 'released_at' => now()]);
                $dist->toAffiliator->decrement('pending_balance', $dist->amount);
                $dist->toAffiliator->increment('available_balance', $dist->amount);
            });

        return true;
    }

    /**
     * Mark booking as paid (termin 1) - called when jamaah payment is full
     * This just marks the referral as verified, balance stays in pending
     */
    public function markBookingPaid(int $referralId): bool
    {
        $referral = $this->referrals()->find($referralId);
        if (!$referral || $referral->termin_1_released) return false;

        $referral->update([
            'termin_1_released' => true,
            'termin_1_paid_at'  => now(),
            'status'            => 'verified',
            'verified_at'       => now(),
        ]);

        return true;
    }

    /**
     * @deprecated Use releaseToAvailable() instead
     */
    public function releaseTermin1(int $referralId): bool
    {
        return $this->markBookingPaid($referralId);
    }

    /**
     * @deprecated Use releaseToAvailable() instead
     */
    public function releaseTermin2(int $referralId): bool
    {
        return $this->releaseToAvailable($referralId);
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
