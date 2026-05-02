<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegRegency extends Model
{
    protected $table = 'reg_regencies';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['name', 'province_id'];

    public function province()
    {
        return $this->belongsTo(RegProvince::class, 'province_id');
    }

    public function districts()
    {
        return $this->hasMany(RegDistrict::class, 'regency_id');
    }
}