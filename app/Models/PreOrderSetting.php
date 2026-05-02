<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrderSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'coa_penjualan',
        'coa_piutang',
        'coa_uang_muka',
        'coa_kas_bank'
    ];

    public function coaPenjualan()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_penjualan');
    }

    public function coaPiutang()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_piutang');
    }

    public function coaUangMuka()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_uang_muka');
    }

    public function coaKasBank()
    {
        return $this->belongsTo(ChartOfAccount::class, 'coa_kas_bank');
    }

    public static function getSettings()
    {
        return self::first() ?? new self();
    }
}