<x-layouts.admin :title="'Setting Fee Jenjang Mitra'">
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Setting Fee Jenjang</h1>
            <p class="text-slate-500 text-sm mt-1">Atur distribusi fee dari downline ke upline (% atau nominal Rp)</p>
        </div>
        <a href="{{ route('admin.inventaris.affiliate.hierarchy.tree') }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 border border-slate-200 text-sm text-slate-700 hover:bg-slate-200 transition">
            <i class="fas fa-sitemap text-xs"></i> Lihat Pohon Jenjang
        </a>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
        <i class="fas fa-info-circle mr-2"></i>
        <strong>Sistem Budget Tetap Rp2.000.000 per penjualan.</strong><br>
        Pembagian otomatis berdasarkan siapa yang melakukan closing. Fee cair setelah pelunasan + keberangkatan.
    </div>

    {{-- Tabel Simulasi Default --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-green-50">
            <h3 class="font-semibold text-green-800 flex items-center gap-2">
                <i class="fas fa-table text-green-600"></i> Simulasi Pembagian Komisi Umrah (Budget Rp2.000.000)
            </h3>
            <p class="text-xs text-green-600 mt-1">Pembagian default otomatis berdasarkan jenjang HM Member → HM Seller → HM Master</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Closing oleh</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">HM Member</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">HM Seller</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">HM Master</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">HM Member</span>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-green-700">Rp500.000</td>
                        <td class="px-4 py-3 text-center font-semibold text-blue-700">Rp500.000</td>
                        <td class="px-4 py-3 text-center font-semibold text-purple-700">Rp1.000.000</td>
                        <td class="px-4 py-3 text-center font-bold text-slate-800">Rp2.000.000</td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">HM Seller</span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-400">-</td>
                        <td class="px-4 py-3 text-center font-semibold text-blue-700">Rp1.000.000</td>
                        <td class="px-4 py-3 text-center font-semibold text-purple-700">Rp1.000.000</td>
                        <td class="px-4 py-3 text-center font-bold text-slate-800">Rp2.000.000</td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">HM Master</span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-400">-</td>
                        <td class="px-4 py-3 text-center text-slate-400">-</td>
                        <td class="px-4 py-3 text-center font-semibold text-purple-700">Rp2.000.000</td>
                        <td class="px-4 py-3 text-center font-bold text-slate-800">Rp2.000.000</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 bg-amber-50">
            <div class="text-xs text-amber-800 space-y-1">
                <p><i class="fas fa-exclamation-triangle mr-1"></i> <strong>Aturan jenjang kosong:</strong></p>
                <ul class="ml-4 space-y-0.5">
                    <li>• Jika mitra <strong>tidak punya upline</strong> → hanya terima sesuai haknya, bagian upline tidak dialihkan ke siapa pun.</li>
                    <li>• Jika <strong>jenjang tengah kosong</strong> (misal: Member tidak punya Seller tapi punya Master) → bagian jenjang kosong dialihkan ke upline di atasnya.</li>
                    <li>• Jika mitra memiliki <strong>voucher diskon</strong> → fee mitra yang closing dipotong sesuai nilai voucher diskon.</li>
                </ul>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.inventaris.affiliate.hierarchy.settings.save') }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-semibold text-slate-800">Matriks Distribusi Fee</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Dari Level</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Ke Level</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600">Tipe Fee</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600">Nilai</th>
                            <th class="text-center px-4 py-3 font-semibold text-slate-600">Aktif</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-600">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($settings as $i => $setting)
                        @php $isFlat = ($setting->fee_type ?? 'percentage') === 'flat'; @endphp
                        <tr class="hover:bg-slate-50">
                            <input type="hidden" name="settings[{{ $i }}][from_level]" value="{{ $setting->from_level }}">
                            <input type="hidden" name="settings[{{ $i }}][to_level]" value="{{ $setting->to_level }}">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $setting->from_level === 'hm-seller' ? 'bg-blue-100 text-blue-700' :
                                       ($setting->from_level === 'hm-partner' ? 'bg-green-100 text-green-700' :
                                       ($setting->from_level === 'hm-leader' ? 'bg-indigo-100 text-indigo-700' : 'bg-purple-100 text-purple-700')) }}">
                                    {{ $setting->from_level_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $setting->to_level === 'hm-partner' ? 'bg-green-100 text-green-700' :
                                       ($setting->to_level === 'hm-leader' ? 'bg-indigo-100 text-indigo-700' : 'bg-purple-100 text-purple-700') }}">
                                    <i class="fas fa-arrow-up text-xs"></i>
                                    {{ $setting->to_level_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <select name="settings[{{ $i }}][fee_type]"
                                        onchange="toggleFeeInput(this, {{ $i }})"
                                        class="h-8 px-2 rounded-lg border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-primary-300">
                                    <option value="percentage" {{ !$isFlat ? 'selected' : '' }}>% Persen</option>
                                    <option value="flat"       {{ $isFlat  ? 'selected' : '' }}>Rp Nominal</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number"
                                           name="settings[{{ $i }}][fee_value]"
                                           id="fee-val-{{ $i }}"
                                           value="{{ $setting->fee_value ?? $setting->percentage }}"
                                           min="0"
                                           max="{{ $isFlat ? '' : '100' }}"
                                           step="{{ $isFlat ? '1000' : '0.5' }}"
                                           class="w-28 h-8 px-2 text-center rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                                    <span id="fee-unit-{{ $i }}" class="text-slate-400 text-xs">{{ $isFlat ? 'Rp' : '%' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" name="settings[{{ $i }}][is_active]" value="1"
                                       {{ $setting->is_active ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-primary-600">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="settings[{{ $i }}][notes]"
                                       value="{{ $setting->notes }}"
                                       placeholder="Catatan opsional..."
                                       class="w-full h-8 px-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-200 flex justify-end">
                <button type="submit" id="btn-settings-submit"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition">
                    <i class="fas fa-save"></i> Simpan Setting
                </button>
            </div>
        </div>
    </form>

    {{-- Simulasi --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i class="fas fa-calculator text-green-500"></i> Simulasi Distribusi Fee (Budget Rp2.000.000)
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Closing oleh</label>
                <select id="sim-level" onchange="runSimulation()"
                        class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <option value="hm-member">HM Member</option>
                    <option value="hm-seller">HM Seller</option>
                    <option value="hm-master">HM Master</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Pax</label>
                <input type="number" id="sim-pax" value="1" min="1" max="10"
                       oninput="runSimulation()"
                       class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 mt-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Ada Upline Seller?</label>
                <select id="sim-has-seller" onchange="runSimulation()"
                        class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Ada Upline Master?</label>
                <select id="sim-has-master" onchange="runSimulation()"
                        class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                </select>
            </div>
        </div>
        <div id="sim-result" class="mt-4 space-y-2"></div>
    </div>

</div>

<script>
// Toggle input saat tipe fee berubah
function toggleFeeInput(sel, idx) {
    const isFlat = sel.value === 'flat';
    const inp  = document.getElementById('fee-val-' + idx);
    const unit = document.getElementById('fee-unit-' + idx);
    inp.step = isFlat ? '1000' : '0.5';
    if (isFlat) { inp.removeAttribute('max'); }
    else { inp.setAttribute('max', '100'); }
    unit.textContent = isFlat ? 'Rp' : '%';
}

// Simulasi Budget Tetap Rp2.000.000
function runSimulation() {
    const level = document.getElementById('sim-level').value;
    const pax = parseInt(document.getElementById('sim-pax').value) || 1;
    const hasSeller = document.getElementById('sim-has-seller').value === '1';
    const hasMaster = document.getElementById('sim-has-master').value === '1';
    const result = document.getElementById('sim-result');
    const budget = 2000000;

    let closerShare = 0, sellerShare = 0, masterShare = 0;
    let closerLabel = '';

    if (level === 'hm-member') {
        closerShare = 500000;
        sellerShare = 500000;
        masterShare = 1000000;
        closerLabel = 'HM Member';

        // Aturan jenjang kosong
        if (!hasSeller && hasMaster) {
            masterShare += sellerShare; // 1.000K + 500K = 1.500K
            sellerShare = 0;
        } else if (!hasSeller && !hasMaster) {
            sellerShare = 0;
            masterShare = 0;
        } else if (hasSeller && !hasMaster) {
            masterShare = 0;
        }
    } else if (level === 'hm-seller') {
        closerShare = 1000000;
        sellerShare = 0;
        masterShare = 1000000;
        closerLabel = 'HM Seller';

        if (!hasMaster) {
            masterShare = 0;
        }
    } else if (level === 'hm-master') {
        closerShare = 2000000;
        sellerShare = 0;
        masterShare = 0;
        closerLabel = 'HM Master';
    }

    const fmt = n => 'Rp ' + (n * pax).toLocaleString('id-ID');
    const total = (closerShare + sellerShare + masterShare) * pax;

    let html = `<div class="p-3 bg-green-50 rounded-lg text-sm flex justify-between items-center">
        <span><i class="fas fa-user text-green-500 mr-1"></i> <strong>${closerLabel}</strong> (yang closing)</span>
        <span class="font-bold text-green-700">${fmt(closerShare)}</span>
    </div>`;

    if (level === 'hm-member' && hasSeller && sellerShare > 0) {
        html += `<div class="p-3 bg-blue-50 rounded-lg text-sm flex justify-between items-center">
            <span><i class="fas fa-arrow-up text-blue-400 mr-1"></i> HM Seller (upline)</span>
            <span class="font-semibold text-blue-700">${fmt(sellerShare)}</span>
        </div>`;
    }

    if (masterShare > 0 && ((level === 'hm-member' && hasMaster) || (level === 'hm-seller' && hasMaster))) {
        html += `<div class="p-3 bg-purple-50 rounded-lg text-sm flex justify-between items-center">
            <span><i class="fas fa-arrow-up text-purple-400 mr-1"></i> HM Master (upline)</span>
            <span class="font-semibold text-purple-700">${fmt(masterShare)}</span>
        </div>`;
    }

    // Bagian yang hilang (tidak dialihkan)
    const distributed = closerShare + sellerShare + masterShare;
    const lost = budget - distributed;
    if (lost > 0) {
        html += `<div class="p-3 bg-red-50 rounded-lg text-sm flex justify-between items-center">
            <span><i class="fas fa-times-circle text-red-400 mr-1"></i> Tidak dialihkan (upline tidak ada)</span>
            <span class="font-semibold text-red-500">${fmt(lost)}</span>
        </div>`;
    }

    html += `<div class="p-3 bg-slate-100 rounded-lg text-sm flex justify-between items-center border-t-2 border-slate-300 mt-2">
        <span class="font-semibold">Total Terdistribusi (${pax} pax)</span>
        <span class="font-bold text-slate-800">Rp ${total.toLocaleString('id-ID')}</span>
    </div>`;

    result.innerHTML = html;
}

runSimulation();
</script>

<script>
// Loading indicator untuk form settings
document.querySelector('form')?.addEventListener('submit', function() {
    const btn = document.getElementById('btn-settings-submit');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:6px"><svg style="animation:spin 0.8s linear infinite;width:14px;height:14px" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/></svg>Menyimpan...</span>';
    }
});
</script>

</x-layouts.admin>
