<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PackageViewLog extends Model
{
    protected $table = 'package_view_logs';

    protected $fillable = [
        'travel_package_id',
        'viewed_date',
        'view_count',
        'referrer',
        'source',
    ];

    protected $casts = [
        'viewed_date' => 'date',
        'view_count'  => 'integer',
    ];

    public function travelPackage()
    {
        return $this->belongsTo(TravelPackage::class, 'travel_package_id');
    }

    /**
     * Log satu view untuk paket tertentu.
     * Aggregasi per hari agar tabel tidak terlalu besar.
     */
    public static function logView(int $packageId, ?string $referrer = null, string $source = 'admin'): void
    {
        $today = now()->toDateString();

        // Upsert: tambah count jika sudah ada record hari ini, buat baru jika belum
        $existing = static::where('travel_package_id', $packageId)
            ->where('viewed_date', $today)
            ->where('source', $source)
            ->first();

        if ($existing) {
            $existing->increment('view_count');
        } else {
            static::create([
                'travel_package_id' => $packageId,
                'viewed_date'       => $today,
                'view_count'        => 1,
                'referrer'          => $referrer ? substr($referrer, 0, 500) : null,
                'source'            => $source,
            ]);
        }
    }

    /**
     * Ambil data views per hari dalam N hari terakhir untuk satu paket.
     */
    public static function getDailyViews(int $packageId, int $days = 30): array
    {
        $start = now()->subDays($days - 1)->toDateString();

        $rows = static::where('travel_package_id', $packageId)
            ->where('viewed_date', '>=', $start)
            ->select('viewed_date', DB::raw('SUM(view_count) as total'))
            ->groupBy('viewed_date')
            ->orderBy('viewed_date')
            ->get()
            ->keyBy(fn($r) => $r->viewed_date->toDateString());

        // Isi tanggal yang tidak ada viewnya dengan 0
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $result[] = [
                'date'  => $date,
                'label' => now()->subDays($i)->format('d M'),
                'views' => (int) ($rows[$date]->total ?? 0),
            ];
        }

        return $result;
    }
}
