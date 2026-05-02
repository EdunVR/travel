<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Log;
use DB;
use Carbon\Carbon;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $investors = Investor::with(['accounts', 'customers'])
                ->select('investors.*');

            return DataTables::of($investors)
                ->addIndexColumn()
                ->addColumn('join_date', function($row) {
                    return $row->join_date->format('d M Y');
                })
                ->addColumn('accounts_info', function($row) {
                            if ($row->accounts->isEmpty()) {
                                return '-';
                            }
                            
                            $accounts = $row->accounts;
                            $firstTwoAccounts = $accounts->take(2);
                            $remainingAccounts = $accounts->slice(2);
                            
                            $html = $firstTwoAccounts->map(function($account) {
                                return $this->formatAccountInfo($account);
                            })->implode('');
                            
                            if ($remainingAccounts->isNotEmpty()) {
                                $html .= '
                                <div class="more-accounts" style="display:none;">
                                    '.$remainingAccounts->map(function($account) {
                                        return $this->formatAccountInfo($account);
                                    })->implode('').'
                                </div>
                                <button class="btn btn-sm btn-link toggle-accounts" 
                                        data-investor="'.$row->id.'">
                                    <i data-feather="chevron-down" class="icon-sm"></i> Lihat '.$remainingAccounts->count().' rekening lainnya
                                </button>';
                            }
                            
                            return '<div class="account-info">'.$html.'</div>';
                        })
                ->addColumn('total_investment', function($row) {
                    return $row->accounts->sum('total_investment');
                })
                ->addColumn('paid_customers_count', function($row) {
                    return $row->customers->where('status', 'paid')->count();
                })
                ->addColumn('total_profit', function($row) {
                    return $row->accounts->sum('profit_balance');
                })
                ->addColumn('average_percentage', function($row) {
                    return $row->accounts->avg('profit_percentage');
                })
                ->addColumn('profit_share', function($row) {
                    $percentage = $row->accounts->avg('profit_percentage');
                    return $row->estimasi_keuntungan * $row->customers->where('status', 'paid')->count() * ($percentage / 100);
                })
                ->addColumn('total_keseluruhan', function($row) {
                    $totalInvestment = $row->accounts->sum('total_investment');
                    $percentage = $row->accounts->avg('profit_percentage');
                    $profitShare = $row->estimasi_keuntungan * $row->customers->where('status', 'paid')->count() * ($percentage / 100);
                    return $totalInvestment + $profitShare;
                })
                ->addColumn('total_transfer', function($row) {
                    return $row->accounts->sum(function($account) {
                        return $account->investments()->where('type', 'withdrawal')->sum('amount');
                    });
                })
                ->addColumn('keuntungan_pengelola', function($row) {
                    $totalProfit = $row->estimasi_keuntungan * $row->customers->where('status', 'paid')->count();
                    $percentage = $row->accounts->avg('profit_percentage');
                    $profitShare = $totalProfit * ($percentage / 100);
                    return $totalProfit - $profitShare;
                })
                ->addColumn('action', function($row) {
                    return '
                    <div class="d-flex">
                        <a href="'.route('irp.investor.show', $row->id).'" class="btn btn-sm btn-info mr-1" title="Detail">
                            <i data-feather="eye" class="icon-sm"></i>
                        </a>
                        <a href="'.route('irp.investor.edit', $row->id).'" class="btn btn-sm btn-warning mr-1" title="Edit">
                            <i data-feather="edit" class="icon-sm"></i>
                        </a>
                        <form action="'.route('irp.investor.destroy', $row->id).'" method="POST" onsubmit="return confirm(\'Apakah Anda yakin?\')">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                <i data-feather="trash-2" class="icon-sm"></i>
                            </button>
                        </form>
                    </div>';
                })
                ->editColumn('tempo', function($row) {
                    return $row->tempo ? Carbon::parse($row->tempo)->format('d/m/Y') : '-';
                })
                ->editColumn('keberangkatan', function($row) {
                    return $row->keberangkatan ? Carbon::parse($row->keberangkatan)->format('d/m/Y') : '-';
                })
                ->editColumn('estimasi_keuntungan', function($row) {
                    return number_format($row->estimasi_keuntungan, 0, ',', '.');
                })
                ->rawColumns(['action', 'accounts_info'])
                ->make(true);
        }

        return view('irp.investor.index');
    }

    private function formatAccountInfo($account)
    {
        $accountNumber = $account->account_number;
        $tempo = $account->tempo ? Carbon::parse($account->tempo)->format('d/m/Y') : 'Belum ada tempo';
        $statusBadge = $account->status == 'active' ? 
            '<span class="badge badge-success">Aktif</span>' : 
            '<span class="badge badge-secondary">Non-Aktif</span>';
        
        return '
        <div class="account-item">
            <strong>'.$accountNumber.'</strong><br>
            <small>Tempo: '.$tempo.'</small><br>
            '.$statusBadge.'
        </div>';
    }

    public function create()
    {
        return view('irp.investor.create', [
            'title' => 'Tambah Investor Baru',
            'action' => route('irp.investor.store')
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:investors,email',
                'phone' => 'required|string|max:20',
                'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
                'category' => 'required|in:syirkah,investama,sukuk,internal,eksternal,aktif,pasif',
                'status' => 'required|in:active,inactive',
                'join_date' => 'required|date',
                'initial_investment' => 'required|numeric|min:0',
                'address' => 'nullable|string',
                'notes' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'tempo' => 'nullable|date',
                'bank' => 'nullable|string|max:255',
                'rekening' => 'nullable|numeric',
                'atas_nama' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi gagal:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        Log::info('Validasi berhasil', $validated);


        // Handle file upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('investor-photos', 'public');
        }

        DB::beginTransaction();
        try {
            $investor = Investor::create($validated);

            /// Jika ada investasi awal, buat rekening
            if ($request->initial_investment > 0) {
               
                $account = $investor->accounts()->create([
                    'account_number' => 'GTS-' . time(),
                    'bank_name' => 'Investasi Awal',
                    'account_name' => $investor->name,
                    'initial_balance' => $request->initial_investment,
                    'current_balance' => $request->initial_investment,
                    'profit_percentage' => $request->profit_percentage, // default 50%
                    'date' => $request->join_date,
                    'tempo' => $request->tempo,
                    'status' => 'active'
                ]);

                // Catat investasi awal
                $account->investments()->create([
                    'date' => now(),
                    'type' => 'investment',
                    'amount' => $request->initial_investment,
                    'description' => 'Investasi awal'
                ]);
            }

            DB::commit();
            return redirect()->route('irp.investor.index')
                            ->with('success', 'Investor berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan investor: ' . $e->getMessage());
        }
    }

    public function show(Investor $investor)
    {
        $investor->load(['accounts.investments', 'documents']);
        $activeTab = request()->get('tab', 'accounts'); // default ke accounts

        $customers = $investor->customers()->with('member')->get();
        
        // Get member IDs already associated with this investor
        $existingMemberIds = $investor->customers->pluck('id_member')->toArray();
        
        // Get available members (not already associated)
        $availableMembers = Member::whereNotIn('id_member', $existingMemberIds)->get();
        
        return view('irp.investor.show', [
            'investor' => $investor,
            'accounts' => $investor->accounts,
            'documents' => $investor->documents,
            'activeTab' => $activeTab,
            'customers' => $customers,
            'availableMembers' => $availableMembers
        ]);
    }

    public function edit(Investor $investor)
    {
        return view('irp.investor.create', [
            'title' => 'Edit Data Investor',
            'action' => route('irp.investor.update', $investor->id),
            'investor' => $investor
        ]);
    }

    public function update(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email,'.$investor->id,
            'phone' => 'required|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'category' => 'required|in:syirkah,investama,sukuk,internal,eksternal,aktif,pasif',
            'status' => 'required|in:active,inactive',
            'join_date' => 'required|date',
            'initial_investment' => 'required|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bank' => 'nullable|string|max:255',
            'rekening' => 'nullable|numeric',
            'atas_nama' => 'nullable|string',
            'tempo' => 'nullable|date',
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($investor->photo) {
                Storage::disk('public')->delete($investor->photo);
            }
            $validated['photo'] = $request->file('photo')->store('investor-photos', 'public');
        }

        $investor->update($validated);

        return redirect()->route('irp.investor.show', $investor->id)
                         ->with('success', 'Data investor berhasil diperbarui');
    }

    public function destroy(Investor $investor)
    {
        // Delete photo if exists
        if ($investor->photo) {
            Storage::disk('public')->delete($investor->photo);
        }

        $investor->delete();

        return redirect()->route('irp.investor.index')
                         ->with('success', 'Investor berhasil dihapus');
    }

    // Method untuk menampilkan daftar rekening investor
    public function accounts(Investor $investor)
    {
        return view('irp.investor.accounts.index', [
            'investor' => $investor,
            'accounts' => $investor->accounts()->withSum('investments', 'amount')->get()
        ]);
    }

    // Method untuk menampilkan form tambah rekening
    public function createAccount(Investor $investor)
    {
        return view('irp.investor.accounts.create', [
            'investor' => $investor
        ]);
    }

    // Method untuk menyimpan rekening baru
    public function storeAccount(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'account_number' => 'required|unique:investor_accounts',
            'bank_name' => 'required',
            'account_name' => 'required',
            'initial_balance' => 'required|numeric|min:0',
            'profit_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable'
        ]);

        $account = $investor->accounts()->create(array_merge($validated, [
            'current_balance' => $validated['initial_balance']
        ]));

        return redirect()->route('irp.investor.accounts.show', [$investor->id, $account->id])
            ->with('success', 'Rekening investor berhasil ditambahkan');
    }

    // Method untuk menampilkan detail rekening

    public function storeDocument(Request $request, Investor $investor)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'document' => 'required|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $path = $request->file('document')->store('investor-documents', 'public');

        $investor->documents()->create([
            'title' => $request->title,
            'type' => $request->type,
            'file_path' => $path,
            'is_custom' => false
        ]);

        return back()->with('success', 'Dokumen berhasil diupload');
    }

    public function createCustomDocument(Request $request, Investor $investor)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'type' => 'required'
        ]);

        // Generate PDF
        $pdf = PDF::loadView('irp.investor.documents.custom_template', [
            'title' => $request->title,
            'header' => $request->header,
            'content' => $request->content,
            'footer' => $request->footer,
            'investor' => $investor,
            'date' => now()->format('d F Y')
        ]);

        $filename = 'doc_'.Str::slug($request->title).'_'.time().'.pdf';
        $path = 'investor-documents/custom/'.$filename;
        
        // Simpan file PDF
        Storage::disk('public')->put($path, $pdf->output());

        // Simpan record dokumen
        $document = $investor->documents()->create([
            'title' => $request->title,
            'type' => $request->type,
            'file_path' => $path,
            'is_custom' => true,
            'content' => $request->content,
            'meta' => [
                'header' => $request->header,
                'footer' => $request->footer
            ]
        ]);

        return back()->with('success', 'Dokumen berhasil dibuat');
    }

    public function viewDocument(Investor $investor, $documentId)
    {
        $document = $investor->documents()->findOrFail($documentId);
        
        if ($document->is_custom) {
            return PDF::loadView('irp.investor.documents.custom_template', [
                'title' => $document->title,
                'header' => $document->meta['header'] ?? '',
                'content' => $document->content,
                'footer' => $document->meta['footer'] ?? '',
                'investor' => $investor,
                'date' => $document->created_at->format('d F Y')
            ])->stream($document->title.'.pdf');
        }

        return response()->file(storage_path('app/public/'.$document->file_path));
    }

    public function destroyDocument(Investor $investor, $documentId)
    {
        $document = $investor->documents()->findOrFail($documentId);
        
        // Hapus file fisik
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();

        return response()->json(['success' => true]);
    }

}