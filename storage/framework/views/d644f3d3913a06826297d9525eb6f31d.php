

<?php $__env->startSection('title', 'Audit Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Audit Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                    <li class="breadcrumb-item active">Audit Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Audit Trail</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#filterPanel">
                        <i class="fas fa-filter"></i> Filters
                    </button>
                    <a href="<?php echo e(route('admin.audit.export', request()->query())); ?>" class="btn btn-sm btn-success">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="collapse <?php echo e(request()->hasAny(['user_id', 'action_type', 'model_type', 'start_date', 'end_date']) ? 'show' : ''); ?>" id="filterPanel">
                <div class="card-body border-bottom">
                    <form method="GET" action="<?php echo e(route('admin.audit.index')); ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>User</label>
                                    <select name="user_id" class="form-control">
                                        <option value="">All Users</option>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                                <?php echo e($user->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Action Type</label>
                                    <select name="action_type" class="form-control">
                                        <option value="">All Actions</option>
                                        <?php $__currentLoopData = $actionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type); ?>" <?php echo e(request('action_type') == $type ? 'selected' : ''); ?>>
                                                <?php echo e(ucwords(str_replace('_', ' ', $type))); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Model Type</label>
                                    <select name="model_type" class="form-control">
                                        <option value="">All Models</option>
                                        <?php $__currentLoopData = $modelTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type); ?>" <?php echo e(request('model_type') == $type ? 'selected' : ''); ?>>
                                                <?php echo e($type); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <div class="input-group">
                                        <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>" placeholder="Start Date">
                                        <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>" placeholder="End Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                <a href="<?php echo e(route('admin.audit.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Timestamp</th>
                                <th style="width: 120px;">User</th>
                                <th style="width: 120px;">Action Type</th>
                                <th>Description</th>
                                <th style="width: 100px;">Model</th>
                                <th style="width: 100px;">IP Address</th>
                                <th style="width: 80px;">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <small><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></small>
                                    </td>
                                    <td>
                                        <?php if($log->user): ?>
                                            <span class="badge badge-info"><?php echo e($log->user->name); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeClass = match($log->action_type) {
                                                'created' => 'success',
                                                'updated' => 'warning',
                                                'deleted' => 'danger',
                                                'login' => 'info',
                                                'logout' => 'secondary',
                                                'workflow_transition' => 'primary',
                                                'payment' => 'success',
                                                'document_upload' => 'info',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge badge-<?php echo e($badgeClass); ?>">
                                            <?php echo e(ucwords(str_replace('_', ' ', $log->action_type))); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo e($log->description); ?></small>
                                    </td>
                                    <td>
                                        <?php if($log->model_type): ?>
                                            <small class="text-muted">
                                                <?php echo e(class_basename($log->model_type)); ?>

                                                <?php if($log->model_id): ?>
                                                    #<?php echo e($log->model_id); ?>

                                                <?php endif; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($log->ip_address); ?></small>
                                    </td>
                                    <td>
                                        <?php if($log->old_values || $log->new_values): ?>
                                            <button type="button" class="btn btn-xs btn-outline-primary" 
                                                    data-toggle="modal" 
                                                    data-target="#detailModal<?php echo e($log->id); ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <!-- Detail Modal -->
                                            <div class="modal fade" id="detailModal<?php echo e($log->id); ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Audit Log Details</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <dl class="row">
                                                                <dt class="col-sm-3">Timestamp:</dt>
                                                                <dd class="col-sm-9"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></dd>

                                                                <dt class="col-sm-3">User:</dt>
                                                                <dd class="col-sm-9"><?php echo e($log->user ? $log->user->name : 'System'); ?></dd>

                                                                <dt class="col-sm-3">Action:</dt>
                                                                <dd class="col-sm-9"><?php echo e(ucwords(str_replace('_', ' ', $log->action_type))); ?></dd>

                                                                <dt class="col-sm-3">Description:</dt>
                                                                <dd class="col-sm-9"><?php echo e($log->description); ?></dd>

                                                                <?php if($log->old_values): ?>
                                                                    <dt class="col-sm-3">Old Values:</dt>
                                                                    <dd class="col-sm-9">
                                                                        <pre class="bg-light p-2"><?php echo e(json_encode($log->old_values, JSON_PRETTY_PRINT)); ?></pre>
                                                                    </dd>
                                                                <?php endif; ?>

                                                                <?php if($log->new_values): ?>
                                                                    <dt class="col-sm-3">New Values:</dt>
                                                                    <dd class="col-sm-9">
                                                                        <pre class="bg-light p-2"><?php echo e(json_encode($log->new_values, JSON_PRETTY_PRINT)); ?></pre>
                                                                    </dd>
                                                                <?php endif; ?>

                                                                <dt class="col-sm-3">IP Address:</dt>
                                                                <dd class="col-sm-9"><?php echo e($log->ip_address); ?></dd>

                                                                <dt class="col-sm-3">User Agent:</dt>
                                                                <dd class="col-sm-9"><small><?php echo e($log->user_agent); ?></small></dd>
                                                            </dl>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No audit logs found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if($logs->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($logs->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?>
        </div>

        <!-- Info Box -->
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info-circle"></i> About Audit Logs</h5>
            <p class="mb-0">
                Audit logs are immutable records of all significant actions in the system. 
                They cannot be modified or deleted to ensure data integrity and compliance. 
                Logs are retained for 5 years as per policy.
            </p>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\audit\index.blade.php ENDPATH**/ ?>