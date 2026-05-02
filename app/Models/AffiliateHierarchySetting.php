<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateHierarchySetting extends Model
{
    protected $fillable = [
        'from_level',
        'to_level',
        'percentage',
        'fee_type',
        'fee_value',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'fee_value'  => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    /**
     * Label nama level
     */
    public static function levelLabel(string $slug): string
    {
        return match($slug) {
            'hm-seller'  => 'HM Seller',
            'hm-partner' => 'HM Partner',
            'hm-leader'  => 'HM Leader',
            'hm-master'  => 'HM Master',
            default      => ucfirst($slug),
        };
    }

    public function getFromLevelLabelAttribute(): string
    {
        return self::levelLabel($this->from_level);
    }

    public function getToLevelLabelAttribute(): string
    {
        return self::levelLabel($this->to_level);
    }

    /**
     * Ambil semua setting aktif sebagai array [from_level][to_level] => ['pct' => x, 'type' => y, 'value' => z]
     */
    public static function getMatrix(): array
    {
        $matrix = [];
        self::where('is_active', true)->get()->each(function ($s) use (&$matrix) {
            $matrix[$s->from_level][$s->to_level] = [
                'percentage' => (float) $s->percentage,
                'fee_type'   => $s->fee_type ?? 'percentage',
                'fee_value'  => (float) ($s->fee_value ?? $s->percentage),
            ];
        });
        return $matrix;
    }

    /**
     * Hitung fee berdasarkan tipe dan nilai
     */
    public static function calculateFee(float $baseCommission, string $feeType, float $feeValue): float
    {
        if ($feeType === 'flat') {
            return $feeValue;
        }
        // percentage
        return round($baseCommission * $feeValue / 100, 2);
    }
}
