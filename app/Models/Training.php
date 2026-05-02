<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'training_name',
        'start_date',
        'end_date',
        'description',
        'trainer',
        'location',
    ];

    // Relasi ke tabel recruitments
    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }
}