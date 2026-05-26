

<?php $__env->startSection('title', 'Wallet'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Wallet</h1>
        <p class="text-sm text-gray-500 mt-1">Riwayat transaksi wallet Anda</p>
    </div>

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-xl shadow-lg p-6 text-white">
            <div class="text-sm opacity-90 mb-1">Saldo Tersedia</div>
            <div class="text-3xl font-bold">Rp <?php echo e(number_format($affiliator->available_balance, 0, ',', '.')); ?></div>
            <div class="text-xs opacity-75 mt-2">Bisa ditarik kapan saja</div>
        </div>

        <div class="bg-gradient-to-r from-yellow-600 to-yellow-500 rounded-xl shadow-lg p-6 text-white">
            <div class="text-sm opacity-90 mb-1">Saldo Pending</div>
            <div class="text-3xl font-bold">Rp <?php echo e(number_format($affiliator->pending_balance, 0, ',', '.')); ?></div>
            <div class="text-xs opacity-75 mt-2">
                <?php if($pendingBreakdown['waiting_payment'] > 0): ?>
                    Menunggu Pelunasan: Rp <?php echo e(number_format($pendingBreakdown['waiting_payment'], 0, ',', '.')); ?><br>
                <?php endif; ?>
                <?php if($pendingBreakdown['waiting_departure'] > 0): ?>
                    Menunggu Keberangkatan: Rp <?php echo e(number_format($pendingBreakdown['waiting_departure'], 0, ',', '.')); ?>

                <?php endif; ?>
                <?php if($pendingBreakdown['waiting_payment'] == 0 && $pendingBreakdown['waiting_departure'] == 0): ?>
                    Menunggu verifikasi
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Riwayat Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Tanggal</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Deskripsi</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600">Jumlah</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">
                            <?php echo e($trx['date']->format('d M Y H:i')); ?>

                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <?php if($trx['type'] == 'click'): ?>
                                    <i class="fas fa-mouse-pointer text-blue-500"></i>
                                <?php elseif($trx['type'] == 'referral'): ?>
                                    <i class="fas fa-users text-green-500"></i>
                                <?php else: ?>
                                    <i class="fas fa-arrow-down text-red-500"></i>
                                <?php endif; ?>
                                <span class="text-gray-900"><?php echo e($trx['description']); ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-bold <?php echo e($trx['amount'] >= 0 ? 'text-green-600' : 'text-red-600'); ?>">
                            <?php echo e($trx['amount'] >= 0 ? '+' : ''); ?>Rp <?php echo e(number_format(abs($trx['amount']), 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if($trx['status'] === 'completed' || $trx['status'] === 'verified'): ?>
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Selesai</span>
                            <?php elseif($trx['status'] === 'pending'): ?>
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">Pending</span>
                            <?php elseif($trx['status'] === 'rejected' || $trx['status'] === 'failed'): ?>
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full">Gagal</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"><?php echo e(ucfirst($trx['status'])); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center py-12 text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            Belum ada transaksi
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('affiliate.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views/affiliate/wallet.blade.php ENDPATH**/ ?>