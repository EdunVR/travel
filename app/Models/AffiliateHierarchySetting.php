<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateHierarchySetting extends Model
{
    protected $fillable = [
        'from_level',
        'to_level',
        'from_affiliator_id',
        'to_affiliator_id',
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
     * Relasi ke affiliator yang generate penjualan (from)
     */
    public function fromAffiliator()
    {
        return $this->belongsTo(Affiliator::class, 'from_affiliator_id');
    }

    /**
     * Relasi ke affiliator yang menerima fee (to)
     */
    public function toAffiliator()
    {
        return $this->belongsTo(Affiliator::class, 'to_affiliator_id');
    }

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
        self::where('is_active', true)
            ->whereNull('from_affiliator_id') // Hanya ambil setting global
            ->whereNull('to_affiliator_id')
            ->get()->each(function ($s) use (&$matrix) {
                $matrix[$s->from_level][$s->to_level] = [
                    'percentage' => (float) $s->percentage,
                    'fee_type'   => $s->fee_type ?? 'percentage',
                    'fee_value'  => (float) ($s->fee_value ?? $s->percentage),
                ];
            });
        return $matrix;
    }

    /**
     * Ambil fee setting untuk pasangan mitra spesifik
     * Prioritas: spesifik > global
     */
    public static function getFeeForPair(
        string $fromLevel,
        string $toLevel,
        ?int $fromAffiliatorId = null,
        ?int $toAffiliatorId = null
    ): ?array {
        // Coba cari setting spesifik dulu
        if ($fromAffiliatorId && $toAffiliatorId) {
            $specific = self::where('from_level', $fromLevel)
                ->where('to_level', $toLevel)
                ->where('from_affiliator_id', $fromAffiliatorId)
                ->where('to_affiliator_id', $toAffiliatorId)
                ->where('is_active', true)
                ->first();
            
            if ($specific) {
                return [
                    'percentage' => (float) $specific->percentage,
                    'fee_type'   => $specific->fee_type ?? 'percentage',
                    'fee_value'  => (float) ($specific->fee_value ?? $specific->percentage),
                    'is_specific' => true,
                ];
            }
        }

        // Fallback ke setting global
        $global = self::where('from_level', $fromLevel)
            ->where('to_level', $toLevel)
            ->whereNull('from_affiliator_id')
            ->whereNull('to_affiliator_id')
            ->where('is_active', true)
            ->first();

        if ($global) {
            return [
                'percentage' => (float) $global->percentage,
                'fee_type'   => $global->fee_type ?? 'percentage',
                'fee_value'  => (float) ($global->fee_value ?? $global->percentage),
                'is_specific' => false,
            ];
        }

        return null;
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
