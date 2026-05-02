<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Outlet;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('supplier.index', compact('outlets', 'userOutlets'));
    }

    public function data(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;

        $supplier = Supplier::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            // Filter berdasarkan akses outlet user
            $query->whereIn('id_outlet', $userOutlets);

            // Jika ada outlet yang dipilih, filter berdasarkan outlet tersebut
            if ($selectedOutlet) {
                $query->where('id_outlet', $selectedOutlet);
            }

            return $query;
        })->latest()->get();

        $data = datatables()
            ->of($supplier)
            ->addIndexColumn()
            ->addColumn('nama_outlet', function ($kategori) {
                return $kategori->outlet ? $kategori->outlet->nama_outlet : '-';
            })
            ->addColumn('hutang', function ($supplier) {
                return '<span class="label label-danger">'. format_uang($supplier->hutang) .'</span>';
            })
            ->addColumn('aksi', function ($supplier) {
                $updateUrl = route('supplier.update', $supplier->id_supplier);
                $deleteUrl = route('supplier.destroy', $supplier->id_supplier);
                return '
                <div class="btn-group">
                    <button onclick="editForm(`'. $updateUrl .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button onclick="deleteData(`'. $deleteUrl .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['aksi', 'hutang'])
            ->make(true);
        return $data;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Supplier Store Request', $request->all());
        
        $data = $request->only(['nama', 'alamat', 'telepon', 'email', 'bank', 'no_rekening', 'atas_nama']);
        $data['id_outlet'] = $request->id_outlet ?? auth()->user()->akses_outlet[0];
        
        $supplier = Supplier::create($data);
        
        \Log::info('Supplier Saved', $supplier->toArray());
        
        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::find($id);
        return response()->json($supplier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        \Log::info('Supplier Update Request', ['id' => $id, 'data' => $request->all()]);
        
        $supplier = Supplier::find($id);
        
        $data = $request->only(['nama', 'alamat', 'telepon', 'email', 'bank', 'no_rekening', 'atas_nama']);
        $data['id_outlet'] = $request->id_outlet ?? auth()->user()->akses_outlet[0];
        
        $supplier->update($data);
        
        \Log::info('Supplier Updated', $supplier->fresh()->toArray());
        
        return response()->json('Data berhasil diupdate', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Supplier::find($id)->delete();
        return response()->json('Data berhasil dihapus', 200);
    }
}
