<?php

namespace App\Console\Commands;

use App\Models\AffiliateReferral;
use App\Models\JamaahBooking;
use App\Models\Keberangkatan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReleaseAffiliateTermin2 extends Command
{
    protected $signature   = 'affiliate:release-termin2 {--dry-run : Tampilkan saja tanpa eksekusi}';
    protected $description = 'Release termin 2 fee affiliator untuk booking yang tanggal keberangkatannya sudah tiba';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $today  = now()->startOfDay();

        // Ambil semua referral yang termin 1 sudah cair tapi termin 2 belum
        $referrals = AffiliateReferral::where('termin_1_released', true)
            ->where('termin_2_released', false)
            ->whereIn('status', ['pending', 'verified'])
            ->with(['affiliator', 'booking.keberangkatan'])
            ->get();

        $count = 0;

        foreach ($referrals as $referral) {
            $booking = $referral->booking;
            if (!$booking) continue;

            // Cek tanggal keberangkatan dari keberangkatan yang terkait
            $keberangkatan = $booking->keberangkatan ?? null;

            // Fallback: cek dari package departure date
            $departureDate = null;
            if ($keberangkatan && $keberangkatan->departure_date) {
                $departureDate = \Carbon\Carbon::parse($keberangkatan->departure_date);
            }

            if (!$departureDate) continue;

            // Release jika tanggal keberangkatan sudah lewat atau hari ini
            if ($departureDate->startOfDay()->lte($today)) {
                if ($dryRun) {
                    $this->line("DRY RUN: Referral #{$referral->id} - {$referral->affiliator->full_name} - Rp " . number_format($referral->termin_2_amount, 0, ',', '.'));
                } else {
                    $referral->affiliator->releaseTermin2($referral->id);
                    Log::info("Affiliate termin 2 released: referral #{$referral->id}, affiliator: {$referral->affiliator->full_name}");
                    $count++;
                }
            }
        }

        if (!$dryRun) {
            $this->info("Selesai. {$count} termin 2 berhasil dicairkan.");
        }

        return Command::SUCCESS;
    }
}
