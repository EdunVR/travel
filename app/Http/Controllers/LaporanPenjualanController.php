<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LaporanPenjualan;
use App\Models\Outlet;
use App\Models\Piutang;
use App\Models\Penjualan;
use PDF;
use Carbon\Carbon;
use Log;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan_penjualan.index', compact('tanggalAwal', 'tanggalAkhir', 'userOutlets', 'outlets'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = [];
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = session('selected_outlet');

        $laporan_penjualan = LaporanPenjualan::with('penjualan') // eager load
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
            })
            ->whereDate('created_at', '>=', $awal)
            ->whereDate('created_at', '<=', $akhir)
            ->get();

        Log::info('Laporan Penjualan:', ['laporan_penjualan' => $laporan_penjualan]);

        foreach ($laporan_penjualan as $item) {
            $hargaJual = $item->penjualan->bayar ?? 0;
            $hpp = $item->hpp;
            $profit = $hargaJual - $hpp;
            
            // Get payment type (cash/bon)
            $paymentType = 'Cash';
            if ($item->penjualan) {
                $piutang = Piutang::where('id_penjualan', $item->penjualan->id_penjualan)
                    ->where('status', '!=', 'lunas')
                    ->first();
                if ($piutang) {
                    $paymentType = 'Bon';
                }
            }

            $data[] = [
                'DT_RowIndex' => $no++,
                'tanggal' => tanggal_indonesia($item->created_at, false),
                'nama_produk' => $item->nama_produk,
                'hpp' => format_uang($hpp),
                'harga_jual' => format_uang($hargaJual),
                'jumlah' => $item->jumlah,
                'profit' => format_uang($profit),
                'payment_type' => $paymentType,
                'id_laporan' => $item->id_laporan,
            ];
        }

        return $data;
    }

    public function data($awal, $akhir, Request $request)
    {
        session()->put('selected_outlet', $request->id_outlet);

        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('select-all', function ($row) {
                return '
                    <input type="checkbox" name="id_laporan[]" value="' . $row['id_laporan'] . '">
                ';
            })
            ->addColumn('tanggal', function ($row) {
                return $row['tanggal'];
            })
            ->addColumn('nama_produk', function ($row) {
                return $row['nama_produk'];
            })
            ->addColumn('hpp', function ($row) {
                return $row['hpp'];
            })
            ->addColumn('harga_jual', function ($row) {
                return $row['harga_jual'];
            })
            ->addColumn('jumlah', function ($row) {
                return $row['jumlah'];
            })
            ->addColumn('profit', function ($row) {
                return $row['profit'];
            })
            ->addColumn('payment_type', function ($row) {
                return $row['payment_type'];
            })
            ->rawColumns(['select-all'])
            ->make(true);
    }

    public function exportPDF($awal, $akhir, $totalHPP = 0, $totalHargaJual = 0, $totalJumlah = 0, $totalProfit = 0)
    {
        $data = $this->getData($awal, $akhir);
        
        // Calculate cash and bon totals
        $totalCash = 0;
        $totalBon = 0;
        
        foreach ($data as $row) {
            $hargaJual = (int) str_replace(['Rp', '.', ' ', ','], '', $row['harga_jual']);
            if ($row['payment_type'] === 'Cash') {
                $totalCash += $hargaJual;
            } else {
                $totalBon += $hargaJual;
            }
        }
        
        $pdf = PDF::loadView('laporan_penjualan.pdf', compact(
            'awal', 
            'akhir', 
            'data', 
            'totalHPP', 
            'totalHargaJual', 
            'totalJumlah', 
            'totalProfit',
            'totalCash',
            'totalBon'
        ));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Laporan-Penjualan-'. date('Y-m-d-his') .'.pdf');
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_laporan as $id) {
            $laporan = LaporanPenjualan::find($id);
            $laporan->delete();
        }

        return response(null, 204);
    }
}