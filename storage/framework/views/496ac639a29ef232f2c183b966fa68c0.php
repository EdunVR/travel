<?php if (isset($component)) { $__componentOriginalc8c9fd5d7827a77a31381de67195f0c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8c9fd5d7827a77a31381de67195f0c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.admin','data' => ['title' => 'Manajemen Mitra']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Manajemen Mitra')]); ?>
<div class="space-y-6">

    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Mitra</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola semua mitra HM Tour</p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.inventaris.affiliate.hierarchy.tree')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-50 border border-blue-200 text-sm text-blue-700 hover:bg-blue-100 transition">
                <i class="fas fa-sitemap text-xs"></i> Pohon Jenjang
            </a>
            <a href="<?php echo e(route('admin.inventaris.affiliate.leaderboard')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-purple-50 border border-purple-200 text-sm text-purple-700 hover:bg-purple-100 transition">
                <i class="fas fa-trophy text-xs"></i> Leaderboard
            </a>
            <a href="<?php echo e(route('admin.inventaris.affiliate.payouts')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-700 hover:bg-amber-100 transition">
                <i class="fas fa-money-bill-wave text-xs"></i> Withdraw
                <?php if($stats['pending_payouts'] > 0): ?>
                <span class="ml-1 bg-amber-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                    <?php echo e(number_format($stats['pending_payouts']/1000)); ?>K
                </span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    
    <?php if(session('success')): ?>
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <?php
        $statCards = [
            ['label' => 'Total Mitra', 'value' => $stats['total'],                                                    'color' => 'text-blue-600'],
            ['label' => 'Aktif',            'value' => $stats['active'],                                                   'color' => 'text-green-600'],
            ['label' => 'Pending',          'value' => $stats['pending'],                                                  'color' => 'text-amber-600'],
            ['label' => 'Total Klik',       'value' => number_format($stats['total_clicks']),                              'color' => 'text-sky-600'],
            ['label' => 'Komisi Dibayar',   'value' => 'Rp '.number_format($stats['total_commission_paid']/1000000,1).'jt','color' => 'text-green-600'],
            ['label' => 'Pending Withdraw', 'value' => 'Rp '.number_format($stats['pending_payouts']/1000000,1).'jt',     'color' => 'text-amber-600'],
        ];
        ?>
        <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4 text-center">
            <div class="text-xl font-bold <?php echo e($card['color']); ?>"><?php echo e($card['value']); ?></div>
            <div class="text-xs text-slate-500 mt-1"><?php echo e($card['label']); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="bg-white rounded-xl border border-slate-200 shadow-card p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                   class="flex-1 min-w-48 h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                   placeholder="Cari nama, HP, atau email...">
            <select name="status"
                    class="h-9 px-3 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
                <option value="">Semua Status</option>
                <option value="pending"   <?php echo e(request('status') == 'pending'   ? 'selected' : ''); ?>>Pending</option>
                <option value="active"    <?php echo e(request('status') == 'active'    ? 'selected' : ''); ?>>Aktif</option>
                <option value="suspended" <?php echo e(request('status') == 'suspended' ? 'selected' : ''); ?>>Suspended</option>
            </select>
            <button type="submit"
                    class="h-9 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition">
                <i class="fas fa-search mr-1"></i> Cari
            </button>
            <?php if(request()->hasAny(['search', 'status'])): ?>
            <a href="<?php echo e(route('admin.inventaris.affiliate.index')); ?>"
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
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Mitra</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Username / HP</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Program</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Klik</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Penjualan</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Saldo Tersedia</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Pending</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $affiliators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 overflow-hidden border-2 border-slate-100">
                                    <?php if($aff->photo): ?>
                                        <img src="<?php echo e($aff->photo_url); ?>" alt="<?php echo e($aff->full_name); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?php echo e(strtoupper(substr($aff->full_name, 0, 1))); ?>

                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900"><?php echo e($aff->full_name); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo e($aff->email); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-green-600 text-xs font-mono"><?php echo e($aff->username); ?></code>
                            <div class="text-xs text-slate-400 mt-0.5">
                                <span class="inline-flex items-center gap-1">
                                    <span class="truncate max-w-xs" title="<?php echo e($aff->referral_link); ?>"><?php echo e($aff->referral_link); ?></span>
                                    <button onclick="copyToClipboard('<?php echo e($aff->referral_link); ?>', this)" 
                                            class="flex-shrink-0 p-0.5 hover:bg-green-100 text-green-600 rounded transition" 
                                            title="Copy link">
                                        <i class="fas fa-copy text-xs"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5"><?php echo e($aff->phone_number); ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($aff->partnershipProgram): ?>
                                <div class="text-sm font-semibold text-slate-800"><?php echo e($aff->partnershipProgram->name); ?></div>
                                <div class="text-xs text-slate-500">Min: <?php echo e($aff->partnershipProgram->formatted_commission); ?></div>
                            <?php else: ?>
                                <span class="text-xs text-slate-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded-full">
                                <?php echo e(number_format($aff->total_clicks)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded-full">
                                <?php echo e(number_format($aff->total_sales)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">
                            Rp <?php echo e(number_format($aff->available_balance, 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-right text-amber-600">
                            Rp <?php echo e(number_format($aff->pending_balance, 0, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if($aff->status === 'active'): ?>
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-medium">Aktif</span>
                            <?php elseif($aff->status === 'pending'): ?>
                                <span class="bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded-full font-medium">Pending</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-medium">Suspended</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-1">
                                <a href="<?php echo e(route('admin.inventaris.affiliate.show', $aff)); ?>"
                                   class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition" title="Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <button onclick="openEditModal(<?php echo e($aff->id); ?>, '<?php echo e($aff->full_name); ?>', <?php echo e($aff->ppc_commission); ?>, <?php echo e($aff->min_sale_commission); ?>, <?php echo e($aff->cookie_lifetime ?? 30); ?>)"
                                        class="p-1.5 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition" title="Edit Komisi">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <?php if($aff->status === 'pending'): ?>
                                <form action="<?php echo e(route('admin.inventaris.affiliate.approve', $aff)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" onclick="return confirm('Aktifkan mitra ini?')"
                                            class="p-1.5 rounded-lg bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 transition" title="Approve">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                <?php elseif($aff->status === 'active'): ?>
                                <form action="<?php echo e(route('admin.inventaris.affiliate.suspend', $aff)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit" onclick="return confirm('Suspend mitra ini?')"
                                            class="p-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 transition" title="Suspend">
                                        <i class="fas fa-ban text-xs"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <form action="<?php echo e(route('admin.inventaris.affiliate.approve', $aff)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit"
                                            class="p-1.5 rounded-lg bg-green-50 border border-green-200 text-green-600 hover:bg-green-100 transition" title="Aktifkan">
                                        <i class="fas fa-redo text-xs"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="text-center py-12 text-slate-400">
                            <i class="fas fa-users text-3xl mb-3 block"></i>
                            Belum ada mitra
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($affiliators->hasPages()): ?>
        <div class="px-4 py-3 border-t border-slate-100">
            <?php echo e($affiliators->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>

</div>


<div id="editCommissionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-float w-full max-w-md mx-4 p-6">
        <h3 class="font-bold text-slate-900 mb-4">Edit Komisi Mitra</h3>
        <p class="text-sm text-slate-600 mb-4">Mitra: <span id="affiliatorName" class="font-semibold"></span></p>
        
        <form id="editCommissionForm" method="POST">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Komisi PPC (Per Klik)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="ppc_commission" id="ppcCommission" required min="0" step="1"
                               class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                               placeholder="50">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Default: Rp 50</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Komisi Minimal (Per Penjualan)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                        <input type="number" name="min_sale_commission" id="minSaleCommission" required min="0" step="1000"
                               class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                               placeholder="500000">
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Default: Rp 500.000</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Cookie Lifetime (Hari)
                    </label>
                    <input type="number" name="cookie_lifetime" id="cookieLifetime" required min="1" max="365" step="1"
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                           placeholder="30">
                    <p class="text-xs text-slate-500 mt-1">Berapa lama cookie referral disimpan (1-365 hari)</p>
                </div>
            </div>
            
            <div class="flex gap-2 justify-end mt-6">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 border border-slate-200 text-slate-600 text-sm rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback
        const icon = button.querySelector('i');
        const originalClass = icon.className;
        icon.className = 'fas fa-check text-xs';
        button.classList.add('bg-green-500', 'text-white');
        button.classList.remove('text-green-600');
        
        setTimeout(function() {
            icon.className = originalClass;
            button.classList.remove('bg-green-500', 'text-white');
            button.classList.add('text-green-600');
        }, 2000);
    }).catch(function(err) {
        alert('Gagal copy link: ' + err);
    });
}

function openEditModal(id, name, ppc, minSale, cookieLifetime) {
    document.getElementById('affiliatorName').textContent = name;
    document.getElementById('ppcCommission').value = ppc || 50;
    document.getElementById('minSaleCommission').value = minSale || 500000;
    document.getElementById('cookieLifetime').value = cookieLifetime || 30;
    document.getElementById('editCommissionForm').action = '<?php echo e(url("admin/inventaris/affiliate")); ?>/' + id + '/update-commission';
    document.getElementById('editCommissionModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editCommissionModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('editCommissionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
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
<?php /**PATH C:\xampp\htdocs\hm\resources\views/admin/affiliate/index.blade.php ENDPATH**/ ?>