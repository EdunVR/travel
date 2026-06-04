<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Katalog Paket Travel']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Katalog Paket Travel')]); ?>
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold">Katalog Paket Travel</h1>
            <p class="text-slate-600 text-sm">Jelajahi paket Hajj dan Umrah yang tersedia</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="<?php echo e(route('admin.inventaris.travel.catalog.index')); ?>" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Destinasi</label>
                    <select name="destination" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                        <option value="">Semua</option>
                        <option value="hajj" <?php echo e(request('destination') == 'hajj' ? 'selected' : ''); ?>>Hajj</option>
                        <option value="umrah" <?php echo e(request('destination') == 'umrah' ? 'selected' : ''); ?>>Umrah</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bulan Keberangkatan</label>
                    <select name="month" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                        <option value="">Semua Bulan</option>
                        <?php for($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::create()->month($m)->format('F')); ?>

                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Durasi (Hari)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="duration_min" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Min" value="<?php echo e(request('duration_min')); ?>">
                        <input type="number" name="duration_max" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Max" value="<?php echo e(request('duration_max')); ?>">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Harga (Rp)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="price_min" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Min" value="<?php echo e(request('price_min')); ?>">
                        <input type="number" name="price_max" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200" placeholder="Max" value="<?php echo e(request('price_max')); ?>">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Urutkan</label>
                    <select name="sort_by" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-primary-200">
                        <option value="departure_date" <?php echo e(request('sort_by') == 'departure_date' ? 'selected' : ''); ?>>Tanggal Keberangkatan</option>
                        <option value="price_low" <?php echo e(request('sort_by') == 'price_low' ? 'selected' : ''); ?>>Harga Terendah</option>
                        <option value="price_high" <?php echo e(request('sort_by') == 'price_high' ? 'selected' : ''); ?>>Harga Tertinggi</option>
                        <option value="popular" <?php echo e(request('sort_by') == 'popular' ? 'selected' : ''); ?>>Paling Populer</option>
                        <option value="duration" <?php echo e(request('sort_by') == 'duration' ? 'selected' : ''); ?>>Durasi</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 text-white px-4 py-2 hover:bg-primary-700">
                    <i class='bx bx-search'></i> Filter
                </button>
                <a href="<?php echo e(route('admin.inventaris.travel.catalog.index')); ?>" class="inline-flex items-center gap-2 rounded-lg bg-slate-200 text-slate-700 px-4 py-2 hover:bg-slate-300">
                    <i class='bx bx-reset'></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Package Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php if (isset($component)) { $__componentOriginalbb3566070790fe8fe5c4e22b7076e036 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbb3566070790fe8fe5c4e22b7076e036 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.package-card','data' => ['package' => $package]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('package-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['package' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($package)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbb3566070790fe8fe5c4e22b7076e036)): ?>
<?php $attributes = $__attributesOriginalbb3566070790fe8fe5c4e22b7076e036; ?>
<?php unset($__attributesOriginalbb3566070790fe8fe5c4e22b7076e036); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbb3566070790fe8fe5c4e22b7076e036)): ?>
<?php $component = $__componentOriginalbb3566070790fe8fe5c4e22b7076e036; ?>
<?php unset($__componentOriginalbb3566070790fe8fe5c4e22b7076e036); ?>
<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                <i class='bx bx-info-circle text-3xl text-blue-600 mb-2'></i>
                <p class="text-blue-800">Tidak ada paket yang tersedia saat ini.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($packages->hasPages()): ?>
    <div class="flex justify-center">
        <?php echo e($packages->links()); ?>

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

<script>
function copyPackageLink(packageId, packageName) {
    // Generate public link dengan url() helper Laravel
    const publicUrl = '<?php echo e(url("/paket")); ?>/' + packageId;
    
    // Copy to clipboard
    navigator.clipboard.writeText(publicUrl).then(() => {
        // Show success notification
        Swal.fire({
            icon: 'success',
            title: 'Link Disalin!',
            text: `Link paket "${packageName}" telah disalin ke clipboard`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }).catch(err => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = publicUrl;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            Swal.fire({
                icon: 'success',
                title: 'Link Disalin!',
                text: `Link paket "${packageName}" telah disalin ke clipboard`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } catch (e) {
            document.body.removeChild(textArea);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyalin link. Silakan copy manual: ' + publicUrl,
                showConfirmButton: true
            });
        }
    });
}
</script>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\catalog\index.blade.php ENDPATH**/ ?>