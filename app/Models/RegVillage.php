<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegVillage extends Model
{
    protected $table = 'reg_villages';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['name', 'district_id'];

    public function district()
    {
        return $this->belongsTo(RegDistrict::class, 'district_id');
    }
}