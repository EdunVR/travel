<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Investor / Profil']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Investor / Profil')]); ?>
  <div x-data="profilInvestor()" x-init="init()" class="space-y-4 overflow-x-hidden">

    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold">Profil Investor</h1>
        <p class="text-slate-600 text-sm">Kelola data investor & perhitungan bagi hasil (frontend only).</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <div class="relative">
          <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400'></i>
          <input x-model="q" placeholder="Cari nama, kategori, bank…" class="w-64 pl-10 pr-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary-200">
        </div>
        <button @click="openForm()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah
        </button>
        <button @click="exportJson()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export
        </button>
        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50 cursor-pointer">
          <i class='bx bx-import text-lg'></i> Import
          <input type="file" class="hidden" accept="application/json" @change="importJson($event)">
        </label>
      </div>
    </div>

    <!-- Toolbar Filter + Sort -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
      <select x-model="kategori" class="rounded-xl border border-slate-200 px-3 py-2">
        <option value="ALL">Kategori: Semua</option>
        <template x-for="k in daftarKategori()" :key="k"><option :value="k" x-text="k"></option></template>
      </select>
      <select x-model="bank" class="rounded-xl border border-slate-200 px-3 py-2">
        <option value="ALL">Bank: Semua</option>
        <template x-for="b in daftarBank()" :key="b"><option :value="b" x-text="b"></option></template>
      </select>
      <select x-model="sortKey" class="rounded-xl border border-slate-200 px-3 py-2">
        <option value="join_at">Tanggal Bergabung</option>
        <option value="name">Nama</option>
        <option value="category">Kategori</option>
        <option value="investment_total">Total Investasi</option>
      </select>
      <select x-model="sortDir" class="rounded-xl border border-slate-200 px-3 py-2">
        <option value="desc">Terbaru</option>
        <option value="asc">Terlama</option>
      </select>
    </div>

    <!-- ACTION BAR (muncul hanya saat ada pilihan) -->
    <div x-show="selected()" class="rounded-2xl border border-slate-200 bg-white px-3 sm:px-4 py-2.5 shadow-card flex flex-wrap items-center gap-2">
      <div class="text-sm">
        Terpilih: <span class="font-semibold" x-text="selected()?.name"></span>
        <span class="text-slate-500">•</span>
        <span class="text-xs text-slate-500" x-text="formatDate(selected()?.join_at)"></span>
      </div>
      <span class="hidden sm:block h-4 w-px bg-slate-200"></span>
      <div class="flex gap-2">
        <button @click="openView(selected())" class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50 text-sm"><i class='bx bx-show'></i> View</button>
        <button @click="edit(selected())" class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50 text-sm"><i class='bx bx-edit'></i> Edit</button>
        <button @click="askDelete(selected())" class="rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50 text-sm"><i class='bx bx-trash'></i> Delete</button>
      </div>
      <button @click="clearSel()" class="ml-auto text-slate-500 hover:text-slate-700 text-sm">Clear</button>
    </div>

    <!-- DESKTOP TABLE -->
    <div class="hidden xl:block rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-700">
          <tr>
            <th class="px-4 py-3 text-left w-12">Pilih</th>
            <th class="px-2 py-3 text-left">Tanggal Bergabung</th>
            <th class="px-2 py-3 text-left">Nama Investor</th>
            <th class="px-2 py-3 text-left">Kategori</th>
            <th class="px-2 py-3 text-left">Rekening Investasi</th>
            <th class="px-2 py-3 text-left">Total Investasi</th>
            <th class="px-2 py-3 text-left">Bank</th>
            <th class="px-2 py-3 text-left">Rekening</th>
            <th class="px-2 py-3 text-left">Atas Nama</th>
            <th class="px-2 py-3 text-left">Persentase</th>
            <th class="px-2 py-3 text-left">Total Bagi Hasil</th>
            <th class="px-2 py-3 text-left">Total Keseluruhan</th>
            <th class="px-2 py-3 text-left">Transfer ke Investor</th>
            <th class="px-2 py-3 text-left">Keuntungan Pengelola</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="inv in filtered()" :key="inv.id">
            <tr class="border-t border-slate-100 cursor-pointer"
                :class="selectedId===inv.id ? 'bg-primary-50/40' : 'bg-white hover:bg-slate-50'"
                @click="toggleSel(inv)">
              <td class="px-4 py-3">
                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border"
                      :class="selectedId===inv.id ? 'border-primary-500 bg-primary-500' : 'border-slate-300 bg-white'">
                  <i class="bx bx-check text-white text-[14px]" x-show="selectedId===inv.id"></i>
                </span>
              </td>
              <td class="px-2 py-3" x-text="formatDate(inv.join_at)"></td>
              <td class="px-2 py-3 font-medium truncate" x-text="inv.name"></td>
              <td class="px-2 py-3" x-text="inv.category"></td>
              <td class="px-2 py-3 font-mono" x-text="inv.account_code || '-'"></td>
              <td class="px-2 py-3" x-text="rupiah(inv.investment_total)"></td>
              <td class="px-2 py-3" x-text="inv.bank"></td>
              <td class="px-2 py-3 font-mono" x-text="inv.account_number"></td>
              <td class="px-2 py-3" x-text="inv.account_name"></td>
              <td class="px-2 py-3" x-text="inv.share_percent + '%'"></td>
              <td class="px-2 py-3" x-text="rupiah(calcShare(inv))"></td>
              <td class="px-2 py-3" x-text="rupiah(calcTotal(inv))"></td>
              <td class="px-2 py-3" x-text="rupiah(inv.transfer_to_investor || 0)"></td>
              <td class="px-2 py-3" x-text="rupiah(calcManager(inv))"></td>
            </tr>
          </template>
          <tr x-show="filtered().length===0">
            <td colspan="14" class="px-4 py-8 text-center text-slate-500">Belum ada data / tidak ditemukan.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MOBILE CARDS -->
    <div class="xl:hidden grid grid-cols-1 gap-3">
      <template x-for="inv in filtered()" :key="inv.id">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card"
             :class="selectedId===inv.id ? 'ring-2 ring-primary-200' : ''">
          <div class="flex items-center justify-between gap-3">
            <button class="shrink-0 inline-flex h-6 w-6 items-center justify-center rounded-full border"
                    :class="selectedId===inv.id ? 'border-primary-500 bg-primary-500' : 'border-slate-300 bg-white'"
                    @click.stop="toggleSel(inv)">
              <i class="bx bx-check text-white text-[16px]" x-show="selectedId===inv.id"></i>
            </button>
            <div class="flex-1 min-w-0">
              <div class="font-semibold truncate" x-text="inv.name"></div>
              <div class="text-xs text-slate-500" x-text="formatDate(inv.join_at) + ' • ' + inv.category"></div>
            </div>
            <span class="text-xs px-2 py-1 rounded-full bg-primary-50 text-primary-700 border border-primary-200"
                  x-text="rupiah(inv.investment_total)"></span>
          </div>

          <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
            <div class="rounded-lg border border-slate-200 p-2">
              <div class="text-xs text-slate-500">Bagi Hasil</div>
              <div class="font-medium" x-text="rupiah(calcShare(inv)) + ' (' + inv.share_percent + '%)'"></div>
            </div>
            <div class="rounded-lg border border-slate-200 p-2">
              <div class="text-xs text-slate-500">Total</div>
              <div class="font-medium" x-text="rupiah(calcTotal(inv))"></div>
            </div>
          </div>
        </div>
      </template>
      <div x-show="filtered().length===0" class="text-center text-slate-500 py-8">Belum ada data / tidak ditemukan.</div>
    </div>

    <!-- Modal VIEW -->
    <div x-show="showView" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div @click.outside="showView=false" class="w-full max-w-3xl bg-white rounded-2xl shadow-float overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold">Detail Investor</div>
          <button @click="showView=false" class="p-2 -m-2 hover:bg-slate-100 rounded-lg"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
          <template x-if="viewData">
            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Nama</div><div class="font-medium" x-text="viewData.name"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Tanggal Bergabung</div><div class="font-medium" x-text="formatDate(viewData.join_at)"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Kategori</div><div class="font-medium" x-text="viewData.category"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Bank</div><div class="font-medium" x-text="viewData.bank"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Rekening</div><div class="font-medium font-mono" x-text="viewData.account_number"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Atas Nama</div><div class="font-medium" x-text="viewData.account_name"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Total Investasi</div><div class="font-medium" x-text="rupiah(viewData.investment_total)"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Persentase</div><div class="font-medium" x-text="viewData.share_percent + '%' "></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Total Bagi Hasil</div><div class="font-medium" x-text="rupiah(calcShare(viewData))"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Total Keseluruhan</div><div class="font-medium" x-text="rupiah(calcTotal(viewData))"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Transfer ke Investor</div><div class="font-medium" x-text="rupiah(viewData.transfer_to_investor||0)"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Keuntungan Pengelola</div><div class="font-medium" x-text="rupiah(calcManager(viewData))"></div>
              </div>
            </div>
          </template>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" @click="showView=false">Tutup</button>
          <button class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700" @click="edit(viewData)">Edit</button>
        </div>
      </div>
    </div>

    <!-- Modal FORM (Add/Edit) -->
    <div x-show="showForm" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div @click.outside="closeForm()" class="w-full max-w-4xl bg-white rounded-2xl shadow-float overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold" x-text="form.id ? 'Edit Investor' : 'Tambah Investor'"></div>
          <button @click="closeForm()" class="p-2 -m-2 hover:bg-slate-100 rounded-lg"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div><label class="text-sm text-slate-600">Tanggal Bergabung</label>
              <input type="date" x-model="form.join_at" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            <div><label class="text-sm text-slate-600">Nama Investor</label>
              <input type="text" x-model.trim="form.name" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            <div><label class="text-sm text-slate-600">Kategori</label>
              <input type="text" x-model.trim="form.category" placeholder="Perorangan / Perusahaan" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            <div><label class="text-sm text-slate-600">Rekening Investasi (Kode/No)</label>
              <input type="text" x-model.trim="form.account_code" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            <div><label class="text-sm text-slate-600">Total Investasi</label>
              <input type="number" min="0" x-model.number="form.investment_total" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
              <p class="text-xs text-blue-600 mt-1" x-show="form.investment_total > 0" 
                 x-text="'≈ ' + formatCurrency(form.investment_total)"></p>
            </div>
            <div><label class="text-sm text-slate-600">Bank</label>
              <input type="text" x-model.trim="form.bank" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            <div><label class="text-sm text-slate-600">Rekening</label>
              <input type="text" x-model.trim="form.account_number" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            <div><label class="text-sm text-slate-600">Atas Nama</label>
              <input type="text" x-model.trim="form.account_name" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            <div><label class="text-sm text-slate-600">Persentase Bagi Hasil (%)</label>
              <input type="number" min="0" max="100" step="0.01" x-model.number="form.share_percent" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>
            <div><label class="text-sm text-slate-600">Transfer ke Investor (Rp)</label>
              <input type="number" min="0" x-model.number="form.transfer_to_investor" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2"></div>

            <div class="sm:col-span-2">
              <div class="rounded-xl border border-slate-200 p-3 bg-slate-50">
                <div class="text-sm text-slate-600 mb-2">Ringkasan Perhitungan</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                  <div class="rounded-lg border border-slate-200 p-2 bg-white">
                    <div class="text-xs text-slate-500">Total Bagi Hasil</div>
                    <div class="font-medium" x-text="rupiah(calcShare(form))"></div>
                  </div>
                  <div class="rounded-lg border border-slate-200 p-2 bg-white">
                    <div class="text-xs text-slate-500">Total Keseluruhan</div>
                    <div class="font-medium" x-text="rupiah(calcTotal(form))"></div>
                  </div>
                  <div class="rounded-lg border border-slate-200 p-2 bg-white">
                    <div class="text-xs text-slate-500">Keuntungan Pengelola</div>
                    <div class="font-medium" x-text="rupiah(calcManager(form))"></div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button @click="closeForm()" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
          <button @click="save()" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">Simpan</button>
        </div>
      </div>
    </div>

    <!-- Modal DELETE -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 p-3">
      <div @click.outside="toDelete=null" class="w-full max-w-md bg-white rounded-2xl shadow-float overflow-hidden">
        <div class="px-5 py-4">
          <div class="font-semibold">Hapus Investor?</div>
          <p class="text-slate-600 text-sm mt-1">Data akan dihapus dari localStorage.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="font-medium" x-text="toDelete?.name"></div>
            <div class="text-xs text-slate-500 mt-1" x-text="formatDate(toDelete?.join_at) + ' • ' + toDelete?.category"></div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50" @click="toDelete=null">Batal</button>
          <button class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700" @click="doDelete()">Hapus</button>
        </div>
      </div>
    </div>

  </div>

  <script>
    function profilInvestor(){
      return {
        STORAGE:'erp_investor_profiles_v1',
        items:[],
        q:'', kategori:'ALL', bank:'ALL', sortKey:'join_at', sortDir:'desc',
        showForm:false, form:{}, toDelete:null,
        showView:false, viewData:null,
        selectedId:null,

        // ===== INIT & PERSIST =====
        init(){
          const raw = localStorage.getItem(this.STORAGE);
          if(raw){ try{ this.items = JSON.parse(raw)||[] }catch{ this.items=[] } }
          if(!this.items.length){ this.seed(); }
          // normalisasi angka
          this.items = this.items.map(it=>({
            ...it,
            investment_total:Number(it.investment_total||0),
            share_percent:Number(it.share_percent||0),
            transfer_to_investor:Number(it.transfer_to_investor||0),
          }));
          this.persist();
        },
        seed(){
          this.items = [
            { id:1, join_at:'2024-01-12', name:'PT Nusantara Abadi', category:'Perusahaan', account_code:'INV-001', investment_total:250000000, bank:'BCA', account_number:'1234567890', account_name:'PT Nusantara Abadi', share_percent:12.5, transfer_to_investor:20000000 },
            { id:2, join_at:'2024-03-03', name:'Andi Pratama',       category:'Perorangan', account_code:'INV-002', investment_total:100000000, bank:'BRI', account_number:'9876543210', account_name:'Andi Pratama',       share_percent:10,   transfer_to_investor:7500000  },
          ];
          this.persist();
        },
        persist(){ localStorage.setItem(this.STORAGE, JSON.stringify(this.items)); },

        // ===== SELECT =====
        toggleSel(inv){ this.selectedId = (this.selectedId===inv.id ? null : inv.id); },
        clearSel(){ this.selectedId=null; },
        selected(){ return this.items.find(i=>i.id===this.selectedId) || null; },

        // ===== FILTERING & SORT =====
        daftarKategori(){ return [...new Set(this.items.map(i=>i.category).filter(Boolean))].sort(); },
        daftarBank(){ return [...new Set(this.items.map(i=>i.bank).filter(Boolean))].sort(); },
        filtered(){
          const q=this.q.toLowerCase();
          const list=this.items.filter(i=>{
            const mQ=!q || [i.name,i.category,i.bank,i.account_number,i.account_name,i.account_code].join(' ').toLowerCase().includes(q);
            const mK=this.kategori==='ALL'||i.category===this.kategori;
            const mB=this.bank==='ALL'||i.bank===this.bank;
            return mQ&&mK&&mB;
          });
          list.sort((a,b)=>{
            let A=a[this.sortKey], B=b[this.sortKey];
            if(this.sortKey==='join_at'){ A=new Date(A).getTime(); B=new Date(B).getTime(); }
            if(this.sortKey==='investment_total'){ A=Number(A); B=Number(B); }
            const c=A>B?1:A<B?-1:0; return this.sortDir==='asc'?c:-c;
          });
          return list;
        },

        // ===== CRUD =====
        openForm(){ this.form={ id:null, join_at:new Date().toISOString().slice(0,10), name:'', category:'', account_code:'', investment_total:0, bank:'', account_number:'', account_name:'', share_percent:0, transfer_to_investor:0 }; this.showForm=true; },
        edit(inv){ this.form=JSON.parse(JSON.stringify(inv)); this.showForm=true; },
        closeForm(){ this.showForm=false; },
        save(){
          if(!this.form.name||!this.form.join_at){ alert('Nama & Tanggal Bergabung wajib.'); return; }
          this.form.investment_total=Number(this.form.investment_total||0);
          this.form.share_percent=Number(this.form.share_percent||0);
          this.form.transfer_to_investor=Number(this.form.transfer_to_investor||0);
          if(this.form.id){ const i=this.items.findIndex(x=>x.id===this.form.id); if(i>-1) this.items.splice(i,1,this.form); }
          else { this.form.id=Date.now(); this.items.unshift(this.form); }
          this.persist(); this.showForm=false;
        },
        askDelete(inv){ this.toDelete=inv; },
        doDelete(){ if(!this.toDelete) return; this.items=this.items.filter(x=>x.id!==this.toDelete.id); if(this.selectedId===this.toDelete.id) this.selectedId=null; this.persist(); this.toDelete=null; },

        // ===== VIEW =====
        openView(inv){ this.viewData=inv; this.showView=true; },

        // ===== Helpers =====
        formatDate(s){ if(!s) return '-'; const d=new Date(s); return d.toLocaleDateString('id-ID',{year:'numeric',month:'short',day:'2-digit'}); },
        rupiah(v){ try{v=Number(v||0)}catch{} return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(v); },
        calcShare(it){ const total=Number(it?.investment_total||0); const p=Number(it?.share_percent||0)/100; return Math.round(total*p); },
        calcTotal(it){ return Number(it?.investment_total||0) + this.calcShare(it); },
        calcManager(it){ return Math.max(0, this.calcShare(it) - Number(it?.transfer_to_investor||0)); },
      }
    }
  </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $attributes = $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3)): ?>
<?php $component = $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3; ?>
<?php unset($__componentOriginalc8c9fd5d7827a77a31381de67195f0c3); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\investor\profil\index.blade.php ENDPATH**/ ?>