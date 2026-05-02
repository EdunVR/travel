<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceServiceCounter extends Model
{
    use HasFactory;

    protected $table = 'invoice_service_counter';
    protected $fillable = ['last_number', 'year', 'prefix', 'month'];

    // Helper method untuk mendapatkan nomor romawi
    public static function getRomanMonth($month = null)
    {
        if (!$month) {
            $month = date('n');
        }

        $romanNumerals = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $romanNumerals[$month] ?? 'I';
    }

    // Method untuk generate nomor invoice
    public static function generateInvoiceNumber()
    {
        $counter = self::first();
        $currentYear = date('Y');
        
        // Jika counter belum ada, buat baru
        if (!$counter) {
            $counter = self::create([
                'last_number' => 0,
                'year' => $currentYear,
                'month' => date('n'),
                'prefix' => 'BBN.INV'
            ]);
        }
        
        // Jika tahun berubah, reset counter
        if ($counter->year != $currentYear) {
            $counter->update([
                'last_number' => 0,
                'year' => $currentYear
            ]);
        }

        $counter->increment('last_number');
        $counter->refresh();

        $number = str_pad($counter->last_number, 3, '0', STR_PAD_LEFT);
        $romanMonth = self::getRomanMonth();
        $year = $currentYear;
        $prefix = $counter->prefix ?? 'BBN.INV';

        return "{$number}/{$prefix}/{$romanMonth}/{$year}";
    }

    // Method untuk set manual starting number
    public static function setStartingNumber($number, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $counter = self::first();
        $counter->update([
            'last_number' => $number,
            'year' => $year
        ]);

        return $counter;
    }
}