<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_book_id',
        'code',
        'name',
        'description'
    ];

    // Relasi ke buku akuntansi
    public function accountingBook()
    {
        return $this->belongsTo(AccountingBook::class);
    }

    // Format nama sub kelas
    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }
}