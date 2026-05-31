<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JamaahBooking;
use App\Models\PaymentReminderSetting;
use App\Models\PaymentReminderLog;
use App\Models\CronJobLog;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    protected $signature = 'payment:send-reminders {--force : Force send even if already sent today} {--dry-run : Simulate without sending}';
    protected $description = 'Kirim pengingat pembayaran ke jamaah yang belum lunas berdasarkan jadwal H-30, H-15, H-7';

    public function handle(): int
    {
        $cronLog = CronJobLog::create([
            'command' => 'payment:send-reminders',
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            // Check if reminders are active
            if (!PaymentReminderSetting::isActive()) {
                $this->info('Payment reminders are disabled.');
                $cronLog->update(['status' => 'success', 'finished_at' => now(), 'notes' => 'Disabled by setting']);
                return 0;
            }

            $reminderDays = PaymentReminderSetting::getReminderDays();
            $startTime = PaymentReminderSetting::getStartTime();
            $intervalMinutes = PaymentReminderSetting::getIntervalMinutes();
            $messageTemplate = PaymentReminderSetting::getMessageTemplate();
            $isDryRun = $this->option('dry-run');
            $isForce = $this->option('force');

            $this->info("Reminder days: " . implode(', ', $reminderDays));
            $this->info("Start time: {$startTime}, Interval: {$intervalMinutes} min");

            $today = Carbon::today();
            $processed = 0;
            $sent = 0;
            $failed = 0;
            $skipped = 0;

            // Get all unpaid/partial bookings with upcoming departures
            $bookings = JamaahBooking::with(['jamaah', 'travelPackage', 'keberangkatan'])
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->whereNotIn('status', ['cancelled'])
                ->whereHas('keberangkatan', function ($q) use ($today, $reminderDays) {
                    $maxDays = max($reminderDays);
                    $q->where('departure_date', '>=', $today)
                      ->where('departure_date', '<=', $today->copy()->addDays($maxDays));
                })
                ->get();

            $this->info("Found {$bookings->count()} unpaid bookings with upcoming departures.");

            // Filter bookings that match reminder days
            $toSend = [];
            foreach ($bookings as $booking) {
                $departureDate = $booking->keberangkatan->departure_date;
                $daysUntilDeparture = $today->diffInDays(Carbon::parse($departureDate), false);

                if ($daysUntilDeparture < 0) continue; // Already departed

                // Check if today matches any reminder day
                foreach ($reminderDays as $reminderDay) {
                    if ($daysUntilDeparture == $reminderDay) {
                        $reminderType = "H-{$reminderDay}";

                        // Check if already sent today (unless forced)
                        if (!$isForce) {
                            $alreadySent = PaymentReminderLog::where('id_jamaah_booking', $booking->id)
                                ->where('reminder_type', $reminderType)
                                ->where('status', 'sent')
                                ->whereDate('sent_at', $today)
                                ->exists();

                            if ($alreadySent) {
                                $skipped++;
                                continue;
                            }
                        }

                        $toSend[] = [
                            'booking' => $booking,
                            'reminder_type' => $reminderType,
                            'days_left' => $daysUntilDeparture,
                        ];
                        break; // Only one reminder per booking per day
                    }
                }
            }

            $this->info("Will send " . count($toSend) . " reminders.");

            // Calculate send times (staggered by interval)
            $baseTime = Carbon::today()->setTimeFromTimeString($startTime);
            $whatsapp = new WhatsAppService();

            foreach ($toSend as $index => $item) {
                $processed++;
                $booking = $item['booking'];
                $reminderType = $item['reminder_type'];
                $daysLeft = $item['days_left'];

                // Calculate scheduled time for this message
                $scheduledTime = $baseTime->copy()->addMinutes($index * $intervalMinutes);

                // Get phone number
                $phone = $booking->jamaah->no_hp ?? $booking->jamaah->phone ?? null;
                if (!$phone) {
                    $skipped++;
                    PaymentReminderLog::create([
                        'id_jamaah_booking' => $booking->id,
                        'id_member' => $booking->id_member,
                        'phone' => null,
                        'reminder_type' => $reminderType,
                        'status' => 'skipped',
                        'error_message' => 'No phone number',
                        'scheduled_at' => $scheduledTime,
                    ]);
                    continue;
                }

                // Build message from template
                $remainingAmount = $booking->getRemainingBalanceAfterDiscounts();
                $message = str_replace(
                    ['{nama}', '{paket}', '{kode_booking}', '{sisa_bayar}', '{tgl_berangkat}', '{sisa_hari}'],
                    [
                        $booking->jamaah->nama ?? $booking->jamaah->name ?? 'Jamaah',
                        $booking->travelPackage->package_name ?? '-',
                        $booking->booking_code ?? '-',
                        number_format($remainingAmount, 0, ',', '.'),
                        $booking->keberangkatan->departure_date ? Carbon::parse($booking->keberangkatan->departure_date)->format('d M Y') : '-',
                        $daysLeft,
                    ],
                    $messageTemplate
                );

                // Create log entry
                $log = PaymentReminderLog::create([
                    'id_jamaah_booking' => $booking->id,
                    'id_member' => $booking->id_member,
                    'phone' => $phone,
                    'reminder_type' => $reminderType,
                    'message' => $message,
                    'status' => 'pending',
                    'scheduled_at' => $scheduledTime,
                ]);

                if ($isDryRun) {
                    $this->line("  [DRY-RUN] Would send to {$phone}: {$reminderType} - {$booking->booking_code}");
                    $log->update(['status' => 'skipped', 'error_message' => 'Dry run']);
                    $skipped++;
                    continue;
                }

                // Check if current time is past scheduled time (send immediately if so)
                $now = Carbon::now();
                if ($now->lt($scheduledTime)) {
                    // Not yet time to send - mark as pending for later
                    // In Hostinger cron (runs every 15 min), this will be picked up next run
                    $this->line("  [SCHEDULED] {$phone}: {$reminderType} at {$scheduledTime->format('H:i')}");
                    continue;
                }

                // Send via WhatsApp
                try {
                    $result = $whatsapp->sendMessage($phone, $message);

                    if ($result['success'] ?? false) {
                        $log->update(['status' => 'sent', 'sent_at' => now()]);
                        $sent++;
                        $this->line("  [SENT] {$phone}: {$reminderType} - {$booking->booking_code}");
                    } else {
                        $log->update([
                            'status' => 'failed',
                            'error_message' => $result['error'] ?? 'Unknown error',
                        ]);
                        $failed++;
                        $this->error("  [FAILED] {$phone}: " . ($result['error'] ?? 'Unknown'));
                    }
                } catch (\Exception $e) {
                    $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                    $failed++;
                    $this->error("  [ERROR] {$phone}: " . $e->getMessage());
                }
            }

            // Also process pending logs from earlier scheduled times
            $pendingLogs = PaymentReminderLog::where('status', 'pending')
                ->where('scheduled_at', '<=', now())
                ->whereDate('scheduled_at', $today)
                ->get();

            foreach ($pendingLogs as $log) {
                if ($isDryRun) continue;

                $processed++;
                try {
                    $result = $whatsapp->sendMessage($log->phone, $log->message);
                    if ($result['success'] ?? false) {
                        $log->update(['status' => 'sent', 'sent_at' => now()]);
                        $sent++;
                        $this->line("  [SENT-PENDING] {$log->phone}: {$log->reminder_type}");
                    } else {
                        $log->update(['status' => 'failed', 'error_message' => $result['error'] ?? 'Unknown']);
                        $failed++;
                    }
                } catch (\Exception $e) {
                    $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                    $failed++;
                }
            }

            $cronLog->update([
                'status' => 'success',
                'processed_count' => $processed,
                'sent_count' => $sent,
                'failed_count' => $failed,
                'skipped_count' => $skipped,
                'finished_at' => now(),
                'notes' => "Processed: {$processed}, Sent: {$sent}, Failed: {$failed}, Skipped: {$skipped}",
            ]);

            $this->info("Done. Processed: {$processed}, Sent: {$sent}, Failed: {$failed}, Skipped: {$skipped}");
            return 0;

        } catch (\Exception $e) {
            Log::error('SendPaymentReminders error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $cronLog->update([
                'status' => 'failed',
                'finished_at' => now(),
                'notes' => 'Error: ' . $e->getMessage(),
            ]);
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
