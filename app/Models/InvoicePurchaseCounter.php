<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePurchaseCounter extends Model
{
    use HasFactory;

    protected $table = 'invoice_purchase_counter';
    protected $primaryKey = 'id_counter';

    protected $fillable = [
        'last_invoice_number',
        'last_po_number',
        'year'
    ];

    /**
     * Generate PO Number
     */
    public static function generatePONumber()
    {
        $counter = self::first();
        
        if (!$counter) {
            $counter = new self();
            $counter->last_po_number = 0;
            $counter->last_invoice_number = 0;
            $counter->year = date('Y');
        }

        // Reset counter if year changed
        if ($counter->year != date('Y')) {
            $counter->last_po_number = 0;
            $counter->last_invoice_number = 0;
            $counter->year = date('Y');
        }

        $counter->last_po_number++;
        $counter->save();

        return str_pad($counter->last_po_number, 3, '0', STR_PAD_LEFT) . '/PO/' . self::getRomanMonth() . '/' . $counter->year;
    }

    /**
     * Generate Invoice Number
     */
    public static function generateInvoiceNumber()
    {
        $counter = self::first();
        
        if (!$counter) {
            $counter = new self();
            $counter->last_invoice_number = 0;
            $counter->last_po_number = 0;
            $counter->year = date('Y');
        }

        // Reset counter if year changed
        if ($counter->year != date('Y')) {
            $counter->last_invoice_number = 0;
            $counter->last_po_number = 0;
            $counter->year = date('Y');
        }

        $counter->last_invoice_number++;
        $counter->save();

        return str_pad($counter->last_invoice_number, 3, '0', STR_PAD_LEFT) . '/PUR.INV/' . self::getRomanMonth() . '/' . $counter->year;
    }

    /**
     * Get Roman Month
     */
    public static function getRomanMonth()
    {
        $months = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        
        return $months[date('n')];
    }

    /**
     * Set Starting Number
     */
    public static function setStartingNumber($invoiceNumber, $poNumber, $year)
    {
        $counter = self::first();
        
        if (!$counter) {
            $counter = new self();
        }

        $counter->last_invoice_number = $invoiceNumber;
        $counter->last_po_number = $poNumber;
        $counter->year = $year;
        $counter->save();
    }
}