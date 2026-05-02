<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProspekTimeline extends Model
{
    use HasFactory;

    protected $table = 'prospek_timeline';
    protected $fillable = ['prospek_id', 'status', 'tanggal', 'deskripsi'];

    public function prospek()
    {
        return $this->belongsTo(Prospek::class, 'prospek_id');
    }
}