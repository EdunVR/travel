<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingCOAPurchase extends Model
{
    protected $table = 'setting_coa_purchase';
    protected $primaryKey = 'id_setting';
    
    protected $fillable = [
        'id_outlet',
        'accounting_book_id',
        'akun_hutang_sementara',
        'akun_hutang_usaha',
        'akun_persediaan',
        'akun_pembelian',
        'akun_kas',
        'akun_bank',
        'akun_ppn_masukan'
    ];
    
    public function accountingBook()
    {
        return $this->belongsTo(AccountingBook::class, 'accounting_book_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
    
    // Di Models/SettingCOAPurchase.php
public function isCompleteForStatus($status)
{
    switch ($status) {
        case 'penerimaan_barang':
            return !empty($this->akun_persediaan) && !empty($this->akun_hutang_sementara);
            
        case 'vendor_bill':
            return !empty($this->akun_hutang_sementara) && 
                   !empty($this->akun_hutang_usaha) && 
                   !empty($this->akun_ppn_masukan);
                   
        case 'payment':
            return !empty($this->akun_hutang_usaha) && 
                   (!empty($this->akun_kas) || !empty($this->akun_bank));
                   
        default:
            return true;
    }
}
}