<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AccountInvestment;

class InvestorDashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:investor');
    }

    public function index()
    {
        $investor = Auth::guard('investor')->user();
        
        $investor->load([
            'accounts.investments' => function($query) {
                $query->latest()->limit(5);
            },
            'accounts.profitDistributions' => function($query) {
                $query->latest();
            },
            'documents' => function($query) {
                $query->latest()->limit(5);
            }
        ]);

        // Ambil distribusi profit dari account pertama
        $distributions = $investor->accounts->first() 
            ? $investor->accounts->first()->profitDistributions 
            : collect();

        return view('investor.dashboard', [
            'investor' => $investor,
            'totalInvestment' => $investor->accounts->sum('current_balance'),
            'totalProfit' => $investor->total_profit,
            'profitShare' => $investor->profit_share,
            'recentInvestments' => $investor->accounts->flatMap->investments,
            'documents' => $investor->documents,
            'distributions' => $distributions
        ]);
    }

    public function profile()
    {
        $investor = Auth::guard('investor')->user();
        return view('investor.profile', compact('investor'));
    }

    public function updateProfile(Request $request)
    {
        $investor = Auth::guard('investor')->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email,'.$investor->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($investor->photo) {
                Storage::disk('public')->delete($investor->photo);
            }
            $validated['photo'] = $request->file('photo')->store('investor-photos', 'public');
        }

        $investor->update($validated);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui');
    }

    public function investments()
    {
        $investor = Auth::guard('investor')->user();
        
        $accounts = $investor->accounts()->with(['investments' => function($query) {
            $query->latest();
        }])->get();

        return view('investor.investments.index', compact('accounts'));
    }

    public function documents()
    {
        $investor = Auth::guard('investor')->user();
        $documents = $investor->documents()
            ->latest()
            ->paginate(10);
            
        return view('investor.documents.index', compact('documents'));
    }

    public function viewDocument($id)
    {
        $investor = Auth::guard('investor')->user();
        $document = $investor->documents()->findOrFail($id);
        
        if ($document->is_custom) {
            return PDF::loadView('investor.documents.custom_template', [
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

    public function profitDistribution()
    {
        $investor = Auth::guard('investor')->user();
        
        // Gunakan paginate langsung pada query
        $distributions = AccountInvestment::whereHas('account', function($query) use ($investor) {
                $query->where('investor_id', $investor->id);
            })
            ->where('type', 'deposit')
            ->with('account') // Eager load account
            ->latest()
            ->paginate(10);
            
        return view('investor.profit-distribution', compact('distributions'));
    }
}