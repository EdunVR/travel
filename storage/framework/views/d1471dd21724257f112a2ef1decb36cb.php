<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => ($isEdit ? 'Edit' : 'Tambah') . ' Hotel Booking - ' . $booking->booking_code]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($isEdit ? 'Edit' : 'Tambah') . ' Hotel Booking - ' . $booking->booking_code)]); ?>
  <div class="container-fluid" x-data="hotelBookingForm()">
    <!-- Header -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0"><?php echo e($isEdit ? 'Edit' : 'Tambah'); ?> Hotel Booking</h1>
            <p class="text-muted">Booking: <?php echo e($booking->booking_code); ?> - <?php echo e($booking->jamaah->nama); ?></p>
          </div>
          <div>
            <a href="<?php echo e(route('admin.inventaris.booking.show', $booking->id)); ?>" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><?php echo e($isEdit ? 'Edit' : 'Tambah'); ?> Hotel Booking</h5>
          </div>
          <div class="card-body">
            <form action="<?php echo e($isEdit ? route('admin.inventaris.booking.hotel-bookings.update', [$booking->id, $hotelBooking->id]) : route('admin.inventaris.booking.hotel-bookings.store', $booking->id)); ?>" method="POST">
              <?php echo csrf_field(); ?>
              <?php if($isEdit): ?>
                <?php echo method_field('PUT'); ?>
              <?php endif; ?>

              <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
              <?php endif; ?>

              <?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
              <?php endif; ?>

              <div class="form-group">
                <label>Kota <span class="text-danger">*</span></label>
                <select name="city_type" class="form-control <?php $__errorArgs = ['city_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                  <option value="makkah" <?php echo e(old('city_type', $hotelBooking->city_type ?? 'makkah') == 'makkah' ? 'selected' : ''); ?>>Mekkah</option>
                  <option value="madinah" <?php echo e(old('city_type', $hotelBooking->city_type ?? '') == 'madinah' ? 'selected' : ''); ?>>Madinah</option>
                </select>
                <?php $__errorArgs = ['city_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label>Hotel <span class="text-danger">*</span></label>
                <select name="id_hotel" x-model="selectedHotelId" @change="onHotelChange()" 
                        class="form-control <?php $__errorArgs = ['id_hotel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                  <option value="">Pilih Hotel</option>
                  <?php $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($hotel->id); ?>" 
                            data-room-types="<?php echo e(json_encode($hotel->roomTypes)); ?>"
                            <?php echo e(old('id_hotel', $hotelBooking->id_hotel ?? '') == $hotel->id ? 'selected' : ''); ?>>
                      <?php echo e($hotel->hotel_name); ?> - <?php echo e($hotel->location); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['id_hotel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label>Tipe Kamar</label>
                <select name="room_type" x-model="selectedRoomType" @change="onRoomTypeChange()"
                        class="form-control <?php $__errorArgs = ['room_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                  <option value="">Pilih Tipe Kamar</option>
                  <template x-for="rt in roomTypes" :key="rt.id">
                    <option :value="rt.room_type_name" 
                            :data-price="rt.price_per_night"
                            x-text="`${rt.room_type_name} - Rp ${formatNumber(rt.price_per_night)}/malam`"></option>
                  </template>
                  <!-- Fallback options -->
                  <template x-if="roomTypes.length === 0">
                    <option value="quad">Quad (4 orang)</option>
                  </template>
                  <template x-if="roomTypes.length === 0">
                    <option value="triple">Triple (3 orang)</option>
                  </template>
                  <template x-if="roomTypes.length === 0">
                    <option value="double">Double (2 orang)</option>
                  </template>
                  <template x-if="roomTypes.length === 0">
                    <option value="single">Single (1 orang)</option>
                  </template>
                </select>
                <small class="text-muted" x-show="roomTypes.length > 0">Harga/malam otomatis terisi dari tipe kamar</small>
                <small class="text-muted" x-show="roomTypes.length === 0 && selectedHotelId">Hotel ini belum memiliki tipe kamar, isi harga manual</small>
                <?php $__errorArgs = ['room_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Check-in</label>
                    <input type="date" name="check_in_date" x-model="checkInDate" @change="calculateNights()"
                           class="form-control <?php $__errorArgs = ['check_in_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           value="<?php echo e(old('check_in_date', $hotelBooking->check_in_date ?? '')); ?>">
                    <?php $__errorArgs = ['check_in_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                      <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Check-out</label>
                    <input type="date" name="check_out_date" x-model="checkOutDate" @change="calculateNights()"
                           class="form-control <?php $__errorArgs = ['check_out_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           value="<?php echo e(old('check_out_date', $hotelBooking->check_out_date ?? '')); ?>">
                    <?php $__errorArgs = ['check_out_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                      <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jumlah Malam</label>
                    <input type="number" name="nights" x-model.number="nights" min="0"
                           class="form-control <?php $__errorArgs = ['nights'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           value="<?php echo e(old('nights', $hotelBooking->nights ?? 0)); ?>">
                    <?php $__errorArgs = ['nights'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                      <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Harga/Malam (Rp)</label>
                    <input type="number" name="price_per_night" x-model.number="pricePerNight" min="0"
                           class="form-control <?php $__errorArgs = ['price_per_night'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           value="<?php echo e(old('price_per_night', $hotelBooking->price_per_night ?? 0)); ?>">
                    <small class="text-muted" x-text="`Total: Rp ${formatNumber(pricePerNight * nights)}`"></small>
                    <?php $__errorArgs = ['price_per_night'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                      <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                  </div>
                </div>
              </div>

              <input type="hidden" name="total_cost" :value="pricePerNight * nights">

              <div class="form-group">
                <div class="custom-control custom-switch">
                  <input type="checkbox" name="is_charged" value="1" class="custom-control-input" id="isCharged"
                         <?php echo e(old('is_charged', $hotelBooking->is_charged ?? false) ? 'checked' : ''); ?>>
                  <label class="custom-control-label" for="isCharged">
                    <strong>Charge ke Invoice</strong>
                  </label>
                </div>
                <small class="text-muted">
                  Jika dicentang, biaya hotel akan ditambahkan ke invoice jamaah. Jika tidak, keterangan "Include Paket".
                </small>
              </div>

              <div class="form-group">
                <label>Catatan</label>
                <textarea name="notes" class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          rows="2"><?php echo e(old('notes', $hotelBooking->notes ?? '')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> Simpan
                </button>
                <a href="<?php echo e(route('admin.inventaris.booking.show', $booking->id)); ?>" class="btn btn-secondary">
                  Batal
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Booking Info Sidebar -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Informasi Booking</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm table-borderless">
              <tr>
                <th>Jamaah:</th>
                <td><?php echo e($booking->jamaah->nama); ?></td>
              </tr>
              <tr>
                <th>Paket:</th>
                <td><?php echo e($booking->travelPackage->package_name); ?></td>
              </tr>
              <tr>
                <th>Total Harga:</th>
                <td>Rp <?php echo e(number_format($booking->total_price, 0, ',', '.')); ?></td>
              </tr>
              <tr>
                <th>Status:</th>
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
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php $__env->startPush('scripts'); ?>
  <script>
    function hotelBookingForm() {
      return {
        selectedHotelId: '<?php echo e(old('id_hotel', $hotelBooking->id_hotel ?? '')); ?>',
        selectedRoomType: '<?php echo e(old('room_type', $hotelBooking->room_type ?? '')); ?>',
        checkInDate: '<?php echo e(old('check_in_date', $hotelBooking->check_in_date ?? '')); ?>',
        checkOutDate: '<?php echo e(old('check_out_date', $hotelBooking->check_out_date ?? '')); ?>',
        nights: <?php echo e(old('nights', $hotelBooking->nights ?? 0)); ?>,
        pricePerNight: <?php echo e(old('price_per_night', $hotelBooking->price_per_night ?? 0)); ?>,
        roomTypes: [],

        init() {
          if (this.selectedHotelId) {
            this.loadRoomTypes();
          }
        },

        onHotelChange() {
          this.loadRoomTypes();
          this.selectedRoomType = '';
          this.pricePerNight = 0;
        },

        loadRoomTypes() {
          const select = document.querySelector('select[name="id_hotel"]');
          const option = select.querySelector(`option[value="${this.selectedHotelId}"]`);
          if (option) {
            const roomTypesData = option.getAttribute('data-room-types');
            this.roomTypes = roomTypesData ? JSON.parse(roomTypesData) : [];
          } else {
            this.roomTypes = [];
          }
        },

        onRoomTypeChange() {
          const rt = this.roomTypes.find(r => r.room_type_name === this.selectedRoomType);
          if (rt) {
            this.pricePerNight = parseFloat(rt.price_per_night) || 0;
          }
        },

        calculateNights() {
          if (this.checkInDate && this.checkOutDate) {
            const checkIn = new Date(this.checkInDate);
            const checkOut = new Date(this.checkOutDate);
            const diffTime = checkOut - checkIn;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            this.nights = diffDays > 0 ? diffDays : 0;
          }
        },

        formatNumber(num) {
          return new Intl.NumberFormat('id-ID').format(num || 0);
        }
      };
    }
  </script>
  <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\booking\hotel-booking-form.blade.php ENDPATH**/ ?>