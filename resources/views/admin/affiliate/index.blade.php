<x-layouts.admin :title="'Manajemen Mitra'">
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Mitra</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola semua mitra HM Tour</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.inventaris.affiliate.hierarchy.tree') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-50 border border-blue-200 text-sm text-blue-700 hover:bg-blue-100 transition">
                <i class="fas fa-sitemap text-xs"></i> Pohon Jenjang
            </a>
            <a href="{{ route('admin.inventaris.affiliate.leaderboard') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-purple-50 border border-purple-200 text-sm text-purple-700 hover:bg-purple-100 transition">
                <i class="fas fa-trophy text-xs"></i> Leaderboard
            </a>
            <a href="{{ route('admin.inventaris.affiliate.payouts') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-700 hover:bg-amber-100 transition">
                <i class="fas fa-money-bill-wave text-xs"></i> Withdraw
                @if($stats['pending_payouts'] > 0)
                <span class="ml-1 bg-amber-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                    {{ number_format($stats['pending_payouts']/1000) }}K
                </span>
                @endif
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
        $statCards = [
            ['label' => 'Total Mitra', 'value' => $stats['total'],                                                    'color' => 'text-blue-600'],
            ['label' => 'Aktif',            'value' => $stats['active'],                                                   'color' => 'text-green-600'],
            ['label' => 'Pending',          'value' => $stats['pending'],                                                  'color' => 'text-amber-600'],
            ['label' => 'Total Klik',       'value' => number_format($stats['total_clicks']),                              'color' => 'text-sky-600'],
            ['label' => 'Komisi Dibayar',   'value' => 'Rp '.number_format($stats['total_commission_paid']/1000000,1).'jt','color' => 'text-green-600'],
            ['label' => 'Pending Withdraw', 'value' => 'Rp '.number_format($stats['pending_payouts']/1000000,1).'jt',     'color' => 'text-amber-600'],
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4 text-center">
            <div class="text-xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</div>
            <div class="text-xs text-slate-500 mt-1">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="flex-1 min-w-48 h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                   placeholder="Cari nama, HP, atau email...">
            <select name="status"
                    class="h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                <option value="">Semua Status</option>
                <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Aktif</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
            <button type="submit"
                    class="h-9 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.inventaris.affiliate.index') }}"
               class="h-9 px-4 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50 transition flex items-center">
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Mitra</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Username / HP</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Program</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Klik</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Penjualan</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Saldo Tersedia</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Pending</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($affiliators as $aff)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 overflow-hidden border-2 border-slate-100">
                                    @if($aff->photo)
                                        <img src="{{ $aff->photo_url }}" alt="{{ $aff->full_name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($aff->full_name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $aff->full_name }}</div>
                                    <div class="text-xs text-slate-400">{{ $aff->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-green-600 text-xs font-mono">{{ $aff->username }}</code>
                            <div class="text-xs text-slate-400 mt-0.5">
                                <span class="inline-flex items-center gap-1">
                                    <span class="truncate max-w-xs" title="{{ $aff->referral_link }}">{{ $aff->referral_link }}</span>
                                    <button onclick="copyToClipboard('{{ $aff->referral_link }}', this)" 
                                            class="flex-shrink-0 p-0.5 hover:bg-green-100 text-green-600 rounded transition" 
                                            title="Copy link">
                                        <i class="fas fa-copy text-xs"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $aff->phone_number }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($aff->partnershipProgram)
                                <div class="text-sm font-semibold text-slate-800">{{ $aff->partnershipProgram->name }}</div>
                                <div class="text-xs text-slate-500">Min: {{ $aff->partnershipProgram->formatted_commission }}</div>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded-full">
                                {{ number_format($aff->total_clicks) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded-full">
                                {{ number_format($aff->total_sales) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">
                            Rp {{ number_format($aff->available_balance, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right text-amber-600">
                            Rp {{ number_format($aff->pending_balance, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($aff->status === 'active')
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">Aktif</span>
                            @elseif($aff->status === 'pending')
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded-full font-medium">Pending</span>
                            @else
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">Suspended</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-1">
                                <a href="{{ route('admin.inventaris.affiliate.show', $aff) }}"
                                   class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition" title="Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <button onclick="openEditModal({{ $aff->id }}, '{{ $aff->full_name }}', {{ $aff->ppc_commission }}, {{ $aff->min_sale_commission }}, {{ $aff->cookie_lifetime ?? 30 }})"
                                        class="p-1.5 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition" title="Edit Komisi">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                @if($aff->status === 'pending')
                                <form action="{{ route('admin.inventaris.affiliate.approve', $aff) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" onclick="return confirm('Aktifkan mitra ini?')"
                                            class="p-1.5 rounded-lg bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 transition" title="Approve">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                @elseif($aff->status === 'active')
                                <form action="{{ route('admin.inventaris.affiliate.suspend', $aff) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" onclick="return confirm('Suspend mitra ini?')"
                                            class="p-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 transition" title="Suspend">
                                        <i class="fas fa-ban text-xs"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.inventaris.affiliate.approve', $aff) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 transition" title="Aktifkan">
                                        <i class="fas fa-redo text-xs"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.inventaris.affiliate.destroy', $aff) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra {{ $aff->full_name }}? Data yang terkait dengan mitra ini juga akan terhapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition" title="Hapus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-12 text-slate-400">
                            <i class="fas fa-users text-3xl mb-3 block"></i>
                            Belum ada mitra
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($affiliators->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $affiliators->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Modal Edit Komisi --}}
<div id="editCommissionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-float w-full max-w-md mx-4 p-6">
        <h3 class="font-bold text-slate-900 mb-4">Edit Komisi Mitra</h3>
        <p class="text-sm text-slate-600 mb-4">Mitra: <span id="affiliatorName" class="font-semibold"></span></p>
        
        <form id="editCommissionForm" method="POST">
            @csrf @method('PATCH')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Komisi PPC (Per Klik)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="ppc_commission" id="ppcCommission" required min="0" step="1"
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
                        <input type="number" name="min_sale_commission" id="minSaleCommission" required min="0" step="1000"
                               class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                               placeholder="500000">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Default: Rp 500.000</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Cookie Lifetime (Hari)
                    </label>
                    <input type="number" name="cookie_lifetime" id="cookieLifetime" required min="1" max="365" step="1"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                           placeholder="30">
                    <p class="text-xs text-slate-500 mt-1">Berapa lama cookie referral disimpan (1-365 hari)</p>
                </div>
            </div>
            
            <div class="flex gap-2 justify-end mt-6">
                <button type="button" onclick="closeEditModal()"
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

<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback
        const icon = button.querySelector('i');
        const originalClass = icon.className;
        icon.className = 'fas fa-check text-xs';
        button.classList.add('bg-green-500', 'text-white');
        button.classList.remove('text-green-600');
        
        setTimeout(function() {
            icon.className = originalClass;
            button.classList.remove('bg-green-500', 'text-white');
            button.classList.add('text-green-600');
        }, 2000);
    }).catch(function(err) {
        alert('Gagal copy link: ' + err);
    });
}

function openEditModal(id, name, ppc, minSale, cookieLifetime) {
    document.getElementById('affiliatorName').textContent = name;
    document.getElementById('ppcCommission').value = ppc || 50;
    document.getElementById('minSaleCommission').value = minSale || 500000;
    document.getElementById('cookieLifetime').value = cookieLifetime || 30;
    document.getElementById('editCommissionForm').action = '{{ url("admin/inventaris/affiliate") }}/' + id + '/update-commission';
    document.getElementById('editCommissionModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCommissionModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('editCommissionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
</x-layouts.admin>
