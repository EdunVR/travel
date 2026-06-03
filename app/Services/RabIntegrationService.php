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
            $result     = $this->generateRabComponents($keberangkatan);
            $components = $result['components'];
            $hotelWarning = $result['hotel_warning'] ?? null;
            
            // Log hotel warning if present
            if ($hotelWarning) {
                Log::warning('RAB generation hotel warning', [
                    'keberangkatan_id' => $keberangkatan->id,
                    'warning' => $hotelWarning,
                ]);
            }

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
                    'nilai_disetujui'     => $component['biaya'], // budget yang disetujui
                    'realisasi_pemakaian' => 0, // Selalu mulai dari 0, diinput manual oleh admin
                    'disetujui'           => true,
                    'payment_status'      => $component['payment_status'] ?? 'hutang',
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
            
            return ['rab' => $rab, 'hotel_warning' => $hotelWarning];
            
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
        
        // Use actual jamaah count from bookings INCLUDING family members
        $jamaahCount = 0;
        $bookings = $keberangkatan->jamaahBookings()
            ->whereNotIn('status', ['cancelled'])
            ->with(['jamaah', 'addons', 'hotelBookings'])
            ->get();
        foreach ($bookings as $booking) {
            $jamaahCount++; // main jamaah
            $fm = $booking->family_members_booking;
            if (is_string($fm)) $fm = json_decode($fm, true);
            if (is_array($fm)) $jamaahCount += count($fm);
        }
        
        if ($jamaahCount <= 0) {
            throw new \Exception('Belum ada jamaah terdaftar di keberangkatan ini. Tidak bisa membuat RAB.');
        }

        $payStatus = $hpp->component_payment_status ?? [];
        $hutangAmt = $hpp->component_hutang_amount ?? [];
        $customComponents = $hpp->custom_components ?? [];

        $components = [];

        // ── HPP DASAR components (× jamaahCount) ──────────────────────────────
        $items = [
            ['key' => 'flight_cost',           'item' => 'Tiket Pesawat',   'desc' => 'Biaya tiket pesawat'],
            ['key' => 'hotel_cost',             'item' => 'Hotel',           'desc' => 'Biaya akomodasi hotel'],
            ['key' => 'transportation_cost',    'item' => 'Transportasi',    'desc' => 'Biaya transportasi'],
            ['key' => 'meal_cost',              'item' => 'Makan',           'desc' => 'Biaya konsumsi'],
            ['key' => 'visa_cost',              'item' => 'Visa',            'desc' => 'Biaya pengurusan visa'],
            ['key' => 'guide_cost',             'item' => 'Guide',           'desc' => 'Biaya pembimbing'],
            ['key' => 'insurance_cost',         'item' => 'Asuransi',        'desc' => 'Biaya asuransi perjalanan'],
            ['key' => 'contingency',            'item' => 'Kontingensi',     'desc' => 'Dana cadangan'],
        ];

        foreach ($items as $def) {
            $unitPrice = (float) ($hpp->{$def['key']} ?? 0);
            if ($unitPrice <= 0) continue;

            $total = $unitPrice * $jamaahCount;
            $status = $payStatus[$def['key']] ?? 'hutang';
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
                'realisasi'     => 0,
            ];
        }

        // Add custom HPP components — gunakan qty dari HPP, TIDAK dikali jamaahCount
        // Qty di HPP sudah diset secara eksplisit oleh admin di modal Kelola HPP Dasar
        foreach ($customComponents as $custom) {
            $rawValue  = (float) ($custom['value'] ?? 0);
            $idrValue  = (float) ($custom['idr_value'] ?? 0);
            $qty       = max(1, (int) ($custom['qty'] ?? 1));
            $satuan    = $custom['satuan'] ?? 'unit';
            $currency  = $custom['currency'] ?? 'IDR';

            // Unit price dalam IDR
            if ($idrValue > 0) {
                $unitPriceIDR = $idrValue / $qty; // idr_value = total (value × qty × kurs)
            } elseif ($currency === 'IDR') {
                $unitPriceIDR = $rawValue;
            } else {
                $unitPriceIDR = $rawValue;
            }

            if ($unitPriceIDR <= 0) continue;

            // Total = harga_satuan × qty dari HPP (bukan jamaahCount)
            $total  = $unitPriceIDR * $qty;
            $label  = !empty($custom['label']) ? $custom['label'] : ($custom['id'] ?? 'Biaya Lainnya');
            $status = $custom['payment_status'] ?? 'hutang';
            $hutang = ($status === 'hutang') ? $total : 0;

            $desc = $label . ' (' . $qty . ' ' . $satuan . ')';
            if ($currency !== 'IDR') {
                $desc .= ' — ' . $rawValue . ' ' . $currency . '/unit → IDR';
            }

            $components[] = [
                'item'          => $label,
                'deskripsi'     => $desc,
                'qty'           => $qty,
                'satuan'        => $satuan,
                'harga_satuan'  => $unitPriceIDR,
                'biaya'         => $total,
                'payment_status'=> $status,
                'hutang_amount' => $hutang,
                'realisasi'     => 0, // Realisasi selalu 0 saat generate, input manual
            ];
        }

        // ── ADD-ONS dari booking jamaah (NOT × pax — per keluarga/booking) ────
        // Akumulasikan add-ons yang sama (berdasarkan nama), beda nama = item baru
        $addonAccumulator = []; // ['nama' => ['total_harga' => X, 'total_qty' => Y]]

        foreach ($bookings as $booking) {
            foreach ($booking->addons as $addon) {
                $nama  = trim($addon->nama ?? 'Add-on');
                $harga = (float) ($addon->harga ?? 0);
                $qty   = (int)   ($addon->qty   ?? 1);
                if ($harga <= 0) continue;

                if (!isset($addonAccumulator[$nama])) {
                    $addonAccumulator[$nama] = ['total_harga' => 0, 'total_qty' => 0, 'unit_price' => $harga];
                }
                $addonAccumulator[$nama]['total_qty']   += $qty;
                $addonAccumulator[$nama]['total_harga'] += $harga * $qty;
            }
        }

        foreach ($addonAccumulator as $nama => $acc) {
            if ($acc['total_harga'] <= 0) continue;
            $components[] = [
                'item'          => 'Add-on: ' . $nama,
                'deskripsi'     => 'Add-on ' . $nama . ' dari ' . count($bookings) . ' booking',
                'qty'           => $acc['total_qty'],
                'satuan'        => 'unit',
                'harga_satuan'  => $acc['unit_price'],
                'biaya'         => $acc['total_harga'],
                'payment_status'=> 'hutang',
                'hutang_amount' => $acc['total_harga'],
                'realisasi'     => 0,
            ];
        }

        // ── HOTEL BOOKINGS dari setiap jamaah (akumulasi per city_type) ─────
        // Jika ada hotel_booking.total_cost > 0, masukkan ke RAB
        // Tidak dikali pax — sudah dihitung per booking
        $hotelAccumulator = []; // ['label' => total_cost]

        foreach ($bookings as $booking) {
            if (!$booking->hotelBookings) continue;
            foreach ($booking->hotelBookings as $hb) {
                $cost = (float) ($hb->total_cost ?? 0);
                if ($cost <= 0) continue;

                // Label berdasarkan city_type (Mekkah/Madinah/dll) atau fallback 'Hotel'
                $city  = ucfirst(strtolower(trim($hb->city_type ?? 'Hotel')));
                $label = 'Hotel ' . $city;

                if (!isset($hotelAccumulator[$label])) {
                    $hotelAccumulator[$label] = 0;
                }
                $hotelAccumulator[$label] += $cost;
            }
        }

        foreach ($hotelAccumulator as $label => $totalCost) {
            if ($totalCost <= 0) continue;
            $components[] = [
                'item'          => $label,
                'deskripsi'     => $label . ' dari ' . count($bookings) . ' booking jamaah',
                'qty'           => 1,
                'satuan'        => 'paket',
                'harga_satuan'  => $totalCost,
                'biaya'         => $totalCost,
                'payment_status'=> 'hutang',
                'hutang_amount' => $totalCost,
                'realisasi'     => 0,
            ];
        }

        // ── WARNING: hotel_cost di HPP = 0 tapi ada hotel_booking ──────────
        // Flag ini dikembalikan bersama components untuk ditampilkan ke user
        $hppHotelCost      = (float) ($hpp->hotel_cost ?? 0);
        $totalHotelBooking = array_sum($hotelAccumulator);
        $hotelWarning      = ($hppHotelCost <= 0 && $totalHotelBooking > 0)
            ? 'Biaya hotel di HPP Dasar paket masih Rp 0, namun terdapat tagihan hotel dari booking jamaah sebesar Rp ' . number_format($totalHotelBooking, 0, ',', '.') . '. Pertimbangkan untuk mengisi biaya hotel di modal Kelola HPP Paket.'
            : null;

        // Attach warning as metadata (accessible via array key, not as RAB component)
        return ['components' => $components, 'hotel_warning' => $hotelWarning];
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
        $customComponents = $hpp->custom_components ?? [];

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

        // Build custom component label-to-status map
        $customStatusMap = [];
        foreach ($customComponents as $cc) {
            $label = $cc['label'] ?? '';
            $customStatusMap[$label] = $cc['payment_status'] ?? 'hutang';
        }

        foreach ($rab->details as $detail) {
            $key = $itemKeyMap[$detail->item] ?? null;
            
            if ($key) {
                // Standard component - hanya update payment_status dan hutang_amount
                // realisasi_pemakaian TIDAK diubah di sini — itu input manual admin
                // Status "lunas" artinya sudah dibayarkan, bukan berarti realisasi = budget
                $status = $payStatus[$key] ?? 'hutang';
                $budget = (float) $detail->budget;
                $hutang = ($status === 'hutang') ? $budget : 0;

                $detail->update([
                    'payment_status' => $status,
                    'hutang_amount'  => $hutang,
                ]);
            } else {
                // Custom component - hanya update payment_status dan hutang_amount
                // realisasi_pemakaian TIDAK diubah — input manual admin
                $customStatus = $customStatusMap[$detail->item] ?? 'hutang';
                $budget = (float) $detail->budget;
                $hutang = ($customStatus === 'hutang') ? $budget : 0;

                $detail->update([
                    'payment_status' => $customStatus,
                    'hutang_amount'  => $hutang,
                ]);
            }
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
