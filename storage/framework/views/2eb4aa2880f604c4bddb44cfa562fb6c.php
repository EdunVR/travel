<a href="<?php echo e(route('irp.investor.show', $investor->id)); ?>" class="btn btn-sm btn-info" title="Detail">
    <i class="fas fa-eye"></i>
</a>
<a href="<?php echo e(route('irp.investor.edit', $investor->id)); ?>" class="btn btn-sm btn-warning" title="Edit">
    <i class="fas fa-edit"></i>
</a>
<form action="<?php echo e(route('irp.investor.destroy', $investor->id)); ?>" method="POST" style="display: inline-block;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin?')">
        <i class="fas fa-trash"></i>
    </button>
</form>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\irp\investor\actions.blade.php ENDPATH**/ ?>