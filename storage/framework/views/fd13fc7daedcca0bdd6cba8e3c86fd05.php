<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Travel / Booking Jamaah']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Travel / Booking Jamaah')]); ?>
  <div x-data="bookingCrud()" x-init="init()" class="space-y-4 overflow-x-hidden self-start w-full">
    
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold">Booking Jamaah</h1>
        <p class="text-slate-600 text-sm">Kelola booking jamaah untuk paket perjalanan</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('admin.inventaris.travel.payment.verify')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 text-white px-4 py-2 hover:bg-amber-700 transition-colors relative">
          <i class='bx bx-check-shield text-lg'></i> 
          <span>Verifikasi Pembayaran</span>
          <?php if($pendingPaymentCount > 0): ?>
          <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
            <?php echo e($pendingPaymentCount); ?>

          </span>
          <?php endif; ?>
        </a>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.booking.create')): ?>
        <button x-on:click="openCreate()" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
          <i class='bx bx-plus-circle text-lg'></i> Tambah Booking
        </button>
        <?php endif; ?>
      </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card">
      
      <div class="flex gap-2 mb-4 border-b border-slate-200 pb-3">
        <button 
          x-on:click="filters.payment_status = ''; fetchData()" 
          :class="filters.payment_status === '' ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Semua
        </button>
        <button 
          x-on:click="filters.payment_status = 'unpaid'; fetchData()" 
          :class="filters.payment_status === 'unpaid' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Belum Bayar
        </button>
        <button 
          x-on:click="filters.payment_status = 'partial'; fetchData()" 
          :class="filters.payment_status === 'partial' ? 'bg-yellow-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Sebagian
        </button>
        <button 
          x-on:click="filters.payment_status = 'paid'; fetchData()" 
          :class="filters.payment_status === 'paid' ? 'bg-green-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
          class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
          Lunas
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Outlet</label>
          <select x-model="filters.outlet" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">Semua Outlet</option>
            <template x-for="outlet in outlets" :key="outlet.id_outlet">
              <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Status Booking</label>
          <select x-model="filters.status" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="paid">Paid</option>
            <option value="departed">Departed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Status Pembayaran</label>
          <select x-model="filters.payment_status" x-on:change="fetchData()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-200">
            <option value="">Semua Status</option>
            <option value="unpaid">Belum Bayar</option>
            <option value="partial">Sebagian</option>
            <option value="paid">Lunas</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">&nbsp;</label>
          <button x-on:click="resetFilters()" class="w-full rounded-xl border border-slate-200 px-3 py-2 hover:bg-slate-50">
            <i class='bx bx-reset'></i> Reset Filter
          </button>
        </div>
      </div>
    </div>

    
    <div x-show="loading" class="text-center py-8">
      <div class="inline-flex items-center gap-2 text-slate-600">
        <i class='bx bx-loader-alt bx-spin text-xl'></i>
        <span>Memuat data...</span>
      </div>
    </div>

    
    <div x-show="!loading" class="rounded-2xl border border-slate-200 bg-white shadow-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Kode</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Jamaah</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Paket</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Berangkat</th>
              <th class="px-2 py-2.5 text-left font-medium text-slate-600 uppercase tracking-wide">Tgl Booking</th>
              <th class="px-2 py-2.5 text-right font-medium text-slate-600 uppercase tracking-wide">Total</th>
              <th class="px-2 py-2.5 text-right font-medium text-slate-600 uppercase tracking-wide">Diskon</th>
              <th class="px-2 py-2.5 text-right font-medium text-slate-600 uppercase tracking-wide">Dibayar</th>
              <th class="px-2 py-2.5 text-right font-medium text-slate-600 uppercase tracking-wide">Sisa</th>
              <th class="px-2 py-2.5 text-center font-medium text-slate-600 uppercase tracking-wide">Status</th>
              <th class="px-2 py-2.5 text-center font-medium text-slate-600 uppercase tracking-wide">Bayar</th>
              <th class="px-2 py-2.5 text-right font-medium text-slate-600 uppercase tracking-wide">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template x-for="booking in bookings" :key="booking.id">
              <tr class="hover:bg-slate-50">
                <td class="px-2 py-2 font-mono text-xs text-slate-500 whitespace-nowrap" x-text="booking.booking_code"></td>
                <td class="px-2 py-2 max-w-[120px]">
                  <div class="truncate font-medium" x-text="booking.jamaah_name" :title="booking.jamaah_name"></div>
                </td>
                <td class="px-2 py-2 max-w-[140px]">
                  <div class="truncate" x-text="booking.package_name" :title="booking.package_name"></div>
                  <div x-show="booking.price_package_name" class="text-xs text-primary-600 truncate">
                    <span x-text="booking.price_package_name"></span>
                    <span x-show="booking.price_variant"> - <span class="capitalize" x-text="booking.price_variant"></span></span>
                  </div>
                </td>
                <td class="px-2 py-2 text-xs whitespace-nowrap" x-text="booking.keberangkatan_name || '-'"></td>
                <td class="px-2 py-2 text-xs whitespace-nowrap" x-text="booking.booking_date"></td>
                <td class="px-2 py-2 font-medium text-right whitespace-nowrap" x-text="booking.total_price_formatted"></td>
                
                <!-- Diskon Column -->
                <td class="px-2 py-2 text-right whitespace-nowrap">
                  <div x-show="booking.voucher_discount > 0 || booking.admin_discount > 0">
                    <span x-show="booking.voucher_discount > 0" 
                          class="text-green-600 text-xs block"
                          :title="'Voucher: ' + booking.voucher_code">
                      -<span x-text="booking.voucher_discount_formatted"></span>
                    </span>
                    <span x-show="booking.admin_discount > 0" 
                          class="text-blue-600 text-xs block"
                          title="Admin Discount">
                      -<span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(booking.admin_discount)"></span>
                    </span>
                  </div>
                  <span x-show="!booking.voucher_discount && !booking.admin_discount" class="text-slate-400">-</span>
                </td>
                
                <td class="px-2 py-2 text-green-600 text-right whitespace-nowrap" x-text="booking.paid_amount_formatted"></td>
                <td class="px-2 py-2 text-red-600 text-right whitespace-nowrap" x-text="booking.remaining_amount_formatted"></td>
                <td class="px-2 py-2 text-center" x-html="booking.status_badge"></td>
                <td class="px-2 py-2 text-center" x-html="booking.payment_status_badge"></td>
                <td class="px-2 py-2">
                  <div class="flex gap-0.5 justify-end">
                    <button x-on:click="viewBooking(booking.id)" class="p-1 rounded hover:bg-slate-100" title="Detail">
                      <i class='bx bx-show text-slate-600'></i>
                    </button>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.booking.update')): ?>
                    <button x-on:click="editBooking(booking.id)" class="p-1 rounded hover:bg-amber-50" title="Edit">
                      <i class='bx bx-edit text-amber-600'></i>
                    </button>
                    <button 
                      x-show="booking.payment_status !== 'paid'" 
                      x-on:click="openSetPaymentAmount(booking)" 
                      class="p-1 rounded hover:bg-blue-50" 
                      title="Set Jumlah Bayar">
                      <i class='bx bx-money text-blue-600'></i>
                    </button>
                    <button 
                      x-show="booking.payment_status !== 'paid'" 
                      x-on:click="sendPaymentLink(booking)" 
                      class="p-1 rounded hover:bg-green-50" 
                      title="Kirim Link Pembayaran">
                      <i class='bx bx-link text-green-600'></i>
                    </button>
                    <?php endif; ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.booking.delete')): ?>
                    <button x-on:click="confirmCancel(booking)" class="p-1 rounded hover:bg-red-50" title="Batalkan">
                      <i class='bx bx-x-circle text-red-600'></i>
                    </button>
                    <button x-on:click="confirmDelete(booking)" class="p-1 rounded hover:bg-red-50" title="Hapus Permanen">
                      <i class='bx bx-trash text-red-600'></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            </template>
            <tr x-show="bookings.length === 0">
              <td colspan="11" class="px-4 py-12 text-center text-slate-500">
                <i class='bx bx-book-open text-5xl mb-2'></i>
                <p>Belum ada data booking</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closeModal()" class="w-full max-w-2xl bg-white rounded-2xl shadow-float my-4 flex flex-col overflow-hidden" style="max-height: calc(100vh - 4rem);">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
          <h3 class="font-semibold" x-text="modalTitle"></h3>
          <button class="p-2 -m-2 hover:bg-slate-100 rounded-lg" x-on:click="closeModal()">
            <i class='bx bx-x text-xl'></i>
          </button>
        </div>

        <div class="px-5 py-4 overflow-y-auto flex-1">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Outlet <span class="text-red-500">*</span></label>
              <select x-model="form.id_outlet" x-on:change="onOutletChange()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-500">
                <option value="">Pilih Outlet</option>
                <template x-for="outlet in outlets" :key="outlet.id_outlet">
                  <option :value="outlet.id_outlet" x-text="outlet.nama_outlet"></option>
                </template>
              </select>
              <div x-show="errors.id_outlet" class="text-red-500 text-xs mt-1" x-text="errors.id_outlet"></div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Paket Perjalanan <span class="text-red-500">*</span></label>
              <select x-model="form.id_travel_package" x-on:change="onPackageChange()" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-500">
                <option value="">Pilih Paket</option>
                <template x-for="pkg in availablePackages" :key="pkg.id">
                  <option :value="pkg.id" x-text="pkg.text"></option>
                </template>
              </select>
              <small class="text-xs text-slate-500">Harga paket akan otomatis terisi</small>
              <div x-show="errors.id_travel_package" class="text-red-500 text-xs mt-1" x-text="errors.id_travel_package"></div>
            </div>

            <!-- Pilihan Paket Harga (muncul jika paket punya price_packages) -->
            <div x-show="selectedPackagePrices.length > 0">
              <label class="block text-sm font-medium text-slate-700 mb-2">Paket Harga <span class="text-red-500">*</span></label>
              <div class="space-y-2">
                <template x-for="(pkg, pkgIdx) in selectedPackagePrices" :key="pkgIdx">
                  <div class="rounded-xl border p-3"
                       :class="form.price_package_name === pkg.name ? 'border-primary-400 bg-primary-50' : 'border-slate-200 bg-white'">
                    <div class="font-medium text-sm mb-2" x-text="pkg.name"></div>
                    <div class="grid grid-cols-3 gap-2">
                      <template x-for="(variant, vIdx) in pkg.variants" :key="vIdx">
                        <button type="button"
                                @click="selectPriceVariant(pkg.name, variant)"
                                class="rounded-lg border px-2 py-2 text-xs text-center transition-colors"
                                :class="form.price_package_name === pkg.name && form.price_variant === variant.type
                                  ? 'border-primary-500 bg-primary-600 text-white'
                                  : 'border-slate-200 hover:border-primary-300 hover:bg-primary-50'">
                          <div class="font-semibold capitalize" x-text="variant.type"></div>
                          <div x-text="'Rp ' + formatNumber(variant.price)"></div>
                        </button>
                      </template>
                    </div>
                  </div>
                </template>
              </div>
              <div x-show="errors.price_package_name" class="text-red-500 text-xs mt-1" x-text="errors.price_package_name"></div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Jamaah <span class="text-red-500">*</span></label>
              <select x-model="form.id_member" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-500">
                <option value="">Pilih Jamaah</option>
                <template x-for="member in availableMembers" :key="member.id">
                  <option :value="member.id" x-text="member.text"></option>
                </template>
              </select>
              <div x-show="errors.id_member" class="text-red-500 text-xs mt-1" x-text="errors.id_member"></div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Keberangkatan</label>
              <select x-model="form.id_keberangkatan" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-500">
                <option value="">Pilih Keberangkatan (Opsional)</option>
                <template x-for="k in availableKeberangkatan" :key="k.id">
                  <option :value="k.id" x-text="k.text"></option>
                </template>
              </select>
            </div>

            <div x-show="form.id_travel_package">
              <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Keberangkatan (dari Paket)</label>
              <input type="text" :value="packageDepartureDateFormatted" readonly class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50 text-slate-600 cursor-not-allowed">
              <small class="text-xs text-slate-500">Tanggal keberangkatan diambil dari paket perjalanan</small>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Booking <span class="text-red-500">*</span></label>
              <input type="date" x-model="form.booking_date" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-500">
              <div x-show="errors.booking_date" class="text-red-500 text-xs mt-1" x-text="errors.booking_date"></div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">Total Harga <span class="text-red-500">*</span></label>
              <input type="number" x-model="form.total_price" step="0.01" readonly class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50 text-slate-600 cursor-not-allowed">
              <small class="text-xs text-slate-500">
                <span x-show="!form.price_package_name">Harga otomatis dari paket perjalanan</span>
                <span x-show="form.price_package_name" x-text="'Paket ' + form.price_package_name + ' - ' + (form.price_variant ? form.price_variant.charAt(0).toUpperCase() + form.price_variant.slice(1) : '')"></span>
              </small>
              <div x-show="errors.total_price" class="text-red-500 text-xs mt-1" x-text="errors.total_price"></div>
            </div>

            <div x-show="form.id">
              <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
              <select x-model="form.status" class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-500">
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="paid">Paid</option>
                <option value="departed">Departed</option>
                <option value="completed">Completed</option>
              </select>
            </div>
          </div>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button x-on:click="closeModal()" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
          <button x-on:click="submitForm()" :disabled="saving" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
            <span x-show="saving" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
            <span x-show="!saving">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    
    <div x-show="showPaymentAmountModal" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="closePaymentAmountModal()" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden my-4">
        <div class="px-5 py-4 border-b border-slate-100">
          <h3 class="font-semibold">Set Jumlah Pembayaran</h3>
          <p class="text-sm text-slate-600 mt-1">Atur jumlah pembayaran untuk link pembayaran jamaah</p>
        </div>
        <div class="px-5 py-4">
          <div x-show="selectedBookingForPayment" class="mb-4 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm font-medium" x-text="selectedBookingForPayment?.jamaah_name"></div>
            <div class="text-xs text-slate-500 mt-1">
              <span class="font-mono" x-text="selectedBookingForPayment?.booking_code"></span>
            </div>
            <div class="mt-2 text-xs space-y-1">
              <div class="flex justify-between">
                <span class="text-slate-600">Total Harga:</span>
                <span class="font-medium" x-text="selectedBookingForPayment?.total_price_formatted"></span>
              </div>
              <div x-show="selectedBookingForPayment?.voucher_discount > 0" class="flex justify-between text-green-600">
                <span>Diskon Voucher:</span>
                <span class="font-medium" x-text="'-' + selectedBookingForPayment?.voucher_discount_formatted"></span>
              </div>
              <div x-show="selectedBookingForPayment?.admin_discount > 0" class="flex justify-between text-blue-600">
                <span>Diskon Admin (Tersimpan):</span>
                <span class="font-medium" x-text="'-' + selectedBookingForPayment?.admin_discount_formatted"></span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-600">Sudah Dibayar:</span>
                <span class="font-medium text-green-600" x-text="selectedBookingForPayment?.paid_amount_formatted"></span>
              </div>
              <div class="flex justify-between border-t border-slate-200 pt-1">
                <span class="text-slate-600">Sisa Tagihan:</span>
                <span class="font-semibold text-red-600" x-text="selectedBookingForPayment?.remaining_amount_formatted"></span>
              </div>
              <div x-show="selectedBookingForPayment?.custom_payment_amount" class="flex justify-between border-t border-blue-200 pt-1 bg-blue-50 -mx-3 px-3 py-1 mt-2">
                <span class="text-blue-700 font-medium">Jumlah Bayar Terakhir:</span>
                <span class="font-semibold text-blue-700" x-text="selectedBookingForPayment?.custom_payment_amount_formatted"></span>
              </div>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="block text-sm font-medium text-slate-700 mb-2">Diskon Admin (Optional)</label>
            <input 
              type="number" 
              x-model="paymentAmountForm.admin_discount" 
              step="1000"
              min="0"
              class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-500"
              placeholder="Masukkan diskon tambahan dari admin"
              @input="updatePaymentPreview()">
            <small class="text-xs text-slate-500 mt-1 block">
              Diskon ini akan ditambahkan ke diskon voucher (jika ada)
            </small>
            <div x-show="paymentAmountErrors.admin_discount" class="text-red-500 text-xs mt-1" x-text="paymentAmountErrors.admin_discount"></div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Jumlah Pembayaran <span class="text-red-500">*</span></label>
            <input 
              type="number" 
              x-model="paymentAmountForm.custom_payment_amount" 
              step="1000"
              min="0"
              class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:ring-2 focus:ring-primary-500"
              placeholder="Masukkan jumlah pembayaran">
            <small class="text-xs text-slate-500 mt-1 block">
              Maksimal: <span x-text="selectedBookingForPayment?.remaining_amount_formatted"></span>
            </small>
            <div x-show="paymentAmountErrors.custom_payment_amount" class="text-red-500 text-xs mt-1" x-text="paymentAmountErrors.custom_payment_amount"></div>
          </div>

          <div class="mt-3 p-3 rounded-lg bg-blue-50 border border-blue-200">
            <div class="flex gap-2">
              <i class='bx bx-info-circle text-blue-600 text-lg'></i>
              <div class="text-xs text-blue-700">
                <p class="font-medium mb-1">Preview Perhitungan:</p>
                <div class="space-y-1">
                  <div class="flex justify-between">
                    <span>Total Harga:</span>
                    <span x-text="selectedBookingForPayment?.total_price_formatted"></span>
                  </div>
                  <div x-show="selectedBookingForPayment?.voucher_discount > 0" class="flex justify-between text-green-700">
                    <span>Diskon Voucher:</span>
                    <span x-text="'-' + selectedBookingForPayment?.voucher_discount_formatted"></span>
                  </div>
                  <div x-show="paymentAmountForm.admin_discount > 0" class="flex justify-between text-green-700">
                    <span>Diskon Admin:</span>
                    <span x-text="'-Rp ' + new Intl.NumberFormat('id-ID').format(paymentAmountForm.admin_discount || 0)"></span>
                  </div>
                  <div class="flex justify-between border-t border-blue-300 pt-1 font-semibold">
                    <span>Total Setelah Diskon:</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateFinalTotal())"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button x-on:click="closePaymentAmountModal()" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
          <button x-on:click="submitPaymentAmount()" :disabled="settingPaymentAmount" class="rounded-xl bg-primary-600 text-white px-4 py-2 hover:bg-primary-700 disabled:opacity-50">
            <span x-show="settingPaymentAmount" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Menyimpan...
            </span>
            <span x-show="!settingPaymentAmount">Simpan</span>
          </button>
        </div>
      </div>
    </div>

    
    <div x-show="toCancel" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 p-3 pt-8 overflow-y-auto">
      <div x-on:click.outside="toCancel=null" class="w-full max-w-md rounded-2xl bg-white shadow-float overflow-hidden my-4">
        <div class="px-5 py-4">
          <div class="font-semibold">Batalkan Booking?</div>
          <p class="text-slate-600 mt-1">Booking akan dibatalkan dan tidak dapat dikembalikan.</p>
          <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-sm font-medium" x-text="toCancel?.jamaah_name"></div>
            <div class="text-xs text-slate-500 mt-1">
              <span class="font-mono" x-text="toCancel?.booking_code"></span> • 
              <span x-text="toCancel?.package_name"></span>
            </div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-end gap-2">
          <button x-on:click="toCancel=null" class="rounded-xl border border-slate-200 px-4 py-2 hover:bg-slate-50">Batal</button>
          <button x-on:click="cancelBooking()" :disabled="cancelling" class="rounded-xl bg-red-600 text-white px-4 py-2 hover:bg-red-700 disabled:opacity-50">
            <span x-show="cancelling" class="inline-flex items-center gap-2">
              <i class='bx bx-loader-alt bx-spin'></i> Membatalkan...
            </span>
            <span x-show="!cancelling">Batalkan Booking</span>
          </button>
        </div>
      </div>
    </div>

    
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
    function bookingCrud() {
      return {
        bookings: [],
        outlets: [],
        availablePackages: [],
        availableMembers: [],
        availableKeberangkatan: [],
        selectedPackagePrices: [],
        packageDepartureDate: '',
        packageDepartureDateFormatted: '-',
        loading: false,
        saving: false,
        cancelling: false,
        deleting: false,
        settingPaymentAmount: false,
        
        filters: {
          outlet: '',
          status: '',
          payment_status: '',
          keberangkatan_id: '',
          package_id: ''
        },
        
        showModal: false,
        modalTitle: 'Tambah Booking',
        form: {
          id: null,
          id_outlet: '',
          id_travel_package: '',
          id_member: '',
          id_keberangkatan: '',
          booking_date: '',
          total_price: '',
          status: 'pending',
          price_package_name: '',
          price_variant: ''
        },
        errors: {},
        
        showPaymentAmountModal: false,
        selectedBookingForPayment: null,
        paymentAmountForm: {
          custom_payment_amount: '',
          admin_discount: 0
        },
        paymentAmountErrors: {},
        
        toCancel: null,
        
        showToast: false,
        toastMessage: '',
        toastType: 'success',

        async init() {
          // Handle query params dari URL (misal dari tab keberangkatan di detail paket)
          const urlParams = new URLSearchParams(window.location.search);
          if (urlParams.get('keberangkatan')) this.filters.keberangkatan_id = urlParams.get('keberangkatan');
          if (urlParams.get('package')) this.filters.package_id = urlParams.get('package');

          await this.fetchOutlets();
          await this.fetchData();
          this.form.booking_date = new Date().toISOString().split('T')[0];
        },

        async fetchData() {
          this.loading = true;
          try {
            const params = new URLSearchParams({
              outlet_id: this.filters.outlet,
              status: this.filters.status,
              payment_status: this.filters.payment_status
            });
            if (this.filters.keberangkatan_id) params.append('keberangkatan_id', this.filters.keberangkatan_id);
            if (this.filters.package_id) params.append('package_id', this.filters.package_id);

            const response = await fetch(`<?php echo e(route('admin.inventaris.booking.getData')); ?>?${params}`);
            const data = await response.json();
            this.bookings = data.data;
          } catch (error) {
            console.error('Error fetching bookings:', error);
            this.showToastMessage('Gagal memuat data', 'error');
          } finally {
            this.loading = false;
          }
        },

        async fetchOutlets() {
          try {
            const response = await fetch('<?php echo e(route("admin.inventaris.outlet.data")); ?>');
            const data = await response.json();
            this.outlets = data.data.map(item => ({
              id_outlet: item.id_outlet || item.id,
              nama_outlet: item.name || item.nama_outlet
            }));
          } catch (error) {
            console.error('Error fetching outlets:', error);
          }
        },

        async onOutletChange() {
          if (!this.form.id_outlet) return;
          
          try {
            const [packagesRes, membersRes] = await Promise.all([
              fetch(`<?php echo e(route('admin.inventaris.booking.getAvailablePackages')); ?>?outlet_id=${this.form.id_outlet}`),
              fetch(`<?php echo e(route('admin.inventaris.booking.getJamaahMembers')); ?>?outlet_id=${this.form.id_outlet}`)
            ]);
            
            this.availablePackages = await packagesRes.json();
            this.availableMembers = await membersRes.json();
          } catch (error) {
            console.error('Error loading outlet data:', error);
          }
        },

        async onPackageChange() {
          if (!this.form.id_travel_package) return;
          
          const selectedPackage = this.availablePackages.find(p => p.id == this.form.id_travel_package);
          if (selectedPackage) {
            // Load price_packages
            this.selectedPackagePrices = (selectedPackage.price_packages && selectedPackage.price_packages.length > 0)
              ? selectedPackage.price_packages
              : [];
            
            // Reset price selection
            this.form.price_package_name = '';
            this.form.price_variant = '';
            
            // If no price_packages, use default price
            if (this.selectedPackagePrices.length === 0) {
              this.form.total_price = selectedPackage.selling_price || selectedPackage.price || 0;
            } else {
              this.form.total_price = '';
            }
            
            // Auto-fill departure date from package (for display only)
            this.packageDepartureDate = selectedPackage.departure_date || '';
            this.packageDepartureDateFormatted = selectedPackage.departure_date 
              ? new Date(selectedPackage.departure_date).toLocaleDateString('id-ID', { 
                  day: 'numeric', 
                  month: 'long', 
                  year: 'numeric' 
                })
              : '-';
          }
          
          try {
            const response = await fetch(`<?php echo e(url('admin/inventaris/travel/booking/keberangkatan')); ?>/${this.form.id_travel_package}`);
            this.availableKeberangkatan = await response.json();
          } catch (error) {
            console.error('Error loading keberangkatan:', error);
          }
        },

        selectPriceVariant(packageName, variant) {
          this.form.price_package_name = packageName;
          this.form.price_variant = variant.type;
          this.form.total_price = variant.price || 0;
        },

        formatNumber(num) {
          return new Intl.NumberFormat('id-ID').format(num || 0);
        },

        resetFilters() {
          this.filters = {
            outlet: '',
            status: '',
            payment_status: ''
          };
          this.fetchData();
        },

        openCreate() {
          this.form = {
            id: null,
            id_outlet: '',
            id_travel_package: '',
            id_member: '',
            id_keberangkatan: '',
            booking_date: new Date().toISOString().split('T')[0],
            total_price: '',
            status: 'pending',
            price_package_name: '',
            price_variant: ''
          };
          this.packageDepartureDate = '';
          this.packageDepartureDateFormatted = '-';
          this.selectedPackagePrices = [];
          this.availablePackages = [];
          this.availableMembers = [];
          this.availableKeberangkatan = [];
          this.errors = {};
          this.modalTitle = 'Tambah Booking';
          this.showModal = true;
        },

        async editBooking(id) {
          try {
            const response = await fetch(`<?php echo e(url('admin/inventaris/travel/booking')); ?>/${id}/edit`);
            const result = await response.json();
            const booking = result.data;
            
            this.form = {
              id: booking.id,
              id_outlet: booking.id_outlet,
              id_travel_package: booking.id_travel_package,
              id_member: booking.id_member,
              id_keberangkatan: booking.id_keberangkatan,
              booking_date: booking.booking_date,
              total_price: booking.total_price,
              status: booking.status,
              price_package_name: booking.price_package_name || '',
              price_variant: booking.price_variant || ''
            };
            
            await this.onOutletChange();
            await this.onPackageChange();
            
            this.errors = {};
            this.modalTitle = 'Edit Booking';
            this.showModal = true;
          } catch (error) {
            console.error('Error loading booking:', error);
            this.showToastMessage('Gagal memuat data booking', 'error');
          }
        },

        viewBooking(id) {
          window.location.href = `<?php echo e(url('admin/inventaris/travel/booking')); ?>/${id}`;
        },

        closeModal() {
          this.showModal = false;
          this.errors = {};
        },

        async submitForm() {
          this.saving = true;
          this.errors = {};

          // Validasi: jika paket punya price_packages, wajib pilih paket harga & variant
          if (this.selectedPackagePrices.length > 0 && (!this.form.price_package_name || !this.form.price_variant)) {
            this.errors.price_package_name = 'Pilih paket harga dan variant terlebih dahulu';
            this.saving = false;
            return;
          }

          try {
            const url = this.form.id 
              ? `<?php echo e(url('admin/inventaris/travel/booking')); ?>/${this.form.id}`
              : '<?php echo e(route("admin.inventaris.booking.store")); ?>';
            const method = this.form.id ? 'PUT' : 'POST';

            const response = await fetch(url, {
              method: method,
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
              },
              body: JSON.stringify(this.form)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Data berhasil disimpan', 'success');
              this.closeModal();
              await this.fetchData();
            } else {
              if (result.errors) {
                this.errors = result.errors;
                // Show detailed validation errors
                let errorMessage = result.message || 'Validasi gagal';
                errorMessage += '\n\nDetail Error:\n';
                Object.keys(result.errors).forEach(field => {
                  const error = result.errors[field];
                  // Handle both array and string error formats
                  const errorText = Array.isArray(error) ? error.join(', ') : error;
                  errorMessage += `- ${field}: ${errorText}\n`;
                });
                console.error('Validation errors:', result.errors);
                console.error('Form data sent:', this.form);
                alert(errorMessage);
              } else {
                console.error('Error response:', result);
                this.showToastMessage(result.message || 'Terjadi kesalahan', 'error');
              }
            }
          } catch (error) {
            console.error('Error saving booking:', error);
            this.showToastMessage('Gagal menyimpan data', 'error');
          } finally {
            this.saving = false;
          }
        },

        confirmCancel(booking) {
          this.toCancel = booking;
        },

        async cancelBooking() {
          if (!this.toCancel) return;
          
          this.cancelling = true;
          try {
            const response = await fetch(`<?php echo e(url('admin/inventaris/travel/booking')); ?>/${this.toCancel.id}/cancel`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Booking berhasil dibatalkan', 'success');
              this.toCancel = null;
              await this.fetchData();
            } else {
              this.showToastMessage(result.message || 'Gagal membatalkan booking', 'error');
            }
          } catch (error) {
            console.error('Error cancelling booking:', error);
            this.showToastMessage('Gagal membatalkan booking', 'error');
          } finally {
            this.cancelling = false;
          }
        },

        confirmDelete(booking) {
          if (confirm(`PERINGATAN: Anda akan menghapus PERMANEN booking ${booking.booking_code}.\n\nSemua data terkait akan dihapus:\n- Semua pembayaran\n- Invoice\n- Piutang\n- Add-ons\n- Hotel bookings\n- Dokumen\n\nTindakan ini TIDAK DAPAT DIBATALKAN!\n\nApakah Anda yakin?`)) {
            this.deleteBooking(booking);
          }
        },

        openSetPaymentAmount(booking) {
          this.selectedBookingForPayment = booking;
          // Load existing values if already set
          this.paymentAmountForm.custom_payment_amount = booking.custom_payment_amount || '';
          this.paymentAmountForm.admin_discount = booking.admin_discount || 0;
          this.paymentAmountErrors = {};
          this.showPaymentAmountModal = true;
        },

        closePaymentAmountModal() {
          this.showPaymentAmountModal = false;
          this.selectedBookingForPayment = null;
          this.paymentAmountForm.custom_payment_amount = '';
          this.paymentAmountForm.admin_discount = 0;
          this.paymentAmountErrors = {};
        },

        calculateFinalTotal() {
          if (!this.selectedBookingForPayment) return 0;
          const total = this.selectedBookingForPayment.total_price || 0;
          const voucherDiscount = this.selectedBookingForPayment.voucher_discount || 0;
          const adminDiscount = parseFloat(this.paymentAmountForm.admin_discount) || 0;
          return Math.max(0, total - voucherDiscount - adminDiscount);
        },

        updatePaymentPreview() {
          // This function is called when admin discount changes
          // The preview is automatically updated via Alpine.js reactivity
        },

        async submitPaymentAmount() {
          if (!this.selectedBookingForPayment) return;
          
          this.settingPaymentAmount = true;
          this.paymentAmountErrors = {};

          try {
            const response = await fetch(`<?php echo e(url('admin/inventaris/travel/booking')); ?>/${this.selectedBookingForPayment.id}/set-payment-amount`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(this.paymentAmountForm)
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Jumlah pembayaran berhasil diatur', 'success');
              this.closePaymentAmountModal();
              await this.fetchData();
            } else {
              if (result.errors) {
                this.paymentAmountErrors = result.errors;
              }
              this.showToastMessage(result.message || 'Gagal mengatur jumlah pembayaran', 'error');
            }
          } catch (error) {
            console.error('Error setting payment amount:', error);
            this.showToastMessage('Gagal mengatur jumlah pembayaran', 'error');
          } finally {
            this.settingPaymentAmount = false;
          }
        },

        async sendPaymentLink(booking) {
          if (!confirm(`Kirim link pembayaran ke ${booking.jamaah_name}?\n\nLink akan dikirim via WhatsApp ke nomor yang terdaftar.`)) {
            return;
          }

          try {
            // Build payment link
            const paymentLink = `<?php echo e(url('/')); ?>/paket/${booking.id_travel_package}/invoice/${booking.id}`;
            
            // Calculate payment amount
            let paymentAmount = booking.total_price - booking.paid_amount;
            let paymentLabel = 'Sisa Tagihan';
            
            // Build WhatsApp message
            let message = '*LINK PEMBAYARAN BOOKING*\n';
            message += '================================\n\n';
            message += `Kode Booking: *${booking.booking_code}*\n`;
            message += `Nama: ${booking.jamaah_name}\n`;
            message += `Paket: ${booking.package_name}\n\n`;
            message += '*RINCIAN PEMBAYARAN*\n';
            message += `Total Tagihan: Rp ${booking.total_price_formatted}\n`;
            message += `Sudah Dibayar: Rp ${booking.paid_amount_formatted}\n`;
            message += `Sisa Tagihan: Rp ${booking.remaining_amount_formatted}\n\n`;
            message += `*JUMLAH YANG HARUS DIBAYAR*\n`;
            message += `${paymentLabel}: *Rp ${booking.remaining_amount_formatted}*\n\n`;
            message += '*LINK PEMBAYARAN*\n';
            message += `${paymentLink}\n\n`;
            message += '_Silakan klik link di atas untuk melanjutkan pembayaran._\n\n';
            message += 'Terima kasih! 🙏';
            
            // Get jamaah phone number
            const jamaahPhone = booking.jamaah_phone || '628976688800'; // Fallback to admin
            const waUrl = `https://wa.me/${jamaahPhone.replace(/^0/, '62').replace(/\D/g, '')}?text=${encodeURIComponent(message)}`;
            
            // Open WhatsApp
            window.open(waUrl, '_blank');
            
            this.showToastMessage('Link pembayaran berhasil disiapkan', 'success');
          } catch (error) {
            console.error('Error sending payment link:', error);
            this.showToastMessage('Gagal mengirim link pembayaran', 'error');
          }
        },

        async deleteBooking(booking) {
          this.deleting = true;
          try {
            const response = await fetch(`<?php echo e(url('admin/inventaris/travel/booking')); ?>/${booking.id}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
              }
            });

            const result = await response.json();

            if (response.ok) {
              this.showToastMessage(result.message || 'Booking berhasil dihapus', 'success');
              await this.fetchData();
            } else {
              this.showToastMessage(result.message || 'Gagal menghapus booking', 'error');
            }
          } catch (error) {
            console.error('Error deleting booking:', error);
            this.showToastMessage('Gagal menghapus booking', 'error');
          } finally {
            this.deleting = false;
          }
        },

        showToastMessage(message, type = 'success') {
          this.toastMessage = message;
          this.toastType = type;
          this.showToast = true;
          
          setTimeout(() => {
            this.showToast = false;
          }, 3000);
        }
      };
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/booking/index.blade.php ENDPATH**/ ?>