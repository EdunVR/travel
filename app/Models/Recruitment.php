<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'name',
        'position',
        'status',
        'department',
        'jobdesk',
        'fingerprint_id',
        'rfid_uid',
        'is_registered_fingerprint',
        'salary',
        'hourly_rate',
        'phone',
        'email',
        'address',
        'join_date',
        'resign_date',
    ];

    // Cast kolom jobdesk ke array
    protected $casts = [
        'jobdesk' => 'array',
        'is_registered_fingerprint' => 'boolean', // Cast ke boolean
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function workSchedule()
    {
        return $this->hasOne(WorkSchedule::class, 'recruitment_id');
    }

    public function outlet()
    {
        return $this->belongsTo(\App\Models\Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }
}