<?php

namespace App\Http\Controllers;

use App\Models\Gerobak;
use App\Models\Member;
use App\Models\Produk;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Log;

class GerobakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($agenId)
    {
        Log::info('GerobakController: Mengakses halaman index gerobak', ['agen_id' => $agenId]);
        
        $agen = Member::findOrFail($agenId);
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        // Return view tanpa extend app
        return view('gerobak.index', compact('agen', 'outlets'));
    }

    /**
     * Get gerobak data for datatable
     */
    public function data($agenId, Request $request) 
    {
        Log::info('GerobakController: Mengakses function data', [
            'agen_id' => $agenId,
            'request_data' => $request->all()
        ]);

        $gerobak = Gerobak::with(['outlet', 'produk'])
            ->where('id_agen', $agenId)
            ->latest();

        return DataTables::of($gerobak)
            ->addIndexColumn()
            ->addColumn('select_all', function ($gerobak) {
                return '<input type="checkbox" name="id_gerobak[]" value="'. $gerobak->id_gerobak .'">';
            })
            ->addColumn('kode_gerobak', function ($gerobak) {
                return '<span class="label label-success">'. $gerobak->kode_gerobak .'</span>';
            })
            ->addColumn('nama_outlet', function ($gerobak) {
                return $gerobak->outlet ? $gerobak->outlet->nama_outlet : '-';
            })
            ->addColumn('total_produk', function ($gerobak) {
                return $gerobak->produk->count();
            })
            ->addColumn('total_stok', function ($gerobak) {
                return $gerobak->produk->sum('pivot.stok');
            })
            ->addColumn('lokasi', function ($gerobak) {
                if ($gerobak->latitude && $gerobak->longitude) {
                    return '
                        <div class="btn-group">
                            <button type="button" onclick="showOnMap('.$gerobak->latitude.','.$gerobak->longitude.')" 
                                class="btn btn-xs btn-info btn-flat">
                                <i class="fa fa-map-marker"></i> Lihat
                            </button>
                            <button type="button" onclick="updateLocation('.$gerobak->id_gerobak.')" 
                                class="btn btn-xs btn-warning btn-flat">
                                <i class="fa fa-refresh"></i> Update
                            </button>
                        </div>
                    ';
                }
                return '<span class="text-muted">Belum di-set</span>';
            })
            ->addColumn('status_badge', function ($gerobak) {
                $badgeClass = [
                    'aktif' => 'success',
                    'nonaktif' => 'danger',
                    'maintenance' => 'warning'
                ];
                return '<span class="label label-'.$badgeClass[$gerobak->status].'">'.ucfirst($gerobak->status).'</span>';
            })
            ->addColumn('aksi', function ($gerobak) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('agen_gerobak.gerobak.edit', [$gerobak->id_agen, $gerobak->id_gerobak]) .'`)" 
                        class="btn btn-xs btn-warning btn-flat"><i class="fa fa-pencil"></i> Edit</button>
                    <button type="button" onclick="manageProduk(`'. route('agen_gerobak.gerobak.show', [$gerobak->id_agen, $gerobak->id_gerobak]) .'`)" 
                        class="btn btn-xs btn-primary btn-flat"><i class="fa fa-cubes"></i> Kelola Produk</button>
                    <button type="button" onclick="deleteData(`'. route('agen_gerobak.gerobak.destroy', [$gerobak->id_agen, $gerobak->id_gerobak]) .'`)" 
                        class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i> Hapus</button>
                </div>';
            })
            ->rawColumns(['aksi', 'select_all', 'kode_gerobak', 'lokasi', 'status_badge'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $agenId)
    {
        Log::info('GerobakController: Menyimpan gerobak baru', [
            'agen_id' => $agenId,
            'data' => $request->all()
        ]);

        $request->validate([
            'nama_gerobak' => 'required',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'status' => 'required|in:aktif,nonaktif,maintenance'
        ]);

        try {
            // Generate kode gerobak
            $lastGerobak = Gerobak::latest()->first();
            $kode_gerobak = 'G'. tambah_nol_didepan((int) ($lastGerobak->id_gerobak ?? 0) + 1, 4);

            $gerobak = new Gerobak();
            $gerobak->id_agen = $agenId;
            $gerobak->nama_gerobak = $request->nama_gerobak;
            $gerobak->kode_gerobak = $kode_gerobak;
            $gerobak->id_outlet = $request->id_outlet;
            $gerobak->status = $request->status;
            $gerobak->save();

            Log::info('GerobakController: Gerobak berhasil disimpan', [
                'id_gerobak' => $gerobak->id_gerobak,
                'kode_gerobak' => $gerobak->kode_gerobak
            ]);

            // Kembalikan response JSON sederhana untuk refresh tabel
            return response()->json([
                'message' => 'Data gerobak berhasil disimpan',
                'success' => true
            ], 200);

        } catch (\Exception $e) {
            Log::error('GerobakController: Gagal menyimpan gerobak', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat menyimpan data',
                'success' => false
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($agenId, $id)
    {
        Log::info('GerobakController: Menampilkan detail gerobak', [
            'agen_id' => $agenId,
            'gerobak_id' => $id
        ]);
        
        try {
            $gerobak = Gerobak::with(['produk', 'agen'])->findOrFail($id);
            $userOutlets = auth()->user()->akses_outlet ?? [];
            $produk = Produk::whereIn('id_outlet', $userOutlets)->get();

            return response()->json([
                'gerobak' => $gerobak,
                'produk' => $produk
            ]);
        } catch (\Exception $e) {
            Log::error('GerobakController: Gagal memuat detail gerobak', [
                'agen_id' => $agenId,
                'gerobak_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Data gerobak tidak ditemukan'], 404);
        }
    }

    public function edit($agenId, $id)
    {
        Log::info('GerobakController: Mengakses form edit gerobak', [
            'agen_id' => $agenId,
            'gerobak_id' => $id
        ]);
        
        try {
            $gerobak = Gerobak::with(['agen'])->findOrFail($id);
            $userOutlets = auth()->user()->akses_outlet ?? [];
            $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
                return $query->whereIn('id_outlet', $userOutlets);
            })->get();

            return response()->json([
                'gerobak' => $gerobak,
                'outlets' => $outlets
            ]);
        } catch (\Exception $e) {
            Log::error('GerobakController: Gagal memuat form edit gerobak', [
                'agen_id' => $agenId,
                'gerobak_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Data gerobak tidak ditemukan'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $agenId, $id)
    {
        Log::info('GerobakController: Memperbarui data gerobak', [
            'agen_id' => $agenId,
            'gerobak_id' => $id,
            'data' => $request->all()
        ]);
        
        $request->validate([
            'nama_gerobak' => 'required',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'status' => 'required|in:aktif,nonaktif,maintenance'
        ]);

        try {
            $gerobak = Gerobak::findOrFail($id);
            $gerobak->nama_gerobak = $request->nama_gerobak;
            $gerobak->id_outlet = $request->id_outlet;
            $gerobak->status = $request->status;
            $gerobak->update();

            Log::info('GerobakController: Data gerobak berhasil diperbarui', [
                'id_gerobak' => $id
            ]);

            return response()->json([
                'message' => 'Data gerobak berhasil diperbarui',
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            Log::error('GerobakController: Gagal memperbarui data gerobak', [
                'id_gerobak' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat memperbarui data',
                'success' => false
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($agenId, $id)
    {
        $gerobak = Gerobak::findOrFail($id);
        $gerobak->delete();

        return response(null, 204);
    }

    /**
     * Update gerobak location
     */
    public function updateLocation($id, Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $gerobak = Gerobak::findOrFail($id);
        $gerobak->latitude = $request->latitude;
        $gerobak->longitude = $request->longitude;
        $gerobak->save();

        return response()->json('Lokasi gerobak berhasil diperbarui', 200);
    }

    /**
     * Update product stock in gerobak
     */
    public function updateStok($id, Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'stok' => 'required|integer|min:0'
        ]);

        $gerobak = Gerobak::findOrFail($id);
        
        if ($request->stok == 0) {
            // Remove product from gerobak if stock is 0
            $gerobak->produk()->detach($request->id_produk);
        } else {
            // Update or add product stock
            $gerobak->produk()->syncWithoutDetaching([
                $request->id_produk => ['stok' => $request->stok]
            ]);
        }

        return response()->json('Stok produk berhasil diperbarui', 200);
    }

    public function produk($agenId, $gerobakId)
    {
        Log::info('GerobakController: Mengambil produk gerobak', [
            'agen_id' => $agenId,
            'gerobak_id' => $gerobakId
        ]);
        
        try {
            $gerobak = Gerobak::with('produk')->findOrFail($gerobakId);
            $userOutlets = auth()->user()->akses_outlet ?? [];
            $produk = Produk::whereIn('id_outlet', $userOutlets)->get();

            return response()->json([
                'gerobak' => $gerobak,
                'produk' => $produk
            ]);
        } catch (\Exception $e) {
            Log::error('GerobakController: Gagal memuat produk gerobak', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Gagal memuat data produk'], 500);
        }
    }

    
    /**
     * Get products in gerobak for datatable
     */
    public function getProdukGerobak($gerobakId)
    {
        Log::info('GerobakController: Mengambil produk gerobak untuk datatable', [
            'gerobak_id' => $gerobakId
        ]);
        
        try {
            $gerobak = Gerobak::with('produk')->findOrFail($gerobakId);
            
            // Return data sederhana tanpa DataTables
            return response()->json([
                'data' => $gerobak->produk->map(function($produk) {
                    return [
                        'id_produk' => $produk->id_produk,
                        'kode_produk' => $produk->kode_produk,
                        'nama_produk' => $produk->nama_produk,
                        'pivot' => [
                            'stok' => $produk->pivot->stok
                        ]
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('GerobakController: Gagal memuat produk gerobak', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Gagal memuat data produk'], 500);
        }
    }

    public function updateProduk($gerobakId, Request $request)
    {
        Log::info('GerobakController: Memperbarui produk gerobak', [
            'gerobak_id' => $gerobakId,
            'data' => $request->all()
        ]);
        
        try {
            $gerobak = Gerobak::findOrFail($gerobakId);
            
            // Format data untuk sync
            $produkData = [];
            foreach ($request->produk as $produk) {
                if ($produk['stok'] > 0) {
                    $produkData[$produk['id_produk']] = ['stok' => $produk['stok']];
                }
            }
            
            // Sync produk dengan gerobak
            $gerobak->produk()->sync($produkData);
            
            Log::info('GerobakController: Produk gerobak berhasil diperbarui', [
                'gerobak_id' => $gerobakId,
                'jumlah_produk' => count($produkData)
            ]);
            
            return response()->json([
                'message' => 'Data produk berhasil disimpan',
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('GerobakController: Gagal memperbarui produk gerobak', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Gagal menyimpan data produk',
                'success' => false
            ], 500);
        }
    }

    public function deleteSelected($agenId, Request $request)
    {
        Log::info('GerobakController: Menghapus gerobak terpilih', [
            'agen_id' => $agenId,
            'selected_ids' => $request->id_gerobak
        ]);
        
        try {
            foreach ($request->id_gerobak as $id) {
                $gerobak = Gerobak::find($id);
                if ($gerobak) {
                    $gerobak->delete();
                }
            }

            return response(null, 204);
        } catch (\Exception $e) {
            Log::error('GerobakController: Gagal menghapus gerobak terpilih', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Terjadi kesalahan saat menghapus data'], 500);
        }
    }
}