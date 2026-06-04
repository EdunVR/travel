<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Manajemen Withdraw Mitra']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Manajemen Withdraw Mitra')]); ?>
<div class="space-y-6">

    
    <div>
        <a href="<?php echo e(route('admin.inventaris.affiliate.index')); ?>"
           class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1 mb-1 w-fit">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Withdraw</h1>
    </div>

    <?php if(session('success')): ?>
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-amber-200 shadow-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Pending</div>
                    <div class="text-2xl font-bold text-amber-600"><?php echo e($stats['pending_count']); ?> request</div>
                    <div class="text-sm text-slate-500 mt-0.5">Rp <?php echo e(number_format($stats['pending_amount'], 0, ',', '.')); ?></div>
                </div>
                <i class="fas fa-clock text-amber-300 text-3xl"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-green-200 shadow-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Dibayar</div>
                    <div class="text-lg font-bold text-green-600">Rp <?php echo e(number_format($stats['completed_amount'], 0, ',', '.')); ?></div>
                </div>
                <i class="fas fa-check-circle text-green-300 text-3xl"></i>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4">
        <form method="GET" class="flex gap-3 items-center flex-wrap">
            <select name="status"
                    class="h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                <option value="">Semua Status</option>
                <option value="pending"    <?php echo e(request('status') == 'pending'    ? 'selected' : ''); ?>>Pending</option>
                <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>Processing</option>
                <option value="completed"  <?php echo e(request('status') == 'completed'  ? 'selected' : ''); ?>>Completed</option>
                <option value="failed"     <?php echo e(request('status') == 'failed'     ? 'selected' : ''); ?>>Failed</option>
            </select>
            <button type="submit"
                    class="h-9 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition">
                Filter
            </button>
            <?php if(request('status')): ?>
            <a href="<?php echo e(route('admin.inventaris.affiliate.payouts')); ?>"
               class="h-9 px-4 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50 transition flex items-center">
                Reset
            </a>
            <?php endif; ?>
        </form>
    </div>

    
    <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Affiliator</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Referensi</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Jumlah</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Metode</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Rekening</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Tanggal</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $payouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900 text-sm"><?php echo e($payout->affiliator->full_name); ?></div>
                            <div class="text-xs text-slate-400"><?php echo e($payout->affiliator->phone_number); ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs text-slate-600 font-mono"><?php echo e($payout->payout_reference); ?></code>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">
                            Rp <?php echo e(number_format($payout->amount, 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full">
                                <?php echo e(ucfirst(str_replace('_', ' ', $payout->payment_method))); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($payout->affiliator->bank_name): ?>
                            <div class="text-xs font-medium text-slate-900"><?php echo e($payout->affiliator->bank_name); ?></div>
                            <div class="text-xs text-slate-400"><?php echo e($payout->affiliator->bank_account_number); ?></div>
                            <?php else: ?>
                            <span class="text-slate-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if($payout->status === 'pending'): ?>
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded-full font-medium">Pending</span>
                            <?php elseif($payout->status === 'processing'): ?>
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-medium">Processing</span>
                            <?php elseif($payout->status === 'completed'): ?>
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">Completed</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs text-slate-700"><?php echo e($payout->requested_at->format('d M Y')); ?></div>
                            <div class="text-xs text-slate-400"><?php echo e($payout->requested_at->format('H:i')); ?></div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if($payout->status === 'pending'): ?>
                            <div class="flex justify-center gap-1">
                                <form action="<?php echo e(route('admin.inventaris.affiliate.payouts.approve', $payout)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" onclick="return confirm('Approve payout ini?')"
                                            class="p-1.5 rounded-lg bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 transition" title="Approve">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                <button type="button" onclick="rejectPayout(<?php echo e($payout->id); ?>)"
                                        class="p-1.5 rounded-lg bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition" title="Reject">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                            <?php else: ?>
                            <span class="text-slate-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-3 block"></i>
                            Tidak ada data withdraw
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($payouts->hasPages()): ?>
        <div class="px-4 py-3 border-t border-slate-100">
            <?php echo e($payouts->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>


<div id="rejectPayoutModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-float w-full max-w-sm mx-4 p-6">
        <h3 class="font-bold text-slate-900 mb-4">Tolak Withdraw</h3>
        <form id="rejectPayoutForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Alasan Penolakan</label>
                <textarea name="reason" rows="3"
                          class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                          placeholder="Contoh: Nomor rekening tidak valid"></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('rejectPayoutModal').classList.add('hidden')"
                        class="px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                    Tolak & Kembalikan Saldo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function rejectPayout(id) {
    document.getElementById('rejectPayoutForm').action = '<?php echo e(url("admin/inventaris/affiliate/payouts")); ?>/' + id + '/reject';
    document.getElementById('rejectPayoutModal').classList.remove('hidden');
}
</script>
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\affiliate\payouts.blade.php ENDPATH**/ ?>