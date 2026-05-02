<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontraBon extends Model
{
    use HasFactory;

    protected $table = 'kontra_bon';
    protected $primaryKey = 'id_kontra_bon';
    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function details()
    {
        return $this->hasMany(KontraBonDetail::class, 'id_kontra_bon');
    }
}