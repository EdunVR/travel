<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSalesCounter extends Model
{
    protected $table = 'invoice_sales_counter';
    
    protected $fillable = [
        'id_outlet',
        'invoice_prefix',
        'last_number', 
        'year'
    ];

    protected $casts = [
        'id_outlet' => 'integer',
        'last_number' => 'integer',
        'year' => 'integer'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public static function getByOutlet($outletId)
    {
        return static::where('id_outlet', $outletId)->first();
    }

    public static function updateOrCreateForOutlet($outletId, $data)
    {
        return static::updateOrCreate(
            ['id_outlet' => $outletId],
            $data
        );
    }

    public static function generateInvoiceNumber($outletId = null)
    {
        if (!$outletId) {
            $outletId = auth()->user()->outlet_id ?? 1;
        }

        $counter = self::getByOutlet($outletId);
        
        if (!$counter) {
            $counter = new self();
            $counter->id_outlet = $outletId;
            $counter->invoice_prefix = 'SLS.INV';
            $counter->last_number = 0;
            $counter->year = date('Y');
            $counter->save();
        }

        // Reset counter jika tahun berubah
        $currentYear = date('Y');
        
        if ($counter->year != $currentYear) {
            $counter->last_number = 0;
            $counter->year = $currentYear;
        }

        $counter->last_number++;
        $counter->save();

        $invoiceNumber = str_pad($counter->last_number, 3, '0', STR_PAD_LEFT) 
            . '/' . $counter->invoice_prefix . '/' 
            . self::getRomanMonth() . '/' . $counter->year;

        \Log::info('Generated invoice number', [
            'outlet_id' => $outletId,
            'invoice_number' => $invoiceNumber,
            'counter' => $counter->toArray()
        ]);

        return $invoiceNumber;
    }

    public static function getRomanMonth()
    {
        $month = date('n');
        $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romans[$month - 1] ?? 'I';
    }

    public static function setStartingNumber($startingNumber, $year, $outletId = null)
    {
        if (!$outletId) {
            $outletId = auth()->user()->outlet_id ?? 1;
        }

        $counter = self::getByOutlet($outletId);
        if (!$counter) {
            $counter = new self();
            $counter->id_outlet = $outletId;
            $counter->invoice_prefix = 'SLS.INV';
        }

        $counter->last_number = $startingNumber;
        $counter->year = $year;
        $counter->save();

        return $counter;
    }
}