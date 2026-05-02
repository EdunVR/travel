<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';
    
    protected $fillable = [
        'book_id',
        'outlet_id',
        'transaction_number',
        'transaction_date',
        'description',
        'status',
        'total_debit',
        'total_credit',
        'notes',
        'reference_type',
        'reference_number',
        'posted_at',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function book()
    {
        return $this->belongsTo(AccountingBook::class, 'book_id');
    }

    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id');
    }

    // Alias for details relationship (for compatibility)
    public function journalEntryDetails()
    {
        return $this->details();
    }

    /**
     * Generate transaction number for journal entry
     * Format: JE-YYYYMMDD-BOOKID-XXXX
     */
    public static function generateTransactionNumber($bookId = null)
    {
        $date = now();
        $dateStr = $date->format('Ymd');
        $bookIdStr = $bookId ? str_pad($bookId, 2, '0', STR_PAD_LEFT) : '01';
        
        // Get the last transaction number for today and this book
        $lastEntry = self::where('transaction_number', 'like', "JE-{$dateStr}-{$bookIdStr}-%")
            ->orderBy('transaction_number', 'desc')
            ->first();
        
        if ($lastEntry) {
            // Extract the sequence number from the last transaction
            $lastNumber = substr($lastEntry->transaction_number, -4);
            $nextNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        
        return "JE-{$dateStr}-{$bookIdStr}-{$nextNumber}";
    }

    /**
     * Generate reference number for journal entry
     * Format: REF-YYYYMMDD-XXXX
     */
    public static function generateReferenceNumber()
    {
        $date = now();
        $dateStr = $date->format('Ymd');
        
        // Get the last reference number for today
        $lastEntry = self::where('reference_number', 'like', "REF-{$dateStr}-%")
            ->orderBy('reference_number', 'desc')
            ->first();
        
        if ($lastEntry) {
            // Extract the sequence number from the last reference
            $lastNumber = substr($lastEntry->reference_number, -4);
            $nextNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        
        return "REF-{$dateStr}-{$nextNumber}";
    }
}
