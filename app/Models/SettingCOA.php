<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingCOA extends Model
{
    protected $table = 'setting_coa';
    protected $primaryKey = 'id_setting_coa';
    
    protected $fillable = [
        'akun_piutang_po',       // Menyimpan code akun, e.g., '1.01.03'
        'akun_pendapatan_po',    // Menyimpan code akun, e.g., '4.01.01'
        'akun_hpp_po',           // Menyimpan code akun, e.g., '5.01.01'
        'akun_persediaan_po',    // Menyimpan code akun, e.g., '1.01.08'
        'akun_ongkir_po',        // Menyimpan code akun, e.g., '6.01.06.04'
        'akun_uang_muka_po', // Akun untuk uang muka
        'akun_pendapatan_diterima_dimuka', // Akun untuk pendapatan diterima dimuka
        'akun_diskon_penjualan', // Akun untuk diskon penjualan
        'accounting_book_id', // Buku akuntansi yang digunakan
        'created_by',
        'updated_by'
    ];

    public function accountingBook()
    {
        return $this->belongsTo(AccountingBook::class, 'accounting_book_id');
    }

    public function getNamaAkunPiutangPoAttribute()
    {
        return $this->getAccountName($this->akun_piutang_po);
    }

    public function getNamaAkunPendapatanPoAttribute()
    {
        return $this->getAccountName($this->akun_pendapatan_po);
    }

    public function getNamaAkunHppPoAttribute()
    {
        return $this->getAccountName($this->akun_hpp_po);
    }

    public function getNamaAkunPersediaanPoAttribute()
    {
        return $this->getAccountName($this->akun_persediaan_po);
    }

    public function getNamaAkunOngkirPoAttribute()
    {
        return $this->getAccountName($this->akun_ongkir_po);
    }

    /**
     * Get account name from config by code
     */
    private function getAccountName($code)
    {
        if (!$code) return null;
        
        $accounts = config('accounts.accounts', []);
        $account = $this->findAccountByCode($accounts, $code);
        
        return $account ? $account['name'] : null;
    }

    /**
     * Recursive function to find account by code in config
     */
    private function findAccountByCode($accounts, $code)
    {
        foreach ($accounts as $account) {
            if ($account['code'] === $code) {
                return $account;
            }
            
            if (isset($account['children'])) {
                $found = $this->findAccountByCode($account['children'], $code);
                if ($found) {
                    return $found;
                }
            }
        }
        
        return null;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}