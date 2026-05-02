<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBankAccount extends Model
{
    use HasFactory;

    protected $table = 'company_bank_accounts';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_outlet',
        'bank_name',
        'account_number',
        'account_holder_name',
        'branch_name',
        'currency',
        'is_active',
        'sort_order',
        'notes'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    /**
     * Scope active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by outlet
     */
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('id_outlet', $outletId);
    }

    /**
     * Get formatted account number
     */
    public function getFormattedAccountNumber()
    {
        return preg_replace('/(\d{4})(?=\d)/', '$1-', $this->account_number);
    }

    /**
     * Get full bank information
     */
    public function getFullBankInfo()
    {
        $info = "{$this->bank_name} - {$this->getFormattedAccountNumber()}";
        if ($this->branch_name) {
            $info .= " ({$this->branch_name})";
        }
        $info .= " a/n {$this->account_holder_name}";
        
        return $info;
    }
}