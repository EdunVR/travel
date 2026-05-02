{{-- resources/views/admin/penjualan/pos/history.blade.php --}}
<x-layouts.admin :title="'Riwayat POS'">
<div x-data="posHistoryApp()" x-init="init()" class="space-y-4">

  {{-- Header --}}
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <h1 class="text-2xl font-bold tracking-tight">Riwayat Transaksi POS</h1>
      <div class="flex gap-2">
        <select x-model="filters.outlet" @change="loadData()" class="h-10 rounded-xl border border-slate-200 px-3">
          <option value="all">Semua Outlet</option>
          @foreach($outlets as $outlet)
            <option value="{{ $outlet->id_outlet }}">{{ $outlet->nama_outlet }}</option>
          @endforeach
        </select>
        <select x-model="filters.status" @change="loadData()" class="h-10 rounded-xl border border-slate-200 px-3">
          <option value="all">Semua Status</option>
          <option value="lunas">Lunas</option>
          <option value="menunggu">Menunggu</option>
        </select>
      </div>
    </div>
    
    {{-- Filter Tanggal --}}
    <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2">
      <input type="date" x-model="filters.start_date" @change="loadData()" 
             class="h-10 rounded-xl border border-slate-200 px-3">
      <input type="date" x-model="filters.end_date" @change="loadData()" 
             class="h-10 rounded-xl border border-slate-200 px-3">
      <button @click="resetFilters()" class="h-10 rounded-xl border border-slate-200 hover:bg-slate-50">
        Reset Filter
      </button>
    </div>
  </section>

  {{-- Table --}}
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="overflow-x-auto">
      <table id="posHistoryTable" class="w-full">
        <thead>
          <tr class="border-b">
            <th class="px-3 py-2 text-left">No. Transaksi</th>
            <th class="px-3 py-2 text-left">Tanggal</th>
            <th class="px-3 py-2 text-left">Outlet</th>
            <th class="px-3 py-2 text-left">Customer</th>
            <th class="px-3 py-2 text-right">Total</th>
            <th class="px-3 py-2 text-center">Status</th>
            <th class="px-3 py-2 text-center">Pembayaran</th>
            <th class="px-3 py-2 text-center">Items</th>
            <th class="px-3 py-2 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </section>

  {{-- Modal Detail --}}
  <div x-show="ui.detailOpen" x-transition class="fixed inset-0 bg-black/30 z-50">
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="w-full max-w-2xl rounded-2xl bg-white border border-slate-200 shadow-card p-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <div class="font-semibold text-lg">Detail Transaksi</div>
          <button @click="ui.detailOpen=false" class="w-8 h-8 rounded hover:bg-slate-100">
            <i class='bx bx-x'></i>
          </button>
        </div>
        
        <template x-if="detail">
          <div class="space-y-3">
            <div class="grid grid-cols-2 gap-2 text-sm">
              <div><span class="text-slate-500">No. Transaksi:</span> <b x-text="detail.no_transaksi"></b></div>
              <div><span class="text-slate-500">Tanggal:</span> <b x-text="formatDate(detail.tanggal)"></b></div>
              <div><span class="text-slate-500">Outlet:</span> <b x-text="detail.outlet?.nama_outlet"></b></div>
              <div><span class="text-slate-500">Customer:</span> <b x-text="detail.member?.nama || 'Pelanggan Umum'"></b></div>
              <div><span class="text-slate-500">Kasir:</span> <b x-text="detail.user?.name"></b></div>
              <div><span class="text-slate-500">Status:</span> <b x-text="detail.status"></b></div>
            </div>
            
            <div class="border-t pt-3">
              <div class="font-medium mb-2">Items:</div>
              <table class="w-full text-sm">
                <thead class="bg-slate-50">
                  <tr>
                    <th class="px-2 py-1 text-left">Produk</th>
                    <th class="px-2 py-1 text-center">Qty</th>
                    <th class="px-2 py-1 text-right">Harga</th>
                    <th class="px-2 py-1 text-right">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="item in detail.items" :key="item.id">
                    <tr class="border-t">
                      <td class="px-2 py-1" x-text="item.nama_produk"></td>
                      <td class="px-2 py-1 text-center" x-text="item.kuantitas"></td>
                      <td class="px-2 py-1 text-right" x-text="idr(item.harga)"></td>
                      <td class="px-2 py-1 text-right" x-text="idr(item.subtotal)"></td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
            
            <div class="border-t pt-3 space-y-1 text-sm">
              <div class="flex justify-between"><span>Subtotal:</span><b x-text="idr(detail.subtotal)"></b></div>
              <div class="flex justify-between"><span>Diskon:</span><b x-text="idr(detail.total_diskon)"></b></div>
              <div class="flex justify-between" x-show="detail.ppn > 0"><span>PPN:</span><b x-text="idr(detail.ppn)"></b></div>
              <div class="flex justify-between text-lg border-t pt-2">
                <span>Total:</span><b x-text="idr(detail.total)"></b>
              </div>
              <template x-if="!detail.is_bon">
                <div>
                  <div class="flex justify-between"><span>Bayar:</span><b x-text="idr(detail.jumlah_bayar)"></b></div>
                  <div class="flex justify-between"><span>Kembali:</span><b x-text="idr(detail.kembalian)"></b></div>
                </div>
              </template>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>

</div>

<script>
function posHistoryApp(){
  return {
    filters: {
      outlet: '{{ $outletId }}',
      status: 'all',
      start_date: '',
      end_date: ''
    },
    ui: {
      detailOpen: false
    },
    detail: null,
    table: null,

    init(){
      this.initDataTable();
    },

    initDataTable(){
      this.table = $('#posHistoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route("admin.penjualan.pos.history.data") }}',
          data: (d) => {
            d.outlet_id = this.filters.outlet;
            d.status = this.filters.status;
            d.start_date = this.filters.start_date;
            d.end_date = this.filters.end_date;
          }
        },
        columns: [
          { data: 'no_transaksi', name: 'no_transaksi' },
          { data: 'tanggal_formatted', name: 'tanggal' },
          { data: 'outlet_name', name: 'outlet.nama_outlet' },
          { data: 'customer_name', name: 'member.nama' },
          { data: 'total_formatted', name: 'total', className: 'text-right' },
          { data: 'status_badge', name: 'status', className: 'text-center' },
          { data: 'payment_type', name: 'jenis_pembayaran', className: 'text-center' },
          { data: 'items_count', name: 'items_count', orderable: false, searchable: false, className: 'text-center' },
          { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'desc']],
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
      });
    },

    loadData(){
      if(this.table) {
        this.table.ajax.reload();
      }
    },

    resetFilters(){
      this.filters = {
        outlet: 'all',
        status: 'all',
        start_date: '',
        end_date: ''
      };
      this.loadData();
    },

    async viewDetail(id){
      try {
        const response = await fetch(`{{ url('penjualan/pos') }}/${id}`);
        const result = await response.json();
        if(result.success) {
          this.detail = result.data;
          this.ui.detailOpen = true;
        }
      } catch(e) {
        console.error(e);
        alert('Gagal memuat detail');
      }
    },

    formatDate(date){
      return new Date(date).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    idr(n){ 
      return (Number(n)||0).toLocaleString('id-ID',{style:'currency',currency:'IDR'}).replace(/\u00A0/g,' '); 
    }
  }
}

// Global function untuk dipanggil dari DataTable
function viewDetail(id) {
  Alpine.$data(document.querySelector('[x-data]')).viewDetail(id);
}
</script>
</x-layouts.admin>
