<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AffiliateReferral;
use Carbon\Carbon;

class ReleaseTermin2AfterDeparture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate:release-termin2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release termin 2 untuk referral yang sudah melewati tanggal keberangkatan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Starting commission release process after departure...');
        $today = Carbon::today();
        
        // Ambil semua referral yang:
        // 1. Payment condition fulfilled (termin_1_released = true)
        // 2. Commission not yet released (termin_2_released = false)
        // 3. Tanggal keberangkatan sudah lewat
        $referrals = AffiliateReferral::where('termin_1_released', true)
            ->where('termin_2_released', false)
            ->whereHas('booking.keberangkatan', function($q) use ($today) {
                $q->where('departure_date', '<=', $today);
            })
            ->with(['booking.keberangkatan', 'affiliator'])
            ->get();
        
        if ($referrals->isEmpty()) {
            $this->info('ℹ️  No referrals ready for commission release.');
            return 0;
        }
        
        $this->info("📋 Found {$referrals->count()} referral(s) ready for commission release:");
        $this->newLine();
        
        $count = 0;
        $totalAmount = 0;
        
        foreach ($referrals as $referral) {
            $affiliator = $referral->affiliator;
            $booking = $referral->booking;
            
            if (!$affiliator || !$booking) {
                $this->warn("⚠️  Skipping referral #{$referral->id}: Missing affiliator or booking");
                continue;
            }
            
            $this->line("Processing referral #{$referral->id}:");
            $this->line("  - Affiliator: {$affiliator->full_name}");
            $this->line("  - Booking: {$booking->booking_code}");
            
            $keberangkatan = $booking->keberangkatan;
            if ($keberangkatan && $keberangkatan->departure_date) {
                $this->line("  - Departure: {$keberangkatan->departure_date->format('d M Y')}");
            } else {
                $this->line("  - Departure: N/A");
            }
            
            $this->line("  - Commission: Rp " . number_format($referral->commission_amount, 0, ',', '.'));
            
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
            
            $count++;
            $totalAmount += $referral->commission_amount;
            $this->info("  ✅ Commission released to available balance");
            
            $this->newLine();
        }
        
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ Process completed!");
        $this->info("📊 Summary:");
        $this->info("   - Total processed: {$referrals->count()}");
        $this->info("   - Successfully released: {$count}");
        $this->info("   - Total amount: Rp " . number_format($totalAmount, 0, ',', '.'));
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        return 0;
    }
}
