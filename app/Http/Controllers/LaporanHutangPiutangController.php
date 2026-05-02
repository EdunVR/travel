<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use App\Models\Piutang;
use Illuminate\Http\Request;
use PDF;

class LaporanHutangPiutangController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan_hutang_piutang.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = array();

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal; // Simpan tanggal yang sedang diproses
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            // Ambil hutang dan piutang berdasarkan tanggal yang benar
            $hutang = Hutang::where('created_at', 'LIKE', "%$tanggal%")->sum('hutang');
            $piutang = Piutang::where('created_at', 'LIKE', "%$tanggal%")->sum('piutang');

            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($tanggal, false); // Gunakan $tanggal, bukan $awal
            $row['hutang'] = format_uang($hutang);
            $row['piutang'] = format_uang($piutang);

            $data[] = $row;
        }

        return $data;
    }

    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($awal, $akhir, $totalHutang = 0, $totalPiutang = 0)
    {
        $data = $this->getData($awal, $akhir);
        $pdf  = PDF::loadView('laporan_hutang_piutang.pdf', compact('awal', 'akhir', 'data', 'totalHutang', 'totalPiutang'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Laporan-Hutang-Piutang-'. date('Y-m-d-his') .'.pdf');
    }
}