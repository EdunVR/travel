<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Detail Paket - ' . $package->package_name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Detail Paket - ' . $package->package_name)]); ?>
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold"><?php echo e($package->package_name); ?></h1>
            <p class="text-slate-600 text-sm"><?php echo e($package->package_code); ?></p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.inventaris.travel.catalog.index')); ?>" class="inline-flex items-center gap-2 rounded-lg bg-slate-200 text-slate-700 px-4 py-2 hover:bg-slate-300">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
            <?php if($availableSeats > 0): ?>
            <a href="<?php echo e(route('admin.inventaris.booking.index', ['package_id' => $package->id])); ?>" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                <i class='bx bx-cart'></i> Booking Sekarang
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Package Image -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden cursor-pointer" onclick="openImageModal('<?php echo e($package->image_path ? asset('storage/' . $package->image_path) : ''); ?>')">
            <?php if($package->image_path): ?>
            <img src="<?php echo e(asset('storage/' . $package->image_path)); ?>" class="w-full h-96 object-cover" alt="<?php echo e($package->package_name); ?>">
            <?php else: ?>
            <div class="w-full h-96 bg-slate-200 flex items-center justify-center">
                <i class='bx bx-image text-8xl text-slate-400'></i>
            </div>
            <?php endif; ?>
        </div>

        <!-- info paket -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Informasi Paket</h2>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Kode Paket</span>
                    <span class="font-medium text-slate-900"><?php echo e($package->package_code); ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Jenis</span>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full <?php echo e($package->package_type == 'hajj' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'); ?> text-xs font-medium">
                        <?php echo e(ucfirst($package->package_type)); ?>

                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Durasi</span>
                    <span class="font-medium text-slate-900"><?php echo e($package->duration_days); ?> Hari</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Keberangkatan</span>
                    <span class="font-medium text-slate-900"><?php echo e($package->departure_date->format('d F Y')); ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Kepulangan</span>
                    <span class="font-medium text-slate-900"><?php echo e($package->return_date->format('d F Y')); ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Harga</span>
                    <span class="text-2xl font-bold text-primary-600">Rp <?php echo e(number_format($package->price, 0, ',', '.')); ?></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-600">Kapasitas</span>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-slate-900"><?php echo e($package->capacity); ?> Jamaah</span>
                        <?php if($availableSeats > 0): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium">
                            <i class='bx bx-check-circle'></i> <?php echo e($availableSeats); ?> Tersedia
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-medium">
                            <i class='bx bx-x-circle'></i> Penuh
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-slate-600">Popularitas</span>
                    <div class="flex items-center gap-3 text-sm text-slate-600">
                        <span class="flex items-center gap-1">
                            <i class='bx bx-show'></i> <?php echo e($package->view_count); ?>

                        </span>
                        <span class="flex items-center gap-1">
                            <i class='bx bx-user'></i> <?php echo e($package->booking_count); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Deskripsi Paket</h2>
        <p class="text-slate-700 leading-relaxed"><?php echo e($package->description ?: 'Tidak ada deskripsi.'); ?></p>
    </div>

    <!-- Inclusions -->
    <?php if(count($inclusions) > 0): ?>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Fasilitas yang Termasuk</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php $__currentLoopData = $inclusions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inclusion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-start gap-2">
                <i class='bx bx-check-circle text-green-600 text-xl mt-0.5'></i>
                <span class="text-slate-700"><?php echo e($inclusion); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Departure Batches -->
    <?php if($package->keberangkatan->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Jadwal Keberangkatan</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Kode</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Nama Keberangkatan</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Tanggal</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Jumlah Jamaah</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $package->keberangkatan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keberangkatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="py-3 px-4 text-sm text-slate-900"><?php echo e($keberangkatan->keberangkatan_code); ?></td>
                        <td class="py-3 px-4 text-sm text-slate-900"><?php echo e($keberangkatan->keberangkatan_name); ?></td>
                        <td class="py-3 px-4 text-sm text-slate-900"><?php echo e($keberangkatan->departure_date->format('d M Y')); ?></td>
                        <td class="py-3 px-4 text-sm text-slate-900"><?php echo e($keberangkatan->total_jamaah); ?></td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo e($keberangkatan->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'); ?>">
                                <?php echo e(ucfirst($keberangkatan->status)); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tour Plan -->
    <?php if($package->tourPlans->count() > 0): ?>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
            <i class='bx bx-calendar-event text-primary-600'></i>
            Rencana Perjalanan
        </h2>
        <div class="space-y-4">
            <?php $__currentLoopData = $package->tourPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-slate-200 rounded-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-4 py-3 border-b border-blue-200">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold">
                            <?php echo e($day->day_number); ?>

                        </span>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900"><?php echo e($day->day_title); ?></h3>
                            <p class="text-xs text-gray-600"><?php echo e(\Carbon\Carbon::parse($day->day_date)->format('d F Y')); ?></p>
                        </div>
                    </div>
                    <?php if($day->description): ?>
                    <p class="text-sm text-gray-600 mt-2"><?php echo e($day->description); ?></p>
                    <?php endif; ?>
                </div>
                <?php if($day->activities->count() > 0): ?>
                <div class="p-4 space-y-3">
                    <?php $__currentLoopData = $day->activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-16 text-center">
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">
                                <?php echo e(\Carbon\Carbon::parse($activity->activity_time)->format('H:i')); ?>

                            </span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900"><?php echo e($activity->activity_title); ?></h4>
                            <?php if($activity->activity_description): ?>
                            <p class="text-sm text-gray-600 mt-1"><?php echo e($activity->activity_description); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
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


<div id="imageModal" class="fixed inset-0 z-[100] hidden bg-black bg-opacity-90 flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-7xl max-h-full">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-4xl font-bold">&times;</button>
        <img id="modalImage" src="" alt="Full Image" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
    </div>
</div>

<script>
function openImageModal(src) {
    if (!src) return;
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\catalog\show.blade.php ENDPATH**/ ?>