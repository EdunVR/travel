<style>
    .card-body {
        background: white;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }
</style>



<?php $__env->startSection('title', 'Manajemen Kinerja Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manajemen Kinerja Karyawan</h6>
            <div>
                <form action="<?php echo e(route('hrm.performance.index')); ?>" method="GET" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="month" class="mr-2">Pilih Periode:</label>
                        <input type="month" name="month" id="month" class="form-control" value="<?php echo e($month); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary ml-2 mb-2">Filter</button>
                </form>
                <a href="<?php echo e(route('hrm.performance.create')); ?>" class="btn btn-primary ml-2">
                    <i class="fas fa-plus"></i> Tambah Kinerja
                </a>
                <a href="<?php echo e(route('hrm.performance.export_pdf', ['month' => $month])); ?>" class="btn btn-success ml-2">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Posisi</th>
                            <th>Tanggal Penilaian</th>
                            <th>Kriteria</th>
                            <th>Nilai</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $performances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $performance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($performance->recruitment->name); ?></td>
                            <td><?php echo e($performance->recruitment->position); ?></td>
                            <td><?php echo e($performance->evaluation_date); ?></td>
                            <td><?php echo e($performance->criteria); ?></td>
                            <td><?php echo e($performance->score); ?></td>
                            <td><?php echo e($performance->remarks); ?></td>
                            <td>
                                <a href="<?php echo e(route('hrm.performance.edit', $performance->id)); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="<?php echo e(route('hrm.performance.destroy', $performance->id)); ?>" method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .table thead th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .table tbody tr:hover {
        background-color: #f1f1f1;
    }
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Inisialisasi DataTables
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\performance\index.blade.php ENDPATH**/ ?>