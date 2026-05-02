<?php

namespace App\Http\Controllers;

use App\Models\KontraBon;
use App\Models\KontraBonDetail;
use App\Models\Penjualan;
use App\Models\Member;
use App\Models\Outlet;
use App\Models\Piutang;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PDF;

class KontraBonController extends Controller
{
    public function index()
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('kontra_bon.index', compact('outlets', 'userOutlets'));
    }

    public function show($id)
    {
        $kontraBon = KontraBon::with(['details.penjualan', 'member', 'outlet'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('kontra_bon.detail', compact('kontraBon'));
        }
        
        return view('kontra_bon.show', compact('kontraBon'));
    }

    public function data(Request $request)
    {
        $status = $request->status; // 'belum_lunas' atau 'lunas'
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;

        $piutang = Piutang::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($selectedOutlet) {
                $query->where('id_outlet', $selectedOutlet);
            }
            return $query;
        })
        ->where('status', $status)
        ->with('penjualan')
        ->latest()->get();

        //Log::info('Data piutang:', $piutang->toArray());

        return datatables()
            ->of($piutang)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($piutang) {
                return tanggal_indonesia($piutang->created_at);
            })
            ->addColumn('member', function ($piutang) {
                return optional(optional($piutang->penjualan)->member)->nama ?? '-';
            })
            ->addColumn('trx_id', function ($piutang) {
                return 'TRX00' . $piutang->id_penjualan;
            })
            ->addColumn('nominal', function ($piutang) {
                return format_uang($piutang->sisa_piutang); // Tampilkan sisa piutang
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function dataKontraBon(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;

        $kontraBon = KontraBon::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($selectedOutlet) {
                $query->where('id_outlet', $selectedOutlet);
            }
            return $query;
        })
        ->with(['member', 'outlet', 'details'])
        ->latest()->get();

        return datatables()
            ->of($kontraBon)
            ->addIndexColumn()
            ->addColumn('kode_kontra_bon', function ($kontraBon) {
                return $kontraBon->kode_kontra_bon;
            })
            ->addColumn('tanggal', function ($kontraBon) {
                return tanggal_indonesia($kontraBon->tanggal);
            })
            ->addColumn('customer', function ($kontraBon) {
                return $kontraBon->member->nama ?? '-';
            })
            ->addColumn('total_pembayaran', function ($kontraBon) {
                return format_uang($kontraBon->total_pembayaran);
            })
            ->addColumn('total_hutang', function ($kontraBon) {
                // HITUNG TOTAL HUTANG BERDASARKAN DETAIL YANG DIPILIH
                $totalHutang = $kontraBon->details->sum('nominal');
                return format_uang($totalHutang);
            })
            ->addColumn('aksi', function ($kontraBon) {
                return '
                    <div class="btn-group">
                        <button type="button" onclick="cetakNota(`'. route('kontra_bon.nota_besar', $kontraBon->id_kontra_bon) .'`)" class="btn btn-info btn-xs btn-flat">
                            <i class="fa fa-print"></i> Cetak
                        </button>
                        <button type="button" onclick="showDetail(`'. route('kontra_bon.show', $kontraBon->id_kontra_bon) .'`)" class="btn btn-primary btn-xs btn-flat">
                            <i class="fa fa-eye"></i> Detail
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create(Request $request)
    {

        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        $selectedOutlet = $request->id_outlet;

        $members = Member::when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
            $query->whereIn('id_outlet', $userOutlets);
            if ($selectedOutlet) {
                $query->where('id_outlet', $selectedOutlet);
            }
            return $query;
        })->get();

        $selectedMember = $request->id_member;

        // Perbaikan: Ambil hanya ID dari $members
        $memberIds = $members->pluck('id_member')->toArray();

        $piutang = Piutang::when(!empty($memberIds), function ($query) use ($memberIds, $selectedMember) {
            $query->whereIn('piutang.id_member', $memberIds);
            if ($selectedMember) {
                $query->where('piutang.id_member', $selectedMember);
            }
            return $query;
        })
        ->where('status', 'belum_lunas')
        ->with('penjualan')
        ->get();

        return view('kontra_bon.create', compact('outlets', 'members', 'piutang'));
    }


    public function store(Request $request)
    {
        $idMember = $request->id_member;

        $piutangBelumLunas = Piutang::where('id_member', $idMember)
            ->where('status', 'belum_lunas')
            ->get();

        $totalHutang = $piutangBelumLunas->sum('sisa_piutang'); // Gunakan sisa_piutang

        if ($request->pembayaran > $totalHutang) {
            return redirect()->back()->withErrors(['pembayaran' => 'Pembayaran melebihi total hutang yang belum lunas.']);
        }

        $kontraBon = KontraBon::create([
            'kode_kontra_bon' => 'KB00' . (KontraBon::count() + 1),
            'id_member' => $idMember,
            'id_outlet' => $request->id_outlet,
            'tanggal' => now(),
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            'total_pembayaran' => $request->pembayaran,
            'sisa_hutang' => $totalHutang - ($request->pembayaran - $request->masuk_saldo),
            'id_user' => auth()->id(),
            'start_date_filter' => $request->start_date_filter, // SIMPAN FILTER
            'end_date_filter' => $request->end_date_filter, // SIMPAN FILTER
        ]);

        foreach ($request->selected_penjualan ?? [] as $idPenjualan) {
            $piutangRecord = Piutang::where('id_penjualan', $idPenjualan)->first();

            if (!$piutangRecord) {
                continue;
            }

            $nominalPiutang = $piutangRecord->sisa_piutang; // Gunakan sisa piutang

            KontraBonDetail::create([
                'id_kontra_bon' => $kontraBon->id_kontra_bon,
                'id_penjualan' => $idPenjualan,
                'nominal' => $nominalPiutang,
            ]);

            // Update pembayaran piutang dengan benar
            $piutangRecord->update([
                'jumlah_dibayar' => $piutangRecord->jumlah_dibayar + $nominalPiutang,
                'sisa_piutang' => $piutangRecord->sisa_piutang - $nominalPiutang,
                'status' => 'lunas', // Set status lunas karena dibayar penuh
            ]);
        }

        $totalHutangSetelahUpdate = Piutang::where('id_member', $idMember)
            ->where('status', 'belum_lunas')
            ->sum('sisa_piutang'); // Gunakan sisa_piutang bukan piutang

        $member = Member::find($idMember);
        if ($member) {
            if ($request->tambahkan_saldo_value == '1') {
                $member->saldo = $request->masuk_saldo;
                $member->save();
            } else {
                $member->saldo += $request->masuk_saldo;
                $member->save();
            }
        }

        // Simpan filter range tanggal di session untuk nota
        session([
            'kontra_bon_filter' => [
                'start_date' => $request->start_date_filter,
                'end_date' => $request->end_date_filter
            ]
        ]);

        return redirect()->route('kontra_bon.selesai', $kontraBon->id_kontra_bon);
    }

    public function selesai($id)
    {
        $setting = Setting::first();
        $kontraBon = KontraBon::findOrFail($id);
        return view('kontra_bon.selesai', compact('kontraBon', 'setting'));
    }

    public function notaBesar($id)
    {
        $kontraBon = KontraBon::findOrFail($id);
        
        // Ambil filter dari session atau dari database
        $startDate = session('kontra_bon_filter.start_date') ?? $kontraBon->start_date_filter;
        $endDate = session('kontra_bon_filter.end_date') ?? $kontraBon->end_date_filter;

        $query = Piutang::where('status', 'belum_lunas')
            ->where('id_member', $kontraBon->id_member);

        // Apply filter tanggal jika ada
        if ($startDate && $endDate) {
            $query->whereHas('penjualan', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [
                    $startDate . ' 00:00:00', 
                    $endDate . ' 23:59:59'
                ]);
            });
        }

        $piutangBelumLunas = $query->with('penjualan')->get();

        // HITUNG TOTAL HUTANG BERDASARKAN FILTER
        $totalHutang = $piutangBelumLunas->sum('sisa_piutang'); // Gunakan sisa_piutang

        $setting = Setting::first();
        
        $pdf = PDF::loadView('kontra_bon.nota_besar_kontrabon', compact(
            'kontraBon', 
            'setting', 
            'piutangBelumLunas',
            'startDate',
            'endDate',
            'totalHutang' // KIRIM TOTAL HUTANG KE VIEW
        ));
        
        $pdf->setPaper('a5', 'potrait');
        $pdf->setOption('margin-top', 15);
        $pdf->setOption('margin-bottom', 5);
        $pdf->setOption('margin-left', 5);
        $pdf->setOption('margin-right', 5);
        
        return $pdf->stream('KontraBon-' . $kontraBon->kode_kontra_bon . '.pdf');
    }

    public function getPiutang($id_member)
    {
        $piutang = Piutang::where('id_member', $id_member)
            ->where('status', 'belum_lunas')
            ->with('penjualan')
            ->get()
            ->map(function ($item) {
                return [
                    'id_penjualan' => $item->penjualan->id_penjualan,
                    'tanggal' => $item->penjualan->created_at->format('d-m-Y'),
                    'piutang' => number_format($item->sisa_piutang, 0, ',', '.'), // Gunakan sisa_piutang
                ];
            });

        return response()->json($piutang);
    }

}
