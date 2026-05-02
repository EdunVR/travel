<?php

namespace App\Http\Controllers;

use App\Models\PoPenjualan;
use App\Models\PoPenjualanDetail;
use App\Models\Member;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Piutang;
use App\Models\HppProduk;
use App\Models\Outlet;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use PDF;
use Log;
use Auth;
use App\Models\SettingCOA;
use App\Services\JournalEntryService;
use DB;

class PoPenjualanController extends Controller
{
    public function index()
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        return view('po_penjualan.index', compact('outlets', 'userOutlets'));
    }

    public function data(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;
        $start_date = $request->start_date ?? date('Y-m-d');
        $end_date = $request->end_date ?? date('Y-m-d');

        $poPenjualan = PoPenjualan::with(['member', 'user', 'outlet'])
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereDate('tanggal', '>=', $start_date)
                    ->whereDate('tanggal', '<=', $end_date);
            })
            ->where('total_item', '>', 0)
            ->latest()
            ->get();

        return datatables()
            ->of($poPenjualan)
            ->addIndexColumn()
            ->addColumn('no_po', function ($po) {
                return $po->no_po;
            })
            ->addColumn('tanggal', function ($po) {
                return tanggal_indonesia($po->tanggal, false);
            })
            ->addColumn('nama_outlet', function ($po) {
                return $po->outlet ? $po->outlet->nama_outlet : '-';
            })
            ->addColumn('nama_member', function ($po) {
                return $po->member->nama ?? 'Customer Umum';
            })
            ->addColumn('total_item', function ($po) {
                return $po->total_item;
            })
            ->addColumn('total_harga', function ($po) {
                return format_uang($po->total_harga);
            })
            ->addColumn('ongkir', function ($po) {
                return format_uang($po->ongkir);
            })
            ->addColumn('diskon', function ($po) {
                return $po->diskon . '%';
            })
            ->addColumn('bayar', function ($po) {
                return format_uang($po->bayar);
            })
            ->addColumn('status_badge', function ($po) {
                $badgeClass = [
                    'menunggu' => 'warning',
                    'lunas' => 'success',
                    'gagal' => 'danger'
                ][$po->status] ?? 'secondary';
                
                return '<span class="label label-'.$badgeClass.'">'.ucfirst($po->status).'</span>';
            })
            ->addColumn('kasir', function ($po) {
                return $po->user->name ?? '';
            })
            ->addColumn('aksi', function ($po) {
                $buttons = '<div class="btn-group">';

                if (in_array('PO Penjualan View', Auth::user()->akses ?? [])) {
                    $buttons .= '<button onclick="showDetail(`'. route('po-penjualan.show', $po->id_po_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>';
                }
                
                if (in_array('PO Penjualan Edit', Auth::user()->akses ?? [])) {
                    $buttons .= '<a href="'. route('po-penjualan.edit', $po->id_po_penjualan) .'" class="btn btn-xs btn-warning btn-flat"><i class="fa fa-edit"></i></a>';
                }
                
                if (in_array('PO Penjualan Delete', Auth::user()->akses ?? [])) {
                    $buttons .= '<button onclick="deleteData(`'. route('po-penjualan.destroy', $po->id_po_penjualan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
                }
                
                $buttons .= '<a href="'. route('po-penjualan.print', $po->id_po_penjualan) .'" target="_blank" class="btn btn-xs btn-success btn-flat"><i class="fa fa-print"></i></a>';
                
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['aksi', 'status_badge'])
            ->make(true);
    }

    public function create()
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $outlets = Outlet::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        $members = Member::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        $produk = Produk::when($userOutlets, function ($query) use ($userOutlets) {
            return $query->whereIn('id_outlet', $userOutlets);
        })->get();

        // Generate PO number
        $lastPO = PoPenjualan::latest()->first();
        $nextNumber = $lastPO ? (int) substr($lastPO->no_po, -4) + 1 : 1;
        $noPO = 'PO-' . date('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('po_penjualan.create', compact('outlets', 'members', 'produk', 'noPO'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_po' => 'required|unique:po_penjualan,no_po',
            'tanggal' => 'required|date',
            'id_member' => 'required|exists:member,id_member',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'items' => 'required|array|min:1',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.diskon' => 'nullable|numeric|min:0|max:100'
        ]);

        DB::beginTransaction();
        try {
            // Ambil setting COA
            $settingCOA = SettingCOA::first();
            if (!$settingCOA) {
                throw new \Exception('Setting COA untuk PO Penjualan belum dikonfigurasi');
            }

            // Hitung total
            $totalHarga = 0;
            $totalItem = 0;
            $ongkir = 0;
            $totalDiskonItem = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['harga_jual'] * $item['jumlah'];
                $diskonItem = $subtotal * ($item['diskon'] ?? 0) / 100;
                $subtotalSetelahDiskon = $subtotal - $diskonItem;
                
                // Debug log
                \Log::info('Processing item:', [
                    'is_ongkir' => $item['is_ongkir'] ?? 0,
                    'harga_jual' => $item['harga_jual'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $subtotal,
                    'subtotal_setelah_diskon' => $subtotalSetelahDiskon
                ]);
                
                if (isset($item['is_ongkir']) && $item['is_ongkir'] == 1) {
                    $ongkir += $subtotalSetelahDiskon;
                } else {
                    $totalHarga += $subtotalSetelahDiskon;
                    $totalItem += $item['jumlah'];
                    $totalDiskonItem += $diskonItem;
                }
            }

            $diskon = $request->diskon ?? 0;
            $diskonGlobal = $totalHarga * $diskon / 100;
            $totalSetelahDiskon = $totalHarga - $diskonGlobal;
            $bayar = $totalSetelahDiskon + $ongkir;
            $diterima = $request->diterima ?? 0;

            // Simpan PO Penjualan
            $poPenjualan = PoPenjualan::create([
                'no_po' => $request->no_po,
                'tanggal' => $request->tanggal,
                'id_member' => $request->id_member,
                'id_outlet' => $request->id_outlet,
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
                'diskon' => $diskon,
                'ongkir' => $ongkir,
                'bayar' => $bayar,
                'diterima' => $diterima,
                'status' => $request->status ?? 'menunggu',
                'id_user' => auth()->id(),
                'tanggal_tempo' => $request->tanggal_tempo
            ]);

            // Simpan detail items
            foreach ($request->items as $item) {
                $isOngkir = isset($item['is_ongkir']) && $item['is_ongkir'] == 1;
                $subtotal = $item['harga_jual'] * $item['jumlah'];
                $subtotalSetelahDiskon = $subtotal - ($subtotal * ($item['diskon'] ?? 0) / 100);
                
                PoPenjualanDetail::create([
                    'id_po_penjualan' => $poPenjualan->id_po_penjualan,
                    'id_produk' => $isOngkir ? null : $item['id_produk'],
                    'harga_jual' => $item['harga_jual'],
                    'jumlah' => $item['jumlah'],
                    'diskon' => $item['diskon'] ?? 0,
                    'subtotal' => $subtotalSetelahDiskon,
                    'tipe_item' => $isOngkir ? 'ongkir' : 'produk',
                    'keterangan' => $item['keterangan'] ?? null
                ]);

                // Kurangi stok dengan metode FIFO jika produk (bukan ongkir)
                if (!$isOngkir) {
                    $produk = Produk::find($item['id_produk']);
                    if ($produk) {
                        // Gunakan metode FIFO untuk mengurangi stok
                        $produk->reduceStock($item['jumlah']);
                    }
                }
            }

            // Handle piutang jika diterima < bayar
            if ($diterima < $bayar) {
                $piutangAmount = $bayar - $diterima;
                $member = Member::find($request->id_member);
                
                Piutang::create([
                    'id_po_penjualan' => $poPenjualan->id_po_penjualan,
                    'id_member' => $request->id_member,
                    'id_outlet' => $request->id_outlet,
                    'nama' => $member->nama,
                    'piutang' => $piutangAmount,
                    'status' => 'belum_lunas',
                    'tanggal_tempo' => $request->tanggal_tempo
                ]);

                // Update piutang member
                $member->piutang += $piutangAmount;
                $member->save();
            }

            // Buat jurnal berdasarkan status
            $this->createJournalEntries($poPenjualan, $settingCOA);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PO Penjualan berhasil dibuat',
                'redirect' => route('po-penjualan.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating PO Penjualan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PO Penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $poPenjualan = PoPenjualan::with(['details.produk', 'member', 'outlet', 'user'])->findOrFail($id);
        return view('po_penjualan.show', compact('poPenjualan'));
    }

    public function edit($id)
    {
        $poPenjualan = PoPenjualan::with('details')->findOrFail($id);
        
        // Cek apakah user hanya boleh edit status
        $editMode = 'status_only'; // atau bisa dari permission
        
        return view('po_penjualan.edit', compact('poPenjualan', 'editMode'));
    }

    public function update(Request $request, $id)
    {
        $poPenjualan = PoPenjualan::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'id_member' => 'required|exists:member,id_member',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required_without:items.*.is_ongkir',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.keterangan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Hitung ulang total
            $totalHarga = 0;
            $totalItem = 0;
            $ongkir = 0;

            foreach ($request->items as $item) {
                if (isset($item['is_ongkir']) && $item['is_ongkir']) {
                    $ongkir += $item['harga_jual'] * $item['jumlah'];
                } else {
                    $totalHarga += $item['harga_jual'] * $item['jumlah'];
                    $totalItem += $item['jumlah'];
                }
            }

            $diskon = $request->diskon ?? 0;
            $totalSetelahDiskon = $totalHarga - ($totalHarga * $diskon / 100);
            $bayar = $totalSetelahDiskon + $ongkir;

            // Update PO Penjualan
            $poPenjualan->update([
                'tanggal' => $request->tanggal,
                'id_member' => $request->id_member,
                'id_outlet' => $request->id_outlet,
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
                'diskon' => $diskon,
                'ongkir' => $ongkir,
                'bayar' => $bayar,
                'diterima' => $request->diterima ?? 0,
                'status' => $request->status ?? 'menunggu',
                'tanggal_tempo' => $request->tanggal_tempo
            ]);

            // Hapus detail lama
            $poPenjualan->details()->delete();

            // Simpan detail baru
            foreach ($request->items as $item) {
                $isOngkir = isset($item['is_ongkir']) && $item['is_ongkir'];
                
                PoPenjualanDetail::create([
                    'id_po_penjualan' => $poPenjualan->id_po_penjualan,
                    'id_produk' => $isOngkir ? null : $item['id_produk'],
                    'harga_jual' => $item['harga_jual'],
                    'jumlah' => $item['jumlah'],
                    'diskon' => $item['diskon'] ?? 0,
                    'subtotal' => $item['harga_jual'] * $item['jumlah'],
                    'tipe_item' => $isOngkir ? 'ongkir' : 'produk',
                    'keterangan' => $item['keterangan'] ?? null
                ]);
            }

            // Update piutang
            $piutang = Piutang::where('id_po_penjualan', $poPenjualan->id_po_penjualan)->first();
            
            if ($request->diterima < $bayar) {
                $piutangAmount = $bayar - $request->diterima;
                
                if ($piutang) {
                    $piutang->update([
                        'piutang' => $piutangAmount,
                        'status' => 'belum_lunas'
                    ]);
                } else {
                    Piutang::create([
                        'id_po_penjualan' => $poPenjualan->id_po_penjualan,
                        'id_member' => $request->id_member,
                        'id_outlet' => $request->id_outlet,
                        'nama' => $poPenjualan->member->nama,
                        'piutang' => $piutangAmount,
                        'status' => 'belum_lunas'
                    ]);
                }
            } else {
                if ($piutang) {
                    $piutang->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PO Penjualan berhasil diperbarui',
                'redirect' => route('po-penjualan.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating PO Penjualan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui PO Penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $poPenjualan = PoPenjualan::findOrFail($id);

        DB::beginTransaction();
        try {
            // Hapus piutang terkait
            Piutang::where('id_po_penjualan', $poPenjualan->id_po_penjualan)->delete();
            
            // Hapus detail
            $poPenjualan->details()->delete();
            
            // Hapus PO
            $poPenjualan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PO Penjualan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus PO Penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:menunggu,lunas,gagal',
        'diterima' => 'nullable|numeric|min:0',
        'keterangan' => 'nullable|string|max:500'
    ]);

    DB::beginTransaction();
    try {
        $poPenjualan = PoPenjualan::with('member')->findOrFail($id);
        $oldStatus = $poPenjualan->status;
        
        // Update status
        $poPenjualan->update([
            'status' => $request->status,
            'diterima' => $request->diterima ?? $poPenjualan->diterima
        ]);

        $settingCOA = SettingCOA::first();
        
        // Handle perubahan status
        if ($oldStatus !== $request->status) {
            // Jika status berubah dari menunggu ke lunas
            if ($oldStatus === 'menunggu' && $request->status === 'lunas') {
                // Update piutang
                $piutang = Piutang::where('id_po_penjualan', $id)->first();
                if ($piutang) {
                    $piutang->update(['status' => 'lunas']);
                    
                    // Kurangi piutang member
                    if ($poPenjualan->member) {
                        $poPenjualan->member->piutang -= $piutang->piutang;
                        $poPenjualan->member->save();
                    }
                }
                
                // Buat jurnal HPP - perbaiki pemanggilan method
                if ($settingCOA) {
                    $this->createHppJournal($poPenjualan, $settingCOA);
                }
            }
            
            // Jika status berubah ke gagal, handle reversal
            if ($request->status === 'gagal') {
                // Kembalikan stok jika status menjadi gagal
                foreach ($poPenjualan->details()->where('tipe_item', 'produk')->get() as $detail) {
                    if ($detail->produk) {
                        $averageHpp = $this->calculateAverageHpp($detail->produk, $detail->jumlah);
                        $detail->produk->addStock(
                            $averageHpp,
                            $detail->jumlah,
                            'Reversal PO Gagal: ' . $poPenjualan->no_po
                        );
                    }
                }
            }

            // Buat jurnal untuk status baru
            if ($settingCOA) {
                $this->createJournalEntries($poPenjualan, $settingCOA);
            }
        }

        // Log perubahan status
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_status',
            'description' => 'Mengubah status PO ' . $poPenjualan->no_po . ' dari ' . $oldStatus . ' ke ' . $request->status . ($request->keterangan ? ': ' . $request->keterangan : ''),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Status PO berhasil diperbarui'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating PO status: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui status: ' . $e->getMessage()
        ], 500);
    }
}

    public function print($id)
    {
        $poPenjualan = PoPenjualan::with(['details.produk', 'member', 'outlet', 'user'])->findOrFail($id);
        $setting = Setting::first();

        $pdf = PDF::loadView('po_penjualan.print_po', compact('poPenjualan', 'setting'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('PO-' . $poPenjualan->no_po . '.pdf');
    }

    public function getProductPrice($id)
    {
        $produk = Produk::findOrFail($id);
        return response()->json([
            'harga_jual' => $produk->harga_jual,
            'diskon' => $produk->diskon
        ]);
    }

    private function createJournalEntries($poPenjualan, $settingCOA)
    {
        $journalService = new JournalEntryService();
        $accountingBookId = $settingCOA->accounting_book_id;

        if (!$accountingBookId) {
            throw new \Exception('Accounting book belum dikonfigurasi');
        }

        // Pastikan semua akun yang diperlukan tersedia
        $requiredAccounts = [
            'akun_piutang_po',
            'akun_pendapatan_po', 
            'akun_hpp_po',
            'akun_persediaan_po',
            'akun_ongkir_po',
            'akun_uang_muka_po',
            'akun_pendapatan_diterima_dimuka',
            'akun_diskon_penjualan'
        ];

        foreach ($requiredAccounts as $accountField) {
            if (empty($settingCOA->$accountField)) {
                throw new \Exception("Akun {$accountField} belum dikonfigurasi di setting COA");
            }
        }

        $entries = [];
        $totalHargaSetelahDiskon = $poPenjualan->total_harga - ($poPenjualan->total_harga * $poPenjualan->diskon / 100);

        try {
            switch ($poPenjualan->status) {
                case 'menunggu':
                    // Jurnal untuk PO status menunggu (belum lunas)
                    if ($poPenjualan->diterima > 0) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($settingCOA->akun_uang_muka_po),
                            'debit' => $poPenjualan->diterima,
                            'memo' => 'Uang muka PO ' . $poPenjualan->no_po
                        ];
                    }

                    if ($poPenjualan->diterima < $poPenjualan->bayar) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($settingCOA->akun_piutang_po),
                            'debit' => $poPenjualan->bayar - $poPenjualan->diterima,
                            'memo' => 'Piutang PO ' . $poPenjualan->no_po
                        ];
                    }

                    // Pendapatan diterima dimuka (untuk bagian yang sudah dibayar)
                    if ($poPenjualan->diterima > 0) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($settingCOA->akun_pendapatan_diterima_dimuka),
                            'credit' => $poPenjualan->diterima,
                            'memo' => 'Pendapatan diterima dimuka PO ' . $poPenjualan->no_po
                        ];
                    }

                    // Pendapatan (untuk bagian yang masih piutang)
                    if ($poPenjualan->diterima < $poPenjualan->bayar) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($settingCOA->akun_pendapatan_po),
                            'credit' => $totalHargaSetelahDiskon - $poPenjualan->diterima,
                            'memo' => 'Pendapatan PO ' . $poPenjualan->no_po
                        ];
                    }

                    // Ongkir
                    if ($poPenjualan->ongkir > 0) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($settingCOA->akun_ongkir_po),
                            'credit' => $poPenjualan->ongkir,
                            'memo' => 'Pendapatan ongkir PO ' . $poPenjualan->no_po
                        ];
                    }

                    // Diskon penjualan
                    $diskonAmount = $poPenjualan->total_harga * $poPenjualan->diskon / 100;
                    if ($diskonAmount > 0) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($settingCOA->akun_diskon_penjualan),
                            'debit' => $diskonAmount,
                            'memo' => 'Diskon penjualan PO ' . $poPenjualan->no_po
                        ];
                    }
                    break;

                case 'lunas':
                    // Jurnal untuk PO status lunas (semua sudah dibayar)
                    if ($poPenjualan->diterima > 0) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode('1.01.01'), // Asumsikan akun kas
                            'debit' => $poPenjualan->diterima,
                            'memo' => 'Pembayaran PO ' . $poPenjualan->no_po
                        ];
                    }

                    // Pendapatan
                    $entries[] = [
                        'account_id' => $this->getAccountIdByCode($settingCOA->akun_pendapatan_po),
                        'credit' => $totalHargaSetelahDiskon,
                        'memo' => 'Pendapatan PO ' . $poPenjualan->no_po
                    ];

                    // Ongkir
                    if ($poPenjualan->ongkir > 0) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($settingCOA->akun_ongkir_po),
                            'credit' => $poPenjualan->ongkir,
                            'memo' => 'Pendapatan ongkir PO ' . $poPenjualan->no_po
                        ];
                    }

                    // Diskon penjualan
                    $diskonAmount = $poPenjualan->total_harga * $poPenjualan->diskon / 100;
                    if ($diskonAmount > 0) {
                        $entries[] = [
                            'account_id' => $this->getAccountIdByCode($settingCOA->akun_diskon_penjualan),
                            'debit' => $diskonAmount,
                            'memo' => 'Diskon penjualan PO ' . $poPenjualan->no_po
                        ];
                    }

                    // Jurnal HPP (jika diperlukan)
                    $this->createHppJournal($poPenjualan, $settingCOA, $accountingBookId);
                    break;
            }

            if (!empty($entries)) {
                $journalService->createAutomaticJournal(
                    'po_penjualan',
                    $poPenjualan->id_po_penjualan,
                    $poPenjualan->tanggal,
                    'PO Penjualan ' . $poPenjualan->no_po . ' - Status: ' . $poPenjualan->status,
                    $entries,
                    $accountingBookId
                );
            }

        } catch (\Exception $e) {
            Log::error('Gagal membuat jurnal untuk PO ' . $poPenjualan->no_po . ': ' . $e->getMessage());
            throw new \Exception('Gagal membuat jurnal: ' . $e->getMessage());
        }
    }

    /**
 * Buat jurnal HPP untuk PO Penjualan
 */
private function createHppJournal($poPenjualan, $settingCOA)
{
    try {
        $journalService = new JournalEntryService();
        $accountingBookId = $settingCOA->accounting_book_id;
        
        if (!$accountingBookId) {
            throw new \Exception('Accounting book belum dikonfigurasi');
        }

        $totalHpp = 0;

        // Hitung total HPP dari detail produk
        foreach ($poPenjualan->details()->where('tipe_item', 'produk')->get() as $detail) {
            if ($detail->produk) {
                // Hitung HPP berdasarkan metode FIFO
                $hpp = $this->calculateHppFifo($detail->produk, $detail->jumlah);
                $totalHpp += $hpp;
            }
        }

        // Jika tidak ada HPP, tidak perlu buat jurnal
        if ($totalHpp <= 0) {
            \Log::info('Tidak ada HPP untuk PO: ' . $poPenjualan->no_po);
            return null;
        }

        $entries = [
            [
                'account_id' => $this->getAccountIdByCode($settingCOA->akun_hpp_po),
                'debit' => $totalHpp,
                'memo' => 'HPP PO ' . $poPenjualan->no_po
            ],
            [
                'account_id' => $this->getAccountIdByCode($settingCOA->akun_persediaan_po),
                'credit' => $totalHpp,
                'memo' => 'Persediaan keluar PO ' . $poPenjualan->no_po
            ]
        ];

        \Log::info('Membuat jurnal HPP untuk PO: ' . $poPenjualan->no_po, [
            'total_hpp' => $totalHpp,
            'entries' => $entries
        ]);

        return $journalService->createAutomaticJournal(
            'hpp_po_penjualan',
            $poPenjualan->id_po_penjualan,
            $poPenjualan->tanggal,
            'HPP PO Penjualan ' . $poPenjualan->no_po,
            $entries,
            $accountingBookId
        );

    } catch (\Exception $e) {
        \Log::error('Gagal membuat jurnal HPP untuk PO ' . $poPenjualan->no_po . ': ' . $e->getMessage());
        throw new \Exception('Gagal membuat jurnal HPP: ' . $e->getMessage());
    }
}

/**
 * Hitung HPP dengan metode FIFO
 */
private function calculateHppFifo($produk, $jumlah)
{
    $sisaKurang = $jumlah;
    $totalHpp = 0;

    $hppProduks = $produk->hppProduk()
        ->where('stok', '>', 0)
        ->orderBy('created_at', 'asc')
        ->get();

    foreach ($hppProduks as $hppProduk) {
        if ($sisaKurang <= 0) break;

        if ($hppProduk->stok >= $sisaKurang) {
            $totalHpp += $hppProduk->hpp * $sisaKurang;
            $sisaKurang = 0;
        } else {
            $totalHpp += $hppProduk->hpp * $hppProduk->stok;
            $sisaKurang -= $hppProduk->stok;
        }
    }

    // Jika stok tidak mencukupi, gunakan HPP terakhir
    if ($sisaKurang > 0) {
        $lastHpp = $produk->hppProduk()->orderBy('created_at', 'desc')->first();
        if ($lastHpp) {
            $totalHpp += $lastHpp->hpp * $sisaKurang;
        }
    }

    return $totalHpp;
}

/**
 * Hitung average HPP untuk reversal
 */
private function calculateAverageHpp($produk, $jumlah)
{
    $hppProduks = $produk->hppProduk()
        ->where('stok', '>', 0)
        ->get();

    if ($hppProduks->isEmpty()) {
        return 0;
    }

    $totalHpp = $hppProduks->sum(function($hpp) {
        return $hpp->hpp * $hpp->stok;
    });

    $totalStok = $hppProduks->sum('stok');

    return $totalStok > 0 ? $totalHpp / $totalStok : 0;
}

    /**
     * Helper untuk mendapatkan account_id dari kode akun
     */
    private function getAccountIdByCode($code)
    {
        $accountFromConfig = $this->findAccountByCodeInConfig($code);
    
        if ($accountFromConfig) {
            // Buat record baru di database
            $newAccount = ChartOfAccount::create([
                'code' => $accountFromConfig['code'],
                'name' => $accountFromConfig['name'],
                'type' => $accountFromConfig['type'],
                'is_active' => true,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);
            
            return $newAccount->id;
        }
    }

    private function findAccountByCodeInConfig($code)
    {
        $allAccounts = collect($this->getAllAccountsFromConfig());
        return $allAccounts->firstWhere('code', $code);
    }

    /**
     * Get all accounts from config (copy dari SettingCOAController)
     */
    private function getAllAccountsFromConfig()
    {
        $accounts = [];
        $this->flattenAccounts(config('accounts.accounts', []), $accounts);
        return $accounts;
    }

    private function flattenAccounts($accountList, &$result, $level = 0)
    {
        foreach ($accountList as $account) {
            // Only include accounts that don't have children (leaf nodes)
            if (!isset($account['children']) || empty($account['children'])) {
                $result[] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'is_active' => $account['is_active'] ?? true,
                    'level' => $level
                ];
            }
            
            // If has children, recursively process them
            if (isset($account['children']) && !empty($account['children'])) {
                $this->flattenAccounts($account['children'], $result, $level + 1);
            }
        }
    }

    public function printReport(Request $request)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        $selectedOutlet = $request->id_outlet;
        $start_date = $request->start_date ?? date('Y-m-d');
        $end_date = $request->end_date ?? date('Y-m-d');
        $status = $request->status;

        $poPenjualan = PoPenjualan::with(['member', 'outlet', 'user'])
            ->when($userOutlets, function ($query) use ($userOutlets, $selectedOutlet) {
                $query->whereIn('id_outlet', $userOutlets);
                if ($selectedOutlet) {
                    $query->where('id_outlet', $selectedOutlet);
                }
                return $query;
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                $query->whereDate('tanggal', '>=', $start_date)
                    ->whereDate('tanggal', '<=', $end_date);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->where('total_item', '>', 0)
            ->latest()
            ->get();

        $setting = \App\Models\Setting::first();
        $outlet = $selectedOutlet ? Outlet::find($selectedOutlet) : null;

        $pdf = PDF::loadView('po_penjualan.print_report', compact('poPenjualan', 'setting', 'start_date', 'end_date', 'outlet', 'status'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->stream('Laporan-PO-Penjualan-'. date('Y-m-d-his') .'.pdf');
    }

    
}