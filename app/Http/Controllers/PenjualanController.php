<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Piutang;
use App\Models\Produk;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\HppProduk;
use App\Models\LaporanPenjualan;
use App\Models\Outlet;
use PDF;
use App\Services\JournalEntryService;
use App\Models\ChartOfAccount;
use Log;
use Auth;

class PenjualanController extends Controller
{
    public function index()
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('penjualan.index', compact('outlets', 'userOutlets'));
    }

    public function data(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;
        $start_date = $request->start_date ?? date('Y-m-d');
        $end_date = $request->end_date ?? date('Y-m-d');

        $penjualan = Penjualan::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereDate('created_at', '>=', $start_date)
                      ->whereDate('created_at', '<=', $end_date);
            })
            ->where('total_item', '>', 0)
            ->latest()
            ->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('nama_outlet', function ($kategori) {
                return $kategori->outlet ? $kategori->outlet->nama_outlet : '-';
            })
            ->addColumn('total_item', function ($penjualan) {
                return $penjualan->total_item;
            })
            ->addColumn('total_harga', function ($penjualan) {
                return format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return format_uang($penjualan->bayar);
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->addColumn('nama', function ($penjualan) {
                $member = $penjualan->member->nama ?? 'Customer Umum';
                return $member;
            })
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            ->addColumn('payment_type', function ($penjualan) {
                // Cek apakah transaksi ini memiliki piutang yang belum lunas
                if ($penjualan->piutang && $penjualan->piutang->status != 'lunas') {
                    return '<span class="label label-danger">Bon</span>';
                } else {
                    return '<span class="label label-success">Cash</span>';
                }
            })
           ->addColumn('aksi', function ($penjualan) {
                $buttons = '<div class="btn-group">';
    
                if (in_array('Penjualan View', Auth::user()->akses ?? [])) {
                    $buttons .= '<button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>';
                }
                
                
                if (in_array('Penjualan Delete', Auth::user()->akses ?? [])) {
                    $buttons .= '<button onclick="deleteData(`'. route('penjualan.destroy', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
                }
                
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['aksi', 'payment_type', 'nama'])
            ->make(true);
    }

    public function create()
    {
        $penjualan = new Penjualan();
        $penjualan->id_member = null;
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan->bayar = 0;
        $penjualan->diterima = 0;
        $penjualan->id_user = auth()->id();
        $penjualan->id_outlet = auth()->user()->akses_outlet[0];
        $penjualan->save();

        session(['id_penjualan' => $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
    }

    public function store(Request $request)
    {
        $penjualan = Penjualan::findOrFail($request->id_penjualan);
        $penjualan->id_member = $request->id_member;
        $penjualan->total_item = $request->total_item;
        $penjualan->total_harga = $request->total;
        $penjualan->diskon = $request->diskon;
        $penjualan->bayar = $request->bayar;
        $penjualan->diterima = $request->diterima;
        $penjualan->id_outlet = $request->id_outlet ?? auth()->user()->akses_outlet[0];
        $penjualan->update();

        session(['tanggal_tempo' => $request->tanggal_tempo]);
        

        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $item->diskon = $request->diskon;
            $item->update();

            LaporanPenjualan::create([
                'id_penjualan' => $penjualan->id_penjualan, // tambahkan baris ini
                'nama_produk' => $item->produk->nama_produk,
                'hpp' => $item->hpp,
                'harga_jual' => $item->harga_jual * $item->jumlah,
                'jumlah' => $item->jumlah,
                'id_outlet' => $request->id_outlet ?? auth()->user()->akses_outlet[0],
            ]);
            
        }

        //$journalService = new JournalEntryService();

        $cashAccount = ChartOfAccount::where('code', '1101')->first(); // Cash account
        $receivableAccount = ChartOfAccount::where('code', '1102')->first(); // Receivable account
        $salesAccount = ChartOfAccount::where('code', '4101')->first(); // Sales account
        $cogsAccount = ChartOfAccount::where('code', '5101')->first(); // COGS account
        $inventoryAccount = ChartOfAccount::where('code', '1201')->first(); // Inventory account

        $entries = [];

        // Jika Cash
        if ($penjualan->diterima >= $penjualan->bayar) {
            $entries[] = [
                'account_id' => $cashAccount->id,
                'debit' => $penjualan->bayar,
                'memo' => 'Penerimaan kas dari penjualan'
            ];
        } else {
            // Jika credit
            $entries[] = [
                'account_id' => $receivableAccount->id,
                'debit' => $penjualan->bayar,
                'memo' => 'Piutang dari penjualan'
            ];
        }

        // Sales revenue
        $entries[] = [
            'account_id' => $salesAccount->id,
            'credit' => $penjualan->total_harga - ($penjualan->total_harga * $penjualan->diskon / 100),
            'memo' => 'Pendapatan penjualan'
        ];

        // $journalService->createAutomaticJournal(
        //     'penjualan',
        //     $penjualan->id_penjualan,
        //     $penjualan->created_at,
        //     'Penjualan #'.$penjualan->id_penjualan,
        //     $entries
        // );

        session(['auto_print' => true]);

        return redirect()->route('transaksi.selesai');
    }

    public function show($id)
    {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga_jual', function ($detail) {
                return format_uang($detail->harga_jual);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::find($id);
        
        // Delete related PenjualanDetail records
        $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $item->delete();
        }

        // Delete related LaporanPenjualan records
        LaporanPenjualan::where('id_penjualan', $penjualan->id_penjualan)->delete();
        
        // Delete related Piutang records if exists
        Piutang::where('id_penjualan', $penjualan->id_penjualan)->delete();

        // Finally delete the Penjualan record
        $penjualan->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();
        return view('penjualan.selesai', compact('setting'));
    }

    public function notaKecil()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();
        
        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }

    public function notaBesar($isChecked = "false")
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
            alert.error('Penjualan tidak ditemukan ZZZ');
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();
        
        $tempo = null;
        if (session('isBon') === 'true') {
            $tempo = session('tanggal_tempo') ? date('d-m-Y', strtotime(session('tanggal_tempo'))) : now()->addWeek()->format('d-m-Y');
        }

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail', 'isChecked', 'tempo'));
        $pdf->setPaper('a5', 'landscape');
        $pdf->setOption('margin-top', 15);
        $pdf->setOption('margin-bottom', 5);
        $pdf->setOption('margin-left', 5);
        $pdf->setOption('margin-right', 5);
        
        return $pdf->stream('Transaksi-'. date('Y-m-d-his') .'.pdf');
    }

    public function showDetailLedger($id)
    {
        $penjualan = Penjualan::with(['member', 'user', 'outlet', 'details.produk'])
            ->findOrFail($id);
        
        return view('penjualan.detail_ledger', compact('penjualan'));
    }

    public function cetak(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;
        $start_date = $request->start_date ?? date('Y-m-d');
        $end_date = $request->end_date ?? date('Y-m-d');

        $penjualan = Penjualan::with(['member', 'user', 'outlet'])
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            })
            ->latest()
            ->get();

        // Debug: cek jumlah data
        \Log::info('Jumlah data penjualan:', ['count' => $penjualan->count()]);

        $setting = Setting::first();
        $outlet = $selectedOutlet ? Outlet::find($selectedOutlet) : null;

        $pdf = PDF::loadView('penjualan.cetak', compact('penjualan', 'setting', 'start_date', 'end_date', 'outlet'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('Laporan-Penjualan-'. date('Y-m-d-his') .'.pdf');
    }

    public function cetakPost(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;
        $start_date = $request->start_date ?? date('Y-m-d');
        $end_date = $request->end_date ?? date('Y-m-d');

        // Ambil data penjualan dengan detail
        $penjualan = Penjualan::with(['member', 'user', 'outlet', 'details.produk'])
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            })
            ->where('total_item', '>', 0)
            ->latest()
            ->get();

        $setting = Setting::first();
        $outlet = $selectedOutlet ? Outlet::find($selectedOutlet) : null;

        $pdf = PDF::loadView('penjualan.cetak_detail', compact('penjualan', 'setting', 'start_date', 'end_date', 'outlet'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Laporan-Penjualan-Detail-'. date('Y-m-d-his') .'.pdf');
    }

    public function cetakSederhana(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;
        $start_date = $request->start_date ?? date('Y-m-d');
        $end_date = $request->end_date ?? date('Y-m-d');

        // Ambil data penjualan tanpa detail
        $penjualan = Penjualan::with(['member', 'user', 'outlet'])
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date);
            })
            ->where('total_item', '>', 0)
            ->latest()
            ->get();

        $setting = Setting::first();
        $outlet = $selectedOutlet ? Outlet::find($selectedOutlet) : null;

        $pdf = PDF::loadView('penjualan.cetak_sederhana', compact('penjualan', 'setting', 'start_date', 'end_date', 'outlet'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Laporan-Penjualan-Sederhana-'. date('Y-m-d-his') .'.pdf');
    }

    public function cetakSederhanaPost(Request $request)
    {
        return $this->cetakSederhana($request);
    }
}