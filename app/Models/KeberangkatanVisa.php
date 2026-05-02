<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeberangkatanVisa extends Model
{
    use HasFactory;

    protected $table = 'keberangkatan_visas';

    protected $fillable = [
        'id_keberangkatan',
        'visa_type',
        'seller_name',
        'seller_phone',
        'price_per_person',
        'status',
        'submission_date',
        'ready_date',
        'notes',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'ready_date' => 'date',
        'price_per_person' => 'decimal:2',
    ];

    public function keberangkatan()
    {
        return $this->belongsTo(Keberangkatan::class, 'id_keberangkatan');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'     => 'Pending',
            'processing'  => 'Diproses',
            'ready'       => 'Siap',
            'distributed' => 'Sudah Dibagikan',
            default       => ucfirst($this->status),
        };
    }
}
