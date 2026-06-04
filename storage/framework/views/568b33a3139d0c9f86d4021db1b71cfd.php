<div class="flex items-center gap-2">
    <?php if($production->status === 'draft'): ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'production.produksi.approve')): ?>
        <button onclick="approveProduction(<?php echo e($production->id); ?>)" 
                class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition"
                title="Setujui">
            <i class='bx bx-check text-lg'></i>
        </button>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'production.produksi.update')): ?>
        <button onclick="editProduction(<?php echo e($production->id); ?>)" 
                class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded transition"
                title="Edit">
            <i class='bx bx-edit text-lg'></i>
        </button>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'production.produksi.delete')): ?>
        <button onclick="deleteProduction(<?php echo e($production->id); ?>)" 
                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition"
                title="Hapus">
            <i class='bx bx-trash text-lg'></i>
        </button>
        <?php endif; ?>
    <?php elseif($production->status === 'approved'): ?>
        <button onclick="startProduction(<?php echo e($production->id); ?>)" 
                class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded transition"
                title="Mulai Produksi">
            <i class='bx bx-play text-lg'></i>
        </button>
    <?php elseif($production->status === 'in_progress'): ?>
        <button onclick="addRealization(<?php echo e($production->id); ?>)" 
                class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded transition"
                title="Tambah Realisasi">
            <i class='bx bx-plus-circle text-lg'></i>
        </button>
    <?php endif; ?>
    
    <button onclick="viewProduction(<?php echo e($production->id); ?>)" 
            class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded transition"
            title="Lihat Detail">
        <i class='bx bx-show text-lg'></i>
    </button>
</div>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\produksi\produksi\partials\actions.blade.php ENDPATH**/ ?>