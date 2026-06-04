<?php
    $slug = $node->partnershipProgram?->slug ?? '';
    $colorMap = [
        'hm-master'  => ['bg' => 'bg-purple-500', 'border' => 'border-purple-300', 'badge' => 'bg-purple-100 text-purple-700'],
        'hm-leader'  => ['bg' => 'bg-indigo-500', 'border' => 'border-indigo-300', 'badge' => 'bg-indigo-100 text-indigo-700'],
        'hm-partner' => ['bg' => 'bg-green-500',  'border' => 'border-green-300',  'badge' => 'bg-green-100 text-green-700'],
        'hm-seller'  => ['bg' => 'bg-blue-500',   'border' => 'border-blue-300',   'badge' => 'bg-blue-100 text-blue-700'],
        'hm-member'  => ['bg' => 'bg-gray-400',   'border' => 'border-gray-300',   'badge' => 'bg-gray-100 text-gray-600'],
    ];
    $colors = $colorMap[$slug] ?? $colorMap['hm-member'];

    // Tentukan downline berdasarkan level
    $children = collect();
    if ($slug === 'hm-master')  $children = $node->downlineLeaders ?? collect();
    elseif ($slug === 'hm-leader')  $children = $node->downlinePartners ?? collect();
    elseif ($slug === 'hm-partner') $children = $node->downlineSellers ?? collect();

    $viewUrl = route('admin.inventaris.affiliate.show', $node);
    $editUrl = route('admin.inventaris.affiliate.show', $node);
?>

<div class="tree-node">
    
    <?php if($level > 0): ?>
    <div class="tree-connector-top"></div>
    <?php endif; ?>

    
    <div class="tree-card bg-white rounded-xl border-2 <?php echo e($colors['border']); ?> shadow-sm p-3 w-44 text-center"
         onclick="window.location='<?php echo e($viewUrl); ?>'"
         oncontextmenu="showContextMenu(event, <?php echo e($node->id); ?>, '<?php echo e(addslashes($node->full_name)); ?>', '<?php echo e($node->partnershipProgram?->name); ?>', '<?php echo e($viewUrl); ?>', '<?php echo e($editUrl); ?>')">

        
        <div class="w-12 h-12 rounded-full <?php echo e($colors['bg']); ?> flex items-center justify-center text-white font-bold text-lg mx-auto mb-2 overflow-hidden border-2 border-white shadow">
            <?php if($node->photo): ?>
                <img src="<?php echo e($node->photo_url); ?>" alt="<?php echo e($node->full_name); ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <?php echo e(strtoupper(substr($node->full_name, 0, 1))); ?>

            <?php endif; ?>
        </div>

        
        <div class="font-semibold text-slate-800 text-xs leading-tight truncate" title="<?php echo e($node->full_name); ?>">
            <?php echo e($node->full_name); ?>

        </div>
        <div class="text-xs text-slate-400 truncate">{{ $node->username }}</div>

        
        <span class="inline-block mt-1.5 text-xs px-2 py-0.5 rounded-full <?php echo e($colors['badge']); ?> font-medium">
            <?php echo e($node->partnershipProgram?->name ?? 'N/A'); ?>

        </span>

        
        <div class="mt-2 pt-2 border-t border-slate-100 grid grid-cols-2 gap-1 text-xs">
            <div>
                <div class="text-slate-400">Penjualan</div>
                <div class="font-semibold text-slate-700"><?php echo e($node->total_sales); ?></div>
            </div>
            <div>
                <div class="text-slate-400">Status</div>
                <div class="font-semibold <?php echo e($node->status === 'active' ? 'text-green-600' : 'text-amber-600'); ?>">
                    <?php echo e($node->status === 'active' ? 'Aktif' : ucfirst($node->status)); ?>

                </div>
            </div>
        </div>

        
        <?php if($children->isNotEmpty()): ?>
        <div class="mt-1 text-xs text-slate-400">
            <i class="fas fa-users mr-1"></i><?php echo e($children->count()); ?> downline
        </div>
        <?php endif; ?>
    </div>

    
    <?php if($children->isNotEmpty()): ?>
    <div class="tree-children">
        <div class="tree-children-wrapper">
            <?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="relative pt-6">
                <div class="tree-connector-top absolute top-0 left-1/2 -translate-x-1/2"></div>
                <?php echo $__env->make('admin.affiliate.partials.tree-node', ['node' => $child, 'level' => $level + 1], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\affiliate\partials\tree-node.blade.php ENDPATH**/ ?>