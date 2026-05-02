<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegProvince extends Model
{
    protected $table = 'reg_provinces';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function regencies()
    {
        return $this->hasMany(RegRegency::class, 'province_id');
    }
}