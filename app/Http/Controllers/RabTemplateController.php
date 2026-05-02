<?php

namespace App\Http\Controllers;

use App\Models\RabTemplate;
use App\Models\RabDetail;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RabTemplateController extends Controller
{
    public function index()
    {
        $rabTemplates = RabTemplate::with('details')->latest()->get();
        return view('rab_template.index', compact('rabTemplates'));
    }

    public function create()
    {
        $products = Produk::where('tipe_produk', '!=', 'barang_dagang')->get();
        return view('rab_template.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'komponen_utama' => 'nullable|boolean', 
            'products' => 'nullable|array|min:1',
            'products.*' => 'exists:produk,id_produk',
            'items' => 'required|array|min:1',
            'items.*.nama_komponen' => 'required|string',
            'items.*.jumlah' => 'required|numeric|min:1',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.deskripsi' => 'nullable|string',
        ]);

        // Create template
        $template = RabTemplate::create([
            'nama_template' => $request->nama_template,
            'deskripsi' => $request->deskripsi,
            'total_biaya_per_orang' => 0, // Will be calculated
            'komponen_utama' => $request->has('komponen_utama'),
        ]);

        // Save details
        $totalBiaya = 0;
        foreach ($request->items as $item) {
            $hargaSatuan = str_replace('.', '', $item['harga_satuan']);
            $hargaSatuan = is_numeric($hargaSatuan) ? $hargaSatuan : 0;
            $budget = $item['jumlah'] * $item['harga_satuan'];
            $totalBiaya += $budget;
            
            $detailData = [
                'id_rab' => $template->id_rab,
                'nama_komponen' => $item['nama_komponen'],
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'budget' => $budget,
                'deskripsi' => $item['deskripsi'] ?? null,
                'biaya' => $budget,
            ];

            // Jika komponen utama, set sebagai disetujui
            if ($request->komponen_utama) {
                $detailData['disetujui'] = true;
                $detailData['nilai_disetujui'] = $budget;
            }

            RabDetail::create($detailData);
        }

        // Update total
        $template->update(['total_biaya_per_orang' => $totalBiaya]);

        if ($request->has('products')) {
            $template->products()->attach($request->products);
        }

        return redirect()->route('rab_template.index')
            ->with('success', 'Template RAB berhasil dibuat');
    }

    public function show($id)
    {
        $template = RabTemplate::with(['details', 'products'])->findOrFail($id);
        return view('rab_template.show', compact('template'));
    }

    public function edit($id)
    {
        $template = RabTemplate::with(['details', 'products'])->findOrFail($id);
        $products = Produk::where('tipe_produk', '!=', 'barang_dagang')->get();
        
        return view('rab_template.edit', compact('template', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'komponen_utama' => 'nullable|boolean',
            'products' => 'nullable|array|min:1',
            'products.*' => 'exists:produk,id_produk',
            'items' => 'required|array|min:1',
            'items.*.nama_komponen' => 'required|string',
            'items.*.jumlah' => 'required|numeric|min:1',
            'items.*.satuan' => 'nullable|string|max:50',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.deskripsi' => 'nullable|string',
        ]);

        $template = RabTemplate::findOrFail($id);

        // Update template
        $template->update([
            'nama_template' => $request->nama_template,
            'deskripsi' => $request->deskripsi,
            'komponen_utama' => $request->boolean('komponen_utama'),
        ]);

        // Delete old details
        RabDetail::where('id_rab', $template->id_rab)->delete();

        // Save new details
        $totalBiaya = 0;
        foreach ($request->items as $item) {
            $budget = $item['jumlah'] * $item['harga_satuan'];
            $totalBiaya += $budget;
            
            $detail = RabDetail::create([
                'id_rab' => $template->id_rab,
                'nama_komponen' => $item['nama_komponen'],
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'budget' => $budget,
                'deskripsi' => $item['deskripsi'] ?? null,
                'biaya' => $budget,
            ]);

            if ($request->komponen_utama) {
                $detail->update([
                    'disetujui' => true,
                    'nilai_disetujui' => $budget
                ]);
            }
        }

        // Update total
        $template->update(['total_biaya_per_orang' => $totalBiaya]);

        // Sync products
        $template->products()->sync($request->products ?? []);

        return redirect()->route('rab_template.index')
            ->with('success', 'Template RAB berhasil diperbarui');
    }

    public function destroy($id)
    {
        $template = RabTemplate::findOrFail($id);
        $template->products()->detach();
        RabDetail::where('id_rab', $template->id_rab)->delete();
        $template->delete();

        return redirect()->route('rab_template.index')
            ->with('success', 'Template RAB berhasil dihapus');
    }

    public function updateApproval(Request $request, $id)
    {
        \Log::info('Starting updateApproval', ['id' => $id, 'request' => $request->all()]);

        try {
            $template = RabTemplate::with('details')->findOrFail($id);
            
            DB::beginTransaction();

            foreach ($template->details as $detail) {
                $itemKey = $detail->id_rab_detail;
                
                if (isset($request->items[$itemKey])) {
                    $item = $request->items[$itemKey];
                    $realisasi_pemakaian = str_replace('.', '', $item['realisasi_pemakaian'] ?? '0');
                    $oldRealisasi = $detail->realisasi_pemakaian;
                    
                    $data = [
                        'nilai_disetujui' => $item['nilai_disetujui'] ?? $detail->nilai_disetujui,
                        'realisasi_pemakaian' => $realisasi_pemakaian,
                        'disetujui' => isset($item['disetujui']) ? true : false,
                    ];
                    
                    $detail->update($data);
                    
                    // Simpan history jika ada perubahan realisasi
                    if ($realisasi_pemakaian != $oldRealisasi) {
                        $tambahan = $realisasi_pemakaian - $oldRealisasi;
                        
                        if ($tambahan > 0) {
                            DB::table('rab_realisasi_history')->insert([
                                'id_rab_detail' => $detail->id_rab_detail,
                                'jumlah' => $tambahan,
                                'keterangan' => $request->input('keterangan', 'Penambahan realisasi'),
                                'user_id' => auth()->id(),
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                }
            }

            // Handle bukti transfer
            if ($request->hasFile('bukti_transfer')) {
                \Log::debug('Processing bukti transfer upload');
                try {
                    // Delete old file if exists
                    $oldFile = $template->details->first()->bukti_transfer ?? null;
                    if ($oldFile && Storage::exists('public/'.$oldFile)) {
                        Storage::delete('public/'.$oldFile);
                    }
            
                    // Store new file
                    $file = $request->file('bukti_transfer');
                    $path = $file->store('bukti_transfer', 'public');
                    
                    // Update first detail with transfer info
                    $firstDetail = $template->details->first();
                    if ($firstDetail) {
                        $updateData = [
                            'bukti_transfer' => str_replace('bukti_transfer/', '', $path),
                            'sumber_dana' => $request->sumber_dana ?? $firstDetail->sumber_dana
                        ];
                        $firstDetail->update($updateData);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing bukti transfer', [
                        'error' => $e->getMessage()
                    ]);
                }
            } elseif ($request->has('sumber_dana')) {
                \Log::debug('Updating sumber dana only');
                $firstDetail = $template->details->first();
                if ($firstDetail) {
                    \Log::debug('Updating sumber dana', ['sumber_dana' => $request->sumber_dana]);
                    $firstDetail->update([
                        'sumber_dana' => $request->sumber_dana
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Persetujuan RAB berhasil diperbarui',
                    'redirect' => route('rab_template.index')
                ]);
            }

            return redirect()->back()
                ->with('success', 'Persetujuan RAB berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function setDefault(Request $request, $id)
    {
        $template = RabTemplate::findOrFail($id);
        
        foreach ($template->products as $product) {
            $product->rabs()->updateExistingPivot($template->id_rab, [
                'is_default' => false
            ]);
        }
        
        $template->products()->updateExistingPivot($request->product_id, [
            'is_default' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'RAB berhasil dijadikan default'
        ]);
    }

    public function list()
    {
        try {
            $rabTemplates = RabTemplate::with(['details'])
                ->select('id_rab', 'nama_template', 'deskripsi', 'created_at')
                ->latest()
                ->get()
                ->map(function($rab) {
                    $totalDisetujui = $rab->details->sum('nilai_disetujui');
                    $totalRealisasi = $rab->details->sum('realisasi_pemakaian');
                    
                    return [
                        'id_rab' => $rab->id_rab,
                        'nama_template' => $rab->nama_template,
                        'deskripsi' => $rab->deskripsi,
                        'total_disetujui' => $totalDisetujui,
                        'total_realisasi' => $totalRealisasi,
                        'status' => $this->calculateStatus($rab, $totalDisetujui, $totalRealisasi),
                        'details' => $rab->details->map(function($detail) {
                            return [
                                'nama_komponen' => $detail->nama_komponen ?? '-',
                                'jumlah' => $detail->jumlah ?? 0,
                                'satuan' => $detail->satuan ?? '-',
                                'nilai_disetujui' => $detail->nilai_disetujui ?? 0,
                                'realisasi_pemakaian' => $detail->realisasi_pemakaian ?? 0
                            ];
                        })->toArray()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $rabTemplates
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data RAB'
            ], 500);
        }
    }

    private function calculateStatus($rab, $totalDisetujui, $totalRealisasi)
    {
        if ($rab->details->whereNotNull('bukti_transfer')->count() > 0) {
            return 'Ditransfer';
        }

        $hasApprovals = $rab->details->where('disetujui', true)->count() > 0 || 
                    $totalDisetujui > 0;

        if (!$hasApprovals) return 'Draft';

        $allApproved = $rab->details->where('disetujui', false)->count() === 0;
        $budgetEqualsApproved = $rab->total_budget == $totalDisetujui;
        
        if ($allApproved) {
            return $budgetEqualsApproved ? 'Disetujui Semua' : 'Disetujui dengan Revisi';
        }
        return $budgetEqualsApproved ? 'Disetujui Sebagian' : 'Disetujui Sebagian dengan Revisi';
    }

    public function getHistory($id)
    {
        try {
            $history = DB::table('rab_realisasi_history')
                ->join('users', 'rab_realisasi_history.user_id', '=', 'users.id')
                ->where('rab_realisasi_history.id_rab_detail', $id)
                ->select(
                    'rab_realisasi_history.id',
                    'rab_realisasi_history.jumlah',
                    'rab_realisasi_history.keterangan',
                    'users.name as user',
                    DB::raw("DATE_FORMAT(rab_realisasi_history.created_at, '%d/%m/%Y %H:%i') as tanggal")
                )
                ->orderBy('rab_realisasi_history.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addRealisasi(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            $detail = RabDetail::findOrFail($id);
            
            DB::beginTransaction();
            
            // Tambahkan realisasi
            $detail->increment('realisasi_pemakaian', $request->jumlah);
            
            // Simpan history
            DB::table('rab_realisasi_history')->insert([
                'id_rab_detail' => $detail->id_rab_detail,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Realisasi berhasil ditambahkan',
                'new_value' => $detail->fresh()->realisasi_pemakaian
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteHistory($id)
    {
        try {
            DB::beginTransaction();
            
            // Ambil data history yang akan dihapus
            $history = DB::table('rab_realisasi_history')->where('id', $id)->first();
            
            if (!$history) {
                throw new \Exception('Data history tidak ditemukan');
            }
            
            // Kurangi realisasi pemakaian
            DB::table('rab_detail')
                ->where('id_rab_detail', $history->id_rab_detail)
                ->decrement('realisasi_pemakaian', $history->jumlah);
            
            // Hapus history
            DB::table('rab_realisasi_history')->where('id', $id)->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'History berhasil dihapus',
                'detail_id' => $history->id_rab_detail,
                'jumlah_dikurangi' => $history->jumlah
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus history: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetHistory($detailId)
    {
        try {
            DB::beginTransaction();
            
            // Hapus semua history
            DB::table('rab_realisasi_history')
                ->where('id_rab_detail', $detailId)
                ->delete();
                
            // Reset realisasi ke 0
            DB::table('rab_detail')
                ->where('id_rab_detail', $detailId)
                ->update(['realisasi_pemakaian' => 0]);
                
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Semua history berhasil dihapus dan realisasi direset'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset history: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHistorySum($detailId)
    {
        try {
            $total = DB::table('rab_realisasi_history')
                ->where('id_rab_detail', $detailId)
                ->sum('jumlah');
                
            return response()->json([
                'success' => true,
                'total' => (float)$total
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil total realisasi'
            ], 500);
        }
    }
}