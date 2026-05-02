<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;

use App\Models\Tipe;
use App\Models\Outlet;
use App\Models\ProdukTipe;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerTypeController extends Controller
{
    use \App\Traits\HasOutletFilter;

    /**
     * Display customer type management page
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get outlets based on user access
        if ($user->hasRole('superadmin')) {
            $outlets = Outlet::where('is_active', true)->get();
        } else {
            // Get outlets from user's outlet access
            $outletIds = $user->outlets()->pluck('outlet_id')->toArray();
            $outlets = Outlet::whereIn('id_outlet', $outletIds)
                ->where('is_active', true)
                ->get();
        }
        
        // If no outlets found, get all active outlets (fallback)
        if ($outlets->isEmpty()) {
            $outlets = Outlet::where('is_active', true)->get();
        }
        
        return view('admin.crm.tipe.index', compact('outlets'));
    }

    /**
     * Get customer type data
     */
    public function getData(Request $request)
    {
        $search = $request->get('search', '');
        $outletIds = $request->get('outlet_ids', []);

        $query = Tipe::with('outlet');

        // Apply outlet filter using trait
        $query = $this->applyOutletFilter($query, 'id_outlet');

        // Additional outlet filter from request (multiple outlets)
        if (!empty($outletIds) && is_array($outletIds)) {
            $query->whereIn('id_outlet', $outletIds);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_tipe', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $types = $query->orderBy('nama_tipe', 'asc')->get();

        // Transform data for frontend
        $data = $types->map(function($type) {
            return [
                'id_tipe' => $type->id_tipe,
                'nama_tipe' => $type->nama_tipe,
                'keterangan' => $type->keterangan,
                'id_outlet' => $type->id_outlet,
                'outlet_name' => $type->outlet ? $type->outlet->nama_outlet : 'Semua Outlet',
                'member_count' => \App\Models\Member::where('id_tipe', $type->id_tipe)->count(),
                'produk_count' => \App\Models\ProdukTipe::where('id_tipe', $type->id_tipe)->count(),
                'created_at' => $type->created_at ? $type->created_at->format('d/m/Y') : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store new customer type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_tipe' => 'required|string|max:255|unique:tipe,nama_tipe',
            'keterangan' => 'nullable|string',
            'id_outlet' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $tipe = Tipe::create($request->only(['nama_tipe', 'keterangan', 'id_outlet']));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipe customer berhasil ditambahkan',
                'data' => $tipe
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating customer type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan tipe customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show customer type detail
     */
    public function show($id)
    {
        try {
            $tipe = Tipe::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $tipe
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe customer tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update customer type
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_tipe' => 'required|string|max:255|unique:tipe,nama_tipe,' . $id . ',id_tipe',
            'keterangan' => 'nullable|string',
            'id_outlet' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $tipe = Tipe::findOrFail($id);
            $tipe->update($request->only(['nama_tipe', 'keterangan', 'id_outlet']));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipe customer berhasil diupdate',
                'data' => $tipe
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating customer type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tipe customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete customer type
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $tipe = Tipe::findOrFail($id);
            
            // Check if type has members
            $memberCount = \App\Models\Member::where('id_tipe', $id)->count();
            if ($memberCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe customer tidak dapat dihapus karena masih digunakan oleh ' . $memberCount . ' pelanggan'
                ], 422);
            }

            $tipe->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tipe customer berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting customer type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tipe customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $outletIds = $request->get('outlet_ids', []);

            $query = Tipe::query();

            // Apply multiple outlet filter
            if (!empty($outletIds) && is_array($outletIds)) {
                $query->whereIn('id_outlet', $outletIds);
            }

            $totalTypes = $query->count();
            
            // Calculate total members for selected outlets
            $memberQuery = \App\Models\Member::query();
            if (!empty($outletIds) && is_array($outletIds)) {
                $memberQuery->whereIn('id_outlet', $outletIds);
            }
            $totalMembers = $memberQuery->count();
            
            $typeUsage = Tipe::withCount('produkTipe')
                ->when(!empty($outletIds) && is_array($outletIds), function($q) use ($outletIds) {
                    return $q->whereIn('id_outlet', $outletIds);
                })
                ->orderBy('produk_tipe_count', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_types' => $totalTypes,
                    'total_members' => $totalMembers,
                    'type_usage' => $typeUsage
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik'
            ], 500);
        }
    }

    /**
     * Get products for a type
     */
    public function getTypeProducts($id)
    {
        try {
            $products = \App\Models\ProdukTipe::where('id_tipe', $id)
                ->with('produk')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting type products: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil produk'
            ], 500);
        }
    }

    /**
     * Search products to add to type
     */
    public function searchProducts(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $typeId = $request->get('type_id');
            $outletIds = $request->get('outlet_ids', []);

            // Get products that are not yet in this type
            $existingProductIds = ProdukTipe::where('id_tipe', $typeId)
                ->pluck('id_produk')
                ->toArray();

            $query = Produk::where(function($q) use ($search) {
                    $q->where('nama_produk', 'like', "%{$search}%")
                      ->orWhere('kode_produk', 'like', "%{$search}%");
                })
                ->whereNotIn('id_produk', $existingProductIds)
                ->where('is_active', true);

            // Filter by multiple outlets if provided
            if (!empty($outletIds) && is_array($outletIds)) {
                $query->whereIn('id_outlet', $outletIds);
            }

            $products = $query->limit(10)
                ->get(['id_produk', 'kode_produk', 'nama_produk', 'harga_jual']);

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (\Exception $e) {
            \Log::error('Error searching products: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari produk'
            ], 500);
        }
    }

    /**
     * Add product to type
     */
    public function addProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_produk' => 'required|exists:produk,id_produk',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'harga_jual' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Check if already exists
            $exists = \App\Models\ProdukTipe::where('id_tipe', $id)
                ->where('id_produk', $request->id_produk)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk sudah ada dalam tipe ini'
                ], 422);
            }

            \App\Models\ProdukTipe::create([
                'id_tipe' => $id,
                'id_produk' => $request->id_produk,
                'diskon' => $request->diskon ?? 0,
                'harga_jual' => $request->harga_jual,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error adding product to type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product in type (diskon & harga_jual)
     */
    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'diskon' => 'nullable|numeric|min:0|max:100',
            'harga_jual' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $produkTipe = \App\Models\ProdukTipe::findOrFail($id);
            $produkTipe->update([
                'diskon' => $request->diskon ?? 0,
                'harga_jual' => $request->harga_jual,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Diskon dan harga berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating product in type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove product from type
     */
    public function removeProduct($id)
    {
        try {
            DB::beginTransaction();

            $produkTipe = \App\Models\ProdukTipe::findOrFail($id);
            $produkTipe->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari tipe'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error removing product from type: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ], 500);
        }
    }
}
