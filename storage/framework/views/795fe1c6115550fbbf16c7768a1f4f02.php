
<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Point of Sales']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Point of Sales']); ?>

<div x-data="posApp()" x-init="init()" class="space-y-4">

  
  <div x-data="{ 
    keepAlive() {
      // Ping server every 5 minutes to keep session alive
      fetch('<?php echo e(route("admin.dashboard")); ?>', {
        method: 'HEAD',
        credentials: 'same-origin'
      }).catch(() => {
        console.warn('Session keep-alive failed');
      });
    }
  }" x-init="setInterval(keepAlive, 300000)"></div>

  
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold">Point of Sales</h1>
        <span class="hidden md:inline text-slate-400">•</span>
        <div class="text-sm text-slate-600">Kasir: <b x-text="state.cashier"></b></div>
        <button x-on:click="showHistoryModal = true; loadHistory()" class="text-xs px-3 py-1 rounded-full border border-slate-200 hover:bg-slate-50">
          📋 History
        </button>
        <button x-on:click="showCoaModal = true" class="text-xs px-3 py-1 rounded-full border border-slate-200 hover:bg-slate-50">
          ⚙️ Setting COA
        </button>
      </div>
      <div class="flex items-center gap-3">
        <div class="text-sm text-slate-600">
            <span x-text="nowStr.split(',')[0]"></span><br>
            <span class="text-xs text-slate-500">Transaksi: <span x-text="formatDateDDMMYYYY(state.transactionDate)"></span></span>
          </div>
        <select x-model="state.outlet" @change="onOutletChange()" class="h-10 rounded-xl border border-slate-200 px-3">
          <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>
  </section>

  <section class="grid grid-cols-1 lg:grid-cols-5 gap-4">
    
    <div class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3">
        <div class="md:col-span-2 relative">
          <input type="text" placeholder="Cari SKU/Nama produk… (Enter)" x-model="ui.search" x-on:keydown.enter.prevent="quickAdd()" class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-3" />
          <i class='bx bx-search text-slate-400 absolute left-3 top-1/2 -translate-y-1/2'></i>
        </div>
        <input type="text" placeholder="Scan Barcode (SKU)…" x-model="ui.barcode" x-on:keydown.enter.prevent="scanAdd()" class="h-11 w-full rounded-xl border border-slate-200 px-3" />
      </div>

      <div class="flex flex-wrap gap-2 mb-3">
        <button class="px-3 h-8 rounded-full border text-sm" :class="ui.cat==='all' ? 'bg-primary-100 text-primary-700 border-primary-200' : 'border-slate-200 text-slate-600 hover:bg-slate-50'" x-on:click="ui.cat='all'">Semua</button>
        <template x-for="c in categories" :key="c">
          <button class="px-3 h-8 rounded-full border text-sm" :class="ui.cat===c ? 'bg-primary-100 text-primary-700 border-primary-200' : 'border-slate-200 text-slate-600 hover:bg-slate-50'" x-on:click="ui.cat=c" x-text="c"></button>
        </template>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
        
        <template x-if="products.length === 0">
          <div class="col-span-full flex flex-col items-center justify-center py-12 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300">
            <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-primary-600 mb-4"></div>
            <p class="text-lg font-semibold text-slate-700 mb-1">Memuat produk...</p>
            <p class="text-sm text-slate-500" x-text="'Outlet: ' + state.outlet"></p>
            <p class="text-xs text-slate-400 mt-2">Jika loading terlalu lama, cek console (F12)</p>
          </div>
        </template>
        
        
        <template x-for="p in filteredProducts()" :key="p.sku">
          <button class="text-left rounded-2xl border p-3 shadow-sm flex flex-col relative" 
                  :class="p.stock > 0 ? 'border-slate-200 bg-white hover:bg-slate-50' : 'border-red-200 bg-red-50 opacity-75'"
                  x-on:click="addItem(p)">
            
            
            <div x-show="p.stock <= 0" class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium z-10">
              HABIS
            </div>
            
            <div class="w-full aspect-square bg-slate-100 rounded-lg mb-2 overflow-hidden flex items-center justify-center"
                 :class="p.stock <= 0 ? 'grayscale' : ''">
              <img x-show="p.image" :src="p.image" :alt="p.name" class="w-full h-full object-cover" x-on:error="$event.target.style.display='none'">
              <div x-show="!p.image" class="text-slate-400 text-center p-2">
                <i class='bx bx-image text-4xl'></i>
                <div class="text-xs mt-1">No Image</div>
              </div>
            </div>
            <div class="flex justify-center mb-2 bg-white p-1 rounded">
              <svg class="barcode" :data-code="p.sku" style="max-width: 100%;"></svg>
            </div>
            <div class="font-medium text-sm line-clamp-2" x-text="p.name"></div>
            <div class="text-xs text-slate-500 mt-1" x-text="`SKU: ${p.sku}`"></div>
            <div class="mt-2">
              <div class="font-bold" :class="p.stock > 0 ? 'text-primary-700' : 'text-slate-500'" x-text="idr(p.price)"></div>
              <div x-show="p.has_discount" class="text-xs text-green-600 mt-1">
                <span x-text="p.discount_info"></span>
                <div class="line-through text-slate-400" x-text="'Normal: ' + idr(p.original_price)"></div>
              </div>
            </div>
            <div class="text-xs text-slate-500" x-text="p.category"></div>
            <div class="text-xs font-medium" :class="p.stock > 0 ? 'text-green-600' : 'text-red-600'" x-text="`Stok: ${p.stock}`"></div>
          </button>
        </template>
      </div>
    </div>

    
    <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-4 shadow-card flex flex-col">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
        <div class="relative">
          <label class="text-xs text-slate-500">Customer</label>
          <input type="text" x-model="ui.customerSearch" x-on:input="searchCustomer()" x-on:focus="searchCustomer()" x-on:keyup="searchCustomer()" placeholder="Cari customer..." class="h-10 w-full rounded-xl border border-slate-200 px-3">
          <div x-show="ui.customerDropdown && filteredCustomers().length > 0" x-on:click.away="ui.customerDropdown=false" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
            <div class="p-2">
              <button x-on:click="selectCustomer(null)" class="w-full text-left px-3 py-2 hover:bg-slate-50 rounded-lg">
                <div class="font-medium">Pelanggan Umum</div>
              </button>
              <template x-for="c in filteredCustomers()" :key="c.id">
                <button x-on:click="selectCustomer(c)" class="w-full text-left px-3 py-2 hover:bg-slate-50 rounded-lg">
                  <div class="flex items-center justify-between">
                    <div class="font-medium" x-text="c.name"></div>
                    <span x-show="c.tipe_name" class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700" x-text="c.tipe_name"></span>
                  </div>
                  <div class="text-xs text-slate-500" x-text="c.telepon"></div>
                  <div class="text-xs" :class="c.piutang > 0 ? 'text-red-600' : 'text-green-600'" x-text="c.piutang > 0 ? 'Piutang: ' + idr(c.piutang) : 'Tidak ada piutang'"></div>
                </button>
              </template>
            </div>
          </div>
          <div x-show="state.customerId" class="mt-1 text-xs text-slate-600">
            <span x-text="selectedCustomer()?.name"></span>
            <span x-show="selectedCustomer()?.tipe_name" class="ml-2 px-2 py-0.5 rounded-full bg-purple-100 text-purple-700" x-text="selectedCustomer()?.tipe_name"></span>
            <span x-show="selectedCustomer()?.piutang > 0" class="text-red-600 ml-2" x-text="'(Piutang: ' + idr(selectedCustomer()?.piutang || 0) + ')'"></span>
          </div>
        </div>
        <div>
          <label class="text-xs text-slate-500">Tanggal Transaksi</label>
          <input type="date" x-model="state.transactionDate" class="h-10 w-full rounded-xl border border-slate-200 px-3" :min="getMinDueDate()" :max="getMaxDueDate()">
        </div>
        <div>
          <label class="text-xs text-slate-500">Catatan</label>
          <input x-model="state.note" class="h-10 w-full rounded-xl border border-slate-200 px-3" placeholder="Catatan struk (opsional)" />
        </div>
      </div>

      <div class="grow overflow-y-auto mb-3">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 sticky top-0">
            <tr>
              <th class="px-3 py-2 text-left">Item</th>
              <th class="px-3 py-2 text-center w-24">Qty</th>
              <th class="px-3 py-2 text-right w-28">Harga</th>
              <th class="px-3 py-2 text-right w-28">Subtotal</th>
              <th class="px-3 py-2 w-10"></th>
            </tr>
          </thead>
          <tbody>
            <template x-if="cart.length===0">
              <tr><td colspan="5" class="px-3 py-6 text-center text-slate-500">Belum ada item</td></tr>
            </template>
            <template x-for="(c,i) in cart" :key="c.sku">
              <tr class="border-t">
                <td class="px-3 py-2">
                  <div class="font-medium" x-text="c.name"></div>
                  <div class="text-xs text-slate-500" x-text="c.sku"></div>
                  <div x-show="c.has_discount" class="text-xs text-green-600 mt-1">
                    <span x-text="c.discount_info"></span>
                    <span class="line-through text-slate-400 ml-1" x-text="'(' + idr(c.original_price) + ')'"></span>
                  </div>
                </td>
                <td class="px-3 py-2">
                  <div class="flex items-center justify-center">
                    <input type="number" min="1" x-model.number="c.qty" x-on:change="recalc()" class="w-20 h-8 rounded border border-slate-200 text-center focus:border-primary-300 focus:ring-2 focus:ring-primary-100">
                  </div>
                </td>
                <td class="px-3 py-2 text-right" x-text="idr(c.price)"></td>
                <td class="px-3 py-2 text-right" x-text="idr(c.price*c.qty)"></td>
                <td class="px-3 py-2 text-center">
                  <button class="w-7 h-7 rounded border border-rose-200 text-rose-600 hover:bg-rose-50" x-on:click="removeItem(i)">
                    <i class='bx bx-trash'></i>
                  </button>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div class="border-t pt-3">
        <div class="grid grid-cols-2 gap-2 mb-3">
          <div>
            <label class="text-xs text-slate-500">Diskon</label>
            <div class="flex gap-2">
              <input type="number" min="0" x-model.number="state.discountRp" x-on:change="recalc()" placeholder="Rp" class="h-10 w-full rounded-xl border border-slate-200 px-3">
              <input type="number" min="0" max="100" x-model.number="state.discountPct" x-on:change="recalc()" placeholder="%" class="h-10 w-24 rounded-xl border border-slate-200 px-3">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" class="rounded" x-model="state.tax10" x-on:change="recalc()"> PPN 10%
            </label>
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" class="rounded" x-model="state.isBon" x-on:change="recalc(); toggleBonForm()"> Bon (Piutang)
            </label>
          </div>
        </div>

        
        <div x-show="state.isBon" x-transition class="mb-3">
          <label class="text-xs text-slate-500 mb-1 block">Tanggal Jatuh Tempo</label>
          <input type="date" x-model="state.dueDate" class="h-10 w-full rounded-xl border border-slate-200 px-3" :min="getMinDueDate()" :max="getMaxDueDate()">
        </div>

        <div class="text-sm space-y-1 mb-3">
          <div class="flex justify-between"><span>Subtotal</span><b x-text="idr(total.subtotal)"></b></div>
          <div class="flex justify-between"><span>Diskon</span><b x-text="idr(total.discount)"></b></div>
          <div class="flex justify-between" x-show="state.tax10"><span>PPN 10%</span><b x-text="idr(total.tax)"></b></div>
          <div class="flex justify-between text-lg border-t pt-2">
            <span>Total Bayar</span><b x-text="idr(total.grand)"></b>
          </div>
        </div>

        <div class="space-y-2 mb-3" x-show="!state.isBon">
          <div>
            <label class="text-xs text-slate-500 mb-1 block">Metode Pembayaran</label>
            <select x-model="pay.method" class="h-10 w-full rounded-xl border border-slate-200 px-3">
              <option value="cash">💵 Cash</option>
              <option value="transfer">🏦 Transfer</option>
              <option value="qris">📱 QRIS</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-slate-500 mb-1 block">Jumlah Bayar</label>
            <div class="grid grid-cols-3 gap-2">
              <input type="number" min="0" x-model.number="pay.tendered" x-on:input="calcChange()" class="h-10 col-span-2 rounded-xl border border-slate-200 px-3" placeholder="Uang diterima">
              <button x-on:click="pay.tendered = total.grand; calcChange()" class="h-10 rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 font-medium text-sm">
                💰 Lunas
              </button>
            </div>
          </div>
          <div class="flex justify-between text-sm bg-slate-50 p-2 rounded-lg">
            <span>Kembalian</span><b class="text-lg" x-text="idr(pay.change)"></b>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <button class="h-11 rounded-xl border border-slate-200 hover:bg-slate-50" x-on:click="holdOrder()" :disabled="cart.length===0">Tahan</button>
          <button class="h-11 rounded-xl border border-amber-200 hover:bg-amber-50" x-on:click="openHolds()">Ambil Tahanan</button>
          <button class="h-11 rounded-xl border border-rose-200 hover:bg-rose-50" x-on:click="clearCart()" :disabled="cart.length===0">Batal</button>
          <button class="h-11 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50" x-on:click="submitSale()" :disabled="cart.length===0 || (!state.isBon && pay.tendered < total.grand)">
            Bayar & Cetak
          </button>
        </div>
      </div>
    </div>
  </section>

  
  <div x-show="ui.holdOpen" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="w-full max-w-xl rounded-2xl bg-white border border-slate-200 shadow-card p-4">
        <div class="flex items-center justify-between mb-3">
          <div class="font-semibold">Order Ditahan</div>
          <button x-on:click="ui.holdOpen=false" class="w-8 h-8 rounded hover:bg-slate-100"><i class='bx bx-x'></i></button>
        </div>
        <div class="max-h-96 overflow-y-auto">
          <template x-if="holds.length===0">
            <div class="p-6 text-center text-slate-500">Belum ada order ditahan.</div>
          </template>
          <template x-for="(h,i) in holds" :key="h.id">
            <div class="border rounded-xl p-3 mb-2">
              <div class="flex items-center justify-between text-sm mb-2">
                <div><b x-text="h.note||'—'"></b><div class="text-xs text-slate-500" x-text="h.time"></div></div>
                <div class="font-semibold" x-text="idr(h.total)"></div>
              </div>
              <div class="flex gap-2">
                <button class="h-9 rounded-lg border border-slate-200 px-3 hover:bg-slate-50" x-on:click="resumeHold(i)">Ambil</button>
                <button class="h-9 rounded-lg border border-rose-200 text-rose-600 px-3 hover:bg-rose-50" x-on:click="removeHold(i)">Hapus</button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>

  
  <div x-show="showCoaModal" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-start justify-center p-4 overflow-y-auto">
      <div @click.away="showCoaModal=false" class="w-full max-w-3xl rounded-2xl bg-white border border-slate-200 shadow-lg p-6 my-4">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold">Setting COA Point of Sales</h2>
          <button x-on:click="showCoaModal=false" class="w-8 h-8 rounded hover:bg-slate-100">
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>
        
        <form x-on:submit.prevent="saveCoaSettings()">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Buku Akuntansi <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.accounting_book_id" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Buku Akuntansi</option>
                <template x-for="book in books" :key="book.id">
                  <option :value="book.id" x-text="book.name"></option>
                </template>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Kas <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.akun_kas" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Kas (Asset)</option>
                <template x-for="acc in accountsByType.asset" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">💵 Untuk pembayaran tunai (Tipe: Asset)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Bank <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.akun_bank" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Bank (Asset)</option>
                <template x-for="acc in accountsByType.asset" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">🏦 Untuk pembayaran transfer/QRIS (Tipe: Asset)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Piutang Usaha <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.akun_piutang_usaha" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Piutang (Asset)</option>
                <template x-for="acc in accountsByType.asset" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">📋 Untuk transaksi bon/piutang (Tipe: Asset)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Pendapatan Penjualan <span class="text-red-500">*</span>
              </label>
              <select x-model="coaForm.akun_pendapatan_penjualan" required class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Pendapatan (Revenue)</option>
                <template x-for="acc in accountsByType.revenue" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">💰 Pendapatan dari penjualan (Tipe: Revenue)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun PPN (Pajak Pertambahan Nilai)
              </label>
              <select x-model="coaForm.akun_ppn" class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun PPN (Liability - Opsional)</option>
                <template x-for="acc in accountsByType.liability" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">📊 Untuk mencatat PPN 10% (Tipe: Liability)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun HPP (Harga Pokok Penjualan)
              </label>
              <select x-model="coaForm.akun_hpp" class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun HPP (Expense - Opsional)</option>
                <template x-for="acc in accountsByType.expense" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">📦 Untuk mencatat HPP produk yang terjual (Tipe: Expense)</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Akun Persediaan
              </label>
              <select x-model="coaForm.akun_persediaan" class="w-full h-10 rounded-xl border border-slate-200 px-3">
                <option value="">Pilih Akun Persediaan (Asset - Opsional)</option>
                <template x-for="acc in accountsByType.asset" :key="acc.code">
                  <option :value="acc.code" x-text="`${acc.code} - ${acc.name}`"></option>
                </template>
              </select>
              <p class="text-xs text-slate-500 mt-1">📦 Untuk mengurangi nilai persediaan (Tipe: Asset)</p>
            </div>
            
            <div class="flex gap-2 pt-4 border-t">
              <button type="submit" :disabled="coaLoading" class="px-4 h-10 rounded-xl bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!coaLoading">💾 Simpan Setting</span>
                <span x-show="coaLoading">⏳ Menyimpan...</span>
              </button>
              <button type="button" x-on:click="showCoaModal=false" class="px-4 h-10 rounded-xl border border-slate-200 hover:bg-slate-50">
                Batal
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  
  <div x-show="showHistoryModal" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-start justify-center p-4 overflow-y-auto">
      <div @click.away="showHistoryModal=false" class="w-full max-w-7xl rounded-2xl bg-white border border-slate-200 shadow-lg my-4 flex flex-col">
        
        
        <div class="flex items-center justify-between p-4 border-b bg-slate-50">
          <h2 class="text-xl font-bold">📋 History Transaksi POS</h2>
          <button x-on:click="showHistoryModal=false" class="w-8 h-8 rounded hover:bg-slate-100">
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>

        
        <div class="p-4 border-b bg-white">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Status</label>
              <select x-model="historyFilter.status" @change="loadHistory()" class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
                <option value="all">Semua Status</option>
                <option value="lunas">Lunas</option>
                <option value="menunggu">Menunggu (BON)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Urutkan</label>
              <select x-model="historyFilter.sort_by" @change="loadHistory()" class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Mulai</label>
              <input type="date" x-model="historyFilter.start_date" @change="loadHistory()" class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Tanggal Akhir</label>
              <input type="date" x-model="historyFilter.end_date" @change="loadHistory()" class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-700 mb-1">Cari</label>
              <input type="text" x-model="historyFilter.search" @input.debounce.500ms="loadHistory()" placeholder="No transaksi..." class="w-full h-9 rounded-lg border border-slate-200 px-2 text-sm">
            </div>
          </div>
        </div>

        
        <div x-show="historyLoading" class="flex-1 flex items-center justify-center p-8">
          <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-3"></div>
            <p class="text-slate-600">Memuat history...</p>
          </div>
        </div>

        
        <div x-show="!historyLoading" class="flex-1 overflow-auto p-4">
          <table class="w-full text-sm">
            <thead class="bg-slate-50 sticky top-0">
              <tr>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">No Transaksi</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Tanggal</th>
                <th class="px-3 py-2 text-left font-semibold text-slate-700">Customer</th>
                <th class="px-3 py-2 text-right font-semibold text-slate-700">Total</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-700">Pembayaran/Jatuh Tempo</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-700">Status</th>
                <th class="px-3 py-2 text-center font-semibold text-slate-700">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <template x-for="item in historyData" :key="item.id">
                <tr class="hover:bg-slate-50">
                  <td class="px-3 py-2">
                    <span class="font-medium text-primary-600" x-text="item.no_transaksi"></span>
                  </td>
                  <td class="px-3 py-2 text-slate-600" x-text="formatDateTime(item.tanggal)"></td>
                  <td class="px-3 py-2">
                    <span x-text="item.member ? item.member.nama : 'Umum'"></span>
                  </td>
                  <td class="px-3 py-2 text-right font-semibold" x-text="idr(item.total)"></td>
                  <td class="px-3 py-2 text-center">
                    
                    <div x-show="item.status === 'menunggu' && item.piutang && item.piutang.tanggal_jatuh_tempo">
                      <div class="text-xs text-slate-500 mb-1">Jatuh Tempo:</div>
                      <div class="text-xs font-medium text-orange-600" x-text="item.piutang && item.piutang.tanggal_jatuh_tempo ? formatDate(item.piutang.tanggal_jatuh_tempo) : '-'"></div>
                    </div>
                    
                    <div x-show="item.status === 'lunas'">
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" 
                            :class="{
                              'bg-green-100 text-green-800': item.jenis_pembayaran === 'cash',
                              'bg-blue-100 text-blue-800': item.jenis_pembayaran === 'transfer',
                              'bg-purple-100 text-purple-800': item.jenis_pembayaran === 'qris'
                            }">
                        <span x-show="item.jenis_pembayaran === 'cash'">💵 Cash</span>
                        <span x-show="item.jenis_pembayaran === 'transfer'">🏦 Transfer</span>
                        <span x-show="item.jenis_pembayaran === 'qris'">📱 QRIS</span>
                      </span>
                    </div>
                    
                    <div x-show="item.status === 'menunggu' && (!item.piutang || !item.piutang.tanggal_jatuh_tempo)">
                      <span class="text-xs text-slate-500">BON</span>
                    </div>
                  </td>
                  <td class="px-3 py-2 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                          :class="{
                            'bg-green-100 text-green-800': item.status === 'lunas',
                            'bg-orange-100 text-orange-800': item.status === 'menunggu'
                          }">
                      <span x-show="item.status === 'lunas'">✅ Lunas</span>
                      <span x-show="item.status === 'menunggu'">⏳ BON</span>
                    </span>
                  </td>
                  <td class="px-3 py-2 text-center">
                    <button @click="printHistoryItem(item.id)" class="inline-flex items-center gap-1 px-2 py-1 rounded bg-primary-50 text-primary-600 hover:bg-primary-100 text-xs">
                      <i class='bx bx-printer'></i> Print
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>

          
          <div x-show="historyData.length === 0" class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
              <i class='bx bx-receipt text-3xl text-slate-400'></i>
            </div>
            <p class="text-slate-600 font-medium">Tidak ada transaksi</p>
            <p class="text-sm text-slate-500 mt-1">Belum ada history transaksi untuk filter yang dipilih</p>
          </div>
        </div>

        
        <div class="p-4 border-t bg-slate-50 flex items-center justify-between">
          <div class="text-sm text-slate-600">
            Total: <b x-text="historyData.length"></b> transaksi
          </div>
          <button x-on:click="showHistoryModal=false" class="px-4 h-9 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-sm">
            Tutup
          </button>
        </div>

      </div>
    </div>
  </div>

  
  <div x-show="showPrintModal" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-start justify-center p-4 overflow-y-auto">
      <div class="w-full max-w-5xl rounded-2xl bg-white border border-slate-200 shadow-lg my-4">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b bg-primary-50">
          <h2 class="text-xl font-bold text-primary-900">✅ Transaksi Berhasil - Cetak Struk</h2>
          <button x-on:click="closePrintModal()" class="w-8 h-8 rounded hover:bg-primary-100">
            <i class='bx bx-x text-2xl'></i>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6">
          <!-- Pilihan Jenis Nota -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Jenis Nota:</label>
            <div class="flex gap-3">
              <button x-on:click="updatePreview('besar')" class="flex-1 h-12 rounded-xl border-2 hover:bg-slate-50 transition" :class="printPreviewUrl.includes('type=besar') ? 'border-primary-500 bg-primary-50 text-primary-700 font-semibold' : 'border-slate-200'">
                📄 Nota Besar (A4)
              </button>
              <button x-on:click="updatePreview('kecil')" class="flex-1 h-12 rounded-xl border-2 hover:bg-slate-50 transition" :class="printPreviewUrl.includes('type=kecil') ? 'border-primary-500 bg-primary-50 text-primary-700 font-semibold' : 'border-slate-200'">
                🧾 Nota Kecil (Thermal)
              </button>
            </div>
          </div>

          <!-- Preview -->
          <div class="border-2 border-slate-200 rounded-xl overflow-hidden bg-slate-50" style="height: 70vh; max-height: 600px; display: flex; flex-direction: column;">
            <iframe :src="printPreviewUrl" class="w-full h-full" frameborder="0" style="display: block; margin: 0; padding: 0; border: none; flex: 1;"></iframe>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex gap-3 p-4 border-t bg-slate-50">
          <button x-on:click="printNota(printPreviewUrl.includes('type=kecil') ? 'kecil' : 'besar')" class="flex-1 h-12 rounded-xl bg-primary-600 text-white hover:bg-primary-700 font-semibold">
            🖨️ Cetak Sekarang
          </button>
          <button x-on:click="closePrintModal()" class="px-6 h-12 rounded-xl border border-slate-200 hover:bg-slate-100">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  
  <div x-show="showStockOutModal" x-transition class="fixed inset-0 bg-black/30 z-50" style="display: none;">
    <div class="absolute inset-0 flex items-start justify-center p-4 pt-20">
      <div @click.away="closeStockModal()" class="w-full max-w-lg rounded-2xl bg-white border border-red-200 shadow-lg">
        
        
        <div class="flex items-center justify-between p-4 border-b bg-red-50">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
              <i class='bx bx-x-circle text-2xl text-red-600'></i>
            </div>
            <h2 class="text-lg font-bold text-red-900">Stok Habis</h2>
          </div>
          <button x-on:click="closeStockModal()" class="w-8 h-8 rounded hover:bg-red-100">
            <i class='bx bx-x text-xl text-red-600'></i>
          </button>
        </div>

        
        <div class="p-6">
          <div x-show="stockOutProduct" class="text-center">
            
            <div class="w-20 h-20 mx-auto mb-4 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center">
              <img x-show="stockOutProduct?.image" :src="stockOutProduct?.image" :alt="stockOutProduct?.name" class="w-full h-full object-cover grayscale">
              <div x-show="!stockOutProduct?.image" class="text-slate-400">
                <i class='bx bx-image text-3xl'></i>
              </div>
            </div>
            
            
            <h3 class="font-semibold text-lg text-slate-900 mb-1" x-text="stockOutProduct?.name"></h3>
            <p class="text-sm text-slate-500 mb-2" x-text="'SKU: ' + (stockOutProduct?.sku || '')"></p>
            <p class="text-lg font-bold text-slate-700 mb-4" x-text="stockOutProduct ? idr(stockOutProduct.price) : ''"></p>
            
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
              <div class="flex items-center gap-2 text-red-700 mb-2">
                <i class='bx bx-error-circle text-xl'></i>
                <span class="font-semibold">Produk Tidak Tersedia</span>
              </div>
              <p class="text-sm text-red-600">
                Maaf, produk ini sedang habis stok di outlet ini. 
                Silakan pilih produk lain atau hubungi admin untuk restock.
              </p>
            </div>
            
            
            <div class="text-center">
              <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-medium">
                <i class='bx bx-package'></i>
                Stok: 0
              </span>
            </div>
          </div>
        </div>

        
        <div class="p-4 border-t bg-slate-50">
          <button x-on:click="closeStockModal()" class="w-full h-10 rounded-xl bg-slate-600 text-white hover:bg-slate-700 font-medium">
            Mengerti
          </button>
        </div>

      </div>
    </div>
  </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>


<script>
// Set up window variables for POS initialization
window.posInitialOutlet = <?php echo e($selectedOutlet); ?>;
window.posCSRFToken = '<?php echo e(csrf_token()); ?>';

// Set up route variables for POS API calls
window.posProductsRoute = '<?php echo e(route("admin.penjualan.pos.products")); ?>';
window.posCustomersRoute = '<?php echo e(route("admin.penjualan.pos.customers")); ?>';
window.posCustomerTypePricesRoute = '<?php echo e(route("admin.penjualan.pos.customer-type-prices")); ?>';
window.posAccountingBooksRoute = '<?php echo e(route("finance.accounting-books.data")); ?>';
window.posChartOfAccountsRoute = '<?php echo e(route("finance.chart-of-accounts.data")); ?>';
window.posCoaSettingsRoute = '<?php echo e(route("admin.penjualan.pos.coa.settings")); ?>';
window.posCoaSettingsUpdateRoute = '<?php echo e(route("admin.penjualan.pos.coa.settings.update")); ?>';
window.posStoreRoute = '<?php echo e(route("admin.penjualan.pos.store")); ?>';
window.posPrintRoute = '<?php echo e(route("admin.penjualan.pos.print", ":id")); ?>';
window.posHistoryRoute = '<?php echo e(route("admin.penjualan.pos.history.data")); ?>';
window.posDashboardRoute = '<?php echo e(route("admin.dashboard")); ?>';
window.posLoginRoute = '<?php echo e(route("login")); ?>';

console.log('✅ [POS] Initialization variables set up for separate pos.js file');
</script>

<style>
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>


<script>
console.log("🔍 [DEBUG] POS page script loaded");

// Check Alpine.js availability
function checkAlpine() {
    console.log("🔍 [DEBUG] Checking Alpine.js...");
    console.log("   - Alpine available:", typeof Alpine !== "undefined");
    console.log("   - Window.Alpine:", window.Alpine);
    
    if (typeof Alpine !== "undefined") {
        console.log("✅ [DEBUG] Alpine.js is available");
        return true;
    } else {
        console.log("❌ [DEBUG] Alpine.js not available");
        return false;
    }
}

// Check POS elements
function checkPosElements() {
    console.log("🔍 [DEBUG] Checking POS elements...");
    
    const posElement = document.querySelector('[x-data="posApp()"]');
    console.log("   - POS element found:", !!posElement);
    
    const customerInput = document.querySelector('input[x-model="ui.customerSearch"]');
    console.log("   - Customer input found:", !!customerInput);
    
    if (customerInput) {
        console.log("   - Customer input events:");
        console.log("     - x-model:", customerInput.getAttribute("x-model"));
        console.log("     - x-on:input:", customerInput.getAttribute("x-on:input"));
        console.log("     - x-on:focus:", customerInput.getAttribute("x-on:focus"));
    }
    
    return {posElement, customerInput};
}

// Test customer input manually
function testCustomerInput() {
    console.log("🧪 [TEST] Testing customer input manually...");
    
    const customerInput = document.querySelector('input[x-model="ui.customerSearch"]');
    if (customerInput) {
        console.log("✅ [TEST] Customer input found, adding test events");
        
        customerInput.addEventListener("input", function(e) {
            console.log("📝 [TEST] Manual input event:", e.target.value);
        });
        
        customerInput.addEventListener("focus", function(e) {
            console.log("🎯 [TEST] Manual focus event");
        });
        
        customerInput.addEventListener("keyup", function(e) {
            console.log("⌨️ [TEST] Manual keyup event:", e.target.value);
        });
    } else {
        console.log("❌ [TEST] Customer input not found");
    }
}

// Run checks
document.addEventListener("DOMContentLoaded", function() {
    console.log("📄 [DEBUG] DOM loaded, running checks...");
    
    setTimeout(() => {
        checkAlpine();
        checkPosElements();
        testCustomerInput();
    }, 1000);
    
    // Check again after 3 seconds
    setTimeout(() => {
        console.log("🔄 [DEBUG] Re-checking after 3 seconds...");
        checkAlpine();
        checkPosElements();
    }, 3000);
});

// Alpine.js event listeners
document.addEventListener("alpine:init", () => {
    console.log("🏔️ [ALPINE] Alpine.js init event fired");
});

document.addEventListener("alpine:initialized", () => {
    console.log("🏔️ [ALPINE] Alpine.js fully initialized");
});
</script>

<script>
// Fallback untuk memastikan posApp tersedia
document.addEventListener("DOMContentLoaded", function() {
    console.log("🔍 [POS] Checking posApp availability...");
    
    // Check if Alpine.js is loaded
    let alpineCheckCount = 0;
    const maxChecks = 50; // 5 seconds max
    
    function checkAlpineAndPosApp() {
        alpineCheckCount++;
        
        if (typeof Alpine !== "undefined") {
            console.log("✅ [POS] Alpine.js is available");
            
            // Check if posApp is registered
            if (Alpine.data && typeof Alpine.data === "function") {
                console.log("✅ [POS] Alpine.data is available");
                
                // Try to get posApp
                try {
                    const testElement = document.createElement("div");
                    testElement.setAttribute("x-data", "posApp()");
                    console.log("✅ [POS] posApp function test passed");
                } catch (error) {
                    console.error("❌ [POS] posApp function test failed:", error);
                    
                    // Show user-friendly error
                    const posElement = document.querySelector('[x-data="posApp()"]');
                    if (posElement) {
                        posElement.innerHTML = `
                            <div class="flex items-center justify-center min-h-screen">
                                <div class="text-center p-8 bg-red-50 border border-red-200 rounded-xl max-w-md">
                                    <div class="text-red-600 text-6xl mb-4">⚠️</div>
                                    <h2 class="text-xl font-bold text-red-800 mb-2">Error Loading POS</h2>
                                    <p class="text-red-600 mb-4">Terjadi kesalahan saat memuat sistem POS.</p>
                                    <button onclick="window.location.reload()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        🔄 Refresh Halaman
                                    </button>
                                </div>
                            </div>
                        `;
                    }
                }
            } else {
                console.warn("⚠️ [POS] Alpine.data not available yet");
                if (alpineCheckCount < maxChecks) {
                    setTimeout(checkAlpineAndPosApp, 100);
                }
            }
        } else {
            console.warn("⚠️ [POS] Alpine.js not loaded yet, attempt", alpineCheckCount);
            if (alpineCheckCount < maxChecks) {
                setTimeout(checkAlpineAndPosApp, 100);
            } else {
                console.error("❌ [POS] Alpine.js failed to load after", maxChecks, "attempts");
                alert("Gagal memuat sistem POS. Silakan refresh halaman.");
            }
        }
    }
    
    // Start checking
    setTimeout(checkAlpineAndPosApp, 100);
});

// Additional error handling for Alpine.js
window.addEventListener("error", function(event) {
    if (event.message && event.message.includes("posApp")) {
        console.error("❌ [POS] posApp error detected:", event.message);
        console.error("❌ [POS] Error details:", event);
        
        // Show user-friendly error
        setTimeout(() => {
            if (confirm("Terjadi kesalahan pada sistem POS. Refresh halaman?")) {
                window.location.reload();
            }
        }, 1000);
    }
});
</script>


<script>
// Fallback untuk memastikan posApp tersedia
document.addEventListener("DOMContentLoaded", function() {
    console.log("🔍 [POS] Checking posApp availability...");
    
    // Check if Alpine.js is loaded
    let alpineCheckCount = 0;
    const maxChecks = 50; // 5 seconds max
    
    function checkAlpineAndPosApp() {
        alpineCheckCount++;
        
        if (typeof Alpine !== "undefined") {
            console.log("✅ [POS] Alpine.js is available");
            
            // Check if posApp is registered
            if (Alpine.data && typeof Alpine.data === "function") {
                console.log("✅ [POS] Alpine.data is available");
                
                // Try to get posApp
                try {
                    const testElement = document.createElement("div");
                    testElement.setAttribute("x-data", "posApp()");
                    console.log("✅ [POS] posApp function test passed");
                } catch (error) {
                    console.error("❌ [POS] posApp function test failed:", error);
                    
                    // Show user-friendly error
                    const posElement = document.querySelector('[x-data="posApp()"]');
                    if (posElement) {
                        posElement.innerHTML = `
                            <div class="flex items-center justify-center min-h-screen">
                                <div class="text-center p-8 bg-red-50 border border-red-200 rounded-xl max-w-md">
                                    <div class="text-red-600 text-6xl mb-4">⚠️</div>
                                    <h2 class="text-xl font-bold text-red-800 mb-2">Error Loading POS</h2>
                                    <p class="text-red-600 mb-4">Terjadi kesalahan saat memuat sistem POS.</p>
                                    <button onclick="window.location.reload()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        🔄 Refresh Halaman
                                    </button>
                                </div>
                            </div>
                        `;
                    }
                }
            } else {
                console.warn("⚠️ [POS] Alpine.data not available yet");
                if (alpineCheckCount < maxChecks) {
                    setTimeout(checkAlpineAndPosApp, 100);
                }
            }
        } else {
            console.warn("⚠️ [POS] Alpine.js not loaded yet, attempt", alpineCheckCount);
            if (alpineCheckCount < maxChecks) {
                setTimeout(checkAlpineAndPosApp, 100);
            } else {
                console.error("❌ [POS] Alpine.js failed to load after", maxChecks, "attempts");
                alert("Gagal memuat sistem POS. Silakan refresh halaman.");
            }
        }
    }
    
    // Start checking
    setTimeout(checkAlpineAndPosApp, 100);
});

// Additional error handling for Alpine.js
window.addEventListener("error", function(event) {
    if (event.message && event.message.includes("posApp")) {
        console.error("❌ [POS] posApp error detected:", event.message);
        console.error("❌ [POS] Error details:", event);
        
        // Show user-friendly error
        setTimeout(() => {
            if (confirm("Terjadi kesalahan pada sistem POS. Refresh halaman?")) {
                window.location.reload();
            }
        }, 1000);
    }
});
</script>

<style>
/* POS Page - Force Dropdown Text to Display Fully */
/* CRITICAL: Override all truncation styles */

/* All select elements in POS page */
select,
select option {
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
    word-wrap: break-word !important;
    word-break: break-word !important;
}

/* CRITICAL: Reduce font size for selected text */
select:not([multiple]) {
    font-size: 11px !important;
    padding: 8px 32px 8px 10px !important;
    line-height: 1.3 !important;
}

/* Options can be slightly larger */
select option {
    font-size: 12px !important;
    padding: 10px 12px !important;
    line-height: 1.4 !important;
}

/* Specific targeting for POS dropdowns */
select[x-model*="outlet"],
select[x-model*="method"],
select[x-model*="accounting_book"],
select[x-model*="akun"],
select[x-model*="historyFilter"],
select[x-model*="coaForm"] {
    white-space: normal !important;
    max-width: 100% !important;
    width: 100% !important;
    overflow: visible !important;
    text-overflow: clip !important;
    font-size: 11px !important;
    min-width: 200px !important;
}

/* Force options to wrap */
select[x-model*="outlet"] option,
select[x-model*="method"] option,
select[x-model*="accounting_book"] option,
select[x-model*="akun"] option,
select[x-model*="historyFilter"] option,
select[x-model*="coaForm"] option {
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
    padding: 10px 12px !important;
    min-height: 2.5em !important;
    word-wrap: break-word !important;
    display: block !important;
    line-height: 1.5 !important;
    font-size: 12px !important;
}

/* Header outlet dropdown - make wider and smaller font */
section .flex select[x-model*="outlet"] {
    max-width: 320px !important;
    min-width: 240px !important;
    width: auto !important;
    font-size: 11px !important;
}

/* Payment method dropdown in cart */
.space-y-2 select[x-model*="method"] {
    width: 100% !important;
    max-width: 100% !important;
    font-size: 11px !important;
}

/* COA Modal dropdowns */
.max-w-2xl select {
    width: 100% !important;
    max-width: 100% !important;
    font-size: 11px !important;
}

/* History modal dropdowns */
.max-w-6xl select {
    width: 100% !important;
    max-width: 100% !important;
    font-size: 11px !important;
}

/* Remove any truncate classes effect */
.truncate select,
.truncate option,
select.truncate,
option.truncate {
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
}

/* Ensure proper box sizing */
select {
    box-sizing: border-box !important;
}

/* Fix for Tailwind's default select styling */
select.rounded-xl,
select.rounded-lg,
select.rounded {
    white-space: normal !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    select[x-model*="outlet"] {
        max-width: 100% !important;
        width: 100% !important;
        font-size: 12px !important;
    }
    
    select option {
        font-size: 13px !important;
        padding: 12px !important;
    }
}

/* Ensure dropdown menu has proper z-index */
select {
    position: relative;
    z-index: 10;
}

/* Fix for select in grid layouts */
.grid select {
    max-width: 100% !important;
}

/* Additional specificity for stubborn cases */
div select,
form select,
section select {
    white-space: normal !important;
    font-size: 11px !important;
}

div select option,
form select option,
section select option {
    white-space: normal !important;
    word-wrap: break-word !important;
    font-size: 12px !important;
}

/* Specific height adjustments for POS */
select.h-10 {
    height: 2.75rem !important;
    min-height: 2.75rem !important;
}

select.h-9 {
    height: 2.5rem !important;
    min-height: 2.5rem !important;
}

/* Ensure text doesn't overflow container */
select {
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

/* But options should wrap */
select option {
    white-space: normal !important;
    text-overflow: clip !important;
}
</style>

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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\pos\index.blade.php ENDPATH**/ ?>