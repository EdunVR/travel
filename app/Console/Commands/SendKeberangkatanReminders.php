<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Keberangkatan;
use App\Models\KeberangkatanReminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\KeberangkatanReminderNotification;

class SendKeberangkatanReminders extends Command
{
    protected $signature   = 'travel:send-reminders';
    protected $description = 'Kirim reminder keberangkatan 2 bulan sebelum berangkat, setiap minggu';

    // Item yang perlu diingatkan dan siapa target-nya
    const REMINDER_ITEMS = [
        'hotel'        => 'owner',
        'tiket'        => 'owner',
        'visa'         => 'owner',
        'kereta_cepat' => 'admin',
    ];

    public function handle(): void
    {
        $now       = Carbon::now();
        $twoMonths = Carbon::now()->addMonths(2);

        // Ambil keberangkatan yang departure_date dalam 2 bulan ke depan
        $keberangkatans = Keberangkatan::with(['outlet'])
            ->whereBetween('departure_date', [$now, $twoMonths])
            ->whereIn('status', ['planning', 'confirmed'])
            ->get();

        $this->info("Checking {$keberangkatans->count()} keberangkatan...");

        foreach ($keberangkatans as $k) {
            foreach (self::REMINDER_ITEMS as $type => $role) {
                $this->processReminder($k, $type, $role, $now);
            }
        }

        $this->info('Done.');
    }

    private function processReminder(Keberangkatan $k, string $type, string $role, Carbon $now): void
    {
        // Cek apakah sudah ada reminder yang dikirim dalam 7 hari terakhir
        $recentlySent = KeberangkatanReminder::where('id_keberangkatan', $k->id)
            ->where('reminder_type', $type)
            ->where('status', 'sent')
            ->where('sent_at', '>=', $now->copy()->subDays(7))
            ->exists();

        if ($recentlySent) {
            return; // Sudah dikirim minggu ini
        }

        $daysLeft = $now->diffInDays($k->departure_date, false);
        $message  = $this->buildMessage($k, $type, $daysLeft);

        // Simpan reminder
        $reminder = KeberangkatanReminder::create([
            'id_keberangkatan' => $k->id,
            'reminder_type'    => $type,
            'target_role'      => $role,
            'status'           => 'pending',
            'scheduled_at'     => $now,
            'message'          => $message,
        ]);

        // Kirim notifikasi ke user dengan role yang sesuai
        $users = User::role($role)->get();

        if ($users->isEmpty()) {
            // Fallback: kirim ke semua admin
            $users = User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super-admin']))->get();
        }

        foreach ($users as $user) {
            try {
                $user->notify(new KeberangkatanReminderNotification($reminder, $k));
            } catch (\Exception $e) {
                Log::warning("Failed to notify user {$user->id}: " . $e->getMessage());
            }
        }

        // Mark as sent
        $reminder->update(['status' => 'sent', 'sent_at' => $now]);

        $this->line("  Sent [{$type}] reminder for {$k->keberangkatan_code} ({$daysLeft} days left)");
    }

    private function buildMessage(Keberangkatan $k, string $type, int $daysLeft): string
    {
        $label    = KeberangkatanReminder::typeLabel($type);
        $depDate  = $k->departure_date->format('d M Y');
        $code     = $k->keberangkatan_code;

        return "REMINDER: {$label} untuk keberangkatan {$code} ({$depDate}) belum dikonfirmasi. Sisa {$daysLeft} hari lagi.";
    }
}
