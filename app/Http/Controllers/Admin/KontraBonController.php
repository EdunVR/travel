<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontraBon;
use App\Models\KontraBonDetail;
use App\Models\Penjualan;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Piutang;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PDF;

class KontraBonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Check permission
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasPermission('sales.kontrabon.view')) {
            abort(403, 'Unauthorized');
        }

        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        $members = Member::all();

        return view('admin.penjualan.kontrabon.index', compact('outlets', 'userOutlets', 'members'));
    }

    public function store(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasPermission('sales.kontrabon.create')) {
            abort(403, 'Unauthorized');
        }

        // LOG: Debug request data - ENHANCED
        Log::info('Kontra Bon Store Request - ENHANCED DEBUG', [
            'all_data' => $request->all(),
            'piutang_ids' => $request->input('piutang_ids'),
            'piutang_ids_count' => is_array($request->input('piutang_ids')) ? count($request->input('piutang_ids')) : 0,
            'has_piutang_ids' => $request->has('piutang_ids'),
            'start_date_filter' => $request->input('start_date_filter'),
            'end_date_filter' => $request->input('end_date_filter'),
            'id_member' => $request->input('id_member'),
            'pembayaran' => $request->input('pembayaran'),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'user_id' => auth()->id()
        ]);

        $request->validate([
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'id_member' => 'required|exists:member,id_member',
            'tanggal_jatuh_tempo' => 'required|date',
            'pembayaran' => 'required|numeric|min:0', // Boleh 0 untuk penagihan tanpa pembayaran
            'piutang_ids' => 'required|array|min:1', // Minimal 1 piutang harus dipilih
            'piutang_ids.*' => 'exists:piutang,id_piutang'
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor kontra bon
            $lastKontraBon = KontraBon::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->orderBy('id_kontra_bon', 'desc')
                ->first();

            $counter = $lastKontraBon ? (int)substr($lastKontraBon->no_kontra_bon, -4) + 1 : 1;
            $noKontraBon = 'KB' . date('Ym') . str_pad($counter, 4, '0', STR_PAD_LEFT);

            // Create kontra bon
            $kontraBon = KontraBon::create([
                'kode_kontra_bon' => $noKontraBon,
                'no_kontra_bon' => $noKontraBon,
                'tanggal' => now(),
                'id_member' => $request->id_member,
                'id_outlet' => $request->id_outlet,
                'id_user' => auth()->id(),
                'total_pembayaran' => $request->pembayaran,
                'total' => $request->pembayaran,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'status' => 'pending',
                'keterangan' => $request->keterangan ?? '',
                'start_date_filter' => $request->start_date_filter,
                'end_date_filter' => $request->end_date_filter
            ]);

            // Create kontra bon details and update piutang
            $totalBayar = $request->pembayaran;
            $sisaBayar = $totalBayar;

            // LOG: Debug piutang_ids yang diterima
            Log::info('Processing Piutang IDs', [
                'piutang_ids' => $request->piutang_ids,
                'count' => count($request->piutang_ids),
                'total_bayar' => $totalBayar
            ]);

            // PERBAIKAN: Jika pembayaran = 0, tetap buat detail untuk piutang yang dicentang
            // Ini untuk use case penagihan hutang tanpa pembayaran langsung
            if ($totalBayar == 0) {
                // Buat detail untuk semua piutang yang dicentang tanpa update status piutang
                foreach ($request->piutang_ids as $piutangId) {
                    $piutang = Piutang::find($piutangId);
                    if (!$piutang) {
                        Log::warning('Piutang not found', ['id_piutang' => $piutangId]);
                        continue;
                    }

                    // Create detail dengan nominal = sisa piutang
                    $detail = KontraBonDetail::create([
                        'id_kontra_bon' => $kontraBon->id_kontra_bon,
                        'id_penjualan' => $piutang->id_penjualan,
                        'nominal' => $piutang->sisa_piutang,
                        'jumlah_bayar' => 0 // Belum ada pembayaran
                    ]);
                    
                    Log::info('Created detail for piutang (zero payment)', [
                        'id_piutang' => $piutangId,
                        'id_penjualan' => $piutang->id_penjualan,
                        'nominal' => $piutang->sisa_piutang,
                        'detail_id' => $detail->id
                    ]);
                }
            } else {
                // Logika normal jika ada pembayaran
                foreach ($request->piutang_ids as $piutangId) {
                    if ($sisaBayar <= 0) break;

                    $piutang = Piutang::find($piutangId);
                    if (!$piutang) {
                        Log::warning('Piutang not found', ['id_piutang' => $piutangId]);
                        continue;
                    }

                    $sisaPiutang = $piutang->sisa_piutang;
                    $bayarPiutang = min($sisaBayar, $sisaPiutang);

                    // Create detail
                    $detail = KontraBonDetail::create([
                        'id_kontra_bon' => $kontraBon->id_kontra_bon,
                        'id_penjualan' => $piutang->id_penjualan,
                        'nominal' => $bayarPiutang,
                        'jumlah_bayar' => $bayarPiutang
                    ]);
                    
                    Log::info('Created detail for piutang (with payment)', [
                        'id_piutang' => $piutangId,
                        'id_penjualan' => $piutang->id_penjualan,
                        'bayar_piutang' => $bayarPiutang,
                        'detail_id' => $detail->id
                    ]);

                    // Update piutang
                    $piutang->update([
                        'jumlah_dibayar' => $piutang->jumlah_dibayar + $bayarPiutang,
                        'sisa_piutang' => $piutang->sisa_piutang - $bayarPiutang,
                        'status' => ($piutang->sisa_piutang - $bayarPiutang <= 0) ? 'lunas' : 'belum_lunas'
                    ]);

                    $sisaBayar -= $bayarPiutang;
                }
            }

            // Update member saldo if there's remaining payment
            if ($sisaBayar > 0 && $request->has('tambahkan_saldo')) {
                $member = Member::find($request->id_member);
                $member->update(['saldo' => $member->saldo + $sisaBayar]);
            }

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kontra bon berhasil dibuat dengan nomor: ' . $noKontraBon,
                    'data' => $kontraBon
                ]);
            }

            return redirect()->route('admin.penjualan.kontrabon.index')
                ->with('success', 'Kontra bon berhasil dibuat dengan nomor: ' . $noKontraBon);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating kontra bon: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat membuat kontra bon: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat membuat kontra bon')->withInput();
        }
    }

    public function show($id)
    {
        // Check permission
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasPermission('sales.kontrabon.view')) {
            abort(403, 'Unauthorized');
        }

        $kontraBon = KontraBon::with(['details.penjualan', 'member', 'outlet', 'user'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('admin.penjualan.kontrabon.modals.detail', compact('kontraBon'));
        }
        
        return view('admin.penjualan.kontrabon.show', compact('kontraBon'));
    }

    public function data(Request $request)
    {
        $status = $request->status ?? 'belum_lunas'; // Default to 'belum_lunas'
        $user = auth()->user();
        $userOutlets = $user->akses_outlet ?? [];
        
        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            $allOutlets = \App\Models\Outlet::pluck('id_outlet')->toArray();
            $userOutlets = $allOutlets;
        }
        
        // Handle outlet_ids array from GET request
        $selectedOutlets = $request->input('outlet_ids', []);
        if (is_string($selectedOutlets)) {
            $selectedOutlets = [$selectedOutlets];
        }
        if (!is_array($selectedOutlets)) {
            $selectedOutlets = [];
        }

        // Debug logging
        \Log::info('Kontra Bon Piutang Data Request', [
            'status' => $status,
            'userOutlets' => $userOutlets,
            'selectedOutlets' => $selectedOutlets,
            'isSuperAdmin' => $user->hasRole('super_admin'),
            'request_all' => $request->all()
        ]);

        // NEW LOGIC: If no outlets selected, return empty result (user unchecked all)
        if (empty($selectedOutlets)) {
            return datatables(collect([]))->make(true);
        }

        // Validate outlet access - only allow outlets user has access to
        $selectedOutlets = array_intersect($selectedOutlets, $userOutlets);

        // If after validation no valid outlets remain, return empty result
        if (empty($selectedOutlets)) {
            return datatables(collect([]))->make(true);
        }

        $piutang = \App\Models\Piutang::whereIn('id_outlet', $selectedOutlets)
            ->when($status == 'belum_lunas', function ($query) {
                $query->where('status', 'belum_lunas');
            })
            ->when($status == 'lunas', function ($query) {
                $query->where('status', 'lunas');
            })
            ->with(['penjualan', 'member', 'outlet'])
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('Piutang Query Result', [
            'count' => $piutang->count(),
            'selectedOutlets' => $selectedOutlets,
            'status' => $status
        ]);

        return datatables($piutang)
            ->addColumn('checkbox', function ($piutang) {
                if ($piutang->status == 'belum_lunas') {
                    return '<input type="checkbox" name="piutang_ids[]" value="' . $piutang->id_piutang . '" class="piutang-checkbox" data-sisa="' . $piutang->sisa_piutang . '">';
                }
                return '';
            })
            ->addColumn('tanggal', function ($piutang) {
                return $piutang->created_at->format('d/m/Y');
            })
            ->addColumn('no_nota', function ($piutang) {
                return $piutang->penjualan->kode_penjualan ?? '-';
            })
            ->addColumn('nama_member', function ($piutang) {
                return $piutang->member->nama ?? '-';
            })
            ->addColumn('outlet', function ($piutang) {
                return $piutang->outlet->nama_outlet ?? '-';
            })
            ->addColumn('total_formatted', function ($piutang) {
                return 'Rp ' . number_format($piutang->jumlah_piutang, 0, ',', '.');
            })
            ->addColumn('dibayar_formatted', function ($piutang) {
                return 'Rp ' . number_format($piutang->jumlah_dibayar, 0, ',', '.');
            })
            ->addColumn('sisa_formatted', function ($piutang) {
                return 'Rp ' . number_format($piutang->sisa_piutang, 0, ',', '.');
            })
            ->addColumn('status_badge', function ($piutang) {
                if ($piutang->status == 'lunas') {
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>';
                } else {
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Belum Lunas</span>';
                }
            })
            ->rawColumns(['checkbox', 'status_badge'])
            ->make(true);
    }

    public function dataKontraBon(Request $request)
    {
        $user = auth()->user();
        $userOutlets = $user->akses_outlet ?? [];
        
        // Super admin has access to all outlets
        if ($user->hasRole('super_admin')) {
            $allOutlets = \App\Models\Outlet::pluck('id_outlet')->toArray();
            $userOutlets = $allOutlets;
        }
        
        // Handle outlet_ids array from GET request
        $selectedOutlets = $request->input('outlet_ids', []);
        if (is_string($selectedOutlets)) {
            $selectedOutlets = [$selectedOutlets];
        }
        if (!is_array($selectedOutlets)) {
            $selectedOutlets = [];
        }

        // Debug logging
        \Log::info('Kontra Bon Data Request', [
            'userOutlets' => $userOutlets,
            'selectedOutlets' => $selectedOutlets,
            'isSuperAdmin' => $user->hasRole('super_admin'),
            'request_all' => $request->all()
        ]);

        // NEW LOGIC: If no outlets selected, return empty result (user unchecked all)
        if (empty($selectedOutlets)) {
            return datatables(collect([]))->make(true);
        }

        // Validate outlet access - only allow outlets user has access to
        $selectedOutlets = array_intersect($selectedOutlets, $userOutlets);

        // If after validation no valid outlets remain, return empty result
        if (empty($selectedOutlets)) {
            return datatables(collect([]))->make(true);
        }

        $kontraBon = KontraBon::whereIn('id_outlet', $selectedOutlets)
            ->with(['member', 'outlet', 'details'])
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('KontraBon Query Result', [
            'count' => $kontraBon->count(),
            'selectedOutlets' => $selectedOutlets
        ]);

        return datatables($kontraBon)
            ->addColumn('tanggal', function ($kontraBon) {
                return $kontraBon->created_at->format('d/m/Y');
            })
            ->addColumn('nama_member', function ($kontraBon) {
                return $kontraBon->member->nama ?? '-';
            })
            ->addColumn('outlet', function ($kontraBon) {
                return $kontraBon->outlet->nama_outlet ?? '-';
            })
            ->addColumn('total_formatted', function ($kontraBon) {
                // PERBAIKAN: Hitung total dari detail kontra bon yang dipilih
                // Bukan dari semua piutang belum lunas member
                $totalHutang = $kontraBon->details->sum('nominal');
                
                return 'Rp ' . number_format($totalHutang, 0, ',', '.');
            })
            ->addColumn('jatuh_tempo', function ($kontraBon) {
                return date('d/m/Y', strtotime($kontraBon->tanggal_jatuh_tempo));
            })
            ->addColumn('status_badge', function ($kontraBon) {
                if ($kontraBon->status == 'lunas') {
                    $badgeClass = 'bg-green-100 text-green-800';
                    $statusText = 'Lunas';
                } elseif ($kontraBon->status == 'selesai') {
                    $badgeClass = 'bg-blue-100 text-blue-800';
                    $statusText = 'Selesai';
                } else {
                    $badgeClass = 'bg-yellow-100 text-yellow-800';
                    $statusText = 'Pending';
                }
                return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->addColumn('action', function ($kontraBon) {
                $user = auth()->user();
                $canDelete = $user->hasRole('super_admin') || $user->hasPermission('sales.kontrabon.delete');
                
                $actions = '<div class="flex space-x-2">';
                $actions .= '<button onclick="showDetail(' . $kontraBon->id_kontra_bon . ')" class="text-blue-600 hover:text-blue-900" title="Detail"><i class="bx bx-show"></i></button>';
                $actions .= '<button onclick="showPrintModal(' . $kontraBon->id_kontra_bon . ')" class="text-green-600 hover:text-green-900" title="Print"><i class="bx bx-printer"></i></button>';
                
                // Tambahkan tombol lunasi jika status belum lunas
                if ($kontraBon->status != 'lunas') {
                    $actions .= '<button onclick="lunasi(' . $kontraBon->id_kontra_bon . ')" class="text-orange-600 hover:text-orange-900 ml-2" title="Lunasi"><i class="bx bx-check-circle"></i></button>';
                }
                
                // Tambahkan tombol hapus jika user memiliki permission
                if ($canDelete) {
                    $actions .= '<button onclick="deleteKontraBon(' . $kontraBon->id_kontra_bon . ')" class="text-red-600 hover:text-red-900 ml-2" title="Hapus"><i class="bx bx-trash"></i></button>';
                }
                
                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function getPiutang($id_member)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        
        // Ambil SEMUA piutang belum lunas tanpa filter bulan
        $piutang = Piutang::where('id_member', $id_member)
            ->where('status', 'belum_lunas')
            ->when($userOutlets, function ($query) use ($userOutlets) {
                return $query->whereIn('id_outlet', $userOutlets);
            })
            ->with(['penjualan.posSale', 'outlet'])
            ->orderBy('created_at', 'asc') // FIFO - urutkan dari yang paling lama
            ->get()
            ->map(function ($item) {
                // Get no_transaksi from pos_sales
                $noTransaksi = $item->penjualan && $item->penjualan->posSale 
                    ? $item->penjualan->posSale->no_transaksi 
                    : 'TRX00' . $item->id_penjualan;
                
                // Get tanggal from penjualan (tanggal transaksi), bukan created_at piutang
                $tanggalTransaksi = $item->penjualan && $item->penjualan->created_at
                    ? $item->penjualan->created_at->format('d-m-Y')
                    : $item->created_at->format('d-m-Y');
                
                return [
                    'id_piutang' => $item->id_piutang,
                    'id_penjualan' => $item->id_penjualan,
                    'no_transaksi' => $noTransaksi,
                    'tanggal' => $tanggalTransaksi,
                    'piutang' => number_format($item->sisa_piutang, 0, ',', '.'),
                    'sisa_piutang' => $item->sisa_piutang,
                ];
            });

        return response()->json($piutang);
    }

    public function print($id)
    {
        $kontraBon = KontraBon::with(['details.penjualan.posSale', 'member', 'outlet', 'user'])->findOrFail($id);
        
        // Get company setting based on kontrabon outlet
        $companySetting = \App\Models\CompanySetting::where('outlet_id', $kontraBon->id_outlet)->first();
        
        // Fallback to first company setting if outlet-specific not found
        if (!$companySetting) {
            $companySetting = \App\Models\CompanySetting::first();
        }
        
        // Fallback to default values if no company setting found
        if (!$companySetting) {
            $companySetting = (object) [
                'company_name' => 'NAMA PERUSAHAAN',
                'company_address' => 'Alamat Perusahaan',
                'company_phone' => '-',
                'company_logo' => null
            ];
        }

        // SOLUSI SEDERHANA: Gunakan langsung dari details dengan relasi penjualan
        // Buat collection dengan format yang sama seperti piutang
        $piutangBelumLunas = $kontraBon->details->map(function($detail) {
            return (object) [
                'id_piutang' => $detail->id_penjualan, // Gunakan id_penjualan sebagai identifier
                'id_penjualan' => $detail->id_penjualan,
                'penjualan' => $detail->penjualan, // Relasi sudah di-load
                'sisa_piutang' => $detail->nominal,
                'created_at' => $detail->penjualan->created_at ?? now(),
            ];
        });
        
        // Hitung total dari detail
        $totalHutang = $kontraBon->details->sum('nominal');
        
        // Get date filter if exists (untuk informasi di PDF)
        $startDate = $kontraBon->start_date_filter;
        $endDate = $kontraBon->end_date_filter;

        // Log untuk debugging
        \Log::info('Print Kontra Bon', [
            'id_kontra_bon' => $kontraBon->id_kontra_bon,
            'jumlah_detail' => $kontraBon->details->count(),
            'jumlah_piutang' => $piutangBelumLunas->count(),
            'total_hutang' => $totalHutang
        ]);

        $pdf = PDF::loadView('admin.penjualan.kontrabon.print', compact(
            'kontraBon', 
            'companySetting', 
            'piutangBelumLunas', 
            'totalHutang', 
            'startDate', 
            'endDate'
        ));
        
        return $pdf->stream('kontra-bon-' . $kontraBon->no_kontra_bon . '.pdf');
    }

    public function lunasi($id)
    {
        // Check permission
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasPermission('sales.kontrabon.edit')) {
            abort(403, 'Unauthorized');
        }

        try {
            DB::beginTransaction();

            $kontraBon = KontraBon::with(['details'])->findOrFail($id);
            
            // Get piutang belum lunas untuk member ini yang sesuai dengan filter tanggal
            $piutangBelumLunasQuery = Piutang::where('id_member', $kontraBon->id_member)
                ->where('status', 'belum_lunas');
            
            // Apply date filter if exists (same as in print method)
            if ($kontraBon->start_date_filter && $kontraBon->end_date_filter) {
                $piutangBelumLunasQuery = $piutangBelumLunasQuery->whereBetween('created_at', [
                    $kontraBon->start_date_filter . ' 00:00:00',
                    $kontraBon->end_date_filter . ' 23:59:59'
                ]);
            }
            
            $piutangBelumLunas = $piutangBelumLunasQuery->get();
            
            // Create kontra_bon_detail for each piutang that will be paid
            foreach ($piutangBelumLunas as $piutang) {
                // Check if detail already exists
                $existingDetail = KontraBonDetail::where('id_kontra_bon', $kontraBon->id_kontra_bon)
                    ->where('id_penjualan', $piutang->id_penjualan)
                    ->first();
                
                if (!$existingDetail) {
                    // Create detail with the sisa_piutang amount
                    KontraBonDetail::create([
                        'id_kontra_bon' => $kontraBon->id_kontra_bon,
                        'id_penjualan' => $piutang->id_penjualan,
                        'nominal' => $piutang->sisa_piutang,
                        'jumlah_bayar' => $piutang->sisa_piutang
                    ]);
                }
                
                // Update piutang status to lunas
                $piutang->update([
                    'jumlah_dibayar' => $piutang->jumlah_piutang,
                    'sisa_piutang' => 0,
                    'status' => 'lunas'
                ]);
            }
            
            // Update kontra bon status to lunas
            $kontraBon->update(['status' => 'lunas']);

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kontra bon berhasil dilunasi. ' . count($piutangBelumLunas) . ' piutang telah dipindahkan ke "Data Hutang yang Sudah Dilunasi".'
                ]);
            }

            return redirect()->back()->with('success', 'Kontra bon berhasil dilunasi');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error lunasi kontra bon: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat melunasi kontra bon: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat melunasi kontra bon');
        }
    }

    public function destroy($id)
    {
        // Check permission
        if (!auth()->user()->hasRole('super_admin') && !auth()->user()->hasPermission('sales.kontrabon.delete')) {
            abort(403, 'Unauthorized');
        }

        try {
            DB::beginTransaction();

            $kontraBon = KontraBon::with(['details'])->findOrFail($id);
            
            // Kembalikan status piutang yang sudah dibayar melalui kontra bon ini
            foreach ($kontraBon->details as $detail) {
                $piutang = Piutang::where('id_penjualan', $detail->id_penjualan)->first();
                
                if ($piutang) {
                    // Kembalikan jumlah yang sudah dibayar
                    $piutang->update([
                        'jumlah_dibayar' => max(0, $piutang->jumlah_dibayar - $detail->jumlah_bayar),
                        'sisa_piutang' => min($piutang->jumlah_piutang, $piutang->sisa_piutang + $detail->jumlah_bayar),
                        'status' => ($piutang->sisa_piutang + $detail->jumlah_bayar >= $piutang->jumlah_piutang) ? 'belum_lunas' : $piutang->status
                    ]);
                }
            }
            
            // Hapus detail kontra bon
            KontraBonDetail::where('id_kontra_bon', $kontraBon->id_kontra_bon)->delete();
            
            // Hapus kontra bon
            $kontraBon->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kontra bon berhasil dihapus dan status piutang telah dikembalikan.'
                ]);
            }

            return redirect()->route('admin.penjualan.kontrabon.index')
                ->with('success', 'Kontra bon berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting kontra bon: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus kontra bon: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat menghapus kontra bon');
        }
    }
}