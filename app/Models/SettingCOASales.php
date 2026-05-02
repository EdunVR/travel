<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingCOASales extends Model
{
    protected $table = 'setting_coa_sales';
    
    protected $fillable = [
        'id_outlet',
        'accounting_book_id',
        'akun_piutang_usaha',
        'akun_pendapatan_penjualan', 
        'akun_kas',
        'akun_bank',
        'akun_hpp',
        'akun_persediaan'
    ];

    protected $casts = [
        'id_outlet' => 'integer'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function accountingBook()
    {
        return $this->belongsTo(AccountingBook::class, 'accounting_book_id');
    }

    /**
     * Cek kelengkapan setting untuk status tertentu
     */
    public function isCompleteForStatus($status)
    {
        $required = ['akun_piutang_usaha', 'akun_pendapatan_penjualan'];
        
        if ($status === 'lunas') {
            $required[] = 'akun_kas';
            $required[] = 'akun_bank';
        }

        foreach ($required as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return !empty($this->accounting_book_id);
    }

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('id_outlet', $outletId);
    }

    /**
     * Get setting untuk outlet tertentu
     */
    public static function getByOutlet($outletId)
    {
        return static::byOutlet($outletId)->first();
    }

    /**
     * Create atau update setting untuk outlet tertentu
     */
    public static function updateOrCreateForOutlet($outletId, $data)
    {
        return static::updateOrCreate(
            ['id_outlet' => $outletId],
            $data
        );
    }
}