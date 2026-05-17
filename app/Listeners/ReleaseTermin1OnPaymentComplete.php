<?php

namespace App\Listeners;

use App\Events\BookingFullyPaid;
use App\Models\AffiliateReferral;
use Illuminate\Support\Facades\Log;

class ReleaseTermin1OnPaymentComplete
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\BookingFullyPaid  $event
     * @return void
     */
    public function handle(BookingFullyPaid $event)
    {
        $booking = $event->booking;
        
        // Cari referral terkait booking ini
        $referral = AffiliateReferral::where('booking_id', $booking->id)->first();
        
        if (!$referral) {
            Log::info("No referral found for booking #{$booking->id}");
            return;
        }
        
        if ($referral->termin_1_released) {
            Log::info("Payment condition already marked for referral #{$referral->id}");
            return;
        }
        
        // Mark payment condition as fulfilled (termin_1_released = payment complete)
        $referral->update([
            'termin_1_released' => true,
            'termin_1_paid_at' => now(),
            'status' => 'verified', // Change from pending to verified (waiting for departure)
            'verified_at' => now(),
        ]);
        
        // Add FULL commission to pending_balance (not split, full amount)
        $affiliator = $referral->affiliator;
        $affiliator->increment('pending_balance', $referral->commission_amount);
        $affiliator->increment('total_earnings', $referral->commission_amount);
        
        // Also add fee distributions to upline pending balance
        \App\Models\AffiliateFeeDistribution::where('referral_id', $referral->id)
            ->where('status', 'pending')
            ->get()
            ->each(function ($dist) {
                $dist->toAffiliator->increment('pending_balance', $dist->amount);
                $dist->toAffiliator->increment('total_earnings', $dist->amount);
            });
        
        Log::info("✅ Payment condition fulfilled for referral #{$referral->id}");
        Log::info("   Affiliator: {$referral->affiliator->full_name} (#{$referral->affiliator->id})");
        Log::info("   Booking: {$booking->booking_code}");
        Log::info("   Commission: Rp " . number_format($referral->commission_amount, 0, ',', '.'));
        Log::info("   💰 Added to PENDING balance (waiting for departure)");
        Log::info("   Status: verified → Menunggu Keberangkatan");
        
        // Check if departure condition is also met
        $this->checkAndReleaseCommission($referral);
    }
    
    /**
     * Check if both conditions are met and release commission
     */
    private function checkAndReleaseCommission($referral)
    {
        // Check if departure date has passed
        $booking = $referral->booking;
        $keberangkatan = $booking->keberangkatan;
        
        if (!$keberangkatan || !$keberangkatan->departure_date) {
            Log::info("   ⏳ No departure date set yet");
            return;
        }
        
        $today = \Carbon\Carbon::today();
        if ($keberangkatan->departure_date->lte($today)) {
            // Both conditions met! Release commission
            $affiliator = $referral->affiliator;
            
            // Move from pending to available balance
            $affiliator->decrement('pending_balance', $referral->commission_amount);
            $affiliator->increment('available_balance', $referral->commission_amount);
            
            $referral->update([
                'termin_2_released' => true,
                'termin_2_paid_at' => now(),
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            
            // Release fee distributions to upline
            \App\Models\AffiliateFeeDistribution::where('referral_id', $referral->id)
                ->where('status', 'pending')
                ->get()
                ->each(function ($dist) {
                    $dist->update(['status' => 'released', 'released_at' => now()]);
                    $dist->toAffiliator->decrement('pending_balance', $dist->amount);
                    $dist->toAffiliator->increment('available_balance', $dist->amount);
                });
            
            Log::info("   ✅✅ BOTH CONDITIONS MET! Commission released to available balance");
            Log::info("   💰 Amount: Rp " . number_format($referral->commission_amount, 0, ',', '.'));
        } else {
            Log::info("   ⏳ Departure date not reached yet: " . $keberangkatan->departure_date->format('d M Y'));
        }
    }
}
