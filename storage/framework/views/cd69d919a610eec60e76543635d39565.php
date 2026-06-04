<tr class="level-<?php echo e($level); ?>">
    <td class="account-code" style="text-align: left">
        <span class="d-block text-left pr-2"><?php echo e($account['code']); ?></span>
    </td>
    <td class="account-name" style="text-align: left">
        <span class="d-block text-left pl-2"><?php echo e($account['name']); ?></span>
    </td>
    <td class="text-center">
        <?php switch($account['type']):
            case ('asset'): ?>
                <span class="badge badge-primary">Asset</span>
                <?php break; ?>
            <?php case ('liability'): ?>
                <span class="badge badge-success">Liability</span>
                <?php break; ?>
            <?php case ('equity'): ?>
                <span class="badge badge-info">Equity</span>
                <?php break; ?>
            <?php case ('revenue'): ?>
                <span class="badge badge-warning">Revenue</span>
                <?php break; ?>
            <?php case ('expense'): ?>
                <span class="badge badge-danger">Expense</span>
                <?php break; ?>
        <?php endswitch; ?>
    </td>
    <td class="text-center">
        <?php if($account['is_active']): ?>
            <span class="badge badge-success">Aktif</span>
        <?php else: ?>
            <span class="badge badge-secondary">Non-Aktif</span>
        <?php endif; ?>
    </td>
    <td class="text-center">
        <div class="btn-group btn-group-sm">
            <button class="btn btn-primary add-child-btn" 
                    data-parent-code="<?php echo e($account['code']); ?>" 
                    data-parent-name="<?php echo e($account['name']); ?>"
                    title="Tambah Child">
                <i data-feather="plus" width="16"></i>
            </button>
            <?php if($level > 0 || empty($account['children'])): ?>
                <button class="btn btn-danger delete-account-btn" 
                        data-code="<?php echo e($account['code']); ?>" 
                        title="Hapus">
                    <i data-feather="trash-2" width="16"></i>
                </button>
            <?php endif; ?>
        </div>
    </td>
</tr>

<?php if(!empty($account['children'])): ?>
    <?php $__currentLoopData = $account['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('financial.book.account_row', [
            'account' => $child, 
            'level' => $level + 1,
            'parentAccounts' => $parentAccounts
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\financial\book\account_row.blade.php ENDPATH**/ ?>