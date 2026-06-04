

<?php $__env->startSection('title', 'Aktivitas Investasi'); ?>

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
                <h2 class="text-xl font-semibold">Semua Aktivitas</h2>
                <p class="text-gray-500">Riwayat lengkap investasi dan pencairan</p>
            </div>
            <a href="<?php echo e(route('investor.dashboard')); ?>" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="divide-y">
            <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start">
                        <div class="p-2 rounded-full mr-3 
                            <?php if($activity instanceof \App\Models\AccountInvestment && $activity->type === 'deposit'): ?> bg-green-100 text-green-600
                            <?php elseif($activity instanceof \App\Models\InvestorWithdrawal): ?> bg-blue-100 text-blue-600
                            <?php else: ?> bg-gray-100 text-gray-600 <?php endif; ?>">
                            <?php if($activity instanceof \App\Models\AccountInvestment && $activity->type === 'deposit'): ?>
                                <i class="fas fa-hand-holding-usd"></i>
                            <?php elseif($activity instanceof \App\Models\InvestorWithdrawal): ?>
                                <i class="fas fa-money-bill-wave"></i>
                            <?php else: ?>
                                <i class="fas fa-info-circle"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h4 class="font-medium">
                                    <?php if($activity instanceof \App\Models\AccountInvestment): ?>
                                        <?php echo e($activity->description); ?>

                                    <?php else: ?>
                                        Pencairan Dana
                                    <?php endif; ?>
                                </h4>
                                <span class="text-sm text-gray-500">
                                    
                                    <?php if($activity instanceof \App\Models\AccountInvestment): ?>
                                        <?php echo e($activity->date->format('d M Y')); ?>

                                    <?php else: ?>
                                        <?php echo e($activity->requested_at->format('d M Y')); ?>

                                    <?php endif; ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                <?php if($activity->amount): ?>
                                    Rp<?php echo e(number_format($activity->amount, 0, ',', '.')); ?> • 
                                <?php endif; ?>
                                <?php echo e($activity->account->bank_name); ?> (<?php echo e($activity->account->account_number); ?>)
                                <?php if($activity instanceof \App\Models\InvestorWithdrawal): ?>
                                    • Status: <?php echo e(ucfirst($activity->status)); ?>

                                <?php endif; ?>
                            </p>
                            <?php if($activity->notes): ?>
                            <div class="mt-2 p-2 bg-gray-50 rounded text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-1"></i> <?php echo e($activity->notes); ?>

                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-6 text-center text-gray-500">
                    Tidak ada aktivitas
                </div>
                <?php endif; ?>
            </div>

            <?php if($activities->hasPages()): ?>
            <div class="px-6 py-4 border-t bg-gray-50">
                <?php echo e($activities->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('investor.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\investor\activities\index.blade.php ENDPATH**/ ?>