<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliator;
use App\Models\AffiliateReferral;
use App\Models\AffiliatePayout;
use App\Models\AffiliateClick;
use App\Models\AffiliateSetting;
use App\Models\AffiliatePackageCommission;
use App\Models\AffiliateHierarchySetting;
use App\Models\AffiliateFeeDistribution;
use App\Models\TravelPackage;
use Illuminate\Http\Request;

class AffiliateAdminController extends Controller
{
    /**
     * Dashboard & daftar affiliator
     */
    public function index(Request $request)
    {
        $query = Affiliator::withCount(['clicks', 'referrals'])
            ->withSum('referrals as total_commission', 'commission_amount');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('phone_number', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $affiliators = $query->latest()->paginate(20);

        // Calculate agency fee for each affiliator
        foreach ($affiliators as $affiliator) {
            $affiliator->agency_fee_total = $this->calculateAgencyFee($affiliator);
            $affiliator->recruited_count = $affiliator->recruits()->count();
        }

        // Summary stats
        $stats = [
            'total' => Affiliator::count(),
            'active' => Affiliator::where('status', 'active')->count(),
            'pending' => Affiliator::where('status', 'pending')->count(),
            'total_commission_paid' => AffiliateReferral::where('status', 'verified')->sum('commission_amount'),
            'total_clicks' => AffiliateClick::count(),
            'pending_payouts' => AffiliatePayout::where('status', 'pending')->sum('amount'),
        ];

        return view('admin.affiliate.index', compact('affiliators', 'stats'));
    }

    /**
     * Calculate agency fee for an affiliator
     */
    private function calculateAgencyFee($affiliator)
    {
        $settings = \App\Models\CompanySetting::first();
        
        if (!$settings || !$settings->agency_fee_enabled) {
            return 0;
        }

        $recruited = Affiliator::where('recruited_by', $affiliator->id)->get();
        $totalFee = 0;

        foreach ($recruited as $downline) {
            $downlineCommission = AffiliateReferral::where('affiliator_id', $downline->id)
                ->where('status', 'verified')
                ->sum('commission_amount');

            // Calculate agency fee based on type
            if ($settings->agency_fee_type == 'percentage') {
                $totalFee += ($downlineCommission * $settings->agency_fee_percentage / 100);
            } elseif ($settings->agency_fee_type == 'fixed') {
                $transactionCount = AffiliateReferral::where('affiliator_id', $downline->id)
                    ->where('status', 'verified')
                    ->count();
                $totalFee += ($transactionCount * $settings->agency_fee_fixed);
            } elseif ($settings->agency_fee_type == 'both') {
                $percentageFee = ($downlineCommission * $settings->agency_fee_percentage / 100);
                $transactionCount = AffiliateReferral::where('affiliator_id', $downline->id)
                    ->where('status', 'verified')
                    ->count();
                $fixedFee = ($transactionCount * $settings->agency_fee_fixed);
                $totalFee += ($percentageFee + $fixedFee);
            }
        }

        return $totalFee;
    }

    /**
     * Detail affiliator
     */
    public function show(Request $request, Affiliator $affiliator)
    {
        $affiliator->load([
            'clicks', 'referrals.package', 'payouts',
            'partnershipProgram',
            'uplineMaster', 'uplineLeader', 'uplinePartner',
        ]);

        $referralStats = [
            'pending' => $affiliator->referrals()->where('status', 'pending')->count(),
            'verified' => $affiliator->referrals()->where('status', 'verified')->count(),
            'rejected' => $affiliator->referrals()->where('status', 'rejected')->count(),
        ];

        // Load data based on active tab
        $tab = $request->get('tab', 'dashboard');
        
        if ($tab == 'referrals') {
            $query = $affiliator->referrals()->with('package')->latest();
            if ($request->status) {
                $query->where('status', $request->status);
            }
            $recentReferrals = $query->get();
        } else {
            $recentReferrals = $affiliator->referrals()
                ->with('package')
                ->latest()
                ->take(20)
                ->get();
        }

        $recentClicks = $affiliator->clicks()
            ->latest('clicked_at')
            ->take(20)
            ->get();

        // Load leaderboard data for the leaderboard tab
        $period = $request->get('period', 'all');
        $type = $request->get('type', 'total');

        $query = Affiliator::where('status', 'active')
            ->withCount(['clicks', 'referrals as total_sales' => function($q) {
                $q->where('status', 'verified');
            }])
            ->withSum(['clicks as ppc_earnings' => function($q) use ($period) {
                $this->applyPeriodFilter($q, $period, 'clicked_at');
            }], 'commission_amount')
            ->withSum(['referrals as referral_earnings' => function($q) use ($period) {
                $q->where('status', 'verified');
                $this->applyPeriodFilter($q, $period, 'order_date');
            }], 'commission_amount');

        $leaderboard = $query->get()->map(function($aff) {
            $aff->total_earnings = ($aff->ppc_earnings ?? 0) + ($aff->referral_earnings ?? 0);
            return $aff;
        });

        // Sort by type
        if ($type == 'ppc') {
            $leaderboard = $leaderboard->sortByDesc('ppc_earnings')->values();
        } elseif ($type == 'referral') {
            $leaderboard = $leaderboard->sortByDesc('referral_earnings')->values();
        } else {
            $leaderboard = $leaderboard->sortByDesc('total_earnings')->values();
        }

        $topThree = $leaderboard->take(3);

        return view('admin.affiliate.show', compact('affiliator', 'referralStats', 'recentReferrals', 'recentClicks', 'leaderboard', 'topThree'));
    }

    /**
     * Approve affiliator
     */
    public function approve(Affiliator $affiliator)
    {
        $affiliator->update([
            'status' => 'active',
            'approved_at' => now(),
        ]);

        return back()->with('success', "Affiliator {$affiliator->full_name} berhasil diaktifkan.");
    }

    /**
     * Suspend affiliator
     */
    public function suspend(Affiliator $affiliator)
    {
        $affiliator->update(['status' => 'suspended']);
        return back()->with('success', "Affiliator {$affiliator->full_name} telah disuspend.");
    }

    /**
     * Halaman pengaturan sistem affiliate
     */
    public function settings()
    {
        $settings = AffiliateSetting::all()->keyBy('key');
        $packages = TravelPackage::active()->get(['id', 'package_name', 'price']);
        $packageCommissions = AffiliatePackageCommission::with('package')->get();

        return view('admin.affiliate.settings', compact('settings', 'packages', 'packageCommissions'));
    }

    /**
     * Simpan pengaturan
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'cookie_lifetime' => 'required|integer|min:3600',
            'default_click_commission' => 'required|integer|min:0',
            'default_sale_commission_type' => 'required|in:percentage,flat',
            'default_sale_commission_value' => 'required|numeric|min:0',
            'minimum_payout' => 'required|integer|min:10000',
        ]);

        AffiliateSetting::setValue('cookie_lifetime', $request->cookie_lifetime, 'integer');
        AffiliateSetting::setValue('default_click_commission', $request->default_click_commission, 'integer');
        AffiliateSetting::setValue('default_sale_commission_type', $request->default_sale_commission_type, 'string');
        AffiliateSetting::setValue('default_sale_commission_value', $request->default_sale_commission_value, 'integer');
        AffiliateSetting::setValue('minimum_payout', $request->minimum_payout, 'integer');
        AffiliateSetting::setValue('auto_approve_affiliates', $request->has('auto_approve_affiliates') ? 1 : 0, 'boolean');
        AffiliateSetting::setValue('click_fraud_prevention', $request->has('click_fraud_prevention') ? 1 : 0, 'boolean');
        AffiliateSetting::setValue('affiliate_enabled', $request->has('affiliate_enabled') ? 1 : 0, 'boolean');

        return back()->with('success', 'Pengaturan affiliate berhasil disimpan.');
    }

    /**
     * Simpan komisi per paket
     */
    public function savePackageCommission(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:travel_packages,id',
            'click_commission' => 'required|numeric|min:0',
            'sale_commission_type' => 'required|in:percentage,flat',
            'sale_commission_value' => 'required|numeric|min:0',
        ]);

        AffiliatePackageCommission::updateOrCreate(
            ['package_id' => $request->package_id],
            [
                'click_commission' => $request->click_commission,
                'sale_commission_type' => $request->sale_commission_type,
                'sale_commission_value' => $request->sale_commission_value,
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Komisi paket berhasil disimpan.');
    }

    /**
     * Hapus komisi per paket
     */
    public function deletePackageCommission(AffiliatePackageCommission $commission)
    {
        $commission->delete();
        return back()->with('success', 'Komisi paket berhasil dihapus.');
    }

    /**
     * Halaman manajemen withdraw/payout
     */
    public function payouts(Request $request)
    {
        $query = AffiliatePayout::with('affiliator')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payouts = $query->paginate(20);

        $stats = [
            'pending_count' => AffiliatePayout::where('status', 'pending')->count(),
            'pending_amount' => AffiliatePayout::where('status', 'pending')->sum('amount'),
            'completed_amount' => AffiliatePayout::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.affiliate.payouts', compact('payouts', 'stats'));
    }

    /**
     * Approve payout
     */
    public function approvePayout(AffiliatePayout $payout)
    {
        $payout->update([
            'status' => 'completed',
            'processed_at' => now(),
            'completed_at' => now(),
        ]);

        return back()->with('success', "Payout Rp " . number_format($payout->amount, 0, ',', '.') . " berhasil diproses.");
    }

    /**
     * Reject payout
     */
    public function rejectPayout(Request $request, AffiliatePayout $payout)
    {
        $payout->update([
            'status' => 'failed',
            'notes' => $request->reason,
        ]);

        // Kembalikan saldo ke affiliator
        $payout->affiliator->increment('available_balance', $payout->amount);

        return back()->with('success', 'Payout ditolak dan saldo dikembalikan ke affiliator.');
    }

    /**
     * Update commission settings per affiliator
     */
    public function updateCommission(Request $request, Affiliator $affiliator)
    {
        $request->validate([
            'ppc_commission' => 'required|numeric|min:0',
            'min_sale_commission' => 'required|numeric|min:0',
            'cookie_lifetime' => 'required|integer|min:1|max:365',
        ]);

        $affiliator->update([
            'ppc_commission' => $request->ppc_commission,
            'min_sale_commission' => $request->min_sale_commission,
            'cookie_lifetime' => $request->cookie_lifetime,
        ]);

        return back()->with('success', "Komisi {$affiliator->full_name} berhasil diupdate.");
    }

    /**
     * Verify referral (komisi cair)
     */
    public function verifyReferral(AffiliateReferral $referral)
    {
        if ($referral->status !== 'pending') {
            return back()->withErrors(['error' => 'Referral ini sudah diproses.']);
        }

        $referral->affiliator->verifyReferral($referral->id);

        return back()->with('success', 'Komisi referral berhasil diverifikasi dan saldo affiliator bertambah.');
    }

    /**
     * Reject referral
     */
    public function rejectReferral(Request $request, AffiliateReferral $referral)
    {
        if ($referral->status !== 'pending') {
            return back()->withErrors(['error' => 'Referral ini sudah diproses.']);
        }

        $referral->update([
            'status' => 'rejected',
            'notes' => $request->reason ?? 'Ditolak oleh admin',
        ]);

        $referral->affiliator->decrement('pending_balance', $referral->commission_amount);

        return back()->with('success', 'Referral berhasil ditolak.');
    }

    /**
     * Halaman Leaderboard Affiliator
     */
    public function leaderboard(Request $request)
    {
        $period = $request->get('period', 'all');
        $type = $request->get('type', 'total');

        $query = Affiliator::where('status', 'active')
            ->withCount(['clicks', 'referrals as total_sales' => function($q) {
                $q->where('status', 'verified');
            }])
            ->withSum(['clicks as ppc_earnings' => function($q) use ($period) {
                $this->applyPeriodFilter($q, $period, 'clicked_at');
            }], 'commission_amount')
            ->withSum(['referrals as referral_earnings' => function($q) use ($period) {
                $q->where('status', 'verified');
                $this->applyPeriodFilter($q, $period, 'order_date');
            }], 'commission_amount');

        $leaderboard = $query->get()->map(function($aff) {
            $aff->total_earnings = ($aff->ppc_earnings ?? 0) + ($aff->referral_earnings ?? 0);
            return $aff;
        });

        // Sort by type
        if ($type == 'ppc') {
            $leaderboard = $leaderboard->sortByDesc('ppc_earnings')->values();
        } elseif ($type == 'referral') {
            $leaderboard = $leaderboard->sortByDesc('referral_earnings')->values();
        } else {
            $leaderboard = $leaderboard->sortByDesc('total_earnings')->values();
        }

        $topThree = $leaderboard->take(3);

        return view('admin.affiliate.leaderboard', compact('leaderboard', 'topThree'));
    }

    /**
     * Helper untuk apply period filter
     */
    private function applyPeriodFilter($query, $period, $dateColumn)
    {
        if ($period == 'month') {
            $query->whereMonth($dateColumn, now()->month)->whereYear($dateColumn, now()->year);
        } elseif ($period == 'quarter') {
            $query->whereBetween($dateColumn, [now()->startOfQuarter(), now()->endOfQuarter()]);
        } elseif ($period == 'year') {
            $query->whereYear($dateColumn, now()->year);
        }
    }

    /**
     * Release termin 1 (saat pelunasan)
     */
    public function releaseTermin1(AffiliateReferral $referral)
    {
        if ($referral->termin_1_released) {
            return back()->withErrors(['error' => 'Termin 1 sudah pernah dicairkan.']);
        }

        $referral->affiliator->releaseTermin1($referral->id);

        return back()->with('success', 'Termin 1 (50%) berhasil dicairkan ke saldo pending mitra.');
    }

    /**
     * Release termin 2 (saat keberangkatan)
     */
    public function releaseTermin2(AffiliateReferral $referral)
    {
        if (!$referral->termin_1_released) {
            return back()->withErrors(['error' => 'Termin 1 harus dicairkan terlebih dahulu.']);
        }
        if ($referral->termin_2_released) {
            return back()->withErrors(['error' => 'Termin 2 sudah pernah dicairkan.']);
        }

        $referral->affiliator->releaseTermin2($referral->id);

        return back()->with('success', 'Termin 2 (50%) berhasil dicairkan ke saldo pending mitra.');
    }

    /**
     * Halaman setting persentase fee antar jenjang
     */
    public function hierarchySettings()
    {
        $settings = AffiliateHierarchySetting::orderBy('from_level')->orderBy('to_level')->get();
        $levels = ['hm-seller', 'hm-partner', 'hm-leader', 'hm-master'];
        return view('admin.affiliate.hierarchy-settings', compact('settings', 'levels'));
    }

    /**
     * Simpan setting jenjang
     */
    public function saveHierarchySettings(Request $request)
    {
        $request->validate([
            'settings'                  => 'required|array',
            'settings.*.from_level'     => 'required|string',
            'settings.*.to_level'       => 'required|string',
            'settings.*.fee_type'       => 'required|in:percentage,flat',
            'settings.*.fee_value'      => 'required|numeric|min:0',
            'settings.*.is_active'      => 'nullable|boolean',
        ]);

        foreach ($request->settings as $data) {
            $feeType  = $data['fee_type'];
            $feeValue = (float) $data['fee_value'];
            $pct      = $feeType === 'percentage' ? $feeValue : 0;

            AffiliateHierarchySetting::updateOrCreate(
                ['from_level' => $data['from_level'], 'to_level' => $data['to_level']],
                [
                    'percentage' => $pct,
                    'fee_type'   => $feeType,
                    'fee_value'  => $feeValue,
                    'is_active'  => isset($data['is_active']) ? (bool)$data['is_active'] : true,
                    'notes'      => $data['notes'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Setting jenjang berhasil disimpan.');
    }

    /**
     * Halaman pohon jenjang MLM (tree view)
     */
    public function hierarchyTree(Request $request)
    {
        // Ambil semua HM Master sebagai root
        $roots = Affiliator::with([
                'partnershipProgram',
                'downlineLeaders.partnershipProgram',
                'downlineLeaders.downlinePartners.partnershipProgram',
                'downlineLeaders.downlinePartners.downlineSellers.partnershipProgram',
            ])
            ->whereHas('partnershipProgram', fn($q) => $q->where('slug', 'hm-master'))
            ->where('status', 'active')
            ->get();

        // Mitra tanpa upline master (standalone)
        $standalone = Affiliator::with('partnershipProgram')
            ->whereNull('upline_master_id')
            ->whereHas('partnershipProgram', fn($q) => $q->whereNotIn('slug', ['hm-master']))
            ->where('status', 'active')
            ->get();

        $hierarchySettings = AffiliateHierarchySetting::orderBy('from_level')->get();

        return view('admin.affiliate.hierarchy-tree', compact('roots', 'standalone', 'hierarchySettings'));
    }

    /**
     * JSON data untuk SVG tree (dipakai oleh JS)
     */
    public function hierarchyTreeData()
    {
        $matrix   = AffiliateHierarchySetting::getMatrix();
        $allAffs  = Affiliator::with('partnershipProgram')
            ->whereIn('status', ['active', 'pending'])
            ->get();

        $feeMap = AffiliateFeeDistribution::selectRaw(
                'from_affiliator_id, to_affiliator_id, SUM(amount) as total, COUNT(*) as cnt'
            )
            ->whereIn('status', ['released', 'paid'])
            ->groupBy('from_affiliator_id', 'to_affiliator_id')
            ->get()
            ->keyBy(fn($r) => $r->from_affiliator_id . '_' . $r->to_affiliator_id);

        $nodes = $allAffs->map(fn($a) => [
            'id'       => $a->id,
            'name'     => $a->full_name,
            'program'  => $a->partnershipProgram?->name ?? '-',
            'slug'     => $a->partnershipProgram?->slug ?? '',
            'status'   => $a->status,
            'upline_master_id'  => $a->upline_master_id,
            'upline_leader_id'  => $a->upline_leader_id,
            'upline_partner_id' => $a->upline_partner_id,
            'total_sales'       => $a->total_sales,
        ]);

        $edges = [];
        foreach ($allAffs as $a) {
            $slug = $a->partnershipProgram?->slug ?? '';
            $pairs = [
                ['child' => $a->id, 'parent' => $a->upline_partner_id, 'to_level' => 'hm-partner'],
                ['child' => $a->id, 'parent' => $a->upline_leader_id,  'to_level' => 'hm-leader'],
                ['child' => $a->id, 'parent' => $a->upline_master_id,  'to_level' => 'hm-master'],
            ];
            foreach ($pairs as $p) {
                if (!$p['parent']) continue;
                $key     = $a->id . '_' . $p['parent'];
                $feeData = $feeMap[$key] ?? null;
                
                // Ambil fee setting spesifik atau global
                $feeSetting = AffiliateHierarchySetting::getFeeForPair(
                    $slug,
                    $p['to_level'],
                    $a->id,
                    $p['parent']
                );
                
                $pct     = $feeSetting['percentage'] ?? 0;
                $feeType = $feeSetting['fee_type']   ?? 'percentage';
                $feeVal  = $feeSetting['fee_value']  ?? $pct;
                $isSpecific = $feeSetting['is_specific'] ?? false;
                
                $edges[] = [
                    'from'       => $a->id,
                    'to'         => $p['parent'],
                    'percentage' => $pct,
                    'fee_type'   => $feeType,
                    'fee_value'  => $feeVal,
                    'fee_total'  => $feeData ? (float)$feeData->total : 0,
                    'has_fee'    => $feeData && $feeData->total > 0,
                    'from_level' => $slug,
                    'to_level'   => $p['to_level'],
                    'is_specific' => $isSpecific,
                ];
            }
        }

        return response()->json([
            'nodes'  => $nodes->values(),
            'edges'  => $edges,
            'matrix' => $matrix,
        ]);
    }

    /**
     * Simpan fee setting untuk satu pasangan level (dari klik garis)
     * Bisa untuk setting global atau spesifik per mitra
     */
    public function saveLineFee(Request $request)
    {
        try {
            $validated = $request->validate([
                'from_level' => 'required|string|min:1',
                'to_level'   => 'required|string|min:1',
                'fee_type'   => 'required|in:percentage,flat',
                'fee_value'  => 'required|numeric|min:0',
                'from_affiliator_id' => 'nullable|exists:affiliators,id',
                'to_affiliator_id'   => 'nullable|exists:affiliators,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()),
                'errors'  => $e->validator->errors(),
            ], 422);
        }

        $feeType  = $request->fee_type;
        $feeValue = (float) $request->fee_value;
        $pct      = $feeType === 'percentage' ? $feeValue : 0;

        $data = [
            'percentage' => $pct,
            'fee_type'   => $feeType,
            'fee_value'  => $feeValue,
            'is_active'  => true,
        ];

        // Tentukan unique key berdasarkan apakah ini setting spesifik atau global
        $uniqueKey = [
            'from_level' => $request->from_level,
            'to_level'   => $request->to_level,
            'from_affiliator_id' => $request->from_affiliator_id,
            'to_affiliator_id'   => $request->to_affiliator_id,
        ];

        AffiliateHierarchySetting::updateOrCreate($uniqueKey, $data);

        $message = $request->from_affiliator_id && $request->to_affiliator_id
            ? 'Fee spesifik untuk mitra ini berhasil disimpan.'
            : 'Fee global berhasil disimpan.';

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Hapus affiliator
     */
    public function destroy(Affiliator $affiliator)
    {
        $affiliator->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Data node untuk AJAX tree
     */
    public function hierarchyTreeNode(Affiliator $affiliator)
    {
        $affiliator->load([
            'partnershipProgram',
            'downlineLeaders.partnershipProgram',
            'downlinePartners.partnershipProgram',
            'downlineSellers.partnershipProgram',
        ]);

        return response()->json([
            'id'       => $affiliator->id,
            'name'     => $affiliator->full_name,
            'username' => $affiliator->username,
            'program'  => $affiliator->partnershipProgram?->name,
            'slug'     => $affiliator->partnershipProgram?->slug,
            'photo'    => $affiliator->photo_url,
            'status'   => $affiliator->status,
            'children' => $this->buildTreeChildren($affiliator),
        ]);
    }

    private function buildTreeChildren(Affiliator $aff): array
    {
        $children = [];
        $slug = $aff->partnershipProgram?->slug;

        if ($slug === 'hm-master') {
            foreach ($aff->downlineLeaders as $leader) {
                $children[] = $this->buildTreeNode($leader);
            }
        } elseif ($slug === 'hm-leader') {
            foreach ($aff->downlinePartners as $partner) {
                $children[] = $this->buildTreeNode($partner);
            }
        } elseif ($slug === 'hm-partner') {
            foreach ($aff->downlineSellers as $seller) {
                $children[] = $this->buildTreeNode($seller);
            }
        }

        return $children;
    }

    private function buildTreeNode(Affiliator $aff): array
    {
        $aff->load(['partnershipProgram', 'downlineLeaders.partnershipProgram', 'downlinePartners.partnershipProgram', 'downlineSellers.partnershipProgram']);
        return [
            'id'       => $aff->id,
            'name'     => $aff->full_name,
            'username' => $aff->username,
            'program'  => $aff->partnershipProgram?->name,
            'slug'     => $aff->partnershipProgram?->slug,
            'photo'    => $aff->photo_url,
            'status'   => $aff->status,
            'children' => $this->buildTreeChildren($aff),
        ];
    }

    /**
     * Update jenjang upline affiliator
     */
    public function updateHierarchy(Request $request, Affiliator $affiliator)
    {
        $request->validate([
            'upline_master_id'  => 'nullable|exists:affiliators,id',
            'upline_leader_id'  => 'nullable|exists:affiliators,id',
            'upline_partner_id' => 'nullable|exists:affiliators,id',
        ]);

        $affiliator->update([
            'upline_master_id'  => $request->upline_master_id  ?: null,
            'upline_leader_id'  => $request->upline_leader_id  ?: null,
            'upline_partner_id' => $request->upline_partner_id ?: null,
        ]);

        // Return JSON untuk AJAX, redirect untuk form biasa
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Jenjang berhasil diperbarui.']);
        }

        return back()->with('success', 'Jenjang mitra berhasil diperbarui.');
    }
}
