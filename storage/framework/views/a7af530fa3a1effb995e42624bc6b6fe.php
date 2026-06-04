<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Tambah Pembayaran - Booking ' . $booking->booking_code]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Tambah Pembayaran - Booking ' . $booking->booking_code)]); ?>
  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h3 mb-0">Tambah Pembayaran</h1>
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
            <h5 class="mb-0">Form Pembayaran</h5>
          </div>
          <div class="card-body">
            <form action="<?php echo e(route('admin.inventaris.payment.store', $booking->id)); ?>" method="POST" enctype="multipart/form-data">
              <?php echo csrf_field(); ?>

              <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
              <?php endif; ?>

              <?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
              <?php endif; ?>

              <div class="form-group">
                <label>Tanggal Pembayaran <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" class="form-control <?php $__errorArgs = ['payment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('payment_date', date('Y-m-d'))); ?>" required>
                <?php $__errorArgs = ['payment_date'];
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
                <label>Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('amount')); ?>" min="0" max="<?php echo e($booking->remaining_amount); ?>" step="0.01" required>
                <small class="form-text text-muted">
                  Sisa tagihan: Rp <?php echo e(number_format($booking->remaining_amount, 0, ',', '.')); ?>

                </small>
                <?php $__errorArgs = ['amount'];
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
                <label>Metode Pembayaran <span class="text-danger">*</span></label>
                <select name="payment_method" class="form-control <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                  <option value="">Pilih Metode</option>
                  <option value="cash" <?php echo e(old('payment_method') == 'cash' ? 'selected' : ''); ?>>Tunai</option>
                  <option value="transfer" <?php echo e(old('payment_method') == 'transfer' ? 'selected' : ''); ?>>Transfer Bank</option>
                  <option value="credit_card" <?php echo e(old('payment_method') == 'credit_card' ? 'selected' : ''); ?>>Kartu Kredit</option>
                  <option value="debit_card" <?php echo e(old('payment_method') == 'debit_card' ? 'selected' : ''); ?>>Kartu Debit</option>
                  <option value="other" <?php echo e(old('payment_method') == 'other' ? 'selected' : ''); ?>>Lainnya</option>
                </select>
                <?php $__errorArgs = ['payment_method'];
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
                <label>Tipe Pembayaran</label>
                <select name="payment_type" class="form-control <?php $__errorArgs = ['payment_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                  <option value="dp" <?php echo e(old('payment_type', 'dp') == 'dp' ? 'selected' : ''); ?>>DP (Down Payment)</option>
                  <option value="full" <?php echo e(old('payment_type') == 'full' ? 'selected' : ''); ?>>Lunas</option>
                </select>
                <?php $__errorArgs = ['payment_type'];
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
                <label>Nomor Referensi</label>
                <input type="text" name="reference_number" class="form-control <?php $__errorArgs = ['reference_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       value="<?php echo e(old('reference_number')); ?>" placeholder="Nomor transaksi/referensi">
                <?php $__errorArgs = ['reference_number'];
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
                <label>Bukti Transfer</label>
                <input type="file" name="bukti_transfer" class="form-control-file <?php $__errorArgs = ['bukti_transfer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       accept="image/jpeg,image/jpg,image/png">
                <small class="form-text text-muted">Format: JPG, PNG. Maksimal 10MB</small>
                <?php $__errorArgs = ['bukti_transfer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label>Keterangan</label>
                <textarea name="notes" class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          rows="3"><?php echo e(old('notes')); ?></textarea>
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
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Simpan Pembayaran
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
        <div class="card mb-3">
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
                <th>Terbayar:</th>
                <td class="text-success">Rp <?php echo e(number_format($booking->paid_amount ?? 0, 0, ',', '.')); ?></td>
              </tr>
              <tr>
                <th>Sisa Tagihan:</th>
                <td class="text-warning font-weight-bold">Rp <?php echo e(number_format($booking->remaining_amount ?? 0, 0, ',', '.')); ?></td>
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

        <?php if($booking->payments->count() > 0): ?>
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Riwayat Pembayaran</h6>
          </div>
          <div class="card-body p-0">
            <div class="list-group list-group-flush">
              <?php $__currentLoopData = $booking->payments->sortByDesc('payment_date')->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <div class="font-weight-bold text-success">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></div>
                    <small class="text-muted"><?php echo e($payment->payment_date->format('d M Y')); ?></small>
                  </div>
                  <span class="badge badge-<?php echo e($payment->payment_type === 'full' ? 'success' : 'warning'); ?>">
                    <?php echo e($payment->payment_type === 'full' ? 'LUNAS' : 'DP'); ?>

                  </span>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        </div>
        <?php endif; ?>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\payment\create.blade.php ENDPATH**/ ?>