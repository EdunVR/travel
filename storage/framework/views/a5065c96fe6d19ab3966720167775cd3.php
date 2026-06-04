
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Halaman Agen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Halaman Agen')]); ?>
  <div x-data="agenPage()" x-init="init()" class="space-y-6 overflow-x-hidden">

    
    <header class="space-y-2">
      <h1 class="text-2xl font-bold tracking-tight">Halaman Agen</h1>
      <p class="text-slate-600">Kelola data agen/gerobakan, monitoring penjualan & komisi (frontend only).</p>
    </header>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-slate-500 text-xs flex items-center gap-2">
          <i class='bx bx-group text-primary-600 text-lg'></i> Total Agen
        </div>
        <div class="mt-1 text-2xl font-bold" x-text="kpi.total"></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-slate-500 text-xs flex items-center gap-2">
          <i class='bx bx-badge-check text-emerald-600 text-lg'></i> Agen Aktif
        </div>
        <div class="mt-1 text-2xl font-bold" x-text="kpi.aktif"></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-slate-500 text-xs flex items-center gap-2">
          <i class='bx bx-line-chart text-primary-600 text-lg'></i> Omzet Bulan Ini
        </div>
        <div class="mt-1 text-2xl font-bold" x-text="idr(kpi.omzet)"></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-slate-500 text-xs flex items-center gap-2">
          <i class='bx bx-wallet text-amber-600 text-lg'></i> Komisi Terutang
        </div>
        <div class="mt-1 text-2xl font-bold" x-text="idr(kpi.komisi)"></div>
      </div>
    </section>

    
    <section class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-card overflow-visible">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
        <div class="lg:col-span-4">
          <label class="text-xs font-medium text-slate-500 mb-1 block">Pencarian</label>
          <div class="relative">
            <i class='bx bx-search absolute left-2 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model.debounce.300ms="filter.q" type="text" placeholder="Cari nama agen / WA / wilayah"
                   class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-8 pr-3">
          </div>
        </div>
        <div class="lg:col-span-3">
          <label class="text-xs font-medium text-slate-500 mb-1 block">Outlet</label>
          <select x-model="filter.outlet" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3">
            <option value="all">Semua Outlet</option>
            <option>Bumibraja</option>
            <option>Dahana</option>
          </select>
        </div>
        <div class="lg:col-span-3">
          <label class="text-xs font-medium text-slate-500 mb-1 block">Status</label>
          <select x-model="filter.status" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3">
            <option value="all">Semua</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>

        <div class="lg:col-span-2 flex flex-wrap gap-2">
          <button @click="openForm()"
                  class="inline-flex h-10 items-center gap-2 rounded-xl bg-primary-600 px-4 text-white hover:bg-primary-700">
            <i class='bx bx-plus'></i> Tambah Agen
          </button>
          <button @click="exportCSV()"
                  class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 hover:bg-slate-50">
            <i class='bx bx-export'></i> Export
          </button>
        </div>
      </div>
    </section>

    
    <section class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-card">
      <div class="overflow-x-auto">
        <table class="min-w-[1000px] w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-2 w-10"><input type="checkbox" @change="toggleAll($event)"></th>
              <th class="px-3 py-2 text-left">Kode</th>
              <th class="px-3 py-2 text-left">Nama Agen</th>
              <th class="px-3 py-2 text-left">Outlet</th>
              <th class="px-3 py-2 text-left">Wilayah</th>
              <th class="px-3 py-2 text-left">WA</th>
              <th class="px-3 py-2 text-right">Target/Bulan</th>
              <th class="px-3 py-2 text-right">Penjualan Bulan Ini</th>
              <th class="px-3 py-2 text-right">Komisi</th>
              <th class="px-3 py-2 text-right">Saldo Titipan</th>
              <th class="px-3 py-2 text-center">Status</th>
              <th class="px-3 py-2 text-center w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="rows.length === 0">
              <tr>
                <td class="px-3 py-6 text-center text-slate-500" colspan="12">Tidak ada data</td>
              </tr>
            </template>

            <template x-for="(r,i) in rows" :key="r.id">
              <tr class="border-t">
                <td class="px-3 py-2"><input type="checkbox" x-model="r._checked"></td>
                <td class="px-3 py-2 font-medium" x-text="r.kode"></td>
                <td class="px-3 py-2">
                  <div class="font-medium truncate" x-text="r.nama"></div>
                  <div class="text-xs text-slate-500" x-text="`Bergabung: ${tgl(r.gabung)}`"></div>
                </td>
                <td class="px-3 py-2" x-text="r.outlet"></td>
                <td class="px-3 py-2" x-text="r.wilayah"></td>
                <td class="px-3 py-2">
                  <a :href="`https://wa.me/${r.wa}`" target="_blank" class="text-primary-600 hover:underline" x-text="r.wa"></a>
                </td>
                <td class="px-3 py-2 text-right" x-text="idr(r.target)"></td>
                <td class="px-3 py-2 text-right" x-text="idr(r.penjualan)"></td>
                <td class="px-3 py-2 text-right" x-text="idr(r.komisi)"></td>
                <td class="px-3 py-2 text-right" x-text="idr(r.titipan)"></td>
                <td class="px-3 py-2 text-center">
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs"
                        :class="r.status==='aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                        x-text="r.status==='aktif'?'Aktif':'Nonaktif'"></span>
                </td>
                <td class="px-3 py-2">
                  <div class="flex items-center justify-center gap-2">
                    <button class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50"
                            @click="openDetail(r)"><i class='bx bx-show'></i> Lihat</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50"
                            @click="openForm(r)"><i class='bx bx-edit'></i> Edit</button>
                    <button class="px-2 py-1 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100"
                            @click="remove(r)"><i class='bx bx-trash'></i></button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </section>

    
    <div x-show="selectedIds.length>0" x-cloak
         class="fixed bottom-4 left-4 right-4 md:left-1/2 md:-translate-x-1/2 z-40">
      <div class="mx-auto w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-lg">
        <div class="flex items-center justify-between p-3">
          <div class="text-sm text-slate-600">
            <strong x-text="selectedIds.length"></strong> dipilih
          </div>
          <div class="flex items-center gap-2">
            <button class="h-9 rounded-xl border border-slate-200 px-3 hover:bg-slate-50"
                    @click="setStatusSelected('aktif')">Aktifkan</button>
            <button class="h-9 rounded-xl border border-slate-200 px-3 hover:bg-slate-50"
                    @click="setStatusSelected('nonaktif')">Nonaktifkan</button>
            <button class="h-9 rounded-xl bg-rose-600 px-3 text-white hover:bg-rose-700"
                    @click="removeSelected()">Hapus</button>
          </div>
        </div>
      </div>
    </div>

    
    <div x-show="form.open" x-cloak class="fixed inset-0 z-50 flex items-start justify-center bg-black/30 p-4 pt-20">
      <div class="w-full max-w-3xl rounded-2xl bg-white p-4 shadow-xl">
        <div class="flex items-center justify-between">
          <div class="font-semibold" x-text="form.id? 'Edit Agen' : 'Tambah Agen'"></div>
          <button class="p-2 -m-2 rounded hover:bg-slate-100" @click="form.open=false"><i class='bx bx-x text-xl'></i></button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
          <div>
            <label class="text-xs text-slate-500">Kode</label>
            <input type="text" x-model="form.kode" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div>
            <label class="text-xs text-slate-500">Nama Agen</label>
            <input type="text" x-model="form.nama" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div>
            <label class="text-xs text-slate-500">Outlet</label>
            <select x-model="form.outlet" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3">
              <option>Bumibraja</option><option>Dahana</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-slate-500">Wilayah</label>
            <input type="text" x-model="form.wilayah" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div>
            <label class="text-xs text-slate-500">WA</label>
            <input type="text" x-model="form.wa" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3" placeholder="628xxxxxxxxxx">
          </div>
          <div>
            <label class="text-xs text-slate-500">Target/Bulan</label>
            <input type="number" x-model.number="form.target" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div>
            <label class="text-xs text-slate-500">Komisi (%)</label>
            <input type="number" x-model.number="form.persen" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3">
          </div>
          <div>
            <label class="text-xs text-slate-500">Status</label>
            <select x-model="form.status" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3">
              <option value="aktif">Aktif</option>
              <option value="nonaktif">Nonaktif</option>
            </select>
          </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
          <button class="h-10 rounded-xl border border-slate-200 px-4 hover:bg-slate-50" @click="form.open=false">Batal</button>
          <button class="h-10 rounded-xl bg-primary-600 px-4 text-white hover:bg-primary-700" @click="save()">Simpan</button>
        </div>
      </div>
    </div>

    
    <div x-show="detail.open" x-cloak class="fixed inset-0 z-40">
      <div class="absolute inset-0 bg-black/30" @click="detail.open=false"></div>
      <aside class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl p-4 overflow-y-auto">
        <div class="flex items-center justify-between">
          <div class="font-semibold">Detail Agen</div>
          <button class="p-2 -m-2 rounded hover:bg-slate-100" @click="detail.open=false"><i class='bx bx-x text-xl'></i></button>
        </div>

        <template x-if="detail.data">
          <div class="mt-4 space-y-3">
            <div class="flex items-center gap-3">
              <div class="rounded-2xl bg-primary-50 p-3">
                <i class='bx bx-user text-primary-700 text-2xl'></i>
              </div>
              <div>
                <div class="font-semibold" x-text="detail.data.nama"></div>
                <div class="text-xs text-slate-500" x-text="detail.data.kode"></div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Outlet</div>
                <div class="font-medium" x-text="detail.data.outlet"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Wilayah</div>
                <div class="font-medium" x-text="detail.data.wilayah"></div>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">WA</div>
                <a :href="`https://wa.me/${detail.data.wa}`" target="_blank" class="font-medium text-primary-600 hover:underline" x-text="detail.data.wa"></a>
              </div>
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="text-xs text-slate-500">Bergabung</div>
                <div class="font-medium" x-text="tgl(detail.data.gabung)"></div>
              </div>
            </div>

            <div class="rounded-xl border border-slate-200 p-3">
              <div class="text-xs text-slate-500">Penjualan Bulan Ini</div>
              <div class="text-xl font-bold" x-text="idr(detail.data.penjualan)"></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
              <div class="text-xs text-slate-500">Komisi Terhitung</div>
              <div class="text-xl font-bold" x-text="idr(detail.data.komisi)"></div>
            </div>

            <div>
              <div class="text-sm font-semibold mb-2">Transaksi Terakhir</div>
              <ul class="space-y-2">
                <template x-for="t in detail.transaksi" :key="t.no">
                  <li class="rounded-xl border border-slate-200 p-3">
                    <div class="flex items-center justify-between">
                      <div class="text-sm font-medium" x-text="t.no"></div>
                      <div class="text-xs text-slate-500" x-text="tgl(t.tgl)"></div>
                    </div>
                    <div class="mt-1 text-sm">Total: <span class="font-medium" x-text="idr(t.total)"></span></div>
                  </li>
                </template>
              </ul>
            </div>
          </div>
        </template>
      </aside>
    </div>
  </div>

  <script>
    function agenPage(){
      return {
        // filter dan data
        filter: { q:'', outlet:'all', status:'all' },
        allRows: [], rows: [],

        kpi: { total:0, aktif:0, omzet:0, komisi:0 },

        // form modal
        form: { open:false, id:null, kode:'', nama:'', outlet:'Bumibraja', wilayah:'', wa:'', target:0, persen:5, status:'aktif' },

        // detail panel
        detail: { open:false, data:null, transaksi:[] },

        get selectedIds(){ return this.rows.filter(x=>x._checked).map(x=>x.id); },

        init(){
          // dummy data
          this.allRows = [
            { id:1, kode:'AG-001', nama:'Acep',     outlet:'Bumibraja', wilayah:'Cikoneng', wa:'6281234567890', target: 50000000, penjualan: 42000000, persen:5,  komisi:2100000, titipan: 500000, status:'aktif',    gabung:'2024-06-01' },
            { id:2, kode:'AG-002', nama:'Budi',     outlet:'Dahana',    wilayah:'Panyileukan', wa:'6289876543210', target: 60000000, penjualan: 80000000, persen:5,  komisi:4000000, titipan: 0,      status:'aktif',    gabung:'2023-10-12' },
            { id:3, kode:'AG-003', nama:'Citra',    outlet:'Bumibraja', wilayah:'Ujung Berung', wa:'628111222333',  target: 30000000, penjualan: 12000000, persen:7.5, komisi:900000,  titipan: 250000, status:'nonaktif', gabung:'2022-01-05' },
          ];
          this.applyFilters();
        },

        // helpers
        idr(n){ return (Math.round(Number(n)||0)).toLocaleString('id-ID',{style:'currency', currency:'IDR'}).replace(/\u00A0/g,' '); },
        tgl(s){ const d=new Date(s+'T00:00:00'); return d.toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}); },

        applyFilters(){
          const q = this.filter.q.toLowerCase();
          this.rows = this.allRows.filter(r=>{
            const okOutlet = this.filter.outlet==='all' || r.outlet===this.filter.outlet;
            const okStatus = this.filter.status==='all' || r.status===this.filter.status;
            const okText = !q || [r.nama,r.wa,r.wilayah].join(' ').toLowerCase().includes(q);
            return okOutlet && okStatus && okText;
          });

          // KPI
          this.kpi.total = this.allRows.length;
          this.kpi.aktif = this.allRows.filter(r=>r.status==='aktif').length;
          this.kpi.omzet = this.allRows.reduce((a,b)=>a+b.penjualan, 0);
          this.kpi.komisi = this.allRows.reduce((a,b)=>a+b.komisi, 0);
        },

        // table bulk
        toggleAll(e){ const v=e.target.checked; this.rows.forEach(r=> r._checked=v); },
        setStatusSelected(st){ this.allRows = this.allRows.map(r=> this.selectedIds.includes(r.id)? {...r,status:st}:r); this.applyFilters(); },
        removeSelected(){ if(!this.selectedIds.length) return; this.allRows = this.allRows.filter(r=>!this.selectedIds.includes(r.id)); this.applyFilters(); },

        // actions
        openForm(row=null){
          if(row){ this.form = { open:true, ...row }; }
          else { this.form = { open:true, id:null, kode:'', nama:'', outlet:'Bumibraja', wilayah:'', wa:'', target:0, persen:5, status:'aktif' }; }
        },
        save(){
          if(this.form.id){
            this.allRows = this.allRows.map(r=> r.id===this.form.id ? {...r, ...this.form} : r);
          }else{
            const id = Math.max(0,...this.allRows.map(r=>r.id))+1;
            const gabung = new Date().toISOString().slice(0,10);
            this.allRows.push({ id, gabung, penjualan:0, komisi:0, titipan:0, ...this.form });
          }
          this.form.open=false; this.applyFilters();
        },
        remove(row){ if(!confirm('Hapus agen ini?')) return; this.allRows = this.allRows.filter(r=>r.id!==row.id); this.applyFilters(); },

        openDetail(r){
          this.detail.data = r;
          // dummy transaksi terakhir
          this.detail.transaksi = [
            { no:'INV/001', tgl:'2025-10-01', total: 12000000 },
            { no:'INV/014', tgl:'2025-10-11', total: 3000000 },
            { no:'INV/020', tgl:'2025-10-22', total: 8000000 },
          ];
          this.detail.open = true;
        },

        exportCSV(){
          // export mudah (demo) – ubah ke backend saat siap
          const headers = ['Kode','Nama','Outlet','Wilayah','WA','Target','Penjualan','Komisi','Titipan','Status'];
          const lines = this.rows.map(r=> [r.kode,r.nama,r.outlet,r.wilayah,r.wa,r.target,r.penjualan,r.komisi,r.titipan,r.status].join(','));
          const csv = [headers.join(','), ...lines].join('\n');
          const blob = new Blob([csv],{type:'text/csv;charset=utf-8;'});
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url; a.download = 'agen.csv'; a.click();
          URL.revokeObjectURL(url);
        },
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\agen\index.blade.php ENDPATH**/ ?>