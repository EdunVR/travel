<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'evaluation_date',
        'criteria',
        'score',
        'remarks',
    ];

    // Relasi ke tabel recruitments
    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }
}