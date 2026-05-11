<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Travel / Detail Booking']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Travel / Detail Booking')]); ?>
<style>
/* Fix Bootstrap modal z-index in Tailwind layout */
.modal { z-index: 99998 !important; }
.modal-backdrop { z-index: 99997 !important; }
.modal-dialog { z-index: 99999 !important; }
/* Fix invoice modal height */
#invoice-modal .modal-body { overflow: hidden; }
#invoice-modal .modal-content { max-height: calc(100vh - 2rem); }
</style>
  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0">Detail Booking</h1>
            <p class="text-muted"><?php echo e($booking->booking_code); ?></p>
          </div>
          <div>
            <a href="<?php echo e(route('admin.inventaris.booking.index')); ?>" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Booking Information -->
      <div class="col-md-8">
        <div class="card mb-3">
          <div class="card-header">
            <h5 class="mb-0">Informasi Booking</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-sm table-borderless">
                  <tr>
                    <th width="40%">Kode Booking</th>
                    <td><?php echo e($booking->booking_code); ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Booking</th>
                    <td><?php echo e($booking->booking_date->format('d M Y')); ?></td>
                  </tr>
                  <tr>
                    <th>Status</th>
                    <td>
                      <?php
                        $badges = [
                          'pending' => 'warning',
                          'confirmed' => 'info',
                          'paid' => 'success',
                          'departed' => 'primary',
                          'completed' => 'success',
                          'cancelled' => 'danger'
                        ];
                        $color = $badges[$booking->status] ?? 'secondary';
                      ?>
                      <span class="badge badge-<?php echo e($color); ?>"><?php echo e(ucfirst($booking->status)); ?></span>
                    </td>
                  </tr>
                  <tr>
                    <th>Outlet</th>
                    <td><?php echo e($booking->outlet->nama_outlet ?? '-'); ?></td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-sm table-borderless">
                  <tr>
                    <th width="40%">Paket</th>
                    <td>
                      <a href="<?php echo e(route('admin.inventaris.travel.package.detail', $booking->id_travel_package)); ?>">
                        <?php echo e($booking->travelPackage->package_name); ?>

                      </a>
                    </td>
                  </tr>
                  <tr>
                    <th>Keberangkatan</th>
                    <td>
                      <?php if($booking->keberangkatan): ?>
                        <a href="<?php echo e(route('admin.inventaris.travel.keberangkatan.show', $booking->id_keberangkatan)); ?>">
                          <?php echo e($booking->keberangkatan->keberangkatan_name); ?>

                        </a>
                      <?php else: ?>
                        <span class="text-muted">Belum ditentukan</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <tr>
                    <th>Tanggal Keberangkatan</th>
                    <td>
                      <?php if($booking->keberangkatan): ?>
                        <?php echo e($booking->keberangkatan->departure_date->format('d M Y')); ?>

                      <?php else: ?>
                        <?php echo e($booking->travelPackage->departure_date->format('d M Y')); ?>

                      <?php endif; ?>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Jamaah Information -->
        <div class="card mb-3">
          <div class="card-header">
            <h5 class="mb-0">Informasi Jamaah</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-sm table-borderless">
                  <tr>
                    <th width="40%">Nama</th>
                    <td><?php echo e($booking->jamaah->nama); ?></td>
                  </tr>
                  <tr>
                    <th>Telepon</th>
                    <td><?php echo e($booking->jamaah->telepon ?? '-'); ?></td>
                  </tr>
                  <tr>
                    <th>Alamat</th>
                    <td><?php echo e($booking->jamaah->alamat ?? '-'); ?></td>
                  </tr>
                  <tr>
                    <th>KTP NIK</th>
                    <td><?php echo e($booking->jamaah->ktp_nik ?? '-'); ?></td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-sm table-borderless">
                  <tr>
                    <th width="40%">Nomor Passport</th>
                    <td><?php echo e($booking->jamaah->passport_nomor ?? '-'); ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Kadaluarsa</th>
                    <td>
                      <?php if($booking->jamaah->passport_tanggal_kadaluarsa): ?>
                        <?php echo e(\Carbon\Carbon::parse($booking->jamaah->passport_tanggal_kadaluarsa)->format('d M Y')); ?>

                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </td>
                  </tr>
                  <tr>
                    <th>Mahram</th>
                    <td><?php echo e($booking->jamaah->mahram_name ?? '-'); ?></td>
                  </tr>
                  <tr>
                    <th>Kontak Darurat</th>
                    <td>
                      <?php if($booking->jamaah->emergency_contact_name): ?>
                        <?php echo e($booking->jamaah->emergency_contact_name); ?> 
                        (<?php echo e($booking->jamaah->emergency_contact_phone); ?>)
                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Hotel Booking per Jamaah -->
        <div class="card mb-3" x-data="jamaahHotelManager(<?php echo e($booking->id); ?>)">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-hotel mr-2"></i>Booking Hotel Jamaah</h5>
            <?php if($booking->status !== 'cancelled'): ?>
            <a href="<?php echo e(route('admin.inventaris.booking.hotel-bookings.create', $booking->id)); ?>" 
               class="btn btn-sm btn-primary"
               x-data="{ loading: false }"
               @click="loading = true"
               :disabled="loading">
              <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-plus'"></i> 
              <span x-text="loading ? 'Memuat...' : 'Tambah Hotel'"></span>
            </a>
            <?php endif; ?>
          </div>
          <div class="card-body p-0">
            <div x-show="loading" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i></div>
            <div x-show="!loading">
              <table class="table table-sm mb-0" x-show="hotels.length > 0">
                <thead class="thead-light">
                  <tr>
                    <th>Kota</th>
                    <th>Hotel</th>
                    <th>Tipe Kamar</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th class="text-center">Malam</th>
                    <th class="text-right">Harga/Malam</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Charge</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="hb in hotels" :key="hb.id">
                    <tr>
                      <td><span class="badge badge-info capitalize" x-text="hb.city_type"></span></td>
                      <td x-text="hb.hotel_name"></td>
                      <td class="capitalize" x-text="hb.room_type || '-'"></td>
                      <td x-text="hb.check_in_date || '-'"></td>
                      <td x-text="hb.check_out_date || '-'"></td>
                      <td class="text-center" x-text="hb.nights"></td>
                      <td class="text-right" x-text="'Rp ' + formatNum(hb.price_per_night)"></td>
                      <td class="text-right font-weight-bold" x-text="'Rp ' + formatNum(hb.total_cost)"></td>
                      <td class="text-center">
                        <span :class="hb.is_charged ? 'badge badge-warning' : 'badge badge-success'"
                              x-text="hb.is_charged ? 'Charge' : 'Include'"></span>
                      </td>
                      <td class="text-right">
                        <a href="<?php echo e(route('admin.inventaris.booking.hotel-bookings.edit', [$booking->id, ':id'])); ?>" 
                           class="btn btn-xs btn-outline-primary mr-1"
                           :href="$el.getAttribute('href').replace(':id', hb.id)">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-xs btn-outline-danger" @click="deleteHotel(hb.id)"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </template>
                </tbody>
                <tfoot>
                  <tr class="table-light">
                    <td colspan="7" class="text-right font-weight-bold">Total Charge:</td>
                    <td class="text-right font-weight-bold text-warning" x-text="'Rp ' + formatNum(totalCharged)"></td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </table>
              <div x-show="hotels.length === 0" class="text-center py-4 text-muted">
                <i class="fas fa-hotel fa-2x mb-2"></i>
                <p class="mb-0">Belum ada booking hotel</p>
              </div>
            </div>
          </div>

          <!-- Modal Add/Edit Hotel Booking -->
          <div class="modal fade" id="hotelBookingModal" tabindex="-1" style="z-index:99999;">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" x-text="hotelForm.id ? 'Edit Hotel Booking' : 'Tambah Hotel Booking'"></h5>
                  <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label>Kota <span class="text-danger">*</span></label>
                    <select class="form-control" x-model="hotelForm.city_type">
                      <option value="makkah">Mekkah</option>
                      <option value="madinah">Madinah</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Hotel <span class="text-danger">*</span></label>
                    <select class="form-control" x-model="hotelForm.id_hotel" @change="onHotelChange()">
                      <option value="">Pilih Hotel</option>
                      <template x-for="h in availableHotels" :key="h.id">
                        <option :value="h.id" x-text="h.hotel_name + (h.city ? ' - ' + h.city : '')"></option>
                      </template>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Tipe Kamar</label>
                    <select class="form-control" x-model="hotelForm.room_type"
                            @change="const rt = availableRoomTypes.find(r => r.room_type_name === hotelForm.room_type || r.id == hotelForm.room_type); if(rt) hotelForm.price_per_night = parseFloat(rt.price_per_night)||0;">
                      <option value="">Pilih Tipe Kamar</option>
                      <template x-for="rt in availableRoomTypes" :key="rt.id">
                        <option :value="rt.room_type_name" x-text="rt.room_type_name + ' - Rp ' + new Intl.NumberFormat('id-ID').format(rt.price_per_night||0) + '/malam'"></option>
                      </template>
                      <!-- Fallback jika tidak ada tipe kamar -->
                      <template x-if="availableRoomTypes.length === 0">
                        <option value="quad">Quad (4 orang)</option>
                      </template>
                      <template x-if="availableRoomTypes.length === 0">
                        <option value="triple">Triple (3 orang)</option>
                      </template>
                      <template x-if="availableRoomTypes.length === 0">
                        <option value="double">Double (2 orang)</option>
                      </template>
                      <template x-if="availableRoomTypes.length === 0">
                        <option value="single">Single (1 orang)</option>
                      </template>
                    </select>
                    <small class="text-muted" x-show="availableRoomTypes.length > 0">Harga/malam otomatis terisi dari tipe kamar</small>
                    <small class="text-muted" x-show="availableRoomTypes.length === 0 && hotelForm.id_hotel">Hotel ini belum memiliki tipe kamar, isi harga manual</small>
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Check-in</label>
                        <input type="date" class="form-control" x-model="hotelForm.check_in_date">
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="form-group">
                        <label>Check-out</label>
                        <input type="date" class="form-control" x-model="hotelForm.check_out_date" @change="calcNights()">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Jumlah Malam</label>
                        <input type="number" class="form-control" x-model.number="hotelForm.nights" min="0">
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="form-group">
                        <label>Harga/Malam (Rp)</label>
                        <input type="number" class="form-control" x-model.number="hotelForm.price_per_night" min="0">
                        <small class="text-muted" x-text="'Total: Rp ' + formatNum((hotelForm.price_per_night||0) * (hotelForm.nights||0))"></small>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="isCharged" x-model="hotelForm.is_charged">
                      <label class="custom-control-label" for="isCharged">
                        <strong x-text="hotelForm.is_charged ? 'Charge ke Invoice' : 'Include Paket'"></strong>
                      </label>
                    </div>
                    <small class="text-muted">
                      Jika dicentang, biaya hotel akan ditambahkan ke invoice jamaah. Jika tidak, keterangan "Include Paket".
                    </small>
                  </div>
                  <div class="form-group">
                    <label>Catatan</label>
                    <textarea class="form-control" x-model="hotelForm.notes" rows="2"></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-primary" @click="saveHotel()" :disabled="saving">
                    <span x-show="saving"><i class="fas fa-spinner fa-spin"></i></span>
                    <span x-show="!saving">Simpan</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Add-ons Section -->
        <div class="card mb-3" id="addons-section" x-data="addonManager(<?php echo e($booking->id); ?>)">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add-ons / Request Tambahan</h5>
            <?php if($booking->status !== 'cancelled'): ?>
            <a href="<?php echo e(route('admin.inventaris.booking.addons.create', $booking->id)); ?>" 
               class="btn btn-sm btn-primary"
               x-data="{ loading: false }"
               @click="loading = true"
               :disabled="loading">
              <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-plus'"></i> 
              <span x-text="loading ? 'Memuat...' : 'Tambah Add-on'"></span>
            </a>
            <?php endif; ?>
          </div>
          <div class="card-body p-0">
            <div x-show="loading" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i></div>
            <div x-show="!loading">
              <table class="table table-sm mb-0" x-show="addons.length > 0">
                <thead class="thead-light">
                  <tr>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th class="text-right">Harga</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-center">Masuk HPP</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="addon in addons" :key="addon.id">
                    <tr>
                      <td x-text="addon.nama"></td>
                      <td class="text-muted small" x-text="addon.keterangan || '-'"></td>
                      <td class="text-right" x-text="'Rp ' + formatNum(addon.harga)"></td>
                      <td class="text-center" x-text="addon.qty"></td>
                      <td class="text-right font-weight-bold" x-text="'Rp ' + formatNum(addon.harga * addon.qty)"></td>
                      <td class="text-center">
                        <span :class="addon.masuk_hpp ? 'badge badge-success' : 'badge badge-secondary'" x-text="addon.masuk_hpp ? 'Ya' : 'Tidak'"></span>
                      </td>
                      <td class="text-right">
                        <a href="<?php echo e(route('admin.inventaris.booking.addons.edit', [$booking->id, ':id'])); ?>" 
                           class="btn btn-xs btn-outline-primary mr-1"
                           :href="$el.getAttribute('href').replace(':id', addon.id)">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-xs btn-outline-danger" @click="deleteAddon(addon.id)"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </template>
                </tbody>
                <tfoot>
                  <tr class="table-light">
                    <td colspan="4" class="text-right font-weight-bold">Total Add-ons:</td>
                    <td class="text-right font-weight-bold text-primary" x-text="'Rp ' + formatNum(totalAddons)"></td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </table>
              <div x-show="addons.length === 0" class="text-center py-4 text-muted">
                <i class="fas fa-box-open fa-2x mb-2"></i>
                <p class="mb-0">Belum ada add-on</p>
              </div>
            </div>
          </div>

          <!-- Modal Add/Edit Addon -->
          <div class="modal fade" id="addonModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" x-text="addonForm.id ? 'Edit Add-on' : 'Tambah Add-on'"></h5>
                  <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label>Nama <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" x-model="addonForm.nama" placeholder="Contoh: Upgrade Kamar, Makan Tambahan">
                  </div>
                  <div class="form-group">
                    <label>Keterangan</label>
                    <textarea class="form-control" x-model="addonForm.keterangan" rows="2" placeholder="Deskripsi tambahan"></textarea>
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Harga <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" x-model.number="addonForm.harga" min="0">
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="form-group">
                        <label>Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" x-model.number="addonForm.qty" min="1">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="masukHpp" x-model="addonForm.masuk_hpp">
                      <label class="custom-control-label" for="masukHpp">Masuk HPP</label>
                    </div>
                    <small class="text-muted">Centang jika add-on ini harus dihitung dalam HPP booking</small>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="button" class="btn btn-primary" @click="saveAddon()" :disabled="saving">
                    <span x-show="saving"><i class="fas fa-spinner fa-spin"></i></span>
                    <span x-show="!saving">Simpan</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment History -->
        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-2"></i>Riwayat Pembayaran</h5>
            <div x-data="{ loadingInvoice: false, loadingPayment: false }">
              <?php if($booking->status !== 'cancelled'): ?>
                <?php if(!$booking->id_invoice): ?>
                <button type="button" 
                        class="btn btn-sm btn-primary mr-2" 
                        id="btn-create-invoice"
                        @click="loadingInvoice = true"
                        :disabled="loadingInvoice">
                  <i class="fas" :class="loadingInvoice ? 'fa-spinner fa-spin' : 'fa-file-invoice'"></i> 
                  <span x-text="loadingInvoice ? 'Memuat...' : 'Buat Invoice'"></span>
                </button>
                <?php endif; ?>
                <?php if($booking->payment_status !== 'paid'): ?>
                <a href="<?php echo e(route('admin.inventaris.payment.create', $booking->id)); ?>" 
                   class="btn btn-sm btn-success"
                   @click="loadingPayment = true"
                   :disabled="loadingPayment">
                  <i class="fas" :class="loadingPayment ? 'fa-spinner fa-spin' : 'fa-plus'"></i> 
                  <span x-text="loadingPayment ? 'Memuat...' : 'Tambah Pembayaran'"></span>
                </a>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="card-body">
            <?php if($booking->payments->count() > 0): ?>
              <!-- Payment Summary Cards -->
              <div class="row mb-3">
                <div class="col-md-4">
                  <div class="card bg-primary text-white">
                    <div class="card-body py-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Total Harga</h6>
                          <h4 class="mb-0">Rp <?php echo e(number_format($booking->total_price, 0, ',', '.')); ?></h4>
                        </div>
                        <i class="fas fa-tag fa-2x opacity-50"></i>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-success text-white">
                    <div class="card-body py-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Terbayar</h6>
                          <h4 class="mb-0">Rp <?php echo e(number_format($booking->paid_amount ?? 0, 0, ',', '.')); ?></h4>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-<?php echo e($booking->remaining_amount > 0 ? 'warning' : 'success'); ?> text-white">
                    <div class="card-body py-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Sisa Tagihan</h6>
                          <h4 class="mb-0">Rp <?php echo e(number_format($booking->remaining_amount ?? 0, 0, ',', '.')); ?></h4>
                        </div>
                        <i class="fas fa-<?php echo e($booking->remaining_amount > 0 ? 'exclamation-circle' : 'check-double'); ?> fa-2x opacity-50"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Payment List -->
              <h6 class="mb-3"><i class="fas fa-list mr-2"></i>Detail Pembayaran (<?php echo e($booking->payments->count()); ?> transaksi)</h6>
              <?php $__currentLoopData = $booking->payments->sortByDesc('payment_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="card mb-2 border-left-success" style="border-left: 4px solid #28a745;">
                <div class="card-body p-3">
                  <div class="row">
                    <!-- Left: Payment Info -->
                    <div class="col-md-8">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                          <h5 class="mb-1 text-success">
                            <i class="fas fa-money-bill-wave mr-1"></i>
                            Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?>

                          </h5>
                          <small class="text-muted">
                            <i class="fas fa-calendar mr-1"></i><?php echo e($payment->payment_date->format('d M Y')); ?>

                            <i class="fas fa-clock ml-2 mr-1"></i><?php echo e($payment->created_at->format('H:i')); ?>

                          </small>
                        </div>
                        <span class="badge badge-<?php echo e($payment->payment_type === 'full' ? 'success' : 'warning'); ?> badge-pill">
                          <?php echo e($payment->payment_type === 'full' ? 'LUNAS' : 'DP'); ?>

                        </span>
                      </div>
                      
                      <div class="row small">
                        <div class="col-6">
                          <p class="mb-1">
                            <i class="fas fa-credit-card text-muted mr-1"></i>
                            <strong>Metode:</strong> <?php echo e($payment->formatted_payment_method); ?>

                          </p>
                          <?php if($payment->reference_number): ?>
                          <p class="mb-1">
                            <i class="fas fa-hashtag text-muted mr-1"></i>
                            <strong>Ref:</strong> <?php echo e($payment->reference_number); ?>

                          </p>
                          <?php endif; ?>
                        </div>
                        <div class="col-6">
                          <?php if($payment->notes): ?>
                          <p class="mb-1">
                            <i class="fas fa-sticky-note text-muted mr-1"></i>
                            <strong>Catatan:</strong> <?php echo e(Str::limit($payment->notes, 50)); ?>

                          </p>
                          <?php endif; ?>
                          <p class="mb-0 text-muted">
                            <i class="fas fa-user text-muted mr-1"></i>
                            <small><?php echo e($payment->recordedBy ? $payment->recordedBy->name : 'Website (Public)'); ?></small>
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Right: Bukti Transfer & Actions -->
                    <div class="col-md-4 text-right">
                      <?php if($payment->bukti_transfer): ?>
                        <?php
                          $buktiPath = $payment->bukti_transfer;
                          if (!str_contains($buktiPath, '/')) {
                              $buktiPath = 'bukti-transfer/' . $buktiPath;
                          }
                          $buktiUrl = url(Storage::url($buktiPath));
                        ?>
                        <div class="mb-2">
                          <a href="<?php echo e($buktiUrl); ?>" 
                             target="_blank" 
                             rel="noopener noreferrer"
                             title="Klik untuk melihat bukti transfer">
                            <img src="<?php echo e($buktiUrl); ?>" 
                                 alt="Bukti Transfer" 
                                 class="img-thumbnail"
                                 style="max-width: 120px; max-height: 80px; cursor: pointer; transition: transform 0.2s;"
                                 onmouseover="this.style.transform='scale(1.05)'"
                                 onmouseout="this.style.transform='scale(1)'">
                          </a>
                        </div>
                      <?php endif; ?>
                      
                      <div class="btn-group-vertical btn-group-sm" role="group">
                        <button type="button" 
                                class="btn btn-info btn-view-receipt" 
                                data-url="<?php echo e(route('admin.inventaris.payment.receipt', $payment->id)); ?>">
                          <i class="fas fa-file-pdf"></i> Kwitansi
                        </button>
                        <a href="<?php echo e(route('admin.inventaris.payment.receipt.download', $payment->id)); ?>" 
                           class="btn btn-primary">
                          <i class="fas fa-download"></i> Download
                        </a>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'travel.payment.delete')): ?>
                        <button type="button"
                                class="btn btn-danger btn-delete-payment"
                                data-id="<?php echo e($payment->id); ?>"
                                data-amount="Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?>"
                                data-date="<?php echo e($payment->payment_date->format('d M Y')); ?>"
                                x-data="{ deleting: false }"
                                @click="deleting = true"
                                :disabled="deleting">
                          <i class="fas" :class="deleting ? 'fa-spinner fa-spin' : 'fa-trash'"></i> 
                          <span x-text="deleting ? 'Menghapus...' : 'Hapus'"></span>
                        </button>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <div class="text-center py-5">
              <i class="fas fa-money-bill-wave fa-4x text-muted mb-3"></i>
              <h5 class="text-muted">Belum ada pembayaran</h5>
              <p class="text-muted">Klik tombol "Tambah Pembayaran" untuk mencatat pembayaran pertama</p>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Documents -->
        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Dokumen</h5>
            <div x-data="{ loadingManage: false, loadingUpload: false }">
              <a href="<?php echo e(route('admin.inventaris.document.index', $booking->id)); ?>" 
                 class="btn btn-sm btn-info mr-2"
                 @click="loadingManage = true"
                 :disabled="loadingManage">
                <i class="fas" :class="loadingManage ? 'fa-spinner fa-spin' : 'fa-folder-open'"></i> 
                <span x-text="loadingManage ? 'Memuat...' : 'Manage Documents'"></span>
              </a>
              <?php if($booking->status !== 'cancelled'): ?>
              <button type="button" 
                      class="btn btn-sm btn-primary" 
                      id="btn-upload-document"
                      @click="loadingUpload = true"
                      :disabled="loadingUpload">
                <i class="fas" :class="loadingUpload ? 'fa-spinner fa-spin' : 'fa-upload'"></i> 
                <span x-text="loadingUpload ? 'Memuat...' : 'Upload Dokumen'"></span>
              </button>
              <?php endif; ?>
            </div>
          </div>
          <div class="card-body">
            <?php if($booking->documents->count() > 0): ?>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Tipe Dokumen</th>
                    <th>Nomor</th>
                    <th>Tanggal Kadaluarsa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $booking->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e(ucfirst(str_replace('_', ' ', $doc->document_type))); ?></td>
                    <td><?php echo e($doc->document_number ?? '-'); ?></td>
                    <td>
                      <?php if($doc->expiry_date): ?>
                        <?php echo e($doc->expiry_date->format('d M Y')); ?>

                      <?php else: ?>
                        -
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php
                        $docBadges = [
                          'pending' => 'secondary',
                          'submitted' => 'info',
                          'approved' => 'success',
                          'rejected' => 'danger'
                        ];
                        $docColor = $docBadges[$doc->status] ?? 'secondary';
                      ?>
                      <span class="badge badge-<?php echo e($docColor); ?>"><?php echo e(ucfirst($doc->status)); ?></span>
                    </td>
                    <td>
                      <?php if($doc->file_path): ?>
                      <a href="<?php echo e(Storage::url($doc->file_path)); ?>" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-download"></i>
                      </a>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
            <p class="text-muted text-center mb-0">Belum ada dokumen</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Payment Summary -->
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="card-header">
            <h5 class="mb-0">Ringkasan Pembayaran</h5>
          </div>
          <div class="card-body">
            <?php
              // Hitung grand total termasuk anggota keluarga
              $showUnitPrice = 0;
              $showRoomType = $booking->price_variant ?? $booking->room_type ?? 'double';
              $showPkgName = $booking->price_package_name ?? null;
              $showPricePackages = $booking->travelPackage->price_packages ?? [];
              if (is_string($showPricePackages)) $showPricePackages = json_decode($showPricePackages, true);
              if (!empty($showPricePackages) && is_array($showPricePackages)) {
                  $showTargetPkg = null;
                  if ($showPkgName) { foreach ($showPricePackages as $pp) { if (strtolower($pp['name'] ?? '') === strtolower($showPkgName)) { $showTargetPkg = $pp; break; } } }
                  if (!$showTargetPkg) $showTargetPkg = $showPricePackages[0] ?? null;
                  if ($showTargetPkg) {
                      foreach ($showTargetPkg['variants'] ?? [] as $v) { if (strtolower($v['type'] ?? '') === strtolower($showRoomType)) { $showUnitPrice = (float)($v['price'] ?? 0); break; } }
                      if ($showUnitPrice == 0) { foreach ($showTargetPkg['variants'] ?? [] as $v) { if (strtolower($v['type'] ?? '') === 'double') { $showUnitPrice = (float)($v['price'] ?? 0); break; } } }
                  }
              }
              if ($showUnitPrice == 0) $showUnitPrice = (float)$booking->total_price;
              
              $showFamilyMembers = $booking->family_members_booking;
              if (is_string($showFamilyMembers)) $showFamilyMembers = json_decode($showFamilyMembers, true);
              if (!is_array($showFamilyMembers)) $showFamilyMembers = [];
              
              $showFamilyNormal = 0; $showFamilyDiscountTotal = 0;
              foreach ($showFamilyMembers as $fm) {
                  if (empty($fm['tanggal_lahir'])) { $showFamilyNormal++; }
                  else {
                      $fmAge = \Carbon\Carbon::parse($fm['tanggal_lahir'])->age;
                      if ($fmAge < 2) { $showFamilyDiscountTotal += 18000000; }
                      elseif ($fmAge <= 8) { $showFamilyDiscountTotal += $showUnitPrice * 0.85; }
                      else { $showFamilyNormal++; }
                  }
              }
              $showMainPax = 1 + $showFamilyNormal;

              // Hotel charge dari jamaah hotel bookings
              $showHotelCharge = $booking->hotelBookings ? $booking->hotelBookings->where('is_charged', true)->sum('total_cost') : 0;

              // Add-ons total
              $showAddonsTotal = $booking->addons ? $booking->addons->sum(fn($a) => $a->harga * $a->qty) : 0;

              // Handling fee
              $showHandlingFee = 0;
              if ($booking->travelPackage && $booking->travelPackage->include_handling_lounge_fee && $booking->travelPackage->handling_lounge_fee_amount > 0) {
                  $showHandlingFee = $booking->travelPackage->handling_lounge_fee_amount;
              }

              $showGrandTotal = ($showUnitPrice * $showMainPax) + $showFamilyDiscountTotal
                  + ($booking->equipment_cost ?? 0) + ($booking->upgrade_cost ?? 0)
                  + $showHotelCharge + $showAddonsTotal + $showHandlingFee
                  - $booking->discount_amount;
              $showSisa = max(0, $showGrandTotal - $booking->paid_amount);
            ?>
            <table class="table table-sm table-borderless">
              <?php if(count($showFamilyMembers) > 0): ?>
              <tr>
                <th>Paket Utama (<?php echo e($showMainPax); ?> Pax)</th>
                <td class="text-right">Rp <?php echo e(number_format($showUnitPrice * $showMainPax, 0, ',', '.')); ?></td>
              </tr>
              <?php if($showFamilyDiscountTotal > 0): ?>
              <tr>
                <th>Anggota Keluarga (Diskon)</th>
                <td class="text-right">Rp <?php echo e(number_format($showFamilyDiscountTotal, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>
              <?php else: ?>
              <tr>
                <th>Harga Paket</th>
                <td class="text-right">Rp <?php echo e(number_format($showUnitPrice, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>

              <?php if(($booking->equipment_cost ?? 0) > 0): ?>
              <tr>
                <th>Perlengkapan</th>
                <td class="text-right">Rp <?php echo e(number_format($booking->equipment_cost, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>
              <?php if(($booking->upgrade_cost ?? 0) > 0): ?>
              <tr>
                <th>Upgrade</th>
                <td class="text-right">Rp <?php echo e(number_format($booking->upgrade_cost, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>

              <?php if($showHotelCharge > 0): ?>
              <tr>
                <th>Hotel (Charge)</th>
                <td class="text-right text-warning">Rp <?php echo e(number_format($showHotelCharge, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>

              <?php if($showAddonsTotal > 0): ?>
              <tr>
                <th>Add-ons / Request</th>
                <td class="text-right text-info">Rp <?php echo e(number_format($showAddonsTotal, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>

              <?php if($showHandlingFee > 0): ?>
              <tr>
                <th><?php echo e($booking->travelPackage->handling_lounge_fee_description ?? 'Handling & Lounge Fee Wajib'); ?></th>
                <td class="text-right text-primary">Rp <?php echo e(number_format($showHandlingFee, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>

              <tr class="border-top">
                <th>Total Harga</th>
                <td class="text-right">Rp <?php echo e(number_format($showGrandTotal + $booking->discount_amount, 0, ',', '.')); ?></td>
              </tr>
              <?php if($booking->discount_amount > 0): ?>
              <tr>
                <th>Diskon</th>
                <td class="text-right text-danger">- Rp <?php echo e(number_format($booking->discount_amount, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>
              
              <?php if($booking->hasVoucher()): ?>
              <tr class="text-success">
                <th>Diskon Voucher (<?php echo e($booking->voucher_code); ?>)</th>
                <td class="text-right">- Rp <?php echo e(number_format($booking->voucher_discount, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>
              
              <?php if($booking->hasAdminDiscount()): ?>
              <tr class="text-success">
                <th>Diskon Admin</th>
                <td class="text-right">- Rp <?php echo e(number_format($booking->admin_discount, 0, ',', '.')); ?></td>
              </tr>
              <?php endif; ?>
              
              <?php if($booking->hasVoucher() || $booking->hasAdminDiscount()): ?>
              <tr class="border-top">
                <th>Total Setelah Diskon</th>
                <td class="text-right"><strong>Rp <?php echo e(number_format($booking->getFinalTotal(), 0, ',', '.')); ?></strong></td>
              </tr>
              <?php else: ?>
              <tr>
                <th>Total Bayar</th>
                <td class="text-right"><strong>Rp <?php echo e(number_format($showGrandTotal, 0, ',', '.')); ?></strong></td>
              </tr>
              <?php endif; ?>
              
              <tr>
                <th>Sudah Dibayar</th>
                <td class="text-right text-success">Rp <?php echo e(number_format($booking->paid_amount, 0, ',', '.')); ?></td>
              </tr>
              <tr class="border-top">
                <th>Sisa Pembayaran</th>
                <td class="text-right text-danger">
                  <strong>Rp <?php echo e(number_format($booking->getRemainingBalanceAfterDiscounts(), 0, ',', '.')); ?></strong>
                </td>
              </tr>
              <tr>
                <th>Status Pembayaran</th>
                <td class="text-right">
                  <?php
                    $paymentBadges = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success'];
                    $paymentColor = $paymentBadges[$booking->payment_status] ?? 'secondary';
                  ?>
                  <span class="badge badge-<?php echo e($paymentColor); ?>"><?php echo e(ucfirst($booking->payment_status)); ?></span>
                </td>
              </tr>
            </table>

            <?php if($booking->id_invoice && $booking->invoice): ?>
            <div class="alert alert-info mt-3">
              <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                  <i class="fas fa-file-invoice"></i>
                  <strong>Invoice:</strong> <?php echo e($booking->invoice->no_invoice ?? 'N/A'); ?>

                  <br>
                  <small class="d-block mt-2">
                    Status: 
                    <?php
                      $invoiceStatusBadges = [
                        'draft' => 'secondary',
                        'menunggu' => 'warning',
                        'dibayar_sebagian' => 'info',
                        'lunas' => 'success',
                        'gagal' => 'danger'
                      ];
                      $invoiceStatus = $booking->invoice->status ?? 'draft';
                      $invoiceColor = $invoiceStatusBadges[$invoiceStatus] ?? 'secondary';
                    ?>
                    <span class="badge badge-<?php echo e($invoiceColor); ?>">
                      <?php echo e(ucfirst(str_replace('_', ' ', $invoiceStatus))); ?>

                    </span>
                  </small>
                </div>
                <div class="btn-group-vertical btn-group-sm">
                  <a href="javascript:void(0)" 
                     onclick="window.open('<?php echo e(route('admin.inventaris.payment.jamaah-invoice-pdf', $booking->id)); ?>', '_blank')"
                     class="btn btn-sm btn-primary mb-1">
                    <i class="fas fa-file-pdf"></i> Lihat PDF
                  </a>
                  <a href="javascript:void(0)" 
                     onclick="window.open('<?php echo e(route('admin.inventaris.payment.jamaah-invoice-pdf', ['booking' => $booking->id, 'download' => 1])); ?>', '_blank')"
                     class="btn btn-sm btn-success mb-1">
                    <i class="fas fa-download"></i> Download
                  </a>
                  <?php if($booking->id_invoice): ?>
                  <button type="button" 
                          class="btn btn-sm btn-warning mb-1" 
                          id="btn-edit-invoice"
                          x-data="{ editing: false }"
                          @click="editing = true"
                          :disabled="editing">
                    <i class="fas" :class="editing ? 'fa-spinner fa-spin' : 'fa-edit'"></i> 
                    <span x-text="editing ? 'Memuat...' : 'Edit Invoice'"></span>
                  </button>
                  <button type="button" 
                          class="btn btn-sm btn-danger" 
                          id="btn-delete-invoice"
                          x-data="{ deleting: false }"
                          @click="deleting = true"
                          :disabled="deleting">
                    <i class="fas" :class="deleting ? 'fa-spinner fa-spin' : 'fa-trash'"></i> 
                    <span x-text="deleting ? 'Menghapus...' : 'Hapus Invoice'"></span>
                  </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <?php if($booking->payment_status !== 'paid'): ?>
            <div class="alert alert-warning mt-3">
              <small>
                <i class="fas fa-exclamation-triangle"></i>
                Pembayaran harus lunas sebelum keberangkatan
              </small>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Package Details -->
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Detail Paket</h5>
          </div>
          <div class="card-body">
            <table class="table table-sm table-borderless">
              <tr>
                <th>Tipe</th>
                <td class="text-right"><?php echo e(ucfirst($booking->travelPackage->package_type)); ?></td>
              </tr>
              <tr>
                <th>Durasi</th>
                <td class="text-right"><?php echo e($booking->travelPackage->duration_days); ?> hari</td>
              </tr>
              <tr>
                <th>Keberangkatan</th>
                <td class="text-right"><?php echo e($booking->travelPackage->departure_date->format('d M Y')); ?></td>
              </tr>
              <tr>
                <th>Kepulangan</th>
                <td class="text-right"><?php echo e($booking->travelPackage->return_date->format('d M Y')); ?></td>
              </tr>
              <?php if($booking->travelPackage->flightDeparture): ?>
              <tr>
                <th>Maskapai Berangkat</th>
                <td class="text-right">
                  <?php echo e($booking->travelPackage->flightDeparture->airline_name); ?>

                  (<?php echo e($booking->travelPackage->flightDeparture->flight_number); ?>)
                  <br><small class="text-muted"><?php echo e($booking->travelPackage->flightDeparture->departure_airport); ?> → <?php echo e($booking->travelPackage->flightDeparture->arrival_airport); ?></small>
                </td>
              </tr>
              <?php endif; ?>
              <?php if($booking->travelPackage->flightReturn): ?>
              <tr>
                <th>Maskapai Pulang</th>
                <td class="text-right">
                  <?php echo e($booking->travelPackage->flightReturn->airline_name); ?>

                  (<?php echo e($booking->travelPackage->flightReturn->flight_number); ?>)
                  <br><small class="text-muted"><?php echo e($booking->travelPackage->flightReturn->departure_airport); ?> → <?php echo e($booking->travelPackage->flightReturn->arrival_airport); ?></small>
                </td>
              </tr>
              <?php endif; ?>
              <?php
                $pkgHotelMakkah = $booking->travelPackage->hotelMakkah ?? null;
                $pkgHotelMadinah = $booking->travelPackage->hotelMadinah ?? null;
                $jamaahHotelMakkah = $booking->hotelBookings ? $booking->hotelBookings->where('city_type', 'makkah')->first() : null;
                $jamaahHotelMadinah = $booking->hotelBookings ? $booking->hotelBookings->where('city_type', 'madinah')->first() : null;
              ?>
              <tr>
                <th>Hotel Mekkah</th>
                <td class="text-right">
                  <?php if($jamaahHotelMakkah): ?>
                    <?php echo e($jamaahHotelMakkah->hotel->hotel_name ?? '-'); ?>

                    <?php if($jamaahHotelMakkah->room_type): ?> <small>(<?php echo e(ucfirst($jamaahHotelMakkah->room_type)); ?>)</small><?php endif; ?>
                    <?php if($jamaahHotelMakkah->is_charged): ?>
                      <span class="badge badge-warning badge-sm">Charge</span>
                    <?php else: ?>
                      <span class="badge badge-success badge-sm">Include</span>
                    <?php endif; ?>
                  <?php elseif($pkgHotelMakkah): ?>
                    <?php echo e($pkgHotelMakkah->hotel_name); ?>

                    <?php if($pkgHotelMakkah->star_rating): ?> <small>(<?php echo e($pkgHotelMakkah->star_rating); ?>⭐)</small><?php endif; ?>
                    <span class="badge badge-success badge-sm">Include</span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
              </tr>
              <tr>
                <th>Hotel Madinah</th>
                <td class="text-right">
                  <?php if($jamaahHotelMadinah): ?>
                    <?php echo e($jamaahHotelMadinah->hotel->hotel_name ?? '-'); ?>

                    <?php if($jamaahHotelMadinah->room_type): ?> <small>(<?php echo e(ucfirst($jamaahHotelMadinah->room_type)); ?>)</small><?php endif; ?>
                    <?php if($jamaahHotelMadinah->is_charged): ?>
                      <span class="badge badge-warning badge-sm">Charge</span>
                    <?php else: ?>
                      <span class="badge badge-success badge-sm">Include</span>
                    <?php endif; ?>
                  <?php elseif($pkgHotelMadinah): ?>
                    <?php echo e($pkgHotelMadinah->hotel_name); ?>

                    <?php if($pkgHotelMadinah->star_rating): ?> <small>(<?php echo e($pkgHotelMadinah->star_rating); ?>⭐)</small><?php endif; ?>
                    <span class="badge badge-success badge-sm">Include</span>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
              </tr>
              <tr>
                <th>Harga Paket</th>
                <td class="text-right">Rp <?php echo e(number_format($booking->travelPackage->price, 0, ',', '.')); ?></td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Communication History -->
        <div class="card mt-3">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Riwayat Komunikasi</h5>
              <button type="button" 
                      class="btn btn-sm btn-primary" 
                      id="btn-add-communication"
                      x-data="{ loading: false }"
                      @click="loading = true"
                      :disabled="loading">
                <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-plus'"></i> 
                <span x-text="loading ? 'Memuat...' : 'Tambah'"></span>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div id="communication-history-container">
              <p class="text-muted text-center">Memuat riwayat komunikasi...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Communication Modal -->
  <div class="modal fade" id="add-communication-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Komunikasi</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form id="add-communication-form">
          <div class="modal-body">
            <input type="hidden" name="id_member" value="<?php echo e($booking->id_member); ?>">
            <input type="hidden" name="id_travel_package" value="<?php echo e($booking->id_travel_package); ?>">
            
            <div class="form-group">
              <label>Metode Komunikasi <span class="text-danger">*</span></label>
              <select class="form-control" name="communication_method" required>
                <option value="">Pilih Metode</option>
                <option value="phone_call">Phone Call</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="email">Email</option>
                <option value="in_person">In Person</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="form-group">
              <label>Tanggal Komunikasi <span class="text-danger">*</span></label>
              <input type="datetime-local" class="form-control" name="communication_date" required>
            </div>

            <div class="form-group">
              <label>Catatan</label>
              <textarea class="form-control" name="notes" rows="3" placeholder="Tulis catatan komunikasi..."></textarea>
            </div>

            <div class="form-group">
              <label>Status Follow-up <span class="text-danger">*</span></label>
              <select class="form-control" name="follow_up_status" required>
                <option value="pending">Pending</option>
                <option value="contacted">Contacted</option>
                <option value="responded">Responded</option>
                <option value="no_response">No Response</option>
              </select>
            </div>

            <div class="form-group">
              <label>Tanggal Follow-up Berikutnya</label>
              <input type="date" class="form-control" name="next_follow_up_date">
              <small class="form-text text-muted">Kosongkan jika tidak perlu follow-up</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php $__env->startPush('scripts'); ?>
  <script>
    console.log('Booking detail page scripts loaded');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('Swal loaded:', typeof Swal !== 'undefined');
    
    $(document).ready(function() {
      console.log('Document ready');

      // Global modal cleanup — pastikan backdrop selalu dibersihkan
      function cleanupModal() {
        setTimeout(function() {
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open');
          $('body').css('padding-right', '');
        }, 300);
      }

      // Override data-dismiss="modal" to also cleanup
      $(document).on('click', '[data-dismiss="modal"]', function() {
        var $modal = $(this).closest('.modal');
        $modal.modal('hide');
        cleanupModal();
      });

      // Bind cleanup ke semua modal hidden events
      $(document).on('hidden.bs.modal', '.modal', function() {
        cleanupModal();
        // Jika ada modal lain yang masih open, restore modal-open class
        if ($('.modal.show').length > 0) {
          $('body').addClass('modal-open');
        }
      });

      // AGGRESSIVE FIX: Remove any leftover overlays/backdrops that block interaction
      function removeBlockingOverlays() {
        // 1. Remove any lightbox-related elements
        $('.lightbox-overlay, .lb-overlay, .lb-outerContainer, .lb-container, .lb-dataContainer, .lb-nav, .lb-closeContainer').remove();
        
        // 2. Remove orphaned modal backdrops (when no modal is shown)
        if ($('.modal.show').length === 0) {
          $('.modal-backdrop').remove();
          $('body').removeClass('modal-open');
          $('body').css({
            'overflow': '',
            'padding-right': '',
            'pointer-events': ''
          });
        }
        
        // 3. AGGRESSIVE: Remove ANY div that covers the screen and blocks interaction
        $('body > div, body > *').each(function() {
          var $el = $(this);
          
          // Skip if it's a known good element
          if ($el.is('script, style, link, meta') || 
              $el.hasClass('container-fluid') || 
              $el.hasClass('modal') ||
              $el.attr('id') === 'app' ||
              $el.closest('.modal').length > 0) {
            return; // skip
          }
          
          var zIndex = parseInt($el.css('z-index')) || 0;
          var position = $el.css('position');
          var pointerEvents = $el.css('pointer-events');
          
          // Check if element is blocking
          if ((position === 'fixed' || position === 'absolute') && zIndex > 1000) {
            var width = $el.outerWidth();
            var height = $el.outerHeight();
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            
            // If it covers most of the screen
            if (width > windowWidth * 0.5 && height > windowHeight * 0.5) {
              // Check if it's actually blocking (has content or background)
              var hasBackground = $el.css('background-color') !== 'rgba(0, 0, 0, 0)' && 
                                  $el.css('background-color') !== 'transparent';
              var hasContent = $el.children().length > 0 || $el.text().trim().length > 0;
              
              // If it's empty or just a backdrop, remove it
              if (!hasContent || hasBackground) {
                console.warn('Removed blocking overlay:', $el[0].tagName, $el.attr('class'));
                $el.remove();
              }
            }
          }
          
          // Also check for pointer-events: none on body or main containers
          if ($el.is('body') && pointerEvents === 'none') {
            $el.css('pointer-events', '');
          }
        });
        
        // 4. Ensure body is interactive
        $('body').css({
          'pointer-events': 'auto',
          'overflow': 'auto'
        });
      }

      // Run cleanup on page load
      setTimeout(removeBlockingOverlays, 500);

      // Run cleanup when user returns to page (from another tab)
      $(window).on('focus', function() {
        setTimeout(removeBlockingOverlays, 100);
        setTimeout(removeBlockingOverlays, 500); // Run again after 500ms
      });

      // Run cleanup when user clicks anywhere (in case something blocks)
      $(document).on('click', function(e) {
        // Only run if click didn't work (element might be blocked)
        setTimeout(function() {
          if ($('body').css('pointer-events') === 'none' || $('.modal-backdrop').length > 0 && $('.modal.show').length === 0) {
            removeBlockingOverlays();
          }
        }, 100);
      });

      // Run cleanup periodically but less frequently (every 5 seconds instead of 3)
      setInterval(removeBlockingOverlays, 5000);
      
      // Add payment button
      $('#btn-add-payment').on('click', function() {
        forceShowModal('#payment-modal');
      });

      // Create invoice button
      $('#btn-create-invoice').on('click', function() {
        console.log('Create invoice button clicked');
        console.log('Modal element:', $('#invoice-modal').length);
        
        // Reset form untuk create mode
        $('#invoice-modal .modal-title').text('Buat Invoice Jamaah');
        $('#btn-save-invoice').html('<i class="fas fa-save"></i> Simpan Invoice');
        $('#invoice-form')[0].reset();
        
        // Set default values
        $('#seller_name').val('<?php echo e(auth()->user()->name); ?>');
        $('#discount_amount').val('<?php echo e($booking->discount_amount ?? 0); ?>');
        $('#closing_source').val('<?php echo e($booking->closing_source ?? "kantor"); ?>');
        
        forceShowModal('#invoice-modal');
        loadInvoicePreview();
      });

      // Edit invoice button
      $('#btn-edit-invoice').on('click', function() {
        console.log('Edit invoice button clicked');
        
        // Change modal title and button text
        $('#invoice-modal .modal-title').text('Edit Invoice Jamaah');
        $('#btn-save-invoice').html('<i class="fas fa-save"></i> Update Invoice');
        
        // Load existing invoice data
        $('#seller_name').val('<?php echo e($booking->seller_name ?? auth()->user()->name); ?>');
        $('#discount_amount').val('<?php echo e($booking->discount_amount ?? 0); ?>');
        $('#closing_source').val('<?php echo e($booking->closing_source ?? "kantor"); ?>');
        $('#terms_conditions').val(`<?php echo e($booking->terms_conditions ?? ''); ?>`);
        
        forceShowModal('#invoice-modal');
        loadInvoicePreview();
      });

      // Helper: force show modal by destroying and recreating instance
      function forceShowModal(selector) {
        var $modal = $(selector);
        // Remove any stuck state
        $modal.removeData('bs.modal');
        $modal.removeClass('show');
        $modal.css('display', '');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
        // Small delay then show fresh
        setTimeout(function() {
          $modal.css('z-index', '99999');
          $modal.modal({ backdrop: true, keyboard: true, show: true });
        }, 50);
      }

      // Make forceShowModal globally available
      window.forceShowModal = forceShowModal;

      // Submit payment form
      $('#payment-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Payment form submitted');
        
        const formData = {
          payment_date: $('#payment_date').val(),
          amount: $('#amount').val(),
          payment_method: $('#payment_method').val(),
          reference_number: $('#reference_number').val(),
          notes: $('#notes').val(),
          _token: '<?php echo e(csrf_token()); ?>'
        };

        console.log('Payment form data:', formData);

        $.ajax({
          url: '<?php echo e(route("admin.inventaris.payment.store", $booking->id)); ?>',
          method: 'POST',
          data: formData,
          beforeSend: function() {
            // Disable submit button to prevent double submission
            $('#payment-form button[type="submit"]').prop('disabled', true);
          },
          success: function(response) {
            console.log('Payment success:', response);
            if (response.success) {
              // Hide modal properly
              $('#payment-modal').modal('hide');
              
              // Clean up modal backdrop
              $('.modal-backdrop').remove();
              $('body').removeClass('modal-open');
              $('body').css('padding-right', '');
              
              // Use Swal if available, otherwise use alert
              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  title: 'Berhasil',
                  text: response.message,
                  icon: 'success',
                  timer: 1500,
                  showConfirmButton: false
                }).then(() => {
                  // Reload halaman untuk menampilkan data pembayaran terbaru
                  window.location.reload();
                });
              } else {
                alert(response.message);
                window.location.reload();
              }
            }
          },
          error: function(xhr) {
            console.error('Payment error:', xhr);
            const message = xhr.responseJSON?.message || 'Gagal mencatat pembayaran';
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', message, 'error');
            } else {
              alert('Error: ' + message);
            }
            $('#payment-form button[type="submit"]').prop('disabled', false);
          }
        });
      });

      // Load invoice preview
      function loadInvoicePreview() {
        const discount = $('#discount_amount').val() || 0;
        const roomType = $('#room_type').val();
        const termsConditions = $('#terms_conditions').val();
        const sellerName = $('#seller_name').val();
        const closingSource = $('#closing_source').val();
        
        const previewUrl = '<?php echo e(route("admin.inventaris.payment.preview-invoice", $booking->id)); ?>' + 
          '?discount_amount=' + discount +
          '&room_type=' + encodeURIComponent(roomType) +
          '&terms_conditions=' + encodeURIComponent(termsConditions) +
          '&seller_name=' + encodeURIComponent(sellerName) +
          '&closing_source=' + encodeURIComponent(closingSource);
        
        console.log('Loading preview:', previewUrl);
        
        // Calculate optimal height for iframe
        const containerHeight = $('#pdf-preview-container').height();
        
        $('#pdf-preview-container').html(`
          <iframe src="${previewUrl}" 
                  style="width: 100%; 
                         height: ${containerHeight}px; 
                         min-height: 800px;
                         border: none; 
                         background: white;
                         transform-origin: top left;"
                  frameborder="0"
                  id="invoice-preview-iframe">
          </iframe>
        `);
        
        // Adjust iframe after load to ensure full visibility
        $('#invoice-preview-iframe').on('load', function() {
          try {
            const iframe = this;
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            // Get actual content height
            const contentHeight = iframeDoc.body.scrollHeight;
            
            // If content is taller than container, adjust
            if (contentHeight > containerHeight) {
              console.log('Content height:', contentHeight, 'Container height:', containerHeight);
              // Set iframe height to content height so scrolling works properly
              $(iframe).css('height', contentHeight + 'px');
            }
          } catch(e) {
            console.log('Cannot access iframe content (CORS):', e);
            // If CORS blocks us, just ensure minimum height
            $(this).css('min-height', '1200px');
          }
        });
      }

      // Refresh preview button
      $('#btn-refresh-preview').on('click', function() {
        loadInvoicePreview();
      });

      // Auto-refresh preview on input change (debounced)
      let previewTimeout;
      $('#discount_amount, #room_type, #terms_conditions, #seller_name, #closing_source').on('input change', function() {
        clearTimeout(previewTimeout);
        previewTimeout = setTimeout(function() {
          loadInvoicePreview();
        }, 800); // Wait 800ms after user stops typing
      });

      // Save invoice button
      $('#btn-save-invoice').on('click', function() {
        console.log('Save invoice button clicked');
        
        const isEditMode = $('#invoice-modal .modal-title').text().includes('Edit');
        console.log('Edit mode:', isEditMode);
        
        const formData = {
          discount_amount: $('#discount_amount').val() || 0,
          room_type: $('#room_type').val(),
          terms_conditions: $('#terms_conditions').val(),
          seller_name: $('#seller_name').val(),
          closing_source: $('#closing_source').val(),
          _token: '<?php echo e(csrf_token()); ?>'
        };

        console.log('Invoice form data:', formData);

        // Disable button to prevent double submission
        const buttonText = isEditMode ? 'Mengupdate...' : 'Menyimpan...';
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> ' + buttonText);

        // Use different endpoint for edit mode
        const url = isEditMode 
          ? '<?php echo e(route("admin.inventaris.payment.update-invoice", $booking->id)); ?>'
          : '<?php echo e(route("admin.inventaris.payment.create-invoice", $booking->id)); ?>';

        $.ajax({
          url: url,
          method: 'POST',
          data: formData,
          success: function(response) {
            console.log('Invoice success:', response);
            if (response.success) {
              // Hide modal properly
              $('#invoice-modal').modal('hide');
              
              // Clean up modal backdrop
              $('.modal-backdrop').remove();
              $('body').removeClass('modal-open');
              $('body').css('padding-right', '');
              
              const successMessage = isEditMode ? 'Invoice berhasil diupdate' : response.message;
              if (typeof Swal !== 'undefined') {
                Swal.fire('Berhasil', successMessage, 'success').then(() => {
                  location.reload();
                });
              } else {
                alert(successMessage);
                location.reload();
              }
            }
          },
          error: function(xhr) {
            console.error('Invoice error:', xhr);
            const message = xhr.responseJSON?.message || (isEditMode ? 'Gagal mengupdate invoice' : 'Gagal membuat invoice');
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', message, 'error');
            } else {
              alert('Error: ' + message);
            }
            const btnText = isEditMode ? '<i class="fas fa-save"></i> Update Invoice' : '<i class="fas fa-save"></i> Simpan Invoice';
            $('#btn-save-invoice').prop('disabled', false).html(btnText);
          }
        });
      });

      // Upload document button
      $('#btn-upload-document').on('click', function() {
        forceShowModal('#upload-document-modal');
      });

      // Submit document upload form
      $('#upload-document-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Document form submitted');
        
        const formData = new FormData(this);
        
        $.ajax({
          url: '<?php echo e(route("admin.inventaris.document.upload", $booking->id)); ?>',
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            console.log('Document success:', response);
            if (response.success) {
              // Hide modal properly
              $('#upload-document-modal').modal('hide');
              
              // Clean up modal backdrop
              $('.modal-backdrop').remove();
              $('body').removeClass('modal-open');
              $('body').css('padding-right', '');
              
              if (typeof Swal !== 'undefined') {
                Swal.fire('Berhasil', response.message, 'success').then(() => {
                  location.reload();
                });
              } else {
                alert(response.message);
                location.reload();
              }
            }
          },
          error: function(xhr) {
            console.error('Document error:', xhr);
            const message = xhr.responseJSON?.message || 'Gagal mengupload dokumen';
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', message, 'error');
            } else {
              alert('Error: ' + message);
            }
          }
        });
      });

      // Toggle expiry date field based on document type
      $('#document_type').on('change', function() {
        const requiresExpiry = ['passport', 'visa', 'health_certificate'];
        if (requiresExpiry.includes($(this).val())) {
          $('#expiry-date-group').show();
          $('#expiry_date').prop('required', true);
        } else {
          $('#expiry-date-group').hide();
          $('#expiry_date').prop('required', false);
        }
      });

      // Delete invoice button
      $(document).on('click', '#btn-delete-invoice', function() {
        const paymentCount = <?php echo e($booking->payments->count()); ?>;
        const paymentText = paymentCount > 0
          ? 'Invoice memiliki <strong>' + paymentCount + ' data pembayaran</strong> yang akan ikut dihapus.<br><br>'
          : '';
        const confirmMsg = paymentText + 'Invoice akan dihapus permanen dan booking kembali ke status tanpa invoice.';
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Hapus Invoice?', html: confirmMsg, icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
          }).then((result) => { if (result.isConfirmed) { doDeleteInvoice(); } });
        } else {
          if (confirm('Hapus invoice beserta semua pembayaran?')) { doDeleteInvoice(); }
        }
      });

      function doDeleteInvoice() {
        $.ajax({
          url: '<?php echo e(route("admin.inventaris.payment.delete-invoice", $booking->id)); ?>',
          method: 'DELETE',
          data: { _token: '<?php echo e(csrf_token()); ?>', force: 1 },
          success: function(response) {
            if (response.success) {
              if (typeof Swal !== 'undefined') {
                Swal.fire('Berhasil', response.message, 'success').then(() => location.reload());
              } else { alert(response.message); location.reload(); }
            } else {
              if (typeof Swal !== 'undefined') { Swal.fire('Error', response.message, 'error'); }
              else { alert(response.message); }
            }
          },
          error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Gagal menghapus invoice';
            if (typeof Swal !== 'undefined') { Swal.fire('Error', msg, 'error'); }
            else { alert(msg); }
          }
        });
      }

      // Load communication history
      function loadCommunicationHistory() {
        console.log('Loading communication history...');
        $.get('<?php echo e(route("admin.inventaris.travel.communication.member-history", $booking->id_member)); ?>', function(response) {
          console.log('Communication history response:', response);
          if (response.success) {
            const container = $('#communication-history-container');
            container.empty();
            
            if (response.data.length === 0) {
              container.append('<p class="text-muted text-center">Belum ada riwayat komunikasi</p>');
            } else {
              const timeline = $('<div class="timeline"></div>');
              
              response.data.forEach(function(comm) {
                const methodBadges = {
                  'phone_call': 'primary',
                  'whatsapp': 'success',
                  'email': 'info',
                  'in_person': 'warning',
                  'other': 'secondary'
                };
                const statusBadges = {
                  'pending': 'warning',
                  'contacted': 'info',
                  'responded': 'success',
                  'no_response': 'danger'
                };
                
                const methodColor = methodBadges[comm.communication_method] || 'secondary';
                const statusColor = statusBadges[comm.follow_up_status] || 'secondary';
                
                const item = $(`
                  <div class="timeline-item mb-3">
                    <div class="d-flex">
                      <div class="mr-3">
                        <i class="fas fa-comment-dots text-${methodColor}"></i>
                      </div>
                      <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                          <div>
                            <span class="badge badge-${methodColor}">${comm.communication_method_label}</span>
                            <span class="badge badge-${statusColor}">${comm.follow_up_status_label}</span>
                          </div>
                          <small class="text-muted">${new Date(comm.communication_date).toLocaleString()}</small>
                        </div>
                        <p class="mt-2 mb-1">${comm.notes || '<em class="text-muted">Tidak ada catatan</em>'}</p>
                        ${comm.next_follow_up_date ? `<small class="text-muted"><i class="fas fa-clock"></i> Follow-up: ${comm.next_follow_up_date}</small>` : ''}
                        <br><small class="text-muted">Oleh: ${comm.contacted_by_user?.name || '-'}</small>
                      </div>
                    </div>
                    <hr>
                  </div>
                `);
                
                timeline.append(item);
              });
              
              container.append(timeline);
            }
          }
        }).fail(function(xhr) {
          console.error('Failed to load communication history:', xhr);
        });
      }

      // Load communication history on page load
      loadCommunicationHistory();

      // Add communication button
      $('#btn-add-communication').on('click', function() {
        console.log('Add communication button clicked');
        $('#add-communication-form')[0].reset();
        $('input[name="communication_date"]').val(new Date().toISOString().slice(0, 16));
        forceShowModal('#add-communication-modal');
      });

      // Submit communication form
      $('#add-communication-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Communication form submitted');
        
        $.ajax({
          url: '<?php echo e(route("admin.inventaris.travel.communication.store")); ?>',
          method: 'POST',
          data: $(this).serialize() + '&_token=<?php echo e(csrf_token()); ?>',
          success: function(response) {
            console.log('Communication success:', response);
            if (response.success) {
              // Hide modal properly
              $('#add-communication-modal').modal('hide');
              
              // Clean up modal backdrop
              $('.modal-backdrop').remove();
              $('body').removeClass('modal-open');
              $('body').css('padding-right', '');
              
              loadCommunicationHistory();
              if (typeof Swal !== 'undefined') {
                Swal.fire('Berhasil', response.message, 'success');
              } else {
                alert(response.message);
              }
            }
          },
          error: function(xhr) {
            console.error('Communication error:', xhr);
            const message = xhr.responseJSON?.message || 'Gagal menyimpan komunikasi';
            if (typeof Swal !== 'undefined') {
              Swal.fire('Error', message, 'error');
            } else {
              alert('Error: ' + message);
            }
          }
        });
      });

      // Clean up any leftover modal backdrops on page load
      $(document).ready(function() {
        // Remove any modal-backdrop that might be stuck
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
        
        // Remove any pointer-events blocking
        $('body').css('pointer-events', 'auto');

        // Handle receipt view button with window.open
        $(document).on('click', '.btn-view-receipt', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          const url = $(this).data('url');
          console.log('Opening receipt in new window:', url);
          
          // Open in new window/tab
          window.open(url, '_blank', 'noopener,noreferrer');
          
          // Cleanup backdrop hanya jika tidak ada modal yang terbuka
          setTimeout(function() {
            if ($('.modal.show').length === 0 && $('.modal:visible').length === 0) {
              $('.modal-backdrop').remove();
              $('body').removeClass('modal-open');
              $('body').css('padding-right', '');
              $('body').css('pointer-events', 'auto');
            }
          }, 500);
          
          return false;
        });

        // Ensure receipt links work properly (fallback for any remaining <a> tags)
        $('a[href*="payment.receipt"]').on('click', function(e) {
          // Don't prevent default - let the link open in new tab
          console.log('Receipt link clicked:', $(this).attr('href'));
          
          // Clean up any modal backdrops immediately
          setTimeout(function() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
          }, 100);
        });
      });

      // Also clean up when any modal is hidden
      $('.modal').on('hidden.bs.modal', function () {
        // Remove all backdrops
        $('.modal-backdrop').remove();
        // Remove modal-open class from body
        $('body').removeClass('modal-open');
        // Reset body padding
        $('body').css('padding-right', '');
      });

      // Delete payment handler
      $(document).on('click', '.btn-delete-payment', function() {
        const paymentId = $(this).data('id');
        const amount = $(this).data('amount');
        const date = $(this).data('date');

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Hapus Pembayaran?',
            html: `Pembayaran <strong>${amount}</strong> tanggal <strong>${date}</strong> akan dihapus permanen.<br><br>Saldo booking akan dikurangi sesuai jumlah pembayaran ini.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
          }).then((result) => {
            if (result.isConfirmed) { doDeletePayment(paymentId); }
          });
        } else {
          if (confirm(`Hapus pembayaran ${amount}?`)) { doDeletePayment(paymentId); }
        }
      });

      function doDeletePayment(paymentId) {
        $.ajax({
          url: '<?php echo e(url("admin/inventaris/travel/payment")); ?>/' + paymentId,
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
          success: function() {
            if (typeof Swal !== 'undefined') {
              Swal.fire('Berhasil', 'Pembayaran berhasil dihapus', 'success').then(() => location.reload());
            } else {
              alert('Berhasil'); location.reload();
            }
          },
          error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Gagal menghapus pembayaran';
            if (typeof Swal !== 'undefined') { Swal.fire('Error', msg, 'error'); }
            else { alert(msg); }
          }
        });
      }

      // Clean up when window regains focus (after opening PDF in new tab)
      // Hanya cleanup jika tidak ada modal yang sedang terbuka
      $(window).on('focus', function() {
        console.log('Window regained focus - cleaning up');
        // Tunggu sebentar agar modal sempat render sebelum dicek
        setTimeout(function() {
          if ($('.modal.show').length === 0 && $('.modal:visible').length === 0) {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $('body').css('pointer-events', 'auto');
            $('body').css('overflow', 'auto');
          }
        }, 300);
      });

      // Periodic cleanup every 2 seconds to ensure no stuck backdrops
      setInterval(function() {
        if ($('.modal:visible').length === 0 && $('.modal.show').length === 0) {
          // No visible modals, clean up any stuck backdrops
          if ($('.modal-backdrop').length > 0) {
            console.log('Cleaning up stuck backdrop');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $('body').css('pointer-events', 'auto');
          }
        }
      }, 2000);
    });
  </script>

  <script>
    function addonManager(bookingId) {
      return {
        bookingId: bookingId,
        addons: [],
        loading: false,
        saving: false,
        addonForm: { id: null, nama: '', keterangan: '', harga: 0, qty: 1, masuk_hpp: true },

        get totalAddons() {
          return this.addons.reduce((sum, a) => sum + (parseFloat(a.harga) * parseInt(a.qty)), 0);
        },

        async init() {
          await this.fetchAddons();
        },

        async fetchAddons() {
          this.loading = true;
          try {
            const res = await fetch(`/hm/admin/inventaris/travel/booking/${this.bookingId}/addons`);
            const data = await res.json();
            this.addons = data.data || [];
          } catch(e) { console.error(e); }
          finally { this.loading = false; }
        },

        openAdd() {
          this.addonForm = { id: null, nama: '', keterangan: '', harga: 0, qty: 1, masuk_hpp: true };
          window.forceShowModal('#addonModal');
        },

        openEdit(addon) {
          this.addonForm = { id: addon.id, nama: addon.nama, keterangan: addon.keterangan || '', harga: parseFloat(addon.harga), qty: parseInt(addon.qty), masuk_hpp: !!addon.masuk_hpp };
          window.forceShowModal('#addonModal');
        },

        async saveAddon() {
          if (!this.addonForm.nama) { alert('Nama add-on wajib diisi'); return; }
          this.saving = true;
          try {
            const url = this.addonForm.id
              ? `/hm/admin/inventaris/travel/booking/${this.bookingId}/addons/${this.addonForm.id}`
              : `/hm/admin/inventaris/travel/booking/${this.bookingId}/addons`;
            const method = this.addonForm.id ? 'PUT' : 'POST';
            const res = await fetch(url, {
              method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
              body: JSON.stringify(this.addonForm)
            });
            const result = await res.json();
            if (res.ok) { 
            $('#addonModal').css('z-index', '99999').modal('hide'); 
            await this.fetchAddons(); 
          }
            else { alert(result.message || 'Gagal menyimpan'); }
          } catch(e) { alert('Terjadi kesalahan'); }
          finally { this.saving = false; }
        },

        async deleteAddon(id) {
          if (!confirm('Hapus add-on ini?')) return;
          const res = await fetch(`/hm/admin/inventaris/travel/booking/${this.bookingId}/addons/${id}`, {
            method: 'DELETE', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
          });
          if (res.ok) await this.fetchAddons();
        },

        formatNum(n) { return new Intl.NumberFormat('id-ID').format(n || 0); }
      };
    }

    function jamaahHotelManager(bookingId) {
      return {
        bookingId: bookingId,
        hotels: [],
        availableHotels: [],
        availableRoomTypes: [],
        loading: false,
        saving: false,
        hotelForm: { id: null, city_type: 'makkah', id_hotel: '', room_type: '', check_in_date: '', check_out_date: '', nights: 0, price_per_night: 0, is_charged: false, notes: '' },

        get totalCharged() {
          return this.hotels.filter(h => h.is_charged).reduce((s,h) => s + (h.total_cost||0), 0);
        },

        async init() {
          await Promise.all([this.fetchHotels(), this.fetchAvailableHotels()]);
        },

        async fetchHotels() {
          this.loading = true;
          try {
            const res = await fetch(`/hm/admin/inventaris/travel/booking/${this.bookingId}/hotel-bookings`);
            const data = await res.json();
            this.hotels = data.data || [];
          } catch(e) { console.error(e); }
          finally { this.loading = false; }
        },

        async fetchAvailableHotels() {
          try {
            const res = await fetch(`<?php echo e(route('admin.inventaris.hotel.data')); ?>`);
            const data = await res.json();
            this.availableHotels = (data.data || []).map(h => ({
              id: h.id,
              hotel_name: h.hotel_name,
              city: h.city_country || '',
              room_types_count: h.room_types_count || 0
            }));
          } catch(e) {}
        },

        async onHotelChange() {
          this.availableRoomTypes = [];
          this.hotelForm.room_type = '';
          if (!this.hotelForm.id_hotel) return;
          try {
            const res = await fetch(`<?php echo e(url('admin/inventaris/hotel')); ?>/${this.hotelForm.id_hotel}/room-types`);
            const data = await res.json();
            this.availableRoomTypes = data.data || [];
          } catch(e) {}
        },

        openAdd() {
          this.hotelForm = { id: null, city_type: 'makkah', id_hotel: '', room_type: '', check_in_date: '', check_out_date: '', nights: 0, price_per_night: 0, is_charged: false, notes: '' };
          this.availableRoomTypes = [];
          window.forceShowModal('#hotelBookingModal');
        },

        async openEdit(hb) {
          this.hotelForm = { id: hb.id, city_type: hb.city_type, id_hotel: hb.id_hotel, room_type: hb.room_type||'', check_in_date: hb.check_in_date||'', check_out_date: hb.check_out_date||'', nights: hb.nights, price_per_night: parseFloat(hb.price_per_night)||0, is_charged: hb.is_charged, notes: hb.notes||'' };
          // Load room types for selected hotel
          if (hb.id_hotel) {
            try {
              const res = await fetch(`<?php echo e(url('admin/inventaris/hotel')); ?>/${hb.id_hotel}/room-types`);
              const data = await res.json();
              this.availableRoomTypes = data.data || [];
            } catch(e) { this.availableRoomTypes = []; }
          }
          window.forceShowModal('#hotelBookingModal');
        },

        calcNights() {
          if (this.hotelForm.check_in_date && this.hotelForm.check_out_date) {
            const d1 = new Date(this.hotelForm.check_in_date);
            const d2 = new Date(this.hotelForm.check_out_date);
            this.hotelForm.nights = Math.max(0, Math.round((d2-d1)/(1000*60*60*24)));
          }
        },

        async saveHotel() {
          if (!this.hotelForm.id_hotel) { alert('Pilih hotel terlebih dahulu'); return; }
          this.saving = true;
          try {
            const url = this.hotelForm.id
              ? `/hm/admin/inventaris/travel/booking/${this.bookingId}/hotel-bookings/${this.hotelForm.id}`
              : `/hm/admin/inventaris/travel/booking/${this.bookingId}/hotel-bookings`;
            const method = this.hotelForm.id ? 'PUT' : 'POST';
            const res = await fetch(url, {
              method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
              body: JSON.stringify(this.hotelForm)
            });
            const result = await res.json();
            if (res.ok) { $('#hotelBookingModal').modal('hide'); await this.fetchHotels(); }
            else { alert(result.message || 'Gagal menyimpan'); }
          } catch(e) { alert('Terjadi kesalahan'); }
          finally { this.saving = false; }
        },

        async deleteHotel(id) {
          if (!confirm('Hapus hotel booking ini?')) return;
          const res = await fetch(`/hm/admin/inventaris/travel/booking/${this.bookingId}/hotel-bookings/${id}`, {
            method: 'DELETE', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
          });
          if (res.ok) await this.fetchHotels();
        },

        formatNum(n) { return new Intl.NumberFormat('id-ID').format(n || 0); }
      };
    }
  </script>
  <?php $__env->stopPush(); ?>

  <!-- Payment Modal -->
  <div class="modal fade" id="payment-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Pembayaran</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form id="payment-form">
          <div class="modal-body">
            <div class="form-group">
              <label>Tanggal Pembayaran <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="payment_date" name="payment_date" 
                     value="<?php echo e(date('Y-m-d')); ?>" required>
            </div>
            
            <div class="form-group">
              <label>Jumlah <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="amount" name="amount" 
                     min="0" max="<?php echo e($showSisa); ?>" step="0.01" required>
              <small class="form-text text-muted">
                Maksimal: Rp <?php echo e(number_format($showSisa, 0, ',', '.')); ?>

              </small>
            </div>
            
            <div class="form-group">
              <label>Metode Pembayaran <span class="text-danger">*</span></label>
              <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="">Pilih Metode</option>
                <option value="cash">Tunai</option>
                <option value="transfer">Transfer Bank</option>
                <option value="credit_card">Kartu Kredit</option>
                <option value="debit_card">Kartu Debit</option>
                <option value="other">Lainnya</option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Nomor Referensi</label>
              <input type="text" class="form-control" id="reference_number" name="reference_number" 
                     placeholder="Nomor transaksi/referensi">
            </div>
            
            <div class="form-group">
              <label>Keterangan</label>
              <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Simpan Pembayaran</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Invoice Modal with Preview -->
  <div class="modal fade" id="invoice-modal" tabindex="-1" role="dialog" style="z-index:99998;">
    <div class="modal-dialog modal-xl" role="document" style="margin: 1rem auto; max-width: 96%;">
      <div class="modal-content" style="display: flex; flex-direction: column; max-height: calc(100vh - 2rem); overflow: hidden;">
        <div class="modal-header" style="flex-shrink: 0; padding: 0.75rem 1rem;">
          <h5 class="modal-title">Buat Invoice Jamaah</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body" style="flex: 1; overflow: hidden; padding: 0; display: flex;">
          <div style="display: flex; width: 100%; height: 100%;">
            <!-- Left Panel: Form Penyesuaian -->
            <div style="width: 35%; padding: 15px; overflow-y: auto; border-right: 1px solid #dee2e6;">
              <h6 class="mb-3"><i class="fas fa-sliders-h"></i> Penyesuaian Invoice</h6>
              
              <form id="invoice-form">
                <!-- Seller/Closing -->
                <div class="card mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                  <div class="card-header bg-light py-2">
                    <strong style="font-size: 13px;">Seller/Closing</strong>
                  </div>
                  <div class="card-body py-2">
                    <div class="form-group mb-2">
                      <label class="mb-1" style="font-size: 12px;">Nama Seller</label>
                      <input type="text" class="form-control form-control-sm" id="seller_name" name="seller_name" 
                             value="<?php echo e(auth()->user()->name); ?>" placeholder="Nama seller/closing">
                      <small class="form-text text-muted">Default: User yang login</small>
                    </div>
                    <div class="form-group mb-0">
                      <label class="mb-1" style="font-size: 12px;">Sumber Closing</label>
                      <select class="form-control form-control-sm" id="closing_source" name="closing_source">
                        <option value="kantor" <?php echo e(($booking->closing_source ?? '') == 'kantor' ? 'selected' : ''); ?>>Kantor</option>
                        <option value="alumni" <?php echo e(($booking->closing_source ?? '') == 'alumni' ? 'selected' : ''); ?>>Alumni</option>
                        <option value="digital_marketing" <?php echo e(($booking->closing_source ?? '') == 'digital_marketing' ? 'selected' : ''); ?>>Digital Marketing</option>
                        <option value="event" <?php echo e(($booking->closing_source ?? '') == 'event' ? 'selected' : ''); ?>>Event</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Diskon -->
                <div class="card mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                  <div class="card-header bg-light py-2">
                    <strong style="font-size: 13px;">Diskon</strong>
                  </div>
                  <div class="card-body py-2">
                    <div class="form-group mb-0">
                      <label class="mb-1" style="font-size: 12px;">Jumlah Diskon</label>
                      <input type="number" class="form-control form-control-sm" id="discount_amount" name="discount_amount" 
                             value="0" min="0" max="<?php echo e($booking->total_price); ?>" step="1000">
                      <small class="form-text text-muted">Dalam Rupiah</small>
                    </div>
                  </div>
                </div>

                <!-- Syarat dan Ketentuan -->
                <div class="card mb-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                  <div class="card-header bg-light py-2">
                    <strong style="font-size: 13px;">Syarat dan Ketentuan</strong>
                  </div>
                  <div class="card-body py-2">
                    <div class="form-group mb-0">
                      <textarea class="form-control form-control-sm" id="terms_conditions" name="terms_conditions" 
                                rows="16" style="font-size: 11px; line-height: 1.4;">Ketentuan Pembelian Paket Umrah:

1. Pembayaran DP untuk Booking seat sebesar 10 juta/pax di transfer ke rekening Perusahaan.

2. Pembayaran 50% harga paket dilakukan maksimal H-40 dari tanggal keberangkatan.

3. Pelunasan dilakukan paling lambat H-30 dari tanggal keberangkatan.

4. Ketentuan Pembatalan:
   a. Pembatalan dikenakan biaya 3 juta/Jemaah Non Refundable.
   b. Pembatalan setelah H-30 dikenakan biaya seharga tiket pesawat.
   c. Pembatalan/perubahan paket H-20 Non Refundable.

5. Tiket pesawat kelas ekonomi. Untuk upgrade ke bisnis, hubungi kami untuk ketersediaan kursi dan biaya tambahan.

6. Jika hingga H-16 keberangkatan kuota kamar paket QUAD belum terpenuhi, pembeli harus upgrade sesuai ketersediaan kamar.

7. Jika terdapat Force Majure maka tidak dapat dibebankan kepada travel.</textarea>
                    </div>
                  </div>
                </div>

                <div class="alert alert-info alert-sm mb-0 py-2" style="font-size: 11px;">
                  <i class="fas fa-info-circle"></i> Perubahan akan langsung terlihat di preview
                </div>
              </form>
            </div>

            <!-- Right Panel: Preview PDF -->
            <div style="width: 65%; display: flex; flex-direction: column; background: #525659;">
              <div class="d-flex justify-content-between align-items-center px-3 py-2 bg-dark text-white" style="flex-shrink: 0;">
                <span style="font-size: 13px;"><i class="fas fa-file-pdf"></i> Preview Invoice</span>
                <div>
                  <button type="button" class="btn btn-sm btn-light" id="btn-refresh-preview">
                    <i class="fas fa-sync-alt"></i> Refresh
                  </button>
                </div>
              </div>
              <div id="pdf-preview-container" style="flex: 1; overflow: auto; background: #525659; position: relative;">
                <div class="text-center text-white py-5">
                  <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                  <p>Memuat preview invoice...</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="flex-shrink: 0; padding: 0.5rem 1rem;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="btn-save-invoice">
            <i class="fas fa-save"></i> Simpan Invoice
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Upload Document Modal -->
  <div class="modal fade" id="upload-document-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document" style="margin-top: 2rem;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Upload Dokumen</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <form id="upload-document-form">
          <?php echo csrf_field(); ?>
          <div class="modal-body">
            <div class="form-group">
              <label>Tipe Dokumen <span class="text-danger">*</span></label>
              <select class="form-control" id="document_type" name="document_type" required>
                <option value="">Pilih Tipe Dokumen</option>
                <option value="passport">Passport</option>
                <option value="visa">Visa</option>
                <option value="ticket">Tiket</option>
                <option value="insurance">Asuransi</option>
                <option value="health_certificate">Sertifikat Kesehatan</option>
              </select>
            </div>
            
            <div class="form-group">
              <label>Nomor Dokumen</label>
              <input type="text" class="form-control" name="document_number" 
                     placeholder="Nomor dokumen (opsional)">
            </div>
            
            <div class="form-group">
              <label>Tanggal Terbit</label>
              <input type="date" class="form-control" name="issue_date">
            </div>
            
            <div class="form-group" id="expiry-date-group" style="display: none;">
              <label>Tanggal Kadaluarsa <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="expiry_date" name="expiry_date">
              <small class="form-text text-muted">
                Untuk passport, minimal 6 bulan dari tanggal keberangkatan
              </small>
            </div>
            
            <div class="form-group">
              <label>File Dokumen <span class="text-danger">*</span></label>
              <input type="file" class="form-control-file" name="file" 
                     accept=".pdf,.jpg,.jpeg,.png" required>
              <small class="form-text text-muted">
                Format: PDF, JPG, PNG. Maksimal 5MB
              </small>
            </div>
            
            <div class="form-group">
              <label>Catatan</label>
              <textarea class="form-control" name="notes" rows="3" 
                        placeholder="Catatan tambahan (opsional)"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Upload</button>
          </div>
        </form>
      </div>
    </div>
  </div>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/booking/show.blade.php ENDPATH**/ ?>