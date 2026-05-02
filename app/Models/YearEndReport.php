<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearEndReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'accounting_book_id',
        'report_data',
        'generated_by'
    ];

    protected $casts = [
        'report_data' => 'json'
    ];

    public function accountingBook()
    {
        return $this->belongsTo(AccountingBook::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}