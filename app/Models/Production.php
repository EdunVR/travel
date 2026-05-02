<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'production_code',
        'outlet_id',
        'product_id',
        'production_line',
        'target_quantity',
        'realized_quantity',
        'start_date',
        'end_date',
        'status',
        'priority',
        'max_reject_rate',
        'min_efficiency',
        'notes',
        'material_cost',
        'labor_cost',
        'operational_cost',
        'total_cost',
        'hpp_per_unit',
        'expiry_date',
        'warehouse_location',
        'business_type',
        'tofu_data',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
        'target_quantity' => 'integer',
        'realized_quantity' => 'integer',
        'max_reject_rate' => 'decimal:2',
        'min_efficiency' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'operational_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'hpp_per_unit' => 'decimal:2',
        'tofu_data' => 'array',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'id_produk');
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterial::class);
    }

    public function realizations()
    {
        return $this->hasMany(ProductionRealization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function laborCosts()
    {
        return $this->hasMany(ProductionLaborCost::class);
    }

    public function operationalCosts()
    {
        return $this->hasMany(ProductionOperationalCost::class);
    }

    public function hppRecords()
    {
        return $this->hasMany(HppProduk::class, 'production_id');
    }

    // Accessors & Mutators
    public function getProgressPercentageAttribute()
    {
        if ($this->target_quantity == 0) {
            return 0;
        }
        return round(($this->realized_quantity / $this->target_quantity) * 100, 2);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'approved' => 'Disetujui',
            'in_progress' => 'Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'slate',
            'approved' => 'blue',
            'in_progress' => 'green',
            'completed' => 'emerald',
            'cancelled' => 'red',
        ];
        return $colors[$this->status] ?? 'slate';
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['approved', 'in_progress']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Methods
    public function approve($userId)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function start()
    {
        $this->update(['status' => 'in_progress']);
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function addRealization($quantity, $rejected = 0, $notes = null)
    {
        $realization = $this->realizations()->create([
            'quantity_produced' => $quantity,
            'quantity_rejected' => $rejected,
            'realization_date' => now(),
            'notes' => $notes,
            'recorded_by' => auth()->id(),
        ]);

        // Update realized quantity
        $this->increment('realized_quantity', $quantity);

        // Auto complete if target reached
        if ($this->realized_quantity >= $this->target_quantity) {
            $this->complete();
        }

        return $realization;
    }

    // Generate production code
    public static function generateCode($outletId)
    {
        $prefix = 'PRD';
        $year = date('Y');
        $month = date('m');
        
        $lastProduction = self::where('production_code', 'like', "{$prefix}-{$year}{$month}%")
            ->orderBy('production_code', 'desc')
            ->first();

        if ($lastProduction) {
            $lastNumber = (int) substr($lastProduction->production_code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}{$newNumber}";
    }

    /**
     * Calculate total HPP including material, labor, and operational costs
     */
    public function calculateTotalHpp()
    {
        $materialCost = $this->calculateMaterialCost();
        $laborCost = $this->labor_cost ?? 0;
        $operationalCost = $this->operational_cost ?? 0;
        
        return $materialCost + $laborCost + $operationalCost;
    }

    /**
     * Calculate material cost based on current materials
     */
    public function calculateMaterialCost()
    {
        $totalCost = 0;
        
        foreach ($this->materials as $material) {
            if ($material->material_type === 'bahan') {
                $bahan = Bahan::with('hargaBahan')->find($material->material_id);
                if ($bahan && $bahan->hargaBahan->count() > 0) {
                    // Use average price from available stock
                    $avgPrice = $bahan->hargaBahan->where('stok', '>', 0)->avg('harga_beli') ?? 0;
                    $totalCost += $avgPrice * $material->quantity_required;
                }
            }
        }
        
        return $totalCost;
    }

    /**
     * Calculate HPP per unit
     */
    public function calculateHppPerUnit()
    {
        if ($this->target_quantity <= 0) {
            return 0;
        }
        
        return $this->calculateTotalHpp() / $this->target_quantity;
    }

    /**
     * Get formatted total HPP
     */
    public function getFormattedTotalHppAttribute()
    {
        return 'Rp ' . number_format($this->calculateTotalHpp(), 0, ',', '.');
    }

    /**
     * Get formatted HPP per unit
     */
    public function getFormattedHppPerUnitAttribute()
    {
        return 'Rp ' . number_format($this->calculateHppPerUnit(), 0, ',', '.');
    }
}
