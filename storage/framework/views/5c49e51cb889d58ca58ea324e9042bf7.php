

<?php $__env->startSection('title', 'Notifikasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Notifikasi
            <small>Kelola notifikasi Anda</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(route('admin.dashboard')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Notifikasi</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Semua Notifikasi</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-sm btn-default" onclick="markAllAsRead()">
                                <i class="fa fa-check"></i> Tandai Semua Dibaca
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if($notifications->count() > 0): ?>
                            <div class="list-group">
                                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="#" 
                                       class="list-group-item <?php echo e(!$notification->is_read ? 'active' : ''); ?>"
                                       onclick="markAsRead(<?php echo e($notification->id); ?>); return false;">
                                        <div class="row">
                                            <div class="col-md-1 text-center">
                                                <i class="fa <?php echo e($notification->icon); ?> fa-2x text-<?php echo e($notification->color); ?>"></i>
                                            </div>
                                            <div class="col-md-11">
                                                <h4 class="list-group-item-heading">
                                                    <?php echo e($notification->title); ?>

                                                    <?php if(!$notification->is_read): ?>
                                                        <span class="label label-warning pull-right">Baru</span>
                                                    <?php endif; ?>
                                                </h4>
                                                <p class="list-group-item-text"><?php echo e($notification->message); ?></p>
                                                <small class="text-muted">
                                                    <i class="fa fa-clock-o"></i> 
                                                    <?php echo e($notification->created_at->diffForHumans()); ?>

                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            
                            <div class="text-center" style="margin-top: 20px;">
                                <?php echo e($notifications->links()); ?>

                            </div>
                        <?php else: ?>
                            <div class="text-center" style="padding: 40px;">
                                <i class="fa fa-bell-o fa-4x text-muted"></i>
                                <p class="text-muted" style="margin-top: 20px;">Tidak ada notifikasi</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`<?php echo e(url('notifications')); ?>/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('<?php echo e(route("notifications.read-all")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\notifications\index.blade.php ENDPATH**/ ?>