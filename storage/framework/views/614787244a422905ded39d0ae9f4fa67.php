<?php if (isset($component)) { $__componentOriginal6fed4215e8735ef3561b0b5668218f7a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6fed4215e8735ef3561b0b5668218f7a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.investor.layout','data' => ['title' => 'Investasi Saya']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('investor.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Investasi Saya']); ?>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Akun Investasi Saya</h2>
        <button class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah Akun
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 <?php echo e($account->status === 'active' ? 'border-green-500' : 'border-gray-400'); ?>">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-lg"><?php echo e($account->bank_name); ?></h3>
                        <p class="text-gray-500"><?php echo e($account->account_number); ?></p>
                        <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full 
                              <?php echo e($account->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo e($account->status === 'active' ? 'Aktif' : 'Nonaktif'); ?>

                        </span>
                    </div>
                    <div class="p-2 rounded-full <?php echo e($account->status === 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'); ?>">
                        <i class="fas fa-university text-xl"></i>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Saldo Awal</span>
                        <span class="font-medium">@money($account->initial_balance)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Saldo Saat Ini</span>
                        <span class="font-medium">@money($account->current_balance)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Investasi</span>
                        <span class="font-medium">@money($account->total_investment)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bagi Hasil</span>
                        <span class="font-medium"><?php echo e(number_format($account->profit_percentage, 2)); ?>%</span>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 flex justify-end">
                <a href="<?php echo e(route('investor.accounts.show', $account->id)); ?>" 
                   class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center">
                    Lihat Detail <i class="fas fa-chevron-right ml-1"></i>
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full bg-white rounded-lg shadow p-6 text-center">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-university text-4xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-700">Belum ada akun investasi</h3>
            <p class="text-gray-500 mt-2">Mulai dengan menambahkan akun investasi Anda</p>
            <button class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Akun
            </button>
        </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6fed4215e8735ef3561b0b5668218f7a)): ?>
<?php $attributes = $__attributesOriginal6fed4215e8735ef3561b0b5668218f7a; ?>
<?php unset($__attributesOriginal6fed4215e8735ef3561b0b5668218f7a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6fed4215e8735ef3561b0b5668218f7a)): ?>
<?php $component = $__componentOriginal6fed4215e8735ef3561b0b5668218f7a; ?>
<?php unset($__componentOriginal6fed4215e8735ef3561b0b5668218f7a); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\investor\accounts.blade.php ENDPATH**/ ?>