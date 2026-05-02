<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\ServiceInvoice;
use App\Models\ServiceInvoiceItem;
use App\Models\MesinCustomer;
use App\Models\OngkosKirim;
use App\Models\Member;
use App\Models\Produk;
use App\Models\Outlet;
use App\Models\Sparepart;
use App\Models\SparepartLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ServiceController extends Controller
{
    use \App\Traits\HasOutletFilter;

    /**
     * Display service dashboard page
     */
    public function index()
    {
        // Get accessible outlets for current user
        $outletIds = $this->getAccessibleOutletIds();
        $outlets = Outlet::whereIn('id_outlet', $outletIds)
            ->where('is_active', true)
            ->get(['id_outlet', 'nama_outlet']);
        
        return view('admin.service.index', compact('outlets'));
    }

    /**
     * Get service dashboard data
     */
    public function getData(Request $request)
    {
        try {
            // Get accessible outlets for current user
            $accessibleOutletIds = $this->getAccessibleOutletIds();
            
            $outletIds = $request->input('outlet_ids', []);
            $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            // Validate outlet access if specific outlets requested
            if (!empty($outletIds)) {
                $invalidOutlets = array_diff($outletIds, $accessibleOutletIds);
                if (!empty($invalidOutlets)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke beberapa outlet yang dipilih'
                    ], 403);
                }
                // Use only the requested outlets that user has access to
                $filterOutletIds = array_intersect($outletIds, $accessibleOutletIds);
            } else {
                // If no specific outlets requested, use all accessible outlets
                $filterOutletIds = $accessibleOutletIds;
            }

            $data = [
                'kpi' => $this->getServiceKPI($filterOutletIds, $startDate, $endDate),
                'status_counts' => $this->getServiceStatusCounts($filterOutletIds),
                'recent_invoices' => $this->getRecentServiceInvoices($filterOutletIds, 10),
                'due_soon' => $this->getDueSoonServiceInvoices($filterOutletIds),
                'revenue_trend' => $this->getServiceRevenueTrend($filterOutletIds, $startDate, $endDate),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading Service dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get service KPI metrics
     */
    private function getServiceKPI($filterOutletIds, $startDate, $endDate)
    {
        // Total Revenue
        $totalRevenue = ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
            ->where('status', 'lunas')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total');

        // Total Invoices
        $totalInvoices = ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count();

        // Average Invoice Value
        $avgInvoiceValue = $totalInvoices > 0 ? $totalRevenue / $totalInvoices : 0;

        // Active Customers
        $activeCustomers = ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->distinct('id_member')
            ->count('id_member');

        // Completion Rate
        $completedInvoices = ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
            ->where('status', 'lunas')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count();
        
        $completionRate = $totalInvoices > 0 ? ($completedInvoices / $totalInvoices) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_invoices' => $totalInvoices,
            'avg_invoice_value' => $avgInvoiceValue,
            'active_customers' => $activeCustomers,
            'completion_rate' => $completionRate,
        ];
    }

    /**
     * Get service status counts
     */
    private function getServiceStatusCounts($filterOutletIds)
    {
        return [
            'menunggu' => ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
                ->where('status', 'menunggu')->count(),
            'lunas' => ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
                ->where('status', 'lunas')->count(),
            'gagal' => ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
                ->where('status', 'gagal')->count(),
            'service_berikutnya' => ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
                ->whereNotNull('tanggal_service_berikutnya')
                ->where('tanggal_service_berikutnya', '>=', now())->count(),
        ];
    }

    /**
     * Get recent service invoices
     */
    private function getRecentServiceInvoices($filterOutletIds, $limit = 10)
    {
        return ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
            ->with(['member', 'outlet'])
            ->orderBy('tanggal', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id_service_invoice,
                    'no_invoice' => $invoice->no_invoice,
                    'tanggal' => $invoice->tanggal->format('Y-m-d'),
                    'customer' => $invoice->member->nama ?? '-',
                    'jenis_service' => $invoice->jenis_service,
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'outlet' => $invoice->outlet->nama_outlet ?? '-',
                ];
            });
    }

    /**
     * Get invoices due soon
     */
    private function getDueSoonServiceInvoices($filterOutletIds)
    {
        $tomorrow = now()->addDay();
        
        return ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
            ->where('status', 'menunggu')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $tomorrow)
            ->with(['member', 'outlet'])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function($invoice) {
                $remainingHours = now()->diffInHours($invoice->due_date, false);
                
                return [
                    'id' => $invoice->id_service_invoice,
                    'no_invoice' => $invoice->no_invoice,
                    'customer' => $invoice->member->nama ?? '-',
                    'due_date' => $invoice->due_date->format('Y-m-d H:i'),
                    'remaining_hours' => $remainingHours,
                    'is_overdue' => $remainingHours < 0,
                    'outlet' => $invoice->outlet->nama_outlet ?? '-',
                ];
            });
    }

    /**
     * Get service revenue trend
     */
    private function getServiceRevenueTrend($filterOutletIds, $startDate, $endDate)
    {
        $revenues = ServiceInvoice::whereIn('outlet_id', $filterOutletIds)
            ->where('status', 'lunas')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('DATE(tanggal) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $revenues->map(function($item) {
            return [
                'date' => $item->date,
                'revenue' => $item->revenue,
            ];
        });
    }

    /**
     * Display invoice service page
     */
    public function invoiceIndex(Request $request)
    {
        // Get accessible outlets for current user
        $outletIds = $this->getAccessibleOutletIds();
        $outlets = Outlet::whereIn('id_outlet', $outletIds)
            ->where('is_active', true)
            ->get(['id_outlet', 'nama_outlet']);
        
        // Get selected outlet from accessible outlets
        $selectedOutlet = $request->get('outlet_id');
        if (!$selectedOutlet || !in_array($selectedOutlet, $outletIds)) {
            $selectedOutlet = $outlets->first()->id_outlet ?? null;
        }
        
        // Get members yang memiliki mesin customer di outlet ini (only from accessible outlets)
        $members = Member::whereHas('mesinCustomers', function($query) use ($selectedOutlet) {
            $query->whereHas('ongkosKirim', function($q) use ($selectedOutlet) {
                $q->where('id_outlet', $selectedOutlet);
            });
        })->with('mesinCustomers')->get();
        
        return view('admin.service.invoice.index', compact('selectedOutlet', 'outlets', 'members'));
    }

    /**
     * Display history service page
     */
    public function historyIndex(Request $request)
    {
        // Get accessible outlets for current user
        $outletIds = $this->getAccessibleOutletIds();
        $outlets = Outlet::whereIn('id_outlet', $outletIds)
            ->where('is_active', true)
            ->get(['id_outlet', 'nama_outlet']);
        
        // Get selected outlet from accessible outlets
        $selectedOutlet = $request->get('outlet_id');
        if (!$selectedOutlet || !in_array($selectedOutlet, $outletIds)) {
            $selectedOutlet = $outlets->first()->id_outlet ?? null;
        }
        
        $status = $request->get('status', 'terkini');
        
        return view('admin.service.history.index', compact('selectedOutlet', 'outlets', 'status'));
    }

    /**
     * Get history data for DataTables
     */
    public function getHistoryData(Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletIds = $request->input('outlet_ids', []);
        $status = $request->get('status', 'terkini');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        // Validate outlet access if specific outlets requested
        if (!empty($outletIds)) {
            $invalidOutlets = array_diff($outletIds, $accessibleOutletIds);
            if (!empty($invalidOutlets)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke beberapa outlet yang dipilih'
                ], 403);
            }
            // Use only the requested outlets that user has access to
            $filterOutletIds = array_intersect($outletIds, $accessibleOutletIds);
        } else {
            // Fallback to single outlet_id for backward compatibility
            $outletId = $request->get('outlet_id');
            if ($outletId && in_array($outletId, $accessibleOutletIds)) {
                $filterOutletIds = [$outletId];
            } else {
                // Use all accessible outlets as default
                $filterOutletIds = $accessibleOutletIds;
            }
        }

        if (empty($filterOutletIds)) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }
        
        $query = ServiceInvoice::with(['member', 'user', 'mesinCustomer.ongkosKirim', 'outlet'])
            ->whereIn('outlet_id', $filterOutletIds);
        
        // Filter berdasarkan status tab
        switch ($status) {
            case 'menunggu':
                $query->where('status', 'menunggu');
                break;
            case 'lunas':
                $query->where('status', 'lunas');
                break;
            case 'gagal':
                $query->where('status', 'gagal');
                break;
            case 'service-berikutnya':
                $query->whereNotNull('tanggal_service_berikutnya')
                    ->where('tanggal_service_berikutnya', '>=', now())
                    ->orderBy('tanggal_service_berikutnya', 'asc');
                break;
            case 'terkini':
            default:
                // Semua data
                break;
        }
        
        // Filter berdasarkan tanggal
        if ($start_date && $end_date) {
            $query->whereBetween('tanggal', [$start_date, $end_date]);
        } elseif ($start_date) {
            $query->where('tanggal', '>=', $start_date);
        } elseif ($end_date) {
            $query->where('tanggal', '<=', $end_date);
        }
        
        // Sorting
        if ($status === 'menunggu') {
            $query->orderBy('due_date', 'asc');
        } else {
            $query->orderBy('tanggal', 'desc');
        }
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_formatted', function ($row) {
                return $row->tanggal->format('d/m/Y');
            })
            ->addColumn('customer_display', function ($row) {
                if ($row->member) {
                    $kode = $row->getMemberCodeWithPrefix();
                    return $row->member->nama . '<br><small class="text-gray-500">' . $kode . '</small>';
                }
                return '-';
            })
            ->addColumn('total_formatted', function ($row) {
                if ($row->is_garansi) {
                    return '<span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">GARANSI</span>';
                }
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($row) {
                $colors = [
                    'menunggu' => 'yellow',
                    'lunas' => 'green',
                    'gagal' => 'red'
                ];
                $color = $colors[$row->status] ?? 'gray';
                
                $badge = '<span class="px-2 py-1 text-xs font-semibold text-'.$color.'-800 bg-'.$color.'-100 rounded-full">' . ucfirst($row->status) . '</span>';
                
                if ($row->id_invoice_sebelumnya && $row->service_lanjutan_ke > 0) {
                    $badge .= '<br><small class="text-gray-500">Lanjutan ke-' . $row->service_lanjutan_ke . '</small>';
                }
                
                return $badge;
            })
            ->addColumn('sisa_hari', function ($row) use ($status) {
                if ($status === 'service-berikutnya') {
                    if (!$row->tanggal_service_berikutnya) return '-';
                    
                    $now = now();
                    $serviceDate = Carbon::parse($row->tanggal_service_berikutnya)->startOfDay();
                    $today = $now->copy()->startOfDay();
                    
                    if ($serviceDate < $today) {
                        $hariTerlambat = $today->diffInDays($serviceDate);
                        return '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Terlambat ' . $hariTerlambat . ' hari</span>';
                    }
                    
                    $sisaHari = $today->diffInDays($serviceDate);
                    
                    if ($sisaHari === 0) {
                        return '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Hari Ini</span>';
                    } elseif ($sisaHari === 1) {
                        return '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Besok</span>';
                    } else {
                        return '<span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">Sisa ' . $sisaHari . ' hari</span>';
                    }
                }

                if ($row->status !== 'menunggu') {
                    return '<span class="text-gray-500">-</span>';
                }
                
                if (!$row->due_date) return '-';
                
                $now = now();
                $dueDate = $row->due_date;
                
                if ($dueDate < $now) {
                    $totalJamTerlambat = $dueDate->diffInHours($now);
                    
                    if ($totalJamTerlambat < 24) {
                        return '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Terlambat ' . $totalJamTerlambat . ' jam</span>';
                    }
                    
                    $hariTerlambat = floor($totalJamTerlambat / 24);
                    $jamTerlambat = $totalJamTerlambat % 24;
                    
                    return '<span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Terlambat ' . $hariTerlambat . ' hari ' . $jamTerlambat . ' jam</span>';
                }
                
                $totalSisaJam = $now->diffInHours($dueDate, false);
                
                if ($totalSisaJam < 24) {
                    return '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Sisa ' . $totalSisaJam . ' jam</span>';
                }
                
                $sisaHari = floor($totalSisaJam / 24);
                $sisaJam = $totalSisaJam % 24;
                
                return '<span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Sisa ' . $sisaHari . ' hari ' . $sisaJam . ' jam</span>';
            })
            ->addColumn('aksi', function ($row) use ($status) {
                $btn = '<div class="flex gap-1">';
                
                if($status === 'lunas' && $row->status === 'lunas') {
                    $btn .= '<button onclick="jadwalkanServiceBerikutnya(' . $row->id_service_invoice . ')" class="px-2 py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700"><i class="fas fa-calendar"></i></button>';
                }
                
                $btn .= '<a href="'.route('admin.service.invoice.print', $row->id_service_invoice).'" target="_blank" class="px-2 py-1 text-xs text-white bg-green-600 rounded hover:bg-green-700"><i class="fas fa-print"></i></a>';
                
                if ($status === 'menunggu' && $row->status === 'menunggu') {
                    $btn .= '<button onclick="updateStatus(' . $row->id_service_invoice . ', \'lunas\')" class="px-2 py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700"><i class="fas fa-check"></i></button>';
                    $btn .= '<button onclick="updateStatus(' . $row->id_service_invoice . ', \'gagal\')" class="px-2 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700"><i class="fas fa-times"></i></button>';
                }
                
                $btn .= '<button onclick="deleteInvoice(' . $row->id_service_invoice . ')" class="px-2 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700"><i class="fas fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['customer_display', 'total_formatted', 'status_badge', 'sisa_hari', 'aksi'])
            ->make(true);
    }

    /**
     * Display ongkir page
     */
    public function ongkirIndex(Request $request)
    {
        // Get accessible outlets for current user
        $outletIds = $this->getAccessibleOutletIds();
        $outlets = Outlet::whereIn('id_outlet', $outletIds)
            ->where('is_active', true)
            ->get(['id_outlet', 'nama_outlet']);
        
        // Get selected outlet from accessible outlets
        $selectedOutlet = $request->get('outlet_id');
        if (!$selectedOutlet || !in_array($selectedOutlet, $outletIds)) {
            $selectedOutlet = $outlets->first()->id_outlet ?? null;
        }
        
        return view('admin.service.ongkir.index', compact('selectedOutlet', 'outlets'));
    }

    /**
     * Get ongkir data for DataTables or JSON
     */
    public function getOngkirData(Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletId = $request->get('outlet_id');
        
        // Validate outlet access
        if ($outletId && !in_array($outletId, $accessibleOutletIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini'
            ], 403);
        }
        
        // Use first accessible outlet if no outlet specified
        if (!$outletId) {
            $outletId = $accessibleOutletIds[0] ?? null;
        }
        
        $search = $request->get('search', '');
        
        // Check if this is a DataTables request
        if ($request->has('draw')) {
            $ongkosKirim = OngkosKirim::where('id_outlet', $outletId);
            
            return DataTables::of($ongkosKirim)
                ->addIndexColumn()
                ->addColumn('harga_formatted', function ($row) {
                    return 'Rp ' . number_format($row->harga, 0, ',', '.');
                })
                ->addColumn('aksi', function ($row) {
                    return '<div class="flex gap-2 justify-center">
                        <button onclick="editOngkir(' . $row->id_ongkir . ')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                            <i class="bx bx-edit text-lg"></i>
                        </button>
                        <button onclick="deleteOngkir(' . $row->id_ongkir . ')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                            <i class="bx bx-trash text-lg"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        
        // Return JSON for Alpine.js
        $query = OngkosKirim::query();
        
        if ($outletId && $outletId !== 'ALL') {
            $query->where('id_outlet', $outletId);
        }
        
        if ($search) {
            $query->where('daerah', 'like', '%' . $search . '%');
        }
        
        $data = $query->orderBy('daerah', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store ongkir
     */
    public function storeOngkir(Request $request)
    {
        $request->validate([
            'daerah' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'id_outlet' => 'required|exists:outlets,id_outlet'
        ]);

        // Validate outlet access
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        if (!in_array($request->id_outlet, $accessibleOutletIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini'
            ], 403);
        }

        $data = $request->all();
        // Set nama_tujuan sama dengan daerah jika tidak ada
        if (!isset($data['nama_tujuan']) || empty($data['nama_tujuan'])) {
            $data['nama_tujuan'] = $data['daerah'];
        }
        // Set biaya sama dengan harga jika tidak ada
        if (!isset($data['biaya']) || empty($data['biaya'])) {
            $data['biaya'] = $data['harga'];
        }

        OngkosKirim::create($data);
        
        return response()->json(['success' => true, 'message' => 'Data ongkos kirim berhasil disimpan']);
    }

    /**
     * Get ongkir by ID
     */
    public function getOngkir($id)
    {
        $ongkir = OngkosKirim::findOrFail($id);
        return response()->json($ongkir);
    }

    /**
     * Update ongkir
     */
    public function updateOngkir(Request $request, $id)
    {
        $request->validate([
            'daerah' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0'
        ]);

        $ongkir = OngkosKirim::findOrFail($id);
        
        $data = $request->only(['daerah', 'harga']);
        // Set nama_tujuan sama dengan daerah jika tidak ada
        if (!isset($data['nama_tujuan']) || empty($data['nama_tujuan'])) {
            $data['nama_tujuan'] = $data['daerah'];
        }
        // Set biaya sama dengan harga jika tidak ada
        if (!isset($data['biaya']) || empty($data['biaya'])) {
            $data['biaya'] = $data['harga'];
        }
        
        $ongkir->update($data);
        
        return response()->json(['success' => true, 'message' => 'Data ongkos kirim berhasil diupdate']);
    }

    /**
     * Delete ongkir
     */
    public function deleteOngkir($id)
    {
        $ongkir = OngkosKirim::findOrFail($id);
        $ongkir->delete();
        
        return response()->json(['success' => true, 'message' => 'Data ongkos kirim berhasil dihapus']);
    }

    /**
     * Display mesin customer page
     */
    public function mesinIndex(Request $request)
    {
        // Get accessible outlets for current user
        $outletIds = $this->getAccessibleOutletIds();
        $outlets = Outlet::whereIn('id_outlet', $outletIds)
            ->where('is_active', true)
            ->get(['id_outlet', 'nama_outlet']);
        
        // Get selected outlet from accessible outlets
        $selectedOutlet = $request->get('outlet_id');
        if (!$selectedOutlet || !in_array($selectedOutlet, $outletIds)) {
            $selectedOutlet = $outlets->first()->id_outlet ?? null;
        }
        
        // Get members only from accessible outlets
        $members = Member::whereIn('id_outlet', $outletIds)->get();
        $ongkosKirim = OngkosKirim::where('id_outlet', $selectedOutlet)->get();
        $produks = Produk::where('id_outlet', $selectedOutlet)->where('is_active', true)->get();
        
        return view('admin.service.mesin.index', compact('selectedOutlet', 'outlets', 'members', 'ongkosKirim', 'produks'));
    }

    /**
     * Get mesin customer data for DataTables or JSON
     */
    public function getMesinData(Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletId = $request->get('outlet_id');
        
        // Validate outlet access
        if ($outletId && !in_array($outletId, $accessibleOutletIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini'
            ], 403);
        }
        
        // Use first accessible outlet if no outlet specified
        if (!$outletId) {
            $outletId = $accessibleOutletIds[0] ?? null;
        }
        
        $search = $request->get('search', '');
        
        // Check if this is a DataTables request
        if ($request->has('draw')) {
            $mesinCustomers = MesinCustomer::with(['member', 'ongkosKirim', 'produk'])
                ->whereHas('ongkosKirim', function($q) use ($outletId) {
                    $q->where('id_outlet', $outletId);
                });
            
            return DataTables::of($mesinCustomers)
                ->addIndexColumn()
                ->addColumn('member_name', function ($row) {
                    return $row->member ? $row->member->nama : '-';
                })
                ->addColumn('ongkir_daerah', function ($row) {
                    return $row->ongkosKirim ? $row->ongkosKirim->daerah : '-';
                })
                ->addColumn('produk_list', function ($row) {
                    $produkList = '';
                    foreach ($row->produk as $produk) {
                        $biayaService = $produk->pivot->biaya_service ? ' (Rp ' . number_format($produk->pivot->biaya_service, 0, ',', '.') . ')' : '';
                        $closingType = $produk->pivot->closing_type ? ' - ' . ucfirst($produk->pivot->closing_type) : '';
                        $produkList .= '<span class="inline-block px-2 py-1 mb-1 mr-1 text-xs text-blue-800 bg-blue-100 rounded">' . $produk->nama_produk . $biayaService . $closingType . '</span> ';
                    }
                    return $produkList ?: '-';
                })
                ->addColumn('aksi', function ($row) {
                    return '<div class="flex gap-2 justify-center">
                        <button onclick="editMesin(' . $row->id . ')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                            <i class="bx bx-edit text-lg"></i>
                        </button>
                        <button onclick="deleteMesin(' . $row->id . ')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                            <i class="bx bx-trash text-lg"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['produk_list', 'aksi'])
                ->make(true);
        }
        
        // Return JSON for Alpine.js
        $query = MesinCustomer::with(['member', 'ongkosKirim', 'produk']);
        
        if ($outletId && $outletId !== 'ALL') {
            $query->whereHas('ongkosKirim', function($q) use ($outletId) {
                $q->where('id_outlet', $outletId);
            });
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_mesin', 'like', '%' . $search . '%')
                  ->orWhereHas('member', function($q2) use ($search) {
                      $q2->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $data = $query->orderBy('id', 'desc')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'kode_mesin' => $item->kode_mesin ?? '-',
                'customer_name' => $item->member ? $item->member->nama : '-',
                'daerah' => $item->ongkosKirim ? $item->ongkosKirim->daerah : '-',
                'ongkir_harga' => $item->ongkosKirim ? $item->ongkosKirim->harga : 0,
                'produk' => $item->produk->map(function($p) {
                    return [
                        'id' => $p->id_produk,
                        'nama' => $p->nama_produk,
                        'biaya_service' => $p->pivot->biaya_service ?? 0,
                        'jumlah' => $p->pivot->jumlah ?? 1,
                        'closing_type' => $p->pivot->closing_type ?? 'jual_putus'
                    ];
                })
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store mesin customer
     */
    public function storeMesin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_member' => 'required|exists:member,id_member',
            'id_ongkir' => 'required|exists:ongkos_kirim,id_ongkir',
            'produk' => 'required|array|min:1',
            'produk.*' => 'required|exists:produk,id_produk',
            'jumlah_produk' => 'required|array|min:1',
            'jumlah_produk.*' => 'required|numeric|min:1',
            'biaya_service_produk' => 'required|array|min:1',
            'biaya_service_produk.*' => 'required|numeric|min:0',
            'closing_type_produk' => 'required|array|min:1',
            'closing_type_produk.*' => 'required|in:jual_putus,deposit'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::transaction(function () use ($request) {
            // Generate kode_mesin otomatis
            $lastMesin = MesinCustomer::orderBy('id', 'desc')->first();
            $nextNumber = $lastMesin ? $lastMesin->id + 1 : 1;
            $kode_mesin = 'MSN-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            // Generate nama_mesin dari produk yang dipilih
            $produkNames = Produk::whereIn('id_produk', $request->produk)->pluck('nama_produk')->toArray();
            $nama_mesin = implode(', ', $produkNames);
            
            $mesinCustomer = MesinCustomer::create([
                'id_member' => $request->id_member,
                'id_ongkir' => $request->id_ongkir,
                'kode_mesin' => $kode_mesin,
                'nama_mesin' => $nama_mesin
            ]);
            
            $produkData = [];
            foreach ($request->produk as $index => $produkId) {
                $produkData[$produkId] = [
                    'jumlah' => $request->jumlah_produk[$index] ?? 1,
                    'biaya_service' => $request->biaya_service_produk[$index] ?? 0,
                    'closing_type' => $request->closing_type_produk[$index] ?? 'jual_putus'
                ];
            }
            
            $mesinCustomer->produk()->attach($produkData);
        });

        return response()->json(['success' => true, 'message' => 'Data mesin customer berhasil disimpan']);
    }

    /**
     * Get mesin customer by ID
     */
    public function getMesin($id)
    {
        $mesinCustomer = MesinCustomer::with(['member', 'produk'])->findOrFail($id);
        return response()->json($mesinCustomer);
    }

    /**
     * Update mesin customer
     */
    public function updateMesin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_member' => 'required|exists:member,id_member',
            'id_ongkir' => 'required|exists:ongkos_kirim,id_ongkir',
            'produk' => 'required|array|min:1',
            'produk.*' => 'required|exists:produk,id_produk',
            'jumlah_produk' => 'required|array|min:1',
            'jumlah_produk.*' => 'required|numeric|min:1',
            'biaya_service_produk' => 'required|array|min:1',
            'biaya_service_produk.*' => 'required|numeric|min:0',
            'closing_type_produk' => 'required|array|min:1',
            'closing_type_produk.*' => 'required|in:jual_putus,deposit'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::transaction(function () use ($request, $id) {
            $mesinCustomer = MesinCustomer::findOrFail($id);
            
            // Generate nama_mesin dari produk yang dipilih
            $produkNames = Produk::whereIn('id_produk', $request->produk)->pluck('nama_produk')->toArray();
            $nama_mesin = implode(', ', $produkNames);
            
            $mesinCustomer->update([
                'id_member' => $request->id_member,
                'id_ongkir' => $request->id_ongkir,
                'nama_mesin' => $nama_mesin
            ]);
            
            $produkData = [];
            foreach ($request->produk as $index => $produkId) {
                $produkData[$produkId] = [
                    'jumlah' => $request->jumlah_produk[$index] ?? 1,
                    'biaya_service' => $request->biaya_service_produk[$index] ?? 0,
                    'closing_type' => $request->closing_type_produk[$index] ?? 'jual_putus'
                ];
            }
            
            $mesinCustomer->produk()->sync($produkData);
        });

        return response()->json(['success' => true, 'message' => 'Data mesin customer berhasil diupdate']);
    }

    /**
     * Delete mesin customer
     */
    public function deleteMesin($id)
    {
        $mesinCustomer = MesinCustomer::findOrFail($id);
        $mesinCustomer->delete();
        
        return response()->json(['success' => true, 'message' => 'Data mesin customer berhasil dihapus']);
    }

    /**
     * Get mesin customer by member
     */
    public function getMesinByMember($id_member, Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletId = $request->get('outlet_id');
        
        // Validate outlet access
        if ($outletId && !in_array($outletId, $accessibleOutletIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini'
            ], 403);
        }
        
        // Use first accessible outlet if no outlet specified
        if (!$outletId) {
            $outletId = $accessibleOutletIds[0] ?? null;
        }
        
        $mesinCustomers = MesinCustomer::with(['produk.satuan', 'ongkosKirim'])
            ->where('id_member', $id_member)
            ->whereHas('ongkosKirim', function($q) use ($outletId) {
                $q->where('id_outlet', $outletId);
            })
            ->get()
            ->map(function($mesin) {
                return [
                    'id' => $mesin->id,
                    'id_mesin_customer' => $mesin->id, // For backward compatibility
                    'id_member' => $mesin->id_member,
                    'closing_type' => $mesin->closing_type,
                    'kode_mesin' => $mesin->kode_mesin,
                    'nama_mesin' => $mesin->nama_mesin,
                    'produk' => $mesin->produk->map(function($p) {
                        return [
                            'id_produk' => $p->id_produk,
                            'nama_produk' => $p->nama_produk,
                            'pivot' => [
                                'biaya_service' => $p->pivot->biaya_service ?? 0,
                                'jumlah' => $p->pivot->jumlah ?? 1,
                            ],
                            'satuan' => $p->satuan ? [
                                'nama_satuan' => $p->satuan->nama_satuan
                            ] : null
                        ];
                    }),
                    'ongkos_kirim' => $mesin->ongkosKirim ? [
                        'id_ongkir' => $mesin->ongkosKirim->id_ongkir,
                        'daerah' => $mesin->ongkosKirim->daerah,
                        'harga' => $mesin->ongkosKirim->harga,
                    ] : null
                ];
            });
        
        return response()->json($mesinCustomers);
    }

    /**
     * Search customers for datalist autocomplete
     */
    public function searchCustomers(Request $request)
    {
        $searchTerm = $request->get('search', $request->get('q', ''));
        
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletId = $request->get('outlet_id');
        
        // Validate outlet access
        if ($outletId && !in_array($outletId, $accessibleOutletIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini'
            ], 403);
        }
        
        // Use first accessible outlet if no outlet specified
        if (!$outletId) {
            $outletId = $accessibleOutletIds[0] ?? null;
        }
        
        $query = Member::withCount(['mesinCustomers as mesin_count'])
            ->with(['mesinCustomers' => function($query) {
                $query->with(['produk' => function($q) {
                    $q->withPivot('closing_type');
                }]);
            }])
            ->where('id_outlet', $outletId) // Filter members by outlet
            ->orderBy('nama', 'asc');
        
        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('alamat', 'like', '%' . $searchTerm . '%')
                  ->orWhere('telepon', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kode_member', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $customers = $query->limit(50)->get();
        
        // Process customers to determine closing type
        $processedCustomers = $customers->map(function($customer) {
            $closingTypes = [];
            
            foreach ($customer->mesinCustomers as $mesinCustomer) {
                foreach ($mesinCustomer->produk as $produk) {
                    $closingType = $produk->pivot->closing_type ?? 'jual_putus';
                    if (!in_array($closingType, $closingTypes)) {
                        $closingTypes[] = $closingType;
                    }
                }
            }
            
            // Determine prefix based on closing types
            $prefix = '';
            if (in_array('deposit', $closingTypes) && in_array('jual_putus', $closingTypes)) {
                $prefix = 'JD'; // Mixed
            } elseif (in_array('deposit', $closingTypes)) {
                $prefix = 'D'; // Deposit only
            } else {
                $prefix = 'JP'; // Jual putus only or default
            }
            
            $customer->closing_type_prefix = $prefix;
            $customer->closing_types = $closingTypes;
            
            return $customer;
        });
        
        return response()->json([
            'success' => true,
            'customers' => $processedCustomers,
            'count' => $customers->count()
        ]);
    }

    /**
     * Get produk list for mesin customer
     */
    public function getProdukList(Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletId = $request->get('outlet_id');
        
        // Validate outlet access
        if ($outletId && !in_array($outletId, $accessibleOutletIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke outlet ini'
            ], 403);
        }
        
        // Use first accessible outlet if no outlet specified
        if (!$outletId) {
            $outletId = $accessibleOutletIds[0] ?? null;
        }
        
        $produk = Produk::where('id_outlet', $outletId)
            ->where('is_active', true)
            ->orderBy('nama_produk', 'asc')
            ->get(['id_produk', 'nama_produk']);

        return response()->json([
            'success' => true,
            'data' => $produk->map(function($item) {
                return [
                    'id' => $item->id_produk,
                    'nama' => $item->nama_produk
                ];
            })
        ]);
    }

    /**
     * Store invoice
     */
    public function storeInvoice(Request $request)
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
            'outlet_id' => 'nullable|exists:outlets,id_outlet',
            'tanggal' => 'required|date',
            'tanggal_mulai_service' => 'required|date',
            'tanggal_selesai_service' => 'required|date|after_or_equal:tanggal_mulai_service',
            'id_member' => 'required|exists:member,id_member',
            'id_mesin_customer' => 'nullable|exists:mesin_customer,id',
            'jenis_service' => 'required|string|in:Service,Maintenance,Pembelian Sparepart,Lainnya',
            'keterangan_service' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.deskripsi' => 'required|string|max:255',
            'items.*.keterangan' => 'nullable|string|max:255',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.tipe' => 'required|in:produk,ongkir,teknisi,sparepart,lainnya',
            'jumlah_teknisi' => 'nullable|numeric|min:0',
            'jumlah_jam' => 'nullable|numeric|min:0',
            'biaya_teknisi' => 'nullable|numeric|min:0',
            'is_garansi' => 'sometimes|boolean',
            'diskon' => 'required|numeric|min:0',
            'total_setelah_diskon' => 'required|numeric|min:0',
            'tanggal_service_berikutnya' => 'nullable|date|after_or_equal:tanggal_selesai_service',
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
                $invoiceNumber = \App\Models\InvoiceServiceCounter::generateInvoiceNumber();

                $subtotal = 0;
                $statusFix = 'menunggu';

                foreach ($request->items as $item) {
                    $itemSubtotal = $parseNumber($item['subtotal']);
                    $subtotal += $itemSubtotal;
                }

                $tanggalWithTime = Carbon::parse($request->tanggal . ' ' . now()->format('H:i:s'));
                $dueDate = Carbon::parse($request->tanggal_selesai_service)->addDays(7);

                if($request->is_garansi) {
                    $statusFix = 'lunas';
                }

                $tanggalServiceBerikutnya = null;
                if ($request->has('tanggal_service_berikutnya') && $request->tanggal_service_berikutnya) {
                    $tanggalServiceBerikutnya = $request->tanggal_service_berikutnya;
                }

                // Get outlet_id from request or from selected outlet filter
                $firstOutlet = Outlet::where('is_active', true)->orderBy('id_outlet')->first();
                $defaultOutlet = $firstOutlet ? $firstOutlet->id_outlet : 1;
                $outletId = $request->outlet_id ?? $request->get('outlet_id', auth()->user()->outlet_id ?? $defaultOutlet);

                $invoice = ServiceInvoice::create([
                    'no_invoice' => $invoiceNumber,
                    'tanggal' => $tanggalWithTime,
                    'tanggal_mulai_service' => $request->tanggal_mulai_service,
                    'tanggal_selesai_service' => $request->tanggal_selesai_service,
                    'tanggal_service_berikutnya' => $tanggalServiceBerikutnya,
                    'id_member' => $request->id_member,
                    'id_mesin_customer' => $request->id_mesin_customer,
                    'id_user' => auth()->user()->id,
                    'outlet_id' => $outletId, // Save outlet_id from filter
                    'is_garansi' => $request->is_garansi ?? false,
                    'diskon' => $parseNumber($request->diskon),
                    'total_sebelum_diskon' => $subtotal,
                    'jenis_service' => $request->jenis_service,
                    'keterangan_service' => $request->keterangan_service,
                    'jumlah_teknisi' => $request->jumlah_teknisi ?? 0,
                    'jumlah_jam' => $request->jumlah_jam ?? 0,
                    'biaya_teknisi' => $parseNumber($request->biaya_teknisi ?? 0),
                    'total' => $parseNumber($request->total_setelah_diskon),
                    'status' => $statusFix,
                    'due_date' => $dueDate
                ]);

                foreach ($request->items as $item) {
                    $harga = $parseNumber($item['harga']);
                    $diskon = $parseNumber($item['diskon'] ?? 0);
                    $hargaSetelahDiskon = $harga - $diskon;
                    if ($hargaSetelahDiskon < 0) $hargaSetelahDiskon = 0;

                    ServiceInvoiceItem::create([
                        'id_service_invoice' => $invoice->id_service_invoice,
                        'id_produk' => !empty($item['id_produk']) ? $item['id_produk'] : null,
                        'deskripsi' => $item['deskripsi'],
                        'keterangan' => $item['keterangan'] ?? null,
                        'kuantitas' => $item['kuantitas'],
                        'satuan' => $item['satuan'] ?? null,
                        'harga' => $parseNumber($item['harga']),
                        'diskon' => $diskon,
                        'harga_setelah_diskon' => $hargaSetelahDiskon,
                        'subtotal' => $parseNumber($item['subtotal']),
                        'tipe' => $item['tipe'],
                    ]);

                    // Kurangi stok sparepart jika tipe adalah sparepart
                    if ($item['tipe'] === 'sparepart' && !empty($item['id_sparepart'])) {
                        $sparepart = Sparepart::find($item['id_sparepart']);
                        
                        if ($sparepart) {
                            $kuantitas = (int) $item['kuantitas'];
                            
                            // Cek apakah stok mencukupi
                            if (!$sparepart->isStokMencukupi($kuantitas)) {
                                throw new \Exception("Stok {$sparepart->nama_sparepart} tidak mencukupi. Stok tersedia: {$sparepart->stok}");
                            }
                            
                            $stokLama = $sparepart->stok;
                            
                            // Kurangi stok
                            $sparepart->kurangiStok($kuantitas);
                            
                            // Log perubahan stok
                            SparepartLog::create([
                                'id_sparepart' => $sparepart->id_sparepart,
                                'id_user' => auth()->id(),
                                'tipe_perubahan' => 'stok',
                                'nilai_lama' => $stokLama,
                                'nilai_baru' => $sparepart->stok,
                                'selisih' => -$kuantitas,
                                'keterangan' => "Pemakaian untuk service invoice {$invoiceNumber}"
                            ]);
                        }
                    }
                }
            });

            return response()->json([
                'success' => true, 
                'message' => 'Invoice berhasil dibuat',
                'redirect' => route('admin.service.history.index')
            ]);

        } catch (\Exception $e) {
            \Log::error('Invoice store error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print invoice
     */
    public function printInvoice($id)
    {
        try {
            $invoice = ServiceInvoice::with(['member', 'mesinCustomer.produk', 'items', 'outlet'])->findOrFail($id);
            
            // Get company settings for the invoice's outlet
            $outletId = $invoice->outlet_id ?? 1;
            $companySetting = \App\Models\CompanySetting::getOrCreateForOutlet($outletId);
            
            // Transform company setting to match view expectations
            $companySettings = [
                'company_name' => $companySetting->company_name ?? 'Nama Perusahaan',
                'company_address' => $companySetting->company_address,
                'formatted_address' => $companySetting->formatted_address,
                'company_phone' => $companySetting->company_phone,
                'company_email' => $companySetting->company_email,
                'logo_url' => $companySetting->logo_url, // This uses the accessor
                'bank_name' => $companySetting->bank_name,
                'bank_account_number' => $companySetting->bank_account_number,
                'bank_account_name' => $companySetting->bank_account_name,
                'npwp' => $companySetting->npwp,
                'nib' => $companySetting->nib,
                'siup' => $companySetting->siup,
                'tdp' => $companySetting->tdp,
            ];
            
            $pdf = Pdf::loadView('admin.service.invoice.print', compact('invoice', 'companySettings'));
            $pdf->setPaper('A4', 'portrait');
            
            $safeFilename = 'invoice-service-' . str_replace('/', '-', $invoice->no_invoice);
            
            return $pdf->stream($safeFilename . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Print invoice error: ' . $e->getMessage(), [
                'invoice_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Gagal generate PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update invoice status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,lunas,gagal',
            'jenis_pembayaran' => 'required_if:status,lunas|in:cash,transfer',
            'penerima' => 'required_if:status,lunas|string|max:255',
            'tanggal_pembayaran' => 'required_if:status,lunas|date',
            'catatan_pembayaran' => 'nullable|string',
            'tanggal_service_berikutnya' => 'nullable|date|after:today'
        ]);

        try {
            $invoice = ServiceInvoice::findOrFail($id);
            
            $updateData = [
                'status' => $request->status,
                'catatan' => $request->catatan
            ];

            if ($request->status === 'lunas') {
                $updateData['jenis_pembayaran'] = $request->jenis_pembayaran;
                $updateData['penerima'] = $request->penerima;
                $updateData['tanggal_pembayaran'] = $request->tanggal_pembayaran;
                $updateData['catatan_pembayaran'] = $request->catatan_pembayaran;

                if ($request->has('tanggal_service_berikutnya') && $request->tanggal_service_berikutnya) {
                    $updateData['tanggal_service_berikutnya'] = $request->tanggal_service_berikutnya;
                }
            }

            $invoice->update($updateData);

            return response()->json([
                'success' => true, 
                'message' => 'Status invoice berhasil diupdate menjadi ' . $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete invoice
     */
    public function deleteInvoice($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $invoice = ServiceInvoice::findOrFail($id);
                ServiceInvoiceItem::where('id_service_invoice', $id)->delete();
                $invoice->delete();
            });
            
            return response()->json(['success' => true, 'message' => 'Invoice berhasil dihapus']);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status counts
     */
    public function getStatusCounts(Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        // Support multiple outlet IDs
        $outletIds = $request->get('outlet_ids', []);
        if (empty($outletIds)) {
            // Get first available outlet as default instead of hardcoded 1
            $firstOutlet = Outlet::where('is_active', true)->orderBy('id_outlet')->first();
            $defaultOutlet = $firstOutlet ? $firstOutlet->id_outlet : 1;
            $outletIds = [$request->get('outlet_id', auth()->user()->outlet_id ?? $defaultOutlet)];
        }
        
        // Validate outlet access
        $filterOutletIds = array_intersect($outletIds, $accessibleOutletIds);
        
        try {
            $counts = [
                'menunggu' => ServiceInvoice::where('status', 'menunggu')
                    ->whereIn('outlet_id', $filterOutletIds)->count(),
                'lunas' => ServiceInvoice::where('status', 'lunas')
                    ->whereIn('outlet_id', $filterOutletIds)->count(),
                'gagal' => ServiceInvoice::where('status', 'gagal')
                    ->whereIn('outlet_id', $filterOutletIds)->count(),
                'service_berikutnya' => ServiceInvoice::whereNotNull('tanggal_service_berikutnya')
                    ->where('tanggal_service_berikutnya', '>=', now()->startOfDay())
                    ->whereIn('outlet_id', $filterOutletIds)->count(),
            ];

            return response()->json($counts);
        } catch (\Exception $e) {
            return response()->json([
                'menunggu' => 0,
                'lunas' => 0,
                'gagal' => 0,
                'service_berikutnya' => 0
            ]);
        }
    }

    /**
     * Schedule next service
     */
    public function scheduleNextService(Request $request, $id)
    {
        $request->validate([
            'tanggal_service_berikutnya' => 'required|date|after:today'
        ]);

        try {
            $invoice = ServiceInvoice::findOrFail($id);
            
            $invoice->update([
                'tanggal_service_berikutnya' => $request->tanggal_service_berikutnya
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Service berikutnya berhasil dijadwalkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menjadwalkan service berikutnya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice settings
     */
    public function getInvoiceSettings()
    {
        $counter = \App\Models\InvoiceServiceCounter::first();
        
        if (!$counter) {
            $counter = \App\Models\InvoiceServiceCounter::create([
                'last_number' => 0,
                'year' => date('Y'),
                'month' => date('n'),
                'prefix' => 'BBN.INV'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'settings' => [
                'prefix' => $counter->prefix,
                'last_number' => $counter->last_number,
                'year' => $counter->year
            ]
        ]);
    }
    
    /**
     * Save invoice settings
     */
    public function saveInvoiceSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prefix' => 'required|string|max:50',
            'last_number' => 'required|integer|min:0',
            'year' => 'required|integer|min:2020|max:2099'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $counter = \App\Models\InvoiceServiceCounter::first();
        
        if (!$counter) {
            $counter = \App\Models\InvoiceServiceCounter::create([
                'prefix' => $request->prefix,
                'last_number' => $request->last_number,
                'year' => $request->year,
                'month' => date('n')
            ]);
        } else {
            $counter->update([
                'prefix' => $request->prefix,
                'last_number' => $request->last_number,
                'year' => $request->year,
                'month' => date('n')
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Setting berhasil disimpan',
            'settings' => [
                'prefix' => $counter->prefix,
                'last_number' => $counter->last_number,
                'year' => $counter->year
            ]
        ]);
    }

    /**
     * Export history to Excel
     */
    public function exportHistory(Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletIds = $request->input('outlet_ids', []);

        // Validate outlet access if specific outlets requested
        if (!empty($outletIds)) {
            $invalidOutlets = array_diff($outletIds, $accessibleOutletIds);
            if (!empty($invalidOutlets)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke beberapa outlet yang dipilih'
                ], 403);
            }
            // Use only the requested outlets that user has access to
            $filterOutletIds = array_intersect($outletIds, $accessibleOutletIds);
        } else {
            // Fallback to single outlet_id for backward compatibility
            $outletId = $request->get('outlet_id');
            if ($outletId && in_array($outletId, $accessibleOutletIds)) {
                $filterOutletIds = [$outletId];
            } else {
                // Use all accessible outlets as default
                $filterOutletIds = $accessibleOutletIds;
            }
        }

        if (empty($filterOutletIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada outlet yang dapat diakses'
            ], 403);
        }
        
        $status = $request->get('status', 'terkini');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = ServiceInvoice::with(['member', 'user', 'mesinCustomer.ongkosKirim'])
            ->whereIn('outlet_id', $filterOutletIds);
        
        // Filter berdasarkan status
        if ($status !== 'terkini') {
            if ($status === 'service-berikutnya') {
                $query->whereNotNull('tanggal_service_berikutnya')
                    ->where('tanggal_service_berikutnya', '>=', now());
            } else {
                $query->where('status', $status);
            }
        }
        
        // Filter berdasarkan tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }
        
        $invoices = $query->orderBy('tanggal', 'desc')->get();
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ServiceInvoiceExport($invoices), 
            'service-invoices-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export history to PDF
     */
    public function exportHistoryPdf(Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletIds = $request->input('outlet_ids', []);

        // Validate outlet access if specific outlets requested
        if (!empty($outletIds)) {
            $invalidOutlets = array_diff($outletIds, $accessibleOutletIds);
            if (!empty($invalidOutlets)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke beberapa outlet yang dipilih'
                ], 403);
            }
            // Use only the requested outlets that user has access to
            $filterOutletIds = array_intersect($outletIds, $accessibleOutletIds);
        } else {
            // Fallback to single outlet_id for backward compatibility
            $outletId = $request->get('outlet_id');
            if ($outletId && in_array($outletId, $accessibleOutletIds)) {
                $filterOutletIds = [$outletId];
            } else {
                // Use all accessible outlets as default
                $filterOutletIds = $accessibleOutletIds;
            }
        }

        if (empty($filterOutletIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada outlet yang dapat diakses'
            ], 403);
        }
        
        $status = $request->get('status', 'terkini');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = ServiceInvoice::with(['member', 'user', 'mesinCustomer.ongkosKirim', 'items'])
            ->whereIn('outlet_id', $filterOutletIds);
        
        // Filter berdasarkan status
        if ($status !== 'terkini') {
            if ($status === 'service-berikutnya') {
                $query->whereNotNull('tanggal_service_berikutnya')
                    ->where('tanggal_service_berikutnya', '>=', now());
            } else {
                $query->where('status', $status);
            }
        }
        
        // Filter berdasarkan tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }
        
        $invoices = $query->orderBy('tanggal', 'desc')->get();
        
        $pdf = Pdf::loadView('admin.service.history.export', [
            'invoices' => $invoices,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        return $pdf->stream('service-history-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Get invoices that are due soon (within 24 hours)
     */
    public function getDueSoonInvoices(Request $request)
    {
        // Get accessible outlets for current user
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        
        $outletIds = $request->input('outlet_ids', []);

        // Validate outlet access if specific outlets requested
        if (!empty($outletIds)) {
            $invalidOutlets = array_diff($outletIds, $accessibleOutletIds);
            if (!empty($invalidOutlets)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke beberapa outlet yang dipilih'
                ], 403);
            }
            // Use only the requested outlets that user has access to
            $filterOutletIds = array_intersect($outletIds, $accessibleOutletIds);
        } else {
            // Fallback to single outlet_id for backward compatibility
            $outletId = $request->get('outlet_id');
            if ($outletId && in_array($outletId, $accessibleOutletIds)) {
                $filterOutletIds = [$outletId];
            } else {
                // Use all accessible outlets as default
                $filterOutletIds = $accessibleOutletIds;
            }
        }

        if (empty($filterOutletIds)) {
            return response()->json([
                'success' => true,
                'invoices' => []
            ]);
        }
        
        $now = now();
        $tomorrow = $now->copy()->addDay();
        
        // Get invoices dengan status menunggu yang jatuh tempo dalam 24 jam
        $invoices = ServiceInvoice::with(['member', 'mesinCustomer.ongkosKirim'])
            ->where('status', 'menunggu')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $tomorrow)
            ->whereIn('outlet_id', $filterOutletIds)
            ->orderBy('due_date', 'asc')
            ->get();
        
        // Calculate remaining hours for each invoice
        $invoicesWithTime = $invoices->map(function($invoice) use ($now) {
            $dueDate = Carbon::parse($invoice->due_date);
            $remainingHours = $now->diffInHours($dueDate, false);
            
            // Determine time description
            if ($remainingHours < 0) {
                $hoursLate = abs($remainingHours);
                if ($hoursLate < 24) {
                    $timeDescription = "Terlambat {$hoursLate} jam";
                } else {
                    $daysLate = floor($hoursLate / 24);
                    $timeDescription = "Terlambat {$daysLate} hari";
                }
            } elseif ($remainingHours <= 1) {
                $timeDescription = "Sisa {$remainingHours} jam";
            } elseif ($remainingHours <= 24) {
                $timeDescription = "Sisa {$remainingHours} jam";
            } else {
                $days = floor($remainingHours / 24);
                $timeDescription = "Sisa {$days} hari";
            }
            
            return [
                'id_service_invoice' => $invoice->id_service_invoice,
                'no_invoice' => $invoice->no_invoice,
                'tanggal' => $invoice->tanggal->format('Y-m-d'),
                'due_date' => $invoice->due_date->format('Y-m-d H:i:s'),
                'jenis_service' => $invoice->jenis_service,
                'total' => $invoice->total,
                'status' => $invoice->status,
                'remaining_hours' => $remainingHours,
                'time_description' => $timeDescription,
                'member' => [
                    'id_member' => $invoice->member->id_member ?? null,
                    'nama' => $invoice->member->nama ?? '-',
                    'kode_member' => $invoice->member->kode_member ?? '-'
                ]
            ];
        });
        
        return response()->json([
            'success' => true,
            'invoices' => $invoicesWithTime,
            'count' => $invoicesWithTime->count()
        ]);
    }
}
