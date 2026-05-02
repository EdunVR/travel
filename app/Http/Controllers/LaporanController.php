<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use App\Models\Hutang;
use App\Models\Piutang;
use App\Models\Outlet;
use PDF;

class LaporanController extends Controller
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

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir', 'userOutlets', 'outlets'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;
        $hutang = 0;
        $total_hutang = 0;
        $piutang = 0;
        $total_piutang = 0;
        
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = session('selected_outlet');

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_penjualan = Penjualan::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                    $query->whereIn('id_outlet', $userOutlets);
                    if ($selectedOutlet) {
                        $query->where('id_outlet', $selectedOutlet);
                    }
                    return $query;
                })
                ->where('created_at', 'LIKE', "%$tanggal%")
                ->sum('bayar');
            
            $total_pembelian = Pembelian::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                    $query->whereIn('id_outlet', $userOutlets);
                    if ($selectedOutlet) {
                        $query->where('id_outlet', $selectedOutlet);
                    }
                    return $query;
                })
                ->where('created_at', 'LIKE', "%$tanggal%")
                ->sum('bayar');

            $total_pengeluaran = Pengeluaran::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                    $query->whereIn('id_outlet', $userOutlets);
                    if ($selectedOutlet) {
                        $query->where('id_outlet', $selectedOutlet);
                    }
                    return $query;
                })
                ->where('created_at', 'LIKE', "%$tanggal%")
                ->sum('nominal');

            $hutang = Hutang::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                    $query->whereIn('id_outlet', $userOutlets);
                    if ($selectedOutlet) {
                        $query->where('id_outlet', $selectedOutlet);
                    }
                    return $query;
                })
                ->where('created_at', 'LIKE', "%$tanggal%")
                ->sum('hutang');

            $total_hutang += $hutang;

            $piutang = Piutang::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                    $query->whereIn('id_outlet', $userOutlets);
                    if ($selectedOutlet) {
                        $query->where('id_outlet', $selectedOutlet);
                    }
                    return $query;
                })
                ->where('created_at', 'LIKE', "%$tanggal%")
                ->sum('piutang');

            $total_piutang += $piutang;

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $total_pendapatan += $pendapatan;


            
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($tanggal, false);
            $row['penjualan'] = format_uang($total_penjualan);
            $row['pembelian'] = format_uang($total_pembelian);
            $row['pengeluaran'] = format_uang($total_pengeluaran);
            $row['pendapatan'] = format_uang($pendapatan);
            $row['hutang'] = format_uang($hutang);
            $row['piutang'] = format_uang($piutang);

            $data[] = $row;
        }

        return $data;
    }

    public function data($awal, $akhir, Request $request)
    {
        session()->put('selected_outlet', $request->id_outlet);

        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($awal, $akhir, $totalPenjualan = 0, $totalPembelian = 0, $totalPengeluaran = 0, $totalPendapatan = 0, $totalHutang = 0, $totalPiutang = 0)
    {
        $data = $this->getData($awal, $akhir);
        $pdf  = PDF::loadView('laporan.pdf', compact('awal', 'akhir', 'data', 'totalPenjualan', 'totalPembelian', 'totalPengeluaran', 'totalPendapatan', 'totalHutang', 'totalPiutang'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Laporan-umum-'. date('Y-m-d-his') .'.pdf');
    }
}