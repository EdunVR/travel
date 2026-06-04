<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobGradeSetting extends Model
{
    protected $fillable = [
        'grade', 'min_percent', 'max_percent', 'label', 'color', 'updated_by',
    ];

    protected $casts = [
        'min_percent' => 'float',
        'max_percent' => 'float',
    ];

    /** Ambil grade berdasarkan overall progress (%) */
    public static function resolveGrade(float $percent): ?self
    {
        return static::where('min_percent', '<=', $percent)
            ->where('max_percent', '>=', $percent)
            ->orderByDesc('min_percent')
            ->first();
    }

    /** Default settings jika tabel kosong */
    public static function defaults(): array
    {
        return [
            ['grade' => 'A', 'min_percent' => 90, 'max_percent' => 100, 'label' => 'Sangat Baik',    'color' => 'emerald'],
            ['grade' => 'B', 'min_percent' => 75, 'max_percent' => 89.99, 'label' => 'Baik',         'color' => 'blue'],
            ['grade' => 'C', 'min_percent' => 60, 'max_percent' => 74.99, 'label' => 'Cukup',        'color' => 'amber'],
            ['grade' => 'D', 'min_percent' => 0,  'max_percent' => 59.99, 'label' => 'Perlu Perbaikan', 'color' => 'red'],
        ];
    }
}
