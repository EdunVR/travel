

<?php $__env->startSection('title', 'Daftar Pencairan - Portal Investor'); ?>

<?php $__env->startPush('styles'); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <main class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold">Riwayat Pencairan Dana</h2>
                <p class="text-gray-500">Total pencairan disetujui: Rp<?php echo e(number_format($totalApprovedWithdrawals, 0, ',', '.')); ?></p>
            </div>
            <a href="<?php echo e(route('investor.investments')); ?>" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Investasi
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h3 class="font-semibold">Semua Pencairan</h3>
                    <div class="relative">
                        <select class="appearance-none bg-gray-100 border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option>Semua Status</option>
                            <option>Disetujui</option>
                            <option>Menunggu</option>
                            <option>Ditolak</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <span class="text-sm text-gray-500">
                    Menampilkan <?php echo e($withdrawals->count()); ?> dari <?php echo e($withdrawals->total()); ?> pencairan
                </span>
            </div>
            <div class="divide-y">
                <?php $__empty_1 = true; $__currentLoopData = $withdrawals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdrawal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium">Pencairan Dana</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                <?php echo e($withdrawal->requested_at->format('d M Y H:i')); ?>

                                • <?php echo e($withdrawal->account->bank_name); ?> (<?php echo e($withdrawal->account->account_number); ?>)
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-medium text-red-600">
                                -Rp<?php echo e(number_format($withdrawal->amount, 0, ',', '.')); ?>

                            </span>
                            <p class="text-sm text-gray-500 mt-1">
                                <?php if($withdrawal->status === 'approved'): ?>
                                    <span class="text-green-600">Disetujui pada <?php echo e($withdrawal->approved_at->format('d M Y')); ?></span>
                                <?php elseif($withdrawal->status === 'rejected'): ?>
                                    <span class="text-red-600">Ditolak</span>
                                <?php else: ?>
                                    <span class="text-yellow-600">Menunggu Persetujuan</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <?php if($withdrawal->notes): ?>
                    <div class="mt-2 p-2 bg-gray-50 rounded text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i> <?php echo e($withdrawal->notes); ?>

                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-6 text-center text-gray-500">
                    Belum ada riwayat pencairan
                </div>
                <?php endif; ?>
            </div>
            <?php if($withdrawals->hasPages()): ?>
            <div class="px-6 py-4 border-t bg-gray-50">
                <?php echo e($withdrawals->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('investor.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\investor\withdrawals\index.blade.php ENDPATH**/ ?>