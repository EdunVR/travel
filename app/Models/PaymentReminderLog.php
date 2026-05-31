<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReminderLog extends Model
{
    protected $table = 'payment_reminder_logs';

    protected $fillable = [
        'id_jamaah_booking', 'id_member', 'phone', 'reminder_type',
        'message', 'status', 'error_message', 'scheduled_at', 'sent_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }
}
