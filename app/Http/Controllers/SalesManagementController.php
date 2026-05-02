<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\Member;
use App\Models\Produk;
use App\Models\OngkosKirim;
use App\Models\CustomerPrice;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\InvoicePaymentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use App\Exports\SalesInvoiceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\JournalEntryService;
use App\Models\Prospek;
use Illuminate\Support\Carbon;
use App\Models\Outlet;
use App\Models\InvoiceSalesCounter;
use App\Models\SettingCOASales;
use App\Models\Piutang;
use App\Traits\HasCompanySettings;

class SalesManagementController extends Controller
{
    use \App\Traits\HasOutletFilter;
    use HasCompanySettings;
    protected $journalService;

    public function __construct(JournalEntryService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index(Request $request)
    {
        $selectedOutlet = $this->getSelectedOutlet($request);
        $outlets = Outlet::where('is_active', true)->get();
        
        return view('admin.penjualan.invoice.index', compact('selectedOutlet', 'outlets'));
    }

    public function generateInvoiceCode()
    {
        try {
            $outletId = request()->get('outlet_id') ?: $this->getSelectedOutlet();
            $invoiceNumber = InvoiceSalesCounter::generateInvoiceNumber($outletId);
            
            return response()->json([
                'success' => true,
                'invoice_number' => $invoiceNumber
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating invoice code: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate kode invoice'
            ], 500);
        }
    }

    /**
     * Get Data untuk Datatable Invoice
     */
    public function invoiceData(Request $request)
    {
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search', '');
        $outletFilter = $request->get('outlet_filter', 'all');
        $page = $request->get('page', 1);

        $query = SalesInvoice::with(['member', 'user', 'items', 'outlet', 'paymentHistory']);

        // Filter status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        // Filter outlet - support both old and new format
        if ($outletFilter !== 'all') {
            $query->where('id_outlet', $outletFilter);
        }
        
        // New multi-outlet filter support
        if ($request->has('outlet_ids') && is_array($request->outlet_ids) && !empty($request->outlet_ids)) {
            $query->whereIn('id_outlet', $request->outlet_ids);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_invoice', 'like', "%{$search}%")
                  ->orWhereHas('member', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function($q) use ($search) {
                      $q->where('deskripsi', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortColumn = $request->get('sort_column', 'tanggal');
        $sortDirection = $request->get('sort_direction', 'desc');

        $columnMapping = [
            'no_invoice' => 'no_invoice',
            'tanggal' => 'tanggal',
            'customer' => 'member.nama',
            'total' => 'total',
            'status' => 'status',
            'due_date' => 'due_date'
        ];

        $sortColumn = $columnMapping[$sortColumn] ?? 'tanggal';
        
        if (str_contains($sortColumn, '.')) {
            $relations = explode('.', $sortColumn);
            $query->join('member', 'sales_invoice.id_member', '=', 'member.id_member')
                  ->orderBy($sortColumn, $sortDirection)
                  ->select('sales_invoice.*');
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        // Check if request is from DataTables or Alpine.js
        // DataTables sends 'draw' parameter
        if ($request->has('draw')) {
            // Return DataTables format
            return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_invoice_formatted', function ($row) {
                return '<span class="font-mono text-sm">' . $row->no_invoice . '</span>';
            })
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal ? $row->tanggal->format('d/m/Y') : '-';
            })
            ->addColumn('customer_name', function ($row) {
                return $row->member ? $row->member->nama : 'Customer Tidak Ditemukan';
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
            ->addColumn('status_badge', function ($row) {
                $badgeClass = [
                    'menunggu' => 'warning',
                    'lunas' => 'success',
                    'gagal' => 'danger'
                ][$row->status] ?? 'secondary';
                
                $statusText = [
                    'menunggu' => 'Menunggu',
                    'lunas' => 'Lunas',
                    'gagal' => 'Retur/Gagal'
                ][$row->status] ?? $row->status;

                return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-' . $badgeClass . '-100 text-' . $badgeClass . '-800">' . $statusText . '</span>';
            })
            ->addColumn('due_date_formatted', function ($row) {
                if (!$row->due_date) return '-';
                return $row->due_date->format('d/m/Y');
            })
            ->addColumn('sisa_hari', function ($row) {
                if ($row->status !== 'menunggu' || !$row->due_date) {
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
                    $list .= '• ' . $item->deskripsi . '<br>';
                }
                if ($row->items->count() > 2) {
                    $list .= '... dan ' . ($row->items->count() - 2) . ' item lainnya';
                }
                return $list;
            })
            ->addColumn('outlet_name', function ($row) {
                return $row->outlet ? $row->outlet->nama_outlet : '-';
            })
            ->addColumn('jenis_pembayaran', function ($row) {
                return $row->jenis_pembayaran ? ucfirst($row->jenis_pembayaran) : '-';
            })
            ->addColumn('penerima', function ($row) {
                return $row->penerima ?: '-';
            })
            ->addColumn('tanggal_pembayaran_formatted', function ($row) {
                return $row->tanggal_pembayaran ? $row->tanggal_pembayaran->format('d/m/Y') : '-';
            })
            ->addColumn('catatan_pembayaran', function ($row) {
                return $row->catatan_pembayaran ?: '-';
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="flex gap-1">';
                
                // Print button
                $actions .= '<a href="' . route('admin.penjualan.invoice.print', $row->id_sales_invoice) . '" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-green-100 text-green-700 hover:bg-green-200">
                    <i class="bx bx-printer text-xs"></i> Print
                </a>';
                
                // Edit button
                $actions .= '<button onclick="editInvoice(' . $row->id_sales_invoice . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                    <i class="bx bx-edit text-xs"></i> Edit
                </button>';
                
                // Status actions untuk invoice menunggu
                if ($row->status === 'menunggu') {
                    $actions .= '<button onclick="updateInvoiceStatus(' . $row->id_sales_invoice . ', \'lunas\')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                        <i class="bx bx-check text-xs"></i> Lunas
                    </button>';
                    $actions .= '<button onclick="updateInvoiceStatus(' . $row->id_sales_invoice . ', \'gagal\')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-red-100 text-red-700 hover:bg-red-200">
                        <i class="bx bx-x text-xs"></i> Retur
                    </button>';
                }
                
                // Delete button
                $actions .= '<button onclick="deleteInvoice(' . $row->id_sales_invoice . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-red-100 text-red-700 hover:bg-red-200">
                    <i class="bx bx-trash text-xs"></i> Hapus
                </button>';
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['no_invoice_formatted', 'status_badge', 'sisa_hari', 'items_list', 'actions'])
            ->make(true);
        }

        // Return JSON format for Alpine.js
        $invoices = $query->paginate(100); // Increased from 20 to 100 to show more invoices
        
        return response()->json([
            'data' => $invoices->map(function($invoice) {
                return [
                    'id_sales_invoice' => $invoice->id_sales_invoice,
                    'no_invoice' => $invoice->no_invoice,
                    'tanggal' => $invoice->tanggal ? $invoice->tanggal->format('Y-m-d') : null,
                    'customer_name' => $invoice->member ? $invoice->member->nama : 'Customer Tidak Ditemukan',
                    'outlet_name' => $invoice->outlet ? $invoice->outlet->nama_outlet : '-',
                    'subtotal' => $invoice->subtotal,
                    'total_diskon' => $invoice->total_diskon,
                    'total' => $invoice->total,
                    'total_dibayar' => $invoice->total_dibayar ?? 0,
                    'sisa_tagihan' => $invoice->sisa_tagihan ?? $invoice->total,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                    'items' => $invoice->items->map(function($item) {
                        return [
                            'id_sales_invoice_item' => $item->id_sales_invoice_item,
                            'deskripsi' => $item->deskripsi,
                            'keterangan' => $item->keterangan,
                            'kuantitas' => $item->kuantitas,
                            'satuan' => $item->satuan,
                            'harga_normal' => $item->harga_normal,
                            'diskon' => $item->diskon,
                            'subtotal' => $item->subtotal,
                        ];
                    }),
                ];
            }),
            'current_page' => $invoices->currentPage(),
            'last_page' => $invoices->lastPage(),
            'total' => $invoices->total(),
            'per_page' => $invoices->perPage(),
        ]);
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
            'due_date' => 'required|date|after_or_equal:tanggal',
            'customer_type' => 'required|in:member,prospek',
            'customer_id' => 'required',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'items' => 'required|array|min:1',
            'items.*.deskripsi' => 'required|string|max:255',
            'items.*.keterangan' => 'nullable|string|max:255',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.harga_khusus' => 'nullable|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.tipe' => 'required|in:produk,ongkir,lainnya',
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
                
                // Calculate totals first
                $subtotal = 0;
                $totalDiskon = 0;
                $total_item = 0;
                
                foreach ($request->items as $item) {
                    $itemSubtotal = $parseNumber($item['subtotal']);
                    $itemDiskon = $parseNumber($item['diskon']) * $parseNumber($item['kuantitas']);
                    
                    $subtotal += $itemSubtotal;
                    $totalDiskon += $itemDiskon;
                    
                    if ($item['tipe'] === 'produk') {
                        $total_item += $item['kuantitas'];
                    }
                }
                
                $total = $subtotal - $totalDiskon;

                $tanggalWithTime = \Carbon\Carbon::parse($request->tanggal . ' ' . now()->format('H:i:s'));
                $dueDate = \Carbon\Carbon::parse($request->due_date . ' ' . now()->format('H:i:s'));

                // Validasi Customer
                $customerId = $request->customer_id;
                $customerType = $request->customer_type;
                
                \Log::info('Validating customer', ['customer_type' => $customerType, 'customer_id' => $customerId]);
                
                if ($customerType === 'member') {
                    $customer = \App\Models\Member::find($customerId);
                    if (!$customer) {
                        throw new \Exception("Member dengan ID {$customerId} tidak ditemukan");
                    }
                    \Log::info('Member found', ['member' => $customer->nama]);
                } else {
                    $customer = \App\Models\Prospek::find($customerId);
                    if (!$customer) {
                        throw new \Exception("Prospek dengan ID {$customerId} tidak ditemukan");
                    }
                    \Log::info('Prospek found', ['prospek' => $customer->nama]);
                }

                $idOngkir = null;
                foreach ($request->items as $item) {
                    if ($item['tipe'] === 'ongkir' && !empty($item['id_ongkir'])) {
                        $idOngkir = $item['id_ongkir'];
                        break;
                    }
                }

                // 1. Create Sales Invoice as DRAFT (unique draft number with timestamp)
            $invoiceData = [
                'no_invoice' => 'DRAFT-' . time() . '-' . uniqid(), // Unique draft number
                'tanggal' => $tanggalWithTime,
                'id_member' => $customerType === 'member' ? $customerId : null,
                'id_prospek' => $customerType === 'prospek' ? $customerId : null,
                'id_outlet' => $request->id_outlet,
                'id_ongkir' => $idOngkir,
                'id_customer_price' => $request->id_customer_price ?? null,
                'id_user' => auth()->user()->id ?? null,
                'subtotal' => $subtotal,
                'total_diskon' => $totalDiskon,
                'total' => $total,
                'total_dibayar' => 0, // Initialize to 0
                'sisa_tagihan' => $total, // Initialize to total
                'status' => 'draft', // Start as draft
                'due_date' => $dueDate,
                'keterangan' => $request->keterangan
            ];
            
            $invoice = SalesInvoice::create($invoiceData);
            \Log::info('Invoice created as draft', ['invoice_id' => $invoice->id_sales_invoice]);

            // 2. Create Penjualan dengan diskon
            $penjualanData = [
                'id_member' => $customerType === 'member' ? $customerId : null,
                'id_outlet' => $request->id_outlet,
                'total_item' => $total_item,
                'total_harga' => $total,
                'total_diskon' => $totalDiskon,
                'diskon' => 0, // diskon lama, tetap untuk kompatibilitas
                'bayar' => 0,
                'diterima' => 0,
                'id_user' => auth()->user()->id ?? null,
                'created_at' => $tanggalWithTime,
                'updated_at' => $tanggalWithTime
            ];
            
            $penjualan = \App\Models\Penjualan::create($penjualanData);

                // 3. Update invoice dengan id_penjualan
                \Log::info('Updating invoice with penjualan_id', [
                    'invoice_id' => $invoice->id_sales_invoice, 
                    'penjualan_id' => $penjualan->id_penjualan
                ]);
                $invoice->update(['id_penjualan' => $penjualan->id_penjualan]);

                // 4. Piutang will be created when invoice is confirmed (not in draft)
                \Log::info('Skipping piutang creation - invoice is draft');

                // 5. Create Sales Invoice Items (NO stock reduction for draft)
                \Log::info('Creating invoice items', ['item_count' => count($request->items)]);
                foreach ($request->items as $index => $item) {
                    $id_produk = !empty($item['id_produk']) ? $item['id_produk'] : null;
                    $kuantitas = $item['kuantitas'];
                    $tipe = $item['tipe'];
                    $harga = $parseNumber($item['harga']);
                    $harga_normal = $parseNumber($item['harga_normal']);
                    $diskon = $parseNumber($item['diskon']);
                    $subtotal = $parseNumber($item['subtotal']);

                    \Log::info("Processing item {$index}", [
                        'tipe' => $tipe,
                        'id_produk' => $id_produk,
                        'kuantitas' => $kuantitas
                    ]);

                    // Stock reduction and penjualan detail will be created when invoice is confirmed
                    \Log::info('Skipping stock reduction - invoice is draft');

                    $invoiceItemData = [
                        'id_sales_invoice' => $invoice->id_sales_invoice,
                        'id_produk' => $id_produk,
                        'deskripsi' => $item['deskripsi'],
                        'keterangan' => $item['keterangan'] ?? null,
                        'kuantitas' => $item['kuantitas'],
                        'satuan' => $item['satuan'] ?? null,
                        'harga_normal' => $harga_normal,
                        'harga' => $harga, // harga setelah diskon
                        'diskon' => $diskon,
                        'subtotal' => $subtotal, // subtotal sebelum diskon
                        'tipe' => $item['tipe'],
                    ];
                    
                    SalesInvoiceItem::create($invoiceItemData);
                }

                \Log::info('Invoice draft creation completed successfully', [
                    'invoice_id' => $invoice->id_sales_invoice,
                    'penjualan_id' => $penjualan->id_penjualan,
                    'status' => 'draft'
                ]);

                // Journal will be created when invoice is confirmed
                \Log::info('Skipping journal creation - invoice is draft');
            });

            \Log::info('Invoice store transaction committed successfully');

            return response()->json([
                'success' => true, 
                'message' => 'Invoice penjualan berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            \Log::error('Sales invoice store error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Invoice Detail
     */
    public function show($id)
    {
        try {
            $invoice = SalesInvoice::with([
                'member', 
                'prospek', 
                'ongkosKirim', // Load ongkos kirim dari sales_invoice
                'items' => function($query) {
                    $query->with(['produk' => function($q) {
                        $q->with('satuan');
                    }]); // Hapus ongkosKirim dari sini
                }, 
                'penjualan.outlet'
            ])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update Invoice
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
            'due_date' => 'required|date|after_or_equal:tanggal',
            'customer_type' => 'required|in:member,prospek',
            'customer_id' => 'required',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'items' => 'required|array|min:1',
            'items.*.deskripsi' => 'required|string|max:255',
            'items.*.keterangan' => 'nullable|string|max:255',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.harga_normal' => 'required|numeric|min:0',
            'items.*.diskon' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.tipe' => 'required|in:produk,ongkir,lainnya',
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
                \Log::info('Starting invoice update transaction', ['invoice_id' => $id, 'request_data' => $request->all()]);

                $invoice = SalesInvoice::findOrFail($id);
                
                // Only allow edit if status is draft
                if ($invoice->status !== 'draft') {
                    throw new \Exception('Hanya invoice dengan status draft yang bisa diedit');
                }
                
                // Hitung totals dengan diskon
                $subtotal = 0;
                $totalDiskon = 0;
                $total_item = 0;
                
                foreach ($request->items as $item) {
                    $itemSubtotal = $parseNumber($item['subtotal']);
                    $itemDiskon = $parseNumber($item['diskon']) * $parseNumber($item['kuantitas']);
                    
                    $subtotal += $itemSubtotal;
                    $totalDiskon += $itemDiskon;
                    
                    if ($item['tipe'] === 'produk') {
                        $total_item += $item['kuantitas'];
                    }
                }
                
                $total = $subtotal - $totalDiskon;

                // Validasi Customer
                $customerId = $request->customer_id;
                $customerType = $request->customer_type;
                
                if ($customerType === 'member') {
                    $customer = \App\Models\Member::find($customerId);
                    if (!$customer) {
                        throw new \Exception("Member dengan ID {$customerId} tidak ditemukan");
                    }
                } else {
                    $customer = \App\Models\Prospek::find($customerId);
                    if (!$customer) {
                        throw new \Exception("Prospek dengan ID {$customerId} tidak ditemukan");
                    }
                }

                // Update Sales Invoice
                $invoiceData = [
                    'tanggal' => \Carbon\Carbon::parse($request->tanggal . ' ' . now()->format('H:i:s')),
                    'due_date' => \Carbon\Carbon::parse($request->due_date . ' ' . now()->format('H:i:s')),
                    'id_member' => $customerType === 'member' ? $customerId : null,
                    'id_prospek' => $customerType === 'prospek' ? $customerId : null,
                    'id_outlet' => $request->id_outlet,
                    'subtotal' => $subtotal,
                    'total_diskon' => $totalDiskon,
                    'total' => $total,
                    'keterangan' => $request->keterangan
                ];
                
                $invoice->update($invoiceData);

                // Update Penjualan
                if ($invoice->penjualan) {
                    $invoice->penjualan->update([
                        'id_member' => $customerType === 'member' ? $customerId : null,
                        'id_outlet' => $request->id_outlet,
                        'total_item' => $total_item,
                        'total_harga' => $total,
                        'total_diskon' => $totalDiskon,
                        'updated_at' => now()
                    ]);
                }

                // Piutang will only exist if invoice is confirmed (not draft)
                // So no need to update piutang here since we only allow editing draft
                \Log::info('Skipping piutang update - invoice is draft');

                // Hapus items lama dan buat yang baru
                SalesInvoiceItem::where('id_sales_invoice', $id)->delete();

                // Create Sales Invoice Items baru
                foreach ($request->items as $index => $item) {
                    $id_produk = !empty($item['id_produk']) ? $item['id_produk'] : null;
                    $kuantitas = $item['kuantitas'];
                    $tipe = $item['tipe'];
                    $harga = $parseNumber($item['harga']);
                    $harga_normal = $parseNumber($item['harga_normal']);
                    $diskon = $parseNumber($item['diskon']);
                    $subtotal = $parseNumber($item['subtotal']);

                    // Stock adjustment not needed for draft edit
                    // Stock will be reduced when invoice is confirmed
                    \Log::info('Skipping stock adjustment - invoice is draft');

                    // Create Sales Invoice Item
                    $invoiceItemData = [
                        'id_sales_invoice' => $invoice->id_sales_invoice,
                        'id_produk' => $id_produk,
                        'deskripsi' => $item['deskripsi'],
                        'keterangan' => $item['keterangan'] ?? null,
                        'kuantitas' => $item['kuantitas'],
                        'satuan' => $item['satuan'] ?? null,
                        'harga_normal' => $harga_normal,
                        'harga' => $harga,
                        'diskon' => $diskon,
                        'subtotal' => $subtotal,
                        'tipe' => $item['tipe'],
                    ];
                    
                    SalesInvoiceItem::create($invoiceItemData);
                }

                \Log::info('Invoice update completed successfully', [
                    'invoice_id' => $invoice->id_sales_invoice,
                    'subtotal' => $subtotal,
                    'total_diskon' => $totalDiskon,
                    'total' => $total
                ]);
            });

            return response()->json([
                'success' => true, 
                'message' => 'Invoice penjualan berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            \Log::error('Sales invoice update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete Invoice
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $invoice = SalesInvoice::findOrFail($id);
                
                if ($invoice->status !== 'gagal') {
                    $this->cancelInvoice($id);
                }
                
                SalesInvoiceItem::where('id_sales_invoice', $id)->delete();
                $invoice->delete();
            });
            
            return response()->json(['success' => true, 'message' => 'Invoice berhasil dihapus']);
            
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export PDF
     */
    public function invoiceExportPdf(Request $request)
    {
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $outletFilter = $request->get('outlet_filter', 'all');

        $query = SalesInvoice::with(['member', 'items', 'penjualan.outlet']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if ($outletFilter !== 'all') {
            $query->whereHas('penjualan', function($q) use ($outletFilter) {
                $q->where('id_outlet', $outletFilter);
            });
        }

        $invoices = $query->orderBy('tanggal', 'desc')->get();

        $pdf = Pdf::loadView('admin.penjualan.invoice.export_pdf', compact('invoices', 'status', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'laporan-invoice-penjualan-' . date('Y-m-d') . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Export Excel
     */
    public function invoiceExportExcel(Request $request)
    {
        return Excel::download(new SalesInvoiceExport($request), 'invoice-penjualan-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Download Template Excel
     */
    public function invoiceDownloadTemplate()
    {
        return Excel::download(new SalesInvoiceTemplateExport(), 'template-import-invoice.xlsx');
    }

    /**
     * Import Excel
     */
    public function invoiceImportExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            // Implement import logic here
            return response()->json(['message' => 'Data berhasil diimport'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimport data: ' . $e->getMessage()], 500);
        }
    }

    public function invoiceStatusCounts(Request $request)
    {
        $outletId = $request->get('outlet_id') ?: $this->getSelectedOutlet($request);
        
        $counts = [
            'all' => SalesInvoice::where('id_outlet', $outletId)->count(),
            'draft' => SalesInvoice::where('status', 'draft')->where('id_outlet', $outletId)->count(),
            'menunggu' => SalesInvoice::where('status', 'menunggu')->where('id_outlet', $outletId)->count(),
            'dibayar_sebagian' => SalesInvoice::where('status', 'dibayar_sebagian')->where('id_outlet', $outletId)->count(),
            'lunas' => SalesInvoice::where('status', 'lunas')->where('id_outlet', $outletId)->count(),
            'gagal' => SalesInvoice::where('status', 'gagal')->where('id_outlet', $outletId)->count(),
        ];
        
        return response()->json($counts);
    }

    /**
     * Get Due Soon Invoices
     */
    public function invoiceDueSoon()
    {
        $tomorrow = now()->addDay();
        
        $invoices = SalesInvoice::with('member')
            ->where('status', 'menunggu')
            ->where('due_date', '<=', $tomorrow)
            ->get()
            ->map(function($invoice) {
                $dueDate = $invoice->due_date;
                $remainingHours = now()->diffInHours($dueDate, false);
                
                $timeDescription = '';
                if ($remainingHours < 0) {
                    $hoursLate = abs($remainingHours);
                    if ($hoursLate < 24) {
                        $timeDescription = 'Terlambat ' . $hoursLate . ' jam';
                    } else {
                        $daysLate = floor($hoursLate / 24);
                        $hoursRemaining = $hoursLate % 24;
                        $timeDescription = 'Terlambat ' . $daysLate . ' hari ' . $hoursRemaining . ' jam';
                    }
                } else {
                    if ($remainingHours < 24) {
                        $timeDescription = 'Sisa ' . $remainingHours . ' jam';
                    } else {
                        $daysRemaining = floor($remainingHours / 24);
                        $hoursRemaining = $remainingHours % 24;
                        $timeDescription = 'Sisa ' . $daysRemaining . ' hari ' . $hoursRemaining . ' jam';
                    }
                }
                
                return [
                    'id_sales_invoice' => $invoice->id_sales_invoice,
                    'no_invoice' => $invoice->no_invoice,
                    'member' => $invoice->member,
                    'total' => $invoice->total,
                    'due_date' => $invoice->due_date,
                    'remaining_hours' => $remainingHours,
                    'time_description' => $timeDescription
                ];
            });
        
        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    }

    public function invoicePrint($id)
    {
        $companySettings = $this->getCompanySettingsForPrint();
        try {
            $invoice = SalesInvoice::with(['member', 'prospek', 'items', 'outlet'])->findOrFail($id);
            $setting = DB::table('setting')->first();
            
            // Get company bank accounts berdasarkan outlet invoice
            $outletId = $invoice->id_outlet ?? 1; // Fallback to outlet 1 if not set
            
            \Log::info('Loading bank accounts for invoice print', [
                'invoice_id' => $id,
                'outlet_id' => $outletId
            ]);
            
            // Try to get all bank accounts first to debug
            $allBankAccounts = \App\Models\CompanyBankAccount::all();
            \Log::info('All bank accounts in database', [
                'total_count' => $allBankAccounts->count(),
                'sample' => $allBankAccounts->take(2)->map(function($acc) {
                    return [
                        'id' => $acc->id_company_bank_account,
                        'outlet_id_value' => $acc->id_outlet ?? 'NULL',
                        'bank' => $acc->bank_name,
                        'active' => $acc->is_active
                    ];
                })->toArray()
            ]);
            
            // Try to get bank accounts for specific outlet
            $bankAccounts = \App\Models\CompanyBankAccount::where('id_outlet', (int)$outletId)
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('bank_name')
                ->get();
            
            // If empty, try with NULL outlet_id (global accounts)
            if ($bankAccounts->count() == 0) {
                \Log::warning('No accounts for outlet, trying NULL outlet_id (global accounts)');
                $bankAccounts = \App\Models\CompanyBankAccount::whereNull('id_outlet')
                    ->where('is_active', 1)
                    ->orderBy('sort_order')
                    ->orderBy('bank_name')
                    ->get();
            }
            
            // If still empty, try without is_active filter
            if ($bankAccounts->count() == 0) {
                \Log::warning('No active accounts found, trying without is_active filter');
                $bankAccounts = \App\Models\CompanyBankAccount::where(function($query) use ($outletId) {
                    $query->where('id_outlet', (int)$outletId)
                          ->orWhereNull('id_outlet');
                })
                ->orderBy('sort_order')
                ->orderBy('bank_name')
                ->get();
            }
            
            \Log::info('Bank accounts loaded for outlet', [
                'outlet_id' => $outletId,
                'count' => $bankAccounts->count(),
                'accounts' => $bankAccounts->pluck('bank_name')->toArray(),
                'sql' => \App\Models\CompanyBankAccount::where('id_outlet', $outletId)->where('is_active', true)->toSql()
            ]);
            
            $template = request()->get('template', 'standard');
            $preview = request()->get('preview', false);
            $download = request()->get('download', false);
            
            if ($preview) {
                return view('admin.penjualan.invoice.print', compact('invoice', 'setting', 'template', 'bankAccounts', 'companySettings'));
            }
            
            $pdf = Pdf::loadView('admin.penjualan.invoice.print', compact('invoice', 'setting', 'template', 'bankAccounts', 'companySettings'));
            $pdf->setPaper('A4', 'portrait');
            
            $safeFilename = 'invoice-' . str_replace('/', '-', $invoice->no_invoice) . '.pdf';
            
            if ($download) {
                return $pdf->download($safeFilename);
            }
            
            return $pdf->stream($safeFilename);
            
        } catch (\Exception $e) {
            \Log::error('Error printing invoice: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Gagal generate PDF: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:menunggu,lunas,gagal',
            'jenis_pembayaran' => 'required_if:status,lunas|in:cash,transfer',
            'penerima' => 'required_if:status,lunas|string|max:255',
            'tanggal_pembayaran' => 'required_if:status,lunas|date',
            'catatan_pembayaran' => 'nullable|string',
            'bukti_transfer' => 'required_if:jenis_pembayaran,transfer|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'nama_bank' => 'required_if:jenis_pembayaran,transfer|string|max:100',
            'nama_pengirim' => 'required_if:jenis_pembayaran,transfer|string|max:255',
            'jumlah_transfer' => 'required_if:jenis_pembayaran,transfer|numeric|min:0'
        ], [
            'bukti_transfer.required_if' => 'Bukti transfer wajib diupload untuk pembayaran transfer',
            'nama_bank.required_if' => 'Nama bank wajib diisi untuk pembayaran transfer',
            'nama_pengirim.required_if' => 'Nama pengirim wajib diisi untuk pembayaran transfer',
            'jumlah_transfer.required_if' => 'Jumlah transfer wajib diisi untuk pembayaran transfer'
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
                $invoice = SalesInvoice::findOrFail($id);
                $oldStatus = $invoice->status;
                $newStatus = $request->status;

                $updateData = ['status' => $newStatus];

                if ($newStatus === 'lunas') {
                    $updateData['jenis_pembayaran'] = $request->jenis_pembayaran;
                    $updateData['penerima'] = $request->penerima;
                    $updateData['tanggal_pembayaran'] = $request->tanggal_pembayaran;
                    $updateData['catatan_pembayaran'] = $request->catatan_pembayaran;

                    // Handle bukti transfer untuk pembayaran transfer
                    if ($request->jenis_pembayaran === 'transfer') {
                        $updateData['nama_bank'] = $request->nama_bank;
                        $updateData['nama_pengirim'] = $request->nama_pengirim;
                        $updateData['jumlah_transfer'] = $request->jumlah_transfer;

                        // Upload bukti transfer
                        if ($request->hasFile('bukti_transfer')) {
                            $file = $request->file('bukti_transfer');
                            $fileName = 'bukti_' . time() . '_' . $invoice->no_invoice . '.' . $file->getClientOriginalExtension();
                            
                            // Simpan file ke storage
                            $Path = $file->storeAs('bukti-transfer-penjualan', $fileName, 'public');
                            $updateData['bukti_transfer'] = $fileName;
                        }
                    } else {
                        // Reset data transfer untuk pembayaran cash
                        $updateData['bukti_transfer'] = null;
                        $updateData['nama_bank'] = null;
                        $updateData['nama_pengirim'] = null;
                        $updateData['jumlah_transfer'] = null;
                    }

                    if ($invoice->penjualan && $invoice->penjualan->piutang) {
                        $invoice->penjualan->piutang->update([
                            'status' => 'lunas',
                            'updated_at' => now()
                        ]);
                    }
                    
                    if ($invoice->penjualan) {
                        $invoice->penjualan->update([
                            'bayar' => $invoice->total,
                            'diterima' => $invoice->total,
                            'updated_at' => now()
                        ]);
                    }
                } else if ($newStatus === 'gagal'){
                    $this->cancelInvoice($id);
                    $updateData['jenis_pembayaran'] = null;
                    $updateData['penerima'] = null;
                    $updateData['tanggal_pembayaran'] = null;
                    $updateData['catatan_pembayaran'] = null;
                    $updateData['bukti_transfer'] = null;
                    $updateData['nama_bank'] = null;
                    $updateData['nama_pengirim'] = null;
                    $updateData['jumlah_transfer'] = null;
                } else {
                    $updateData['jenis_pembayaran'] = null;
                    $updateData['penerima'] = null;
                    $updateData['tanggal_pembayaran'] = null;
                    $updateData['catatan_pembayaran'] = null;
                    $updateData['bukti_transfer'] = null;
                    $updateData['nama_bank'] = null;
                    $updateData['nama_pengirim'] = null;
                    $updateData['jumlah_transfer'] = null;
                }

                $invoice->update($updateData);
                
                // Panggil service baru untuk membuat jurnal
                $this->createAutomaticJournal($invoice, $newStatus, $oldStatus);
            });

            return response()->json([
                'success' => true, 
                'message' => 'Status invoice berhasil diupdate menjadi ' . $request->status
            ]);

        } catch (\Exception $e) {
            \Log::error('Update status error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel Invoice
     */
    public function cancelInvoice($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $invoice = SalesInvoice::with(['penjualan', 'penjualan.details', 'penjualan.piutang', 'items.produk'])->findOrFail($id);
                
                foreach ($invoice->items as $item) {
                    if ($item->tipe === 'produk' && $item->id_produk && $item->produk) {
                        $produk = $item->produk;
                        $kuantitas = $item->kuantitas;
                        
                        $hppRataRata = $produk->calculateHpp();
                        $produk->addStock($hppRataRata, $kuantitas);
                    }
                }
                
                $invoice->update(['status' => 'gagal']);

                if ($invoice->penjualan && $invoice->penjualan->piutang) {
                    $invoice->penjualan->piutang->delete();
                }
                
                if ($invoice->penjualan && $invoice->penjualan->details) {
                    $invoice->penjualan->details()->delete();
                }
                
                if ($invoice->penjualan) {
                    $invoice->penjualan->delete();
                }
                
                $invoice->update(['id_penjualan' => null]);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dibatalkan dan stok dikembalikan'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Cancel invoice error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function invoiceSetting(Request $request)
    {
        try {
            // Get outlet ID from request or use selected outlet
            $outletId = $request->get('outlet_id');
            
            if (!$outletId) {
                $outletId = $this->getSelectedOutlet($request);
            }
            
            // If still no outlet, get first accessible outlet
            if (!$outletId) {
                $accessibleOutlets = $this->getAccessibleOutlets();
                if ($accessibleOutlets->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke outlet manapun'
                    ], 403);
                }
                $outletId = $accessibleOutlets->first()->id_outlet;
            }
            
            // Validate outlet access
            $this->validateOutletAccess($outletId);
            
            $counter = InvoiceSalesCounter::getByOutlet($outletId);
            
            if (!$counter) {
                $counter = new InvoiceSalesCounter();
                $counter->id_outlet = $outletId;
                $counter->invoice_prefix = 'SLS.INV';
                $counter->last_number = 0;
                $counter->year = date('Y');
                $counter->save();
            }
            
            $currentNumber = $counter->last_number;
            $currentYear = $counter->year;
            
            $currentInvoiceNumber = str_pad($currentNumber, 3, '0', STR_PAD_LEFT) . '/' . $counter->invoice_prefix . '/' . InvoiceSalesCounter::getRomanMonth() . '/' . $currentYear;
            $nextNumber = $currentNumber + 1;
            $nextInvoiceNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . '/' . $counter->invoice_prefix . '/' . InvoiceSalesCounter::getRomanMonth() . '/' . $currentYear;
            
            return response()->json([
                'success' => true,
                'current_invoice_number' => $currentInvoiceNumber,
                'next_invoice_number' => $nextInvoiceNumber,
                'current_number' => $currentNumber,
                'current_year' => $currentYear,
                'invoice_prefix' => $counter->invoice_prefix,
                'outlet_id' => $outletId
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in invoiceSetting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat setting invoice'
            ], 500);
        }
    }

    /**
     * Update Invoice Setting
     */
    public function updateInvoiceSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'starting_number' => 'required|integer|min:1|max:999',
            'year' => 'required|integer|min:2020|max:2030',
            'invoice_prefix' => 'required|string|max:20',
            'outlet_id' => 'required|exists:outlets,id_outlet'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validate outlet access
            $this->validateOutletAccess($request->outlet_id);
            
            InvoiceSalesCounter::updateOrCreateForOutlet(
                $request->outlet_id,
                [
                    'last_number' => $request->starting_number - 1,
                    'year' => $request->year,
                    'invoice_prefix' => $request->invoice_prefix
                    // Hapus month dari sini
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Setting nomor invoice berhasil disimpan untuk outlet ini'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating invoice setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan setting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function coaSetting(Request $request)
    {
        try {
            // Get outlet ID from request or use selected outlet
            $outletId = $request->get('outlet_id');
            
            if (!$outletId) {
                $outletId = $this->getSelectedOutlet($request);
            }
            
            // If still no outlet, get first accessible outlet
            if (!$outletId) {
                $accessibleOutlets = $this->getAccessibleOutlets();
                if ($accessibleOutlets->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke outlet manapun'
                    ], 403);
                }
                $outletId = $accessibleOutlets->first()->id_outlet;
            }
            
            // Validate outlet access
            $this->validateOutletAccess($outletId);
            $setting = SettingCOASales::getByOutlet($outletId); // Gunakan method baru
            
            $accountingBooks = \App\Models\AccountingBook::where('outlet_id', $outletId)
                ->where('status', 'active')
                ->get();
            
            // Ambil semua akun dari database ChartOfAccount berdasarkan outlet
            $allAccounts = \App\Models\ChartOfAccount::with(['parent'])
                ->where('outlet_id', $outletId)
                ->where('status', 'active')
                ->orderBy('code')
                ->get()
                ->map(function($account) {
                    $level = $account->parent_id ? 2 : 1;
                    return [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'type' => $account->type,
                        'type_name' => $account->type_name,
                        'level' => $level,
                        'parent_id' => $account->parent_id,
                        'full_name' => $account->code . ' - ' . $account->name . ' (' . $account->type_name . ')'
                    ];
                });

            $accountTypes = [
                'asset' => 'Aset',
                'liability' => 'Kewajiban', 
                'equity' => 'Ekuitas',
                'revenue' => 'Pendapatan',
                'expense' => 'Beban',
                'otherrevenue' => 'Pendapatan Lain',
                'otherexpense' => 'Beban Lain'
            ];
            
            return response()->json([
                'success' => true,
                'setting' => $setting,
                'accounting_books' => $accountingBooks,
                'accounts' => $allAccounts,
                'account_types' => $accountTypes,
                'outlet_id' => $outletId
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in coaSetting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat setting COA: ' . $e->getMessage()
            ], 500);
        }
    }

    public function coaSettingUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'akun_piutang_usaha' => 'required',
            'akun_pendapatan_penjualan' => 'required',
            'akun_kas' => 'required',
            'akun_bank' => 'required',
            'akun_hpp' => 'required',
            'akun_persediaan' => 'required',
        ], [
            'accounting_book_id.required' => 'Buku akuntansi harus dipilih',
            'akun_piutang_usaha.required' => 'Akun piutang usaha harus dipilih',
            'akun_pendapatan_penjualan.required' => 'Akun pendapatan penjualan harus dipilih',
            'akun_kas.required' => 'Akun kas harus dipilih',
            'akun_bank.required' => 'Akun bank harus dipilih',
            'akun_hpp.required' => 'Akun HPP harus dipilih',
            'akun_persediaan.required' => 'Akun persediaan harus dipilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $outletId = $request->get('outlet_id') ?: $this->getSelectedOutlet($request);
            
            DB::transaction(function () use ($request, $outletId) {
                // Validasi manual - pastikan akun ada berdasarkan KODE, bukan ID
                $accounts = [
                    'akun_piutang_usaha' => $request->akun_piutang_usaha,
                    'akun_pendapatan_penjualan' => $request->akun_pendapatan_penjualan,
                    'akun_kas' => $request->akun_kas,
                    'akun_bank' => $request->akun_bank,
                    'akun_hpp' => $request->akun_hpp,
                    'akun_persediaan' => $request->akun_persediaan,
                ];

                foreach ($accounts as $field => $accountCode) {
                    if (!empty($accountCode)) {
                        $account = \App\Models\ChartOfAccount::where('code', $accountCode)
                            ->where('outlet_id', $outletId)
                            ->first();
                        
                        if (!$account) {
                            throw new \Exception("Akun {$field} dengan kode {$accountCode} tidak ditemukan untuk outlet ini");
                        }
                    }
                }

                // Simpan langsung kode akun (tidak perlu konversi)
                $coaSettings = [
                    'id_outlet' => $outletId,
                    'accounting_book_id' => $request->accounting_book_id,
                    'akun_piutang_usaha' => $request->akun_piutang_usaha,
                    'akun_pendapatan_penjualan' => $request->akun_pendapatan_penjualan,
                    'akun_kas' => $request->akun_kas,
                    'akun_bank' => $request->akun_bank,
                    'akun_hpp' => $request->akun_hpp,
                    'akun_persediaan' => $request->akun_persediaan,
                ];

                // Gunakan method updateOrCreateForOutlet
                SettingCOASales::updateOrCreateForOutlet($outletId, $coaSettings);

                \Log::info('COA setting updated for outlet', [
                    'outlet_id' => $outletId,
                    'settings' => $coaSettings
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Setting COA berhasil disimpan untuk outlet ini'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saving COA setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan setting: ' . $e->getMessage()
            ], 500);
        }
    }

/**
 * Helper method untuk mendapatkan kode akun berdasarkan ID (jika masih diperlukan)
 */
private function getAccountCodeById($accountId)
{
    $account = \App\Models\ChartOfAccount::find($accountId);
    return $account ? $account->code : null;
}

    /**
     * Helper method untuk validasi akun
     */
    private function validateAccountExists($accountId, $fieldName)
    {
        $account = \App\Models\ChartOfAccount::find($accountId);
        if (!$account) {
            throw new \Exception("Akun {$fieldName} tidak valid");
        }
        return $account->code;
    }


    public function getCustomers(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);
        $outletId = $request->get('outlet_id') ?: $this->getSelectedOutlet($request); // Tambahkan default value
        $perPage = 20;

        \Log::info('=== CUSTOMER SEARCH REQUEST ===');
        \Log::info('Outlet ID:', ['outlet_id' => $outletId]);
        \Log::info('Search term:', ['search' => $search]);
        \Log::info('All request params:', $request->all());

        // Hapus validasi yang terlalu strict, gunakan default value
        if (!$outletId) {
            $outletId = $this->getSelectedOutlet($request);
            if (!$outletId) {
                throw new \Exception('No accessible outlet found for user');
            }
            \Log::info('Using selected outlet:', ['outlet_id' => $outletId]);
        }

        try {
            // Query members dengan outlet_id
            $membersQuery = Member::where('id_outlet', $outletId);
            
            if ($search) {
                $membersQuery->where(function($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                        ->orWhere('telepon', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            }

            $members = $membersQuery->with('tipe')
                        ->select('id_member as id', 'nama', 'nama_perusahaan', 'telepon', 'alamat', 'id_tipe', DB::raw("'member' as type"))
                        ->orderBy('nama')
                        ->get()
                        ->map(function($member) {
                            return [
                                'id' => $member->id,
                                'nama' => $member->nama,
                                'nama_perusahaan' => $member->nama_perusahaan,
                                'telepon' => $member->telepon,
                                'alamat' => $member->alamat,
                                'type' => 'member',
                                'id_tipe' => $member->id_tipe,
                                'tipe' => $member->tipe ? [
                                    'id_tipe' => $member->tipe->id_tipe,
                                    'nama_tipe' => $member->tipe->nama_tipe
                                ] : null
                            ];
                        })
                        ->toArray();

            // Query prospeks dengan outlet_id
            $prospeksQuery = Prospek::where('id_outlet', $outletId);
            
            if ($search) {
                $prospeksQuery->where(function($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                        ->orWhere('telepon', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            }

            $prospeks = $prospeksQuery->select('id_prospek as id', 'nama', 'telepon', 'alamat', DB::raw("'prospek' as type"))
                        ->orderBy('nama')
                        ->get()
                        ->map(function($prospek) {
                            return [
                                'id' => $prospek->id,
                                'nama' => $prospek->nama,
                                'telepon' => $prospek->telepon,
                                'alamat' => $prospek->alamat,
                                'type' => 'prospek',
                                'id_tipe' => null, // Prospek belum memiliki tipe
                                'tipe' => null
                            ];
                        })
                        ->toArray();

            $allCustomers = array_merge($members, $prospeks);
            $total = count($allCustomers);

            \Log::info('🔍 Customer search results:', [
                'outlet_id' => $outletId,
                'members_found' => count($members),
                'prospeks_found' => count($prospeks),
                'total_customers' => $total
            ]);

            // Pagination
            $offset = ($page - 1) * $perPage;
            $customers = array_slice($allCustomers, $offset, $perPage);
            $lastPage = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'customers' => $customers,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => (int)$page,
                    'last_page' => $lastPage,
                    'from' => $offset + 1,
                    'to' => $offset + count($customers)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Error in getCustomers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving customers: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOutlets()
    {
        try {
            $outlets = Outlet::where('is_active', true)
                        ->select('id_outlet', 'kode_outlet', 'nama_outlet')
                        ->orderBy('created_at', 'asc')
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

    public function getCustomerDetail($type, $id)
    {
        try {
            if ($type === 'member') {
                $customer = Member::with('tipe')->findOrFail($id);
                return response()->json([
                    'success' => true,
                    'customer' => [
                        'id' => $customer->id_member,
                        'nama' => $customer->nama,
                        'telepon' => $customer->telepon,
                        'alamat' => $customer->alamat,
                        'type' => 'member',
                        'id_tipe' => $customer->id_tipe,
                        'tipe' => $customer->tipe ? [
                            'id_tipe' => $customer->tipe->id_tipe,
                            'nama_tipe' => $customer->tipe->nama_tipe
                        ] : null
                    ]
                ]);
            } else if ($type === 'prospek') {
                $customer = Prospek::findOrFail($id);
                return response()->json([
                    'success' => true,
                    'customer' => [
                        'id' => $customer->id_prospek,
                        'nama' => $customer->nama,
                        'telepon' => $customer->telepon,
                        'alamat' => $customer->alamat,
                        'nama_perusahaan' => $customer->nama_perusahaan,
                        'type' => 'prospek',
                        'id_tipe' => null, // Prospek belum memiliki tipe customer
                        'tipe' => null
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tipe customer tidak valid'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get Customer Prices by Customer
     */
    public function getCustomerPricesByCustomer($customerId, $customerType)
    {
        try {
            $customerPrices = CustomerPrice::with(['produk' => function($query) {
                $query->with(['satuan', 'hppProduk']);
            }, 'ongkosKirim'])
                ->where('customer_type', $customerType)
                ->where('customer_id', $customerId)
                ->get()
                ->map(function($customerPrice) {
                    $customerPrice->produk = $customerPrice->produk->map(function($produk) {
                        $stok = $produk->hppProduk->sum('stok');
                        $produk->stok = $stok;
                        $produk->stok_tersedia = $stok;
                        return $produk;
                    });
                    return $customerPrice;
                });
            
            return response()->json([
                'success' => true,
                'customer_prices' => $customerPrices
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting customer prices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving customer prices'
            ], 500);
        }
    }

    public function getProdukHargaNormal(Request $request)
    {
        $search = $request->get('search', '');
        $outletId = $request->get('outlet_id');

        \Log::info('=== PRODUCT SEARCH REQUEST ===');
        \Log::info('Outlet ID:', ['outlet_id' => $outletId]);
        \Log::info('Search term:', ['search' => $search]);
        \Log::info('All request params:', $request->all());

        if (!$outletId) {
            \Log::error('❌ OUTLET ID IS MISSING in product search');
            return response()->json([
                'success' => false,
                'message' => 'Outlet ID is required'
            ], 422);
        }

        try {
            $produks = Produk::where('id_outlet', $outletId)
                ->when($search, function($query) use ($search) {
                    return $query->where('nama_produk', 'like', "%{$search}%")
                                ->orWhere('kode_produk', 'like', "%{$search}%");
                })
                ->get()
                ->map(function($produk) {
                    return [
                        'id_produk' => $produk->id_produk,
                        'kode_produk' => $produk->kode_produk,
                        'nama_produk' => $produk->nama_produk,
                        'harga' => $produk->harga_jual,
                        'satuan' => $produk->satuan ? $produk->satuan->nama_satuan : 'Unit',
                        'stok' => $produk->stok ?? 0,
                    ];
                });

            \Log::info('🔍 Product search results:', [
                'outlet_id' => $outletId,
                'products_found' => $produks->count()
            ]);

            return response()->json([
                'success' => true,
                'produks' => $produks
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Error in getProdukHargaNormal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Produk dengan Harga Khusus
     */
    public function getProdukHargaKhusus(Request $request)
    {
        $customerPriceId = $request->get('customer_price_id');
        $search = $request->get('search', '');
        
        try {
            $customerPrice = CustomerPrice::with(['produk' => function($query) use ($search) {
                if ($search) {
                    $query->where('nama_produk', 'like', "%{$search}%")
                          ->orWhere('kode_produk', 'like', "%{$search}%");
                }
            }])->findOrFail($customerPriceId);
            
            $produks = $customerPrice->produk->map(function($produk) {
                return [
                    'id_produk' => $produk->id_produk,
                    'kode_produk' => $produk->kode_produk,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => $produk->pivot->harga_khusus,
                    'satuan' => $produk->satuan ? $produk->satuan->nama_satuan : 'Unit',
                    'stok' => $produk->stok ?? 0,
                ];
            });
            
            return response()->json([
                'success' => true,
                'produks' => $produks,
                'ongkos_kirim' => $customerPrice->ongkosKirim
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function ongkirData(Request $request)
    {
        $search = $request->get('search', '');
        $outletId = $request->get('outlet_id');
        
        $ongkosKirim = OngkosKirim::byOutlet($outletId)
            ->when($search, function($query) use ($search) {
                return $query->where('daerah', 'like', "%{$search}%");
            })
            ->get();
        
        return response()->json([
            'data' => $ongkosKirim,
            'success' => true
        ]);
    }

    public function ongkirStore(Request $request)
{
    // Pastikan hanya menerima request AJAX/JSON
    if (!$request->wantsJson() && !$request->ajax()) {
        \Log::warning('Non-JSON request to ongkirStore');
    }

    \Log::info('ONGKIR STORE REQUEST', $request->all());

    try {
        // Validasi
        $validator = Validator::make($request->all(), [
            'daerah' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'outlet_id' => 'required|exists:outlets,id_outlet'
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        \Log::info('Creating ongkos kirim', $request->all());
        
        // Create ongkos kirim
        $ongkosKirim = OngkosKirim::create([
            'id_outlet' => $request->outlet_id,
            'daerah' => $request->daerah,
            'harga' => $request->harga
        ]);

        \Log::info('Ongkos kirim created successfully', $ongkosKirim->toArray());

        // Return JSON response dengan header yang jelas
        return response()->json([
            'success' => true,
            'message' => 'Data ongkos kirim berhasil disimpan',
            'data' => $ongkosKirim
        ])->header('Content-Type', 'application/json');

    } catch (\Exception $e) {
        \Log::error('Error in ongkirStore: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan data ongkos kirim: ' . $e->getMessage()
        ], 500)->header('Content-Type', 'application/json');
    }
}

    // Update juga method ongkirUpdate:
    public function ongkirUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'daerah' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'outlet_id' => 'required|exists:outlets,id_outlet'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ongkosKirim = OngkosKirim::findOrFail($id);
            $ongkosKirim->update([
                'id_outlet' => $request->outlet_id,
                'daerah' => $request->daerah,
                'harga' => $request->harga
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data ongkos kirim berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating ongkos kirim: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data ongkos kirim'
            ], 500);
        }
    }

    /**
     * Edit Ongkos Kirim
     */
    public function ongkirEdit($id)
    {
        try {
            $ongkosKirim = OngkosKirim::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $ongkosKirim
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data ongkos kirim tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Delete Ongkos Kirim
     */
    public function ongkirDestroy($id)
    {
        try {
            $ongkosKirim = OngkosKirim::findOrFail($id);
            $ongkosKirim->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data ongkos kirim berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting ongkos kirim: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data ongkos kirim'
            ], 500);
        }
    }

    public function customerPriceData(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        \Log::info('Loading customer price data for outlet:', ['outlet_id' => $outletId]);

        $customerPrices = CustomerPrice::with(['ongkosKirim', 'produk'])
            ->byOutlet($outletId)
            ->get();

        \Log::info('Customer prices found:', ['count' => $customerPrices->count()]);
        
        // Debug first item
        if ($customerPrices->count() > 0) {
            $first = $customerPrices->first();
            \Log::info('First customer price debug:', [
                'id' => $first->id_customer_price,
                'id_ongkir' => $first->id_ongkir,
                'ongkir_loaded' => $first->ongkosKirim ? 'YES' : 'NO',
                'ongkir_data' => $first->ongkosKirim ? $first->ongkosKirim->toArray() : null,
                'produk_count' => $first->produk->count(),
                'produk_data' => $first->produk->take(2)->toArray()
            ]);
        }
        
        return DataTables::of($customerPrices)
            ->addIndexColumn()
            ->addColumn('customer_name', function ($row) {
                if ($row->customer_type === 'member') {
                    $customer = Member::where('id_member', $row->customer_id)
                        ->where('id_outlet', $row->id_outlet)
                        ->first();
                    return $customer ? $customer->nama : 'Member Tidak Ditemukan';
                } else {
                    $customer = Prospek::where('id_prospek', $row->customer_id)
                        ->where('id_outlet', $row->id_outlet)
                        ->first();
                    return $customer ? ($customer->nama ?? $customer->nama_perusahaan) : 'Prospek Tidak Ditemukan';
                }
            })
            ->addColumn('customer_type', function ($row) {
                return $row->customer_type === 'member' ? 'Member' : 'Prospek';
            })
            ->addColumn('produk_list', function ($row) {
                if (!$row->produk || $row->produk->isEmpty()) {
                    return '<span class="text-slate-500">Tidak ada produk</span>';
                }
                
                $produkList = '';
                foreach ($row->produk->take(2) as $produk) {
                    if ($produk && $produk->pivot) {
                        $hargaKhusus = $produk->pivot->harga_khusus ? ' (Rp ' . number_format($produk->pivot->harga_khusus, 0, ',', '.') . ')' : '';
                        $produkList .= '<span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">' . 
                                    $produk->nama_produk . $hargaKhusus . '</span>';
                    }
                }
                
                if ($row->produk->count() > 2) {
                    $produkList .= '<span class="text-xs text-slate-500">... dan ' . ($row->produk->count() - 2) . ' produk lainnya</span>';
                }
                
                return $produkList;
            })
            ->addColumn('ongkos_kirim', function ($row) {
                return $row->ongkosKirim ? $row->ongkosKirim->daerah . ' (Rp ' . number_format($row->ongkosKirim->harga, 0, ',', '.') . ')' : '-';
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="flex gap-1">';
                $actions .= '<button onclick="editCustomerPrice(' . $row->id_customer_price . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                    <i class="bx bx-edit text-xs"></i> Edit
                </button>';
                $actions .= '<button onclick="deleteCustomerPrice(' . $row->id_customer_price . ')" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-red-100 text-red-700 hover:bg-red-200">
                    <i class="bx bx-trash text-xs"></i> Hapus
                </button>';
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['produk_list', 'actions'])
            ->make(true);
    }

    public function customerPriceStore(Request $request)
    {
        \Log::info('=== CUSTOMER PRICE STORE REQUEST ===');
        \Log::info('Full request data:', $request->all());
        \Log::info('Request headers:', $request->headers->all());

        $validator = Validator::make($request->all(), [
            'customer_type' => 'required|in:member,prospek',
            'customer_id' => 'required',
            'id_ongkir' => 'required|exists:ongkos_kirim,id_ongkir',
            'produk' => 'required|array|min:1',
            'produk.*' => 'required|exists:produk,id_produk',
            'harga_khusus_produk' => 'required|array|min:1',
            'harga_khusus_produk.*' => 'required|numeric|min:0',
            'outlet_id' => 'required|exists:outlets,id_outlet'
        ]);

        \Log::info('Validation rules checked');

        if ($validator->fails()) {
            \Log::error('VALIDATION FAILED:', $validator->errors()->toArray());
            \Log::error('Failed data:', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        \Log::info('Validation passed');

        // Validasi customer exists dan sesuai outlet
        if ($request->customer_type === 'member') {
            $customer = Member::where('id_member', $request->customer_id)
                ->where('id_outlet', $request->outlet_id)
                ->first();
            \Log::info('Member search result:', [$customer ? 'Found' : 'Not found']);
            if (!$customer) {
                \Log::error('Member not found for outlet', [
                    'member_id' => $request->customer_id,
                    'outlet_id' => $request->outlet_id
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => ['customer_id' => 'Member tidak ditemukan di outlet ini']
                ], 422);
            }
        } else {
            $customer = Prospek::where('id_prospek', $request->customer_id)
                ->where('id_outlet', $request->outlet_id)
                ->first();
            \Log::info('Prospek search result:', [$customer ? 'Found' : 'Not found']);
            if (!$customer) {
                \Log::error('Prospek not found for outlet', [
                    'prospek_id' => $request->customer_id,
                    'outlet_id' => $request->outlet_id
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => ['customer_id' => 'Prospek tidak ditemukan di outlet ini']
                ], 422);
            }
        }

        // Validasi ongkos kirim sesuai outlet
        $ongkosKirim = OngkosKirim::where('id_ongkir', $request->id_ongkir)
            ->where('id_outlet', $request->outlet_id)
            ->first();
        
        if (!$ongkosKirim) {
            \Log::error('Ongkos kirim not found for outlet', [
                'ongkir_id' => $request->id_ongkir,
                'outlet_id' => $request->outlet_id
            ]);
            return response()->json([
                'success' => false,
                'errors' => ['id_ongkir' => 'Ongkos kirim tidak ditemukan di outlet ini']
            ], 422);
        }

        // Validasi produk sesuai outlet
        foreach ($request->produk as $produkId) {
            $produk = Produk::where('id_produk', $produkId)
                ->where('id_outlet', $request->outlet_id)
                ->first();
            
            if (!$produk) {
                \Log::error('Product not found for outlet', [
                    'produk_id' => $produkId,
                    'outlet_id' => $request->outlet_id
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => ['produk' => "Produk dengan ID {$produkId} tidak ditemukan di outlet ini"]
                ], 422);
            }
        }

        try {
            DB::transaction(function () use ($request) {
                \Log::info('Creating customer price record');
                
                $customerPrice = CustomerPrice::create([
                    'id_outlet' => $request->outlet_id,
                    'customer_type' => $request->customer_type,
                    'customer_id' => $request->customer_id,
                    'id_ongkir' => $request->id_ongkir,
                ]);

                \Log::info('Customer price created:', ['id' => $customerPrice->id_customer_price]);

                $produkData = [];
                foreach ($request->produk as $index => $produkId) {
                    $produkData[$produkId] = [
                        'harga_khusus' => $request->harga_khusus_produk[$index] ?? 0,
                    ];
                    \Log::info('Adding product to customer price:', [
                        'produk_id' => $produkId,
                        'harga_khusus' => $request->harga_khusus_produk[$index] ?? 0
                    ]);
                }
                
                $customerPrice->produk()->attach($produkData);
                \Log::info('Products attached successfully');
            });

            \Log::info('Customer price stored successfully');

            return response()->json([
                'success' => true, 
                'message' => 'Data harga khusus customer berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error storing customer price: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data harga khusus customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function customerPriceEdit($id)
    {
        try {
            $customerPrice = CustomerPrice::with([
                'produk' => function($query) {
                    $query->withPivot('harga_khusus');
                },
                'ongkosKirim'
            ])->findOrFail($id);
            
            \Log::info('Customer Price Edit - Raw Data:', [
                'customer_price' => $customerPrice->toArray(),
                'customer_type' => $customerPrice->customer_type,
                'customer_id' => $customerPrice->customer_id,
                'outlet_id' => $customerPrice->id_outlet
            ]);
            
            // Ambil data customer dari database dengan filter outlet
            $customerName = 'Customer Tidak Ditemukan';
            $customerData = [];
            
            if ($customerPrice->customer_type === 'member') {
                $customer = Member::where('id_member', $customerPrice->customer_id)
                    ->where('id_outlet', $customerPrice->id_outlet)
                    ->first();
                if ($customer) {
                    $customerName = $customer->nama;
                    $customerData = [
                        'id' => $customer->id_member,
                        'nama' => $customer->nama,
                        'telepon' => $customer->telepon,
                        'alamat' => $customer->alamat,
                        'type' => 'member'
                    ];
                    \Log::info('Found Member:', $customer->toArray());
                }
            } else {
                $customer = Prospek::where('id_prospek', $customerPrice->customer_id)
                    ->where('id_outlet', $customerPrice->id_outlet)
                    ->first();
                if ($customer) {
                    $customerName = $customer->nama ?? $customer->nama_perusahaan;
                    $customerData = [
                        'id' => $customer->id_prospek,
                        'nama' => $customer->nama ?? $customer->nama_perusahaan,
                        'telepon' => $customer->telepon,
                        'alamat' => $customer->alamat,
                        'nama_perusahaan' => $customer->nama_perusahaan,
                        'type' => 'prospek'
                    ];
                    \Log::info('Found Prospek:', $customer->toArray());
                }
            }
            
            \Log::info('Final Customer Name:', ['customer_name' => $customerName]);

            // Format produk data
            \Log::info('Produk count:', ['count' => $customerPrice->produk->count()]);
            \Log::info('Produk raw:', ['produk' => $customerPrice->produk->toArray()]);
            
            $produkData = $customerPrice->produk->map(function($produk) {
                \Log::info('Mapping produk:', [
                    'id_produk' => $produk->id_produk,
                    'nama_produk' => $produk->nama_produk,
                    'pivot' => $produk->pivot ? $produk->pivot->toArray() : null
                ]);
                
                return [
                    'id_produk' => $produk->id_produk,
                    'nama_produk' => $produk->nama_produk,
                    'harga_jual' => $produk->harga_jual,
                    'pivot' => [
                        'harga_khusus' => $produk->pivot->harga_khusus
                    ]
                ];
            })->toArray();
            
            \Log::info('Produk data formatted:', ['produk_data' => $produkData]);

            $responseData = [
                'success' => true,
                'data' => [
                    'id_customer_price' => $customerPrice->id_customer_price,
                    'id_outlet' => $customerPrice->id_outlet,
                    'customer_type' => $customerPrice->customer_type,
                    'customer_id' => $customerPrice->customer_id,
                    'customer' => $customerData,
                    'customer_name' => $customerName,
                    'id_ongkir' => $customerPrice->id_ongkir,
                    'ongkos_kirim' => $customerPrice->ongkosKirim ? [
                        'id_ongkir' => $customerPrice->ongkosKirim->id_ongkir,
                        'daerah' => $customerPrice->ongkosKirim->daerah,
                        'harga' => $customerPrice->ongkosKirim->harga
                    ] : null,
                    'produk' => $produkData
                ]
            ];

            \Log::info('Customer Price Edit Final Response:', $responseData);

            return response()->json($responseData);
            
        } catch (\Exception $e) {
            \Log::error('Error in customer price edit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menampilkan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function customerPriceUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_type' => 'required|in:member,prospek',
            'customer_id' => 'required',
            'id_ongkir' => 'required|exists:ongkos_kirim,id_ongkir',
            'produk' => 'required|array|min:1',
            'produk.*' => 'required|exists:produk,id_produk',
            'harga_khusus_produk' => 'required|array|min:1',
            'harga_khusus_produk.*' => 'required|numeric|min:0',
            'outlet_id' => 'required|exists:outlets,id_outlet' // Tambahkan validasi
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $customerPrice = CustomerPrice::findOrFail($id);
                $customerPrice->update([
                    'id_outlet' => $request->outlet_id, // UPDATE outlet_id
                    'customer_type' => $request->customer_type,
                    'customer_id' => $request->customer_id,
                    'id_ongkir' => $request->id_ongkir,
                ]);
                
                $produkData = [];
                foreach ($request->produk as $index => $produkId) {
                    $produkData[$produkId] = [
                        'harga_khusus' => $request->harga_khusus_produk[$index] ?? 0,
                    ];
                }
                
                $customerPrice->produk()->sync($produkData);
            });

            return response()->json([
                'success' => true, 
                'message' => 'Data harga khusus customer berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating customer price: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data harga khusus customer'
            ], 500);
        }
    }

    /**
     * Delete Customer Price
     */
    public function customerPriceDestroy($id)
    {
        try {
            $customerPrice = CustomerPrice::findOrFail($id);
            $customerPrice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data harga khusus customer berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting customer price: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data harga khusus customer'
            ], 500);
        }
    }

    public function coaSettingPreview(Request $request)
    {
        try {
            $status = $request->get('status', 'menunggu');
            $total = (float) str_replace(['.', ','], '', $request->get('total', '1000000'));
            $outletId = $request->get('outlet_id') ?: $this->getSelectedOutlet($request);
            
            // Ambil setting berdasarkan outlet
            $setting = SettingCOASales::getByOutlet($outletId);
            
            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting COA belum diatur untuk outlet ini'
                ]);
            }

            $previewData = $this->generateJournalPreview($setting, $status, $total);
            
            return response()->json([
                'success' => true,
                'preview' => $previewData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in coaSettingPreview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Journal Preview Data untuk semua status
     */
    private function generateJournalPreview($setting, $status, $total)
    {
        $entries = [];
        $description = "Invoice Penjualan INV/001/2024 - " . strtoupper($status);
        
        // Hitung HPP contoh (30% dari total)
        $hppAmount = $total * 0.3;

        try {
            switch ($status) {
                case 'menunggu':
                    if (!empty($setting->akun_piutang_usaha) && !empty($setting->akun_pendapatan_penjualan)) {
                        $piutangAccount = $this->getAccountByCode($setting->akun_piutang_usaha);
                        $pendapatanAccount = $this->getAccountByCode($setting->akun_pendapatan_penjualan);
                        
                        $entries = [
                            [
                                'account_code' => $piutangAccount->code,
                                'account_name' => $piutangAccount->name,
                                'account_type' => $piutangAccount->type_name,
                                'debit' => $total,
                                'credit' => 0,
                                'position' => 'Debit'
                            ],
                            [
                                'account_code' => $pendapatanAccount->code,
                                'account_name' => $pendapatanAccount->name,
                                'account_type' => $pendapatanAccount->type_name,
                                'debit' => 0,
                                'credit' => $total,
                                'position' => 'Kredit'
                            ]
                        ];
                        
                        // Tambahkan entri HPP dan Persediaan jika ada setting-nya
                        if (!empty($setting->akun_hpp) && !empty($setting->akun_persediaan) && $hppAmount > 0) {
                            $hppAccount = $this->getAccountByCode($setting->akun_hpp);
                            $persediaanAccount = $this->getAccountByCode($setting->akun_persediaan);
                            
                            $entries[] = [
                                'account_code' => $hppAccount->code,
                                'account_name' => $hppAccount->name,
                                'account_type' => $hppAccount->type_name,
                                'debit' => $hppAmount,
                                'credit' => 0,
                                'position' => 'Debit'
                            ];
                            $entries[] = [
                                'account_code' => $persediaanAccount->code,
                                'account_name' => $persediaanAccount->name,
                                'account_type' => $persediaanAccount->type_name,
                                'debit' => 0,
                                'credit' => $hppAmount,
                                'position' => 'Kredit'
                            ];
                        }
                    }
                    break;

                case 'lunas':
                    $akunKasBank = !empty($setting->akun_kas) ? $setting->akun_kas : $setting->akun_bank;
                    
                    if (!empty($akunKasBank) && !empty($setting->akun_piutang_usaha)) {
                        $kasBankAccount = $this->getAccountByCode($akunKasBank);
                        $piutangAccount = $this->getAccountByCode($setting->akun_piutang_usaha);
                        
                        $entries = [
                            [
                                'account_code' => $kasBankAccount->code,
                                'account_name' => $kasBankAccount->name,
                                'account_type' => $kasBankAccount->type_name,
                                'debit' => $total,
                                'credit' => 0,
                                'position' => 'Debit'
                            ],
                            [
                                'account_code' => $piutangAccount->code,
                                'account_name' => $piutangAccount->name,
                                'account_type' => $piutangAccount->type_name,
                                'debit' => 0,
                                'credit' => $total,
                                'position' => 'Kredit'
                            ]
                        ];
                    }
                    break;

                case 'gagal':
                    if (!empty($setting->akun_pendapatan_penjualan) && !empty($setting->akun_piutang_usaha)) {
                        $pendapatanAccount = $this->getAccountByCode($setting->akun_pendapatan_penjualan);
                        $piutangAccount = $this->getAccountByCode($setting->akun_piutang_usaha);
                        
                        $entries = [
                            [
                                'account_code' => $pendapatanAccount->code,
                                'account_name' => $pendapatanAccount->name,
                                'account_type' => $pendapatanAccount->type_name,
                                'debit' => $total,
                                'credit' => 0,
                                'position' => 'Debit'
                            ],
                            [
                                'account_code' => $piutangAccount->code,
                                'account_name' => $piutangAccount->name,
                                'account_type' => $piutangAccount->type_name,
                                'debit' => 0,
                                'credit' => $total,
                                'position' => 'Kredit'
                            ]
                        ];
                        
                        // Reverse entri HPP dan Persediaan jika ada setting-nya
                        if (!empty($setting->akun_hpp) && !empty($setting->akun_persediaan) && $hppAmount > 0) {
                            $hppAccount = $this->getAccountByCode($setting->akun_hpp);
                            $persediaanAccount = $this->getAccountByCode($setting->akun_persediaan);
                            
                            $entries[] = [
                                'account_code' => $hppAccount->code,
                                'account_name' => $hppAccount->name,
                                'account_type' => $hppAccount->type_name,
                                'debit' => 0,
                                'credit' => $hppAmount,
                                'position' => 'Kredit'
                            ];
                            $entries[] = [
                                'account_code' => $persediaanAccount->code,
                                'account_name' => $persediaanAccount->name,
                                'account_type' => $persediaanAccount->type_name,
                                'debit' => $hppAmount,
                                'credit' => 0,
                                'position' => 'Debit'
                            ];
                        }
                    }
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Error generating journal preview: ' . $e->getMessage());
        }

        return [
            'status' => $status,
            'description' => $description,
            'total' => $total,
            'hpp_amount' => $hppAmount,
            'entries' => $entries,
            'is_balanced' => $this->isJournalBalanced($entries)
        ];
    }

/**
 * Helper method untuk mendapatkan account berdasarkan kode
 */
private function getAccountByCode($code)
{
    $account = \App\Models\ChartOfAccount::where('code', $code)->first();
    
    if (!$account) {
        throw new \Exception("Akun dengan kode {$code} tidak ditemukan");
    }
    
    return $account;
}

    private function isJournalBalanced($entries)
    {
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($entries as $entry) {
            $totalDebit += $entry['debit'];
            $totalCredit += $entry['credit'];
        }
        
        return $totalDebit === $totalCredit;
    }


    private function createAutomaticJournal($invoice, $status, $oldStatus = null)
    {
        try {
            $journalEntry = $this->journalService->createSalesInvoiceJournal($invoice, $status, $oldStatus);
            
            if ($journalEntry) {
                \Log::info("Jurnal otomatis berhasil dibuat untuk invoice {$invoice->no_invoice}", [
                    'journal_entry_id' => $journalEntry->id,
                    'status' => $status,
                    'outlet_id' => $invoice->id_outlet
                ]);
            } else {
                \Log::info("Tidak ada jurnal yang dibuat untuk invoice {$invoice->no_invoice} status {$status}");
            }

        } catch (\Exception $e) {
            \Log::error('Gagal membuat jurnal otomatis: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id_sales_invoice,
                'status' => $status,
                'outlet_id' => $invoice->id_outlet
            ]);
            // Jangan throw exception agar proses invoice tidak terganggu
        }
    }

    private function getAccountIdByCode($code, $outletId = null)
    {
        if (!$outletId) {
            $outletId = $this->getSelectedOutlet();
            if (!$outletId) {
                throw new \Exception('No accessible outlet found for user');
            }
        }
        
        $account = \App\Models\ChartOfAccount::where('code', $code)
            ->where('outlet_id', $outletId)
            ->first();
        
        if (!$account) {
            throw new \Exception("Akun dengan kode {$code} tidak ditemukan untuk outlet {$outletId}");
        }
        
        return $account->id;
    }

    /**
     * Cek kelengkapan setting COA berdasarkan status
     */
    private function isCoaSettingComplete($setting, $status)
    {
        $requiredAccounts = [
            'akun_piutang_usaha',
            'akun_pendapatan_penjualan',
            'akun_hpp', // Wajib untuk perhitungan HPP
            'akun_persediaan' // Wajib untuk persediaan
        ];

        if ($status === 'lunas') {
            $requiredAccounts[] = 'akun_kas';
            $requiredAccounts[] = 'akun_bank';
        }

        foreach ($requiredAccounts as $account) {
            if (empty($setting->$account)) {
                return false;
            }
        }

        return !empty($setting->accounting_book_id);
    }


    public function invoiceDestroy($id)
{
    try {
        DB::transaction(function () use ($id) {
            $invoice = SalesInvoice::findOrFail($id);
            
            // Jika invoice status bukan gagal, batalkan dulu
            if ($invoice->status !== 'gagal') {
                $this->cancelInvoice($id);
            }
            
            // Hapus sales invoice items
            SalesInvoiceItem::where('id_sales_invoice', $id)->delete();
            
            // Hapus sales invoice
            $invoice->delete();
            
            \Log::info("Invoice dihapus - ID: {$id}");
        });
        
        return response()->json(['success' => true, 'message' => 'Invoice berhasil dihapus']);
        
    } catch (\Exception $e) {
        \Log::error('Gagal menghapus invoice: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Gagal menghapus invoice: ' . $e->getMessage()
        ], 500);
    }
}

// Helper method untuk display value (dipanggil dari view)
public function getAccountDisplayValue($accountCode)
{
    if (empty($accountCode)) {
        return '';
    }
    
    $accountName = $this->getAccountNameByCode($accountCode);
    return $accountCode . ' - ' . $accountName;
}

// Method untuk get customer price detail (update)
public function getCustomerPriceDetail($id)
{
    try {
        $customerPrice = CustomerPrice::with([
            'produk' => function($query) {
                $query->withPivot('harga_khusus');
            }, 
            'ongkosKirim',
            'customer'
        ])->findOrFail($id);
        
        return response()->json($customerPrice);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Data harga khusus tidak ditemukan'
        ], 404);
    }
}

// Method untuk get customer price by member (existing, perlu disesuaikan)
public function getCustomerPrice($id_member)
{
    try {
        // Cari dari member dan prospek
        $customerPrices = CustomerPrice::with(['produk', 'ongkosKirim'])
            ->where(function($query) use ($id_member) {
                $query->where('customer_type', 'member')
                      ->where('customer_id', $id_member);
            })
            ->orWhere(function($query) use ($id_member) {
                $query->where('customer_type', 'prospek')
                      ->where('customer_id', $id_member);
            })
            ->get();
        
        return response()->json($customerPrices);

    } catch (\Exception $e) {
        return response()->json([], 500);
    }
}

// Method untuk preview invoice PDF
public function invoicePreview($id)
{
    try {
        $invoice = SalesInvoice::with(['member', 'customerPrice.produk', 'items'])->findOrFail($id);
        $setting = DB::table('setting')->first();
        
        $pdf = Pdf::loadView('sales_management.invoice.preview', compact('invoice', 'setting'));
        $pdf->setPaper('A4', 'portrait');
        
        $safeFilename = 'invoice-preview-' . str_replace('/', '-', $invoice->no_invoice);
        return $pdf->stream($safeFilename . '.pdf');
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal generate PDF'], 500);
    }
}

// Method untuk preview jurnal
public function journalPreview($id)
{
    try {
        $invoice = SalesInvoice::with(['member', 'journalEntries.account'])->findOrFail($id);
        $setting = DB::table('setting')->first();
        
        $pdf = Pdf::loadView('sales_management.invoice.journal_preview', compact('invoice', 'setting'));
        $pdf->setPaper('A4', 'portrait');
        
        $safeFilename = 'jurnal-preview-' . str_replace('/', '-', $invoice->no_invoice);
        return $pdf->stream($safeFilename . '.pdf');
        
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal generate PDF jurnal'], 500);
    }
}

// Method untuk preview jurnal sebelum simpan
public function previewJournalBeforeSave(Request $request)
{
    try {
        $status = 'menunggu'; // Default status untuk invoice baru
        $total = (float) str_replace(['.', ','], '', $request->get('total', '0'));
        $customerType = $request->get('customer_type');
        $customerId = $request->get('customer_id');
        
        $setting = \App\Models\SettingCOASales::first();
        
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting COA belum diatur'
            ]);
        }

        $previewData = $this->generateJournalPreview($setting, $status, $total);
        
        return response()->json([
            'success' => true,
            'preview' => $previewData
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in previewJournalBeforeSave: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

// Method untuk menghapus jurnal otomatis berdasarkan created_at
private function deleteAutomaticJournal($invoice)
{
    try {
        // Cari jurnal berdasarkan created_at yang sama dengan invoice
        // Asumsi: jurnal dibuat pada waktu yang sama dengan invoice
        $journal = \App\Models\Journal::where('created_at', $invoice->created_at)
            ->where('description', 'like', '%Invoice Penjualan ' . $invoice->no_invoice . '%')
            ->first();
            
        if ($journal) {
            // Hapus journal entries terlebih dahulu
            \App\Models\JournalEntry::where('journal_id', $journal->id)->delete();
            
            // Hapus journal
            $journal->delete();
            
            \Log::info("Jurnal otomatis dihapus untuk invoice: {$invoice->no_invoice}, Journal ID: {$journal->id}");
        } else {
            \Log::warning("Jurnal tidak ditemukan untuk invoice: {$invoice->no_invoice}");
        }
    } catch (\Exception $e) {
        \Log::error("Gagal menghapus jurnal otomatis: " . $e->getMessage());
    }
}

public function invoiceSettingJson()
{
    $counter = \App\Models\InvoiceSalesCounter::first();
    
    if (!$counter) {
        $counter = new \App\Models\InvoiceSalesCounter();
        $counter->last_number = 0;
        $counter->year = date('Y');
        $counter->save();
    }
    
    $currentNumber = $counter->last_number;
    $currentYear = $counter->year;
    
    $currentInvoiceNumber = str_pad($currentNumber, 3, '0', STR_PAD_LEFT) . '/SLS.INV/' . \App\Models\InvoiceSalesCounter::getRomanMonth() . '/' . $currentYear;
    $nextInvoiceNumber = str_pad($currentNumber + 1, 3, '0', STR_PAD_LEFT) . '/SLS.INV/' . \App\Models\InvoiceSalesCounter::getRomanMonth() . '/' . $currentYear;
    
    return response()->json([
        'success' => true,
        'data' => [
            'current_invoice_number' => $currentInvoiceNumber,
            'next_invoice_number' => $nextInvoiceNumber,
            'starting_number' => $currentNumber + 1,
            'year' => $currentYear
        ]
    ]);
}

// API untuk get setting nomor invoice
public function getInvoiceSetting()
{
    $counter = \App\Models\InvoiceSalesCounter::first();
    
    if (!$counter) {
        $counter = new \App\Models\InvoiceSalesCounter();
        $counter->last_number = 0;
        $counter->year = date('Y');
        $counter->save();
    }
    
    $currentNumber = $counter->last_number;
    $currentYear = $counter->year;
    
    $currentInvoiceNumber = str_pad($currentNumber, 3, '0', STR_PAD_LEFT) . '/SLS.INV/' . \App\Models\InvoiceSalesCounter::getRomanMonth() . '/' . $currentYear;
    $nextInvoiceNumber = str_pad($currentNumber + 1, 3, '0', STR_PAD_LEFT) . '/SLS.INV/' . \App\Models\InvoiceSalesCounter::getRomanMonth() . '/' . $currentYear;
    
    return response()->json([
        'success' => true,
        'data' => [
            'current_invoice_number' => $currentInvoiceNumber,
            'next_invoice_number' => $nextInvoiceNumber,
            'starting_number' => $currentNumber + 1,
            'year' => $currentYear,
            'last_number' => $currentNumber
        ]
    ]);
}

/**
 * View Bukti Transfer
 */
public function viewBuktiTransfer($id)
{
    try {
        $invoice = SalesInvoice::findOrFail($id);
        
        \Log::info('View bukti transfer request', [
            'invoice_id' => $id,
            'bukti_transfer' => $invoice->bukti_transfer,
            'has_bukti_transfer' => $invoice->hasBuktiTransfer()
        ]);

        if (!$invoice->hasBuktiTransfer()) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti transfer tidak ditemukan di database'
            ], 404);
        }

        // Gunakan storage_path yang benar
        $filePath = storage_path('app/public/bukti-transfer-penjualan/' . $invoice->bukti_transfer);
        
        \Log::info('Checking file path', [
            'file_path' => $filePath,
            'file_exists' => file_exists($filePath)
        ]);

        if (!file_exists($filePath)) {
            // Coba alternative path
            $alternativePath = storage_path('app/public/bukti-transfer-penjualan/' . $invoice->bukti_transfer);
            \Log::info('Checking alternative path', [
                'alternative_path' => $alternativePath,
                'alternative_exists' => file_exists($alternativePath)
            ]);

            if (file_exists($alternativePath)) {
                $filePath = $alternativePath;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukti transfer tidak ditemukan di storage. Path: ' . $filePath
                ], 404);
            }
        }

        // Determine content type
        $contentType = mime_content_type($filePath);
        \Log::info('File info', [
            'content_type' => $contentType,
            'file_size' => filesize($filePath)
        ]);

        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="' . $invoice->bukti_transfer . '"'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error viewing bukti transfer: ' . $e->getMessage(), [
            'invoice_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Gagal menampilkan bukti transfer: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Download Bukti Transfer
 */
public function downloadBuktiTransfer($id)
{
    try {
        $invoice = SalesInvoice::findOrFail($id);
        
        if (!$invoice->hasBuktiTransfer()) {
            return response()->json([
                'success' => false,
                'message' => 'Bukti transfer tidak ditemukan'
            ], 404);
        }

        $filePath = storage_path('app/public/bukti-transfer-penjualan/' . $invoice->bukti_transfer);
        
        if (!file_exists($filePath)) {
            // Coba alternative path
            $alternativePath = storage_path('app/public/bukti-transfer-penjualan/' . $invoice->bukti_transfer);
            if (file_exists($alternativePath)) {
                $filePath = $alternativePath;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukti transfer tidak ditemukan'
                ], 404);
            }
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = 'Bukti-Transfer-' . $invoice->no_invoice . '.' . $fileExtension;

        return response()->download($filePath, $fileName);

    } catch (\Exception $e) {
        \Log::error('Error downloading bukti transfer: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengunduh bukti transfer: ' . $e->getMessage()
        ], 500);
    }
}

public function getCompanyBankAccounts(Request $request)
{
    try {
        $outletId = $request->get('outlet_id') ?: $this->getSelectedOutlet($request);
        
        $bankAccounts = \App\Models\CompanyBankAccount::byOutlet($outletId)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('bank_name')
            ->get();
            
        return response()->json([
            'success' => true,
            'bank_accounts' => $bankAccounts
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error getting company bank accounts: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat data rekening perusahaan'
        ], 500);
    }
}

/**
 * Get accounts by type - only leaf accounts (accounts without children)
 * Similar to purchase order COA setting
 */
public function getAccountsByType(Request $request)
{
    try {
        $type = $request->get('type', 'asset');
        $outletId = $request->get('outlet_id') ?: $this->getSelectedOutlet($request);
        
        // Get all accounts of the specified type
        $accounts = \App\Models\ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', $type)
            ->where('status', 'active')
            ->orderBy('code')
            ->get();
        
        // Identifikasi parent yang punya children
        $parentIdsWithChildren = collect();
        foreach ($accounts as $account) {
            if ($account->parent_id) {
                $parentIdsWithChildren->push($account->parent_id);
            }
        }
        $parentIdsWithChildren = $parentIdsWithChildren->unique();
        
        // Filter accounts dengan logika:
        // 1. Skip parent yang punya children
        // 2. Include child dari parent yang punya children
        // 3. Include parent yang tidak punya children
        $displayAccounts = $accounts->filter(function($account) use ($parentIdsWithChildren) {
            // Jika ini parent yang punya children, skip
            if ($parentIdsWithChildren->contains($account->id)) {
                return false;
            }
            
            // Jika ini child dari parent yang punya children, include
            if ($account->parent_id && $parentIdsWithChildren->contains($account->parent_id)) {
                return true;
            }
            
            // Jika ini parent yang tidak punya children, include
            if (!$account->parent_id && !$parentIdsWithChildren->contains($account->id)) {
                return true;
            }
            
            return false;
        });
        
        // Format the response
        $formattedAccounts = $displayAccounts->map(function($account) {
            return [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'parent_id' => $account->parent_id,
                'level' => $account->parent_id ? 2 : 1
            ];
        })->values();
        
        return response()->json([
            'success' => true,
            'accounts' => $formattedAccounts
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error getting accounts by type: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat data akun'
        ], 500);
    }
}

/**
 * Process invoice payment (installment or full)
 */
public function processInvoicePayment(Request $request)
{
    try {
        $request->validate([
            'invoice_id' => 'required|exists:sales_invoice,id_sales_invoice',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0.01',
            'jenis_pembayaran' => 'required|in:cash,transfer',
            'nama_bank' => 'nullable|string|max:255',
            'nama_pengirim' => 'nullable|string|max:255',
            'penerima' => 'nullable|string|max:255',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max - OPTIONAL
            'keterangan' => 'nullable|string'
        ]);

        $invoice = SalesInvoice::findOrFail($request->invoice_id);
        
        // Calculate remaining balance
        $sisaTagihan = $invoice->total - $invoice->total_dibayar;
        
        // Validate payment amount
        if ($request->jumlah_bayar > $sisaTagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah bayar (' . number_format($request->jumlah_bayar, 0, ',', '.') . ') melebihi sisa tagihan (' . number_format($sisaTagihan, 0, ',', '.') . ')'
            ]);
        }

        // Process bukti pembayaran if exists
        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $this->compressAndSaveBukti($request->file('bukti_pembayaran'), $invoice->no_invoice);
        }

        DB::transaction(function () use ($request, $invoice, $buktiPath, $sisaTagihan) {
            // Save payment history
            $paymentHistory = InvoicePaymentHistory::create([
                'id_sales_invoice' => $invoice->id_sales_invoice,
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar' => $request->jumlah_bayar,
                'jenis_pembayaran' => $request->jenis_pembayaran,
                'nama_bank' => $request->nama_bank,
                'nama_pengirim' => $request->nama_pengirim,
                'penerima' => $request->penerima,
                'bukti_pembayaran' => $buktiPath,
                'keterangan' => $request->keterangan,
                'created_by' => auth()->id()
            ]);

            // Update invoice totals
            $invoice->total_dibayar += $request->jumlah_bayar;
            $invoice->sisa_tagihan = $invoice->total - $invoice->total_dibayar;

            // Update status based on payment
            if ($invoice->sisa_tagihan <= 0) {
                $invoice->status = 'lunas';
                // Update piutang status
                if ($invoice->piutang) {
                    $invoice->piutang->update(['status' => 'lunas']);
                }
            } else {
                $invoice->status = 'dibayar_sebagian';
            }

            $invoice->save();

            // Create journal entry for payment
            try {
                $this->journalService->createInvoicePaymentJournal($invoice, $paymentHistory);
                \Log::info('Journal entry created for payment', [
                    'invoice_id' => $invoice->id_sales_invoice,
                    'payment_id' => $paymentHistory->id,
                    'amount' => $paymentHistory->jumlah_bayar
                ]);
            } catch (\Exception $e) {
                \Log::error('Error creating payment journal: ' . $e->getMessage());
                // Don't throw - journal creation failure shouldn't block payment
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dicatat',
            'data' => [
                'total_dibayar' => $invoice->total_dibayar,
                'sisa_tagihan' => $invoice->sisa_tagihan,
                'status' => $invoice->status,
                'is_fully_paid' => $invoice->sisa_tagihan <= 0
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error processing invoice payment: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Get payment history for an invoice
 */
public function getPaymentHistory($invoiceId)
{
    try {
        $invoice = SalesInvoice::with(['paymentHistory.creator'])->findOrFail($invoiceId);
        
        $paymentHistory = $invoice->paymentHistory->map(function ($payment) {
            return [
                'id' => $payment->id,
                'tanggal_bayar' => $payment->tanggal_bayar->format('d/m/Y'),
                'jumlah_bayar' => $payment->jumlah_bayar,
                'jenis_pembayaran' => $payment->jenis_pembayaran,
                'nama_bank' => $payment->nama_bank,
                'nama_pengirim' => $payment->nama_pengirim,
                'penerima' => $payment->penerima,
                'bukti_pembayaran' => $payment->bukti_pembayaran_url,
                'keterangan' => $payment->keterangan,
                'created_by' => $payment->creator ? $payment->creator->name : 'Unknown',
                'created_at' => $payment->created_at->format('d/m/Y H:i')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'invoice' => [
                    'no_invoice' => $invoice->no_invoice,
                    'total' => $invoice->total,
                    'total_dibayar' => $invoice->total_dibayar,
                    'sisa_tagihan' => $invoice->sisa_tagihan,
                    'status' => $invoice->status
                ],
                'payment_history' => $paymentHistory
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error getting payment history: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil riwayat pembayaran'
        ], 500);
    }
}

/**
 * Compress and save bukti pembayaran
 */
private function compressAndSaveBukti($file, $invoiceNumber)
{
    try {
        $fileName = 'bukti_' . str_replace('/', '-', $invoiceNumber) . '_' . time() . '.jpg';
        $storagePath = 'bukti_pembayaran/' . $fileName;
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

            \Log::info('Image compressed', [
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
        \Log::error('Error compressing bukti: ' . $e->getMessage());
        throw $e;
    }
}

/**
 * Confirm invoice (change from draft to menunggu)
 */
public function confirmInvoice($invoiceId)
{
    try {
        $invoice = SalesInvoice::findOrFail($invoiceId);
        
        // Check if invoice is draft
        if ($invoice->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya invoice dengan status draft yang bisa dikonfirmasi'
            ]);
        }

        DB::transaction(function () use ($invoice) {
            // Generate invoice number if not exists or is draft placeholder
            if (empty($invoice->no_invoice) || str_starts_with($invoice->no_invoice, 'DRAFT-')) {
                $invoice->no_invoice = $this->generateInvoiceNumber($invoice->id_outlet);
            }
            
            // Update status to menunggu
            $invoice->status = 'menunggu';
            $invoice->save();
            
            // Process invoice items: reduce stock and create penjualan details
            $this->processInvoiceItemsOnConfirm($invoice);
            
            // Create piutang record
            $this->createPiutangFromInvoice($invoice);
            
            // Create journal entry
            $this->createJournalFromInvoice($invoice);
        });

        return response()->json([
            'success' => true,
            'message' => 'Invoice berhasil dikonfirmasi',
            'data' => [
                'no_invoice' => $invoice->no_invoice,
                'status' => $invoice->status
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error confirming invoice: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengkonfirmasi invoice: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Generate invoice number
 */
private function generateInvoiceNumber($outletId)
{
    // Get outlet info
    $outlet = Outlet::find($outletId);
    $outletCode = $outlet ? $outlet->kode : 'PBU';
    
    // Get current month and year
    $month = date('m');
    $year = date('Y');
    $monthRoman = $this->numberToRoman((int)$month);
    
    // Get or create counter for this outlet and year
    $counter = InvoiceSalesCounter::where('id_outlet', $outletId)
                                  ->where('year', $year)
                                  ->first();
    
    if (!$counter) {
        $counter = InvoiceSalesCounter::create([
            'id_outlet' => $outletId,
            'invoice_prefix' => 'INV',
            'last_number' => 1,
            'year' => $year
        ]);
        $nextNumber = 1;
    } else {
        $nextNumber = $counter->last_number + 1;
        $counter->update(['last_number' => $nextNumber]);
    }
    
    // Format: 001/PBU/INV/XI/2025
    return sprintf('%03d/%s/INV/%s/%s', $nextNumber, $outletCode, $monthRoman, $year);
}

/**
 * Convert number to Roman numeral
 */
private function numberToRoman($number)
{
    $map = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    return $map[$number] ?? 'I';
}

/**
 * Create piutang from invoice
 */
private function createPiutangFromInvoice($invoice)
{
    // Check if piutang already exists
    $existingPiutang = Piutang::where('id_penjualan', $invoice->id_penjualan)->first();
    if ($existingPiutang) {
        \Log::info('Piutang already exists for invoice', ['invoice_id' => $invoice->id_sales_invoice]);
        return; // Already exists
    }
    
    $piutangData = [
        'tanggal_tempo' => $invoice->due_date,
        'id_member' => $invoice->id_member,
        'id_outlet' => $invoice->id_outlet,
        'id_penjualan' => $invoice->id_penjualan,
        'nama' => 'Invoice ' . $invoice->no_invoice,
        'piutang' => $invoice->total,
        'jumlah_piutang' => $invoice->total,
        'sisa_piutang' => $invoice->total,
        'status' => 'belum_lunas',
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    \Log::info('Creating piutang on confirm', $piutangData);
    Piutang::create($piutangData);
    \Log::info('Piutang created successfully');
}

/**
 * Create journal entry from invoice
 */
private function createJournalFromInvoice($invoice)
{
    // Create journal entry using existing service
    try {
        $this->journalService->createSalesInvoiceJournal($invoice, 'menunggu');
        \Log::info('Journal entry created for invoice: ' . $invoice->no_invoice);
    } catch (\Exception $e) {
        \Log::error('Error creating journal for invoice: ' . $e->getMessage());
        // Don't throw - journal creation failure shouldn't block confirmation
    }
}

/**
 * Process invoice items on confirm: reduce stock and create penjualan details
 */
private function processInvoiceItemsOnConfirm($invoice)
{
    \Log::info('Processing invoice items on confirm', ['invoice_id' => $invoice->id_sales_invoice]);
    
    // Load invoice items
    $items = $invoice->items;
    
    foreach ($items as $item) {
        if ($item->tipe === 'produk' && $item->id_produk) {
            $produk = Produk::find($item->id_produk);
            
            if ($produk) {
                // Check stock availability
                $stokTersedia = $produk->stok;
                \Log::info('Checking stock on confirm', [
                    'produk' => $produk->nama_produk,
                    'stok_tersedia' => $stokTersedia,
                    'dibutuhkan' => $item->kuantitas
                ]);

                if ($stokTersedia < $item->kuantitas) {
                    throw new \Exception("Stok tidak mencukupi untuk produk: {$produk->nama_produk}. Stok tersedia: {$stokTersedia}, dibutuhkan: {$item->kuantitas}");
                }

                // Reduce stock
                $hpp = $produk->calculateHppBarangDagang();
                \Log::info('Reducing stock on confirm', ['produk' => $produk->nama_produk, 'qty' => $item->kuantitas]);
                $produk->reduceStock($item->kuantitas);

                // Create penjualan detail
                $penjualanDetailData = [
                    'id_penjualan' => $invoice->id_penjualan,
                    'id_produk' => $item->id_produk,
                    'hpp' => $hpp,
                    'harga_jual' => $item->harga,
                    'jumlah' => $item->kuantitas,
                    'diskon' => 0,
                    'subtotal' => $item->subtotal,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                \Log::info('Creating penjualan detail on confirm', $penjualanDetailData);
                \App\Models\PenjualanDetail::create($penjualanDetailData);
            }
        }
    }
    
    \Log::info('Invoice items processed successfully on confirm');
}

}