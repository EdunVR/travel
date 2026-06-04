<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Pengaturan Affiliate']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Pengaturan Affiliate')]); ?>
<div class="space-y-6">

    
    <div>
        <a href="<?php echo e(route('admin.inventaris.affiliate.index')); ?>"
           class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1 mb-1 w-fit">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Affiliate</h1>
    </div>

    <?php if(session('success')): ?>
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
            <h2 class="font-bold text-slate-900 mb-5 flex items-center gap-2">
                <i class="fas fa-sliders-h text-primary-600"></i> Pengaturan Global
            </h2>
            <form action="<?php echo e(route('admin.inventaris.affiliate.settings.save')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>

                
                <?php
                $toggles = [
                    ['key' => 'affiliate_enabled',       'label' => 'Aktifkan Sistem Affiliate',       'desc' => null,                                                                                    'checked' => $settings['affiliate_enabled']->value ?? 1],
                    ['key' => 'auto_approve_affiliates',  'label' => 'Auto Approve Pendaftaran Baru',   'desc' => 'Affiliator langsung aktif tanpa verifikasi manual',                                      'checked' => $settings['auto_approve_affiliates']->value ?? 0],
                    ['key' => 'click_fraud_prevention',   'label' => 'Anti Klik Ganda (Fraud Prevention)', 'desc' => 'Mencegah klik berulang dari IP yang sama dalam 24 jam',                             'checked' => $settings['click_fraud_prevention']->value ?? 1],
                ];
                ?>
                <?php $__currentLoopData = $toggles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold text-slate-900"><?php echo e($t['label']); ?></div>
                        <?php if($t['desc']): ?>
                        <div class="text-xs text-slate-500 mt-0.5"><?php echo e($t['desc']); ?></div>
                        <?php endif; ?>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                        <input type="checkbox" name="<?php echo e($t['key']); ?>" value="1" class="sr-only peer"
                               <?php echo e($t['checked'] ? 'checked' : ''); ?>>
                        <div class="w-10 h-5 bg-slate-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                    </label>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <hr class="border-slate-100">

                
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-1.5">
                        <i class="fas fa-cookie-bite text-amber-500 mr-1"></i> Durasi Cookie Tracking
                    </label>
                    <select name="cookie_lifetime"
                            class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                        <?php
                        $cookieOptions = [
                            3600   => '1 Jam (Flash Sale / FOMO)',
                            28800  => '8 Jam',
                            86400  => '1 Hari',
                            259200 => '3 Hari (Rekomendasi)',
                            604800 => '7 Hari',
                            2592000=> '30 Hari',
                        ];
                        $currentCookie = $settings['cookie_lifetime']->value ?? 259200;
                        ?>
                        <?php $__currentLoopData = $cookieOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e($currentCookie == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Berapa lama cookie referral aktif setelah klik</p>
                </div>

                <hr class="border-slate-100">

                
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-1.5">
                        <i class="fas fa-mouse-pointer text-sky-500 mr-1"></i> Komisi Per Klik (PPC)
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-200 bg-slate-50 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="default_click_commission"
                               value="<?php echo e($settings['default_click_commission']->value ?? 1000); ?>"
                               class="flex-1 h-9 px-3 rounded-r-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                               min="0" step="100">
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Komisi default per klik link referral</p>
                </div>

                
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-1.5">
                        <i class="fas fa-percentage text-green-500 mr-1"></i> Komisi Per Penjualan (Default)
                    </label>
                    <div class="flex gap-2">
                        <select name="default_sale_commission_type"
                                class="w-40 h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                            <option value="percentage" <?php echo e(($settings['default_sale_commission_type']->value ?? 'percentage') == 'percentage' ? 'selected' : ''); ?>>Persentase (%)</option>
                            <option value="flat"       <?php echo e(($settings['default_sale_commission_type']->value ?? '') == 'flat' ? 'selected' : ''); ?>>Nominal (Rp)</option>
                        </select>
                        <input type="number" name="default_sale_commission_value"
                               value="<?php echo e($settings['default_sale_commission_value']->value ?? 5); ?>"
                               class="flex-1 h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                               min="0" step="0.5">
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-semibold text-slate-900 mb-1.5">
                        <i class="fas fa-wallet text-purple-500 mr-1"></i> Minimum Penarikan
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-200 bg-slate-50 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="minimum_payout"
                               value="<?php echo e($settings['minimum_payout']->value ?? 100000); ?>"
                               class="flex-1 h-9 px-3 rounded-r-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                               min="10000" step="10000">
                    </div>
                </div>

                <button type="submit"
                        class="w-full h-10 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">
                    <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                </button>
            </form>
        </div>

        
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-card p-6">
                <h2 class="font-bold text-slate-900 mb-5 flex items-center gap-2">
                    <i class="fas fa-tags text-green-600"></i> Komisi Per Paket (Custom)
                </h2>
                <form action="<?php echo e(route('admin.inventaris.affiliate.package-commission.save')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Paket</label>
                        <select name="package_id"
                                class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                            <option value="">-- Pilih Paket --</option>
                            <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pkg->id); ?>"><?php echo e($pkg->package_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Komisi Per Klik (Rp)</label>
                        <input type="number" name="click_commission" value="1000" min="0" step="100"
                               class="w-full h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Komisi Penjualan</label>
                        <div class="flex gap-2">
                            <select name="sale_commission_type"
                                    class="w-40 h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                                <option value="percentage">Persentase (%)</option>
                                <option value="flat">Nominal (Rp)</option>
                            </select>
                            <input type="number" name="sale_commission_value" value="5" min="0" step="0.5"
                                   class="flex-1 h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full h-10 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
                        <i class="fas fa-plus mr-1"></i> Tambah / Update Komisi Paket
                    </button>
                </form>
            </div>

            
            <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-900 text-sm">Komisi Paket Terdaftar</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="text-left px-4 py-2.5 font-semibold text-slate-600 text-xs">Paket</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Klik</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Sale</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 text-xs">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php $__empty_1 = true; $__currentLoopData = $packageCommissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5 text-xs text-slate-700"><?php echo e($pc->package->package_name ?? 'N/A'); ?></td>
                                <td class="px-4 py-2.5 text-center text-xs">Rp <?php echo e(number_format($pc->click_commission, 0, ',', '.')); ?></td>
                                <td class="px-4 py-2.5 text-center text-xs">
                                    <?php echo e($pc->sale_commission_type === 'percentage' ? $pc->sale_commission_value.'%' : 'Rp '.number_format($pc->sale_commission_value, 0, ',', '.')); ?>

                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <form action="<?php echo e(route('admin.inventaris.affiliate.package-commission.delete', $pc)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" onclick="return confirm('Hapus komisi paket ini?')"
                                                class="p-1 rounded bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center py-6 text-slate-400 text-xs">
                                    Belum ada komisi per paket. Menggunakan komisi default.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\affiliate\settings.blade.php ENDPATH**/ ?>