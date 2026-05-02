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
        Fee bisa berupa <strong>persentase (%)</strong> dari komisi dasar, atau <strong>nominal tetap (Rp)</strong>.
        Fee dibagi 2 termin: 50% saat pelunasan, 50% saat keberangkatan.
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
            <i class="fas fa-calculator text-green-500"></i> Simulasi Distribusi Fee
        </h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Komisi Dasar (Rp)</label>
                <input type="number" id="sim-base" value="1000000" min="0"
                       oninput="runSimulation()"
                       class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Level Mitra</label>
                <select id="sim-level" onchange="runSimulation()"
                        class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <option value="hm-seller">HM Seller</option>
                    <option value="hm-partner">HM Partner</option>
                    <option value="hm-leader">HM Leader</option>
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

// Simulasi
const settingsMatrix = @json($settings->groupBy('from_level')->map(fn($g) => $g->keyBy('to_level')->map(fn($s) => [
    'fee_type'  => $s->fee_type ?? 'percentage',
    'fee_value' => (float)($s->fee_value ?? $s->percentage),
    'active'    => $s->is_active,
])));

function runSimulation() {
    const base  = parseFloat(document.getElementById('sim-base').value) || 0;
    const level = document.getElementById('sim-level').value;
    const result = document.getElementById('sim-result');
    const levelSettings = settingsMatrix[level] || {};

    let html = `<div class="p-3 bg-green-50 rounded-lg text-sm">
        <strong>Mitra (${level.replace('hm-', 'HM ')}):</strong> Rp ${base.toLocaleString('id-ID')} (100%)
    </div>`;

    let remaining = base;
    for (const [toLevel, data] of Object.entries(levelSettings)) {
        if (!data.active) continue;
        const fee = data.fee_type === 'flat'
            ? data.fee_value
            : base * data.fee_value / 100;
        remaining -= fee;
        const t1 = fee * 0.5;
        const t2 = fee * 0.5;
        const label = data.fee_type === 'flat'
            ? `Rp ${data.fee_value.toLocaleString('id-ID')} (nominal)`
            : `${data.fee_value}%`;
        html += `<div class="p-3 bg-blue-50 rounded-lg text-sm flex justify-between items-center">
            <span><i class="fas fa-arrow-up text-blue-400 mr-1"></i> ${toLevel.replace('hm-', 'HM ')} (${label})</span>
            <span class="font-semibold text-blue-700">Rp ${fee.toLocaleString('id-ID')}
                <span class="text-xs text-blue-400">(T1: ${t1.toLocaleString('id-ID')} | T2: ${t2.toLocaleString('id-ID')})</span>
            </span>
        </div>`;
    }
    html += `<div class="p-3 bg-slate-50 rounded-lg text-sm flex justify-between items-center border-t-2 border-slate-300">
        <span class="font-semibold">Sisa untuk Mitra</span>
        <span class="font-bold text-slate-800">Rp ${remaining.toLocaleString('id-ID')}</span>
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
