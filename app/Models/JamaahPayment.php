<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamaahPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_jamaah_booking',
        'payment_date',
        'amount',
        'payment_method',
        'receipt_number',
        'reference_number',
        'notes',
        'recorded_by',
        'bukti_transfer',
        'payment_type',
        'verification_status',
        'verified_at',
        'verified_by',
        'verification_notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the jamaah booking that owns the payment
     */
    public function jamaahBooking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }

    /**
     * Get the user who recorded the payment
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get formatted payment method
     */
    public function getFormattedPaymentMethodAttribute()
    {
        $methods = [
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'credit_card' => 'Kartu Kredit',
            'debit_card' => 'Kartu Debit',
            'qris' => 'QRIS',
            'other' => 'Lainnya'
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Scope to filter by booking
     */
    public function scopeForBooking($query, $bookingId)
    {
        return $query->where('id_jamaah_booking', $bookingId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('payment_date', [$startDate, $endDate]);
        }
        return $query;
    }

    /**
     * Scope to filter by payment method
     */
    public function scopeByMethod($query, $method)
    {
        if ($method) {
            return $query->where('payment_method', $method);
        }
        return $query;
    }

    /**
     * Relationship: User who verified this payment
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relationship: Booking (alias for jamaahBooking)
     */
    public function booking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }

    /**
     * Scope: Pending verification payments
     */
    public function scopePendingVerification($query)
    {
        return $query->where('verification_status', 'pending_verification');
    }

    /**
     * Scope: Verified payments
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    /**
     * Scope: Rejected payments
     */
    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }
}
