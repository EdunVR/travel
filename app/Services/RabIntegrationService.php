<?php

namespace App\Services;

use App\Models\Keberangkatan;
use App\Models\RabTemplate;
use App\Models\RabDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RabIntegrationService
{
    /**
     * Create RAB for a keberangkatan
     * 
     * @param Keberangkatan $keberangkatan
     * @return RabTemplate
     * @throws \Exception
     */
    public function createRabForKeberangkatan(Keberangkatan $keberangkatan)
    {
        DB::beginTransaction();
        
        try {
            // Load necessary relationships
            $keberangkatan->load('travelPackage.hppCalculation');
            
            $package = $keberangkatan->travelPackage;
            
            if (!$package) {
                throw new \Exception('Travel package not found for keberangkatan');
            }
            
            $hpp = $package->hppCalculation;
            
            if (!$hpp) {
                throw new \Exception('HPP calculation not found for package');
            }
            
            // Generate RAB components
            $components = $this->generateRabComponents($keberangkatan);
            
            // Calculate total budget
            $totalBudget = collect($components)->sum('biaya');
            
            // Create RAB template
            $rab = RabTemplate::create([
                'outlet_id' => $keberangkatan->id_outlet,
                'book_id' => $this->getDefaultBookId($keberangkatan->id_outlet),
                'nama_template' => "RAB Keberangkatan {$keberangkatan->keberangkatan_name}",
                'deskripsi' => "Budget untuk keberangkatan {$keberangkatan->keberangkatan_code} - {$package->package_name}",
                'total_biaya' => $totalBudget,
                'is_active' => true
            ]);
            
            // Create RAB details
            foreach ($components as $component) {
                RabDetail::create([
                    'id_rab'              => $rab->id_rab,
                    'item'                => $component['item'],
                    'nama_komponen'       => $component['item'],
                    'deskripsi'           => $component['deskripsi'] ?? '',
                    'qty'                 => $component['qty'],
                    'jumlah'              => $component['qty'],
                    'satuan'              => $component['satuan'],
                    'harga'               => $component['harga_satuan'],
                    'harga_satuan'        => $component['harga_satuan'],
                    'subtotal'            => $component['biaya'],
                    'budget'              => $component['biaya'],
                    'biaya'               => $component['biaya'],
                    'nilai_disetujui'     => $component['biaya'], // semua disetujui sesuai budget
                    'realisasi_pemakaian' => $component['realisasi'], // LUNAS=budget, HUTANG=0
                    'disetujui'           => true,
                    'payment_status'      => $component['payment_status'] ?? 'lunas',
                    'hutang_amount'       => $component['hutang_amount'] ?? 0,
                ]);
            }
            
            // Link RAB to keberangkatan
            $keberangkatan->update(['id_rab' => $rab->id_rab]);
            
            DB::commit();
            
            Log::info('RAB created for keberangkatan', [
                'keberangkatan_id' => $keberangkatan->id,
                'rab_id' => $rab->id_rab,
                'total_budget' => $totalBudget
            ]);
            
            return $rab;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create RAB for keberangkatan', [
                'keberangkatan_id' => $keberangkatan->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Generate RAB components based on keberangkatan and HPP
     * Komponen LUNAS → realisasi = budget (100%), HUTANG → realisasi = 0
     */
    public function generateRabComponents(Keberangkatan $keberangkatan)
    {
        $package = $keberangkatan->travelPackage;
        $hpp = $package->hppCalculation;
        $jamaahCount = $keberangkatan->total_jamaah ?: 1;

        $payStatus = $hpp->component_payment_status ?? [];
        $hutangAmt = $hpp->component_hutang_amount ?? [];

        $components = [];

        $items = [
            ['key' => 'flight_cost',           'item' => 'Tiket Pesawat',   'desc' => 'Biaya tiket pesawat'],
            ['key' => 'hotel_cost',             'item' => 'Hotel',           'desc' => 'Biaya akomodasi hotel'],
            ['key' => 'transportation_cost',    'item' => 'Transportasi',    'desc' => 'Biaya transportasi'],
            ['key' => 'meal_cost',              'item' => 'Makan',           'desc' => 'Biaya konsumsi'],
            ['key' => 'visa_cost',              'item' => 'Visa',            'desc' => 'Biaya pengurusan visa'],
            ['key' => 'guide_cost',             'item' => 'Guide',           'desc' => 'Biaya pembimbing'],
            ['key' => 'insurance_cost',         'item' => 'Asuransi',        'desc' => 'Biaya asuransi perjalanan'],
            ['key' => 'operational_overhead',   'item' => 'Operasional',     'desc' => 'Biaya operasional'],
            ['key' => 'contingency',            'item' => 'Kontingensi',     'desc' => 'Dana cadangan'],
        ];

        foreach ($items as $def) {
            $unitPrice = (float) ($hpp->{$def['key']} ?? 0);
            if ($unitPrice <= 0) continue;

            $total = $unitPrice * $jamaahCount;
            // Default status: hutang (semua komponen default hutang)
            $status = $payStatus[$def['key']] ?? 'hutang';
            // Realisasi: LUNAS = 100% (= budget), HUTANG = 0
            $realisasi = ($status === 'lunas') ? $total : 0;
            // Hutang amount (jika ada override)
            $hutang = ($status === 'hutang') ? $total : 0;

            $components[] = [
                'item'          => $def['item'],
                'deskripsi'     => $def['desc'] . ' untuk ' . $jamaahCount . ' jamaah',
                'qty'           => $jamaahCount,
                'satuan'        => 'pax',
                'harga_satuan'  => $unitPrice,
                'biaya'         => $total,
                'payment_status'=> $status,
                'hutang_amount' => $hutang,
                'realisasi'     => $realisasi,
            ];
        }

        // Add custom components as individual RAB items
        $customComponents = $hpp->custom_components ?? [];
        foreach ($customComponents as $custom) {
            $unitPrice = (float) ($custom['value'] ?? 0);
            if ($unitPrice <= 0) continue;

            $total = $unitPrice * $jamaahCount;
            $label = $custom['label'] ?? 'Biaya Lainnya';
            
            $components[] = [
                'item'          => $label,
                'deskripsi'     => $label . ' untuk ' . $jamaahCount . ' jamaah',
                'qty'           => $jamaahCount,
                'satuan'        => 'pax',
                'harga_satuan'  => $unitPrice,
                'biaya'         => $total,
                'payment_status'=> 'hutang', // Custom components always hutang
                'hutang_amount' => $total,
                'realisasi'     => 0, // Hutang = realisasi 0
            ];
        }

        return $components;
    }
    
    /**
     * Get default accounting book ID for outlet
     * 
     * @param int $outletId
     * @return int|null
     */
    private function getDefaultBookId($outletId)
    {
        // Get the first accounting book for the outlet
        $book = \App\Models\AccountingBook::where('outlet_id', $outletId)
            ->first();
        
        // Return null if no book found (RAB can work without book_id)
        return $book ? $book->id : null;
    }

    /**
     * Sync HPP payment status changes to existing RAB
     * Dipanggil saat HPP diupdate setelah RAB sudah dibuat
     */
    public function syncHppStatusToRab(Keberangkatan $keberangkatan): void
    {
        if (!$keberangkatan->id_rab) return;

        $keberangkatan->load('travelPackage.hppCalculation');
        $hpp = $keberangkatan->travelPackage?->hppCalculation;
        if (!$hpp) return;

        $rab = RabTemplate::with('details')->find($keberangkatan->id_rab);
        if (!$rab) return;

        $payStatus = $hpp->component_payment_status ?? [];
        $hutangAmt = $hpp->component_hutang_amount ?? [];

        $itemKeyMap = [
            'Tiket Pesawat' => 'flight_cost',
            'Hotel'         => 'hotel_cost',
            'Transportasi'  => 'transportation_cost',
            'Makan'         => 'meal_cost',
            'Visa'          => 'visa_cost',
            'Guide'         => 'guide_cost',
            'Asuransi'      => 'insurance_cost',
            'Operasional'   => 'operational_overhead',
            'Kontingensi'   => 'contingency',
        ];

        foreach ($rab->details as $detail) {
            $key = $itemKeyMap[$detail->item] ?? null;
            if (!$key) continue;

            $status = $payStatus[$key] ?? 'lunas';
            $budget = (float) $detail->budget;
            $realisasi = ($status === 'lunas') ? $budget : 0;
            $hutang = (float) ($hutangAmt[$key] ?? 0);

            $detail->update([
                'realisasi_pemakaian' => $realisasi,
                'payment_status'      => $status,
                'hutang_amount'       => $hutang,
            ]);
        }
    }
    
    /**
     * Get RAB for keberangkatan
     * 
     * @param Keberangkatan $keberangkatan
     * @return RabTemplate|null
     */
    public function getRabForKeberangkatan(Keberangkatan $keberangkatan)
    {
        if (!$keberangkatan->id_rab) {
            return null;
        }
        
        return RabTemplate::with('details')->find($keberangkatan->id_rab);
    }
    
    /**
     * Calculate budget variance for keberangkatan
     * 
     * @param Keberangkatan $keberangkatan
     * @return array
     */
    public function calculateBudgetVariance(Keberangkatan $keberangkatan)
    {
        $rab = $this->getRabForKeberangkatan($keberangkatan);
        
        if (!$rab) {
            return [
                'has_rab' => false,
                'total_budget' => 0,
                'total_actual' => 0,
                'variance' => 0,
                'variance_percentage' => 0,
                'items' => []
            ];
        }
        
        $items = [];
        $totalBudget = 0;
        $totalActual = 0;
        
        foreach ($rab->details as $detail) {
            $budget = $detail->budget ?? 0;
            $actual = $detail->realisasi_pemakaian ?? 0;
            $variance = $actual - $budget;
            $variancePercentage = $budget > 0 ? ($variance / $budget) * 100 : 0;
            
            $totalBudget += $budget;
            $totalActual += $actual;
            
            $items[] = [
                'item' => $detail->nama_komponen,
                'budget' => $budget,
                'actual' => $actual,
                'variance' => $variance,
                'variance_percentage' => $variancePercentage,
                'has_warning' => $variancePercentage > 10
            ];
        }
        
        $totalVariance = $totalActual - $totalBudget;
        $totalVariancePercentage = $totalBudget > 0 ? ($totalVariance / $totalBudget) * 100 : 0;
        
        return [
            'has_rab' => true,
            'total_budget' => $totalBudget,
            'total_actual' => $totalActual,
            'variance' => $totalVariance,
            'variance_percentage' => $totalVariancePercentage,
            'has_warning' => $totalVariancePercentage > 10,
            'items' => $items
        ];
    }
}
