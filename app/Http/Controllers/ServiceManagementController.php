<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;
use App\Models\Member;
use App\Models\Produk;
use App\Models\OngkosKirim;
use App\Models\MesinCustomer;
use App\Models\ServiceInvoice;
use App\Models\ServiceInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Exports\ServiceInvoiceExport;
use Maatwebsite\Excel\Facades\Excel;

class ServiceManagementController extends Controller
{
    use HasOutletFilter;
    // Halaman Ongkos Kirim
    public function ongkosKirimIndex(Request $request)
    {
        if ($request->ajax()) {
            $ongkosKirim = OngkosKirim::query();
            return DataTables::of($ongkosKirim)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                $btn = '<div class="btn-group">';
                $btn .= '<button type="button" onclick="editForm(`'.route('service.ongkos-kirim.update', $row->id_ongkir).'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>';
                $btn .= '<button type="button" onclick="deleteData(`'.route('service.ongkos-kirim.destroy', $row->id_ongkir).'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
                $btn .= '</div>';
                return $btn;
            })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        
        return view('service_management.ongkos_kirim.index');
    }

    public function ongkosKirimStore(Request $request)
    {
        $request->validate([
            'daerah' => 'required',
            'harga' => 'required|numeric'
        ]);

        OngkosKirim::create($request->all());
        return redirect()->back()->with('success', 'Data ongkos kirim berhasil disimpan');
    }

    public function ongkosKirimEdit($id)
    {
        $ongkosKirim = OngkosKirim::findOrFail($id);
        return response()->json($ongkosKirim);
    }

    public function ongkosKirimUpdate(Request $request, $id)
    {
        $request->validate([
            'daerah' => 'required',
            'harga' => 'required|numeric'
        ]);

        $ongkosKirim = OngkosKirim::findOrFail($id);
        $ongkosKirim->update($request->all());
        return redirect()->back()->with('success', 'Data ongkos kirim berhasil diupdate');
    }

    public function ongkosKirimDestroy($id)
    {
        $ongkosKirim = OngkosKirim::findOrFail($id);
        $ongkosKirim->delete();
        return redirect()->back()->with('success', 'Data ongkos kirim berhasil dihapus');
    }

    // Halaman Mesin Customer
    public function mesinCustomerIndex(Request $request)
    {
        if ($request->ajax()) {
            $mesinCustomers = MesinCustomer::with(['member', 'ongkosKirim', 'produk'])->get();
            
            return DataTables::of($mesinCustomers)
                ->addIndexColumn()
                ->addColumn('produk', function ($row) {
                    $produkList = '';
                    foreach ($row->produk as $produk) {
                        $biayaService = $produk->pivot->biaya_service ? ' (Rp ' . number_format($produk->pivot->biaya_service, 0, ',', '.') . ')' : '';
                        $produkList .= '<span class="label label-primary" style="margin: 2px; display: inline-block;">' . $produk->nama_produk . $biayaService . '</span> ';
                    }
                    return $produkList;
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" onclick="editForm(`'.route('service.mesin-customer.update', $row->id_mesin_customer).'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>';
                    $btn .= '<button type="button" onclick="deleteData(`'.route('service.mesin-customer.destroy', $row->id_mesin_customer).'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['produk', 'aksi'])
                ->make(true);
        }
        
        // Get data only from accessible outlets
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        $members = Member::whereIn('id_outlet', $accessibleOutletIds)->get();
        $ongkosKirim = OngkosKirim::all();
        $produks = Produk::whereIn('id_outlet', $accessibleOutletIds)->get();
        
        return view('service_management.mesin_customer.index', compact('members', 'ongkosKirim', 'produks'));
    }

    public function mesinCustomerStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_member' => 'required|exists:member,id_member',
            'id_ongkir' => 'required|exists:ongkos_kirim,id_ongkir',
            'produk' => 'required|array|min:1',
            'produk.*' => 'required|exists:produk,id_produk',
            'jumlah_produk' => 'required|array|min:1',
            'jumlah_produk.*' => 'required|numeric|min:1',
            'biaya_service_produk' => 'required|array|min:1',
            'biaya_service_produk.*' => 'required|numeric|min:0',
            'closing_type_produk' => 'required|array|min:1',
            'closing_type_produk.*' => 'required|in:jual_putus,deposit'
        ], [
            'produk.required' => 'Pilih minimal satu produk',
            'produk.min' => 'Pilih minimal satu produk',
            'jumlah_produk.required' => 'Jumlah produk harus diisi',
            'biaya_service_produk.required' => 'Biaya service harus diisi',
            'closing_type_produk.required' => 'Closing type harus dipilih',
            'produk.*.required' => 'Produk harus dipilih',
            'jumlah_produk.*.required' => 'Jumlah produk harus diisi',
            'biaya_service_produk.*.required' => 'Biaya service harus diisi',
            'closing_type_produk.*.required' => 'Closing type harus dipilih'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Pastikan array memiliki length yang sama
        if (count($request->produk) !== count($request->jumlah_produk) ||
            count($request->produk) !== count($request->biaya_service_produk) || 
            count($request->produk) !== count($request->closing_type_produk)) {
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Data produk tidak konsisten']
            ], 422);
        }

        DB::transaction(function () use ($request) {
            $mesinCustomer = MesinCustomer::create($request->only(['id_member', 'id_ongkir']));
            
            // Attach produk dengan biaya service dan closing type
            $produkData = [];
            foreach ($request->produk as $index => $produkId) {
                $produkData[$produkId] = [
                    'jumlah' => $request->jumlah_produk[$index] ?? 1,
                    'biaya_service' => $request->biaya_service_produk[$index] ?? 0,
                    'closing_type' => $request->closing_type_produk[$index] ?? 'jual_putus'
                ];
            }
            
            $mesinCustomer->produk()->attach($produkData);
        });

        return response()->json(['success' => true, 'message' => 'Data mesin customer berhasil disimpan']);
    }

    public function mesinCustomerEdit($id)
    {
        $mesinCustomer = MesinCustomer::with(['member', 'produk'])->findOrFail($id);
        return response()->json($mesinCustomer);
    }

    public function mesinCustomerUpdate(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_member' => 'required|exists:member,id_member',
            'id_ongkir' => 'required|exists:ongkos_kirim,id_ongkir',
            'produk' => 'required|array|min:1',
            'produk.*' => 'required|exists:produk,id_produk',
            'biaya_service_produk' => 'required|array|min:1',
            'biaya_service_produk.*' => 'required|numeric|min:0',
            'closing_type_produk' => 'required|array|min:1',
            'closing_type_produk.*' => 'required|in:jual_putus,deposit'
        ], [
            'produk.required' => 'Pilih minimal satu produk',
            'produk.min' => 'Pilih minimal satu produk',
            'biaya_service_produk.required' => 'Biaya service harus diisi',
            'closing_type_produk.required' => 'Closing type harus dipilih',
            'produk.*.required' => 'Produk harus dipilih',
            'biaya_service_produk.*.required' => 'Biaya service harus diisi',
            'closing_type_produk.*.required' => 'Closing type harus dipilih'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Pastikan array memiliki length yang sama
        if (count($request->produk) !== count($request->biaya_service_produk) || 
            count($request->produk) !== count($request->closing_type_produk)) {
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Data produk tidak konsisten']
            ], 422);
        }

        DB::transaction(function () use ($request, $id) {
            $mesinCustomer = MesinCustomer::findOrFail($id);
            $mesinCustomer->update($request->only(['id_member', 'id_ongkir']));
            
            // Sync produk dengan biaya service dan closing type
            $produkData = [];
            foreach ($request->produk as $index => $produkId) {
                $produkData[$produkId] = [
                    'biaya_service' => $request->biaya_service_produk[$index] ?? 0,
                    'closing_type' => $request->closing_type_produk[$index] ?? 'jual_putus'
                ];
            }
            
            $mesinCustomer->produk()->sync($produkData);
        });

        return response()->json(['success' => true, 'message' => 'Data mesin customer berhasil diupdate']);
    }

    public function mesinCustomerDestroy($id)
    {
        $mesinCustomer = MesinCustomer::findOrFail($id);
        $mesinCustomer->delete();
        return redirect()->back()->with('success', 'Data mesin customer berhasil dihapus');
    }

    // Halaman Generate Invoice
    public function invoiceIndex()
    {
        // Gunakan whereHas untuk filter member yang memiliki mesin customers
        $members = Member::whereHas('mesinCustomers')->with('mesinCustomers')->get();
        return view('service_management.invoice.index', compact('members'));
    }

    public function getMesinCustomer($id_member)
    {
        $mesinCustomers = MesinCustomer::with(['produk', 'ongkosKirim'])
            ->where('id_member', $id_member)
            ->get();
        
        return response()->json($mesinCustomers);
    }

    public function getMesinCustomerDetail($id)
    {
        $mesinCustomer = MesinCustomer::with([
            'produk' => function($query) {
                $query->withPivot('biaya_service', 'closing_type');
            }, 
            'ongkosKirim',
            'member'
        ])->findOrFail($id);
        
        return response()->json($mesinCustomer);
    }

    public function invoiceStore(Request $request)
    {
        // Helper function untuk parse formatted number
        $parseNumber = function($value) {
            if (is_numeric($value)) {
                return (float) $value;
            }
            if (is_string($value)) {
                return (float) str_replace(['.', ','], '', $value);
            }
            return (float) $value;
        };

        // Custom validation rules yang lebih toleran
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'tanggal_mulai_service' => 'required|date',
            'tanggal_selesai_service' => 'required|date|after_or_equal:tanggal_mulai_service',
            'id_member' => 'required|exists:member,id_member',
            'id_mesin_customer' => 'nullable|exists:mesin_customer,id_mesin_customer',
            'jenis_service' => 'required|string|in:Service,Maintenance,Pembelian Sparepart,Lainnya',
            'keterangan_service' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.deskripsi' => 'required|string|max:255',
            'items.*.keterangan' => 'nullable|string|max:255',
            'items.*.kuantitas' => 'required|numeric|min:0.01',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
            'items.*.tipe' => 'required|in:produk,ongkir,teknisi,sparepart,lainnya',
            'items.*.is_sparepart' => 'sometimes|boolean',
            'items.*.jenis_kendaraan' => 'nullable|in:mobil,motor',
            'items.*.kode_sparepart' => 'nullable|string|max:50',
            'jumlah_teknisi' => 'nullable|numeric|min:0',
            'jumlah_jam' => 'nullable|numeric|min:0',
            'biaya_teknisi' => 'nullable|numeric|min:0',
            'is_garansi' => 'sometimes|boolean',
            'diskon' => 'required|numeric|min:0',
            'total_setelah_diskon' => 'required|numeric|min:0',
            'tanggal_service_berikutnya' => 'nullable|date|after_or_equal:tanggal_selesai_service',
        ], [
            'items.required' => 'Minimal harus ada satu item invoice',
            'items.min' => 'Minimal harus ada satu item invoice',
            'items.*.deskripsi.required' => 'Deskripsi item wajib diisi',
            'items.*.kuantitas.min' => 'Kuantitas harus lebih dari 0',
            'items.*.harga.min' => 'Harga tidak boleh negatif',
            'tanggal_selesai_service.after_or_equal' => 'Tanggal selesai service harus setelah atau sama dengan tanggal mulai service',
            'tanggal_service_berikutnya.after_or_equal' => 'Tanggal service berikutnya harus setelah atau sama dengan tanggal selesai service',
        ]);

        if ($validator->fails()) {
            \Log::error('Invoice validation failed:', $validator->errors()->toArray());
            \Log::error('Request data:', $request->all());
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $parseNumber) {
                // Generate invoice number dengan format baru
                $invoiceNumber = \App\Models\InvoiceServiceCounter::generateInvoiceNumber();

                // Parse total dari items
                $subtotal = 0;
                $statusFix = 'menunggu';

                foreach ($request->items as $item) {
                    $itemSubtotal = $parseNumber($item['subtotal']);
                    $subtotal += $itemSubtotal;
                }

                $tanggalWithTime = \Carbon\Carbon::parse($request->tanggal . ' ' . now()->format('H:i:s'));
                //$dueDate = \Carbon\Carbon::parse($tanggalWithTime)->addDays(7);
                $dueDate = \Carbon\Carbon::parse($request->tanggal_selesai_service)->addDays(7);

                if($request->is_garansi) {
                    $statusFix = 'lunas';
                }

                $tanggalServiceBerikutnya = null;
                if ($request->has('tanggal_service_berikutnya') && $request->tanggal_service_berikutnya) {
                    $tanggalServiceBerikutnya = $request->tanggal_service_berikutnya;
                }

                $invoice = ServiceInvoice::create([
                    'no_invoice' => $invoiceNumber,
                    'tanggal' => $tanggalWithTime,
                    'tanggal_mulai_service' => $request->tanggal_mulai_service,
                    'tanggal_selesai_service' => $request->tanggal_selesai_service,
                    'tanggal_service_berikutnya' => $tanggalServiceBerikutnya,
                    'id_member' => $request->id_member,
                    'id_mesin_customer' => $request->id_mesin_customer,
                    'id_user' => auth()->user()->id,
                    'is_garansi' => $request->is_garansi ?? false,
                    'diskon' => $parseNumber($request->diskon),
                    'total_sebelum_diskon' => $subtotal,
                    'jenis_service' => $request->jenis_service,
                    'keterangan_service' => $request->keterangan_service,
                    'jumlah_teknisi' => $request->jumlah_teknisi ?? 0,
                    'jumlah_jam' => $request->jumlah_jam ?? 0,
                    'biaya_teknisi' => $parseNumber($request->biaya_teknisi ?? 0),
                    'total' => $parseNumber($request->total_setelah_diskon),
                    'status' => $statusFix,
                    'due_date' => $dueDate
                ]);

                foreach ($request->items as $item) {
                    // Handle boolean value untuk is_sparepart
                    $isSparepart = false;
                    if (isset($item['is_sparepart'])) {
                        if (is_bool($item['is_sparepart'])) {
                            $isSparepart = $item['is_sparepart'];
                        } else {
                            $isSparepart = filter_var($item['is_sparepart'], FILTER_VALIDATE_BOOLEAN);
                        }
                    }


                    // Hitung harga setelah diskon
                    $harga = $parseNumber($item['harga']);
                    $diskon = $parseNumber($item['diskon'] ?? 0);
                    $hargaSetelahDiskon = $harga - $diskon;
                    if ($hargaSetelahDiskon < 0) $hargaSetelahDiskon = 0;

                    ServiceInvoiceItem::create([
                        'id_service_invoice' => $invoice->id_service_invoice,
                        'id_produk' => !empty($item['id_produk']) ? $item['id_produk'] : null,
                        'id_sparepart' => !empty($item['id_sparepart']) ? $item['id_sparepart'] : null,
                        'deskripsi' => $item['deskripsi'],
                        'keterangan' => $item['keterangan'] ?? null,
                        'kuantitas' => $item['kuantitas'],
                        'satuan' => $item['satuan'] ?? null,
                        'harga' => $parseNumber($item['harga']),
                        'diskon' => $diskon,
                        'harga_setelah_diskon' => $hargaSetelahDiskon,
                        'subtotal' => $parseNumber($item['subtotal']),
                        'tipe' => $item['tipe'],
                        'is_sparepart' => $isSparepart,
                        'jenis_kendaraan' => $item['jenis_kendaraan'] ?? null,
                        'kode_sparepart' => $item['kode_sparepart'] ?? null,
                    ]);

                    if ($isSparepart && !empty($item['id_sparepart'])) {
                        $sparepart = \App\Models\Sparepart::find($item['id_sparepart']);
                        if ($sparepart) {
                            $jumlah = $item['kuantitas'];
                            if (!$sparepart->isStokMencukupi($jumlah)) {
                                throw new \Exception("Stok sparepart {$sparepart->nama_sparepart} tidak mencukupi. Stok tersedia: {$sparepart->stok}, yang dibutuhkan: {$jumlah}");
                            }
                            
                            // Kurangi stok
                            if (!$sparepart->kurangiStok($jumlah)) {
                                throw new \Exception("Gagal mengurangi stok sparepart {$sparepart->nama_sparepart}");
                            }
                            
                            \Log::info("Stok sparepart {$sparepart->nama_sparepart} berkurang {$jumlah}. Stok sekarang: {$sparepart->stok}");
                        } else {
                            throw new \Exception("Sparepart dengan ID {$item['id_sparepart']} tidak ditemukan");
                        }
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Invoice berhasil dibuat']);

        } catch (\Exception $e) {
            \Log::error('Invoice store error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function invoiceHistory(Request $request)
    {
        $status = $request->get('status', 'terkini');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        
        if ($request->ajax()) {
            $query = ServiceInvoice::with(['member', 'user', 'invoiceSebelumnya']);
            
            // Filter berdasarkan status tab
            switch ($status) {
                case 'menunggu':
                    $query->where('status', 'menunggu');
                    break;
                case 'lunas':
                    $query->where('status', 'lunas');
                    break;
                case 'gagal':
                    $query->where('status', 'gagal');
                    break;
                 case 'service-berikutnya':
                    $query->whereNotNull('tanggal_service_berikutnya')
                        ->where('tanggal_service_berikutnya', '>=', now())
                        ->orderBy('tanggal_service_berikutnya', 'asc');
                    break;
                case 'terkini':
                default:
                    // Semua data
                    break;
            }
            
            // Filter berdasarkan tanggal
            if ($start_date && $end_date) {
                $query->whereBetween('tanggal', [$start_date, $end_date]);
            } elseif ($start_date) {
                $query->where('tanggal', '>=', $start_date);
            } elseif ($end_date) {
                $query->where('tanggal', '<=', $end_date);
            }
            
            // Sorting berdasarkan tab
            if ($status === 'menunggu') {
                $query->orderBy('due_date', 'asc');
            } else {
                $query->orderBy('tanggal', 'desc');
            }
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($row) {
                    return tanggal_indonesia($row->tanggal);
                })
                ->addColumn('customer_display', function ($row) {
                    if ($row->member) {
                        // Nama + kode member kecil di bawahnya
                        $kode = $row->getMemberCodeWithPrefix();
                        return $row->member->nama . '<br><small class="text-muted">' . $kode . '</small>';
                    }
                    return '-';
                })
                ->addColumn('total_formatted', function ($row) {
                    // Jika garansi, tampilkan teks GARANSI
                    if ($row->is_garansi) {
                        return '<span class="label label-info">GARANSI</span>';
                    }
                    return 'Rp ' . number_format($row->total, 0, ',', '.');
                })
                ->addColumn('due_date_formatted', function ($row) {
                    if (!$row->due_date) return '-';
                    return tanggal_indonesia($row->due_date);
                })
                ->addColumn('periode_service', function ($row) {
                    if (!$row->tanggal_mulai_service || !$row->tanggal_selesai_service) {
                        return '-';
                    }
                    
                    $mulai = \Carbon\Carbon::parse($row->tanggal_mulai_service)->format('d/m/Y');
                    $selesai = \Carbon\Carbon::parse($row->tanggal_selesai_service)->format('d/m/Y');
                    
                    return $mulai . ' - ' . $selesai;
                })
                ->addColumn('sisa_hari', function ($row) {
                    if ($row->status === 'service-berikutnya') {
                        if (!$row->tanggal_service_berikutnya) return '-';
                        
                        $now = now();
                        $serviceDate = \Carbon\Carbon::parse($row->tanggal_service_berikutnya)->startOfDay();
                        $today = $now->copy()->startOfDay();
                        
                        if ($serviceDate < $today) {
                            $hariTerlambat = $today->diffInDays($serviceDate);
                            return '<span class="label label-danger">Terlambat ' . $hariTerlambat . ' hari</span>';
                        }
                        
                        $sisaHari = $today->diffInDays($serviceDate);
                        
                        if ($sisaHari === 0) {
                            return '<span class="label label-warning">Hari Ini</span>';
                        } elseif ($sisaHari === 1) {
                            return '<span class="label label-warning">Besok</span>';
                        } else {
                            return '<span class="label label-info">Sisa ' . $sisaHari . ' hari</span>';
                        }
                    }

                    if ($row->status !== 'menunggu') {
                        return '<span class="label label-default">-</span>';
                    }
                    
                    if (!$row->due_date) return '-';
                    
                    $now = now();
                    $dueDate = $row->due_date;
                    
                    if ($dueDate < $now) {
                        $totalJamTerlambat = $dueDate->diffInHours($now);
                        
                        if ($totalJamTerlambat < 24) {
                            return '<span class="label label-danger">Terlambat ' . $totalJamTerlambat . ' jam</span>';
                        }
                        
                        $hariTerlambat = floor($totalJamTerlambat / 24);
                        $jamTerlambat = $totalJamTerlambat % 24;
                        
                        return '<span class="label label-danger">Terlambat ' . $hariTerlambat . ' hari ' . $jamTerlambat . ' jam</span>';
                    }
                    
                    $totalSisaJam = $now->diffInHours($dueDate, false);
                    
                    if ($totalSisaJam < 24) {
                        return '<span class="label label-warning">Sisa ' . $totalSisaJam . ' jam</span>';
                    }
                    
                    $sisaHari = floor($totalSisaJam / 24);
                    $sisaJam = $totalSisaJam % 24;
                    
                    return '<span class="label label-warning">Sisa ' . $sisaHari . ' hari ' . $sisaJam . ' jam</span>';
                })
                ->addColumn('tanggal_pembayaran_formatted', function ($row) {
                    if (!$row->tanggal_pembayaran) return '-';
                    return tanggal_indonesia($row->tanggal_pembayaran) . ' ' . $row->tanggal_pembayaran->format('H:i');
                })
                ->addColumn('jenis_pembayaran_badge', function ($row) {
                    if (!$row->jenis_pembayaran) return '-';
                    
                    $badgeClass = [
                        'cash' => 'success',
                        'transfer' => 'info'
                    ][$row->jenis_pembayaran] ?? 'secondary';
                    
                    $icon = $row->jenis_pembayaran === 'cash' ? 'fa-money' : 'fa-exchange';
                    
                    return '<span class="label label-' . $badgeClass . '"><i class="fa ' . $icon . '"></i> ' . ucfirst($row->jenis_pembayaran) . '</span>';
                })
                ->addColumn('pembayaran_info', function ($row) {
                    $info = '';
                    if ($row->penerima) {
                        $info .= '<strong>Penerima:</strong> ' . $row->penerima . '<br>';
                    }
                    if ($row->catatan_pembayaran) {
                        $info .= '<strong>Catatan:</strong> ' . $row->catatan_pembayaran;
                    }
                    return $info ?: '-';
                })
                ->addColumn('status_badge', function ($row) {
                    $badgeClass = [
                        'menunggu' => 'warning',
                        'lunas' => 'success',
                        'gagal' => 'danger'
                    ][$row->status] ?? 'secondary';

                    if ($row->id_invoice_sebelumnya && $row->service_lanjutan_ke > 0) {
                        return '<span class="label label-' . $badgeClass . '">' . ucfirst($row->status) . '</span>' . '<br><small class="text-muted">' . 'Lanjutan ke-' . $row->service_lanjutan_ke . '</small>';
                    }
                    
                    return '<span class="label label-' . $badgeClass . '">' . ucfirst($row->status) . '</span>';

                    if ($row->member) {
                        // Nama + kode member kecil di bawahnya
                        $kode = $row->getMemberCodeWithPrefix();
                        return $row->member->nama . '<br><small class="text-muted">' . $kode . '</small>';
                    }
                    return '-';
                })
                ->addColumn('tanggal_service_berikutnya_formatted', function ($row) {
                    if (!$row->tanggal_service_berikutnya) return '-';
                    return tanggal_indonesia($row->tanggal_service_berikutnya);
                })
                ->addColumn('sparepart_list', function ($row) {
                    $spareparts = $row->items()->where('is_sparepart', true)->get();
                    
                    if ($spareparts->isEmpty()) {
                        return '-';
                    }
                    
                    $list = '';
                    foreach ($spareparts as $sparepart) {
                        $list .= '• ' . $sparepart->deskripsi;
                        // if ($sparepart->kode_sparepart) {
                        //     $list .= ' (' . $sparepart->kode_sparepart . ')';
                        // }
                        $list .= ' - ' . $sparepart->keterangan . '<br>';
                    }
                    
                    return $list;
                })
                
                ->addColumn('petugas', function ($row) {
                    if ($row->user) {
                        return $row->user->name ?: $row->user->username;
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('aksi', function ($row) use ($status) {
                    $btn = '<div class="btn-group">';
                    if($status === 'lunas' && $row->status === 'lunas') {
                        $btn .= '<button type="button" onclick="jadwalkanServiceBerikutnya(' . $row->id_service_invoice . ')" class="btn btn-xs btn-info btn-flat"><i class="fa fa-calendar"></i> Service Berikutnya</button>';
                    }
                    $btn .= '<a href="'.route('service.invoice.print', $row->id_service_invoice).'" target="_blank" class="btn btn-xs btn-success btn-flat" onclick="cetakInvoice(event, \'' . route('service.invoice.print', $row->id_service_invoice) . '\')"><i class="fa fa-print"></i> Cetak</a>';
                    
                    if ($status === 'menunggu' && $row->status === 'menunggu') {
                        $btn .= '<button type="button" onclick="updateStatus(' . $row->id_service_invoice . ', \'lunas\')" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-check"></i> Lunas</button>';
                        $btn .= '<button type="button" onclick="updateStatus(' . $row->id_service_invoice . ', \'gagal\')" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-times"></i> Gagal</button>';
                    }
                    
                    $btn .= '<button type="button" onclick="deleteData(`'.route('service.invoice.destroy', $row->id_service_invoice).'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['sisa_hari', 'status_badge', 'jenis_pembayaran_badge', 'pembayaran_info', 'aksi', 'sparepart_list', 'petugas', 'customer_display', 'total_formatted', 'tanggal_pembayaran_formatted', 'service_lanjutan_info'])
                ->make(true);
        }
        
        return view('service_management.invoice.history', compact('status', 'start_date', 'end_date'));
    }

    public function invoicePrint($id)
    {
        try {
            $invoice = ServiceInvoice::with(['member', 'mesinCustomer.produk', 'items'])->findOrFail($id);
            $setting = DB::table('setting')->first();
            
            $pdf = Pdf::loadView('service_management.invoice.print', compact('invoice', 'setting'));
            $pdf->setPaper('A4', 'portrait');
            
            // Gunakan filename yang aman tanpa karakter khusus
            $safeFilename = 'invoice-service-' . str_replace('/', '-', $invoice->no_invoice);
            
            return $pdf->stream($safeFilename . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Print invoice error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal generate PDF: ' . $e->getMessage()], 500);
        }
    }

    public function invoicePreview($id)
    {
        $invoice = ServiceInvoice::with(['member', 'mesinCustomer', 'items'])->findOrFail($id);
        $setting = DB::table('setting')->first();
        
        $pdf = Pdf::loadView('service_management.invoice.print', compact('invoice', 'setting'));
        
        // Set paper size to A4 portrait dengan optimasi
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'dpi' => 96,
            'defaultFont' => 'Arial'
        ]);
        
        return $pdf->stream('invoice-service-' . $invoice->no_invoice . '.pdf');
    }

    public function invoicePreviewTemp(Request $request)
    {
        try {
            $previewData = [];
            $isPreview = $request->has('preview');
            
            if ($isPreview) {
                // Ambil data dari parameter URL
                $encodedData = $request->get('data');
                if ($encodedData) {
                    $previewData = json_decode(urldecode($encodedData), true);
                }
                
                if (empty($previewData)) {
                    // Fallback: buat data dummy untuk preview
                    $previewData = [
                        'is_preview' => true,
                        'tanggal' => date('Y-m-d'),
                        'jenis_service' => 'Service Preview',
                        'items' => []
                    ];
                }
            }
            
            // Ambil data member real jika ada
            $member = null;
            if (!empty($previewData['id_member'])) {
                $member = Member::find($previewData['id_member']);
            }
            
            if (!$member) {
                // Buat member dummy jika tidak ditemukan
                $member = new \stdClass();
                $member->nama = $previewData['member_nama'] ?? 'Customer Preview';
                $member->alamat = $previewData['member_alamat'] ?? 'Alamat Customer Preview';
                $member->telepon = $previewData['member_telepon'] ?? '-';
            }
            
            // Ambil data mesin customer jika ada
            $mesinCustomer = null;
            if (!empty($previewData['id_mesin_customer'])) {
                $mesinCustomer = MesinCustomer::with('produk')->find($previewData['id_mesin_customer']);
            }
            
            // Buat object invoice untuk preview
            $invoice = new \stdClass();
            $invoice->no_invoice = 'PREVIEW-' . date('YmdHis');
            $invoice->tanggal = $previewData['tanggal'] ?? date('Y-m-d');
            $invoice->jenis_service = $previewData['jenis_service'] ?? 'Service Preview';
            $invoice->total = 0;
            $invoice->jumlah_teknisi = $previewData['jumlah_teknisi'] ?? 0;
            $invoice->jumlah_jam = $previewData['jumlah_jam'] ?? 0;
            $invoice->biaya_teknisi = $previewData['biaya_teknisi'] ?? 0;
            $invoice->is_preview = true;
            $invoice->member = $member;
            $invoice->mesinCustomer = $mesinCustomer; // Assign mesinCustomer object
            
            // Process items real dari form
            $invoiceItems = [];
            $total = 0;
            
            if (!empty($previewData['items']) && is_array($previewData['items'])) {
                foreach ($previewData['items'] as $index => $itemData) {
                    $item = new \stdClass();
                    $item->deskripsi = $itemData['deskripsi'] ?? 'Item ' . ($index + 1);
                    $item->keterangan = $itemData['keterangan'] ?? '';
                    $item->kuantitas = $itemData['kuantitas'] ?? 1;
                    $item->satuan = $itemData['satuan'] ?? 'Unit';
                    $item->harga = $itemData['harga'] ?? 0;
                    $item->subtotal = $itemData['subtotal'] ?? 0;
                    $item->tipe = $itemData['tipe'] ?? 'preview';
                    
                    // Handle optional fields dengan default values
                    $item->jenis_kendaraan = $itemData['jenis_kendaraan'] ?? null;
                    $item->is_sparepart = isset($itemData['is_sparepart']) ? (bool)$itemData['is_sparepart'] : false;
                    $item->kode_sparepart = $itemData['kode_sparepart'] ?? null;
                    
                    $total += $item->subtotal;
                    $invoiceItems[] = $item;
                }
            }
            
            // Add biaya teknisi sebagai item terpisah jika ada
            $biayaTeknisi = $previewData['biaya_teknisi'] ?? 0;
            if ($biayaTeknisi > 0) {
                $teknisiItem = new \stdClass();
                $teknisiItem->deskripsi = 'Biaya Teknisi';
                $teknisiItem->keterangan = ($previewData['jumlah_teknisi'] ?? 0) . ' orang x ' . ($previewData['jumlah_jam'] ?? 0) . ' jam';
                $teknisiItem->kuantitas = 1;
                $teknisiItem->satuan = 'Paket';
                $teknisiItem->harga = $biayaTeknisi;
                $teknisiItem->subtotal = $biayaTeknisi;
                $teknisiItem->tipe = 'teknisi';
                $teknisiItem->jenis_kendaraan = null;
                $teknisiItem->is_sparepart = false;
                $teknisiItem->kode_sparepart = null;
                
                $total += $biayaTeknisi;
                $invoiceItems[] = $teknisiItem;
                
                $invoice->biaya_teknisi = $biayaTeknisi;
            }
            
            $invoice->total = $total;
            $invoice->items = $invoiceItems;
            
            // Tambahkan payment deadline untuk preview
            $invoice->payment_deadline = \Carbon\Carbon::parse($invoice->tanggal)->addDays(7)->format('d F Y');
            $invoice->issue_date = \Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y');
            
            $setting = DB::table('setting')->first();
            
            $pdf = Pdf::loadView('service_management.invoice.preview', compact('invoice', 'setting'));
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'dpi' => 96,
                'defaultFont' => 'Arial'
            ]);
            
            return $pdf->stream('invoice-preview-' . date('YmdHis') . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Invoice preview error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback simple preview jika error
            $html = '<html><body>
                <h1>Preview Invoice Service</h1>
                <div style="border: 2px solid #ffeb3b; background-color: #fffacd; padding: 20px; margin: 20px;">
                    <h3>PREVIEW INVOICE - ERROR</h3>
                    <p>Terjadi kesalahan dalam generating preview: ' . $e->getMessage() . '</p>
                    <p>Request data: ' . json_encode($request->all()) . '</p>
                    <p>Preview data: ' . json_encode($previewData) . '</p>
                </div>
            </body></html>';
            return response($html)->header('Content-Type', 'text/html');
        }
    }

    public function invoiceDestroy($id)
    {
       
        try {
            DB::transaction(function () use ($id) {
                $invoice = ServiceInvoice::findOrFail($id);
                $this->kembalikanStokSparepart($invoice);
                ServiceInvoiceItem::where('id_service_invoice', $id)->delete();
                
                // Hapus invoice
                $invoice->delete();
                
            });
            
            return response()->json(['success' => true, 'message' => 'Invoice berhasil dihapus']);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menghapus invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMesinCustomerGrouped($id_member)
    {
        $mesinCustomers = MesinCustomer::with(['produk' => function($query) {
                $query->withPivot('closing_type', 'biaya_service', 'jumlah')->with('satuan');
            }, 'ongkosKirim'])
            ->where('id_member', $id_member)
            ->get();
        
        $result = [];
        
        foreach ($mesinCustomers as $mesinCustomer) {
            // Group produk oleh closing type
            $produkByClosingType = [];
            
            foreach ($mesinCustomer->produk as $produk) {
                $closingType = $produk->pivot->closing_type;
                if (!isset($produkByClosingType[$closingType])) {
                    $produkByClosingType[$closingType] = [];
                }
                
                $produkByClosingType[$closingType][] = $produk;
            }
            
            // Buat entry terpisah untuk setiap closing type
            foreach ($produkByClosingType as $closingType => $produks) {
                $result[] = [
                    'id' => $mesinCustomer->id, // Use 'id' as primary key
                    'id_mesin_customer' => $mesinCustomer->id, // Keep for backward compatibility
                    'closing_type' => $closingType,
                    'daerah' => $mesinCustomer->ongkosKirim ? $mesinCustomer->ongkosKirim->daerah : 'Unknown',
                    'ongkos_kirim' => $mesinCustomer->ongkosKirim,
                    'produks' => $produks,
                    'produk_count' => count($produks),
                    'label' => $this->generateLabel($closingType, $mesinCustomer->ongkosKirim ? $mesinCustomer->ongkosKirim->daerah : 'Unknown', $produks)
                ];
            }
        }
        
        return response()->json($result);
    }

    // Helper method untuk generate label
    private function generateLabel($closingType, $daerah, $produks)
    {
        $typeLabel = ucfirst(str_replace('_', ' ', $closingType));
        $produkNames = array_map(function($produk) {
            return $produk['nama_produk'];
        }, $produks);
        
        return $typeLabel . ' - ' . implode(', ', $produkNames) . ' - ' . $daerah;
    }

    public function getMesinCustomerByType($id_member, $closing_type)
    {
        $mesinCustomers = MesinCustomer::with(['produk' => function($query) use ($closing_type) {
                $query->withPivot('biaya_service', 'closing_type')
                    ->wherePivot('closing_type', $closing_type);
            }, 'ongkosKirim'])
            ->where('id_member', $id_member)
            ->whereHas('produk', function($query) use ($closing_type) {
                $query->where('mesin_customer_produk.closing_type', $closing_type);
            })
            ->get();
        
        return response()->json($mesinCustomers);
    }

    private function processInvoiceItemsBasedOnServiceType($request, $mesinCustomer)
    {
        $items = [];
        $jenisService = $request->jenis_service;
        $closingType = $this->getClosingTypeFromMesinCustomer($mesinCustomer);
        
        // Process items based on service type
        if ($jenisService === 'Service Rutin') {
            // Service Rutin: include produk items with normal price, no teknisi
            foreach ($mesinCustomer->produk as $produk) {
                $items[] = [
                    'id_produk' => $produk->id_produk,
                    'deskripsi' => $produk->nama_produk,
                    'keterangan' => 'Biaya Service Rutin - ' . ucfirst(str_replace('_', ' ', $closingType)),
                    'kuantitas' => 1,
                    'satuan' => $produk->satuan ? $produk->satuan->nama_satuan : 'Unit',
                    'harga' => $produk->pivot->biaya_service,
                    'subtotal' => $produk->pivot->biaya_service,
                    'tipe' => 'produk'
                ];
            }
            
        } elseif ($jenisService === 'Service') {
            // Service: reset price to 0 if Jual Putus
            foreach ($mesinCustomer->produk as $produk) {
                $harga = $closingType === 'jual_putus' ? 0 : $produk->pivot->biaya_service;
                $keterangan = $closingType === 'jual_putus' ? 
                    'Biaya Service - Jual Putus (Gratis)' : 
                    'Biaya Service - ' . ucfirst(str_replace('_', ' ', $closingType));
                
                $items[] = [
                    'id_produk' => $produk->id_produk,
                    'deskripsi' => $produk->nama_produk,
                    'keterangan' => $keterangan,
                    'kuantitas' => 1,
                    'satuan' => $produk->satuan ? $produk->satuan->nama_satuan : 'Unit',
                    'harga' => $harga,
                    'subtotal' => $harga,
                    'tipe' => 'produk'
                ];
            }
            
        } elseif ($jenisService === 'Pembelian Sparepart' || $jenisService === 'Lainnya') {
            // Pembelian Sparepart/Lainnya: hanya ongkos kirim
            // Tidak ada produk items yang ditambahkan
        }
        
        // Always add ongkir item if exists
        if ($mesinCustomer->ongkosKirim) {
            $items[] = [
                'deskripsi' => 'Ongkos Kirim - ' . $mesinCustomer->ongkosKirim->daerah,
                'keterangan' => 'Pengiriman ke ' . $mesinCustomer->ongkosKirim->daerah,
                'kuantitas' => 1,
                'satuan' => 'Trip',
                'harga' => $mesinCustomer->ongkosKirim->harga,
                'subtotal' => $mesinCustomer->ongkosKirim->harga,
                'tipe' => 'ongkir'
            ];
        }
        
        return $items;
    }

    private function getClosingTypeFromMesinCustomer($mesinCustomer)
    {
        // Get the first produk's closing type as representative
        if ($mesinCustomer->produk->isNotEmpty()) {
            return $mesinCustomer->produk->first()->pivot->closing_type;
        }
        return 'jual_putus'; // default
    }

    // Tambahkan method ini di ServiceManagementController
    public function getStatusCounts()
    {
        try {
            $counts = [
                'menunggu' => \App\Models\ServiceInvoice::where('status', 'menunggu')->count(),
                'lunas' => \App\Models\ServiceInvoice::where('status', 'lunas')->count(),
                'gagal' => \App\Models\ServiceInvoice::where('status', 'gagal')->count(),
                'service_berikutnya' => ServiceInvoice::whereNotNull('tanggal_service_berikutnya')
                    ->where('tanggal_service_berikutnya', '>=', now()->startOfDay())
                    ->count(),
            ];

            return response()->json($counts);
        } catch (\Exception $e) {
            return response()->json([
                'menunggu' => 0,
                'lunas' => 0,
                'gagal' => 0,
                'service_lanjutan' => 0
            ]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,lunas,gagal',
            'jenis_pembayaran' => 'required_if:status,lunas|in:cash,transfer',
            'penerima' => 'required_if:status,lunas|string|max:255',
            'tanggal_pembayaran' => 'required_if:status,lunas|date',
            'catatan_pembayaran' => 'nullable|string',
            'tanggal_service_berikutnya' => 'nullable|date|after:today'
        ]);

        try {
            $invoice = ServiceInvoice::findOrFail($id);
            
            $updateData = [
                'status' => $request->status,
                'catatan' => $request->catatan
            ];

            // Jika status lunas, simpan data pembayaran
            if ($request->status === 'lunas') {
                $updateData['jenis_pembayaran'] = $request->jenis_pembayaran;
                $updateData['penerima'] = $request->penerima;
                $updateData['tanggal_pembayaran'] = $request->tanggal_pembayaran;
                $updateData['catatan_pembayaran'] = $request->catatan_pembayaran;

                if ($request->has('tanggal_service_berikutnya') && $request->tanggal_service_berikutnya) {
                    $updateData['tanggal_service_berikutnya'] = $request->tanggal_service_berikutnya;
                    \Log::info("Tanggal service berikutnya disimpan untuk invoice {$invoice->no_invoice}: {$request->tanggal_service_berikutnya}");
                }
            }
            else if ($request->status === 'gagal') {
                $updateData['catatan'] = $request->catatan;
                $updateData['jenis_pembayaran'] = null;
                $updateData['penerima'] = null;
                $updateData['tanggal_pembayaran'] = null;
                $updateData['catatan_pembayaran'] = null;
                $this->kembalikanStokSparepart($invoice);
            }

            $invoice->update($updateData);

            return response()->json([
                'success' => true, 
                'message' => 'Status invoice berhasil diupdate menjadi ' . $request->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateServiceBerikutnya(Request $request, $id)
    {
        $request->validate([
            'tanggal_service_berikutnya' => 'required|date|after:today'
        ]);

        try {
            $invoice = ServiceInvoice::findOrFail($id);
            
            // Update hanya tanggal_service_berikutnya, tanpa mengubah status
            $invoice->update([
                'tanggal_service_berikutnya' => $request->tanggal_service_berikutnya
            ]);

            \Log::info("Service berikutnya dijadwalkan untuk invoice {$invoice->no_invoice} pada {$request->tanggal_service_berikutnya}");

            return response()->json([
                'success' => true, 
                'message' => 'Service berikutnya berhasil dijadwalkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal menjadwalkan service berikutnya: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk mengembalikan stok sparepart
    private function kembalikanStokSparepart($invoice)
    {
        foreach ($invoice->items as $item) {
            if ($item->is_sparepart && $item->id_sparepart) {
                $sparepart = \App\Models\Sparepart::find($item->id_sparepart);
                if ($sparepart) {
                    $jumlah = $item->kuantitas;
                    if ($sparepart->tambahStok($jumlah)) {
                        \Log::info("Stok sparepart {$sparepart->nama_sparepart} dikembalikan {$jumlah}. Stok sekarang: {$sparepart->stok}");
                    } else {
                        \Log::error("Gagal mengembalikan stok sparepart {$sparepart->nama_sparepart}");
                    }
                } else {
                    \Log::error("Sparepart dengan ID {$item->id_sparepart} tidak ditemukan saat mengembalikan stok");
                }
            }
        }
    }

    private function createServiceLanjutan($invoiceAsal, $tanggalServiceBerikutnya)
    {
        // Generate invoice number baru
        $invoiceNumber = \App\Models\InvoiceServiceCounter::generateInvoiceNumber();
        
        // Hitung service lanjutan ke-berapa
        $serviceLanjutanKe = $invoiceAsal->service_lanjutan_ke + 1;
        
        // Buat invoice baru
        $invoiceBaru = ServiceInvoice::create([
            'no_invoice' => $invoiceNumber,
            'tanggal' => $tanggalServiceBerikutnya,
            'id_member' => $invoiceAsal->id_member,
            'id_mesin_customer' => $invoiceAsal->id_mesin_customer,
            'id_user' => auth()->id(),
            'id_invoice_sebelumnya' => $invoiceAsal->id_service_invoice,
            'service_lanjutan_ke' => $serviceLanjutanKe,
            'jenis_service' => $invoiceAsal->jenis_service,
            'jumlah_teknisi' => $invoiceAsal->jumlah_teknisi,
            'jumlah_jam' => $invoiceAsal->jumlah_jam,
            'biaya_teknisi' => $invoiceAsal->biaya_teknisi,
            'total' => $invoiceAsal->total,
            'status' => 'menunggu',
            'due_date' => \Carbon\Carbon::parse($tanggalServiceBerikutnya)->addDays(7)
        ]);

        // Copy items dari invoice asal
        foreach ($invoiceAsal->items as $itemAsal) {
            ServiceInvoiceItem::create([
                'id_service_invoice' => $invoiceBaru->id_service_invoice,
                'id_produk' => $itemAsal->id_produk,
                'deskripsi' => $itemAsal->deskripsi,
                'keterangan' => $itemAsal->keterangan,
                'kuantitas' => $itemAsal->kuantitas,
                'satuan' => $itemAsal->satuan,
                'harga' => $itemAsal->harga,
                'subtotal' => $itemAsal->subtotal,
                'tipe' => $itemAsal->tipe,
                'is_sparepart' => $itemAsal->is_sparepart,
                'jenis_kendaraan' => $itemAsal->jenis_kendaraan,
                'kode_sparepart' => $itemAsal->kode_sparepart,
            ]);
        }

        return $invoiceBaru;
    }

    public function exportPdf(Request $request)
    {
        $status = $request->get('status', 'terkini');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        
        $query = ServiceInvoice::with(['member', 'items', 'user'])->whereHas('items');
        
        // Filter berdasarkan status
        switch ($status) {
            case 'menunggu':
                $query->where('status', 'menunggu');
                break;
            case 'lunas':
                $query->where('status', 'lunas');
                break;
            case 'gagal':
                $query->where('status', 'gagal');
                break;
            case 'terkini':
            default:
                break;
        }
        
        // Filter tanggal
        if ($start_date && $end_date) {
            $query->whereBetween('tanggal', [$start_date, $end_date]);
        } elseif ($start_date) {
            $query->where('tanggal', '>=', $start_date);
        } elseif ($end_date) {
            $query->where('tanggal', '<=', $end_date);
        }
        
        // Sorting
        if ($status === 'menunggu') {
            $query->orderBy('due_date', 'asc');
        } else {
            $query->orderBy('tanggal', 'desc');
        }
        
        $invoices = $query->get();
        $setting = DB::table('setting')->first();
        
        $pdf = Pdf::loadView('service_management.invoice.export', compact('invoices', 'status', 'start_date', 'end_date', 'setting'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'invoice-history-' . $status . '-' . date('Ymd-His') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function invoiceSetting()
    {
        $counter = \App\Models\InvoiceServiceCounter::first();
        $currentNumber = $counter->last_number;
        $currentYear = $counter->year;
        $romanMonth = \App\Models\InvoiceServiceCounter::getRomanMonth();
        
        $currentInvoiceNumber = str_pad($currentNumber, 3, '0', STR_PAD_LEFT) . '/BBN.INV/' . $romanMonth . '/' . $currentYear;
        $nextInvoiceNumber = str_pad($currentNumber + 1, 3, '0', STR_PAD_LEFT) . '/BBN.INV/' . $romanMonth . '/' . $currentYear;

        return view('service_management.invoice.setting', compact(
            'currentInvoiceNumber', 
            'nextInvoiceNumber', 
            'currentNumber',
            'currentYear'
        ));
    }

    public function updateInvoiceSetting(Request $request)
    {
        $request->validate([
            'starting_number' => 'required|integer|min:1|max:999',
            'year' => 'required|integer|min:2020|max:2030'
        ]);

        try {
            \App\Models\InvoiceServiceCounter::setStartingNumber(
                $request->starting_number - 1, // Karena increment dilakukan saat generate
                $request->year
            );

            return response()->json([
                'success' => true,
                'message' => 'Setting nomor invoice berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui setting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDueSoonInvoices()
    {
        try {
            $now = now();
            $today = $now->copy()->startOfDay();
            $tomorrow = $today->copy()->addDay();
            
            // Ambil invoice yang statusnya menunggu dan due date-nya dalam 24 jam ke depan
            // atau sudah lewat (overdue) tetapi masih status menunggu
            $invoices = ServiceInvoice::with(['member'])
                ->where('status', 'menunggu')
                ->where(function($query) use ($now, $today, $tomorrow) {
                    // Due date dalam 24 jam ke depan ATAU sudah lewat
                    $query->where('due_date', '<=', $now->copy()->addHours(24))
                        ->orWhere('due_date', '<', $today);
                })
                ->orderBy('due_date', 'asc')
                ->get();
                
            // Hitung sisa jam untuk setiap invoice
            $invoicesWithRemainingTime = $invoices->map(function($invoice) use ($now) {
                $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                
                // Hitung selisih dalam jam
                $remainingHours = $now->diffInHours($dueDate, false); // false untuk allow negative
                
                if ($dueDate < $now) {
                    // Sudah lewat - hitung dalam jam
                    $invoice->remaining_hours = -$now->diffInHours($dueDate);
                    $invoice->remaining_days = -$now->diffInDays($dueDate);
                    $invoice->status_type = 'overdue';
                    $invoice->time_description = 'Terlambat ' . $now->diffInHours($dueDate) . ' jam';
                } else {
                    // Masih dalam waktu
                    $invoice->remaining_hours = $remainingHours;
                    $invoice->remaining_days = $now->diffInDays($dueDate, false);
                    $invoice->status_type = $remainingHours <= 24 ? 'due_soon' : 'normal';
                    
                    if ($remainingHours <= 24) {
                        if ($remainingHours <= 1) {
                            $invoice->time_description = 'Sisa ' . $now->diffInMinutes($dueDate) . ' menit';
                        } else {
                            $invoice->time_description = 'Sisa ' . $remainingHours . ' jam';
                        }
                    } else {
                        $invoice->time_description = 'Sisa ' . $invoice->remaining_days . ' hari';
                    }
                }
                
                return $invoice;
            })->filter(function($invoice) {
                // Hanya ambil yang remaining_hours <= 24 (24 jam ke depan) atau sudah lewat
                return $invoice->remaining_hours <= 24;
            });
            
            \Log::info('Due soon invoices check:', [
                'now' => $now->format('Y-m-d H:i:s'),
                'invoices_count' => $invoicesWithRemainingTime->count(),
                'invoices' => $invoicesWithRemainingTime->map(function($inv) {
                    return [
                        'no_invoice' => $inv->no_invoice,
                        'due_date' => $inv->due_date,
                        'remaining_hours' => $inv->remaining_hours,
                        'remaining_days' => $inv->remaining_days,
                        'status_type' => $inv->status_type,
                        'time_description' => $inv->time_description
                    ];
                })->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'invoices' => $invoicesWithRemainingTime->values(), // Reset keys
                'count' => $invoicesWithRemainingTime->count(),
                'now' => $now->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting due soon invoices: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving invoices',
                'invoices' => []
            ], 500);
        }
    }

    public function searchCustomers(Request $request)
    {
        try {
            $searchTerm = $request->get('search', '');
            
            $query = Member::withCount(['mesinCustomers as mesin_count'])
                ->with(['mesinCustomers' => function($query) {
                    $query->with(['produk' => function($q) {
                        $q->withPivot('closing_type');
                    }]);
                }])
                ->whereHas('mesinCustomers') // Hanya customer yang punya mesin
                ->orderBy('nama', 'asc');
            
            if (!empty($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nama', 'like', '%' . $searchTerm . '%')
                    ->orWhere('alamat', 'like', '%' . $searchTerm . '%')
                    ->orWhere('telepon', 'like', '%' . $searchTerm . '%')
                    ->orWhere('kode_member', 'like', '%' . $searchTerm . '%');
                });
            }
            
            $customers = $query->limit(50)->get();
            
            // Process customers to determine closing type
            $processedCustomers = $customers->map(function($customer) {
                $closingTypes = [];
                
                foreach ($customer->mesinCustomers as $mesinCustomer) {
                    foreach ($mesinCustomer->produk as $produk) {
                        $closingType = $produk->pivot->closing_type ?? 'jual_putus';
                        if (!in_array($closingType, $closingTypes)) {
                            $closingTypes[] = $closingType;
                        }
                    }
                }
                
                // Determine prefix based on closing types
                $prefix = '';
                if (in_array('deposit', $closingTypes) && in_array('jual_putus', $closingTypes)) {
                    $prefix = 'JD'; // Mixed
                } elseif (in_array('deposit', $closingTypes)) {
                    $prefix = 'D'; // Deposit only
                } else {
                    $prefix = 'J'; // Jual putus only or default
                }
                
                $customer->closing_type_prefix = $prefix;
                $customer->closing_types = $closingTypes;
                
                return $customer;
            });
            
            return response()->json([
                'success' => true,
                'customers' => $processedCustomers,
                'count' => $customers->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error searching customers: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching customers',
                'customers' => []
            ], 500);
        }
    }

    // Method untuk mendapatkan invoice service berikutnya yang akan datang
    public function getUpcomingServiceInvoices()
    {
        try {
            $now = now();
            $tomorrow = $now->copy()->addDay()->startOfDay();
            
            // Ambil invoice yang memiliki tanggal_service_berikutnya besok
            $invoices = ServiceInvoice::with(['member'])
                ->whereNotNull('tanggal_service_berikutnya')
                ->whereDate('tanggal_service_berikutnya', $tomorrow)
                ->orderBy('tanggal_service_berikutnya', 'asc')
                ->get();
                
            // Hitung info waktu untuk setiap invoice
            $invoicesWithTimeInfo = $invoices->map(function($invoice) use ($tomorrow) {
                $serviceDate = \Carbon\Carbon::parse($invoice->tanggal_service_berikutnya);
                $invoice->remaining_hours = $tomorrow->diffInHours($serviceDate, false);
                $invoice->time_description = 'Besok';
                
                return $invoice;
            });
            
            return response()->json([
                'success' => true,
                'invoices' => $invoicesWithTimeInfo,
                'count' => $invoicesWithTimeInfo->count(),
                'check_time' => $now->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting upcoming service invoices: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving upcoming service invoices',
                'invoices' => []
            ], 500);
        }
    }

    // Method untuk mendapatkan count service berikutnya
    public function getServiceBerikutnyaCounts()
    {
        try {
            $today = now()->startOfDay();
            $tomorrow = $today->copy()->addDay();
            
            $counts = [
                'total' => ServiceInvoice::whereNotNull('tanggal_service_berikutnya')
                            ->whereDate('tanggal_service_berikutnya', '>=', $today)
                            ->where('status', 'lunas')
                            ->count(),
                'besok' => ServiceInvoice::whereNotNull('tanggal_service_berikutnya')
                            ->whereDate('tanggal_service_berikutnya', $tomorrow)
                            ->where('status', 'lunas')
                            ->count(),
                'minggu_ini' => ServiceInvoice::whereNotNull('tanggal_service_berikutnya')
                            ->whereBetween('tanggal_service_berikutnya', [$today, $today->copy()->endOfWeek()])
                            ->where('status', 'lunas')
                            ->count(),
            ];

            return response()->json($counts);
        } catch (\Exception $e) {
            return response()->json([
                'total' => 0,
                'besok' => 0,
                'minggu_ini' => 0
            ]);
        }
    }
}