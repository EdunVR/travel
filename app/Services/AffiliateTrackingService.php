<?php

namespace App\Services;

use App\Models\Affiliator;
use App\Models\AffiliateCookie;
use App\Models\AffiliateSetting;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

class AffiliateTrackingService
{
    const COOKIE_NAME = 'aff_ref';

    /**
     * Track referral click dari URL parameter
     */
    public function trackClick($referralCode, $packageId = null, $landingPage = null)
    {
        // Cek apakah affiliate system enabled
        if (!AffiliateSetting::getValue('affiliate_enabled', true)) {
            return false;
        }

        // Cari affiliator berdasarkan username (bukan phone number lagi)
        $affiliator = Affiliator::active()
            ->where('username', $referralCode)
            ->first();

        if (!$affiliator) {
            return false;
        }

        // Fraud prevention: cek apakah IP sudah klik dalam 24 jam terakhir
        if (AffiliateSetting::getValue('click_fraud_prevention', true)) {
            $recentClick = $affiliator->clicks()
                ->where('ip_address', Request::ip())
                ->where('clicked_at', '>', now()->subDay())
                ->exists();

            if ($recentClick) {
                // Tetap set cookie tapi tidak hitung klik baru
                $this->setCookie($affiliator);
                return false;
            }
        }

        // Simpan klik
        $affiliator->addClick(
            $packageId,
            Request::ip(),
            Request::userAgent(),
            Request::header('referer'),
            $landingPage ?? Request::fullUrl()
        );

        // Set cookie
        $this->setCookie($affiliator);

        return true;
    }

    /**
     * Set cookie untuk tracking
     */
    public function setCookie(Affiliator $affiliator)
    {
        $cookieLifetime = AffiliateSetting::getValue('cookie_lifetime', 259200); // 3 hari default
        $token = AffiliateCookie::generateToken();
        
        // Simpan ke database
        AffiliateCookie::create([
            'affiliator_id' => $affiliator->id,
            'cookie_token' => $token,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'expires_at' => now()->addSeconds($cookieLifetime),
        ]);

        // Set cookie di browser
        Cookie::queue(
            self::COOKIE_NAME,
            $token,
            $cookieLifetime / 60, // Convert to minutes
            '/',
            null,
            false,
            true // httpOnly
        );

        return $token;
    }

    /**
     * Get affiliator dari cookie
     */
    public function getAffiliatorFromCookie()
    {
        $token = Cookie::get(self::COOKIE_NAME);
        
        if (!$token) {
            return null;
        }

        $cookie = AffiliateCookie::active()
            ->where('cookie_token', $token)
            ->first();

        if (!$cookie) {
            return null;
        }

        return $cookie->affiliator;
    }

    /**
     * Track sale/order dari booking
     * 
     * @param int $bookingId
     * @param int $packageId
     * @param float $orderAmount
     * @param string|null $orderReference
     * @param float $voucherDiscount
     * @param int|null $affiliatorId - Jika ada, gunakan affiliator ini (dari voucher). Jika null, ambil dari cookie
     * @return mixed
     */
    public function trackSale($bookingId, $packageId, $orderAmount, $orderReference = null, $voucherDiscount = 0, $affiliatorId = null)
    {
        // Jika affiliatorId diberikan (dari voucher), gunakan itu
        if ($affiliatorId) {
            $affiliator = Affiliator::active()->find($affiliatorId);
            
            if (!$affiliator) {
                \Log::warning('Affiliator from voucher not found or inactive', ['affiliator_id' => $affiliatorId]);
                return false;
            }
            
            \Log::info('Using affiliator from voucher', [
                'affiliator_id' => $affiliator->id,
                'affiliator_name' => $affiliator->full_name
            ]);
        } else {
            // Jika tidak ada affiliatorId, coba ambil dari cookie
            $affiliator = $this->getAffiliatorFromCookie();

            if (!$affiliator) {
                return false;
            }
            
            \Log::info('Using affiliator from cookie', [
                'affiliator_id' => $affiliator->id,
                'affiliator_name' => $affiliator->full_name
            ]);
        }

        // Get booking to calculate total pax
        $booking = \App\Models\JamaahBooking::find($bookingId);
        
        if (!$booking) {
            return false;
        }
        
        // Refresh booking to ensure we have latest data
        $booking->refresh();
        
        $totalPax = $booking->getTotalPax();
        
        \Log::info('Affiliate trackSale - Booking: ' . $bookingId . ', Total Pax: ' . $totalPax . ', Family Members: ' . ($booking->family_members_booking ? 'Yes' : 'No'));

        // Buat referral record dengan voucher discount dan total pax
        $referral = $affiliator->addReferral(
            $bookingId,
            $packageId,
            $orderAmount,
            $orderReference,
            $voucherDiscount,
            $totalPax // Pass total pax untuk perhitungan komisi
        );

        return $referral;
    }

    /**
     * Verify sale (dipanggil saat pembayaran LUNAS/pelunasan)
     * Marks referral as verified (booking paid), fee stays in pending_balance
     */
    public function verifySale($bookingId)
    {
        $referral = \App\Models\AffiliateReferral::where('booking_id', $bookingId)
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return false;
        }

        // Mark booking as paid (fee stays in pending_balance)
        return $referral->affiliator->markBookingPaid($referral->id);
    }

    /**
     * Release to available balance (dipanggil saat tanggal keberangkatan tiba)
     * Moves fee from pending_balance to available_balance
     */
    public function releaseTermin2ByBooking($bookingId)
    {
        $referral = \App\Models\AffiliateReferral::where('booking_id', $bookingId)
            ->whereIn('status', ['pending', 'verified'])
            ->first();

        if (!$referral) {
            return false;
        }

        return $referral->affiliator->releaseToAvailable($referral->id);
    }

    /**
     * Reject sale (dipanggil saat booking dibatalkan)
     */
    public function rejectSale($bookingId, $reason = null)
    {
        $referral = \App\Models\AffiliateReferral::where('booking_id', $bookingId)
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return false;
        }

        $referral->update([
            'status' => 'rejected',
            'notes'  => $reason,
        ]);

        // Kurangi pending balance affiliator utama (gunakan commission_amount)
        $commission = $referral->commission_amount ?? 0;
        if ($commission > 0) {
            $referral->affiliator->decrement('pending_balance', $commission);
            $referral->affiliator->decrement('total_earnings', $commission);
        }

        // Batalkan distribusi fee ke upline yang masih pending/released
        \App\Models\AffiliateFeeDistribution::where('referral_id', $referral->id)
            ->whereIn('status', ['pending', 'released'])
            ->get()
            ->each(function ($dist) {
                if ($dist->status === 'released') {
                    // Kembalikan dari available_balance upline (sudah di-release)
                    $dist->toAffiliator->decrement('available_balance', $dist->amount);
                } else {
                    // Kembalikan dari pending_balance upline
                    $dist->toAffiliator->decrement('pending_balance', $dist->amount);
                }
                $dist->toAffiliator->decrement('total_earnings', $dist->amount);
                $dist->update(['status' => 'rejected']);
            });

        return true;
    }

    /**
     * Clear cookie
     */
    public function clearCookie()
    {
        Cookie::queue(Cookie::forget(self::COOKIE_NAME));
    }
}
