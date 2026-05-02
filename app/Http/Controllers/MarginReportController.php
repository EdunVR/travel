<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;
use App\Traits\HasCompanySettings;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\InterOutletSaleItem;
use App\Models\Piutang;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class MarginReportController extends Controller
{
    use HasOutletFilter, HasCompanySettings;

    public function index()
    {
        $outlets = Outlet::where('is_active', true)->get();
        return view('admin.penjualan.margin.index', compact('outlets'));
    }

    public function getData(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $sortBy = $request->get('sort_by', 'newest'); // newest atau oldest

            $marginData = [];
            $posGeneratedPenjualanIds = PosSale::pluck('id_penjualan')->filter()->toArray();

            // Get Invoice details (exclude POS-generated) - optimized
            $invoiceDetails = PenjualanDetail::select([
                    'id_penjualan_detail', 'id_penjualan', 'id_produk',
                    'harga_jual', 'jumlah', 'subtotal', 'hpp'
                ])
                ->with([
                    'produk:id_produk,nama_produk',
                    'penjualan' => function($query) {
                        $query->select('id_penjualan', 'id_outlet', 'created_at')
                              ->with('outlet:id_outlet,nama_outlet');
                    }
                ])
                ->whereHas('penjualan', function($q) use ($outletId, $startDate, $endDate, $posGeneratedPenjualanIds) {
                    $q->whereNotIn('id_penjualan', $posGeneratedPenjualanIds);
                    if ($outletId) $q->where('id_outlet', $outletId);
                    if ($startDate && $endDate) {
                        $q->whereDate('created_at', '>=', $startDate)
                          ->whereDate('created_at', '<=', $endDate);
                    }
                })
                ->get();

            foreach ($invoiceDetails as $detail) {
                $hpp = floatval($detail->hpp ?? 0);
                $jumlah = floatval($detail->jumlah ?? 0);
                $subtotal = floatval($detail->subtotal ?? 0);
                $hargaJual = floatval($detail->harga_jual ?? 0);
                
                $profit = $subtotal - ($hpp * $jumlah);
                $marginPct = $subtotal > 0 ? ($profit / $subtotal) * 100 : 0;
                
                $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
                $paymentType = $piutang && $piutang->sisa_piutang > 0 ? 'BON' : 'Cash';

                $marginData[] = [
                    'id' => 'invoice_' . $detail->id_penjualan_detail,
                    'source' => 'invoice',
                    'tanggal' => $detail->penjualan->created_at,
                    'outlet' => $detail->penjualan->outlet->nama_outlet ?? '-',
                    'produk' => $detail->produk->nama_produk ?? '-',
                    'qty' => $jumlah,
                    'hpp' => $hpp,
                    'harga_jual' => $hargaJual,
                    'subtotal' => $subtotal,
                    'profit' => $profit,
                    'margin_pct' => round($marginPct, 2),
                    'payment_type' => $paymentType,
                ];
            }

            // Get POS items - optimized with FIFO HPP calculation
            $posItems = PosSaleItem::select([
                    'id', 'pos_sale_id', 'id_produk', 'nama_produk',
                    'kuantitas', 'harga', 'subtotal', 'tipe'
                ])
                ->with([
                    'produk:id_produk,nama_produk',
                    'posSale' => function($query) {
                        $query->select('id', 'id_outlet', 'tanggal', 'is_bon', 'jenis_pembayaran')
                              ->with('outlet:id_outlet,nama_outlet');
                    }
                ])
                ->where('tipe', 'produk')
                ->whereHas('posSale', function($q) use ($outletId, $startDate, $endDate) {
                    if ($outletId) $q->where('id_outlet', $outletId);
                    if ($startDate && $endDate) {
                        $q->whereDate('tanggal', '>=', $startDate)
                          ->whereDate('tanggal', '<=', $endDate);
                    }
                })
                ->get();

            foreach ($posItems as $item) {
                // Gunakan metode FIFO untuk menghitung HPP per unit
                $totalHppFifo = $item->produk ? $this->calculateHppFifo($item->id_produk, $item->kuantitas) : 0;
                $kuantitas = floatval($item->kuantitas ?? 0);
                
                // HPP per unit = Total HPP FIFO / Quantity
                $hpp = $kuantitas > 0 ? $totalHppFifo / $kuantitas : 0;
                
                $subtotal = floatval($item->subtotal ?? 0);
                $harga = floatval($item->harga ?? 0);
                
                $profit = $subtotal - ($hpp * $kuantitas);
                $marginPct = $subtotal > 0 ? ($profit / $subtotal) * 100 : 0;
                
                $paymentType = $item->posSale->is_bon ? 'BON' : ucfirst($item->posSale->jenis_pembayaran);

                $marginData[] = [
                    'id' => 'pos_' . $item->id,
                    'source' => 'pos',
                    'tanggal' => $item->posSale->tanggal,
                    'outlet' => $item->posSale->outlet->nama_outlet ?? '-',
                    'produk' => $item->nama_produk,
                    'qty' => $kuantitas,
                    'hpp' => $hpp,
                    'harga_jual' => $harga,
                    'subtotal' => $subtotal,
                    'profit' => $profit,
                    'margin_pct' => round($marginPct, 2),
                    'payment_type' => $paymentType,
                ];
            }

            // Get Inter-Outlet Sale items - optimized with FIFO HPP calculation
            $interOutletItems = \App\Models\InterOutletSaleItem::select([
                    'id', 'inter_outlet_sale_id', 'id_produk', 'kuantitas', 'harga', 'subtotal', 'data_hpp'
                ])
                ->with([
                    'produk:id_produk,nama_produk',
                    'interOutletSale' => function($query) {
                        $query->select('id', 'outlet_asal', 'outlet_tujuan', 'tanggal', 'status')
                              ->with([
                                  'outletAsal:id_outlet,nama_outlet',
                                  'outletTujuan:id_outlet,nama_outlet'
                              ]);
                    }
                ])
                ->whereHas('interOutletSale', function($q) use ($outletId, $startDate, $endDate) {
                    // Filter by outlet (bisa outlet asal atau tujuan)
                    if ($outletId) {
                        $q->where(function($query) use ($outletId) {
                            $query->where('outlet_asal', $outletId)
                                  ->orWhere('outlet_tujuan', $outletId);
                        });
                    }
                    if ($startDate && $endDate) {
                        $q->whereDate('tanggal', '>=', $startDate)
                          ->whereDate('tanggal', '<=', $endDate);
                    }
                    // Hanya yang sudah approved
                    $q->where('status', 'approved');
                })
                ->get();

            foreach ($interOutletItems as $item) {
                $kuantitas = floatval($item->kuantitas ?? 0);
                $subtotal = floatval($item->subtotal ?? 0);
                $harga = floatval($item->harga ?? 0);
                
                // Gunakan data HPP yang tersimpan saat transaksi
                $hppResult = $this->calculateHppFromStoredData($item);
                
                $hpp = $hppResult['hpp_per_unit'];
                $profit = $hppResult['can_calculate'] ? $subtotal - ($hpp * $kuantitas) : null;
                $marginPct = $hppResult['can_calculate'] && $subtotal > 0 ? ($profit / $subtotal) * 100 : null;
                
                // Tentukan outlet yang relevan (asal untuk penjualan)
                $outletName = $item->interOutletSale->outletAsal->nama_outlet ?? '-';
                $outletTujuan = $item->interOutletSale->outletTujuan->nama_outlet ?? '-';

                $marginData[] = [
                    'id' => 'inter_outlet_' . $item->id,
                    'source' => 'inter_outlet',
                    'tanggal' => $item->interOutletSale->tanggal,
                    'outlet' => $outletName . ' → ' . $outletTujuan,
                    'produk' => $item->produk->nama_produk ?? '-',
                    'qty' => $kuantitas,
                    'hpp' => $hpp,
                    'harga_jual' => $harga,
                    'subtotal' => $subtotal,
                    'profit' => $profit,
                    'margin_pct' => $marginPct ? round($marginPct, 2) : null,
                    'payment_type' => 'Transfer',
                    'hpp_status' => $hppResult['status'],
                    'hpp_message' => $hppResult['message'],
                ];
            }

            usort($marginData, function($a, $b) use ($sortBy) {
                if ($sortBy === 'oldest') {
                    return strtotime($a['tanggal']) - strtotime($b['tanggal']);
                } else {
                    return strtotime($b['tanggal']) - strtotime($a['tanggal']); // default newest
                }
            });

            return response()->json([
                'success' => true,
                'data' => $marginData
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading margin report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            // Gunakan getData dengan filter yang sama untuk memastikan konsistensi
            $response = $this->getData($request);
            $responseData = json_decode($response->getContent(), true);
            
            if (!$responseData['success']) {
                throw new \Exception('Failed to load data');
            }

            $marginData = $responseData['data'];

            // Hitung summary dari data yang sudah difilter
            $summary = [
                'total_items' => count($marginData),
                'total_hpp' => collect($marginData)->sum(fn($i) => ($i['hpp'] ?? 0) * ($i['qty'] ?? 0)),
                'total_penjualan' => collect($marginData)->sum('subtotal'),
                'total_profit' => collect($marginData)->where('profit', '!=', null)->sum('profit'),
                'avg_margin' => collect($marginData)->where('margin_pct', '!=', null)->avg('margin_pct'),
                'total_invoice' => collect($marginData)->where('source', 'invoice')->count(),
                'total_pos' => collect($marginData)->where('source', 'pos')->count(),
                'total_inter_outlet' => collect($marginData)->where('source', 'inter_outlet')->count(),
            ];

            // Get outlet name
            $outletId = $request->get('outlet_id');
            $outletName = 'Semua Outlet';
            if ($outletId) {
                $outlet = Outlet::find($outletId);
                $outletName = $outlet ? $outlet->nama_outlet : 'Semua Outlet';
                
                // Set outlet untuk company settings
                session(['selected_outlet_id' => $outletId]);
            }

            // Get company settings untuk kop surat menggunakan trait
            $companySettings = $this->getCompanySettingsForPrint();
            
            // Try to convert logo to base64 for PDF compatibility
            if (isset($companySettings['logo_url']) && $companySettings['logo_url']) {
                try {
                    // Get the actual file path from URL
                    $logoUrl = $companySettings['logo_url'];
                    
                    // Extract file path from URL
                    if (str_contains($logoUrl, '/storage/')) {
                        $relativePath = substr($logoUrl, strpos($logoUrl, '/storage/') + 9); // Remove '/storage/'
                        $fullPath = storage_path('app/public/' . $relativePath);
                        
                        if (file_exists($fullPath)) {
                            $logoContent = file_get_contents($fullPath);
                            $logoBase64 = 'data:' . mime_content_type($fullPath) . ';base64,' . base64_encode($logoContent);
                            $companySettings['logo_base64'] = $logoBase64;
                            
                            Log::info('Logo converted to base64', [
                                'original_url' => $logoUrl,
                                'file_path' => $fullPath,
                                'file_exists' => true,
                                'file_size' => strlen($logoContent),
                                'base64_length' => strlen($logoBase64)
                            ]);
                        } else {
                            Log::warning('Logo file not found', [
                                'logo_url' => $logoUrl,
                                'expected_path' => $fullPath
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error processing logo for PDF', [
                        'logo_url' => $companySettings['logo_url'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Debug: Log company settings
            Log::info('Margin Report Company Settings:', [
                'outlet_id' => $outletId,
                'session_outlet_id' => session('selected_outlet_id'),
                'company_settings_keys' => array_keys($companySettings),
                'logo_url' => $companySettings['logo_url'] ?? 'NOT SET',
                'logo_base64_available' => isset($companySettings['logo_base64']) ? 'YES' : 'NO',
                'company_name' => $companySettings['company_name'] ?? 'NOT SET'
            ]);

            $data = [
                'marginData' => $marginData,
                'summary' => $summary,
                'outletName' => $outletName,
                'startDate' => $request->get('start_date'),
                'endDate' => $request->get('end_date'),
                'search' => $request->get('search'),
                'generatedAt' => now()->format('d/m/Y H:i'),
                'companySettings' => $companySettings,
                'filters' => [
                    'outlet_id' => $request->get('outlet_id'),
                    'start_date' => $request->get('start_date'),
                    'end_date' => $request->get('end_date'),
                    'search' => $request->get('search'),
                ]
            ];

            $pdf = Pdf::loadView('admin.penjualan.margin.pdf', $data)
                ->setPaper('a4', 'landscape');

            return $pdf->stream('Laporan-Margin-' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error exporting margin report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal export PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hitung HPP menggunakan metode FIFO (First In First Out)
     * Method ini hanya untuk perhitungan, tidak mengubah stok di database
     */
    private function calculateHppFifo($id_produk, $jumlah)
    {
        // Ambil semua HPP produk yang masih memiliki stok lebih dari 0
        $hppDetails = \App\Models\HppProduk::where('id_produk', $id_produk)
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO: yang pertama masuk, pertama keluar
            ->get();

        if ($hppDetails->isEmpty()) {
            return 0;
        }

        $totalHpp = 0;
        $remainingQty = $jumlah;

        foreach ($hppDetails as $hpp) {
            if ($remainingQty <= 0) {
                break;
            }

            // Hitung jumlah stok yang akan digunakan dari HPP ini
            $usedQty = min($hpp->stok, $remainingQty);
            
            // Tambahkan ke total HPP
            $totalHpp += $hpp->hpp * $usedQty;
            
            // Kurangi sisa quantity yang perlu dihitung
            $remainingQty -= $usedQty;
        }

        // Jika remainingQty masih lebih dari 0, berarti stok tidak mencukupi
        // Dalam konteks laporan, kita tetap return HPP yang bisa dihitung
        if ($remainingQty > 0) {
            Log::warning("Stok tidak mencukupi untuk perhitungan HPP FIFO", [
                'id_produk' => $id_produk,
                'jumlah_diminta' => $jumlah,
                'sisa_tidak_terhitung' => $remainingQty
            ]);
        }

        return $totalHpp;
    }

    /**
     * Hitung HPP dari data yang tersimpan saat transaksi
     */
    private function calculateHppFromStoredData($interOutletItem)
    {
        try {
            $kuantitas = floatval($interOutletItem->kuantitas ?? 0);
            
            // Jika tidak ada data HPP tersimpan, gunakan perhitungan FIFO saat ini (fallback)
            if (empty($interOutletItem->data_hpp) || $interOutletItem->data_hpp === null || $interOutletItem->data_hpp === [] || $interOutletItem->data_hpp === '') {
                Log::warning("Data HPP tidak tersimpan untuk inter-outlet item", [
                    'item_id' => $interOutletItem->id,
                    'produk_id' => $interOutletItem->id_produk,
                    'kuantitas' => $kuantitas
                ]);
                
                // Fallback ke perhitungan FIFO saat ini
                $totalHppFifo = $interOutletItem->produk ? $this->calculateHppFifo($interOutletItem->id_produk, $kuantitas) : 0;
                $hppPerUnit = $kuantitas > 0 ? $totalHppFifo / $kuantitas : 0;
                
                return [
                    'hpp_per_unit' => $hppPerUnit,
                    'can_calculate' => true,
                    'status' => 'fallback',
                    'message' => 'Menggunakan perhitungan FIFO saat ini (data HPP tidak tersimpan)'
                ];
            }
            
            // Hitung HPP dari data tersimpan
            $dataHpp = $interOutletItem->data_hpp;
            $totalHpp = 0;
            $totalQtyUsed = 0;
            
            foreach ($dataHpp as $hppData) {
                if (!isset($hppData['hpp']) || !isset($hppData['qty_used'])) {
                    continue;
                }
                
                $hpp = floatval($hppData['hpp']);
                $qtyUsed = floatval($hppData['qty_used']);
                
                $totalHpp += $hpp * $qtyUsed;
                $totalQtyUsed += $qtyUsed;
            }
            
            // Cek apakah semua quantity terpenuhi
            if ($totalQtyUsed < $kuantitas) {
                $sisaQty = $kuantitas - $totalQtyUsed;
                
                Log::warning("Stok HPP tidak mencukupi untuk inter-outlet item", [
                    'item_id' => $interOutletItem->id,
                    'produk_id' => $interOutletItem->id_produk,
                    'kuantitas_diminta' => $kuantitas,
                    'kuantitas_terpenuhi' => $totalQtyUsed,
                    'sisa_tidak_terpenuhi' => $sisaQty
                ]);
                
                return [
                    'hpp_per_unit' => 0,
                    'can_calculate' => false,
                    'status' => 'insufficient',
                    'message' => "Sisa Qty {$sisaQty} tidak terpenuhi dan perlu diperiksa HPP"
                ];
            }
            
            $hppPerUnit = $kuantitas > 0 ? $totalHpp / $kuantitas : 0;
            
            return [
                'hpp_per_unit' => $hppPerUnit,
                'can_calculate' => true,
                'status' => 'complete',
                'message' => 'HPP dihitung dari data tersimpan saat transaksi'
            ];
            
        } catch (\Exception $e) {
            Log::error("Error calculating HPP from stored data", [
                'item_id' => $interOutletItem->id ?? null,
                'error' => $e->getMessage()
            ]);
            
            return [
                'hpp_per_unit' => 0,
                'can_calculate' => false,
                'status' => 'error',
                'message' => 'Error dalam perhitungan HPP: ' . $e->getMessage()
            ];
        }
    }
}
