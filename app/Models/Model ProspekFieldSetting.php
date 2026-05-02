<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspekFieldSetting extends Model
{
    protected $table = 'prospek_field_settings';
    protected $fillable = ['field_name', 'is_required'];
    
    public $timestamps = false;
}