<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\KeberangkatanReminder;
use App\Models\Keberangkatan;

class KeberangkatanReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public KeberangkatanReminder $reminder,
        public Keberangkatan $keberangkatan,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $label   = KeberangkatanReminder::typeLabel($this->reminder->reminder_type);
        $depDate = $this->keberangkatan->departure_date->format('d M Y');
        $daysLeft = now()->diffInDays($this->keberangkatan->departure_date, false);

        return [
            'type'                 => 'keberangkatan_reminder',
            'reminder_type'        => $this->reminder->reminder_type,
            'reminder_type_label'  => $label,
            'keberangkatan_id'     => $this->keberangkatan->id,
            'keberangkatan_code'   => $this->keberangkatan->keberangkatan_code,
            'keberangkatan_name'   => $this->keberangkatan->keberangkatan_name,
            'departure_date'       => $depDate,
            'days_left'            => $daysLeft,
            'message'              => $this->reminder->message,
            'url'                  => url("/admin/inventaris/travel/keberangkatan/{$this->keberangkatan->id}"),
        ];
    }
}
