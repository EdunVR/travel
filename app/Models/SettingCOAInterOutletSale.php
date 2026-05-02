<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingCOAInterOutletSale extends Model
{
    use HasFactory;

    protected $table = 'setting_coa_inter_outlet_sales';

    protected $fillable = [
        'outlet_id',
        'accounting_book_id',
        'akun_piutang_antar_outlet',
        'akun_pendapatan_antar_outlet',
        'akun_hpp',
        'akun_persediaan',
        'akun_ppn',
    ];

    /**
     * Relationship with outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    /**
     * Relationship with accounting book
     */
    public function accountingBook()
    {
        return $this->belongsTo(AccountingBook::class);
    }

    /**
     * Scope by outlet
     */
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    /**
     * Get setting by outlet
     */
    public static function getByOutlet($outletId)
    {
        return self::byOutlet($outletId)->first();
    }

    /**
     * Update or create setting for outlet
     */
    public static function updateOrCreateForOutlet($outletId, array $data)
    {
        $data['outlet_id'] = $outletId;
        
        return self::updateOrCreate(
            ['outlet_id' => $outletId],
            $data
        );
    }
}