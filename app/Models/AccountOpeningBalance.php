<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountOpeningBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_book_id',
        'account_code',
        'debit',
        'credit'
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2'
    ];

    // Relasi ke buku akuntansi
    public function accountingBook()
    {
        return $this->belongsTo(AccountingBook::class);
    }

    // Hitung saldo (debit - credit)
    public function getBalanceAttribute()
    {
        return $this->debit - $this->credit;
    }
}