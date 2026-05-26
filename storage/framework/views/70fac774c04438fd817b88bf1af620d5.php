

<?php $__env->startSection('title', 'Referrals'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Referrals</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola semua referral Anda</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['pending']); ?></div>
            <div class="text-xs text-gray-500 mt-1">Pending Referrals</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['verified']); ?></div>
            <div class="text-xs text-gray-500 mt-1">Verified Referrals</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900"><?php echo e($stats['rejected']); ?></div>
            <div class="text-xs text-gray-500 mt-1">Rejected Referrals</div>
        </div>
    </div>

    <!-- Referrals Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Semua Referrals</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Paket</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Customer</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Pax</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Order Amount</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Komisi</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Status Fee</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900"><?php echo e($ref->package->package_name ?? 'N/A'); ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-700"><?php echo e($ref->booking->member->nama ?? $ref->booking->member->full_name ?? 'N/A'); ?></div>
                            <div class="text-xs text-gray-400"><?php echo e($ref->booking->member->telepon ?? ''); ?></div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            <?php echo e($ref->total_pax ?? 1); ?>

                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">
                            Rp <?php echo e(number_format($ref->order_amount, 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">
                            Rp <?php echo e(number_format($ref->commission_amount, 0, ',', '.')); ?>

                            <?php if(($ref->total_pax ?? 1) > 1): ?>
                            <div class="text-xs text-gray-400 font-normal mt-0.5">
                                Rp <?php echo e(number_format($ref->commission_amount / ($ref->total_pax ?? 1), 0, ',', '.')); ?> x <?php echo e($ref->total_pax); ?> pax
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-600">
                            <?php echo e($ref->order_date->format('d M Y')); ?>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if($ref->status === 'verified'): ?>
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Verified</span>
                            <?php elseif($ref->status === 'pending'): ?>
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Pending</span>
                            <?php elseif($ref->status === 'rejected'): ?>
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Rejected</span>
                            <?php else: ?>
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">Paid</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if(!$ref->termin_1_released): ?>
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded-full">⏳ Menunggu Pelunasan</span>
                            <?php elseif($ref->termin_1_released && !$ref->termin_2_released): ?>
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">⏳ Menunggu Keberangkatan</span>
                            <?php else: ?>
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">✅ Bisa Ditarik</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            Belum ada referral
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($referrals->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-100">
            <?php echo e($referrals->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('affiliate.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views/affiliate/referrals.blade.php ENDPATH**/ ?>