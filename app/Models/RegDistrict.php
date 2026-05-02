<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegDistrict extends Model
{
    protected $table = 'reg_districts';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['name', 'regency_id'];

    public function regency()
    {
        return $this->belongsTo(RegRegency::class, 'regency_id');
    }

    public function villages()
    {
        return $this->hasMany(RegVillage::class, 'district_id');
    }
}