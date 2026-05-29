<x-layouts.admin :title="'Pohon Jenjang Mitra'">
<div class="flex flex-col" style="height: calc(100vh - 64px)">

    {{-- Header --}}
<div class="flex items-center justify-between px-6 py-3 bg-white border-b border-slate-200 flex-shrink-0">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Pohon Jenjang Mitra</h1>
            <p class="text-slate-400 text-xs mt-0.5">Hover untuk highlight · Klik node/garis untuk aksi</p>
        </div>
        <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
        <div class="flex gap-2">
            <div class="flex gap-3 text-xs text-slate-500 items-center mr-4">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-purple-500 inline-block"></span>Master</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-indigo-500 inline-block"></span>Leader</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>Partner</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>Seller</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span>Member</span>
                <span class="flex items-center gap-1.5 ml-2"><span class="w-5 h-0.5 bg-green-500 inline-block"></span>Ada fee</span>
                <span class="flex items-center gap-1.5"><span class="w-5 border-t-2 border-dashed border-slate-300 inline-block"></span>Belum ada fee</span>
            </div>
            <a href="{{ route('admin.inventaris.affiliate.hierarchy.settings') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-700 hover:bg-amber-100 transition">
                <i class="fas fa-cog"></i> Setting Fee Global
            </a>
            <a href="{{ route('admin.inventaris.affiliate.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-xs text-slate-600 hover:bg-slate-200 transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- SVG Canvas — aspect-ratio diset otomatis oleh JS --}}
    <div class="flex-1 relative bg-slate-50" style="min-height:0; overflow:hidden">
        <div id="tree-loading" class="absolute inset-0 flex items-center justify-center z-10 bg-slate-50">
            <div class="text-center text-slate-400">
                <svg class="animate-spin w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <p class="text-sm">Memuat pohon jenjang...</p>
            </div>
        </div>
        <div id="tree-empty" class="hidden absolute inset-0 flex items-center justify-center">
            <div class="text-center text-slate-300">
                <i class="fas fa-sitemap text-5xl mb-3 block"></i>
                <p class="text-sm">Belum ada mitra dengan jenjang terdaftar</p>
            </div>
        </div>
        <svg id="tree-svg" style="width:100%;height:100%;display:block"></svg>
    </div>

    {{-- Fee Settings Panel — selalu tampil di bawah tree --}}
    <div class="flex-shrink-0 bg-white border-t border-slate-200 px-6 py-3">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                <i class="fas fa-percent text-amber-500"></i> Setting Fee Jenjang
                <span class="text-slate-400 font-normal ml-1">(klik ✏ untuk ubah)</span>
            </h3>
            <a href="{{ route('admin.inventaris.affiliate.hierarchy.settings') }}"
               class="text-xs text-amber-600 hover:underline">Lihat semua →</a>
        </div>
        <div id="fee-panel-body" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
            <p class="text-xs text-slate-400 italic col-span-4">Memuat...</p>
        </div>
    </div>
</div>

{{-- ═══ MODALS ═══ --}}

{{-- Modal Edit Jenjang --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Edit Jenjang</h3>
                <p class="text-xs text-slate-400 mt-0.5" id="edit-modal-subtitle"></p>
            </div>
            <button onclick="closeModal('modal-edit')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 transition">✕</button>
        </div>
        <form id="edit-form" method="POST" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cabang</label>
                <select name="upline_master_id" id="edit-master"
                    class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">— Pusat —</option>
                    @foreach(\App\Models\Affiliator::active()->whereHas('partnershipProgram', fn($q) => $q->where('slug','hm-master'))->get() as $m)
                    <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Leader</label>
                <select name="upline_leader_id" id="edit-leader"
                    class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">— Tidak Ada —</option>
                    @foreach(\App\Models\Affiliator::active()->whereHas('partnershipProgram', fn($q) => $q->where('slug','hm-leader'))->get() as $l)
                    <option value="{{ $l->id }}">{{ $l->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Partner</label>
                <select name="upline_partner_id" id="edit-partner"
                    class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="">— Tidak Ada —</option>
                    @foreach(\App\Models\Affiliator::active()->whereHas('partnershipProgram', fn($q) => $q->where('slug','hm-partner'))->get() as $p)
                    <option value="{{ $p->id }}">{{ $p->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit')"
                    class="flex-1 h-10 border border-slate-200 text-slate-600 text-sm rounded-xl hover:bg-slate-50 transition">Batal</button>
                <button type="submit" id="btn-edit-submit"
                    class="flex-1 h-10 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-xl transition font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Downline --}}
<div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Tambah Downline</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pilih mitra yang akan menjadi downline dari <strong id="add-parent-name"></strong></p>
            </div>
            <button onclick="closeModal('modal-add')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 transition">✕</button>
        </div>
        <div class="p-6 space-y-4">
            <select id="add-child-select"
                class="w-full h-10 px-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                <option value="">— Pilih Mitra —</option>
            </select>
            <div class="flex gap-3">
                <button onclick="closeModal('modal-add')"
                    class="flex-1 h-10 border border-slate-200 text-slate-600 text-sm rounded-xl hover:bg-slate-50 transition">Batal</button>
                <button onclick="confirmAddBelow()" id="btn-add-submit"
                    class="flex-1 h-10 bg-green-600 hover:bg-green-700 text-white text-sm rounded-xl transition font-semibold">Tambahkan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="modal-delete" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="p-6 text-center">
            <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-unlink text-amber-500 text-xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">Hapus dari Jenjang?</h3>
            <p class="text-sm text-slate-500 mb-6">Mitra <strong id="delete-name"></strong> akan dilepas dari semua jenjang upline. Data mitra tetap ada, hanya posisi jenjangnya yang dihapus.</p>
            <div class="flex gap-3">
                <button onclick="closeModal('modal-delete')"
                    class="flex-1 h-10 border border-slate-200 text-slate-600 text-sm rounded-xl hover:bg-slate-50 transition">Batal</button>
                <button onclick="confirmDelete()" id="btn-delete-submit"
                    class="flex-1 h-10 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-xl transition font-semibold">Hapus dari Jenjang</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/affiliate-tree.js') }}?v={{ time() }}"></script>
<script>
const CSRF      = '{{ csrf_token() }}';
const DATA_URL  = '{{ route("admin.inventaris.affiliate.hierarchy.tree.data") }}';
const FEE_URL   = '{{ route("admin.inventaris.affiliate.hierarchy.fee.save") }}';
const VIEW_BASE = '{{ url("admin/inventaris/affiliate") }}';
const UPD_BASE  = '{{ url("admin/inventaris/affiliate") }}';

let treeData    = { nodes: [], edges: [] };
let pendingNode = null;
let deleteNode  = null;

// ─── Loading helper ───────────────────────────────────────────────────────────
function setLoading(btnId, loading) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    if (loading) {
        btn._origText = btn.innerHTML;
        btn.disabled  = true;
        btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:6px"><svg style="animation:spin 0.8s linear infinite;width:14px;height:14px" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4" stroke-dashoffset="10"/></svg> Menyimpan...</span>';
    } else {
        btn.disabled  = false;
        btn.innerHTML = btn._origText || 'Simpan';
    }
}

// ─── Load & render ────────────────────────────────────────────────────────────
async function loadTree() {
    document.getElementById('tree-loading').classList.remove('hidden');
    const res = await fetch(DATA_URL);
    treeData  = await res.json();
    window._affTreeData = treeData; // expose untuk update lokal
    document.getElementById('tree-loading').classList.add('hidden');

    if (!treeData.nodes.length) {
        document.getElementById('tree-empty').classList.remove('hidden');
        return;
    }

    requestAnimationFrame(() => {
        const svg = document.getElementById('tree-svg');
        window.affTree = new AffiliateTree(svg, treeData.nodes, treeData.edges, {
            isAdmin:    true,
            csrfToken:  CSRF,
            feeUrl:     FEE_URL,
            viewBase:   VIEW_BASE,
            updateBase: UPD_BASE,
        });
        window.affTree.render();
        window._affTree = window.affTree; // sync reference untuk popup
    });
}

// ─── Modal helpers ────────────────────────────────────────────────────────────
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// Close on backdrop click
document.querySelectorAll('[id^="modal-"]').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

// ─── Edit Jenjang ─────────────────────────────────────────────────────────────
document.addEventListener('affTree:editNode', ({ detail: { node } }) => {
    document.getElementById('edit-modal-subtitle').textContent = node.name;
    const form = document.getElementById('edit-form');
    form.action = `${UPD_BASE}/${node.id}/update-hierarchy`;

    // Pre-select current values from treeData
    const n = treeData.nodes.find(x => x.id === node.id);
    document.getElementById('edit-master').value  = n?.upline_master_id  || '';
    document.getElementById('edit-leader').value  = n?.upline_leader_id  || '';
    document.getElementById('edit-partner').value = n?.upline_partner_id || '';

    document.getElementById('modal-edit').classList.remove('hidden');
});

document.getElementById('edit-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    setLoading('btn-edit-submit', true);
    const fd = new FormData(this);
    const res = await fetch(this.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: fd,
    });
    setLoading('btn-edit-submit', false);
    closeModal('modal-edit');
    loadTree();
});

// ─── Tambah Downline ──────────────────────────────────────────────────────────
document.addEventListener('affTree:addBelow', ({ detail: { node } }) => {
    pendingNode = node;
    document.getElementById('add-parent-name').textContent = node.name;
    const sel = document.getElementById('add-child-select');
    sel.innerHTML = '<option value="">— Pilih Mitra —</option>';
    treeData.nodes
        .filter(n => n.id !== node.id)
        .forEach(n => {
            const opt = document.createElement('option');
            opt.value = n.id;
            opt.textContent = `${n.name} (${n.program})`;
            sel.appendChild(opt);
        });
    document.getElementById('modal-add').classList.remove('hidden');
});

async function confirmAddBelow() {
    const childId = document.getElementById('add-child-select').value;
    if (!childId || !pendingNode) return;
    const slug = pendingNode.slug;

    const body = {};
    if (slug === 'hm-master')  body.upline_master_id  = pendingNode.id;
    if (slug === 'hm-leader')  body.upline_leader_id  = pendingNode.id;
    if (slug === 'hm-partner') body.upline_partner_id = pendingNode.id;

    if (Object.keys(body).length === 0) {
        alert('HM Seller tidak bisa memiliki downline dalam sistem ini.');
        return;
    }

    setLoading('btn-add-submit', true);
    const fd = new FormData();
    Object.entries(body).forEach(([k, v]) => fd.append(k, v));
    fd.append('_token', CSRF);
    fd.append('_method', 'PATCH');
    const res = await fetch(`${UPD_BASE}/${childId}/update-hierarchy`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: fd,
    });

    let resData;
    try { resData = await res.json(); } catch { resData = null; }
    setLoading('btn-add-submit', false);

    if (res.ok || (resData && resData.success)) {
        closeModal('modal-add');
        loadTree();
    } else {
        const msg = resData?.message || ('Status: ' + res.status);
        console.error('Add downline error:', res.status, resData);
        alert('Gagal menambahkan downline: ' + msg);
    }
}

// ─── Hapus dari Jenjang (bukan hapus mitra dari DB) ───────────────────────────
document.addEventListener('affTree:deleteNode', ({ detail: { node } }) => {
    deleteNode = node;
    document.getElementById('delete-name').textContent = node.name;
    document.getElementById('modal-delete').classList.remove('hidden');
});

async function confirmDelete() {
    if (!deleteNode) return;
    setLoading('btn-delete-submit', true);
    const fd = new FormData();
    fd.append('_token', CSRF);
    fd.append('_method', 'PATCH');
    fd.append('upline_master_id',  '');
    fd.append('upline_leader_id',  '');
    fd.append('upline_partner_id', '');
    await fetch(`${UPD_BASE}/${deleteNode.id}/update-hierarchy`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
        body: fd,
    });
    setLoading('btn-delete-submit', false);
    closeModal('modal-delete');
    loadTree();
}

// ─── Reload on fee save ───────────────────────────────────────────────────────
document.addEventListener('affTree:reload', loadTree);

// ─── Fee Settings Panel ───────────────────────────────────────────────────────
async function loadFeePanel() {
    const res  = await fetch(DATA_URL);
    const data = await res.json();
    const matrix = data.matrix || {};
    const panel  = document.getElementById('fee-panel-body');
    if (!panel) return;

    const levelLabel = s => s.replace('hm-', 'HM ').replace('-', ' ');
    const levelColor = {
        'hm-member':  'bg-gray-100 text-gray-700',
        'hm-seller':  'bg-blue-100 text-blue-700',
        'hm-partner': 'bg-green-100 text-green-700',
        'hm-leader':  'bg-indigo-100 text-indigo-700',
        'hm-master':  'bg-purple-100 text-purple-700',
    };

    let html = '';
    for (const [fromLevel, targets] of Object.entries(matrix)) {
        for (const [toLevel, setting] of Object.entries(targets)) {
            const feeType  = setting.fee_type  || 'percentage';
            const feeValue = setting.fee_value || setting.percentage || 0;
            const label    = feeType === 'flat'
                ? `Rp ${new Intl.NumberFormat('id-ID').format(feeValue)}`
                : `${feeValue}%`;
            html += `<div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-100">
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold ${levelColor[fromLevel] || 'bg-gray-100 text-gray-600'}">${levelLabel(fromLevel)}</span>
                <i class="fas fa-arrow-right text-slate-300 text-xs"></i>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold ${levelColor[toLevel] || 'bg-gray-100 text-gray-600'}">${levelLabel(toLevel)}</span>
                <span class="ml-auto font-bold text-sm ${feeValue > 0 ? 'text-green-700' : 'text-slate-400'}">${feeValue > 0 ? label : '—'}</span>
                <button onclick="openFeeEdit('${fromLevel}','${toLevel}','${feeType}',${feeValue})"
                    class="text-xs px-2 py-1 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition">
                    <i class="fas fa-edit"></i>
                </button>
            </div>`;
        }
    }
    panel.innerHTML = html || '<p class="text-xs text-slate-400 italic text-center py-3">Belum ada setting fee</p>';
}

function openFeeEdit(fromLevel, toLevel, feeType, feeValue) {
    // Buat virtual edge untuk popup
    const virtualEdge = {
        from: 0, to: 0,
        from_level: fromLevel,
        to_level:   toLevel,
        fee_type:   feeType,
        fee_value:  feeValue,
        percentage: feeType === 'percentage' ? feeValue : 0,
        has_fee:    false,
        fee_total:  0,
    };
    if (window._affTree) {
        // Posisikan popup di tengah layar
        const sx = window.innerWidth / 2;
        const sy = window.innerHeight / 2;
        window._affTree._showEdgePopup(virtualEdge, sx, sy);
    }
}

// ─── Init ─────────────────────────────────────────────────────────────────────
loadTree();
loadFeePanel();

// Reload fee panel setelah save
document.addEventListener('affTree:reload', loadFeePanel);
</script>
</x-layouts.admin>
