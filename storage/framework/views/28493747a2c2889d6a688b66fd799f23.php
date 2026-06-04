

<?php $__env->startSection('title', 'Advanced Search'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Advanced Search & Filtering</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                    <li class="breadcrumb-item active">Advanced Search</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Search Type Tabs -->
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="searchTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="packages-tab" data-toggle="pill" href="#packages" role="tab">
                            <i class="fas fa-box mr-2"></i>Packages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="jamaah-tab" data-toggle="pill" href="#jamaah" role="tab">
                            <i class="fas fa-users mr-2"></i>Jamaah
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tasks-tab" data-toggle="pill" href="#tasks" role="tab">
                            <i class="fas fa-tasks mr-2"></i>Tasks
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="searchTabsContent">
                    <!-- Packages Tab -->
                    <div class="tab-pane fade show active" id="packages" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if (isset($component)) { $__componentOriginal24a8527dff62ad452e575993f533d8a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24a8527dff62ad452e575993f533d8a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search.filter-panel','data' => ['filterType' => 'package','filters' => request()->all()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search.filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filterType' => 'package','filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->all())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal24a8527dff62ad452e575993f533d8a3)): ?>
<?php $attributes = $__attributesOriginal24a8527dff62ad452e575993f533d8a3; ?>
<?php unset($__attributesOriginal24a8527dff62ad452e575993f533d8a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal24a8527dff62ad452e575993f533d8a3)): ?>
<?php $component = $__componentOriginal24a8527dff62ad452e575993f533d8a3; ?>
<?php unset($__componentOriginal24a8527dff62ad452e575993f533d8a3); ?>
<?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Package Results</h3>
                                        <div class="card-tools">
                                            <span class="badge badge-info" id="packageCount">0 results</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped" id="packagesTable">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Type</th>
                                                    <th>Departure</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jamaah Tab -->
                    <div class="tab-pane fade" id="jamaah" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if (isset($component)) { $__componentOriginal24a8527dff62ad452e575993f533d8a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24a8527dff62ad452e575993f533d8a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search.filter-panel','data' => ['filterType' => 'jamaah','filters' => request()->all()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search.filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filterType' => 'jamaah','filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->all())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal24a8527dff62ad452e575993f533d8a3)): ?>
<?php $attributes = $__attributesOriginal24a8527dff62ad452e575993f533d8a3; ?>
<?php unset($__attributesOriginal24a8527dff62ad452e575993f533d8a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal24a8527dff62ad452e575993f533d8a3)): ?>
<?php $component = $__componentOriginal24a8527dff62ad452e575993f533d8a3; ?>
<?php unset($__componentOriginal24a8527dff62ad452e575993f533d8a3); ?>
<?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Jamaah Results</h3>
                                        <div class="card-tools">
                                            <span class="badge badge-info" id="jamaahCount">0 results</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped" id="jamaahTable">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Passport</th>
                                                    <th>Package</th>
                                                    <th>Payment Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Tab -->
                    <div class="tab-pane fade" id="tasks" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if (isset($component)) { $__componentOriginal24a8527dff62ad452e575993f533d8a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24a8527dff62ad452e575993f533d8a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search.filter-panel','data' => ['filterType' => 'task','filters' => request()->all()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search.filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['filterType' => 'task','filters' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->all())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal24a8527dff62ad452e575993f533d8a3)): ?>
<?php $attributes = $__attributesOriginal24a8527dff62ad452e575993f533d8a3; ?>
<?php unset($__attributesOriginal24a8527dff62ad452e575993f533d8a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal24a8527dff62ad452e575993f533d8a3)): ?>
<?php $component = $__componentOriginal24a8527dff62ad452e575993f533d8a3; ?>
<?php unset($__componentOriginal24a8527dff62ad452e575993f533d8a3); ?>
<?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Task Results</h3>
                                        <div class="card-tools">
                                            <span class="badge badge-info" id="taskCount">0 results</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped" id="tasksTable">
                                            <thead>
                                                <tr>
                                                    <th>Task</th>
                                                    <th>Package</th>
                                                    <th>Team</th>
                                                    <th>Status</th>
                                                    <th>Due Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTables for each tab
    const packagesTable = $('#packagesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("admin.search.filter.packages")); ?>',
            data: function(d) {
                return $.extend({}, d, $('#packages form').serializeObject());
            }
        },
        columns: [
            { data: 'package_code', name: 'package_code' },
            { data: 'package_name', name: 'package_name' },
            { data: 'package_type', name: 'package_type' },
            { data: 'departure_date', name: 'departure_date' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    const jamaahTable = $('#jamaahTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("admin.search.filter.jamaah")); ?>',
            data: function(d) {
                return $.extend({}, d, $('#jamaah form').serializeObject());
            }
        },
        columns: [
            { data: 'nama_member', name: 'nama_member' },
            { data: 'no_passport', name: 'no_passport' },
            { data: 'package', name: 'package' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    const tasksTable = $('#tasksTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("admin.search.filter.tasks")); ?>',
            data: function(d) {
                return $.extend({}, d, $('#tasks form').serializeObject());
            }
        },
        columns: [
            { data: 'task_name', name: 'task_name' },
            { data: 'package', name: 'package' },
            { data: 'assigned_to_team', name: 'assigned_to_team' },
            { data: 'status', name: 'status' },
            { data: 'due_date', name: 'due_date' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    // Reload table when filter form is submitted
    $('#packages form').on('submit', function(e) {
        e.preventDefault();
        packagesTable.ajax.reload();
    });

    $('#jamaah form').on('submit', function(e) {
        e.preventDefault();
        jamaahTable.ajax.reload();
    });

    $('#tasks form').on('submit', function(e) {
        e.preventDefault();
        tasksTable.ajax.reload();
    });

    // Helper to serialize form to object
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\search\advanced.blade.php ENDPATH**/ ?>