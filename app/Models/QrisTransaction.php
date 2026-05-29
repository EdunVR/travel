<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrisTransaction extends Model
{
    protected $table = 'qris_transactions';

    protected $fillable = [
        'id_jamaah_booking',
        'id_jamaah_payment',
        'trx_number',
        'qris_invoice_id',
        'amount',
        'qris_content',
        'qris_nmid',
        'qris_request_date',
        'status',
        'payment_customer_name',
        'payment_method_by',
        'paid_at',
        'expired_at',
        'api_response_create',
        'api_response_check',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'api_response_create' => 'array',
        'api_response_check' => 'array',
    ];

    // Relationships
    public function booking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }

    public function payment()
    {
        return $this->belongsTo(JamaahPayment::class, 'id_jamaah_payment');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Helpers
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') return true;
        if ($this->expired_at && now()->gt($this->expired_at)) return true;
        return false;
    }

    public function markAsPaid(array $checkData = []): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_customer_name' => $checkData['qris_payment_customername'] ?? null,
            'payment_method_by' => $checkData['qris_payment_methodby'] ?? null,
            'api_response_check' => $checkData,
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }
}
