<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_keberangkatan',
        'equipment_name',
        'equipment_category',
        'quantity_needed',
        'quantity_received',
        'status',
        'supplier_name',
        'order_date',
        'shipping_deadline',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'shipping_deadline' => 'date',
        'quantity_needed' => 'integer',
        'quantity_received' => 'integer'
    ];

    /**
     * Get the keberangkatan that owns this equipment checklist
     */
    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }

    /**
     * Check if equipment is fully received
     */
    public function isFullyReceived()
    {
        return $this->quantity_received >= $this->quantity_needed;
    }

    /**
     * Check if shipping deadline is approaching (within 3 days)
     */
    public function isDeadlineApproaching()
    {
        if (!$this->shipping_deadline) {
            return false;
        }
        
        return $this->shipping_deadline->diffInDays(now(), false) <= 3 && 
               $this->shipping_deadline->isFuture();
    }

    /**
     * Check if shipping deadline is overdue
     */
    public function isDeadlineOverdue()
    {
        if (!$this->shipping_deadline) {
            return false;
        }
        
        return $this->shipping_deadline->isPast() && $this->status !== 'shipped';
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'not_ordered' => 'secondary',
            'ordered' => 'info',
            'received' => 'primary',
            'packed' => 'warning',
            'shipped' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get status label for UI
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            'not_ordered' => 'Belum Dipesan',
            'ordered' => 'Dipesan',
            'received' => 'Diterima',
            'packed' => 'Dikemas',
            'shipped' => 'Dikirim',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Scope to get items with approaching deadlines
     */
    public function scopeDeadlineApproaching($query)
    {
        return $query->whereNotNull('shipping_deadline')
                    ->whereDate('shipping_deadline', '>=', now())
                    ->whereDate('shipping_deadline', '<=', now()->addDays(3))
                    ->where('status', '!=', 'shipped');
    }

    /**
     * Scope to get overdue items
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('shipping_deadline')
                    ->whereDate('shipping_deadline', '<', now())
                    ->where('status', '!=', 'shipped');
    }
}
