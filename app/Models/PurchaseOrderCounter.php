<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderCounter extends Model
{
    protected $table = 'purchase_order_counter';
    protected $primaryKey = 'id_counter';
    
    protected $fillable = [
        'id_outlet',
        'last_number',
        'year',
        'prefix'
    ];
    
    public static function generatePONumber($outletId = null)
    {
        // Validasi outletId
        if (!$outletId) {
            throw new \Exception("Outlet ID diperlukan untuk generate PO Number");
        }

        $counter = self::where('id_outlet', $outletId)->first();
        
        if (!$counter) {
            $counter = new self();
            $counter->id_outlet = $outletId;
            $counter->last_number = 0;
            $counter->year = date('Y');
            $counter->prefix = 'PO';
        }
        
        // Reset counter jika tahun berubah
        if ($counter->year != date('Y')) {
            $counter->last_number = 0;
            $counter->year = date('Y');
        }
        
        $counter->last_number++;
        $counter->save();
        
        // Dapatkan kode outlet
        $outletCode = '';
        if ($outletId) {
            $outlet = Outlet::find($outletId);
            $outletCode = $outlet ? $outlet->kode_outlet : '';
        }
        
        return str_pad($counter->last_number, 3, '0', STR_PAD_LEFT) . '/' . $counter->prefix . '/' . self::getRomanMonth() . '/' . $counter->year . ($outletCode ? '/' . $outletCode : '');
    }
    
    public static function getRomanMonth()
    {
        $months = [
            'I', 'II', 'III', 'IV', 'V', 'VI',
            'VII', 'VIII', 'IX', 'X', 'XI', 'XII'
        ];
        
        return $months[date('n') - 1];
    }
    
    public static function setStartingNumber($outletId, $number, $year, $prefix = 'PO')
    {
        $counter = self::where('id_outlet', $outletId)->first();
        
        if (!$counter) {
            $counter = new self();
            $counter->id_outlet = $outletId;
        }
        
        $counter->last_number = $number;
        $counter->year = $year;
        $counter->prefix = $prefix;
        $counter->save();
        
        return $counter;
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
}