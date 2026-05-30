<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => ($isEdit ? 'Edit' : 'Tambah') . ' Add-on - Booking ' . $booking->booking_code]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($isEdit ? 'Edit' : 'Tambah') . ' Add-on - Booking ' . $booking->booking_code)]); ?>
  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0"><?php echo e($isEdit ? 'Edit' : 'Tambah'); ?> Add-on</h1>
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
            <h5 class="mb-0"><?php echo e($isEdit ? 'Edit' : 'Tambah'); ?> Add-on</h5>
          </div>
          <div class="card-body">
            <form action="<?php echo e($isEdit ? route('admin.inventaris.booking.addons.update', [$booking->id, $addon->id]) : route('admin.inventaris.booking.addons.store', $booking->id)); ?>" method="POST">
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
                <label>Nama <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('nama', $addon->nama ?? '')); ?>" 
                       placeholder="Contoh: Upgrade Kamar, Makan Tambahan" required>
                <?php $__errorArgs = ['nama'];
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
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          rows="3" placeholder="Deskripsi tambahan"><?php echo e(old('keterangan', $addon->keterangan ?? '')); ?></textarea>
                <?php $__errorArgs = ['keterangan'];
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
                    <label>Harga <span class="text-danger">*</span></label>
                    <input type="number" name="harga" class="form-control <?php $__errorArgs = ['harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           value="<?php echo e(old('harga', $addon->harga ?? 0)); ?>" min="0" step="0.01" required>
                    <?php $__errorArgs = ['harga'];
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
                    <label>Qty <span class="text-danger">*</span></label>
                    <input type="number" name="qty" class="form-control <?php $__errorArgs = ['qty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           value="<?php echo e(old('qty', $addon->qty ?? 1)); ?>" min="1" required>
                    <?php $__errorArgs = ['qty'];
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

              <div class="form-group">
                <div class="custom-control custom-switch">
                  <input type="checkbox" name="masuk_hpp" value="1" class="custom-control-input" id="masukHpp"
                         <?php echo e(old('masuk_hpp', $addon->masuk_hpp ?? true) ? 'checked' : ''); ?>>
                  <label class="custom-control-label" for="masukHpp">Masuk HPP</label>
                </div>
                <small class="text-muted">Centang jika add-on ini harus dihitung dalam HPP booking</small>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/travel/booking/addon-form.blade.php ENDPATH**/ ?>