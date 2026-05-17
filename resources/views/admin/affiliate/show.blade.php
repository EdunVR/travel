<x-layouts.admin :title="'Detail Mitra - ' . $affiliator->full_name">
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.inventaris.affiliate.index') }}"
               class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $affiliator->full_name }}</h1>
        </div>
        <div class="flex gap-2">
            @if($affiliator->status === 'pending')
            <form action="{{ route('admin.inventaris.affiliate.approve', $affiliator) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                    <i class="fas fa-check text-xs"></i> Approve
                </button>
            </form>
            @elseif($affiliator->status === 'active')
            <form action="{{ route('admin.inventaris.affiliate.suspend', $affiliator) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" onclick="return confirm('Suspend mitra ini?')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition">
                    <i class="fas fa-ban text-xs"></i> Suspend
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Tab Menu -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
        <div class="flex border-b border-slate-200 overflow-x-auto">
            <button onclick="switchTab('dashboard')" 
                    data-tab="dashboard"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-home mr-2"></i>Dashboard
            </button>
            <button onclick="switchTab('referrals')" 
                    data-tab="referrals"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-users mr-2"></i>Referrals
            </button>
            <button onclick="switchTab('payments')" 
                    data-tab="payments"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-money-bill-wave mr-2"></i>Payments
            </button>
            <button onclick="switchTab('wallet')" 
                    data-tab="wallet"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-wallet mr-2"></i>Wallet
            </button>
            <button onclick="switchTab('profile')" 
                    data-tab="profile"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-user mr-2"></i>Profile
            </button>
            <button onclick="switchTab('marketing')" 
                    data-tab="marketing"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-bullhorn mr-2"></i>Marketing
            </button>
            <button onclick="switchTab('reports')" 
                    data-tab="reports"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-chart-bar mr-2"></i>Reports
            </button>
            <button onclick="switchTab('leaderboard')" 
                    data-tab="leaderboard"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-trophy mr-2"></i>Leaderboard
            </button>
            <button onclick="switchTab('jenjang')" 
                    data-tab="jenjang"
                    class="tab-button px-6 py-3 text-sm whitespace-nowrap transition text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                <i class="fas fa-sitemap mr-2"></i>Jenjang
            </button>
        </div>
    </div>

    {{-- Dashboard Tab Content --}}
    <div id="tab-dashboard" class="tab-content grid lg:grid-cols-3 gap-6">
        {{-- Info Mitra --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
                <div class="text-center mb-5">
                    <div class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-3xl mx-auto mb-3 overflow-hidden border-4 border-slate-100 shadow-lg">
                        @if($affiliator->photo)
                            <img src="{{ $affiliator->photo_url }}" alt="{{ $affiliator->full_name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($affiliator->full_name, 0, 1)) }}
                        @endif
                    </div>
                    <h2 class="font-bold text-slate-900">{{ $affiliator->full_name }}</h2>
                    <p class="text-slate-500 text-sm">{{ $affiliator->email }}</p>
                    <div class="mt-2">
                        @if($affiliator->status === 'active')
                            <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium">Aktif</span>
                        @elseif($affiliator->status === 'pending')
                            <span class="bg-amber-100 text-amber-700 text-xs px-3 py-1 rounded-full font-medium">Pending Verifikasi</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full font-medium">Suspended</span>
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4 space-y-2.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Username</span>
                        <code class="font-medium text-green-600 text-xs">{{ $affiliator->username }}</code>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">No. HP</span>
                        <span class="font-medium text-slate-900">{{ $affiliator->phone_number }}</span>
                    </div>
                    <div class="text-sm">
                        <span class="text-slate-500 block mb-1">Link Referral</span>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 flex items-center gap-2">
                            <code class="text-green-600 text-xs flex-1 break-all">{{ $affiliator->referral_link }}</code>
                            <button onclick="copyReferralLink('{{ $affiliator->referral_link }}', event)" 
                                    class="flex-shrink-0 p-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded transition" 
                                    title="Copy link">
                                <i class="fas fa-copy text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Daftar</span>
                        <span class="text-slate-700">{{ $affiliator->created_at ? $affiliator->created_at->format('d M Y') : '-' }}</span>
                    </div>
                    @if($affiliator->approved_at)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Diapprove</span>
                        <span class="text-slate-700">{{ $affiliator->approved_at->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>

                {{-- Komisi Settings --}}
                <div class="mt-4 bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-xs font-semibold text-blue-700">
                            <i class="fas fa-coins mr-1"></i> Pengaturan Komisi
                        </div>
                        <button onclick="openEditCommissionModal()" 
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">PPC (Per Klik)</span>
                            <span class="font-bold text-blue-700">Rp {{ number_format($affiliator->ppc_commission, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Min Sale</span>
                            <span class="font-bold text-blue-700">Rp {{ number_format($affiliator->min_sale_commission, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Cookie Lifetime</span>
                            <span class="font-bold text-blue-700">{{ $affiliator->cookie_lifetime ?? 30 }} Hari</span>
                        </div>
                    </div>
                </div>

                @if($affiliator->bank_name)
                <div class="mt-4 bg-slate-50 rounded-lg p-3 border border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 mb-2">
                        <i class="fas fa-university mr-1"></i> Rekening Bank
                    </div>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->bank_name }}</div>
                    <div class="text-sm font-bold text-slate-900">{{ $affiliator->bank_account_number }}</div>
                    <div class="text-xs text-slate-500">{{ $affiliator->bank_account_name }}</div>
                </div>
                @endif

                {{-- Bukti Pembayaran --}}
                @if($affiliator->payment_proof)
                <div class="mt-4 bg-slate-50 rounded-lg p-3 border border-slate-100">
                    <div class="text-xs font-semibold text-slate-500 mb-2">
                        <i class="fas fa-receipt mr-1"></i> Bukti Pembayaran
                    </div>
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $affiliator->payment_proof) }}" 
                             alt="Bukti Pembayaran" 
                             class="w-full rounded-lg border border-slate-200 cursor-pointer hover:opacity-90 transition"
                             onclick="openPaymentProofModal()">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition rounded-lg flex items-center justify-center">
                            <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-2 flex gap-2">
                        <a href="{{ asset('storage/' . $affiliator->payment_proof) }}" 
                           target="_blank"
                           class="flex-1 text-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs rounded-lg transition">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka
                        </a>
                        <a href="{{ asset('storage/' . $affiliator->payment_proof) }}" 
                           download
                           class="flex-1 text-center px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-600 text-xs rounded-lg transition">
                            <i class="fas fa-download mr-1"></i> Download
                        </a>
                    </div>
                </div>
                @elseif($affiliator->partnershipProgram && $affiliator->partnershipProgram->registration_fee > 0)
                <div class="mt-4 bg-amber-50 rounded-lg p-3 border border-amber-200">
                    <div class="text-xs font-semibold text-amber-700 mb-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Bukti Pembayaran
                    </div>
                    <div class="text-xs text-amber-600">
                        Belum upload bukti pembayaran
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Stats & Referrals --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                $detailStats = [
                    ['label' => 'Total Klik',       'value' => number_format($affiliator->total_clicks),                          'color' => 'text-blue-600'],
                    ['label' => 'Penjualan',         'value' => number_format($affiliator->total_sales),                           'color' => 'text-green-600'],
                    ['label' => 'Saldo Tersedia',    'value' => 'Rp '.number_format($affiliator->available_balance, 0, ',', '.'),  'color' => 'text-green-600'],
                    ['label' => 'Pending',           'value' => 'Rp '.number_format($affiliator->pending_balance, 0, ',', '.'),    'color' => 'text-amber-600'],
                ];
                @endphp
                @foreach($detailStats as $s)
                <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4 text-center">
                    <div class="text-lg font-bold {{ $s['color'] }}">{{ $s['value'] }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $s['label'] }}</div>
                </div>
                @endforeach
            </div>

            {{-- Referral Status --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4">
                <h3 class="font-semibold text-slate-900 mb-3 text-sm">Status Referral</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-xl font-bold text-amber-600">{{ $referralStats['pending'] }}</div>
                        <div class="text-xs text-slate-500">Pending</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-green-600">{{ $referralStats['verified'] }}</div>
                        <div class="text-xs text-slate-500">Verified</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-red-600">{{ $referralStats['rejected'] }}</div>
                        <div class="text-xs text-slate-500">Rejected</div>
                    </div>
                </div>
            </div>

            {{-- Referral Table --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-900 text-sm">Referral Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Paket</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-slate-600 text-xs">Order</th>
                                <th class="text-right px-4 py-2.5 font-semibold text-slate-600 text-xs">Komisi</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Status</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentReferrals as $ref)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5">
                                    <div class="font-medium text-slate-900 text-xs">{{ $ref->package->package_name ?? 'N/A' }}</div>
                                    <div class="text-slate-400 text-xs">{{ $ref->order_date ? $ref->order_date->format('d M Y') : '-' }}</div>
                                </td>
                                <td class="px-4 py-2.5 text-right text-xs text-slate-600">
                                    Rp {{ number_format($ref->order_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-2.5 text-right text-xs font-bold text-green-600">
                                    Rp {{ number_format($ref->commission_amount, 0, ',', '.') }}
                                    @if(($ref->total_pax ?? 1) > 1)
                                    <div class="text-xs text-slate-400 font-normal mt-0.5">
                                        Rp {{ number_format($ref->commission_amount / ($ref->total_pax ?? 1), 0, ',', '.') }} x {{ $ref->total_pax }} pax
                                    </div>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($ref->status === 'verified')
                                        <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Verified</span>
                                    @elseif($ref->status === 'pending')
                                        <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full">Pending</span>
                                    @elseif($ref->status === 'rejected')
                                        <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full">Rejected</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">Paid</span>
                                    @endif
                                    {{-- Fee status --}}
                                    <div class="mt-1">
                                        @if(!$ref->termin_1_released)
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-amber-100 text-amber-600">
                                                ⏳ Menunggu Pelunasan
                                            </span>
                                        @elseif($ref->termin_1_released && !$ref->termin_2_released)
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-blue-100 text-blue-600">
                                                ⏳ Menunggu Keberangkatan
                                            </span>
                                        @else
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-green-100 text-green-600">
                                                ✅ Bisa Ditarik
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        {{-- Button: Release Termin 1 (Pelunasan) --}}
                                        @if(!($ref->termin_1_released ?? false) && !($ref->termin_2_released ?? false))
                                        <form action="{{ route('admin.inventaris.affiliate.referral.release-termin1', $ref) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Cairkan Fee (Saat Pelunasan)"
                                                    class="px-2 py-0.5 rounded bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 transition text-xs">
                                                ✅ Cairkan Fee (Pelunasan)
                                            </button>
                                        </form>
                                        @endif
                                        
                                        {{-- Button: Release Termin 2 (Keberangkatan) --}}
                                        @if(($ref->termin_1_released ?? false) && !($ref->termin_2_released ?? false))
                                        <form action="{{ route('admin.inventaris.affiliate.referral.release-termin2', $ref) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Pindahkan ke Saldo Tersedia (Saat Keberangkatan)"
                                                    class="px-2 py-0.5 rounded bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 transition text-xs">
                                                🚀 Test Keberangkatan
                                            </button>
                                        </form>
                                        @endif
                                        
                                        @if($ref->status === 'pending' && ($ref->termin_1_released ?? false))
                                        <form action="{{ route('admin.inventaris.affiliate.referral.verify', $ref) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="Verify"
                                                    class="p-1 rounded bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 transition">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @if($ref->status === 'pending')
                                        <button type="button" onclick="rejectReferral({{ $ref->id }})"
                                                class="p-1 rounded bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition" title="Reject">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-400 text-sm">Belum ada referral</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Voucher Management --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden" x-data="voucherManager({{ $affiliator->id }})">
                <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-900 text-sm">
                        <i class="fas fa-ticket-alt text-amber-500 mr-1"></i>
                        Voucher Diskon
                    </h3>
                    <button @click="openCreateModal()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs rounded-lg transition">
                        <i class="fas fa-plus text-xs"></i> Buat Voucher
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Kode</th>
                                <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Diskon</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Penggunaan</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Berlaku</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Status</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <template x-if="loading">
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-slate-400 text-sm">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Memuat voucher...
                                    </td>
                                </tr>
                            </template>
                            
                            <template x-if="!loading && vouchers.length === 0">
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-slate-400 text-sm">
                                        Belum ada voucher. Klik "Buat Voucher" untuk membuat voucher pertama.
                                    </td>
                                </tr>
                            </template>
                            
                            <template x-for="voucher in vouchers" :key="voucher.id">
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2.5">
                                        <div class="font-bold text-amber-600 text-xs" x-text="voucher.code"></div>
                                        <div class="text-slate-400 text-xs" x-text="voucher.description || '-'"></div>
                                    </td>
                                    <td class="px-4 py-2.5 text-xs">
                                        <template x-if="voucher.discount_type === 'percentage'">
                                            <span x-text="voucher.discount_value + '%'"></span>
                                        </template>
                                        <template x-if="voucher.discount_type === 'fixed'">
                                            <span x-text="'Rp ' + formatNumber(voucher.discount_value)"></span>
                                        </template>
                                        <template x-if="voucher.max_discount">
                                            <div class="text-slate-400 text-xs" x-text="'Max: Rp ' + formatNumber(voucher.max_discount)"></div>
                                        </template>
                                    </td>
                                    <td class="px-4 py-2.5 text-center text-xs">
                                        <span x-text="voucher.usage_count"></span> / 
                                        <span x-text="voucher.usage_limit || '∞'"></span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center text-xs text-slate-600">
                                        <div x-text="formatDate(voucher.valid_from)"></div>
                                        <div x-text="formatDate(voucher.valid_until)"></div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="text-xs px-2 py-0.5 rounded-full"
                                              :class="{
                                                  'bg-green-100 text-green-700': voucher.is_active && isValidDate(voucher),
                                                  'bg-red-100 text-red-700': !voucher.is_active,
                                                  'bg-amber-100 text-amber-700': voucher.is_active && !isValidDate(voucher)
                                              }"
                                              x-text="getStatusText(voucher)">
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <div class="flex justify-center gap-1">
                                            <button @click="openEditModal(voucher)" 
                                                    class="p-1 rounded bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 transition" 
                                                    title="Edit">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button @click="deleteVoucher(voucher.id)" 
                                                    class="p-1 rounded bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition" 
                                                    title="Hapus"
                                                    :disabled="voucher.usage_count > 0">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Referrals Tab Content --}}
    <div id="tab-referrals" class="tab-content hidden">
    <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold text-slate-900">Semua Referral</h3>
            <div class="flex gap-2">
                <select class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm" onchange="window.location.href='?tab=referrals&status='+this.value">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Tanggal</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Paket</th>
                        <th class="text-right px-4 py-2.5 font-semibold text-slate-600 text-xs">Order</th>
                        <th class="text-right px-4 py-2.5 font-semibold text-slate-600 text-xs">Komisi</th>
                        <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Status</th>
                        <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentReferrals as $ref)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5 text-xs text-slate-600">{{ $ref->order_date ? $ref->order_date->format('d M Y H:i') : '-' }}</td>
                        <td class="px-4 py-2.5">
                            <div class="font-medium text-slate-900 text-xs">{{ $ref->package->package_name ?? 'N/A' }}</div>
                            <div class="text-slate-400 text-xs">Order #{{ $ref->id }}</div>
                        </td>
                        <td class="px-4 py-2.5 text-right text-xs text-slate-600">
                            Rp {{ number_format($ref->order_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2.5 text-right text-xs font-bold text-green-600">
                            Rp {{ number_format($ref->commission_amount, 0, ',', '.') }}
                            <div class="text-xs text-slate-400 font-normal mt-0.5">
                                T1: Rp {{ number_format($ref->termin_1_amount ?? $ref->commission_amount * 0.5, 0, ',', '.') }}
                                | T2: Rp {{ number_format($ref->termin_2_amount ?? $ref->commission_amount * 0.5, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            @if($ref->status === 'verified')
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Verified</span>
                            @elseif($ref->status === 'pending')
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full">Pending</span>
                            @elseif($ref->status === 'rejected')
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full">Rejected</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">Paid</span>
                            @endif
                            <div class="mt-1 flex gap-1 justify-center">
                                <span class="text-xs px-1.5 py-0.5 rounded {{ ($ref->termin_1_released ?? false) ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    T1 {{ ($ref->termin_1_released ?? false) ? '✓' : '○' }}
                                </span>
                                <span class="text-xs px-1.5 py-0.5 rounded {{ ($ref->termin_2_released ?? false) ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    T2 {{ ($ref->termin_2_released ?? false) ? '✓' : '○' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <div class="flex flex-col gap-1 items-center">
                                @if(!($ref->termin_1_released ?? false))
                                <form action="{{ route('admin.inventaris.affiliate.referral.release-termin1', $ref) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Cairkan Termin 1"
                                            class="px-2 py-0.5 rounded bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 transition text-xs">
                                        Cair T1
                                    </button>
                                </form>
                                @elseif(!($ref->termin_2_released ?? false))
                                <form action="{{ route('admin.inventaris.affiliate.referral.release-termin2', $ref) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Cairkan Termin 2"
                                            class="px-2 py-0.5 rounded bg-purple-50 border border-purple-200 text-purple-600 hover:bg-purple-100 transition text-xs">
                                        Cair T2
                                    </button>
                                </form>
                                @endif
                                @if($ref->status === 'pending' && ($ref->termin_1_released ?? false))
                                <form action="{{ route('admin.inventaris.affiliate.referral.verify', $ref) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Verify"
                                            class="p-1 rounded bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 transition">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                @endif
                                @if($ref->status === 'pending')
                                <button type="button" onclick="rejectReferral({{ $ref->id }})"
                                        class="p-1 rounded bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition" title="Reject">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-400 text-sm">Belum ada referral</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

    {{-- Payments Tab Content --}}
    <div id="tab-payments" class="tab-content hidden">
    <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900">Riwayat Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Tanggal</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Metode</th>
                        <th class="text-right px-4 py-2.5 font-semibold text-slate-600 text-xs">Jumlah</th>
                        <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Status</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($affiliator->payouts as $payout)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5 text-xs text-slate-600">{{ $payout->created_at ? $payout->created_at->format('d M Y H:i') : '-' }}</td>
                        <td class="px-4 py-2.5 text-xs text-slate-900">
                            <div class="font-medium">{{ $payout->payment_method ?? 'Transfer Bank' }}</div>
                            <div class="text-slate-400">{{ $payout->bank_name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-2.5 text-right text-xs font-bold text-green-600">
                            Rp {{ number_format($payout->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            @if($payout->status === 'completed')
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Selesai</span>
                            @elseif($payout->status === 'pending')
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full">Pending</span>
                            @elseif($payout->status === 'processing')
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">Diproses</span>
                            @else
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full">Gagal</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-xs text-slate-600">{{ $payout->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-400 text-sm">Belum ada riwayat pembayaran</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

    {{-- Wallet Tab Content --}}
    <div id="tab-wallet" class="tab-content hidden">
    <div class="space-y-4">
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6 text-center">
                <div class="text-2xl font-bold text-green-600">Rp {{ number_format($affiliator->available_balance, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500 mt-1">Saldo Tersedia</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6 text-center">
                <div class="text-2xl font-bold text-amber-600">Rp {{ number_format($affiliator->pending_balance, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500 mt-1">Pending</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6 text-center">
                <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($affiliator->total_withdrawn, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500 mt-1">Total Ditarik</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h3 class="font-semibold text-slate-900">Transaksi Wallet</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Tanggal</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Tipe</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Deskripsi</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-slate-600 text-xs">Jumlah</th>
                            <th class="text-right px-4 py-2.5 font-semibold text-slate-600 text-xs">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php
                        $transactions = collect();
                        // Combine referrals and payouts
                        foreach($affiliator->referrals()->where('status', 'verified')->latest()->get() as $ref) {
                            $transactions->push([
                                'date' => $ref->updated_at,
                                'type' => 'credit',
                                'description' => 'Komisi dari ' . ($ref->package->package_name ?? 'Paket'),
                                'amount' => $ref->commission_amount,
                            ]);
                        }
                        foreach($affiliator->payouts as $payout) {
                            $transactions->push([
                                'date' => $payout->created_at,
                                'type' => 'debit',
                                'description' => 'Penarikan dana',
                                'amount' => $payout->amount,
                            ]);
                        }
                        $transactions = $transactions->sortByDesc('date')->take(20);
                        $balance = $affiliator->available_balance;
                        @endphp
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 text-xs text-slate-600">{{ isset($trx['date']) && $trx['date'] ? $trx['date']->format('d M Y H:i') : '-' }}</td>
                            <td class="px-4 py-2.5">
                                @if($trx['type'] == 'credit')
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Masuk</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full">Keluar</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-xs text-slate-900">{{ $trx['description'] }}</td>
                            <td class="px-4 py-2.5 text-right text-xs font-bold {{ $trx['type'] == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trx['type'] == 'credit' ? '+' : '-' }} Rp {{ number_format($trx['amount'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2.5 text-right text-xs text-slate-600">
                                Rp {{ number_format($balance, 0, ',', '.') }}
                                @php $balance = $trx['type'] == 'credit' ? $balance - $trx['amount'] : $balance + $trx['amount']; @endphp
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-400 text-sm">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    {{-- Profile Tab Content --}}
    <div id="tab-profile" class="tab-content hidden">
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Informasi Pribadi</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500">Nama Lengkap</label>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->full_name }}</div>
                </div>
                <div>
                    <label class="text-xs text-slate-500">Email</label>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->email }}</div>
                </div>
                <div>
                    <label class="text-xs text-slate-500">No. HP</label>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->phone_number }}</div>
                </div>
                <div>
                    <label class="text-xs text-slate-500">Username</label>
                    <div class="text-sm font-medium text-green-600">{{ $affiliator->username }}</div>
                </div>
                <div>
                    <label class="text-xs text-slate-500">Tanggal Daftar</label>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->created_at ? $affiliator->created_at->format('d M Y H:i') : '-' }}</div>
                </div>
                @if($affiliator->approved_at)
                <div>
                    <label class="text-xs text-slate-500">Tanggal Diapprove</label>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->approved_at->format('d M Y H:i') }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Informasi Bank</h3>
            @if($affiliator->bank_name)
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500">Nama Bank</label>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->bank_name }}</div>
                </div>
                <div>
                    <label class="text-xs text-slate-500">Nomor Rekening</label>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->bank_account_number }}</div>
                </div>
                <div>
                    <label class="text-xs text-slate-500">Atas Nama</label>
                    <div class="text-sm font-medium text-slate-900">{{ $affiliator->bank_account_name }}</div>
                </div>
            </div>
            @else
            <div class="text-center py-8 text-slate-400 text-sm">
                <i class="fas fa-university text-3xl mb-2"></i>
                <div>Belum ada informasi bank</div>
            </div>
            @endif
        </div>
    </div>
    </div>

    {{-- Marketing Tab Content --}}
    <div id="tab-marketing" class="tab-content hidden">
    <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
        <h3 class="font-semibold text-slate-900 mb-4">Material Marketing</h3>
        
        <div class="space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm font-medium text-blue-900 mb-2">Link Referral</div>
                <div class="flex items-center gap-2">
                    <code class="flex-1 bg-white border border-blue-200 rounded px-3 py-2 text-xs text-blue-600 break-all">{{ $affiliator->referral_link }}</code>
                    <button onclick="copyReferralLink('{{ $affiliator->referral_link }}', event)" 
                            class="flex-shrink-0 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                        <i class="fas fa-copy mr-1"></i> Copy
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                @php
                $packages = \App\Models\TravelPackage::where('status', 'active')->take(10)->get();
                @endphp
                @foreach($packages as $package)
                <div class="border border-slate-200 rounded-lg p-4">
                    <div class="font-medium text-slate-900 text-sm mb-2">{{ $package->package_name }}</div>
                    <div class="text-xs text-slate-500 mb-3">Rp {{ number_format($package->price, 0, ',', '.') }}</div>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly 
                               value="{{ route('public.paket.show', $package->id) }}?ref={{ $affiliator->username }}"
                               class="flex-1 px-2 py-1 border border-slate-200 rounded text-xs bg-slate-50">
                        <button onclick="copyReferralLink('{{ route('public.paket.show', $package->id) }}?ref={{ $affiliator->username }}', event)" 
                                class="p-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded transition" 
                                title="Copy link">
                            <i class="fas fa-copy text-xs"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    </div>

    {{-- Reports Tab Content --}}
    <div id="tab-reports" class="tab-content hidden">
    <div class="space-y-4">
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4 text-center">
                <div class="text-xl font-bold text-blue-600">{{ number_format($affiliator->total_clicks) }}</div>
                <div class="text-xs text-slate-500 mt-1">Total Klik</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4 text-center">
                <div class="text-xl font-bold text-green-600">{{ number_format($affiliator->total_sales) }}</div>
                <div class="text-xs text-slate-500 mt-1">Total Penjualan</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4 text-center">
                @php
                $conversionRate = $affiliator->total_clicks > 0 ? ($affiliator->total_sales / $affiliator->total_clicks * 100) : 0;
                @endphp
                <div class="text-xl font-bold text-purple-600">{{ number_format($conversionRate, 2) }}%</div>
                <div class="text-xs text-slate-500 mt-1">Conversion Rate</div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4 text-center">
                @php
                $totalEarnings = $affiliator->referrals()->where('status', 'verified')->sum('commission_amount');
                @endphp
                <div class="text-xl font-bold text-green-600">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</div>
                <div class="text-xs text-slate-500 mt-1">Total Komisi</div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h3 class="font-semibold text-slate-900">Klik Terakhir</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Tanggal</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">IP Address</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">User Agent</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Referrer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentClicks as $click)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5 text-xs text-slate-600">{{ $click->clicked_at ? $click->clicked_at->format('d M Y H:i') : '-' }}</td>
                            <td class="px-4 py-2.5 text-xs text-slate-900">{{ $click->ip_address }}</td>
                            <td class="px-4 py-2.5 text-xs text-slate-600">{{ Str::limit($click->user_agent, 50) }}</td>
                            <td class="px-4 py-2.5 text-xs text-slate-600">{{ Str::limit($click->referrer_url ?? '-', 40) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-slate-400 text-sm">Belum ada klik</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    {{-- Leaderboard Tab Content --}}
    <div id="tab-leaderboard" class="tab-content hidden">
        @include('admin.affiliate.leaderboard')
    </div>

</div>

{{-- Modal Reject Referral --}}
<div id="rejectReferralModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-float w-full max-w-sm mx-4 p-6">
        <h3 class="font-bold text-slate-900 mb-4">Tolak Referral</h3>
        <form id="rejectReferralForm" method="POST">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Alasan Penolakan</label>
                <textarea name="reason" rows="3"
                          class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                          placeholder="Contoh: Booking dibatalkan oleh customer"></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('rejectReferralModal').classList.add('hidden')"
                        class="px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Komisi --}}
<div id="editCommissionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-float w-full max-w-md mx-4 p-6">
        <h3 class="font-bold text-slate-900 mb-4">Edit Komisi Mitra</h3>
        <p class="text-sm text-slate-600 mb-4">{{ $affiliator->full_name }}</p>
        
        <form action="{{ route('admin.inventaris.affiliate.update-commission', $affiliator) }}" method="POST">
            @csrf @method('PATCH')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Komisi PPC (Per Klik)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="ppc_commission" value="{{ $affiliator->ppc_commission }}" required min="0" step="1"
                               class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                               placeholder="50">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Default: Rp 50</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Komisi Minimal (Per Penjualan)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="min_sale_commission" value="{{ $affiliator->min_sale_commission }}" required min="0" step="1000"
                               class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                               placeholder="500000">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Default: Rp 500.000</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Cookie Lifetime (Hari)
                    </label>
                    <input type="number" name="cookie_lifetime" value="{{ $affiliator->cookie_lifetime ?? 30 }}" required min="1" max="365" step="1"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                           placeholder="30">
                    <p class="text-xs text-slate-500 mt-1">Berapa lama cookie referral disimpan (1-365 hari)</p>
                </div>
            </div>
            
            <div class="flex gap-2 justify-end mt-6">
                <button type="button" onclick="closeEditCommissionModal()"
                        class="px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Bukti Pembayaran --}}
@if($affiliator->payment_proof)
<div id="paymentProofModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" onclick="closePaymentProofModal()">
    <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
        <button onclick="closePaymentProofModal()" 
                class="absolute -top-10 right-0 text-white hover:text-gray-300 transition">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img src="{{ asset('storage/' . $affiliator->payment_proof) }}" 
             alt="Bukti Pembayaran" 
             class="w-full h-auto rounded-lg shadow-2xl">
        <div class="mt-4 flex gap-2 justify-center">
            <a href="{{ asset('storage/' . $affiliator->payment_proof) }}" 
               target="_blank"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                <i class="fas fa-external-link-alt mr-2"></i> Buka di Tab Baru
            </a>
            <a href="{{ asset('storage/' . $affiliator->payment_proof) }}" 
               download
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                <i class="fas fa-download mr-2"></i> Download
            </a>
        </div>
    </div>
</div>
@endif

{{-- Modal Create/Edit Voucher --}}
<div id="voucherModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" x-data="voucherModalData()">
    <div class="bg-white rounded-2xl shadow-float w-full max-w-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-slate-900" x-text="editMode ? 'Edit Voucher' : 'Buat Voucher Baru'"></h3>
            <button onclick="closeVoucherModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form @submit.prevent="saveVoucher()" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Kode Voucher <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="text" x-model="form.code" required maxlength="50" 
                               :readonly="editMode"
                               class="flex-1 px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 uppercase"
                               placeholder="DISKON10">
                        <button type="button" @click="generateCode()" x-show="!editMode"
                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition">
                            <i class="fas fa-random"></i>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Tipe Diskon <span class="text-red-500">*</span>
                    </label>
                    <select x-model="form.discount_type" required
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300">
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nominal Tetap (Rp)</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Nilai Diskon <span class="text-red-500">*</span>
                    </label>
                    <input type="number" x-model="form.discount_value" required min="0" step="0.01"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                           placeholder="10">
                    <p class="text-xs text-slate-500 mt-1" x-show="form.discount_type === 'percentage'">
                        Contoh: 10 untuk diskon 10%
                    </p>
                    <p class="text-xs text-slate-500 mt-1" x-show="form.discount_type === 'fixed'">
                        Masukkan nominal dalam Rupiah
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Maksimal Diskon (Opsional)
                    </label>
                    <input type="number" x-model="form.max_discount" min="0" step="1000"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                           placeholder="500000">
                    <p class="text-xs text-slate-500 mt-1">Untuk tipe persentase</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Minimal Transaksi
                    </label>
                    <input type="number" x-model="form.min_transaction" min="0" step="1000"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                           placeholder="0">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Batas Penggunaan
                    </label>
                    <input type="number" x-model="form.usage_limit" min="1"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                           placeholder="Unlimited">
                    <p class="text-xs text-slate-500 mt-1">Kosongkan untuk unlimited</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Berlaku Dari
                    </label>
                    <input type="date" x-model="form.valid_from"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Berlaku Sampai
                    </label>
                    <input type="date" x-model="form.valid_until"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Deskripsi
                </label>
                <textarea x-model="form.description" rows="2"
                          class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                          placeholder="Deskripsi voucher..."></textarea>
            </div>
            
            <div class="flex items-center gap-2">
                <input type="checkbox" x-model="form.is_active" id="voucherActive" class="rounded">
                <label for="voucherActive" class="text-sm text-slate-700">Aktif</label>
            </div>
            
            <div class="flex gap-2 justify-end pt-4 border-t border-slate-200">
                <button type="button" onclick="closeVoucherModal()"
                        class="px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" :disabled="saving"
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition disabled:opacity-50">
                    <span x-show="!saving">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </span>
                    <span x-show="saving">
                        <i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tab Jenjang --}}
<div id="tab-jenjang" class="tab-content hidden space-y-4">

    {{-- SVG Tree — tinggi diatur otomatis oleh JS via aspect-ratio --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-card relative" style="overflow:hidden; width:100%">
        <div id="jenjang-loading" class="absolute inset-0 flex items-center justify-center bg-white z-10">
            <div class="text-center text-slate-400">
                <i class="fas fa-sitemap text-3xl mb-2 block opacity-30"></i>
                <p class="text-sm">Klik tab Jenjang untuk memuat pohon</p>
            </div>
        </div>
        <svg id="jenjang-svg" style="width:100%;display:block"></svg>
    </div>

    {{-- Edit Jenjang — via modal (tombol di bawah tree) --}}
    <div class="flex justify-end gap-2">
        <button onclick="openEditJenjangModal()"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition">
            <i class="fas fa-edit"></i> Edit Jenjang Mitra Ini
        </button>
    </div>

    {{-- Fee Distributions --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-card p-5">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i class="fas fa-money-bill-wave text-amber-500"></i> Distribusi Fee dari Downline
        </h3>
        @php
            $feeDists = \App\Models\AffiliateFeeDistribution::where('to_affiliator_id', $affiliator->id)
                ->with(['fromAffiliator'])->latest()->take(30)->get();
        @endphp
        @if($feeDists->isEmpty())
            <p class="text-sm text-slate-400 italic text-center py-4">Belum ada distribusi fee</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-2 font-semibold text-slate-600 text-xs">Dari Mitra</th>
                        <th class="text-center px-4 py-2 font-semibold text-slate-600 text-xs">Termin</th>
                        <th class="text-right px-4 py-2 font-semibold text-slate-600 text-xs">Jumlah</th>
                        <th class="text-center px-4 py-2 font-semibold text-slate-600 text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($feeDists as $fd)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-medium text-slate-800 text-xs">{{ $fd->fromAffiliator?->full_name }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $fd->termin === 'termin_1' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $fd->termin === 'termin_1' ? 'T1' : 'T2' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right font-medium text-green-700 text-xs">Rp {{ number_format($fd->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $fd->status === 'released' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $fd->status === 'released' ? 'Cair' : ucfirst($fd->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<script src="{{ asset('js/affiliate-tree.js') }}?v={{ filemtime(public_path('js/affiliate-tree.js')) }}"></script>
<script>
// ─── Jenjang SVG Tree (uses AffiliateTree class) ──────────────────────────────
(function() {
    @php
        $aff    = $affiliator;
        $myId   = $aff->id;
        $mySlug = $aff->partnershipProgram?->slug ?? '';
        $jNodes = [];
        $jEdges = [];

        $jNodes[] = ['id' => $aff->id, 'name' => $aff->full_name,
                     'program' => $aff->partnershipProgram?->name ?? '-',
                     'slug' => $mySlug, 'isSelf' => true];

        foreach ([
            ['node' => $aff->uplineMaster,  'to_level' => 'hm-master'],
            ['node' => $aff->uplineLeader,  'to_level' => 'hm-leader'],
            ['node' => $aff->uplinePartner, 'to_level' => 'hm-partner'],
        ] as $up) {
            if (!$up['node']) continue;
            $jNodes[] = ['id' => $up['node']->id, 'name' => $up['node']->full_name,
                         'program' => $up['node']->partnershipProgram?->name ?? '-',
                         'slug' => $up['node']->partnershipProgram?->slug ?? '', 'isSelf' => false];
            
            // Ambil fee setting spesifik atau global
            $feeSetting = \App\Models\AffiliateHierarchySetting::getFeeForPair(
                $mySlug,
                $up['to_level'],
                $aff->id,
                $up['node']->id
            );
            
            $jEdges[] = [
                'from' => $aff->id, 
                'to' => $up['node']->id,
                'has_fee' => false, 
                'fee_total' => 0,
                'from_level' => $mySlug,
                'to_level' => $up['to_level'],
                'percentage' => $feeSetting['percentage'] ?? 0,
                'fee_type' => $feeSetting['fee_type'] ?? 'percentage',
                'fee_value' => $feeSetting['fee_value'] ?? 0,
                'is_specific' => $feeSetting['is_specific'] ?? false,
            ];
        }

        $dlNodes = collect();
        if ($mySlug === 'hm-master')  $dlNodes = $aff->downlineLeaders()->with('partnershipProgram')->get();
        elseif ($mySlug === 'hm-leader')  $dlNodes = $aff->downlinePartners()->with('partnershipProgram')->get();
        elseif ($mySlug === 'hm-partner') $dlNodes = $aff->downlineSellers()->with('partnershipProgram')->get();

        $feeMap2 = \App\Models\AffiliateFeeDistribution::where('to_affiliator_id', $aff->id)
            ->whereIn('status', ['released','paid'])
            ->selectRaw('from_affiliator_id, SUM(amount) as total')
            ->groupBy('from_affiliator_id')
            ->pluck('total', 'from_affiliator_id');

        foreach ($dlNodes as $dl) {
            $dlSlug = $dl->partnershipProgram?->slug ?? '';
            $jNodes[] = ['id' => $dl->id, 'name' => $dl->full_name,
                         'program' => $dl->partnershipProgram?->name ?? '-',
                         'slug' => $dlSlug, 'isSelf' => false];
            $dlFee = $feeMap2[$dl->id] ?? 0;
            
            // Ambil fee setting spesifik atau global untuk downline
            $feeSetting = \App\Models\AffiliateHierarchySetting::getFeeForPair(
                $dlSlug,
                $mySlug,
                $dl->id,
                $aff->id
            );
            
            $jEdges[] = [
                'from' => $dl->id, 
                'to' => $aff->id,
                'has_fee' => $dlFee > 0, 
                'fee_total' => (float)$dlFee,
                'from_level' => $dlSlug,
                'to_level' => $mySlug,
                'percentage' => $feeSetting['percentage'] ?? 0,
                'fee_type' => $feeSetting['fee_type'] ?? 'percentage',
                'fee_value' => $feeSetting['fee_value'] ?? 0,
                'is_specific' => $feeSetting['is_specific'] ?? false,
            ];
        }
    @endphp

    const J_NODES = @json($jNodes);
    const J_EDGES = @json($jEdges);
    let jRendered = false;

    const jBtn = document.querySelector('[data-tab="jenjang"]');
    if (jBtn) {
        jBtn.addEventListener('click', () => {
            if (jRendered) return;
            jRendered = true;
            setTimeout(() => {
                requestAnimationFrame(() => {
                const svg = document.getElementById('jenjang-svg');
                document.getElementById('jenjang-loading').classList.add('hidden');
                const tree = new AffiliateTree(svg, J_NODES, J_EDGES, {
                    isAdmin:    true,
                    csrfToken:  '{{ csrf_token() }}',
                    feeUrl:     '{{ route("admin.inventaris.affiliate.hierarchy.fee.save") }}',
                    viewBase:   '{{ url("admin/inventaris/affiliate") }}',
                    updateBase: '{{ url("admin/inventaris/affiliate") }}',
                });
                tree.render();
                window.affTreeTab = tree;

                document.addEventListener('affTree:editNode', ({ detail: { node } }) => {
                    document.getElementById('jtab-edit-subtitle').textContent = node.name;
                    document.getElementById('jtab-edit-form').action =
                        `{{ url("admin/inventaris/affiliate") }}/${node.id}/update-hierarchy`;
                    document.getElementById('jtab-edit-master').value  = node.upline_master_id  || '';
                    document.getElementById('jtab-edit-leader').value  = node.upline_leader_id  || '';
                    document.getElementById('jtab-edit-partner').value = node.upline_partner_id || '';
                    document.getElementById('jtab-modal-edit').classList.remove('hidden');
                });

                document.addEventListener('affTree:reload', () => {
                    jRendered = false; jBtn.click();
                });
                }); // end requestAnimationFrame
            }, 80);
        });
    }

})();

// Pre-fill modal dengan data mitra saat ini (untuk tombol "Edit Jenjang Mitra Ini")
function openEditJenjangModal() {
    const form = document.getElementById('jtab-edit-form');
    form.action = '{{ route("admin.inventaris.affiliate.update-hierarchy", $affiliator) }}';
    document.getElementById('jtab-edit-subtitle').textContent = '{{ $affiliator->full_name }}';
    document.getElementById('jtab-edit-master').value  = '{{ $affiliator->upline_master_id ?? "" }}';
    document.getElementById('jtab-edit-leader').value  = '{{ $affiliator->upline_leader_id ?? "" }}';
    document.getElementById('jtab-edit-partner').value = '{{ $affiliator->upline_partner_id ?? "" }}';
    document.getElementById('jtab-modal-edit').classList.remove('hidden');
}
</script>

{{-- Modal Edit Jenjang (Tab) --}}
<div id="jtab-modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Edit Jenjang</h3>
                <p class="text-xs text-slate-400 mt-0.5" id="jtab-edit-subtitle"></p>
            </div>
            <button onclick="document.getElementById('jtab-modal-edit').classList.add('hidden')"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400">✕</button>
        </div>
        <form id="jtab-edit-form" method="POST" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cabang</label>
                <select name="upline_master_id" id="jtab-edit-master"
                    class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">— Pusat —</option>
                    @foreach(\App\Models\Affiliator::active()->whereHas('partnershipProgram', fn($q) => $q->where('slug','hm-master'))->get() as $m)
                    <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Leader</label>
                <select name="upline_leader_id" id="jtab-edit-leader"
                    class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">— Tidak Ada —</option>
                    @foreach(\App\Models\Affiliator::active()->whereHas('partnershipProgram', fn($q) => $q->where('slug','hm-leader'))->get() as $l)
                    <option value="{{ $l->id }}">{{ $l->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Partner</label>
                <select name="upline_partner_id" id="jtab-edit-partner"
                    class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">— Tidak Ada —</option>
                    @foreach(\App\Models\Affiliator::active()->whereHas('partnershipProgram', fn($q) => $q->where('slug','hm-partner'))->get() as $p)
                    <option value="{{ $p->id }}">{{ $p->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('jtab-modal-edit').classList.add('hidden')"
                    class="flex-1 h-10 border border-slate-200 text-slate-600 text-sm rounded-xl hover:bg-slate-50">Batal</button>
                <button type="submit" id="jtab-edit-submit" class="flex-1 h-10 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-xl font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
// ─── Loading helper ───────────────────────────────────────────────────────────
function setLoadingBtn(btnId, loading) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    if (loading) {
        btn._orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:5px"><svg style="animation:spin 0.8s linear infinite;width:13px;height:13px" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/></svg>Menyimpan...</span>';
    } else {
        btn.disabled = false;
        btn.innerHTML = btn._orig || 'Simpan';
    }
}

// Handle jtab-edit-form submit with loading
document.getElementById('jtab-edit-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    setLoadingBtn('jtab-edit-submit', true);
    const fd = new FormData(this);
    try {
        await fetch(this.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: fd,
        });
    } catch(err) { console.error(err); }
    setLoadingBtn('jtab-edit-submit', false);
    document.getElementById('jtab-modal-edit').classList.add('hidden');
    // Reload jenjang tree
    if (window.affTreeTab) {
        const jBtn = document.querySelector('[data-tab="jenjang"]');
        if (jBtn) { jRendered = false; jBtn.click(); }
    }
});

// Vanilla JavaScript Tab Switcher (No Alpine.js)
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-b-2', 'border-green-600', 'text-green-600', 'font-medium');
        button.classList.add('text-slate-600');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById('tab-' + tabName);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }
    
    // Add active class to selected tab button
    const selectedButton = document.querySelector('.tab-button[data-tab="' + tabName + '"]');
    if (selectedButton) {
        selectedButton.classList.add('border-b-2', 'border-green-600', 'text-green-600', 'font-medium');
        selectedButton.classList.remove('text-slate-600');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check URL parameter for initial tab
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'dashboard';
    switchTab(tab);
});

// Base URL for API calls
const baseUrl = '{{ url("/") }}';

// Voucher Manager Alpine Component
function voucherManager(affiliatorId) {
    return {
        vouchers: [],
        loading: false,
        affiliatorId: affiliatorId,
        
        init() {
            this.loadVouchers();
            
            // Listen for reload event
            window.addEventListener('reload-vouchers', () => {
                this.loadVouchers();
            });
        },
        
        async loadVouchers() {
            this.loading = true;
            try {
                const response = await fetch(`${baseUrl}/admin/affiliate/vouchers/list?affiliator_id=${this.affiliatorId}`);
                const data = await response.json();
                this.vouchers = data.data || [];
            } catch (error) {
                console.error('Error loading vouchers:', error);
            } finally {
                this.loading = false;
            }
        },
        
        openCreateModal() {
            window.dispatchEvent(new CustomEvent('open-voucher-modal', { 
                detail: { mode: 'create', affiliatorId: this.affiliatorId } 
            }));
        },
        
        openEditModal(voucher) {
            window.dispatchEvent(new CustomEvent('open-voucher-modal', { 
                detail: { mode: 'edit', voucher: voucher } 
            }));
        },
        
        async deleteVoucher(id) {
            if (!confirm('Hapus voucher ini?')) return;
            
            try {
                const response = await fetch(`${baseUrl}/admin/affiliate/vouchers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Voucher berhasil dihapus');
                    this.loadVouchers();
                } else {
                    alert(data.message || 'Gagal menghapus voucher');
                }
            } catch (error) {
                console.error('Error deleting voucher:', error);
                alert('Terjadi kesalahan saat menghapus voucher');
            }
        },
        
        formatNumber(num) {
            return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        
        isValidDate(voucher) {
            const now = new Date();
            const validFrom = voucher.valid_from ? new Date(voucher.valid_from) : null;
            const validUntil = voucher.valid_until ? new Date(voucher.valid_until) : null;
            
            if (validFrom && now < validFrom) return false;
            if (validUntil && now > validUntil) return false;
            
            return true;
        },
        
        getStatusText(voucher) {
            if (!voucher.is_active) return 'Tidak Aktif';
            if (!this.isValidDate(voucher)) return 'Kadaluarsa';
            if (voucher.usage_limit && voucher.usage_count >= voucher.usage_limit) return 'Habis';
            return 'Aktif';
        }
    }
}

// Voucher Modal Data
function voucherModalData() {
    return {
        editMode: false,
        saving: false,
        affiliatorId: null,
        form: {
            id: null,
            id_affiliator: null,
            code: '',
            discount_type: 'percentage',
            discount_value: '',
            max_discount: '',
            min_transaction: 0,
            usage_limit: '',
            valid_from: '',
            valid_until: '',
            description: '',
            is_active: true
        },
        
        init() {
            window.addEventListener('open-voucher-modal', (e) => {
                const { mode, voucher, affiliatorId } = e.detail;
                this.editMode = mode === 'edit';
                
                if (mode === 'create') {
                    this.resetForm();
                    this.affiliatorId = affiliatorId;
                    this.form.id_affiliator = affiliatorId;
                } else {
                    this.form = { ...voucher };
                    this.affiliatorId = voucher.id_affiliator;
                }
                
                document.getElementById('voucherModal').classList.remove('hidden');
            });
        },
        
        async generateCode() {
            try {
                const response = await fetch(`${baseUrl}/admin/affiliate/vouchers/generate-code`);
                const data = await response.json();
                if (data.success) {
                    this.form.code = data.code;
                }
            } catch (error) {
                console.error('Error generating code:', error);
            }
        },
        
        async saveVoucher() {
            this.saving = true;
            try {
                const url = this.editMode 
                    ? `${baseUrl}/admin/affiliate/vouchers/${this.form.id}`
                    : `${baseUrl}/admin/affiliate/vouchers`;
                
                const method = this.editMode ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    closeVoucherModal();
                    // Reload vouchers
                    window.dispatchEvent(new CustomEvent('reload-vouchers'));
                } else {
                    alert(data.message || 'Gagal menyimpan voucher');
                }
            } catch (error) {
                console.error('Error saving voucher:', error);
                alert('Terjadi kesalahan saat menyimpan voucher');
            } finally {
                this.saving = false;
            }
        },
        
        resetForm() {
            this.form = {
                id: null,
                id_affiliator: this.affiliatorId,
                code: '',
                discount_type: 'percentage',
                discount_value: '',
                max_discount: '',
                min_transaction: 0,
                usage_limit: '',
                valid_from: '',
                valid_until: '',
                description: '',
                is_active: true
            };
        }
    }
}

// Listen for reload vouchers event
window.addEventListener('reload-vouchers', () => {
    // Trigger reload on all voucher managers
    const event = new CustomEvent('vouchers-reload');
    window.dispatchEvent(event);
});

function closeVoucherModal() {
    document.getElementById('voucherModal').classList.add('hidden');
}

function rejectReferral(id) {
    document.getElementById('rejectReferralForm').action = '{{ url("admin/inventaris/affiliate/referral") }}/' + id + '/reject';
    document.getElementById('rejectReferralModal').classList.remove('hidden');
}

function copyReferralLink(link, event) {
    event.preventDefault();
    event.stopPropagation();
    
    navigator.clipboard.writeText(link).then(function() {
        // Show success feedback
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-xs"></i>';
        btn.classList.remove('bg-green-100', 'hover:bg-green-200');
        btn.classList.add('bg-green-500', 'text-white');
        
        setTimeout(function() {
            btn.innerHTML = originalHTML;
            btn.classList.remove('bg-green-500', 'text-white');
            btn.classList.add('bg-green-100', 'hover:bg-green-200');
        }, 2000);
    }).catch(function(err) {
        alert('Gagal copy link: ' + err);
    });
}

function openEditCommissionModal() {
    document.getElementById('editCommissionModal').classList.remove('hidden');
}

function closeEditCommissionModal() {
    document.getElementById('editCommissionModal').classList.add('hidden');
}

function openPaymentProofModal() {
    document.getElementById('paymentProofModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePaymentProofModal() {
    document.getElementById('paymentProofModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on outside click
document.getElementById('editCommissionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditCommissionModal();
    }
});

document.getElementById('voucherModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeVoucherModal();
    }
});

// Close payment proof modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentProofModal();
        closeVoucherModal();
    }
});
</script>
</x-layouts.admin>
