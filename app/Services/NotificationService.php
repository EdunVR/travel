<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function createNotification(
        int $userId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        bool $sendEmail = false
    ): Notification {
        $notification = Notification::create([
            'user_id' => $userId,
            'notification_type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false
        ]);

        // Send email if requested and user preferences allow
        if ($sendEmail && $this->shouldSendEmail($userId, $type)) {
            $this->sendEmailNotification($userId, $title, $message);
        }

        return $notification;
    }

    /**
     * Create notifications for multiple users
     */
    public function createBulkNotifications(
        array $userIds,
        string $type,
        string $title,
        string $message,
        array $data = [],
        bool $sendEmail = false
    ): void {
        foreach ($userIds as $userId) {
            $this->createNotification($userId, $type, $title, $message, $data, $sendEmail);
        }
    }

    /**
     * Notify when a task is assigned
     * Requirement 18.1
     */
    public function notifyTaskAssigned(int $userId, $task): void
    {
        $this->createNotification(
            $userId,
            Notification::TYPE_TASK_ASSIGNED,
            'Tugas Baru Ditugaskan',
            "Anda telah ditugaskan: {$task->task_name}",
            [
                'task_id' => $task->id,
                'package_id' => $task->id_travel_package,
                'due_date' => $task->due_date?->format('Y-m-d')
            ],
            true // Send email for task assignments
        );
    }

    /**
     * Notify finance team when payment is received
     * Requirement 18.2
     */
    public function notifyPaymentReceived($payment): void
    {
        $financeTeam = Team::where('team_code', 'finance')->first();
        
        if ($financeTeam) {
            $financeUsers = $financeTeam->members()->pluck('id')->toArray();
            
            $this->createBulkNotifications(
                $financeUsers,
                Notification::TYPE_PAYMENT_RECEIVED,
                'Pembayaran Diterima',
                "Pembayaran sebesar Rp " . number_format($payment->amount, 0, ',', '.') . 
                " telah diterima untuk booking {$payment->jamaahBooking->booking_code}",
                [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->id_jamaah_booking,
                    'amount' => $payment->amount
                ],
                true
            );
        }
    }

    /**
     * Notify administration team when document is uploaded
     * Requirement 18.3
     */
    public function notifyDocumentUploaded($document): void
    {
        $adminTeam = Team::where('team_code', 'administration')->first();
        
        if ($adminTeam) {
            $adminUsers = $adminTeam->members()->pluck('id')->toArray();
            
            $this->createBulkNotifications(
                $adminUsers,
                Notification::TYPE_DOCUMENT_UPLOADED,
                'Dokumen Baru Diunggah',
                "Dokumen {$document->document_type} telah diunggah untuk verifikasi",
                [
                    'document_id' => $document->id,
                    'booking_id' => $document->id_jamaah_booking,
                    'document_type' => $document->document_type
                ],
                false
            );
        }
    }

    /**
     * Notify about approaching deadline (within 3 days)
     * Requirement 18.4
     */
    public function notifyDeadlineApproaching($task): void
    {
        if ($task->assigned_to_user) {
            $daysRemaining = now()->diffInDays($task->due_date, false);
            
            $this->createNotification(
                $task->assigned_to_user,
                Notification::TYPE_DEADLINE_APPROACHING,
                'Deadline Mendekat',
                "Tugas '{$task->task_name}' akan jatuh tempo dalam {$daysRemaining} hari",
                [
                    'task_id' => $task->id,
                    'due_date' => $task->due_date->format('Y-m-d'),
                    'days_remaining' => $daysRemaining
                ],
                true // Send email for deadline reminders
            );
        }
    }

    /**
     * Notify next responsible team when workflow stage is completed
     * Requirement 18.5
     */
    public function notifyWorkflowStageCompleted($package, $nextStage): void
    {
        $team = Team::where('team_code', $nextStage->responsible_team)->first();
        
        if ($team) {
            $teamUsers = $team->members()->pluck('id')->toArray();
            
            $this->createBulkNotifications(
                $teamUsers,
                Notification::TYPE_WORKFLOW_STAGE_COMPLETED,
                'Paket Siap untuk Tahap Berikutnya',
                "Paket {$package->package_name} telah memasuki tahap {$nextStage->stage_name}",
                [
                    'package_id' => $package->id,
                    'stage_code' => $nextStage->stage_code,
                    'stage_name' => $nextStage->stage_name
                ],
                false
            );
        }
    }

    /**
     * Check if email should be sent based on user preferences
     * Requirement 18.8
     */
    private function shouldSendEmail(int $userId, string $notificationType): bool
    {
        // For now, send emails for critical notifications
        // In future, check user preferences table
        $criticalTypes = [
            Notification::TYPE_TASK_ASSIGNED,
            Notification::TYPE_DEADLINE_APPROACHING,
            Notification::TYPE_PAYMENT_RECEIVED
        ];
        
        return in_array($notificationType, $criticalTypes);
    }

    /**
     * Send email notification
     * Requirement 18.9
     */
    private function sendEmailNotification(int $userId, string $title, string $message): void
    {
        try {
            $user = User::find($userId);
            
            if ($user && $user->email) {
                // Simple email notification
                // In production, use proper Mailable class
                Mail::raw($message, function ($mail) use ($user, $title) {
                    $mail->to($user->email)
                         ->subject($title);
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        
        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications(int $userId, int $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->recent()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->recent()
            ->count();
    }

    /**
     * Clean up old notifications (older than 90 days)
     * Requirement 18.10
     */
    public function cleanupOldNotifications(): int
    {
        return Notification::where('created_at', '<', now()->subDays(90))
            ->delete();
    }

    /**
     * Check for approaching deadlines and send notifications
     * Should be called by scheduled task
     */
    public function checkDeadlines(): void
    {
        $tasks = \App\Models\WorkflowTask::where('status', 'pending')
            ->whereNotNull('due_date')
            ->whereNotNull('assigned_to_user')
            ->whereBetween('due_date', [now(), now()->addDays(3)])
            ->get();

        foreach ($tasks as $task) {
            // Check if notification already sent today
            $existingNotification = Notification::where('user_id', $task->assigned_to_user)
                ->where('notification_type', Notification::TYPE_DEADLINE_APPROACHING)
                ->where('data->task_id', $task->id)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existingNotification) {
                $this->notifyDeadlineApproaching($task);
            }
        }
    }
}
