<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCookie extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliator_id',
        'cookie_token',
        'ip_address',
        'user_agent',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }
}
