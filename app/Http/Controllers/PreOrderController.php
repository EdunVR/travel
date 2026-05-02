<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Member;
use App\Models\Produk;
use App\Models\Outlet;
use App\Services\PreOrderJournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PreOrderController extends Controller
{
    protected $journalService;

    public function __construct(PreOrderJournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index(Request $request)
    {
        // Get user's accessible outlets
        $user = Auth::user();
        $accessibleOutlets = $user->outlets ?? collect();
        
        if ($accessibleOutlets->isEmpty()) {
            $accessibleOutlets = Outlet::all();
        }
        
        $selectedOutletId = $request->get('outlet_id', $accessibleOutlets->first()->id_outlet ?? null);
        
        $query = PreOrder::with(['customer', 'items', 'outlet'])
            ->orderBy('created_at', 'desc');

        // Filter by outlet
        if ($selectedOutletId) {
            $query->where('outlet_id', $selectedOutletId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_preorder', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $preOrders = $query->paginate(15);
        
        // Load customers and products for selected outlet
        $customers = collect();
        $products = collect();
        
        if ($selectedOutletId) {
            $customers = Member::where('id_outlet', $selectedOutletId)->orderBy('nama')->get();
            $products = Produk::where('id_outlet', $selectedOutletId)->orderBy('nama_produk')->get();
        }

        return view('admin.pre-orders.index', compact('preOrders', 'customers', 'products', 'accessibleOutlets', 'selectedOutletId'));
    }

    public function create()
    {
        $customers = Member::orderBy('nama')->get();
        $products = Produk::orderBy('nama_produk')->get();
        
        return view('admin.pre-orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        // Get user's accessible outlets for validation
        $user = Auth::user();
        $accessibleOutlets = $user->outlets ?? collect();
        
        if ($accessibleOutlets->isEmpty()) {
            $accessibleOutlets = Outlet::all();
        }
        
        // Use selected outlet or first accessible outlet
        $selectedOutletId = $request->get('outlet_id', $accessibleOutlets->first()->id_outlet ?? null);
        
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:member,id_member',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'nullable|exists:produk,id_produk',
            'items.*.deskripsi' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.material_instalasi_biaya' => 'nullable|numeric|min:0',
            'items.*.material_instalasi_satuan' => 'nullable|string',
            'items.*.material_instalasi_keterangan' => 'nullable|string',
            'items.*.pemasangan_pelatihan_biaya' => 'nullable|numeric|min:0',
            'items.*.pemasangan_pelatihan_satuan' => 'nullable|string',
            'items.*.pemasangan_pelatihan_keterangan' => 'nullable|string',
            'items.*.ongkos_kirim_biaya' => 'nullable|numeric|min:0',
            'items.*.ongkos_kirim_satuan' => 'nullable|string',
            'items.*.ongkir_komponen' => 'nullable|array',
            'items.*.ongkir_komponen.*.nama' => 'nullable|string',
            'items.*.ongkir_komponen.*.biaya' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'pajak' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $preOrder = new PreOrder();
            $preOrder->kode_preorder = $preOrder->generateKodePreorder();
            $preOrder->outlet_id = $selectedOutletId;
            $preOrder->customer_id = $request->customer_id;
            $preOrder->tanggal = $request->tanggal;
            $preOrder->diskon = $request->diskon ?? 0;
            $preOrder->pajak = $request->pajak ?? 0;
            $preOrder->catatan = $request->catatan;
            $preOrder->status = 'penawaran';

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemSubtotal = $item['qty'] * $item['harga'];
                
                // Add additional costs
                $materialInstalasi = $item['material_instalasi_biaya'] ?? 0;
                $pemasanganPelatihan = $item['pemasangan_pelatihan_biaya'] ?? 0;
                $ongkosKirim = $item['ongkos_kirim_biaya'] ?? 0;
                $totalBiayaTambahan = $materialInstalasi + $pemasanganPelatihan + $ongkosKirim;
                
                $subtotal += $itemSubtotal + $totalBiayaTambahan;
            }

            $preOrder->subtotal = $subtotal;
            $preOrder->total = $subtotal - $preOrder->diskon + $preOrder->pajak;
            $preOrder->save();

            // Save items
            foreach ($request->items as $item) {
                // Calculate additional costs
                $materialInstalasi = $item['material_instalasi_biaya'] ?? 0;
                $pemasanganPelatihan = $item['pemasangan_pelatihan_biaya'] ?? 0;
                $ongkosKirim = $item['ongkos_kirim_biaya'] ?? 0;
                $totalBiayaTambahan = $materialInstalasi + $pemasanganPelatihan + $ongkosKirim;
                
                // Process ongkir komponen
                $ongkirKomponen = null;
                if (isset($item['ongkir_komponen']) && is_array($item['ongkir_komponen'])) {
                    $ongkirKomponen = array_values($item['ongkir_komponen']);
                }
                
                PreOrderItem::create([
                    'pre_order_id' => $preOrder->id,
                    'produk_id' => $item['produk_id'],
                    'deskripsi' => $item['deskripsi'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['qty'] * $item['harga'],
                    'material_instalasi_biaya' => $materialInstalasi,
                    'material_instalasi_satuan' => $item['material_instalasi_satuan'] ?? 'lot',
                    'material_instalasi_keterangan' => $item['material_instalasi_keterangan'] ?? null,
                    'pemasangan_pelatihan_biaya' => $pemasanganPelatihan,
                    'pemasangan_pelatihan_satuan' => $item['pemasangan_pelatihan_satuan'] ?? 'orang',
                    'pemasangan_pelatihan_keterangan' => $item['pemasangan_pelatihan_keterangan'] ?? null,
                    'ongkos_kirim_biaya' => $ongkosKirim,
                    'ongkos_kirim_satuan' => $item['ongkos_kirim_satuan'] ?? 'unit',
                    'ongkos_kirim_komponen' => $ongkirKomponen,
                    'total_biaya_tambahan' => $totalBiayaTambahan
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pre Order berhasil dibuat',
                'redirect' => route('admin.penjualan.preorders.show', $preOrder->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(PreOrder $preorder)
    {
        $preorder->load(['customer', 'items.product']);
        
        // If AJAX request, return partial view
        if (request()->ajax()) {
            return view('admin.pre-orders.partials.detail', compact('preorder'));
        }
        
        return view('admin.pre-orders.show', compact('preorder'));
    }

    public function updateStatus(Request $request, PreOrder $preorder)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:penawaran,invoice,lunas',
            'dp_amount' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldStatus = $preorder->status;
            $newStatus = $request->status;

            $preorder->status = $newStatus;
            
            if ($newStatus === 'invoice' && $request->filled('dp_amount')) {
                $preorder->dp_amount = $request->dp_amount;
            }

            $preorder->save();

            // Create journal entries based on status change
            $this->journalService->createJournalEntry($preorder, $oldStatus, $newStatus);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, PreOrder $preorder)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'customer_id' => 'required|exists:member,id_member',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'nullable|exists:produk,id_produk',
            'items.*.deskripsi' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.material_instalasi_biaya' => 'nullable|numeric|min:0',
            'items.*.material_instalasi_satuan' => 'nullable|string',
            'items.*.material_instalasi_keterangan' => 'nullable|string',
            'items.*.pemasangan_pelatihan_biaya' => 'nullable|numeric|min:0',
            'items.*.pemasangan_pelatihan_satuan' => 'nullable|string',
            'items.*.pemasangan_pelatihan_keterangan' => 'nullable|string',
            'items.*.ongkos_kirim_biaya' => 'nullable|numeric|min:0',
            'items.*.ongkos_kirim_satuan' => 'nullable|string',
            'items.*.ongkir_komponen' => 'nullable|array',
            'items.*.ongkir_komponen.*.nama' => 'nullable|string',
            'items.*.ongkir_komponen.*.biaya' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'pajak' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update pre order
            $preorder->outlet_id = $request->outlet_id;
            $preorder->customer_id = $request->customer_id;
            $preorder->tanggal = $request->tanggal;
            $preorder->diskon = $request->diskon ?? 0;
            $preorder->pajak = $request->pajak ?? 0;
            $preorder->catatan = $request->catatan;

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemSubtotal = $item['qty'] * $item['harga'];
                
                // Add additional costs
                $materialInstalasi = $item['material_instalasi_biaya'] ?? 0;
                $pemasanganPelatihan = $item['pemasangan_pelatihan_biaya'] ?? 0;
                $ongkosKirim = $item['ongkos_kirim_biaya'] ?? 0;
                $totalBiayaTambahan = $materialInstalasi + $pemasanganPelatihan + $ongkosKirim;
                
                $subtotal += $itemSubtotal + $totalBiayaTambahan;
            }

            $preorder->subtotal = $subtotal;
            $preorder->total = $subtotal - $preorder->diskon + $preorder->pajak;
            $preorder->save();

            // Delete existing items and create new ones
            $preorder->items()->delete();
            
            foreach ($request->items as $item) {
                // Calculate additional costs
                $materialInstalasi = $item['material_instalasi_biaya'] ?? 0;
                $pemasanganPelatihan = $item['pemasangan_pelatihan_biaya'] ?? 0;
                $ongkosKirim = $item['ongkos_kirim_biaya'] ?? 0;
                $totalBiayaTambahan = $materialInstalasi + $pemasanganPelatihan + $ongkosKirim;
                
                // Process ongkir komponen
                $ongkirKomponen = null;
                if (isset($item['ongkir_komponen']) && is_array($item['ongkir_komponen'])) {
                    $ongkirKomponen = array_values($item['ongkir_komponen']);
                }
                
                PreOrderItem::create([
                    'pre_order_id' => $preorder->id,
                    'produk_id' => $item['produk_id'],
                    'deskripsi' => $item['deskripsi'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['qty'] * $item['harga'],
                    'material_instalasi_biaya' => $materialInstalasi,
                    'material_instalasi_satuan' => $item['material_instalasi_satuan'] ?? 'lot',
                    'material_instalasi_keterangan' => $item['material_instalasi_keterangan'] ?? null,
                    'pemasangan_pelatihan_biaya' => $pemasanganPelatihan,
                    'pemasangan_pelatihan_satuan' => $item['pemasangan_pelatihan_satuan'] ?? 'orang',
                    'pemasangan_pelatihan_keterangan' => $item['pemasangan_pelatihan_keterangan'] ?? null,
                    'ongkos_kirim_biaya' => $ongkosKirim,
                    'ongkos_kirim_satuan' => $item['ongkos_kirim_satuan'] ?? 'unit',
                    'ongkos_kirim_komponen' => $ongkirKomponen,
                    'total_biaya_tambahan' => $totalBiayaTambahan
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pre Order berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomersByOutlet(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        if (!$outletId) {
            return response()->json([
                'success' => false,
                'message' => 'Outlet ID is required'
            ], 400);
        }

        $customers = Member::where('id_outlet', $outletId)->orderBy('nama')->get();
        
        return response()->json([
            'success' => true,
            'customers' => $customers
        ]);
    }

    public function getProductsByOutlet(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        if (!$outletId) {
            return response()->json([
                'success' => false,
                'message' => 'Outlet ID is required'
            ], 400);
        }

        $products = Produk::where('id_outlet', $outletId)->orderBy('nama_produk')->get();
        
        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}