<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FixedAsset;
use App\Models\FixedAssetDepreciation;
use App\Models\AccountingBook;
use App\Services\ChartOfAccountService;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\Storage;

class FixedAssetController extends Controller
{
    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }

    // Daftar Aktiva Tetap
    public function index(Request $request)
    {
        $query = FixedAsset::with(['accountingBook', 'depreciations'])
            ->orderBy('acquisition_date', 'desc');

        // Filtering
        if ($request->filled('book_id')) {
            $query->where('accounting_book_id', $request->book_id);
        }

        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        if ($request->filled('date_from')) {
            $query->where('acquisition_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('acquisition_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('asset_code', 'like', "%$search%");
            });
        }

        $assets = $query->paginate(25);
        $books = AccountingBook::all();
        $booksActive = $books->where('status', 'active');

        return view('financial.fixed-asset.index', compact('assets', 'books', 'booksActive'));
    }

    public function store(Request $request)
    {
        \Log::debug('Store Fixed Asset Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'name' => 'required|string|max:255',
            'asset_type' => 'required|in:tangible,intangible,building',
            'asset_group' => 'required|string|max:100',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date|before_or_equal:today',
            'useful_life' => 'required|integer|min:1',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'accounting_book_id.required' => 'Tahun buku wajib dipilih',
            'name.required' => 'Nama aktiva wajib diisi',
            'asset_type.required' => 'Jenis aktiva wajib dipilih',
            'asset_group.required' => 'Kelompok aktiva wajib diisi',
            'quantity.required' => 'Jumlah wajib diisi',
            'unit.required' => 'Satuan wajib diisi',
            'unit_price.required' => 'Harga satuan wajib diisi',
            'acquisition_date.required' => 'Tanggal perolehan wajib diisi',
            'useful_life.required' => 'Umur ekonomis wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $assetCode = $this->generateAssetCode();
            $totalCost = $request->quantity * $request->unit_price;
            $salvageValue = $totalCost * 0.1;

            $data = [
                'accounting_book_id' => $request->accounting_book_id,
                'name' => $request->name,
                'asset_code' => $assetCode,
                'asset_type' => $request->asset_type,
                'asset_group' => $request->asset_group,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'unit_price' => $request->unit_price,
                'total_cost' => $totalCost,
                'acquisition_date' => $request->acquisition_date,
                'useful_life' => $request->useful_life,
                'salvage_value' => $salvageValue,
                'created_by' => auth()->id(),
            ];

            // Handle file upload
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('fixed-assets', 'public');
                $data['photo_path'] = $path; // Simpan full path
                \Log::debug('Photo path to be saved:', ['path' => $path]);
            }

            $asset = FixedAsset::create($data);
            \Log::debug('Asset created:', $asset->toArray());

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Aktiva tetap berhasil disimpan',
                'redirect' => route('financial.fixed-asset.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Fixed asset creation failed: " . $e->getMessage());
            \Log::error("Trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan aktiva tetap: '.$e->getMessage()
            ], 500);
        }
    }

    // Generate jurnal untuk aktiva tetap
    private function createAssetJournal(FixedAsset $asset)
    {
        $book = $asset->accountingBook;
        
        // Buat journal header
        $journal = Journal::create([
            'journal_number' => $this->generateJournalNumber($book),
            'transaction_date' => $asset->acquisition_date,
            'reference_number' => 'FA-'.$asset->asset_code,
            'description' => 'Pencatatan aktiva tetap: '.$asset->name,
            'accounting_book_id' => $asset->accounting_book_id,
            'created_by' => auth()->id(),
            'is_fixed_asset' => true
        ]);

        // Debit: Akun aktiva tetap
        JournalEntry::create([
            'journal_id' => $journal->id,
            'account_code' => $asset->account_code,
            'posting_type' => 'increase',
            'debit' => $asset->total_cost,
            'credit' => 0
        ]);

        // Kredit: Akun pembayaran (default ke kas)
        $paymentAccount = $this->coaService->getDefaultPaymentAccount();
        
        JournalEntry::create([
            'journal_id' => $journal->id,
            'account_code' => $paymentAccount,
            'posting_type' => 'decrease',
            'debit' => 0,
            'credit' => $asset->total_cost
        ]);

        return $journal;
    }

    // Generate nomor unik untuk jurnal (mirip dengan JournalController)
    private function generateJournalNumber($book)
    {
        $prefix = 'JRN-' . $book->start_date->format('Ym') . '-';
        $maxAttempts = 5;
        $attempt = 0;

        do {
            try {
                DB::beginTransaction();

                $lastEntry = Journal::where('journal_number', 'like', $prefix.'%')
                    ->lockForUpdate()
                    ->orderBy('journal_number', 'desc')
                    ->first();

                $nextNumber = $lastEntry 
                    ? (int)str_replace($prefix, '', $lastEntry->journal_number) + 1
                    : 1;

                $journalNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                if (!Journal::where('journal_number', $journalNumber)->exists()) {
                    DB::commit();
                    return $journalNumber;
                }

                DB::rollBack();
                $attempt++;
                usleep(100000);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Journal number generation failed: " . $e->getMessage());
                throw $e;
            }
        } while ($attempt < $maxAttempts);

        throw new \Exception("Failed to generate unique journal number after {$maxAttempts} attempts");
    }

    // Edit Aktiva Tetap
    public function edit(FixedAsset $fixedAsset)
    {
        return response()->json([
            'id' => $fixedAsset->id,
            'accounting_book_id' => $fixedAsset->accounting_book_id,
            'name' => $fixedAsset->name,
            'asset_code' => $fixedAsset->asset_code,
            'asset_type' => $fixedAsset->asset_type, // Pastikan ini ada
            'asset_group' => $fixedAsset->asset_group,
            'quantity' => $fixedAsset->quantity,
            'unit' => $fixedAsset->unit,
            'unit_price' => $fixedAsset->unit_price,
            'acquisition_date' => $fixedAsset->acquisition_date->format('Y-m-d'),
            'useful_life' => $fixedAsset->useful_life,
            'salvage_value' => $fixedAsset->salvage_value,
            'photo_path' => $fixedAsset->photo_path // Tambahkan ini
        ]);
    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
        \Log::debug('Update Fixed Asset Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'accounting_book_id' => 'required|exists:accounting_books,id',
            'name' => 'required|string|max:255',
            'asset_type' => 'required|in:tangible,intangible,building',
            'asset_group' => 'required|string|max:100',
            'quantity' => 'required|numeric|min:1',
            'unit' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date|before_or_equal:today',
            'useful_life' => 'required|integer|min:1',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $totalCost = $request->quantity * $request->unit_price;
            $salvageValue = $totalCost * 0.1;

            $data = [
                'accounting_book_id' => $request->accounting_book_id,
                'name' => $request->name,
                'asset_type' => $request->asset_type,
                'asset_group' => $request->asset_group,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'unit_price' => $request->unit_price,
                'total_cost' => $totalCost,
                'acquisition_date' => $request->acquisition_date,
                'useful_life' => $request->useful_life,
                'salvage_value' => $salvageValue,
            ];

            // Handle file upload
            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada
                if ($fixedAsset->photo_path) {
                    Storage::delete('public/' . $fixedAsset->photo_path);
                }
                
                $path = $request->file('photo')->store('public/fixed-assets');
                $data['photo_path'] = str_replace('public/', '', $path);
                \Log::debug('Photo updated to:', [$data['photo_path']]);
            }

            $fixedAsset->update($data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Aktiva tetap berhasil diperbarui',
                'redirect' => route('financial.fixed-asset.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Fixed asset update failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui aktiva tetap: '.$e->getMessage()
            ], 500);
        }
    }

    

    // Hapus Aktiva Tetap
    public function destroy(FixedAsset $fixedAsset)
    {
        DB::beginTransaction();
        try {
            // Hapus depresiasi terkait terlebih dahulu
            $fixedAsset->depreciations()->delete();
            
            // Hapus aktiva tetap
            $fixedAsset->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Aktiva tetap berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error deleting fixed asset: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    // Generate depresiasi untuk aktiva tetap
    public function generateDepreciation(FixedAsset $fixedAsset)
    {
        DB::beginTransaction();
        try {
            // Hapus depresiasi lama jika ada
            $fixedAsset->depreciations()->delete();

            $depreciationPerYear = ($fixedAsset->total_cost - $fixedAsset->salvage_value) / $fixedAsset->useful_life;
            $depreciationPerMonth = $depreciationPerYear / 12;

            $startDate = Carbon::parse($fixedAsset->acquisition_date);
            $endDate = $startDate->copy()->addYears($fixedAsset->useful_life);

            $currentDate = $startDate->copy()->startOfMonth();
            $period = 1;

            while ($currentDate <= $endDate) {
                FixedAssetDepreciation::create([
                    'fixed_asset_id' => $fixedAsset->id,
                    'period' => $period,
                    'depreciation_date' => $currentDate->format('Y-m-d'),
                    'amount' => $depreciationPerMonth,
                    'accumulated_depreciation' => $depreciationPerMonth * $period,
                    'book_value' => $fixedAsset->total_cost - ($depreciationPerMonth * $period),
                    'created_by' => auth()->id()
                ]);

                $currentDate->addMonth();
                $period++;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Depresiasi berhasil digenerate',
                'redirect' => route('financial.fixed-asset.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Depreciation generation failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate depresiasi: '.$e->getMessage()
            ], 500);
        }
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:fixed_assets,id'
        ]);

        DB::beginTransaction();
        try {
            // Hapus depresiasi terkait terlebih dahulu
            FixedAssetDepreciation::whereIn('fixed_asset_id', $request->ids)->delete();
            
            // Hapus aktiva tetap
            FixedAsset::whereIn('id', $request->ids)->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' aktiva tetap berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus aktiva tetap: '.$e->getMessage()
            ], 500);
        }
    }

    private function generateAssetCode()
    {
        $month = Carbon::now()->format('m');
        $lastAsset = FixedAsset::where('asset_code', 'like', $month.'-%')
            ->orderBy('asset_code', 'desc')
            ->first();

        $nextNumber = $lastAsset 
            ? (int)explode('-', $lastAsset->asset_code)[1] + 1
            : 1;

        return $month . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function generateCode()
    {
        return response()->json([
            'asset_code' => $this->generateAssetCode()
        ]);
    }

    
}