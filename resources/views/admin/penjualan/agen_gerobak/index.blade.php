{{-- resources/views/admin/penjualan/agen_gerobak/index.blade.php --}}
<x-layouts.admin :title="'Agen & Gerobak'">
  <div x-data="agGrPage()" x-init="init()" x-on:keydown.escape.window="closeOverlays()" class="space-y-6 overflow-x-hidden">

    {{-- Header --}}
    <header class="space-y-1">
      <h1 class="text-2xl font-bold tracking-tight">Agen & Gerobak</h1>
      <p class="text-slate-600">Rekap data agen beserta gerobak, pembelian & penjualan (frontend only).</p>
    </header>

    {{-- KPI --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-slate-500 text-xs flex items-center gap-2">
          <i class='bx bx-group text-primary-600 text-lg'></i> Total Agen
        </div>
        <div class="mt-1 text-2xl font-bold" x-text="kpi.totalAgen"></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-slate-500 text-xs flex items-center gap-2">
          <i class='bx bx-coin-stack text-amber-600 text-lg'></i> Total Penjualan (bulan ini)
        </div>
        <div class="mt-1 text-2xl font-bold" x-text="idr(kpi.totalPenjualan)"></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-slate-500 text-xs flex items-center gap-2">
          <i class='bx bx-cart-download text-emerald-600 text-lg'></i> Total Pembelian (bulan ini)
        </div>
        <div class="mt-1 text-2xl font-bold" x-text="idr(kpi.totalPembelian)"></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
        <div class="text-slate-500 text-xs flex items-center gap-2">
          <i class='bx bx-store text-primary-600 text-lg'></i> Total Gerobak
        </div>
        <div class="mt-1 text-2xl font-bold" x-text="kpi.totalGerobak"></div>
      </div>
    </section>

    {{-- Filter Toolbar --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-3 sm:p-4 shadow-card">
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 items-end">
        <div class="xl:col-span-5">
          <label class="text-xs font-medium text-slate-500 mb-1 block">Pencarian</label>
          <div class="relative">
            <i class='bx bx-search absolute left-2 top-1/2 -translate-y-1/2 text-slate-400'></i>
            <input x-model.debounce.300ms="filter.q" type="text" placeholder="Cari nama/kode agen, telepon, alamat, lokasi"
                   class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-8 pr-3">
          </div>
        </div>
        <div class="xl:col-span-3">
          <label class="text-xs font-medium text-slate-500 mb-1 block">Outlet</label>
          <select x-model="filter.outlet" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3">
            <option value="all">Semua Outlet</option>
            <option>Bumibraja</option>
            <option>Dahana</option>
          </select>
        </div>
        <div class="xl:col-span-4 flex flex-wrap gap-2">
          <button @click="openForm()"
                  class="inline-flex h-10 items-center gap-2 rounded-xl bg-primary-600 px-4 text-white hover:bg-primary-700">
            <i class='bx bx-plus'></i> Tambah Agen
          </button>
          <button @click="exportCSV()"
                  class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 hover:bg-slate-50">
            <i class='bx bx-export'></i> Export CSV
          </button>
        </div>
      </div>
    </section>

    {{-- ====== Desktop Table (md+) ====== --}}
    <section class="hidden md:block rounded-2xl border border-slate-200 bg-white p-0 shadow-card">
      <div class="overflow-x-auto">
        <table class="min-w-[1200px] w-full text-sm">
          <thead class="bg-slate-50 sticky top-0 z-10">
            <tr>
              <th class="px-3 py-2 w-10"><input type="checkbox" @change="toggleAll($event)"></th>
              <th class="px-3 py-2">No</th>
              <th class="px-3 py-2 text-left">Kode Agen</th>
              <th class="px-3 py-2 text-left">Nama Agen</th>
              <th class="px-3 py-2 text-left">Outlet</th>
              <th class="px-3 py-2 text-left">Telepon</th>
              <th class="px-3 py-2 text-left">Alamat</th>
              <th class="px-3 py-2 text-right">Jumlah Gerobak</th>
              <th class="px-3 py-2 text-right">Total Produk</th>
              <th class="px-3 py-2 text-right">Jumlah Pembelian</th>
              <th class="px-3 py-2 text-right">Total Pembelian (Rp)</th>
              <th class="px-3 py-2 text-right">Total Penjualan (Rp)</th>
              <th class="px-3 py-2 text-left">Lokasi</th>
              <th class="px-3 py-2 text-center w-40">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="rows.length===0">
              <tr><td colspan="14" class="px-3 py-6 text-center text-slate-500">Tidak ada data</td></tr>
            </template>

            <template x-for="(r,i) in rows" :key="r.id">
              <tr class="border-t">
                <td class="px-3 py-2"><input type="checkbox" x-model="r._checked"></td>
                <td class="px-3 py-2" x-text="i+1"></td>
                <td class="px-3 py-2 font-medium" x-text="r.kode"></td>
                <td class="px-3 py-2" x-text="r.nama"></td>
                <td class="px-3 py-2" x-text="r.outlet"></td>
                <td class="px-3 py-2">
                  <a :href="`https://wa.me/${r.tel}`" target="_blank" class="text-primary-600 hover:underline" x-text="r.tel"></a>
                </td>
                <td class="px-3 py-2 truncate max-w-[240px]" x-text="r.alamat"></td>
                <td class="px-3 py-2 text-right" x-text="r.gerobak.length"></td>
                <td class="px-3 py-2 text-right" x-text="r.totalProduk"></td>
                <td class="px-3 py-2 text-right" x-text="r.jmlPembelian"></td>
                <td class="px-3 py-2 text-right" x-text="idr(r.totalPembelian)"></td>
                <td class="px-3 py-2 text-right" x-text="idr(r.totalPenjualan)"></td>
                <td class="px-3 py-2" x-text="r.lokasi"></td>
                <td class="px-3 py-2">
                  <div class="flex items-center justify-center gap-2">
                    <button class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50"
                            @click="openDetail(r)"><i class='bx bx-store-alt'></i> Gerobak</button>
                    <button class="px-2 py-1 rounded-lg border border-slate-200 hover:bg-slate-50"
                            @click="openForm(r)"><i class='bx bx-edit'></i></button>
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

    {{-- ====== Mobile Cards (sm-) ====== --}}
    <section class="md:hidden space-y-3">
      <template x-if="rows.length===0">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-center text-slate-500">Tidak ada data</div>
      </template>

      <template x-for="(r,i) in rows" :key="r.id">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold" x-text="r.nama"></div>
              <div class="text-xs text-slate-500" x-text="r.kode"></div>
              <div class="mt-1 text-xs" x-text="`Outlet: ${r.outlet}`"></div>
              <div class="text-xs truncate" x-text="r.alamat"></div>
              <div class="mt-1 text-xs"><span class="text-slate-500">Lokasi:</span> <span x-text="r.lokasi"></span></div>
            </div>
            <div class="text-right">
              <div class="text-xs text-slate-500">Penjualan</div>
              <div class="font-bold" x-text="idr(r.totalPenjualan)"></div>
              <div class="mt-1 text-xs text-slate-500">Pembelian</div>
              <div class="font-medium" x-text="idr(r.totalPembelian)"></div>
            </div>
          </div>

          <div class="mt-3 grid grid-cols-3 gap-2 text-center">
            <div class="rounded-xl border border-slate-200 p-2">
              <div class="text-[10px] text-slate-500">Gerobak</div>
              <div class="font-semibold" x-text="r.gerobak.length"></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-2">
              <div class="text-[10px] text-slate-500">Produk</div>
              <div class="font-semibold" x-text="r.totalProduk"></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-2">
              <div class="text-[10px] text-slate-500">Jml Beli</div>
              <div class="font-semibold" x-text="r.jmlPembelian"></div>
            </div>
          </div>

          <div class="mt-3 flex items-center justify-end gap-2">
            <button class="px-3 py-1.5 rounded-lg border border-slate-200 text-sm" @click="openDetail(r)">
              <i class='bx bx-store-alt'></i> Gerobak
            </button>
            <button class="px-3 py-1.5 rounded-lg border border-slate-200 text-sm" @click="openForm(r)">
              <i class='bx bx-edit'></i>
            </button>
            <button class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-sm" @click="remove(r)">
              <i class='bx bx-trash'></i>
            </button>
          </div>
        </div>
      </template>
    </section>

    {{-- Bulk Action Bar --}}
    <div x-show="selectedIds.length>0" x-cloak
         class="fixed bottom-4 left-4 right-4 md:left-1/2 md:-translate-x-1/2 z-40">
      <div class="mx-auto w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-lg">
        <div class="flex items-center justify-between p-3">
          <div class="text-sm text-slate-600">
            <strong x-text="selectedIds.length"></strong> dipilih
          </div>
          <div class="flex items-center gap-2">
            <button class="h-9 rounded-xl border border-slate-200 px-3 hover:bg-slate-50"
                    @click="removeSelected()">Hapus</button>
            <button class="h-9 rounded-xl border border-slate-200 px-3 hover:bg-slate-50"
                    @click="exportCSV(true)">Export Pilihan</button>
          </div>
        </div>
      </div>
    </div>

    {{-- Modal Tambah/Edit Agen (Fix: z-index tertinggi & tutup drawer) --}}
    <div x-show="form.open" x-cloak
         class="fixed inset-0 z-[70] flex items-start justify-center bg-black/30 p-4 pt-20"
         @click.self="form.open=false">
      <div class="w-full max-w-4xl rounded-2xl bg-white p-4 shadow-xl">
        <div class="flex items-center justify-between">
          <div class="font-semibold" x-text="form.id? 'Edit Agen' : 'Tambah Agen'"></div>
          <button class="p-2 -m-2 rounded hover:bg-slate-100" @click="form.open=false"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
          <div>
            <label class="text-xs text-slate-500">Kode Agen</label>
            <input x-model="form.kode" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3" />
          </div>
          <div>
            <label class="text-xs text-slate-500">Nama Agen</label>
            <input x-model="form.nama" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3" />
          </div>
          <div>
            <label class="text-xs text-slate-500">Outlet</label>
            <select x-model="form.outlet" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3">
              <option>Bumibraja</option><option>Dahana</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-slate-500">Telepon (WA)</label>
            <input x-model="form.tel" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3" placeholder="628xxxxxxxxxx" />
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs text-slate-500">Alamat</label>
            <input x-model="form.alamat" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3" />
          </div>
          <div>
            <label class="text-xs text-slate-500">Lokasi</label>
            <input x-model="form.lokasi" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3" placeholder="Kota/Kecamatan" />
          </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
          <button class="h-10 rounded-xl border border-slate-200 px-4 hover:bg-slate-50" @click="form.open=false">Batal</button>
          <button class="h-10 rounded-xl bg-primary-600 px-4 text-white hover:bg-primary-700" @click="save()">Simpan</button>
        </div>
      </div>
    </div>

    {{-- Side Detail: Daftar Gerobak (drawer) --}}
    <div x-show="detail.open" x-cloak class="fixed inset-0 z-40">
      <div class="absolute inset-0 bg-black/30" @click="detail.open=false"></div>
      <aside class="absolute right-0 top-0 h-full w-full max-w-xl bg-white shadow-2xl p-4 overflow-y-auto">
        <div class="flex items-center justify-between">
          <div class="font-semibold">Gerobak – <span x-text="detail.data?.nama"></span></div>
          <button class="p-2 -m-2 rounded hover:bg-slate-100" @click="detail.open=false"><i class='bx bx-x text-xl'></i></button>
        </div>
        <template x-if="detail.data">
          <div class="mt-4 space-y-3">
            <div class="rounded-xl border border-slate-200 p-3">
              <div class="text-xs text-slate-500">Total Gerobak</div>
              <div class="text-xl font-bold" x-text="detail.data.gerobak.length"></div>
            </div>

            <ul class="space-y-2">
              <template x-for="g in detail.data.gerobak" :key="g.kode">
                <li class="rounded-xl border border-slate-200 p-3">
                  <div class="flex items-center justify-between gap-2">
                    <div>
                      <div class="text-sm font-semibold" x-text="g.kode"></div>
                      <div class="text-xs text-slate-500" x-text="g.lokasi"></div>
                    </div>
                    <span class="text-xs rounded-full px-2 py-0.5"
                          :class="g.status==='aktif'?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-600'"
                          x-text="g.status==='aktif'?'Aktif':'Nonaktif'"></span>
                  </div>
                  <div class="mt-2 text-xs">Produk: <span class="font-medium" x-text="g.produk"></span></div>
                </li>
              </template>
            </ul>
          </div>
        </template>
      </aside>
    </div>
  </div>

  <script>
    function agGrPage(){
      return {
        filter: { q:'', outlet:'all' },
        allRows: [], rows: [],
        kpi: { totalAgen:0, totalPenjualan:0, totalPembelian:0, totalGerobak:0 },

        form: { open:false, id:null, kode:'', nama:'', outlet:'Bumibraja', tel:'', alamat:'', lokasi:'' },
        detail: { open:false, data:null },

        get selectedIds(){ return this.rows.filter(x=>x._checked).map(x=>x.id); },

        closeOverlays(){ this.form.open=false; this.detail.open=false; },

        init(){
          // Dummy data
          this.allRows = [
            {
              id:1, kode:'AG-001', nama:'Acep', outlet:'Bumibraja', tel:'6281234567890',
              alamat:'Jl. Asia Afrika No. 1, Bandung', lokasi:'Bandung',
              gerobak:[
                { kode:'GRB-001', lokasi:'Alun-Alun Bandung', status:'aktif', produk: 38 },
                { kode:'GRB-002', lokasi:'Braga', status:'aktif', produk: 25 },
              ],
              totalProduk: 63, jmlPembelian: 14, totalPembelian: 12000000, totalPenjualan: 21000000
            },
            {
              id:2, kode:'AG-002', nama:'Budi', outlet:'Dahana', tel:'628222111333',
              alamat:'Jl. Panyileukan No. 7, Bandung', lokasi:'Cileunyi',
              gerobak:[
                { kode:'GRB-011', lokasi:'Ujung Berung', status:'aktif', produk: 20 },
              ],
              totalProduk: 20, jmlPembelian: 5, totalPembelian: 4500000, totalPenjualan: 8200000
            },
            {
              id:3, kode:'AG-003', nama:'Citra', outlet:'Bumibraja', tel:'628999777555',
              alamat:'Jl. Buah Batu No. 10, Bandung', lokasi:'Buah Batu',
              gerobak:[
                { kode:'GRB-020', lokasi:'Buah Batu', status:'nonaktif', produk: 0 },
                { kode:'GRB-021', lokasi:'Antapani', status:'aktif', produk: 12 },
                { kode:'GRB-022', lokasi:'Lengkong', status:'aktif', produk: 18 },
              ],
              totalProduk: 30, jmlPembelian: 8, totalPembelian: 7000000, totalPenjualan: 15400000
            },
          ];
          this.applyFilters();
        },

        idr(n){ return (Math.round(Number(n)||0)).toLocaleString('id-ID',{style:'currency',currency:'IDR'}).replace(/\u00A0/g,' '); },

        applyFilters(){
          const q = this.filter.q.toLowerCase();
          this.rows = this.allRows.filter(r=>{
            const okOutlet = this.filter.outlet==='all' || r.outlet===this.filter.outlet;
            const text = [r.kode,r.nama,r.tel,r.alamat,r.lokasi].join(' ').toLowerCase();
            const okText = !q || text.includes(q);
            return okOutlet && okText;
          });

          this.kpi.totalAgen = this.allRows.length;
          this.kpi.totalPenjualan = this.allRows.reduce((a,b)=>a+b.totalPenjualan,0);
          this.kpi.totalPembelian = this.allRows.reduce((a,b)=>a+b.totalPembelian,0);
          this.kpi.totalGerobak  = this.allRows.reduce((a,b)=>a+b.gerobak.length,0);
        },

        toggleAll(e){ const v=e.target.checked; this.rows.forEach(r=> r._checked=v); },
        removeSelected(){ if(!this.selectedIds.length) return; this.allRows = this.allRows.filter(r=>!this.selectedIds.includes(r.id)); this.applyFilters(); },

        openForm(row=null){
          // FIX: pastikan drawer tertutup agar tidak menimpa modal
          this.detail.open = false;
          if(row){ this.form = { open:true, ...row }; }
          else{ this.form = { open:true, id:null, kode:'', nama:'', outlet:'Bumibraja', tel:'', alamat:'', lokasi:'' }; }
        },
        save(){
          if(this.form.id){
            this.allRows = this.allRows.map(r=> r.id===this.form.id? {...r, ...this.form}: r);
          }else{
            const id = Math.max(0,...this.allRows.map(r=>r.id))+1;
            this.allRows.push({ id, gerobak:[], totalProduk:0, jmlPembelian:0, totalPembelian:0, totalPenjualan:0, ...this.form });
          }
          this.form.open=false; this.applyFilters();
        },
        remove(r){ if(!confirm('Hapus data agen?')) return; this.allRows=this.allRows.filter(x=>x.id!==r.id); this.applyFilters(); },

        openDetail(r){
          // FIX: pastikan modal tertutup saat buka drawer
          this.form.open = false;
          this.detail.data=r; this.detail.open=true;
        },

        exportCSV(onlySelected=false){
          const data = onlySelected? this.rows.filter(r=>r._checked): this.rows;
          const headers = [
            'No','Kode Agen','Nama Agen','Outlet','Telepon','Alamat','Jumlah Gerobak',
            'Total Produk','Jumlah Pembelian','Total Pembelian (Rp)','Total Penjualan (Rp)','Lokasi'
          ];
          const lines = data.map((r,i)=>[
            i+1, r.kode, r.nama, r.outlet, r.tel, `"${r.alamat.replace(/"/g,'""')}"`, r.gerobak.length,
            r.totalProduk, r.jmlPembelian, r.totalPembelian, r.totalPenjualan, r.lokasi
          ].join(','));
          const csv = [headers.join(','), ...lines].join('\n');
          const blob = new Blob([csv],{type:'text/csv;charset=utf-8;'});
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href=url; a.download='agen_gerobak.csv'; a.click();
          URL.revokeObjectURL(url);
        },
      }
    }
  </script>
</x-layouts.admin>
