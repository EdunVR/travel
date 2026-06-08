<x-layouts.admin :title="'Inventaris / Transfer Gudang'">
  <div x-data="transferGudang()" x-init="init()" class="space-y-4 overflow-x-hidden">
    <!-- Top bar -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Transfer Gudang</h1>
        <p class="text-slate-600 text-sm">Kelola transfer stok antar outlet</p>
      </div>
      
      <div class="flex flex-wrap gap-2">
        <!-- Tombol Daftar Permintaan - SELALU TAMPIL -->
        <button @click="openRequestList=true; loadRequests()"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
          <i class='bx bx-list-ul text-lg'></i>
          <span>Daftar Permintaan Transfer</span>
          <span x-show="pendingCount > 0" 
                class="ml-1 inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-red-500 text-white text-[12px] font-semibold"
                x-text="pendingCount"></span>
        </button>

        @hasPermission('inventaris.transfer-gudang.create')
        <button @click="openCart=true"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-transfer text-lg'></i>
          <span>Buat Transfer Baru</span>
          <span x-show="cart.length > 0" 
                class="ml-1 inline-flex min-w-5 h-5 items-center justify-center rounded-full bg-white/20 px-1 text-[12px] font-semibold"
                x-text="cart.length"></span>
        </button>
        @endhasPermission
        
        <button @click="exportPdf()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export PDF
        </button>
        <button @click="exportExcel()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 hover:bg-slate-50">
          <i class='bx bx-export text-lg'></i> Export Excel
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data...</span>
      </div>
    </div>

    <!-- Two columns -->
    <div x-show="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <!-- PENGIRIM -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card">
        <div class="p-4 border-b border-slate-100">
          <div class="text-sm text-slate-600 mb-1">Outlet Pengirim</div>
          <select x-model="sender" @change="loadSenderItems()" class="w-full rounded-xl border border-slate-200 px-3 py-2">
            <option value="">Pilih Outlet Pengirim</option>
            <template x-for="outlet in outlets" :key="outlet.id">
              <option :value="outlet.id" x-text="outlet.name"></option>
            </template>
          </select>
        </div>

        <div class="p-4">
          <!-- Tabs -->
          <div class="flex gap-2 mb-3">
            <template x-for="t in tabs" :key="t.key">
              <button @click="changeTab(t.key)"
                      class="px-3 py-1.5 rounded-lg text-sm border"
                      :class="tab===t.key ? 'border-primary-200 bg-primary-50 text-primary-700' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'">
                <span x-text="t.label"></span>
              </button>
            </template>
          </div>

          <!-- List -->
          <div class="space-y-3 max-h-96 overflow-y-auto">
            <template x-for="it in senderItems" :key="it.id">
              <div class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
                <div class="flex items-start justify-between gap-3">
                  <div class="flex-1">
                    <div class="font-semibold" x-text="it.name"></div>
                    <div class="text-sm text-slate-600">
                      Stok: <span x-text="it.stock"></span> 
                      <span x-show="it.unit">• <span x-text="it.unit"></span></span>
                    </div>
                    <div x-show="it.code" class="text-xs text-slate-500 font-mono" x-text="it.code"></div>
                  </div>
                  <button @click="addToCart(it)"
                          class="shrink-0 inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm"
                          :class="it.stock > 0 && !isInCart(it.id) ? 'bg-primary-600 text-white hover:bg-primary-700' : 'bg-slate-200 text-slate-500 cursor-not-allowed'"
                          :disabled="it.stock <= 0 || isInCart(it.id)"
                          :title="isInCart(it.id) ? 'Sudah ada di daftar transfer' : (it.stock <= 0 ? 'Stok habis' : 'Tambahkan ke daftar transfer')">
                    <i class='bx' :class="isInCart(it.id) ? 'bx-check' : 'bx-check-circle'"></i>
                    <span x-text="isInCart(it.id) ? 'Dipilih' : 'Pilih'"></span>
                  </button>
                </div>
              </div>
            </template>
            <div x-show="senderItems.length===0" class="text-slate-500 text-sm text-center py-4">
              <span x-show="sender">Tidak ada item di outlet ini.</span>
              <span x-show="!sender">Pilih outlet pengirim terlebih dahulu.</span>
            </div>
          </div>
        </div>
      </div>

      <!-- PENERIMA -->
      <div class="rounded-2xl border border-slate-200 bg-white shadow-card">
        <div class="p-4 border-b border-slate-100">
          <div class="text-sm text-slate-600 mb-1">Outlet Penerima</div>
          <select x-model="receiver" @change="loadReceiverItems()" class="w-full rounded-xl border border-slate-200 px-3 py-2">
            <option value="">Pilih Outlet Penerima</option>
            <template x-for="outlet in outlets" :key="outlet.id">
              <option :value="outlet.id" x-text="outlet.name"></option>
            </template>
          </select>
          <div x-show="receiver && receiver===sender" class="text-[12px] text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mt-2">
            Outlet pengirim dan penerima tidak boleh sama.
          </div>
        </div>

        <div class="p-4">
          <!-- Tabs -->
          <div class="flex gap-2 mb-3">
            <template x-for="t in tabs" :key="t.key">
              <button @click="changeTab(t.key)"
                      class="px-3 py-1.5 rounded-lg text-sm border"
                      :class="tab===t.key ? 'border-primary-200 bg-primary-50 text-primary-700' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'">
                <span x-text="t.label"></span>
              </button>
            </template>
          </div>

          <!-- List penerima (informasi saja) -->
          <div class="space-y-3 max-h-96 overflow-y-auto">
            <template x-for="it in receiverItems" :key="it.id">
              <div class="rounded-xl border border-slate-200 p-3">
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <div class="font-semibold" x-text="it.name"></div>
                    <div class="text-sm text-slate-600">
                      Stok: <span x-text="it.stock"></span>
                      <span x-show="it.unit">• <span x-text="it.unit"></span></span>
                    </div>
                    <div x-show="it.code" class="text-xs text-slate-500 font-mono" x-text="it.code"></div>
                  </div>
                  <span class="text-xs text-slate-500 shrink-0">Info</span>
                </div>
              </div>
            </template>
            <div x-show="receiverItems.length===0" class="text-slate-500 text-sm text-center py-4">
              <span x-show="receiver">Tidak ada item di outlet ini.</span>
              <span x-show="!receiver">Pilih outlet penerima terlebih dahulu.</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: CART -->
    <div x-show="openCart" x-transition.opacity class="fixed inset-0 z-40 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
      <div @click.outside="openCart=false"
           class="w-full max-w-5xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold">Daftar Permintaan Transfer</div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="openCart=false"><i class='bx bx-x text-xl'></i></button>
        </div>

        <div class="p-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
            <div class="rounded-xl border border-slate-200 p-3">
              <div class="text-[12px] text-slate-500">Dari</div>
              <div class="font-medium" x-text="getOutletName(sender)"></div>
            </div>
            <div class="rounded-xl border border-slate-200 p-3">
              <div class="text-[12px] text-slate-500">Ke</div>
              <div class="font-medium" x-text="getOutletName(receiver)"></div>
            </div>
          </div>

          <div class="rounded-2xl border border-slate-200 overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 text-slate-700">
                <tr>
                  <th class="text-left px-4 py-2">Jenis</th>
                  <th class="text-left px-4 py-2">Nama</th>
                  <th class="text-left px-4 py-2">Stok Pengirim</th>
                  <th class="text-left px-4 py-2">Jumlah</th>
                  <th class="text-left px-4 py-2">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <template x-for="(c,idx) in cart" :key="c.cid">
                  <tr class="border-t border-slate-100">
                    <td class="px-4 py-2 capitalize" x-text="c.type"></td>
                    <td class="px-4 py-2 font-medium" x-text="c.name"></td>
                    <td class="px-4 py-2" x-text="c.stock"></td>
                    <td class="px-4 py-2">
                      <div class="inline-flex rounded-lg border border-slate-200 overflow-hidden">
                        <button class="px-2 hover:bg-slate-100" @click="decQty(idx)">-</button>
                        <input type="number" min="1" :max="c.stock" x-model.number="c.qty"
                               class="w-16 text-center border-x border-slate-200">
                        <button class="px-2 hover:bg-slate-100" @click="incQty(idx)">+</button>
                      </div>
                    </td>
                    <td class="px-4 py-2">
                      <button class="rounded-lg border border-red-200 text-red-700 px-3 py-1 hover:bg-red-50"
                              @click="removeFromCart(idx)">
                        Hapus
                      </button>
                    </td>
                  </tr>
                </template>
                <tr x-show="cart.length===0">
                  <td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada item.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-3 flex items-center justify-end gap-2">
            <button class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50"
                    @click="cart=[]">Reset</button>
            <button class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700"
                    :disabled="!canSubmit() || submitting" @click="submitTransfer()">
              <span x-show="!submitting">Kirim Permintaan</span>
              <span x-show="submitting" class="inline-flex items-center gap-2">
                <i class='bx bx-loader-alt bx-spin'></i> Mengirim...
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL: DAFTAR PERMINTAAN TRANSFER -->
    <div x-show="openRequestList" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 overflow-y-auto">
      <div @click.outside="openRequestList=false"
          class="w-full max-w-7xl bg-white rounded-2xl shadow-float my-4 flex flex-col">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <div class="font-semibold">Daftar Permintaan Pengiriman</div>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" @click="openRequestList=false">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="p-4 border-b border-slate-100">
          <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            <div class="text-sm text-slate-600">
              Total <span x-text="requests.length"></span> permintaan pengiriman
            </div>
            <div class="flex gap-2">
              <select x-model="requestStatusFilter" @change="loadRequests()" 
                      class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="ALL">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
              </select>
              <button @click="loadRequests()" 
                      class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50 text-sm">
                <i class='bx bx-refresh'></i> Refresh
              </button>
            </div>
          </div>
        </div>

        <div class="p-4 overflow-y-auto flex-1">
          <!-- Loading State -->
          <div x-show="loadingRequests" class="text-center py-8">
            <div class="inline-flex items-center gap-2 text-slate-600">
              <i class='bx bx-loader-alt bx-spin text-xl'></i>
              <span>Memuat data permintaan...</span>
            </div>
          </div>

          <!-- Table Daftar Permintaan -->
          <div x-show="!loadingRequests" class="rounded-2xl border border-slate-200 overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 text-slate-700">
                <tr>
                  <th class="text-left px-4 py-3">Tanggal</th>
                  <th class="text-left px-4 py-3">No. Request</th>
                  <th class="text-left px-4 py-3">Outlet Asal</th>
                  <th class="text-left px-4 py-3">Outlet Tujuan</th>
                  <th class="text-left px-4 py-3">Item</th>
                  <th class="text-left px-4 py-3">Status</th>
                  <th class="text-left px-4 py-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <template x-for="(request, index) in requests" :key="request.id || index">
                  <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3">
                      <div x-text="request.created_at ? new Date(request.created_at).toLocaleDateString('id-ID') : '-'"></div>
                      <div class="text-xs text-slate-500" x-text="request.created_at ? new Date(request.created_at).toLocaleTimeString('id-ID') : ''"></div>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs" x-text="request.no_permintaan || ('#' + request.id)"></td>
                    <td class="px-4 py-3" x-text="request.outlet_asal || '-'"></td>
                    <td class="px-4 py-3" x-text="request.outlet_tujuan || '-'"></td>
                    <td class="px-4 py-3">
                      {{-- Summary jumlah item --}}
                      <div class="text-xs font-semibold text-slate-700"
                           x-text="(request.item_count || 1) + ' jenis item, total ' + (request.total_qty || 0) + ' unit'"></div>
                      {{-- Expand toggle --}}
                      <button @click="request._expanded = !request._expanded"
                              class="text-xs text-primary-600 hover:underline mt-0.5 flex items-center gap-1">
                        <i class='bx' :class="request._expanded ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
                        <span x-text="request._expanded ? 'Sembunyikan' : 'Lihat detail'"></span>
                      </button>
                      {{-- Detail items --}}
                      <template x-if="request._expanded">
                        <ul class="mt-1 space-y-0.5">
                          <template x-for="(item, i) in (request.items || [])" :key="i">
                            <li class="text-xs text-slate-600 flex gap-2">
                              <span class="text-slate-400" x-text="(i+1) + '.'"></span>
                              <span x-text="item.name"></span>
                              <span class="text-slate-400">—</span>
                              <span x-text="item.jumlah + (item.unit ? ' ' + item.unit : '')"></span>
                              <span class="capitalize text-slate-400" x-text="'(' + item.type + ')'"></span>
                            </li>
                          </template>
                        </ul>
                      </template>
                    </td>
                    <td class="px-4 py-3">
                      <span x-html="request.status || '-'"></span>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex flex-wrap gap-1">
                        {{-- Tombol surat jalan --}}
                        <a :href="`{{ route('admin.inventaris.transfer-gudang.surat-jalan', ':id') }}`.replace(':id', request.id)"
                           target="_blank"
                           class="inline-flex items-center gap-1 rounded-lg border border-purple-200 text-purple-700 px-2 py-1 hover:bg-purple-50 text-xs"
                           title="Surat Jalan">
                          <i class="bx bx-file-blank"></i> Surat Jalan
                        </a>
                        <template x-if="request.status_raw === 'menunggu'">
                          <div class="flex gap-1">
                            @hasPermission('inventaris.transfer-gudang.approve')
                            <button @click="approveTransfer(request.id)" 
                                    class="inline-flex items-center gap-1 rounded-lg border border-green-200 text-green-700 px-2 py-1 hover:bg-green-50 text-xs">
                              <i class="bx bx-check"></i> Setujui
                            </button>
                            <button @click="rejectTransfer(request.id)" 
                                    class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-2 py-1 hover:bg-red-50 text-xs">
                              <i class="bx bx-x"></i> Tolak
                            </button>
                            @endhasPermission
                          </div>
                        </template>
                      </div>
                    </td>
                  </tr>
                </template>
                <tr x-show="!requests || requests.length === 0 && !loadingRequests">
                  <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                    <i class='bx bx-package text-2xl mb-2 block'></i>
                    Tidak ada permintaan transfer
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition.opacity class="fixed top-4 right-4 z-50">
      <div :class="toastType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'" 
           class="px-4 py-3 rounded-xl border shadow-lg max-w-sm">
        <div class="flex items-center gap-2">
          <i :class="toastType === 'success' ? 'bx bx-check-circle text-green-600' : 'bx bx-error-circle text-red-600'"></i>
          <span x-text="toastMessage"></span>
        </div>
      </div>
    </div>
  </div>

  <script>
    function transferGudang(){
      return {
        // State management
        outlets: [],
        senderItems: [],
        receiverItems: [],
        loading: false,
        submitting: false,
        openRequestList: false,
        requests: [],
        pendingCount: 0,
        loadingRequests: false,
        requestStatusFilter: 'ALL',
        
        // Selection
        sender: '',
        receiver: '',
        tab: 'produk',
        tabs: [
          {key:'produk',label:'Produk'},
          {key:'bahan',label:'Bahan'},
          {key:'inventori',label:'Inventori'}
        ],

        // Cart
        openCart: false,
        cart: [],

        // Toast
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init(){
          this.loading = true;
          try {
            await Promise.all([
              this.loadOutlets(),
              this.loadPendingCount()
            ]);
          } catch (error) {
            this.showToastMessage('Gagal memuat data outlet', 'error');
            console.error('Error during initialization:', error);
          } finally {
            this.loading = false;
          }
        },

        async loadRequests(){
          this.loadingRequests = true;
          try {
            const params = new URLSearchParams();
            if (this.requestStatusFilter !== 'ALL') {
              params.append('status', this.requestStatusFilter);
            }

            const response = await fetch(`{{ route("admin.inventaris.transfer-gudang.data") }}?${params}`);
            const data = await response.json();
            this.requests = Array.isArray(data.data) ? data.data : [];
          } catch (error) {
            console.error('Error loading requests:', error);
            this.requests = [];
            this.showToastMessage('Gagal memuat daftar permintaan', 'error');
          } finally {
            this.loadingRequests = false;
          }
        },

        // Method untuk load count permintaan pending
        async loadPendingCount(){
          try {
            const response = await fetch(`{{ route("admin.inventaris.transfer-gudang.data") }}?status=menunggu`);
            const data = await response.json();
            this.pendingCount = data.data.length;
          } catch (error) {
            console.error('Error loading pending count:', error);
          }
        },

        // Method approveTransfer
        async approveTransfer(requestId){
          if (!confirm('Setujui permintaan transfer ini?')) return;

          try {
            // PERBAIKAN: Gunakan route yang benar tanpa empty string
            const response = await fetch(`{{ route("admin.inventaris.transfer-gudang.approve", ":id") }}`.replace(':id', requestId), {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message, 'success');
              await this.loadRequests();
              await this.loadPendingCount();
              if (this.sender) this.loadSenderItems();
              if (this.receiver) this.loadReceiverItems();
            } else {
              this.showToastMessage(result.error || 'Gagal menyetujui permintaan', 'error');
            }
          } catch (error) {
            console.error('Error approving transfer:', error);
            this.showToastMessage('Gagal menyetujui permintaan', 'error');
          }
        },

        // Method rejectTransfer
        async rejectTransfer(requestId){
          if (!confirm('Tolak permintaan transfer ini?')) return;

          try {
            // PERBAIKAN: Gunakan route yang benar tanpa empty string
            const response = await fetch(`{{ route("admin.inventaris.transfer-gudang.reject", ":id") }}`.replace(':id', requestId), {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message, 'success');
              await this.loadRequests();
              await this.loadPendingCount();
            } else {
              this.showToastMessage(result.error || 'Gagal menolak permintaan', 'error');
            }
          } catch (error) {
            console.error('Error rejecting transfer:', error);
            this.showToastMessage('Gagal menolak permintaan', 'error');
          }
        },

        async loadOutlets(){
          try {
            const response = await fetch('{{ route("admin.inventaris.transfer-gudang.outlets") }}');
            const data = await response.json();
            this.outlets = data;
          } catch (error) {
            console.error('Error loading outlets:', error);
            throw error;
          }
        },

        async loadSenderItems(){
          if (!this.sender) {
            this.senderItems = [];
            return;
          }
          try {
            const response = await fetch(`{{ route("admin.inventaris.transfer-gudang.items") }}?outlet_id=${this.sender}&type=${this.tab}`);
            const data = await response.json();
            this.senderItems = data;
          } catch (error) {
            console.error('Error loading sender items:', error);
            this.showToastMessage('Gagal memuat data items pengirim', 'error');
          }
        },

        async loadReceiverItems(){
          if (!this.receiver) {
            this.receiverItems = [];
            return;
          }
          try {
            const response = await fetch(`{{ route("admin.inventaris.transfer-gudang.items") }}?outlet_id=${this.receiver}&type=${this.tab}`);
            const data = await response.json();
            this.receiverItems = data;
          } catch (error) {
            console.error('Error loading receiver items:', error);
            this.showToastMessage('Gagal memuat data items penerima', 'error');
          }
        },

        changeTab(newTab){
          this.tab = newTab;
          if (this.sender) this.loadSenderItems();
          if (this.receiver) this.loadReceiverItems();
        },

        getOutletName(outletId){
          const outlet = this.outlets.find(o => o.id == outletId);
          return outlet ? outlet.name : '-';
        },

        // Cek apakah item sudah ada di cart
        isInCart(itemId){
          const cid = `${itemId}::${this.sender}->${this.receiver}`;
          return this.cart.some(c => c.cid === cid);
        },

        addToCart(it){
          if(this.sender === this.receiver){
            this.showToastMessage('Outlet pengirim & penerima tidak boleh sama.', 'error');
            return;
          }
          if(it.stock <= 0){ 
            this.showToastMessage('Stok item tidak mencukupi', 'error');
            return;
          }

          const cid = `${it.id}::${this.sender}->${this.receiver}`;
          const existingItem = this.cart.find(c => c.cid === cid);
          
          if(existingItem){
            if(existingItem.qty < existingItem.stock) {
              existingItem.qty++;
            } else {
              this.showToastMessage('Jumlah melebihi stok tersedia', 'error');
            }
          } else {
            this.cart.push({
              cid,
              id: it.id,
              name: it.name,
              type: it.type,
              from: this.sender,
              to: this.receiver,
              qty: 1,
              stock: it.stock,
              original_id: it.original_id
            });
          }
        },

        removeFromCart(i){ 
          this.cart.splice(i,1); 
        },

        incQty(i){ 
          if(this.cart[i].qty < this.cart[i].stock) this.cart[i].qty++; 
        },
        
        decQty(i){ 
          this.cart[i].qty = Math.max(1, this.cart[i].qty-1); 
        },

        canSubmit(){
          if(!this.cart.length) return false;
          if(this.sender === this.receiver) return false;
          return this.cart.every(c => c.qty > 0 && c.qty <= c.stock);
        },

        async submitTransfer(){
          if(!this.canSubmit()) return;

          this.submitting = true;
          try {
            const formData = {
              outlet_asal_id: this.sender,
              outlet_tujuan_id: this.receiver,
              items: this.cart.map(item => ({
                id: item.id,
                type: item.type,
                quantity: item.qty,
                original_id: item.original_id,
                name: item.name
              }))
            };

            const response = await fetch('{{ route("admin.inventaris.transfer-gudang.store") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message, 'success');
              this.cart = [];
              this.openCart = false;
              // Reload items to reflect new stock
              this.loadSenderItems();
              this.loadReceiverItems();
            } else {
              this.showToastMessage(result.error || 'Gagal mengirim permintaan', 'error');
            }
          } catch (error) {
            console.error('Error submitting transfer:', error);
            this.showToastMessage('Gagal mengirim permintaan transfer', 'error');
          } finally {
            this.submitting = false;
          }
        },

        exportPdf(){
          window.open('{{ route("admin.inventaris.transfer-gudang.export.pdf") }}', '_blank');
        },

        exportExcel(){
          window.open('{{ route("admin.inventaris.transfer-gudang.export.excel") }}', '_blank');
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 4000);
        }
      }
    }


  </script>
</x-layouts.admin>
