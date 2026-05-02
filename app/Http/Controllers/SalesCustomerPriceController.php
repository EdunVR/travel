<?php

namespace App\Http\Controllers;

use App\Models\CustomerPrice;
use App\Models\Member;
use App\Models\Prospek;
use App\Models\Produk;
use App\Models\OngkosKirim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SalesCustomerPriceController extends Controller
{
    // Halaman Harga Khusus Customer
    public function index(Request $request)
{
    if ($request->ajax()) {
        $customerPrices = CustomerPrice::with(['customer', 'ongkosKirim', 'produk'])->get();
        
        return DataTables::of($customerPrices)
            ->addIndexColumn()
            ->addColumn('customer_name', function ($row) {
                // Gunakan accessor yang sudah diperbaiki
                return $row->nama_customer;
            })
            ->addColumn('customer_type', function ($row) {
                return $row->tipe_customer;
            })
            ->addColumn('produk', function ($row) {
                $produkList = '';
                foreach ($row->produk as $produk) {
                    $hargaKhusus = $produk->pivot->harga_khusus ? ' (Rp ' . number_format($produk->pivot->harga_khusus, 0, ',', '.') . ')' : '';
                    $produkList .= '<span class="label label-primary" style="margin: 2px; display: inline-block;">' . $produk->nama_produk . $hargaKhusus . '</span> ';
                }
                return $produkList;
            })
            ->addColumn('aksi', function ($row) {
                $btn = '<div class="btn-group">';
                $btn .= '<button type="button" onclick="editForm(`'.route('sales.customer-price.update', $row->id_customer_price).'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>';
                $btn .= '<button type="button" onclick="deleteData(`'.route('sales.customer-price.destroy', $row->id_customer_price).'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['produk', 'aksi'])
            ->make(true);
    }
    
    $ongkosKirim = OngkosKirim::all();
    $produks = Produk::all();
    
    return view('sales_management.customer_price.index', compact('ongkosKirim', 'produks'));
}

    // Get customers untuk dropdown (member + prospek) DENGAN PAGINATION
public function getCustomers(Request $request)
{
    $search = $request->get('search', '');
    $page = $request->get('page', 1);
    $perPage = 20;
    
    try {
        $members = Member::select('id_member as id', 'nama', 'telepon', 'alamat', DB::raw("'member' as type"))
                    ->when($search, function($query) use ($search) {
                        return $query->where('nama', 'like', "%{$search}%")
                                    ->orWhere('telepon', 'like', "%{$search}%")
                                    ->orWhere('alamat', 'like', "%{$search}%");
                    })
                    ->orderBy('nama')
                    ->get()
                    ->toArray();

        $prospeks = Prospek::select('id_prospek as id', 'nama', 'telepon', 'alamat', DB::raw("'prospek' as type"))
                    ->when($search, function($query) use ($search) {
                        return $query->where('nama', 'like', "%{$search}%")
                                    ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                                    ->orWhere('telepon', 'like', "%{$search}%")
                                    ->orWhere('alamat', 'like', "%{$search}%");
                    })
                    ->orderBy('nama')
                    ->get()
                    ->toArray();

        // Merge arrays
        $allCustomers = array_merge($members, $prospeks);
        
        // Manual pagination
        $total = count($allCustomers);
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
        \Log::error('Error in getCustomers (CustomerPrice): ' . $e->getMessage());
        return response()->json([], 500);
    }
}

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'customer_type' => 'required|in:member,prospek',
        'customer_id' => 'required',
        'id_ongkir' => 'required|exists:ongkos_kirim,id_ongkir',
        'produk' => 'required|array|min:1',
        'produk.*' => 'required|exists:produk,id_produk',
        'harga_khusus_produk' => 'required|array|min:1',
        'harga_khusus_produk.*' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Validasi customer exists
    if ($request->customer_type === 'member') {
        $customer = Member::find($request->customer_id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'errors' => ['customer_id' => 'Member tidak ditemukan']
            ], 422);
        }
    } else {
        $customer = Prospek::find($request->customer_id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'errors' => ['customer_id' => 'Prospek tidak ditemukan']
            ], 422);
        }
    }

    DB::transaction(function () use ($request) {
        $customerPrice = CustomerPrice::create([
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
        
        $customerPrice->produk()->attach($produkData);
    });

    return response()->json(['success' => true, 'message' => 'Data harga khusus customer berhasil disimpan']);
}

public function edit($id)
{
    try {
        $customerPrice = CustomerPrice::with(['customer', 'produk'])->findOrFail($id);
        
        // Format response untuk customer
        $customerData = [
            'id' => $customerPrice->customer_id,
            'type' => $customerPrice->customer_type,
            'nama' => $customerPrice->customer_type === 'member' 
                ? ($customerPrice->customer->nama ?? 'N/A')
                : ($customerPrice->customer->nama ?? $customerPrice->customer->nama_perusahaan ?? 'N/A')
        ];
        
        return response()->json([
            'success' => true,
            'customer_type' => $customerPrice->customer_type,
            'customer_id' => $customerPrice->customer_id,
            'customer' => $customerData,
            'id_ongkir' => $customerPrice->id_ongkir,
            'produk' => $customerPrice->produk->map(function($produk) {
                return [
                    'id_produk' => $produk->id_produk,
                    'nama_produk' => $produk->nama_produk,
                    'harga_jual' => $produk->harga_jual,
                    'pivot' => [
                        'harga_khusus' => $produk->pivot->harga_khusus
                    ]
                ];
            })
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in customer price edit: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Tidak dapat menampilkan data'
        ], 500);
    }
}

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_type' => 'required|in:member,prospek',
            'customer_id' => 'required',
            'id_ongkir' => 'required|exists:ongkos_kirim,id_ongkir',
            'produk' => 'required|array|min:1',
            'produk.*' => 'required|exists:produk,id_produk',
            'harga_khusus_produk' => 'required|array|min:1',
            'harga_khusus_produk.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::transaction(function () use ($request, $id) {
            $customerPrice = CustomerPrice::findOrFail($id);
            $customerPrice->update([
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

        return response()->json(['success' => true, 'message' => 'Data harga khusus customer berhasil diupdate']);
    }

    public function destroy($id)
    {
        $customerPrice = CustomerPrice::findOrFail($id);
        $customerPrice->delete();
        return redirect()->back()->with('success', 'Data harga khusus customer berhasil dihapus');
    }
    
}