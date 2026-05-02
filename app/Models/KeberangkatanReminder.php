<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeberangkatanReminder extends Model
{
    protected $table = 'keberangkatan_reminders';

    protected $fillable = [
        'id_keberangkatan', 'reminder_type', 'target_role',
        'status', 'scheduled_at', 'sent_at', 'message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'hotel'       => 'Hotel',
            'tiket'       => 'Tiket Penerbangan',
            'visa'        => 'Visa',
            'kereta_cepat'=> 'Kereta Cepat',
            default       => ucfirst($type),
        ];
    }
}
