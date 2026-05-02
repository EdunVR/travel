<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\PembelianDetail;
use App\Models\Bahan;
use App\Models\BahanDetail;
use App\Models\Outlet;
use App\Models\Setting;
use PDF;
use App\Services\JournalEntryService;
use App\Models\ChartOfAccount;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $id_outlet = $request->get('id_outlet');
        $supplier = Supplier::when(!empty($userOutlets), function ($query) use ($userOutlets, $id_outlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($id_outlet) {
                $query->where('id_outlet', $id_outlet);
            }
            return $query;
        })->latest()->get();

        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('pembelian.index', compact('outlets', 'userOutlets', 'supplier'));
    }

    public function data(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;

        $pembelian = Pembelian::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($selectedOutlet) {
                $query->where('id_outlet', $selectedOutlet);
            }
            return $query;
        })->latest()->get();

        return datatables()
            ->of($pembelian)
            ->addIndexColumn()
            ->addColumn('nama_outlet', function ($kategori) {
                return $kategori->outlet ? $kategori->outlet->nama_outlet : '-';
            })
            ->addColumn('total_item', function ($pembelian) {
                return $pembelian->total_item;
            })
            ->addColumn('total_harga', function ($pembelian) {
                return format_uang($pembelian->total_harga);
            })
            ->addColumn('bayar', function ($pembelian) {
                if ($pembelian->total_harga > $pembelian->bayar) {
                    return '<span class="label label-danger">' .format_uang($pembelian->bayar). '</span>';
                } elseif ($pembelian->total_harga < $pembelian->bayar) {
                    return '<span class="label label-success">' .format_uang($pembelian->bayar). '</span>';
                }
                return format_uang($pembelian->bayar);
            })
            ->addColumn('tanggal', function ($pembelian) {
                return tanggal_indonesia($pembelian->created_at, false);
            })
            ->addColumn('supplier', function ($pembelian) {
                return $pembelian->supplier->nama;
            })
            ->editColumn('diskon', function ($pembelian) {
                return $pembelian->diskon . '%';
            })
            ->addColumn('aksi', function ($pembelian) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('pembelian.show', $pembelian->id_pembelian) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('pembelian.destroy', $pembelian->id_pembelian) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'bayar'])
            ->make(true);
    }

    public function create($id, $id_outlet_selected)
    {
        $pembelian = new Pembelian();
        $pembelian->id_supplier = $id;
        $pembelian->total_item = 0;
        $pembelian->total_harga = 0;
        $pembelian->diskon = 0;
        $pembelian->bayar = 0;
        $pembelian->id_outlet = $id_outlet_selected ?? auth()->user()->akses_outlet[0];
        $pembelian->save();

        session(['id_pembelian' => $pembelian->id_pembelian]);
        session(['id_supplier' => $pembelian->id_supplier]);
        session(['id_outlet' => $id_outlet_selected]);

        return redirect()->route('pembelian_detail.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('hutang') && $request->hutang == 'on') {
            $bayar = 0; // Jika "Berhutang" dicentang, nilai bayar menjadi 0
        } else {
            $bayar = $request->bayar; // Jika tidak dicentang, gunakan nilai bayar dari input
        }

        $selectedOutlet = session('id_outlet');

        $pembelian = Pembelian::findOrFail($request->id_pembelian);
        $pembelian->total_item = $request->total_item;
        $pembelian->total_harga = $request->total;
        $pembelian->diskon = $request->diskon;
        $pembelian->bayar = $bayar;
        $pembelian->id_outlet = $selectedOutlet ?? auth()->user()->akses_outlet[0];
        $pembelian->update();

        $detail = PembelianDetail::where('id_pembelian', $pembelian->id_pembelian)->get();
        foreach ($detail as $item) {
            $hargaBahan = BahanDetail::where('id_bahan', $item->id_bahan)
                                ->where('id', $item->id_harga_bahan) // Sesuai ID harga bahan
                                ->first();
                if ($hargaBahan) {
                    // Tambahkan stok berdasarkan jumlah yang dibeli
                    $hargaBahan->stok += $item->jumlah;
                    $hargaBahan->update();
                }
        }

        // Create automatic journal entry
        $journalService = new JournalEntryService();
        
        // Get accounts from configuration
        $cashAccount = ChartOfAccount::where('code', '1101')->first(); // Cash account
        $payableAccount = ChartOfAccount::where('code', '2101')->first(); // Payable account
        $inventoryAccount = ChartOfAccount::where('code', '1201')->first(); // Inventory account
        $expenseAccount = ChartOfAccount::where('code', '5201')->first(); // Expense account

        $entries = [];

        foreach ($detail as $item) {
            $entries[] = [
                'account_id' => $expenseAccount->id,
                'debit' => $item->harga_beli * $item->jumlah,
                'memo' => 'Pembelian bahan '.$item->bahan->nama_bahan
            ];
        }

        // Payment method
        if ($pembelian->bayar > 0) {
            $entries[] = [
                'account_id' => $cashAccount->id,
                'credit' => $pembelian->bayar,
                'memo' => 'Pembayaran untuk pembelian #'.$pembelian->id_pembelian
            ];
        } else {
            $entries[] = [
                'account_id' => $payableAccount->id,
                'credit' => $pembelian->total_harga,
                'memo' => 'Hutang untuk pembelian #'.$pembelian->id_pembelian
            ];
        }

        // $journalService->createAutomaticJournal(
        //     'pembelian',
        //     $pembelian->id_pembelian,
        //     $pembelian->created_at,
        //     'Pembelian #'.$pembelian->id_pembelian,
        //     $entries
        // );

        //return redirect()->route('pembelian.index');
        return redirect()->route('pembelian.selesai');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detail = PembelianDetail::with('bahan')->where('id_pembelian', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('nama_bahan', function ($detail) {
                return '<span class="label label-success">'. $detail->bahan->nama_bahan .'</span>';
            })
            ->addColumn('harga_beli', function ($detail) {
                return format_uang($detail->harga_beli);
            })
            ->addColumn('jumlah', function ($detail) {
                return $detail->jumlah;
            })
            ->addColumn('subtotal', function ($detail) {
                return format_uang($detail->subtotal);
            })
            ->rawColumns(['nama_bahan'])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembelian $pembelian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pembelian = Pembelian::find($id);
        $detail    = PembelianDetail::where('id_pembelian', $pembelian->id_pembelian)->get();
        foreach ($detail as $item) {
            $hargaBahan = BahanDetail::find($item->id_harga_bahan);
            if ($hargaBahan) {
                $hargaBahan->stok -= $item->jumlah;
                $hargaBahan->update();
            }
            $item->delete();
        }

        $pembelian->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();
        return view('pembelian.selesai', compact('setting'));
    }

    public function notaPembelian()
    {
        $setting = Setting::first();
        $pembelian = Pembelian::find(session('id_pembelian'));
        if (!$pembelian) {
            abort(404);
        }
        $detail = PembelianDetail::with('bahan')
            ->where('id_pembelian', session('id_pembelian'))
            ->get();

        $pdf = PDF::loadView('pembelian.nota_pembelian', compact('setting', 'pembelian', 'detail'));
        $pdf->setPaper(0, 0, 609, 440, 'potrait');
        return $pdf->stream('Pembelian-' . date('Y-m-d-his') . '.pdf');
    }

    public function showDetailLedger($id)
    {
        $pembelian = Pembelian::with(['supplier', 'outlet', 'details.bahan'])
            ->findOrFail($id);
        
        return view('pembelian.detail_ledger', compact('pembelian'));
    }
}
