<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentReminderSetting;
use App\Models\PaymentReminderLog;
use App\Models\CronJobLog;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class PaymentReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:travel.payment-reminder.view')->only(['index', 'getData']);
        $this->middleware('permission:travel.payment-reminder.update')->only(['updateSettings']);
        $this->middleware('permission:travel.payment-reminder.send')->only(['triggerManual']);
    }

    /**
     * Show the monitoring & settings page
     */
    public function index()
    {
        return view('admin.travel.payment-reminder.index');
    }

    /**
     * Get dashboard data (AJAX)
     */
    public function getData(Request $request)
    {
        // Settings
        $settings = [
            'reminder_days' => PaymentReminderSetting::getValue('reminder_days', '30,15,7'),
            'start_time' => PaymentReminderSetting::getValue('start_time', '09:00'),
            'interval_minutes' => PaymentReminderSetting::getValue('interval_minutes', '15'),
            'is_active' => PaymentReminderSetting::getValue('is_active', '1'),
            'message_template' => PaymentReminderSetting::getValue('message_template', ''),
        ];

        // Last 10 cron executions
        $cronLogs = CronJobLog::where('command', 'payment:send-reminders')
            ->orderBy('started_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'status' => $log->status,
                    'processed_count' => $log->processed_count,
                    'sent_count' => $log->sent_count,
                    'failed_count' => $log->failed_count,
                    'skipped_count' => $log->skipped_count,
                    'notes' => $log->notes,
                    'started_at' => $log->started_at ? $log->started_at->format('d/m/Y H:i:s') : null,
                    'finished_at' => $log->finished_at ? $log->finished_at->format('d/m/Y H:i:s') : null,
                    'duration' => $log->started_at && $log->finished_at
                        ? $log->started_at->diffInSeconds($log->finished_at) . 's'
                        : '-',
                ];
            });

        // Last cron run
        $lastRun = CronJobLog::where('command', 'payment:send-reminders')
            ->where('status', 'success')
            ->orderBy('started_at', 'desc')
            ->first();

        // Today's reminder stats
        $todayStats = [
            'sent' => PaymentReminderLog::whereDate('sent_at', today())->where('status', 'sent')->count(),
            'failed' => PaymentReminderLog::whereDate('created_at', today())->where('status', 'failed')->count(),
            'pending' => PaymentReminderLog::whereDate('scheduled_at', today())->where('status', 'pending')->count(),
            'skipped' => PaymentReminderLog::whereDate('created_at', today())->where('status', 'skipped')->count(),
        ];

        // Recent reminder logs (last 50)
        $reminderLogs = PaymentReminderLog::with(['booking.jamaah', 'booking.travelPackage'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'booking_code' => $log->booking->booking_code ?? '-',
                    'jamaah_name' => $log->booking->jamaah->nama ?? $log->booking->jamaah->name ?? '-',
                    'package_name' => $log->booking->travelPackage->package_name ?? '-',
                    'phone' => $log->phone,
                    'reminder_type' => $log->reminder_type,
                    'status' => $log->status,
                    'error_message' => $log->error_message,
                    'scheduled_at' => $log->scheduled_at ? $log->scheduled_at->format('d/m/Y H:i') : null,
                    'sent_at' => $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : null,
                    'created_at' => $log->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'settings' => $settings,
            'cron_logs' => $cronLogs,
            'last_run' => $lastRun ? $lastRun->started_at->format('d/m/Y H:i:s') : 'Belum pernah',
            'today_stats' => $todayStats,
            'reminder_logs' => $reminderLogs,
        ]);
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $fields = ['reminder_days', 'start_time', 'interval_minutes', 'is_active', 'message_template'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                PaymentReminderSetting::setValue($field, $request->input($field));
            }
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan']);
    }

    /**
     * Manually trigger reminder send (for testing)
     */
    public function triggerManual(Request $request)
    {
        $dryRun = $request->boolean('dry_run', false);

        try {
            $params = $dryRun ? ['--dry-run' => true] : [];
            $params['--force'] = true;

            Artisan::call('payment:send-reminders', $params);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => $dryRun ? 'Simulasi selesai' : 'Pengingat berhasil dikirim',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
