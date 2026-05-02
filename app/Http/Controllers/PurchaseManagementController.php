<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\Supplier;
use App\Models\Produk;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchasePayment;
use App\Models\POPaymentHistory;
use App\Models\Hutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use App\Exports\PurchaseOrderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\JournalEntryService;
use App\Models\Outlet;
use App\Models\PurchaseOrderCounter;
use App\Models\SettingCOAPurchase;
use App\Models\ChartOfAccount;
use App\Models\AccountingBook;
use App\Models\Bahan;
use App\Traits\HasCompanySettings;

class PurchaseManagementController extends Controller
{
    use \App\Traits\HasOutletFilter;
    use HasCompanySettings;
    protected $journalService;

    public function __construct(JournalEntryService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Main Index Page - Menampilkan semua fitur dalam satu halaman
     */
    public function index()
    {
        return view('admin.pembelian.purchase-order.index');
    }

    public function generatePOCode(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $poNumber = PurchaseOrderCounter::generatePONumber($outletId);
            return response()->json([
                'success' => true,
                'po_number' => $poNumber
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating PO code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate kode PO'
            ], 500);
        }
    }

    public function purchaseOrderData(Request $request)
    {
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search', '');
        $supplierFilter = $request->get('supplier_filter', 'all');
        $outletIds = $request->get('outlet_ids', []); // Support multiple outlets

        $query = PurchaseOrder::with(['supplier', 'user', 'items', 'invoices', 'outlet']);

        // Filter status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter multiple outlets
        if (!empty($outletIds) && is_array($outletIds)) {
            $query->whereIn('id_outlet', $outletIds);
        }

        // Filter tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Filter supplier
        if ($supplierFilter !== 'all') {
            $query->where('id_supplier', $supplierFilter);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_po', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function($q) use ($search) {
                      $q->where('deskripsi', 'like', "%{$search}%");
                  })
                  ->orWhereHas('outlet', function($q) use ($search) {
                      $q->where('nama_outlet', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortColumn = $request->get('sort_column', 'tanggal');
        $sortDirection = $request->get('sort_direction', 'desc');

        $columnMapping = [
            'no_po' => 'no_po',
            'tanggal' => 'tanggal',
            'supplier' => 'supplier.nama',
            'outlet' => 'outlet.nama_outlet',
            'total' => 'total',
            'status' => 'status',
            'due_date' => 'due_date'
        ];

        $sortColumn = $columnMapping[$sortColumn] ?? 'tanggal';
        
        if (str_contains($sortColumn, '.')) {
            $relations = explode('.', $sortColumn);
            if ($relations[0] === 'supplier') {
                $query->join('supplier', 'purchase_order.id_supplier', '=', 'supplier.id_supplier')
                      ->orderBy($sortColumn, $sortDirection)
                      ->select('purchase_order.*');
            } elseif ($relations[0] === 'outlet') {
                $query->join('outlets', 'purchase_order.id_outlet', '=', 'outlets.id_outlet')
                      ->orderBy($sortColumn, $sortDirection)
                      ->select('purchase_order.*');
            }
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_po_formatted', function ($row) {
                return '<span class="font-mono text-sm">' . $row->no_po . '</span>';
            })
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal ? $row->tanggal->format('d/m/Y') : '-';
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->supplier ? $row->supplier->nama : 'Supplier Tidak Ditemukan';
            })
            ->addColumn('outlet_name', function ($row) {
                return $row->outlet ? $row->outlet->nama_outlet : 'Outlet Tidak Ditemukan';
            })
            ->addColumn('subtotal_formatted', function ($row) {
                return 'Rp ' . number_format($row->subtotal, 0, ',', '.');
            })
            ->addColumn('total_diskon_formatted', function ($row) {
                return 'Rp ' . number_format($row->total_diskon, 0, ',', '.');
            })
            ->addColumn('total_formatted', function ($row) {
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->addColumn('has_payment_proof', function ($row) {
                // Cek apakah ada bukti bayar di payments
                $hasProof = false;
                foreach ($row->invoices as $invoice) {
                    foreach ($invoice->payments as $payment) {
                        if ($payment->bukti_bayar) {
                            $hasProof = true;
                            break 2;
                        }
                    }
                }
                return $hasProof;
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = [
                    'draft' => 'secondary',
                    'diproses' => 'warning',
                    'dikirim' => 'info',
                    'diterima' => 'success',
                    'dibatalkan' => 'danger',
                    'selesai' => 'primary'
                ][$row->status] ?? 'secondary';
                
                $statusText = [
                    'draft' => 'Draft',
                    'diproses' => 'Diproses',
                    'dikirim' => 'Dikirim',
                    'diterima' => 'Diterima',
                    'dibatalkan' => 'Dibatalkan',
                    'selesai' => 'Selesai'
                ][$row->status] ?? $row->status;

                return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-' . $badgeClass . '-100 text-' . $badgeClass . '-800">' . $statusText . '</span>';
            })
            ->addColumn('due_date_formatted', function ($row) {
                if (!$row->due_date) return '-';
                return $row->due_date->format('d/m/Y');
            })
            ->addColumn('sisa_hari', function ($row) {
                if (!in_array($row->status, ['draft', 'diproses', 'dikirim']) || !$row->due_date) {
                    return '<span class="text-gray-400">-</span>';
                }

                $now = now();
                $dueDate = $row->due_date;
                $diffDays = $now->diffInDays($dueDate, false);

                if ($diffDays < 0) {
                    return '<span class="text-red-600 font-medium">' . abs($diffDays) . ' hari lewat</span>';
                } elseif ($diffDays === 0) {
                    return '<span class="text-orange-600 font-medium">Jatuh tempo hari ini</span>';
                } else {
                    return '<span class="text-blue-600">Sisa ' . $diffDays . ' hari</span>';
                }
            })
            ->addColumn('items_list', function ($row) {
                $items = $row->items->take(2);
                $list = '';
                foreach ($items as $item) {
                    $list .= '• ' . $item->deskripsi . ' (' . $item->kuantitas . ' ' . $item->satuan . ')<br>';
                }
                if ($row->items->count() > 2) {
                    $list .= '... dan ' . ($row->items->count() - 2) . ' item lainnya';
                }
                return $list;
            })
            ->addColumn('invoice_count', function ($row) {
                $count = $row->invoices->count();
                return '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">' . $count . ' Invoice</span>';
            })
            // Di method purchaseOrderData, ganti dengan:
            ->addColumn('actions', function ($row) {
                $actions = '<div class="flex gap-1">';
                
                // Print button
                $actions .= '<a href="' . route('pembelian.purchase-order.print', $row->id_purchase_order) . '" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-100 text-green-700 hover:bg-green-200">
                    <i class="bx bx-printer text-xs"></i> Print
                </a>';
                
                // Edit button - hanya untuk status awal
                if (in_array($row->status, ['permintaan_pembelian', 'request_quotation', 'purchase_order'])) {
                    $actions .= '<button onclick="editPurchaseOrder(' . $row->id_purchase_order . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                        <i class="bx bx-edit text-xs"></i> Edit
                    </button>';
                }
                
                // Status progression actions
                switch($row->status) {
                    case 'permintaan_pembelian':
                        $actions .= '<button onclick="updatePOStatus(' . $row->id_purchase_order . ', \'request_quotation\')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-purple-100 text-purple-700 hover:bg-purple-200">
                            <i class="bx bx-send text-xs"></i> Buat Quotation
                        </button>';
                        break;
                        
                    case 'request_quotation':
                        $actions .= '<button onclick="updatePOStatus(' . $row->id_purchase_order . ', \'purchase_order\')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-amber-100 text-amber-700 hover:bg-amber-200">
                            <i class="bx bx-cart-alt text-xs"></i> Buat PO
                        </button>';
                        break;
                        
                    case 'purchase_order':
                        $actions .= '<button onclick="updatePOStatus(' . $row->id_purchase_order . ', \'penerimaan_barang\')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-cyan-100 text-cyan-700 hover:bg-cyan-200">
                            <i class="bx bx-package text-xs"></i> Terima Barang
                        </button>';
                        break;
                        
                    case 'penerimaan_barang':
                        $actions .= '<button onclick="updatePOStatus(' . $row->id_purchase_order . ', \'vendor_bill\')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-orange-100 text-orange-700 hover:bg-orange-200">
                            <i class="bx bx-receipt text-xs"></i> Buat Vendor Bill
                        </button>';
                        break;
                        
                    case 'vendor_bill':
                        $actions .= '<button onclick="updatePOStatus(' . $row->id_purchase_order . ', \'payment\')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-100 text-green-700 hover:bg-green-200">
                            <i class="bx bx-credit-card text-xs"></i> Bayar
                        </button>';
                        break;
                }
                
                // Invoice button - untuk Vendor Bill dan Payment
                if (in_array($row->status, ['vendor_bill', 'payment'])) {
                    $actions .= '<button onclick="manageInvoices(' . $row->id_purchase_order . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-purple-100 text-purple-700 hover:bg-purple-200">
                        <i class="bx bx-receipt text-xs"></i> Invoice
                    </button>';
                }
                
                // Cancel button - untuk semua status kecuali payment dan dibatalkan
                if (!in_array($row->status, ['payment', 'dibatalkan'])) {
                    $actions .= '<button onclick="updatePOStatus(' . $row->id_purchase_order . ', \'dibatalkan\')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-red-100 text-red-700 hover:bg-red-200">
                        <i class="bx bx-x text-xs"></i> Batal
                    </button>';
                }
                
                // Delete button - hanya untuk permintaan_pembelian tanpa invoice
                if ($row->status === 'permintaan_pembelian' && $row->invoices->count() === 0) {
                    $actions .= '<button onclick="deletePurchaseOrder(' . $row->id_purchase_order . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs border border-red-200 text-red-700 hover:bg-red-50">
                        <i class="bx bx-trash text-xs"></i> Hapus
                    </button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['no_po_formatted', 'status_badge', 'sisa_hari', 'items_list', 'invoice_count', 'actions', 'outlet_name'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $parseNumber = function($value) {
            if (is_numeric($value)) {
                return (float) $value;
            }
            if (is_string($value)) {
                return (float) str_replace(['.', ','], '', $value);
            }
            return (float) $value;
        };

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'items' => 'required|array|min:1',
            'items.*.deskripsi' => 'required|string|max:255',
            'items.*.keterangan' => 'nullable|string|max:255',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.tipe_item' => 'required|in:produk,bahan,manual',
            'items.*.id_produk' => 'nullable|required_if:items.*.tipe_item,produk|exists:produk,id_produk',
            'items.*.id_bahan' => 'nullable|required_if:items.*.tipe_item,bahan|exists:bahan,id_bahan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $parseNumber) {
                // Validasi Outlet terlebih dahulu
                if (!$request->id_outlet) {
                    throw new \Exception("Outlet ID tidak boleh kosong");
                }

                // Generate DRAFT number yang UNIQUE
                $poNumber = $this->generateDraftNumber();
                
                // Hitung totals
                $subtotal = 0;
                $totalDiskon = 0;
                
                foreach ($request->items as $item) {
                    $itemSubtotal = $parseNumber($item['subtotal']);
                    $itemDiskon = $parseNumber($item['diskon']) * $parseNumber($item['kuantitas']);
                    
                    $subtotal += $itemSubtotal;
                    $totalDiskon += $itemDiskon;
                }
                
                $total = $subtotal - $totalDiskon;

                $tanggalWithTime = \Carbon\Carbon::parse($request->tanggal . ' ' . now()->format('H:i:s'));
                $dueDate = \Carbon\Carbon::parse($tanggalWithTime)->addDays(30);

                // Validasi Supplier
                $supplier = \App\Models\Supplier::find($request->id_supplier);
                if (!$supplier) {
                    throw new \Exception("Supplier dengan ID {$request->id_supplier} tidak ditemukan");
                }

                // Validasi Outlet
                $outlet = Outlet::find($request->id_outlet);
                if (!$outlet) {
                    throw new \Exception("Outlet dengan ID {$request->id_outlet} tidak ditemukan");
                }

                // Create Purchase Order dengan status permintaan_pembelian
                $poData = [
                    'no_po' => $poNumber,
                    'tanggal' => $tanggalWithTime,
                    'id_supplier' => $request->id_supplier,
                    'id_outlet' => $request->id_outlet,
                    'id_user' => auth()->user()->id ?? null,
                    'subtotal' => $subtotal,
                    'total_diskon' => $totalDiskon,
                    'total' => $total,
                    'status' => 'permintaan_pembelian', // Default status
                    'due_date' => $dueDate,
                    'keterangan' => $request->keterangan,
                    'metode_pengiriman' => $request->metode_pengiriman,
                    'alamat_pengiriman' => $request->alamat_pengiriman
                ];
                
                $purchaseOrder = PurchaseOrder::create($poData);

                // Create Purchase Order Items
                foreach ($request->items as $index => $item) {
                    $id_produk = ($item['tipe_item'] === 'produk' && !empty($item['id_produk'])) ? $item['id_produk'] : null;
                    $id_bahan = ($item['tipe_item'] === 'bahan' && !empty($item['id_bahan'])) ? $item['id_bahan'] : null;
                    
                    $kuantitas = $item['kuantitas'];
                    $harga = $parseNumber($item['harga']);
                    $diskon = $parseNumber($item['diskon']);
                    $subtotal = $parseNumber($item['subtotal']);

                    $poItemData = [
                        'id_purchase_order' => $purchaseOrder->id_purchase_order,
                        'tipe_item' => $item['tipe_item'],
                        'id_produk' => $id_produk,
                        'id_bahan' => $id_bahan,
                        'deskripsi' => $item['deskripsi'],
                        'keterangan' => $item['keterangan'] ?? null,
                        'kuantitas' => $item['kuantitas'],
                        'satuan' => $item['satuan'] ?? null,
                        'harga' => $harga,
                        'diskon' => $diskon,
                        'subtotal' => $subtotal,
                    ];
                    
                    PurchaseOrderItem::create($poItemData);
                }

                \Log::info('Permintaan pembelian created successfully', [
                    'po_id' => $purchaseOrder->id_purchase_order,
                    'po_number' => $poNumber,
                    'supplier' => $supplier->nama_supplier,
                    'outlet' => $outlet->nama_outlet,
                    'total' => $total,
                    'status' => 'permintaan_pembelian'
                ]);
            });

            \Log::info('Permintaan pembelian store transaction committed successfully');

            return response()->json([
                'success' => true, 
                'message' => 'Permintaan pembelian berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Permintaan pembelian store error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat permintaan pembelian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique draft number
     */
    private function generateDraftNumber()
    {
        $maxAttempts = 10;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            $datePart = date('Ymd');
            $sequence = PurchaseOrder::where('no_po', 'like', "DRAFT/{$datePart}/%")
                ->count() + 1;
                
            $draftNumber = "DRAFT/{$datePart}/" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            
            // Check if this number already exists
            $exists = PurchaseOrder::where('no_po', $draftNumber)->exists();
            
            if (!$exists) {
                return $draftNumber;
            }
            
            $attempt++;
        }
        
        // Fallback dengan timestamp jika masih gagal
        return "DRAFT/" . date('Ymd') . "/" . time();
    }

    public function show($id)
    {
        try {
            $purchaseOrder = PurchaseOrder::with([
                'supplier', 
                'outlet',
                'items' => function($query) {
                    $query->with([
                        'produk' => function($q) {
                            $q->with('satuan');
                        }, 
                        'bahan' => function($q) {
                            $q->with('satuan');
                        }
                    ]);
                },
                'invoices' => function($query) {
                    $query->with(['items', 'payments']);
                }
            ])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $purchaseOrder
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing purchase order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Purchase order tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update Purchase Order
     */
    public function update(Request $request, $id)
    {
        $parseNumber = function($value) {
            if (is_numeric($value)) {
                return (float) $value;
            }
            if (is_string($value)) {
                return (float) str_replace(['.', ','], '', $value);
            }
            return (float) $value;
        };

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'items' => 'required|array|min:1',
            'items.*.deskripsi' => 'required|string|max:255',
            'items.*.keterangan' => 'nullable|string|max:255',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.diskon' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.tipe_item' => 'required|in:produk,bahan,manual',
            'items.*.id_produk' => 'nullable|required_if:items.*.tipe_item,produk|exists:produk,id_produk',
            'items.*.id_bahan' => 'nullable|required_if:items.*.tipe_item,bahan|exists:bahan,id_bahan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $parseNumber, $id) {
                \Log::info('Starting purchase order update transaction', ['po_id' => $id, 'request_data' => $request->all()]);

                $purchaseOrder = PurchaseOrder::findOrFail($id);
                
                // Bisa edit PO dengan status permintaan_pembelian dan request_quotation
                $editableStatuses = ['permintaan_pembelian', 'request_quotation'];
                if (!in_array($purchaseOrder->status, $editableStatuses)) {
                    throw new \Exception("Hanya purchase order dengan status permintaan pembelian atau request quotation yang dapat diedit");
                }
                
                // Hitung totals
                $subtotal = 0;
                $totalDiskon = 0;
                
                foreach ($request->items as $item) {
                    $itemSubtotal = $parseNumber($item['subtotal']);
                    $itemDiskon = $parseNumber($item['diskon']) * $parseNumber($item['kuantitas']);
                    
                    $subtotal += $itemSubtotal;
                    $totalDiskon += $itemDiskon;
                }
                
                $total = $subtotal - $totalDiskon;

                // Update Purchase Order
                $poData = [
                    'tanggal' => \Carbon\Carbon::parse($request->tanggal . ' ' . now()->format('H:i:s')),
                    'id_supplier' => $request->id_supplier,
                    'id_outlet' => $request->id_outlet,
                    'subtotal' => $subtotal,
                    'total_diskon' => $totalDiskon,
                    'total' => $total,
                    'keterangan' => $request->keterangan,
                    'metode_pengiriman' => $request->metode_pengiriman,
                    'alamat_pengiriman' => $request->alamat_pengiriman
                ];
                
                $purchaseOrder->update($poData);

                // Hapus items lama dan buat yang baru
                PurchaseOrderItem::where('id_purchase_order', $id)->delete();

                // Create Purchase Order Items baru
                foreach ($request->items as $index => $item) {
                    $id_produk = ($item['tipe_item'] === 'produk' && !empty($item['id_produk'])) ? $item['id_produk'] : null;
                    $id_bahan = ($item['tipe_item'] === 'bahan' && !empty($item['id_bahan'])) ? $item['id_bahan'] : null;
                    
                    $kuantitas = $item['kuantitas'];
                    $harga = $parseNumber($item['harga']);
                    $diskon = $parseNumber($item['diskon']);
                    $subtotal = $parseNumber($item['subtotal']);

                    $poItemData = [
                        'id_purchase_order' => $purchaseOrder->id_purchase_order,
                        'tipe_item' => $item['tipe_item'],
                        'id_produk' => $id_produk,
                        'id_bahan' => $id_bahan,
                        'deskripsi' => $item['deskripsi'],
                        'keterangan' => $item['keterangan'] ?? null,
                        'kuantitas' => $item['kuantitas'],
                        'satuan' => $item['satuan'] ?? null,
                        'harga' => $harga,
                        'diskon' => $diskon,
                        'subtotal' => $subtotal,
                    ];
                    
                    PurchaseOrderItem::create($poItemData);
                }

                \Log::info('Purchase order update completed successfully', [
                    'po_id' => $purchaseOrder->id_purchase_order,
                    'subtotal' => $subtotal,
                    'total_diskon' => $totalDiskon,
                    'total' => $total
                ]);
            });

            return response()->json([
                'success' => true, 
                'message' => 'Permintaan pembelian berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            \Log::error('Purchase order update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate permintaan pembelian: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:permintaan_pembelian,request_quotation,purchase_order,penerimaan_barang,vendor_bill,partial,payment,dibatalkan',
            'catatan' => 'nullable|string',
            // Field tambahan untuk setiap status
            'tanggal_quotation' => 'nullable|date|required_if:status,request_quotation',
            'no_quotation' => 'nullable|string|required_if:status,request_quotation',
            'tanggal_penerimaan' => 'nullable|date|required_if:status,penerimaan_barang',
            'penerima_barang' => 'nullable|string|required_if:status,penerimaan_barang',
            'tanggal_vendor_bill' => 'nullable|date|required_if:status,vendor_bill',
            'no_vendor_bill' => 'nullable|string|required_if:status,vendor_bill', // Ini akan menjadi no_invoice
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $purchaseOrder = PurchaseOrder::with(['items', 'invoices'])->findOrFail($id);
                $oldStatus = $purchaseOrder->status;
                $newStatus = $request->status;

                \Log::info('Attempting status transition', [
                    'po_id' => $id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);

                // Validasi untuk batalkan - hanya bisa sebelum penerimaan_barang
                if ($newStatus === 'dibatalkan' && in_array($oldStatus, ['penerimaan_barang', 'vendor_bill', 'partial', 'payment'])) {
                    throw new \Exception("PO tidak dapat dibatalkan setelah status Penerimaan Barang");
                }

                // Jika status sama, tidak perlu update
                if ($oldStatus === $newStatus) {
                    throw new \Exception("Transisi status ke status yang sama tidak diperlukan");
                }

                // Validasi transisi status
                $validTransitions = [
                    'permintaan_pembelian' => ['request_quotation', 'dibatalkan'],
                    'request_quotation' => ['purchase_order', 'dibatalkan'],
                    'purchase_order' => ['penerimaan_barang', 'dibatalkan'],
                    'penerimaan_barang' => ['vendor_bill', 'dibatalkan'],
                    'vendor_bill' => ['partial', 'payment', 'dibatalkan'],
                    'partial' => ['payment'],
                    'payment' => [],
                    'dibatalkan' => []
                ];

                if (!in_array($newStatus, $validTransitions[$oldStatus])) {
                    throw new \Exception("Transisi status dari {$oldStatus} ke {$newStatus} tidak valid");
                }

                $updateData = [
                    'status' => $newStatus,
                    'catatan_status' => $request->catatan
                ];

                // Handle status-specific data
                switch ($newStatus) {
                    case 'request_quotation':
                        $updateData['tanggal_quotation'] = $request->tanggal_quotation;
                        $updateData['no_quotation'] = $request->no_quotation;
                        $updateData['catatan_quotation'] = $request->catatan;
                        break;

                    case 'purchase_order':
                        // Generate PO Number saat status menjadi Purchase Order
                        $poNumber = PurchaseOrderCounter::generatePONumber($purchaseOrder->id_outlet);
                        $updateData['no_po'] = $poNumber;
                        \Log::info('PO Number generated', ['po_id' => $id, 'po_number' => $poNumber]);
                        break;

                    case 'penerimaan_barang':
                        $updateData['tanggal_penerimaan'] = $request->tanggal_penerimaan;
                        $updateData['penerima_barang'] = $request->penerima_barang;
                        $updateData['catatan_penerimaan'] = $request->catatan;
                        // Update stok ketika barang diterima
                        $this->updateStockOnReceive($purchaseOrder);
                        break;

                    case 'vendor_bill':
                        $updateData['tanggal_vendor_bill'] = $request->tanggal_vendor_bill;
                        $updateData['no_vendor_bill'] = $request->no_vendor_bill; // Ini akan menjadi no_invoice
                        $updateData['catatan_vendor_bill'] = $request->catatan;
                        
                        // HANYA buat purchase invoice saat vendor bill
                        $invoiceNumber = $request->no_vendor_bill; // Gunakan no yang diinput user
                        $this->createVendorBill($purchaseOrder, $invoiceNumber);
                        break;

                    case 'payment':
                        // Untuk payment, hanya update status
                        $updateData['status'] = 'payment';
                        $updateData['tanggal_payment'] = now();
                        break;

                    case 'dibatalkan':
                        $this->cancelPurchaseOrder($purchaseOrder);
                        break;
                }

                $purchaseOrder->update($updateData);
                
                \Log::info('Status transition successful', [
                    'po_id' => $id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);
                
                // Buat jurnal otomatis jika diperlukan
                if (in_array($newStatus, ['penerimaan_barang', 'vendor_bill', 'payment'])) {
                    $this->createAutomaticJournal($purchaseOrder, $newStatus, $oldStatus);
                }
            });

            return response()->json([
                'success' => true, 
                'message' => 'Status berhasil diupdate menjadi ' . $this->getStatusText($request->status)
            ]);

        } catch (\Exception $e) {
            \Log::error('Update status error: ' . $e->getMessage(), [
                'po_id' => $id,
                'request_status' => $request->status
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function createVendorBill($purchaseOrder, $invoiceNumber)
    {
        // Cek apakah invoice sudah ada
        $existingInvoice = PurchaseInvoice::where('id_purchase_order', $purchaseOrder->id_purchase_order)->first();
        
        if ($existingInvoice) {
            \Log::info('Invoice already exists for PO', [
                'po_id' => $purchaseOrder->id_purchase_order,
                'invoice_id' => $existingInvoice->id_purchase_invoice
            ]);
            return $existingInvoice;
        }

        // Create Purchase Invoice
        $invoice = PurchaseInvoice::create([
            'no_invoice' => $invoiceNumber,
            'id_purchase_order' => $purchaseOrder->id_purchase_order,
            'tanggal_invoice' => now(),
            'tanggal_jatuh_tempo' => now()->addDays(30),
            'subtotal' => $purchaseOrder->subtotal,
            'total_diskon' => $purchaseOrder->total_diskon,
            'total_pajak' => 0,
            'total' => $purchaseOrder->total,
            'status' => 'draft', // Status draft untuk vendor bill
            'metode_pembayaran' => null, // Belum ada pembayaran
            'keterangan' => 'Vendor Bill untuk PO: ' . $purchaseOrder->no_po,
        ]);

        // Create Invoice Items dari PO Items
        foreach ($purchaseOrder->items as $item) {
            PurchaseInvoiceItem::create([
                'id_purchase_invoice' => $invoice->id_purchase_invoice,
                'id_purchase_order_item' => $item->id_purchase_order_item,
                'deskripsi' => $item->deskripsi,
                'kuantitas' => $item->kuantitas,
                'satuan' => $item->satuan,
                'harga' => $item->harga,
                'diskon' => $item->diskon,
                'pajak' => 0,
                'subtotal' => $item->subtotal,
            ]);
        }

        \Log::info('Vendor bill created', [
            'po_id' => $purchaseOrder->id_purchase_order,
            'invoice_id' => $invoice->id_purchase_invoice,
            'invoice_number' => $invoiceNumber
        ]);

        return $invoice;
    }

    /**
     * Update stock when PO is received
     */
    private function updateStockOnReceive($purchaseOrder)
    {
        foreach ($purchaseOrder->items as $item) {
            if ($item->id_produk && $item->produk) {
                // Update stok produk (FIFO)
                $this->updateProductStock($item->produk, $item->kuantitas, $item->harga);
            } else {
                // Update stok bahan
                $this->updateBahanStock($item->deskripsi, $item->kuantitas, $item->harga, $purchaseOrder->id_outlet);
            }
        }
    }

    /**
     * Update product stock using FIFO method
     */
    private function updateProductStock($produk, $kuantitas, $hargaBeli)
    {
        try {
            // Create HPP record for FIFO
            $hppProduk = \App\Models\HppProduk::create([
                'id_produk' => $produk->id_produk,
                'hpp' => $hargaBeli,
                'stok' => $kuantitas,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            \Log::info('Product stock updated with FIFO', [
                'produk' => $produk->nama_produk,
                'qty_added' => $kuantitas,
                'hpp' => $hargaBeli,
                'new_stock' => $produk->stok
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating product stock: ' . $e->getMessage());
            throw new \Exception("Gagal update stok produk: " . $e->getMessage());
        }
    }

    /**
     * Update bahan stock
     */
    private function updateBahanStock($namaBahan, $kuantitas, $harga, $outletId)
    {
        try {
            // Cari bahan berdasarkan nama
            $bahan = \App\Models\Bahan::where('nama_bahan', $namaBahan)
                ->where('id_outlet', $outletId)
                ->first();

            if (!$bahan) {
                // Buat bahan baru jika tidak ditemukan
                $bahan = \App\Models\Bahan::create([
                    'kode_bahan' => \App\Models\Bahan::generateKodeBahan(),
                    'nama_bahan' => $namaBahan,
                    'id_outlet' => $outletId,
                    'id_satuan' => 1, // Default satuan, sesuaikan dengan kebutuhan
                    'merk' => '-',
                    'is_active' => true
                ]);
            }

            // Tambahkan stok bahan
            $bahanDetail = \App\Models\BahanDetail::create([
                'id_bahan' => $bahan->id_bahan,
                'harga_beli' => $harga,
                'stok' => $kuantitas,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            \Log::info('Bahan stock updated', [
                'bahan' => $bahan->nama_bahan,
                'qty_added' => $kuantitas,
                'harga' => $harga,
                'outlet' => $outletId
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating bahan stock: ' . $e->getMessage());
            throw new \Exception("Gagal update stok bahan: " . $e->getMessage());
        }
    }

    /**
     * Calculate new HPP using weighted average
     */
    private function calculateNewHpp($produk, $qtyBaru, $hargaBeli)
    {
        $stokLama = $produk->stok;
        $hppLama = $produk->hpp;
        
        if ($stokLama + $qtyBaru == 0) {
            return $hargaBeli;
        }
        
        return (($stokLama * $hppLama) + ($qtyBaru * $hargaBeli)) / ($stokLama + $qtyBaru);
    }

    private function cancelPurchaseOrder($purchaseOrder)
    {
        // Validasi: hanya bisa cancel sebelum penerimaan_barang
        if (in_array($purchaseOrder->status, ['penerimaan_barang', 'vendor_bill', 'payment'])) {
            throw new \Exception("PO tidak dapat dibatalkan setelah status Penerimaan Barang");
        }
        
        // Batalkan invoice terkait jika ada
        foreach ($purchaseOrder->invoices as $invoice) {
            $invoice->update(['status' => 'dibatalkan']);
            
            // Batalkan pembayaran terkait
            $invoice->payments()->update(['status' => 'dibatalkan']);
        }
        
        \Log::info('Purchase order cancelled', ['po_id' => $purchaseOrder->id_purchase_order]);
    }

    /**
     * Delete Purchase Order
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $purchaseOrder = PurchaseOrder::findOrFail($id);
                
                // Hanya bisa hapus PO dengan status draft
                if ($purchaseOrder->status !== 'draft') {
                    throw new \Exception("Hanya purchase order dengan status draft yang dapat dihapus");
                }
                
                PurchaseOrderItem::where('id_purchase_order', $id)->delete();
                $purchaseOrder->delete();
            });
            
            return response()->json(['success' => true, 'message' => 'Purchase order berhasil dihapus']);
            
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus purchase order: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSuppliers(Request $request)
    {
        $search = $request->get('search', '');
        $outletIds = $request->get('outlet_ids', []); // Support multiple outlets
        
        try {
            $query = \App\Models\Supplier::query();
            
            // Filter multiple outlets
            if (!empty($outletIds) && is_array($outletIds)) {
                $query->whereIn('id_outlet', $outletIds);
            }
            
            // Kemudian filter search
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
                });
            }
            
            $suppliers = $query->orderBy('nama')
                        ->get()
                        ->map(function($supplier) {
                            return [
                                'id_supplier' => $supplier->id_supplier,
                                'nama' => $supplier->nama,
                                'telepon' => $supplier->telepon,
                                'alamat' => $supplier->alamat,
                                'email' => $supplier->email,
                                'id_outlet' => $supplier->id_outlet,
                                'outlet_name' => $supplier->outlet->nama_outlet ?? 'Semua Outlet'
                            ];
                        });
            
            \Log::info('Suppliers filtered - Multiple Outlets', [
                'search' => $search,
                'outlet_ids' => $outletIds,
                'count' => $suppliers->count(),
                'supplier_outlets' => $suppliers->pluck('id_outlet')->unique()->values()
            ]);
            
            return response()->json([
                'success' => true,
                'suppliers' => $suppliers
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getSuppliers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suppliers'
            ], 500);
        }
    }


    /**
     * Get Outlets
     */
    public function getOutlets()
    {
        try {
            $outlets = Outlet::where('is_active', true)
                        ->select('id_outlet', 'kode_outlet', 'nama_outlet')
                        ->orderBy('nama_outlet')
                        ->get();
            
            return response()->json([
                'success' => true,
                'outlets' => $outlets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving outlets'
            ], 500);
        }
    }

    public function printDocument($id)
    {
        try {
            $companySettings = $this->getCompanySettingsForPrint();
            // Pastikan relasi supplier diload dengan data bank
            $purchaseOrder = PurchaseOrder::with([
                'supplier', // Pastikan ini menginclude semua field supplier
                'items', 
                'outlet', 
                'invoices',
                'user'
            ])->findOrFail($id);
            
            $setting = DB::table('setting')->first();
            
            // Debug: Check data supplier
            \Log::info('Supplier Data for PO ' . $id, [
                'supplier_id' => $purchaseOrder->supplier->id_supplier ?? null,
                'supplier_name' => $purchaseOrder->supplier->nama ?? null,
                'supplier_bank' => $purchaseOrder->supplier->bank ?? null,
                'supplier_rekening' => $purchaseOrder->supplier->no_rekening ?? null,
                'supplier_atas_nama' => $purchaseOrder->supplier->atas_nama ?? null
            ]);
            
            // Tentukan template berdasarkan status
            $template = request()->get('template', 'standard');
            $preview = request()->get('preview', false);
            $download = request()->get('download', false);
            
            // Tentukan view dan data berdasarkan status
            if ($purchaseOrder->status === 'permintaan_pembelian' || $purchaseOrder->status === 'request_quotation') {
                // Draft - layout sederhana
                $view = 'admin.pembelian.purchase-order.print-draft';
                $printNumber = $purchaseOrder->no_po;
                $documentTitle = 'DRAFT PURCHASE ORDER';
            } elseif ($purchaseOrder->status === 'vendor_bill' || $purchaseOrder->status === 'partial' || $purchaseOrder->status === 'payment') {
                // Invoice - layout invoice (untuk vendor_bill, partial, dan payment)
                $view = 'admin.pembelian.purchase-order.print-invoice';
                $printNumber = $purchaseOrder->no_vendor_bill ?? $purchaseOrder->invoices->first()->no_invoice ?? $purchaseOrder->no_po;
                
                if ($purchaseOrder->status === 'vendor_bill') {
                    $documentTitle = 'VENDOR BILL / INVOICE';
                } elseif ($purchaseOrder->status === 'partial') {
                    $documentTitle = 'VENDOR BILL / INVOICE (DIBAYAR SEBAGIAN)';
                } else {
                    $documentTitle = 'PAYMENT RECEIPT';
                }
            } else {
                // PO Standar
                $view = 'admin.pembelian.purchase-order.print';
                $printNumber = $purchaseOrder->no_po;
                $documentTitle = 'PURCHASE ORDER';
            }
            
            if ($preview) {
                return view($view, compact('purchaseOrder', 'setting', 'template', 'printNumber', 'documentTitle', 'companySettings'));
            }
            
            $pdf = Pdf::loadView($view, compact('purchaseOrder', 'setting', 'template', 'printNumber', 'documentTitle', 'companySettings'));
            $pdf->setPaper('A4', 'portrait');
            
            $safeFilename = strtolower(str_replace(' ', '-', $documentTitle)) . '-' . str_replace('/', '-', $printNumber) . '.pdf';
            
            if ($download) {
                return $pdf->download($safeFilename);
            }
            
            return $pdf->stream($safeFilename);
            
        } catch (\Exception $e) {
            \Log::error('Error printing document: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal generate PDF'], 500);
        }
    }

    public function purchaseOrderPrint($id)
    {
        try {
            $companySettings = $this->getCompanySettingsForPrint();
            $purchaseOrder = PurchaseOrder::with(['supplier', 'items', 'outlet', 'invoices'])->findOrFail($id);
            $setting = DB::table('setting')->first();
            
            // Tentukan nomor yang akan ditampilkan berdasarkan status
            $printNumber = $purchaseOrder->no_po; // default
            
            // Gunakan document_number dari parameter jika ada (untuk preview)
            $requestDocumentNumber = request()->get('document_number');
            if ($requestDocumentNumber) {
                $printNumber = $requestDocumentNumber;
            } else {
                // Logic untuk menentukan nomor berdasarkan status
                if ($purchaseOrder->status === 'vendor_bill' && $purchaseOrder->no_vendor_bill) {
                    $printNumber = $purchaseOrder->no_vendor_bill;
                }
                
                // Jika ada invoice, gunakan no_invoice
                $invoice = $purchaseOrder->invoices->first();
                if ($invoice && $invoice->no_invoice) {
                    $printNumber = $invoice->no_invoice;
                }
            }
            
            $template = request()->get('template', 'standard');
            $preview = request()->get('preview', false);
            $download = request()->get('download', false);
            
            if ($preview) {
                return view('admin.pembelian.purchase-order.print', compact('purchaseOrder', 'setting', 'template', 'printNumber', 'companySettings'));
            }
            
            $pdf = Pdf::loadView('admin.pembelian.purchase-order.print', compact('purchaseOrder', 'setting', 'template', 'printNumber', 'companySettings'));
            $pdf->setPaper('A4', 'portrait');
            
            $safeFilename = 'document-' . str_replace('/', '-', $printNumber) . '.pdf';
            
            if ($download) {
                return $pdf->download($safeFilename);
            }
            
            return $pdf->stream($safeFilename);
            
        } catch (\Exception $e) {
            \Log::error('Error printing document: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal generate PDF'], 500);
        }
    }

    public function purchaseOrderStatusCounts(Request $request)
    {
        $outletIds = $request->get('outlet_ids', []); // Support multiple outlets
        
        $query = PurchaseOrder::query();
        
        // Filter multiple outlets
        if (!empty($outletIds) && is_array($outletIds)) {
            $query->whereIn('id_outlet', $outletIds);
        }
        
        $counts = [
            'total' => $query->count(),
            'permintaan_pembelian' => $query->clone()->where('status', 'permintaan_pembelian')->count(),
            'request_quotation' => $query->clone()->where('status', 'request_quotation')->count(),
            'purchase_order' => $query->clone()->where('status', 'purchase_order')->count(),
            'penerimaan_barang' => $query->clone()->where('status', 'penerimaan_barang')->count(),
            'vendor_bill' => $query->clone()->where('status', 'vendor_bill')->count(),
            'partial' => $query->clone()->where('status', 'partial')->count(),
            'payment' => $query->clone()->where('status', 'payment')->count(),
            'dibatalkan' => $query->clone()->where('status', 'dibatalkan')->count(),
        ];
        
        return response()->json($counts);
    }

    public function purchaseOrderExportPdf(Request $request)
    {
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $supplierFilter = $request->get('supplier_filter', 'all');

        $query = PurchaseOrder::with(['supplier', 'items']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if ($supplierFilter !== 'all') {
            $query->where('id_supplier', $supplierFilter);
        }

        $purchaseOrders = $query->orderBy('tanggal', 'desc')->get();

        $pdf = Pdf::loadView('admin.pembelian.purchase-order.export_pdf', compact('purchaseOrders', 'status', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'laporan-purchase-order-' . date('Y-m-d') . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Export Excel
     */
    public function purchaseOrderExportExcel(Request $request)
    {
        return Excel::download(new PurchaseOrderExport($request), 'purchase-order-' . date('Y-m-d') . '.xlsx');
    }

    public function purchaseOrderSetting(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        if (!$outletId) {
            return response()->json([
                'success' => false,
                'message' => 'Outlet ID diperlukan'
            ], 422);
        }

        $counter = PurchaseOrderCounter::where('id_outlet', $outletId)->first();
        
        if (!$counter) {
            $counter = new PurchaseOrderCounter();
            $counter->id_outlet = $outletId;
            $counter->last_number = 0;
            $counter->year = date('Y');
            $counter->prefix = 'PO';
            $counter->save();
        }
        
        $currentNumber = $counter->last_number;
        $currentYear = $counter->year;
        $prefix = $counter->prefix;
        
        $currentPONumber = str_pad($currentNumber, 3, '0', STR_PAD_LEFT) . '/' . $prefix . '/' . PurchaseOrderCounter::getRomanMonth() . '/' . $currentYear;
        $nextPONumber = str_pad($currentNumber + 1, 3, '0', STR_PAD_LEFT) . '/' . $prefix . '/' . PurchaseOrderCounter::getRomanMonth() . '/' . $currentYear;
        
        return response()->json([
            'success' => true,
            'current_po_number' => $currentPONumber,
            'next_po_number' => $nextPONumber,
            'current_number' => $currentNumber,
            'current_year' => $currentYear,
            'prefix' => $prefix
        ]);
    }

    public function updatePurchaseOrderSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'starting_number' => 'required|integer|min:1|max:999',
            'year' => 'required|integer|min:2020|max:2030',
            'prefix' => 'required|string|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            PurchaseOrderCounter::setStartingNumber(
                $request->outlet_id,
                $request->starting_number - 1,
                $request->year,
                $request->prefix
            );

            return response()->json([
                'success' => true,
                'message' => 'Setting nomor purchase order berhasil disimpan untuk outlet ini'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating purchase order setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan setting: ' . $e->getMessage()
            ], 500);
        }
    }


    public function coaSettingPurchase(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            
            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 422);
            }

            $setting = SettingCOAPurchase::where('id_outlet', $outletId)->first();
            $accountingBooks = AccountingBook::where('status', 'active')
                ->where(function($query) use ($outletId) {
                    $query->where('outlet_id', $outletId)
                        ->orWhereNull('outlet_id'); // Buku global tanpa outlet
                })
                ->orderBy('name')
                ->get()
                ->map(function($book) {
                    return [
                        'id' => $book->id,
                        'name' => $book->name,
                        'code' => $book->code,
                        'outlet_id' => $book->outlet_id,
                        'outlet_name' => $book->outlet ? $book->outlet->nama_outlet : 'Global',
                        'full_name' => $book->name . ' (' . $book->code . ')' . ($book->outlet ? ' - ' . $book->outlet->nama_outlet : ' - Global')
                    ];
                });
            
            
            // Get COA accounts yang spesifik untuk outlet ini
            $accounts = ChartOfAccount::where('status', 'active')
                ->where(function($query) use ($outletId) {
                    $query->where('outlet_id', $outletId)
                        ->orWhereNull('outlet_id'); // Account global tanpa outlet
                })
                ->orderBy('code')
                ->get()
                ->map(function($account) {
                     $level = $account->parent_id ? 2 : 1;
                    return [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'type' => $account->type,
                        'type_name' => $this->getAccountTypeName($account->type),
                        'outlet_id' => $account->outlet_id,
                        'outlet_name' => $account->outlet ? $account->outlet->nama_outlet : 'Global',
                        'full_name' => $account->code . ' - ' . $account->name . ($account->outlet ? ' (' . $account->outlet->nama_outlet . ')' : ' (Global)'),
                        'parent_id' => $account->parent_id,
                        'level' => $level
                    ];
                });

            \Log::info('COA Setting loaded for outlet', [
                'outlet_id' => $outletId,
                'setting_exists' => !is_null($setting),
                'accounting_books_count' => $accountingBooks->count(),
                'accounts_count' => $accounts->count(),
                'accounts_outlets' => $accounts->pluck('outlet_id')->unique()->values()
            ]);
            
            return response()->json([
                'success' => true,
                'setting' => $setting,
                'accounting_books' => $accountingBooks,
                'accounts' => $accounts
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading COA setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat setting COA'
            ], 500);
        }
    }

/**
 * Helper method untuk nama tipe akun
 */
private function getAccountTypeName($type)
{
    $typeNames = [
        'asset' => 'Aset',
        'liability' => 'Kewajiban',
        'equity' => 'Ekuitas',
        'revenue' => 'Pendapatan',
        'expense' => 'Beban'
    ];
    
    return $typeNames[$type] ?? $type;
}

    public function coaSettingPurchaseUpdate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'outlet_id' => 'required|exists:outlets,id_outlet',
        'accounting_book_id' => 'required|exists:accounting_books,id',
        'akun_hutang_usaha' => 'required|string',
        'akun_hutang_sementara' => 'required|string', // Tambahkan validasi
        'akun_persediaan' => 'required|string',
        'akun_pembelian' => 'required|string',
        'akun_kas' => 'required|string',
        'akun_bank' => 'required|string',
        'akun_ppn_masukan' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::transaction(function () use ($request) {
            $setting = SettingCOAPurchase::where('id_outlet', $request->outlet_id)->first();
            if (!$setting) {
                $setting = new SettingCOAPurchase();
                $setting->id_outlet = $request->outlet_id;
            }

            $setting->fill([
                'accounting_book_id' => $request->accounting_book_id,
                'akun_hutang_usaha' => $request->akun_hutang_usaha,
                'akun_hutang_sementara' => $request->akun_hutang_sementara, // Tambahkan ini
                'akun_persediaan' => $request->akun_persediaan,
                'akun_pembelian' => $request->akun_pembelian,
                'akun_kas' => $request->akun_kas,
                'akun_bank' => $request->akun_bank,
                'akun_ppn_masukan' => $request->akun_ppn_masukan,
            ])->save();

            \Log::info('COA purchase setting updated', [
                'setting_id' => $setting->id_setting,
                'outlet_id' => $request->outlet_id,
                'accounting_book_id' => $request->accounting_book_id
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Setting COA pembelian berhasil disimpan untuk outlet ini'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error saving COA purchase setting: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan setting: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Helper Methods
     */
    private function flattenAccounts($accounts, &$result = [], $level = 0)
    {
        foreach ($accounts as $account) {
            if (isset($account['code']) && isset($account['name'])) {
                $result[] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'type' => $account['type'] ?? '',
                    'level' => $level
                ];
            }
            
            if (isset($account['children']) && is_array($account['children'])) {
                $this->flattenAccounts($account['children'], $result, $level + 1);
            }
        }
    }


    private function createAccountFromConfig($code)
    {
        try {
            $allAccounts = collect(config('accounts.accounts', []));
            $accountData = $this->findAccountInConfig($allAccounts, $code);
            
            if (!$accountData) {
                throw new \Exception("Akun dengan kode {$code} tidak ditemukan di config");
            }
            
            $account = \App\Models\ChartOfAccount::create([
                'code' => $accountData['code'],
                'name' => $accountData['name'],
                'type' => $accountData['type'],
                'description' => $accountData['description'] ?? null,
                'status' => $accountData['status'] ?? 'active',
                'parent_id' => null,
            ]);
            
            \Log::info("Akun baru dibuat: {$accountData['code']} - {$accountData['name']}");
            
            return $account->id;
            
        } catch (\Exception $e) {
            \Log::error("Gagal membuat akun dari config: " . $e->getMessage());
            throw new \Exception("Akun dengan kode {$code} tidak ditemukan dan gagal dibuat: " . $e->getMessage());
        }
    }

    private function findAccountInConfig($accounts, $code)
    {
        foreach ($accounts as $account) {
            if (isset($account['code']) && $account['code'] === $code) {
                return $account;
            }
            
            if (isset($account['children']) && is_array($account['children'])) {
                $found = $this->findAccountInConfig($account['children'], $code);
                if ($found) {
                    return $found;
                }
            }
        }
        
        return null;
    }

    public function getPOForInvoice($id)
    {
        try {
            \Log::info('Fetching PO for invoice', ['po_id' => $id]);
            
            $purchaseOrder = PurchaseOrder::with([
                    'supplier', 
                    'outlet',
                    'items' => function($query) {
                        $query->with(['produk' => function($q) {
                            $q->with('satuan');
                        }, 'bahan' => function($q) {
                            $q->with('satuan');
                        }]);
                    },
                    'invoices' => function($query) {
                        $query->with(['items', 'payments']) // Pastikan payments di-load
                            ->orderBy('tanggal_invoice', 'desc');
                    }
                ])->findOrFail($id);
            
            // Tambahkan info payment proof untuk setiap PO
            $purchaseOrder->has_payment_proof = false;
            
            if ($purchaseOrder->invoices->count() > 0) {
                foreach ($purchaseOrder->invoices as $invoice) {
                    if ($invoice->payments->count() > 0) {
                        foreach ($invoice->payments as $payment) {
                            if ($payment->bukti_bayar) {
                                $purchaseOrder->has_payment_proof = true;
                                break 2; // Keluar dari kedua loop
                            }
                        }
                    }
                }
            }
            
            \Log::info('PO data found', [
                'po_id' => $purchaseOrder->id_purchase_order,
                'item_count' => $purchaseOrder->items->count(),
                'supplier' => $purchaseOrder->supplier->nama_supplier ?? 'N/A',
                'has_payment_proof' => $purchaseOrder->has_payment_proof
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $purchaseOrder
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting PO for invoice: ' . $e->getMessage(), ['po_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Purchase order tidak ditemukan'
            ], 404);
        }
    }

    public function createPurchaseInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_purchase_order' => 'required|exists:purchase_order,id_purchase_order',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id_purchase_order_item' => 'required|exists:purchase_order_item,id_purchase_order_item',
            'items.*.kuantitas' => 'required|numeric|min:0',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.pajak' => 'nullable|numeric|min:0',
            'mark_as_paid' => 'boolean',
            'metode_pembayaran' => 'required_if:mark_as_paid,true|string',
            'no_referensi_payment' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $purchaseOrder = PurchaseOrder::find($request->id_purchase_order);
                
                // Cek apakah invoice sudah ada (dari vendor bill)
                $existingInvoice = PurchaseInvoice::where('id_purchase_order', $request->id_purchase_order)->first();
                
                if ($existingInvoice) {
                    // Update existing invoice untuk payment
                    $existingInvoice->update([
                        'tanggal_invoice' => $request->tanggal_invoice,
                        'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                        'metode_pembayaran' => $request->metode_pembayaran,
                        'keterangan' => $request->keterangan,
                        'status' => $request->mark_as_paid ? 'dibayar' : 'draft',
                        'tanggal_bayar' => $request->mark_as_paid ? $request->tanggal_invoice : null,
                    ]);
                    
                    $invoice = $existingInvoice;
                } else {
                    // Generate Invoice Number
                    $invoiceNumber = 'INV/' . date('Ym') . '/' . str_pad(PurchaseInvoice::count() + 1, 4, '0', STR_PAD_LEFT);
                    
                    // Hitung totals
                    $subtotal = 0;
                    $totalDiskon = 0;
                    $totalPajak = 0;
                    
                    foreach ($request->items as $item) {
                        $itemSubtotal = ($item['kuantitas'] * $item['harga']) - $item['diskon'] + $item['pajak'];
                        
                        $subtotal += $itemSubtotal;
                        $totalDiskon += $item['diskon'];
                        $totalPajak += $item['pajak'];
                    }
                    
                    $total = $subtotal;

                    // Create Purchase Invoice
                    $invoice = PurchaseInvoice::create([
                        'no_invoice' => $invoiceNumber,
                        'id_purchase_order' => $request->id_purchase_order,
                        'tanggal_invoice' => $request->tanggal_invoice,
                        'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                        'subtotal' => $subtotal,
                        'total_diskon' => $totalDiskon,
                        'total_pajak' => $totalPajak,
                        'total' => $total,
                        'status' => $request->mark_as_paid ? 'dibayar' : 'draft',
                        'metode_pembayaran' => $request->metode_pembayaran,
                        'keterangan' => $request->keterangan,
                        'tanggal_bayar' => $request->mark_as_paid ? $request->tanggal_invoice : null,
                    ]);

                    // Create Invoice Items
                    foreach ($request->items as $item) {
                        PurchaseInvoiceItem::create([
                            'id_purchase_invoice' => $invoice->id_purchase_invoice,
                            'id_purchase_order_item' => $item['id_purchase_order_item'],
                            'deskripsi' => $item['deskripsi'],
                            'kuantitas' => $item['kuantitas'],
                            'satuan' => $item['satuan'],
                            'harga' => $item['harga'],
                            'diskon' => $item['diskon'],
                            'pajak' => $item['pajak'] ?? 0,
                            'subtotal' => ($item['kuantitas'] * $item['harga']) - $item['diskon'] + ($item['pajak'] ?? 0),
                        ]);
                    }
                }

                // Jika mark as paid, update status PO
                if ($request->mark_as_paid) {
                    $purchaseOrder->update([
                        'status' => 'payment',
                        'tanggal_payment' => $request->tanggal_invoice,
                        'metode_payment' => $request->metode_pembayaran,
                        'no_referensi_payment' => $request->no_referensi_payment,
                        'catatan_payment' => $request->keterangan,
                        'tanggal_dibayar' => $request->tanggal_invoice
                    ]);

                    // Buat jurnal otomatis untuk payment
                    $this->createAutomaticJournal($purchaseOrder, 'payment', 'vendor_bill');
                }

                \Log::info('Purchase invoice processed successfully', [
                    'invoice_id' => $invoice->id_purchase_invoice,
                    'po_id' => $request->id_purchase_order,
                    'status' => $invoice->status,
                    'mark_as_paid' => $request->mark_as_paid,
                    'existing_invoice_updated' => !!$existingInvoice
                ]);
            });

            $message = $request->mark_as_paid 
                ? 'Pembayaran berhasil dikonfirmasi'
                : 'Invoice berhasil disimpan sebagai draft';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('Error processing purchase invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function supplierManagement(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', 'all');
            
            \Log::info('Supplier Management Request', [
                'outlet_id' => $outletId,
                'request_params' => $request->all()
            ]);
            
            $query = Supplier::with(['outlet']);

            // Filter berdasarkan outlet
            if ($outletId !== 'all') {
                $query->where('id_outlet', $outletId);
            }

            $suppliers = $query->orderBy('nama')
                ->get()
                ->map(function($supplier) {
                    return [
                        'id_supplier' => $supplier->id_supplier,
                        'nama' => $supplier->nama,
                        'telepon' => $supplier->telepon,
                        'alamat' => $supplier->alamat,
                        'email' => $supplier->email,
                        'is_active' => $supplier->is_active,
                        'id_outlet' => $supplier->id_outlet,
                        'outlet_name' => $supplier->outlet->nama_outlet ?? 'Semua Outlet',
                        'created_at' => $supplier->created_at->format('d/m/Y'),
                        'bank' => $supplier->bank,
                        'no_rekening' => $supplier->no_rekening,
                        'atas_nama' => $supplier->atas_nama
                    ];
                });

            $outlets = Outlet::where('is_active', true)->get();

            \Log::info('Supplier Management Response', [
                'outlet_id' => $outletId,
                'suppliers_count' => $suppliers->count(),
                'outlets_count' => $outlets->count(),
                'suppliers' => $suppliers->take(3)->toArray() // Log first 3 suppliers for debugging
            ]);

            return response()->json([
                'success' => true,
                'suppliers' => $suppliers,
                'outlets' => $outlets,
                'current_outlet' => $outletId
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading supplier management: ' . $e->getMessage(), [
                'outlet_id' => $outletId ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create/Update Supplier
     */
    public function supplierStore(Request $request)
    {
        \Log::info('Supplier Store Request', [
            'request_data' => $request->all(),
            'is_update' => !empty($request->id_supplier)
        ]);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email',
            'id_outlet' => 'required|exists:outlets,id_outlet',
        ]);

        if ($validator->fails()) {
            \Log::warning('Supplier Store Validation Failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $supplier = null;
            DB::transaction(function () use ($request, &$supplier) {
                $data = [
                    'nama' => $request->nama,
                    'telepon' => $request->telepon,
                    'alamat' => $request->alamat,
                    'email' => $request->email,
                    'id_outlet' => $request->id_outlet,
                    'bank' => $request->bank,
                    'no_rekening' => $request->no_rekening,
                    'atas_nama' => $request->atas_nama,
                ];

                if ($request->id_supplier) {
                    // Update supplier
                    $supplier = Supplier::findOrFail($request->id_supplier);
                    $supplier->update($data);
                    \Log::info('Supplier updated', ['id' => $supplier->id_supplier, 'data' => $data]);
                } else {
                    // Create supplier
                    $supplier = Supplier::create($data);
                    \Log::info('Supplier created', ['id' => $supplier->id_supplier, 'data' => $data]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => $request->id_supplier ? 'Supplier berhasil diupdate' : 'Supplier berhasil dibuat',
                'supplier' => $supplier
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saving supplier: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    public function supplierShow($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'supplier' => [
                    'id_supplier' => $supplier->id_supplier,
                    'nama' => $supplier->nama,
                    'telepon' => $supplier->telepon,
                    'alamat' => $supplier->alamat,
                    'email' => $supplier->email,
                    'id_outlet' => $supplier->id_outlet,
                    'is_active' => $supplier->is_active,
                    'bank' => $supplier->bank,
                    'no_rekening' => $supplier->no_rekening,
                    'atas_nama' => $supplier->atas_nama
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting supplier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Supplier tidak ditemukan'
            ], 404);
        }
    }

    public function supplierDestroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            
            // Check if supplier has related purchase orders
            $hasPO = \App\Models\PurchaseOrder::where('id_supplier', $id)->exists();
            if ($hasPO) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus supplier yang memiliki purchase order'
                ], 422);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting supplier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProdukPembelian(Request $request)
    {
        $search = $request->get('search', '');
        $outletId = $request->get('outlet_id');
        
        try {
            $query = Produk::with(['satuan', 'hppProduk'])
                ->where('is_active', true);

            // Filter berdasarkan outlet
            if ($outletId && $outletId !== 'ALL') {
                $query->where('id_outlet', $outletId);
            }

            // Filter pencarian
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
                });
            }

            $produks = $query->get()
                ->map(function($produk) {
                    // Hitung stok dari HPP
                    $stok = $produk->hppProduk->sum('stok');
                    
                    return [
                        'id_produk' => $produk->id_produk,
                        'kode_produk' => $produk->kode_produk,
                        'nama_produk' => $produk->nama_produk,
                        'harga_beli' => $produk->harga_beli ?? 0,
                        'stok' => $stok,
                        'satuan' => $produk->satuan ? $produk->satuan->nama_satuan : 'Unit',
                        'id_outlet' => $produk->id_outlet,
                        'outlet_name' => $produk->outlet->nama_outlet ?? 'N/A',
                    ];
                });
            
            return response()->json([
                'success' => true,
                'produks' => $produks
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getProdukPembelian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products'
            ], 500);
        }
    }

    /**
     * Get Bahan for Purchase dengan filter outlet
     */
    public function getBahanPembelian(Request $request)
    {
        $search = $request->get('search', '');
        $outletId = $request->get('outlet_id');
        
        try {
            $query = Bahan::with(['satuan', 'hargaBahan'])
                ->where('is_active', true);

            // Filter berdasarkan outlet
            if ($outletId && $outletId !== 'ALL') {
                $query->where('id_outlet', $outletId);
            }

            // Filter pencarian
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('kode_bahan', 'like', "%{$search}%");
                });
            }

            $bahans = $query->get()
                ->map(function($bahan) {
                    // Hitung harga rata-rata dan stok dari harga_bahan
                    $totalHarga = $bahan->hargaBahan->sum(function($detail) {
                        return $detail->harga * $detail->stok;
                    });
                    $totalStok = $bahan->hargaBahan->sum('stok');
                    $hargaRataRata = $totalStok > 0 ? $totalHarga / $totalStok : 0;
                    
                    return [
                        'id_bahan' => $bahan->id_bahan,
                        'kode_bahan' => $bahan->kode_bahan,
                        'nama_bahan' => $bahan->nama_bahan,
                        'harga_rata_rata' => $hargaRataRata,
                        'stok' => $totalStok,
                        'satuan' => $bahan->satuan ? $bahan->satuan->nama_satuan : 'Unit',
                        'id_outlet' => $bahan->id_outlet,
                        'outlet_name' => $bahan->outlet->nama_outlet ?? 'N/A',
                    ];
                });
            
            return response()->json([
                'success' => true,
                'bahans' => $bahans
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getBahanPembelian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving bahan'
            ], 500);
        }
    }

    /**
     * Generate Invoice Preview PDF
     */
    public function invoicePreview(Request $request)
    {
        try {
            // Decode data dari query string
            $encodedData = $request->get('data');
            if (!$encodedData) {
                abort(400, 'Data tidak valid');
            }

            $invoiceData = json_decode(base64_decode($encodedData), true);
            
            // Validasi data
            if (!isset($invoiceData['po_id']) || !isset($invoiceData['items'])) {
                abort(400, 'Data invoice tidak lengkap');
            }

            // Get PO data
            $purchaseOrder = PurchaseOrder::with(['supplier', 'outlet'])->find($invoiceData['po_id']);
            if (!$purchaseOrder) {
                abort(404, 'Purchase Order tidak ditemukan');
            }

            $setting = DB::table('setting')->first();
            
            // Data untuk view
            $data = [
                'purchaseOrder' => $purchaseOrder,
                'setting' => $setting,
                'invoiceData' => $invoiceData,
                'isPreview' => true
            ];

            // Bersihkan nama file dari karakter tidak valid
            $safeFilename = 'invoice-preview-' . str_replace(['/', '\\'], '-', $purchaseOrder->no_po);

            // Return PDF preview
            if ($request->get('download')) {
                $pdf = PDF::loadView('admin.pembelian.purchase-order.invoice-preview', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($safeFilename . '.pdf');
            }

            // Stream preview
            $pdf = PDF::loadView('admin.pembelian.purchase-order.invoice-preview', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream($safeFilename . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generating invoice preview: ' . $e->getMessage());
            abort(500, 'Gagal generate preview invoice');
        }
    }

    //===========================INI DASHBOARD===================
/**
 * Dashboard Pembelian - Revisi
 */
public function dashboard(Request $request)
{
    try {
        $outletFilter = $request->get('outlet', 'all');
        $startDate = $request->get('from');
        $endDate = $request->get('to');

        // Base query untuk PO
        $poQuery = PurchaseOrder::with(['supplier', 'outlet', 'items.produk', 'items.bahan']);

        // Filter outlet
        if ($outletFilter !== 'all') {
            $poQuery->where('id_outlet', $outletFilter);
        }

        // Filter tanggal
        if ($startDate && $endDate) {
            $poQuery->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $poQuery->where('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $poQuery->where('tanggal', '<=', $endDate);
        }

        $purchaseOrders = $poQuery->get();

        // Hitung KPI dengan logic baru: status selain draft = sudah bayar
        $kpi = $this->calculateKPIsRevised($purchaseOrders);

        // Data untuk grafik per outlet
        $outletData = $this->getOutletData($purchaseOrders);

        // Infografis bahan/produk yang sering dibeli
        $topItems = $this->getTopPurchasedItems($purchaseOrders);

        // Transaksi terakhir
        $lastTransactions = $this->getLastTransactionsRevised($purchaseOrders);

        // Data untuk sparklines (trend 30 hari)
        $sparkData = $this->getSparkDataRevised($startDate, $endDate, $outletFilter);

        return response()->json([
            'success' => true,
            'data' => [
                'kpi' => $kpi,
                'outlet_data' => $outletData,
                'top_items' => $topItems,
                'last_transactions' => $lastTransactions,
                'spark_data' => $sparkData,
                'periode_label' => $this->getPeriodeLabel($startDate, $endDate)
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error loading purchase dashboard: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat dashboard pembelian'
        ], 500);
    }
}

/**
 * Calculate KPIs dengan logic baru: status selain draft = sudah bayar
 */
private function calculateKPIsRevised($purchaseOrders)
{
    $totalPO = $purchaseOrders->count();
    $totalItems = $purchaseOrders->sum(function($po) {
        return $po->items->sum('kuantitas');
    });
    $totalAmount = $purchaseOrders->sum('total');
    
    // Hitung PO yang sudah dibayar (status selain draft)
    $paidPOs = $purchaseOrders->filter(function($po) {
        return $po->status !== 'draft';
    });
    
    $paidAmount = $paidPOs->sum('total');
    $paidCount = $paidPOs->count();

    return [
        'po_count' => $totalPO,
        'paid_po_count' => $paidCount,
        'total_items' => $totalItems,
        'total_amount' => $totalAmount,
        'paid_amount' => $paidAmount,
        'outstanding' => $totalAmount - $paidAmount // Sisa yang belum dibayar (draft)
    ];
}

private function getTopPurchasedItems($purchaseOrders)
{
    $itemStats = [];
    
    foreach ($purchaseOrders as $po) {
        foreach ($po->items as $item) {
            if ($item->tipe_item === 'produk' && $item->produk) {
                $produkId = $item->produk->id_produk;
                $key = "produk_{$produkId}";
                
                if (!isset($itemStats[$key])) {
                    $itemStats[$key] = [
                        'id' => $produkId,
                        'nama' => $item->produk->nama_produk,
                        'tipe' => 'produk',
                        'kode' => $item->produk->kode_produk,
                        'total_dibeli' => 0,
                        'total_transaksi' => 0,
                        'stok_sekarang' => $item->produk->stok ?? 0,
                        'stok_terakhir' => 0,
                        'last_po_date' => null, // Tambah field untuk tracking tanggal
                        'satuan' => $item->produk->satuan->nama_satuan ?? 'Unit'
                    ];
                }
                
                $itemStats[$key]['total_dibeli'] += $item->kuantitas;
                $itemStats[$key]['total_transaksi'] += 1;
                
                // Update stok terakhir dari PO terbaru
                if (!$itemStats[$key]['last_po_date'] || $po->tanggal->gt($itemStats[$key]['last_po_date'])) {
                    $itemStats[$key]['stok_terakhir'] = $item->kuantitas;
                    $itemStats[$key]['last_po_date'] = $po->tanggal;
                }
            }
            elseif ($item->tipe_item === 'bahan' && $item->bahan) {
                $bahanId = $item->bahan->id_bahan;
                $key = "bahan_{$bahanId}";
                
                if (!isset($itemStats[$key])) {
                    // Hitung stok bahan saat ini
                    $stokSekarang = $item->bahan->hargaBahan->sum('stok');
                    
                    $itemStats[$key] = [
                        'id' => $bahanId,
                        'nama' => $item->bahan->nama_bahan,
                        'tipe' => 'bahan',
                        'kode' => $item->bahan->kode_bahan,
                        'total_dibeli' => 0,
                        'total_transaksi' => 0,
                        'stok_sekarang' => $stokSekarang,
                        'stok_terakhir' => 0,
                        'last_po_date' => null,
                        'satuan' => $item->bahan->satuan->nama_satuan ?? 'Unit'
                    ];
                }
                
                $itemStats[$key]['total_dibeli'] += $item->kuantitas;
                $itemStats[$key]['total_transaksi'] += 1;
                
                // Update stok terakhir dari PO terbaru
                if (!$itemStats[$key]['last_po_date'] || $po->tanggal->gt($itemStats[$key]['last_po_date'])) {
                    $itemStats[$key]['stok_terakhir'] = $item->kuantitas;
                    $itemStats[$key]['last_po_date'] = $po->tanggal;
                }
            }
            elseif ($item->tipe_item === 'manual') {
                $key = "manual_" . md5($item->deskripsi);
                
                if (!isset($itemStats[$key])) {
                    $itemStats[$key] = [
                        'id' => $key,
                        'nama' => $item->deskripsi,
                        'tipe' => 'manual',
                        'kode' => 'MANUAL',
                        'total_dibeli' => 0,
                        'total_transaksi' => 0,
                        'stok_sekarang' => 0,
                        'stok_terakhir' => 0,
                        'last_po_date' => null,
                        'satuan' => $item->satuan ?? 'Unit'
                    ];
                }
                
                $itemStats[$key]['total_dibeli'] += $item->kuantitas;
                $itemStats[$key]['total_transaksi'] += 1;
                
                // Update stok terakhir dari PO terbaru
                if (!$itemStats[$key]['last_po_date'] || $po->tanggal->gt($itemStats[$key]['last_po_date'])) {
                    $itemStats[$key]['stok_terakhir'] = $item->kuantitas;
                    $itemStats[$key]['last_po_date'] = $po->tanggal;
                }
            }
        }
    }
    
    // Hapus field last_po_date sebelum return
    $result = collect($itemStats)
        ->sortByDesc('total_dibeli')
        ->take(5)
        ->map(function($item) {
            unset($item['last_po_date']);
            return $item;
        })
        ->values()
        ->toArray();
    
    return $result;
}

/**
 * Get Last Transactions dengan logic status baru
 */
private function getLastTransactionsRevised($purchaseOrders)
{
    return $purchaseOrders->take(10)->map(function($po) {
        // Status selain draft = sudah bayar
        $isPaid = $po->status !== 'draft';
        $totalPaid = $isPaid ? $po->total : 0;
        $remaining = $isPaid ? 0 : $po->total;
        
        $totalItems = $po->items->sum('kuantitas');
        
        return [
            'id' => $po->id_purchase_order,
            'tanggal' => $po->tanggal->format('Y-m-d'),
            'outlet' => $po->outlet->nama_outlet ?? 'N/A',
            'supplier' => $po->supplier->nama_supplier ?? 'N/A',
            'total_item' => $totalItems,
            'total_harga' => $po->total,
            'total_bayar' => $totalPaid,
            'sisa' => $remaining,
            'status' => $po->status,
            'no_po' => $po->no_po,
            'is_paid' => $isPaid
        ];
    })->values()->toArray();
}

/**
 * Get Spark Data dengan logic status baru
 */
private function getSparkDataRevised($startDate, $endDate, $outletFilter)
{
    // Default 30 days if no date range
    if (!$startDate || !$endDate) {
        $endDate = now()->format('Y-m-d');
        $startDate = now()->subDays(30)->format('Y-m-d');
    }

    $dates = [];
    $current = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($endDate);
    
    while ($current <= $end) {
        $dates[] = $current->format('Y-m-d');
        $current->addDay();
    }

    // Data untuk setiap metrik
    $poSpark = [];
    $itemSpark = [];
    $amountSpark = [];
    $paidSpark = [];

    foreach ($dates as $date) {
        $query = PurchaseOrder::whereDate('tanggal', $date);
        
        if ($outletFilter !== 'all') {
            $query->where('id_outlet', $outletFilter);
        }

        $pos = $query->get();

        $poSpark[] = $pos->count();
        $itemSpark[] = $pos->sum(function($po) {
            return $po->items->sum('kuantitas');
        });
        $amountSpark[] = $pos->sum('total');
        
        // Total yang sudah dibayar (status selain draft)
        $paidSpark[] = $pos->filter(function($po) {
            return $po->status !== 'draft';
        })->sum('total');
    }

    return [
        'po_spark' => $poSpark,
        'item_spark' => $itemSpark,
        'amount_spark' => $amountSpark,
        'paid_spark' => $paidSpark
    ];
}

  
    /**
     * Calculate KPIs
     */
    private function calculateKPIs($purchaseOrders)
    {
        $totalPO = $purchaseOrders->count();
        $totalItems = $purchaseOrders->sum(function($po) {
            return $po->items->sum('kuantitas');
        });
        $totalAmount = $purchaseOrders->sum('total');
        
        // Hitung outstanding (hutang) - PO yang belum lunas
        $outstanding = $purchaseOrders->filter(function($po) {
            return $po->status !== 'dibayar' && $po->status !== 'dibatalkan';
        })->sum('total');

        return [
            'po_count' => $totalPO,
            'total_items' => $totalItems,
            'total_amount' => $totalAmount,
            'outstanding' => $outstanding
        ];
    }

    /**
     * Get Outlet Data for Bar Chart
     */
    private function getOutletData($purchaseOrders)
    {
        $outlets = \App\Models\Outlet::where('is_active', true)->get();
        
        return $outlets->map(function($outlet) use ($purchaseOrders) {
            $outletPOs = $purchaseOrders->where('id_outlet', $outlet->id_outlet);
            $totalAmount = $outletPOs->sum('total');
            
            return [
                'name' => $outlet->nama_outlet,
                'value' => $totalAmount,
                'po_count' => $outletPOs->count(),
                'item_count' => $outletPOs->sum(function($po) {
                    return $po->items->sum('kuantitas');
                })
            ];
        })->filter(function($data) {
            return $data['value'] > 0; // Hanya tampilkan outlet dengan transaksi
        })->values();
    }

    /**
     * Get Top Debt Suppliers
     */
    private function getTopDebtSuppliers($purchaseOrders)
    {
        $supplierDebts = [];
        
        foreach ($purchaseOrders as $po) {
            if ($po->status === 'dibatalkan') continue;
            
            $supplierId = $po->id_supplier;
            $supplierName = $po->supplier->nama_supplier ?? 'Unknown Supplier';
            $outletName = $po->outlet->nama_outlet ?? 'Unknown Outlet';
            
            if (!isset($supplierDebts[$supplierId])) {
                $supplierDebts[$supplierId] = [
                    'nama' => $supplierName,
                    'outlet' => $outletName,
                    'hutang' => 0
                ];
            }
            
            // Hutang = total PO - total yang sudah dibayar
            $totalPaid = $po->invoices->sum(function($invoice) {
                return $invoice->payments->where('status', 'selesai')->sum('jumlah_bayar');
            });
            
            $remaining = max(0, $po->total - $totalPaid);
            $supplierDebts[$supplierId]['hutang'] += $remaining;
        }
        
        // Filter hanya yang punya hutang dan urutkan
        return collect($supplierDebts)
            ->filter(function($debt) {
                return $debt['hutang'] > 0;
            })
            ->sortByDesc('hutang')
            ->take(5)
            ->values()
            ->toArray();
    }

    /**
     * Get Last Transactions
     */
    private function getLastTransactions($purchaseOrders)
    {
        return $purchaseOrders->take(10)->map(function($po) {
            $totalPaid = $po->invoices->sum(function($invoice) {
                return $invoice->payments->where('status', 'selesai')->sum('jumlah_bayar');
            });
            
            $totalItems = $po->items->sum('kuantitas');
            $remaining = max(0, $po->total - $totalPaid);
            
            return [
                'id' => $po->id_purchase_order,
                'tanggal' => $po->tanggal->format('Y-m-d'),
                'outlet' => $po->outlet->nama_outlet ?? 'N/A',
                'supplier' => $po->supplier->nama_supplier ?? 'N/A',
                'total_item' => $totalItems,
                'total_harga' => $po->total,
                'total_bayar' => $totalPaid,
                'sisa' => $remaining,
                'status' => $po->status,
                'no_po' => $po->no_po
            ];
        })->values()->toArray();
    }

    /**
     * Get Spark Data for Trend Charts
     */
    private function getSparkData($startDate, $endDate, $outletFilter)
    {
        // Default 30 days if no date range
        if (!$startDate || !$endDate) {
            $endDate = now()->format('Y-m-d');
            $startDate = now()->subDays(30)->format('Y-m-d');
        }

        $dates = [];
        $current = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Data untuk setiap metrik
        $poSpark = [];
        $itemSpark = [];
        $amountSpark = [];
        $debtSpark = [];

        foreach ($dates as $date) {
            $query = PurchaseOrder::whereDate('tanggal', $date);
            
            if ($outletFilter !== 'all') {
                $query->where('id_outlet', $outletFilter);
            }

            $pos = $query->get();

            $poSpark[] = $pos->count();
            $itemSpark[] = $pos->sum(function($po) {
                return $po->items->sum('kuantitas');
            });
            $amountSpark[] = $pos->sum('total');
            
            // Outstanding untuk hari itu
            $debtSpark[] = $pos->filter(function($po) {
                return $po->status !== 'dibayar' && $po->status !== 'dibatalkan';
            })->sum('total');
        }

        return [
            'po_spark' => $poSpark,
            'item_spark' => $itemSpark,
            'amount_spark' => $amountSpark,
            'debt_spark' => $debtSpark
        ];
    }

    /**
     * Get Periode Label
     */
    private function getPeriodeLabel($startDate, $endDate)
    {
        if (!$startDate && !$endDate) {
            return '30 hari terakhir';
        }

        $start = $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'awal';
        $end = $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'akhir';

        return "{$start} — {$end}";
    }

    /**
     * Get Outlets for Filter
     */
    public function getDashboardOutlets()
    {
        try {
            $outlets = \App\Models\Outlet::where('is_active', true)
                ->select('id_outlet', 'nama_outlet')
                ->orderBy('nama_outlet')
                ->get();

            return response()->json([
                'success' => true,
                'outlets' => $outlets
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading outlets for dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data outlet'
            ], 500);
        }
    }

    public function previewCoaJournal(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $status = $request->get('status', 'draft');
            
            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 422);
            }

            $setting = SettingCOAPurchase::where('id_outlet', $outletId)->first();
            
            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting COA belum diatur untuk outlet ini'
                ], 404);
            }

            $previewData = $this->generateJournalPreview($setting, $status);
            
            return response()->json([
                'success' => true,
                'preview' => $previewData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating COA journal preview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate preview jurnal'
            ], 500);
        }
    }

    
    private function getDraftJournalEntries($setting)
    {
        $entries = [];
        
        // Contoh: Tidak ada jurnal untuk status draft
        // Biasanya jurnal dibuat saat barang diterima atau dibayar
        
        return $entries;
    }

    /**
     * Jurnal untuk status Dibayar (PO sudah dibayar)
     */
    private function getPaidJournalEntries($setting)
    {
        $entries = [];
        $totalAmount = 10000000; // Contoh nominal

        // Hutang Usaha (Kredit) - Hutang berkurang karena sudah dibayar
        if ($setting->akun_hutang_usaha) {
            $hutangAccount = ChartOfAccount::where('code', $setting->akun_hutang_usaha)->first();
            if ($hutangAccount) {
                $entries[] = [
                    'account_code' => $hutangAccount->code,
                    'account_name' => $hutangAccount->name,
                    'account_type' => $hutangAccount->type,
                    'debit' => 0,
                    'credit' => $totalAmount,
                    'position' => 'Kredit',
                    'description' => 'Pembayaran hutang usaha'
                ];
            }
        }

        // Kas/Bank (Debit) - Kas/Bank berkurang
        if ($setting->akun_kas) {
            $kasAccount = ChartOfAccount::where('code', $setting->akun_kas)->first();
            if ($kasAccount) {
                $entries[] = [
                    'account_code' => $kasAccount->code,
                    'account_name' => $kasAccount->name,
                    'account_type' => $kasAccount->type,
                    'debit' => $totalAmount,
                    'credit' => 0,
                    'position' => 'Debit',
                    'description' => 'Kas keluar untuk pembayaran PO'
                ];
            }
        }

        return $entries;
    }

    /**
     * Jurnal untuk status Diterima (Barang diterima)
     */
    private function getReceivedJournalEntries($setting)
    {
        $entries = [];
        $totalAmount = 10000000; // Contoh nominal
        $ppnAmount = $totalAmount * 0.11; // Contoh PPN 11%
        $subtotal = $totalAmount - $ppnAmount;

        // Persediaan (Debit) - Persediaan bertambah
        if ($setting->akun_persediaan) {
            $persediaanAccount = ChartOfAccount::where('code', $setting->akun_persediaan)->first();
            if ($persediaanAccount) {
                $entries[] = [
                    'account_code' => $persediaanAccount->code,
                    'account_name' => $persediaanAccount->name,
                    'account_type' => $persediaanAccount->type,
                    'debit' => $subtotal,
                    'credit' => 0,
                    'position' => 'Debit',
                    'description' => 'Penerimaan persediaan barang'
                ];
            }
        }

        // PPN Masukan (Debit) - PPN masukan bertambah
        if ($setting->akun_ppn_masukan && $ppnAmount > 0) {
            $ppnAccount = ChartOfAccount::where('code', $setting->akun_ppn_masukan)->first();
            if ($ppnAccount) {
                $entries[] = [
                    'account_code' => $ppnAccount->code,
                    'account_name' => $ppnAccount->name,
                    'account_type' => $ppnAccount->type,
                    'debit' => $ppnAmount,
                    'credit' => 0,
                    'position' => 'Debit',
                    'description' => 'PPN masukan dari pembelian'
                ];
            }
        }

        // Hutang Usaha (Kredit) - Hutang bertambah
        if ($setting->akun_hutang_usaha) {
            $hutangAccount = ChartOfAccount::where('code', $setting->akun_hutang_usaha)->first();
            if ($hutangAccount) {
                $entries[] = [
                    'account_code' => $hutangAccount->code,
                    'account_name' => $hutangAccount->name,
                    'account_type' => $hutangAccount->type,
                    'debit' => 0,
                    'credit' => $totalAmount,
                    'position' => 'Kredit',
                    'description' => 'Hutang usaha dari pembelian'
                ];
            }
        }

        return $entries;
    }

    /**
 * Generate preview jurnal berdasarkan status alur baru
 */
private function generateJournalPreview($setting, $status)
{
    $previewData = [
        'status' => $status,
        'description' => '',
        'total' => 10000000, // Contoh nominal untuk preview
        'hpp_amount' => 0,
        'is_balanced' => false,
        'entries' => []
    ];

    switch ($status) {
        case 'permintaan_pembelian':
            $previewData['description'] = 'Jurnal saat Permintaan Pembelian dibuat';
            $previewData['entries'] = $this->getPermintaanPembelianJournalEntries($setting);
            break;
            
        case 'request_quotation':
            $previewData['description'] = 'Jurnal saat Request Quotation';
            $previewData['entries'] = $this->getRequestQuotationJournalEntries($setting);
            break;
            
        case 'purchase_order':
            $previewData['description'] = 'Jurnal saat Purchase Order dibuat';
            $previewData['entries'] = $this->getPurchaseOrderJournalEntries($setting);
            break;
            
        case 'penerimaan_barang':
            $previewData['description'] = 'Jurnal saat Penerimaan Barang';
            $previewData['entries'] = $this->getPenerimaanBarangJournalEntries($setting);
            break;
            
        case 'vendor_bill':
            $previewData['description'] = 'Jurnal saat Vendor/Supplier Bill';
            $previewData['entries'] = $this->getVendorBillJournalEntries($setting);
            break;
            
        case 'payment':
            $previewData['description'] = 'Jurnal saat Pembayaran ke Vendor';
            $previewData['entries'] = $this->getPaymentJournalEntries($setting);
            break;
            
        default:
            $previewData['description'] = 'Jurnal untuk status: ' . $status;
            $previewData['entries'] = [];
    }

    // Hitung balance
    $totalDebit = collect($previewData['entries'])->sum('debit');
    $totalCredit = collect($previewData['entries'])->sum('credit');
    $previewData['is_balanced'] = ($totalDebit == $totalCredit);
    $previewData['total'] = $totalDebit;

    return $previewData;
}

/**
 * Jurnal untuk Permintaan Pembelian (Tidak ada jurnal akuntansi)
 */
private function getPermintaanPembelianJournalEntries($setting)
{
    $entries = [];
    // Permintaan pembelian hanya dokumen internal, tidak ada jurnal
    return $entries;
}

/**
 * Jurnal untuk Request Quotation (Tidak ada jurnal akuntansi)
 */
private function getRequestQuotationJournalEntries($setting)
{
    $entries = [];
    // Request quotation hanya proses penawaran, tidak ada jurnal
    return $entries;
}

/**
 * Jurnal untuk Purchase Order (Komitmen pembelian)
 */
private function getPurchaseOrderJournalEntries($setting)
{
    $entries = [];
    $totalAmount = 10000000;

    // PO biasanya hanya komitmen, tidak langsung mempengaruhi jurnal
    // Tapi bisa dicatat sebagai komitmen anggaran jika diperlukan
    
    return $entries;
}

private function getPenerimaanBarangJournalEntries($setting)
{
    $entries = [];
    $totalAmount = 10000000;

    // Persediaan (Debit) - Persediaan bertambah
    if ($setting->akun_persediaan) {
        $persediaanAccount = ChartOfAccount::where('code', $setting->akun_persediaan)->first();
        if ($persediaanAccount) {
            $entries[] = [
                'account_code' => $persediaanAccount->code,
                'account_name' => $persediaanAccount->name,
                'account_type' => $persediaanAccount->type,
                'debit' => $totalAmount,
                'credit' => 0,
                'position' => 'Debit',
                'description' => 'Penerimaan persediaan barang'
            ];
        }
    }

    // Hutang Sementara (Kredit) - Hutang sementara bertambah
    if ($setting->akun_hutang_sementara) {
        $hutangSementaraAccount = ChartOfAccount::where('code', $setting->akun_hutang_sementara)->first();
        if ($hutangSementaraAccount) {
            $entries[] = [
                'account_code' => $hutangSementaraAccount->code,
                'account_name' => $hutangSementaraAccount->name,
                'account_type' => $hutangSementaraAccount->type,
                'debit' => 0,
                'credit' => $totalAmount,
                'position' => 'Kredit',
                'description' => 'Hutang sementara dari penerimaan barang'
            ];
        }
    }

    return $entries;
}

private function getVendorBillJournalEntries($setting)
{
    $entries = [];
    $totalAmount = 10000000;
    $ppnAmount = $totalAmount * 0.11; // Contoh PPN 11%
    $subtotal = $totalAmount - $ppnAmount;

    // Hutang Sementara (Debit) - Hutang sementara dikurangi
    if ($setting->akun_hutang_sementara) {
        $hutangSementaraAccount = ChartOfAccount::where('code', $setting->akun_hutang_sementara)->first();
        if ($hutangSementaraAccount) {
            $entries[] = [
                'account_code' => $hutangSementaraAccount->code,
                'account_name' => $hutangSementaraAccount->name,
                'account_type' => $hutangSementaraAccount->type,
                'debit' => $totalAmount,
                'credit' => 0,
                'position' => 'Debit',
                'description' => 'Pembebanan hutang sementara'
            ];
        }
    }

    // PPN Masukan (Debit) - PPN masukan bertambah
    if ($setting->akun_ppn_masukan && $ppnAmount > 0) {
        $ppnAccount = ChartOfAccount::where('code', $setting->akun_ppn_masukan)->first();
        if ($ppnAccount) {
            $entries[] = [
                'account_code' => $ppnAccount->code,
                'account_name' => $ppnAccount->name,
                'account_type' => $ppnAccount->type,
                'debit' => $ppnAmount,
                'credit' => 0,
                'position' => 'Debit',
                'description' => 'PPN masukan dari faktur supplier'
            ];
        }
    }

    // Hutang Usaha (Kredit) - Hutang usaha bertambah
    if ($setting->akun_hutang_usaha) {
        $hutangAccount = ChartOfAccount::where('code', $setting->akun_hutang_usaha)->first();
        if ($hutangAccount) {
            $entries[] = [
                'account_code' => $hutangAccount->code,
                'account_name' => $hutangAccount->name,
                'account_type' => $hutangAccount->type,
                'debit' => 0,
                'credit' => $totalAmount + $ppnAmount,
                'position' => 'Kredit',
                'description' => 'Hutang usaha dari faktur supplier (termasuk PPN)'
            ];
        }
    }

    return $entries;
}


private function getPaymentJournalEntries($setting)
{
    $entries = [];
    $totalAmount = 10000000;
    $ppnAmount = $totalAmount * 0.11;
    $totalPayment = $totalAmount + $ppnAmount;

    // Hutang Usaha (Debit) - Hutang usaha berkurang
    if ($setting->akun_hutang_usaha) {
        $hutangAccount = ChartOfAccount::where('code', $setting->akun_hutang_usaha)->first();
        if ($hutangAccount) {
            $entries[] = [
                'account_code' => $hutangAccount->code,
                'account_name' => $hutangAccount->name,
                'account_type' => $hutangAccount->type,
                'debit' => $totalPayment,
                'credit' => 0,
                'position' => 'Debit',
                'description' => 'Pembayaran hutang usaha (termasuk PPN)'
            ];
        }
    }

    // Kas/Bank (Kredit) - Kas/Bank berkurang
    if ($setting->akun_kas) {
        $kasAccount = ChartOfAccount::where('code', $setting->akun_kas)->first();
        if ($kasAccount) {
            $entries[] = [
                'account_code' => $kasAccount->code,
                'account_name' => $kasAccount->name,
                'account_type' => $kasAccount->type,
                'debit' => 0,
                'credit' => $totalPayment,
                'position' => 'Kredit',
                'description' => 'Kas keluar untuk pembayaran'
            ];
        }
    }

    return $entries;
}

private function getStatusText($status)
{
    $statusMap = [
        'permintaan_pembelian' => 'Permintaan Pembelian',
        'request_quotation' => 'Request Quotation',
        'purchase_order' => 'Purchase Order', 
        'penerimaan_barang' => 'Penerimaan Barang',
        'vendor_bill' => 'Vendor Bill',
        'payment' => 'Payment',
        'dibatalkan' => 'Dibatalkan'
    ];
    
    return $statusMap[$status] ?? $status;
}

/**
 * Create automatic journal for purchase order
 */
private function createAutomaticJournal($purchaseOrder, $status, $oldStatus = null)
{
    try {
        $outletId = $purchaseOrder->id_outlet;
        $setting = SettingCOAPurchase::where('id_outlet', $outletId)->first();
        
        if (!$setting) {
            \Log::info('Setting COA untuk pembelian belum diatur untuk outlet ' . $outletId . ', skip jurnal otomatis');
            return;
        }

        if (!$setting->isCompleteForStatus($status)) {
            \Log::info('Setting COA tidak lengkap untuk status ' . $status . ', skip jurnal otomatis');
            return;
        }

        $transactionDate = now();
        $journalData = $this->preparePurchaseJournalData($purchaseOrder, $status, $setting, $oldStatus);
        
        if (empty($journalData['entries'])) {
            \Log::info('Tidak ada entri jurnal untuk status: ' . $status);
            return;
        }

        $this->journalService->createAutomaticJournal(
            'pembelian',
            $purchaseOrder->id_purchase_order,
            $transactionDate,
            $journalData['description'],
            $journalData['entries'],
            $setting->accounting_book_id,
            $outletId
        );

        \Log::info("Jurnal otomatis created untuk PO {$purchaseOrder->no_po} status {$status}");

    } catch (\Exception $e) {
        \Log::error('Gagal membuat jurnal otomatis pembelian: ' . $e->getMessage(), [
            'po_id' => $purchaseOrder->id_purchase_order,
            'status' => $status,
            'outlet_id' => $outletId
        ]);
    }
}

/**
 * Prepare journal data for purchase order based on status
 */
private function preparePurchaseJournalData($purchaseOrder, $status, $setting, $oldStatus = null): array
{
    $description = "Purchase Order {$purchaseOrder->no_po} - " . strtoupper($status);
    $supplierName = $purchaseOrder->supplier ? $purchaseOrder->supplier->nama : 'Supplier';
    $entries = [];

    switch ($status) {
        case 'penerimaan_barang':
            // Jurnal saat penerimaan barang: Persediaan (D) vs Hutang Sementara (K)
            if ($setting->akun_persediaan && $setting->akun_hutang_sementara) {
                $entries = [
                    [
                        'account_id' => $this->getAccountIdByCode($setting->akun_persediaan, $purchaseOrder->id_outlet),
                        'debit' => $purchaseOrder->total,
                        'credit' => 0,
                        'memo' => 'Penerimaan persediaan dari ' . $supplierName
                    ],
                    [
                        'account_id' => $this->getAccountIdByCode($setting->akun_hutang_sementara, $purchaseOrder->id_outlet),
                        'debit' => 0,
                        'credit' => $purchaseOrder->total,
                        'memo' => 'Hutang sementara ke ' . $supplierName
                    ]
                ];
            }
            break;

        case 'vendor_bill':
            // Jurnal saat vendor bill: Hutang Sementara (D) vs Hutang Usaha & PPN Masukan (K)
            if ($setting->akun_hutang_sementara && $setting->akun_hutang_usaha && $setting->akun_ppn_masukan) {
                // Hitung PPN (contoh: 11%)
                $ppnRate = 0.11;
                $ppnAmount = $purchaseOrder->total * $ppnRate;
                $subtotal = $purchaseOrder->total - $ppnAmount;

                $entries = [
                    // Hutang Sementara berkurang (D)
                    [
                        'account_id' => $this->getAccountIdByCode($setting->akun_hutang_sementara, $purchaseOrder->id_outlet),
                        'debit' => $purchaseOrder->total,
                        'credit' => 0,
                        'memo' => 'Pembebanan hutang sementara dari ' . $supplierName
                    ],
                    // PPN Masukan bertambah (D)
                    [
                        'account_id' => $this->getAccountIdByCode($setting->akun_ppn_masukan, $purchaseOrder->id_outlet),
                        'debit' => $ppnAmount,
                        'credit' => 0,
                        'memo' => 'PPN masukan dari faktur ' . $supplierName
                    ],
                    // Hutang Usaha bertambah (K)
                    [
                        'account_id' => $this->getAccountIdByCode($setting->akun_hutang_usaha, $purchaseOrder->id_outlet),
                        'debit' => 0,
                        'credit' => $purchaseOrder->total + $ppnAmount,
                        'memo' => 'Hutang usaha ke ' . $supplierName . ' (termasuk PPN)'
                    ]
                ];
            }
            break;

        case 'payment':
            // Jurnal saat pembayaran: Hutang Usaha (D) vs Kas/Bank (K)
            if ($setting->akun_hutang_usaha && ($setting->akun_kas || $setting->akun_bank)) {
                // Tentukan akun kas/bank berdasarkan metode pembayaran
                $akunKasBank = $this->getKasBankAccount($setting, $purchaseOrder);
                
                if ($akunKasBank) {
                    $entries = [
                        [
                            'account_id' => $this->getAccountIdByCode($setting->akun_hutang_usaha, $purchaseOrder->id_outlet),
                            'debit' => $purchaseOrder->total,
                            'credit' => 0,
                            'memo' => 'Pembayaran hutang usaha ke ' . $supplierName
                        ],
                        [
                            'account_id' => $akunKasBank,
                            'debit' => 0,
                            'credit' => $purchaseOrder->total,
                            'memo' => 'Kas/Bank keluar untuk pembayaran PO ' . $purchaseOrder->no_po
                        ]
                    ];
                }
            }
            break;
    }

    return [
        'description' => $description,
        'entries' => $entries
    ];
}

/**
 * Get kas/bank account based on payment method
 */
private function getKasBankAccount($setting, $purchaseOrder)
{
    // Default ke akun kas
    $accountCode = $setting->akun_kas;
    
    // Jika ada invoice dan metode pembayaran bank, gunakan akun bank
    if ($purchaseOrder->invoices->count() > 0) {
        $invoice = $purchaseOrder->invoices->first();
        if ($invoice->metode_pembayaran === 'transfer' && $setting->akun_bank) {
            $accountCode = $setting->akun_bank;
        }
    }
    
    return $this->getAccountIdByCode($accountCode, $purchaseOrder->id_outlet);
}

/**
 * Get account ID by code and outlet
 */
private function getAccountIdByCode(string $code, int $outletId = null): int
{
    if (!$outletId) {
        $outletId = auth()->user()->outlet_id ?? 1;
    }
    
    $account = ChartOfAccount::where('code', $code)
        ->where(function($query) use ($outletId) {
            $query->where('outlet_id', $outletId)
                  ->orWhereNull('outlet_id'); // Include global accounts
        })
        ->first();
    
    if (!$account) {
        throw new \Exception("Akun dengan kode {$code} tidak ditemukan untuk outlet {$outletId}");
    }
    
    return $account->id;
}

public function purchaseInvoiceData(Request $request)
{
    try {
        $query = PurchaseInvoice::with(['purchaseOrder.supplier', 'purchaseOrder.outlet', 'payments']);
        
        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal_invoice', [$request->start_date, $request->end_date]);
        }
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_invoice_formatted', function ($row) {
                return '<span class="font-mono text-sm">' . $row->no_invoice . '</span>';
            })
            ->addColumn('tanggal_invoice_formatted', function ($row) {
                return $row->tanggal_invoice ? $row->tanggal_invoice->format('d/m/Y') : '-';
            })
            ->addColumn('supplier_name', function ($row) {
                return $row->purchaseOrder->supplier->nama ?? 'N/A';
            })
            ->addColumn('outlet_name', function ($row) {
                return $row->purchaseOrder->outlet->nama_outlet ?? 'N/A';
            })
            ->addColumn('total_formatted', function ($row) {
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = [
                    'draft' => 'secondary',
                    'dibayar' => 'success',
                    'jatuh_tempo' => 'warning',
                    'dibatalkan' => 'danger'
                ][$row->status] ?? 'secondary';
                
                $statusText = [
                    'draft' => 'Draft',
                    'dibayar' => 'Dibayar',
                    'jatuh_tempo' => 'Jatuh Tempo',
                    'dibatalkan' => 'Dibatalkan'
                ][$row->status] ?? $row->status;
                
                return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-' . $badgeClass . '-100 text-' . $badgeClass . '-800">' . $statusText . '</span>';
            })
            ->addColumn('payment_proof', function ($row) {
                $payment = $row->payments->first();
                if ($payment && $payment->bukti_transfer) {
                    return '<button onclick="showPaymentProof(\'' . asset('storage/' . $payment->bukti_transfer) . '\')" 
                            class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                        <i class="bx bx-image text-xs"></i> Lihat Bukti
                    </button>';
                }
                return '<span class="text-slate-400 text-xs">-</span>';
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="flex gap-1">';
                
                // Print button dengan nomor sesuai status
                $actions .= '<a href="' . route('pembelian.purchase-order.print', $row->id_purchase_invoice) . '" target="_blank" 
                            class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-100 text-green-700 hover:bg-green-200">
                    <i class="bx bx-printer text-xs"></i> Print
                </a>';
                
                // Upload bukti transfer untuk invoice yang belum dibayar
                if ($row->status === 'draft') {
                    $actions .= '<button onclick="uploadPaymentProof(' . $row->id_purchase_invoice . ')" 
                                class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-purple-100 text-purple-700 hover:bg-purple-200">
                        <i class="bx bx-upload text-xs"></i> Upload Bukti
                    </button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['no_invoice_formatted', 'status_badge', 'payment_proof', 'actions'])
            ->make(true);
            
    } catch (\Exception $e) {
        \Log::error('Error loading purchase invoice data: ' . $e->getMessage());
        return response()->json(['error' => 'Gagal memuat data invoice'], 500);
    }
}

public function uploadPaymentProof(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'bukti_bayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'tanggal_bayar' => 'required|date',
        'jumlah_bayar' => 'required|numeric|min:0',
        'metode_bayar' => 'required|string',
        'kode_bank' => 'nullable|string',
        'no_referensi' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::transaction(function () use ($request, $id) {
            $invoice = PurchaseInvoice::findOrFail($id);
            $buktiBayarPath = null;
            
            // Upload bukti bayar jika ada
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                $filename = 'payment_proof_' . time() . '_' . $invoice->no_invoice . '.' . $file->getClientOriginalExtension();
                $buktiBayarPath = $file->storeAs('payment_proofs', $filename, 'public');
            }
            
            // Create payment record - SESUAIKAN DENGAN KOLOM DATABASE
            $payment = PurchasePayment::create([
                'id_purchase_invoice' => $id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'metode_bayar' => $request->metode_bayar,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kode_bank' => $request->kode_bank,
                'no_referensi' => $request->no_referensi,
                'keterangan' => $request->keterangan,
                'status' => 'selesai',
                'bukti_bayar' => $buktiBayarPath // Kolom yang benar adalah bukti_bayar
            ]);
            
            // Update invoice status
            $invoice->update([
                'status' => 'dibayar',
                'tanggal_bayar' => $request->tanggal_bayar
            ]);
            
            // Update PO status jika semua invoice sudah dibayar
            $po = $invoice->purchaseOrder;
            $unpaidInvoices = $po->invoices()->where('status', '!=', 'dibayar')->count();
            if ($unpaidInvoices === 0) {
                $po->update([
                    'status' => 'payment',
                    'tanggal_payment' => $request->tanggal_bayar,
                    'metode_payment' => $request->metode_bayar
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi' . ($request->hasFile('bukti_bayar') ? ' dengan bukti bayar' : '')
        ]);

    } catch (\Exception $e) {
        \Log::error('Error uploading payment proof: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal konfirmasi pembayaran: ' . $e->getMessage()
        ], 500);
    }
}

public function getPaymentProof($id)
{
    try {
        $payment = PurchasePayment::where('id_purchase_invoice', $id)->first();
        
        if (!$payment || !$payment->bukti_bayar) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti bayar tidak ditemukan'
            ], 404);
        }
        
        $filePath = storage_path('app/public/' . $payment->bukti_bayar);
        
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File bukti bayar tidak ditemukan'
            ], 404);
        }
        
        // Return file response dengan header yang benar
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf'
        ];
        
        $mimeType = $mimeTypes[strtolower($fileExtension)] ?? 'application/octet-stream';
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error getting payment proof: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat bukti bayar: ' . $e->getMessage()
        ], 500);
    }
}

public function checkPaymentProof($id)
{
    try {
        $payment = PurchasePayment::where('id_purchase_invoice', $id)->first();
        
        $hasProof = $payment && $payment->bukti_bayar;
        $proofUrl = null;
        
        if ($hasProof) {
            $proofUrl = route('pembelian.payment.get-proof', $id);
        }
        
        return response()->json([
            'success' => true,
            'has_proof' => $hasProof,
            'proof_url' => $proofUrl
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error checking payment proof: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'has_proof' => false,
            'proof_url' => null
        ]);
    }
}

private function searchCoaAccounts($searchTerm, $type, $outletId)
{
    $query = ChartOfAccount::where('status', 'active')
        ->where('type', $type)
        ->where(function($query) use ($outletId) {
            $query->where('outlet_id', $outletId)
                  ->orWhereNull('outlet_id');
        });

    if ($searchTerm) {
        $query->where(function($q) use ($searchTerm) {
            $q->where('code', 'like', "%{$searchTerm}%")
              ->orWhere('name', 'like', "%{$searchTerm}%");
        });
    }

    // Ambil semua akun yang match
    $allAccounts = $query->orderBy('code')->get();

    // Filter: jika ada akun anak (level 2), hanya tampilkan yang level 2
    $filteredAccounts = collect();
    
    foreach ($allAccounts as $account) {
        // Cek apakah akun ini punya anak
        $hasChildren = $allAccounts->where('parent_id', $account->id)->count() > 0;
        
        if ($hasChildren) {
            // Jika punya anak, hanya ambil anak-anaknya (level 2)
            $children = $allAccounts->where('parent_id', $account->id);
            $filteredAccounts = $filteredAccounts->merge($children);
        } else {
            // Jika tidak punya anak, ambil akun induk (level 1)
            $filteredAccounts->push($account);
        }
    }

    // Hapus duplikat dan return
    return $filteredAccounts->unique('id')->values();
}

/**
 * Process PO installment payment
 */
public function processPayment(Request $request)
{
    $validator = Validator::make($request->all(), [
        'po_id' => 'required|exists:purchase_order,id_purchase_order',
        'jumlah_pembayaran' => 'required|numeric|min:0.01',
        'jenis_pembayaran' => 'required|in:cash,transfer',
        'tanggal_pembayaran' => 'required|date',
        'penerima' => 'required|string|max:100',
        'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'catatan' => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::transaction(function () use ($request) {
            $purchaseOrder = PurchaseOrder::with(['supplier', 'outlet'])->findOrFail($request->po_id);
            
            // Validate payment amount doesn't exceed remaining balance
            $sisaPembayaran = $purchaseOrder->total - $purchaseOrder->total_dibayar;
            if ($request->jumlah_pembayaran > $sisaPembayaran) {
                throw new \Exception('Jumlah pembayaran (' . number_format($request->jumlah_pembayaran, 0, ',', '.') . ') melebihi sisa pembayaran (' . number_format($sisaPembayaran, 0, ',', '.') . ')');
            }

            // Process bukti pembayaran if exists
            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPath = $this->compressAndSavePOBukti($request->file('bukti_pembayaran'), $purchaseOrder->no_po);
            }

            // Create payment history record
            POPaymentHistory::create([
                'id_purchase_order' => $purchaseOrder->id_purchase_order,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'jumlah_pembayaran' => $request->jumlah_pembayaran,
                'jenis_pembayaran' => $request->jenis_pembayaran,
                'bukti_pembayaran' => $buktiPath,
                'penerima' => $request->penerima,
                'catatan' => $request->catatan
            ]);

            // Update PO payment totals
            $purchaseOrder->total_dibayar += $request->jumlah_pembayaran;
            $purchaseOrder->sisa_pembayaran = $purchaseOrder->total - $purchaseOrder->total_dibayar;

            // Update PO status based on payment
            if ($purchaseOrder->sisa_pembayaran <= 0) {
                $purchaseOrder->status = 'payment'; // Status menjadi payment saat lunas
            } else if ($purchaseOrder->total_dibayar > 0) {
                $purchaseOrder->status = 'partial';
            }

            $purchaseOrder->save();

            // Update or create hutang record
            // Note: We use 'nama' field to track PO number since id_pembelian has FK constraint to pembelian table
            $hutang = Hutang::where('nama', 'PO-' . $purchaseOrder->no_po)
                           ->where('id_supplier', $purchaseOrder->id_supplier)
                           ->where('id_outlet', $purchaseOrder->id_outlet)
                           ->first();
            
            if (!$hutang) {
                // Create hutang if doesn't exist
                $hutang = Hutang::create([
                    'nama' => 'PO-' . $purchaseOrder->no_po,
                    'id_supplier' => $purchaseOrder->id_supplier,
                    'id_outlet' => $purchaseOrder->id_outlet,
                    'jumlah_hutang' => $purchaseOrder->total,
                    'jumlah_dibayar' => $request->jumlah_pembayaran,
                    'sisa_hutang' => $purchaseOrder->total - $request->jumlah_pembayaran,
                    'tanggal_jatuh_tempo' => $purchaseOrder->due_date,
                    'status' => ($purchaseOrder->total - $request->jumlah_pembayaran) <= 0 ? 'lunas' : 'belum_lunas'
                ]);
            } else {
                // Update existing hutang
                $hutang->jumlah_dibayar += $request->jumlah_pembayaran;
                $hutang->sisa_hutang = $hutang->jumlah_hutang - $hutang->jumlah_dibayar;
                
                // Update status if fully paid
                if ($hutang->sisa_hutang <= 0) {
                    $hutang->status = 'lunas';
                } else {
                    $hutang->status = 'belum_lunas';
                }
                
                $hutang->save();
            }

            // Create journal entry for payment
            $this->createPaymentJournal($purchaseOrder, $request->jumlah_pembayaran, $request->jenis_pembayaran);

            \Log::info('PO payment processed successfully', [
                'po_id' => $purchaseOrder->id_purchase_order,
                'jumlah_pembayaran' => $request->jumlah_pembayaran,
                'total_dibayar' => $purchaseOrder->total_dibayar,
                'sisa_pembayaran' => $purchaseOrder->sisa_pembayaran,
                'status' => $purchaseOrder->status,
                'hutang_id' => $hutang->id_hutang,
                'hutang_jumlah_dibayar' => $hutang->jumlah_dibayar,
                'hutang_sisa' => $hutang->sisa_hutang,
                'hutang_status' => $hutang->status
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error processing PO payment: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Get payment history for a PO
 */
public function getPaymentHistory($id)
{
    try {
        $purchaseOrder = PurchaseOrder::with(['paymentHistory'])->findOrFail($id);
        
        $paymentHistory = $purchaseOrder->paymentHistory->map(function ($payment) {
            return [
                'id' => $payment->id_payment,
                'tanggal_pembayaran' => $payment->tanggal_pembayaran->format('d/m/Y'),
                'jumlah_pembayaran' => $payment->jumlah_pembayaran,
                'jenis_pembayaran' => $payment->jenis_pembayaran,
                'penerima' => $payment->penerima,
                'bukti_pembayaran' => $payment->bukti_pembayaran,
                'catatan' => $payment->catatan,
                'created_at' => $payment->created_at->format('d/m/Y H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'purchase_order' => [
                    'no_po' => $purchaseOrder->no_po,
                    'total' => $purchaseOrder->total,
                    'total_dibayar' => $purchaseOrder->total_dibayar,
                    'sisa_pembayaran' => $purchaseOrder->sisa_pembayaran,
                    'status' => $purchaseOrder->status
                ],
                'payment_history' => $paymentHistory
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error getting PO payment history: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat riwayat pembayaran: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Download bukti transfer for PO payment
 */
public function downloadBuktiTransfer($id)
{
    try {
        $payment = POPaymentHistory::findOrFail($id);
        
        if (!$payment->bukti_pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti pembayaran tidak ditemukan'
            ], 404);
        }

        $filePath = storage_path('app/public/' . $payment->bukti_pembayaran);
        
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File bukti pembayaran tidak ditemukan'
            ], 404);
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = 'Bukti-Pembayaran-PO-' . $payment->id_payment . '.' . $fileExtension;

        return response()->download($filePath, $fileName);

    } catch (\Exception $e) {
        \Log::error('Error downloading PO bukti transfer: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengunduh bukti pembayaran: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Compress and save PO bukti pembayaran
 */
private function compressAndSavePOBukti($file, $poNumber)
{
    try {
        $fileName = 'bukti_po_' . str_replace('/', '-', $poNumber) . '_' . time() . '.jpg';
        $storagePath = 'po_bukti_pembayaran/' . $fileName;
        $fullPath = storage_path('app/public/' . $storagePath);
        
        // Create directory if not exists
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // If it's an image, compress it
        if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            // Read image
            $imageData = file_get_contents($file->getPathname());
            $image = imagecreatefromstring($imageData);
            
            if ($image === false) {
                throw new \Exception('Failed to create image from file');
            }

            // Get original dimensions
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Calculate new dimensions (max 1200x1200)
            $maxWidth = 1200;
            $maxHeight = 1200;
            
            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                $newWidth = (int)($originalWidth * $ratio);
                $newHeight = (int)($originalHeight * $ratio);
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }

            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Handle transparency for PNG
            if ($file->getClientOriginalExtension() === 'png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefill($newImage, 0, 0, $transparent);
            }

            // Resize image
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Save as JPEG with 80% quality
            imagejpeg($newImage, $fullPath, 80);

            // Clean up memory
            imagedestroy($image);
            imagedestroy($newImage);

            \Log::info('PO bukti image compressed', [
                'original_size' => filesize($file->getPathname()),
                'compressed_size' => filesize($fullPath),
                'original_dimensions' => $originalWidth . 'x' . $originalHeight,
                'new_dimensions' => $newWidth . 'x' . $newHeight
            ]);
        } else {
            // For PDF files, just move without compression
            $file->move(dirname($fullPath), basename($fullPath));
        }

        return $storagePath;

    } catch (\Exception $e) {
        \Log::error('Error compressing PO bukti: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Create journal entry for PO payment
 */
private function createPaymentJournal($purchaseOrder, $jumlahPembayaran, $jenisPembayaran)
{
    try {
        $outletId = $purchaseOrder->id_outlet;
        $setting = SettingCOAPurchase::where('id_outlet', $outletId)->first();
        
        if (!$setting || !$setting->accounting_book_id) {
            \Log::info('Setting COA untuk pembelian belum diatur untuk outlet ' . $outletId . ', skip jurnal pembayaran');
            return;
        }

        // Determine which cash/bank account to use
        $cashAccountCode = $jenisPembayaran === 'cash' ? $setting->akun_kas : $setting->akun_bank;
        
        if (!$cashAccountCode || !$setting->akun_hutang_usaha) {
            \Log::info('Setting COA tidak lengkap untuk pembayaran, skip jurnal');
            return;
        }

        $supplierName = $purchaseOrder->supplier ? $purchaseOrder->supplier->nama : 'Supplier';
        
        // Build description with invoice number if available
        $invoiceNumber = $purchaseOrder->no_vendor_bill ?? null;
        if ($invoiceNumber) {
            $description = "Pembayaran Invoice {$invoiceNumber} dari PO {$purchaseOrder->no_po} - {$supplierName}";
        } else {
            $description = "Pembayaran PO {$purchaseOrder->no_po} - {$supplierName}";
        }
        
        $entries = [
            // Hutang Usaha berkurang (D)
            [
                'account_id' => $this->getAccountIdByCode($setting->akun_hutang_usaha, $outletId),
                'debit' => $jumlahPembayaran,
                'credit' => 0,
                'memo' => $invoiceNumber ? "Pembayaran Invoice {$invoiceNumber}" : "Pembayaran hutang ke {$supplierName}"
            ],
            // Kas/Bank berkurang (K)
            [
                'account_id' => $this->getAccountIdByCode($cashAccountCode, $outletId),
                'debit' => 0,
                'credit' => $jumlahPembayaran,
                'memo' => 'Pembayaran ' . ($jenisPembayaran === 'cash' ? 'tunai' : 'transfer') . ' ke ' . $supplierName
            ]
        ];

        $this->journalService->createAutomaticJournal(
            'pembelian_payment',
            $purchaseOrder->id_purchase_order,
            now(),
            $description,
            $entries,
            $setting->accounting_book_id,
            $outletId
        );

        \Log::info("Jurnal pembayaran created untuk PO {$purchaseOrder->no_po}");

    } catch (\Exception $e) {
        \Log::error('Gagal membuat jurnal pembayaran: ' . $e->getMessage(), [
            'po_id' => $purchaseOrder->id_purchase_order,
            'jumlah' => $jumlahPembayaran
        ]);
    }
}

}