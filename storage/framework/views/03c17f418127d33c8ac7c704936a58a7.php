

<?php $__env->startSection('title', 'Manajemen Penggajian & Benefit'); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')); ?>">
<style>
    .total-info {
        font-size: 16px;
        font-weight: bold;
        padding: 8px 12px;
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 5px;
        display: inline-block;
        margin-left: 10px;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-draft {
        background-color: #ffc107;
        color: #000;
    }
    .status-final {
        background-color: #28a745;
        color: #fff;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Manajemen Penggajian & Benefit</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <form action="<?php echo e(route('hrm.payroll.index')); ?>" method="GET" class="form-inline">
                    <div class="form-group mb-2">
                        <label for="month" class="mr-2">Pilih Bulan dan Tahun:</label>
                        <input type="month" name="month" id="month" class="form-control" value="<?php echo e(request('month') ?? date('Y-m')); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary ml-2 mb-2">Filter</button>
                </form>
                <a href="<?php echo e(route('hrm.payroll.create')); ?>" class="btn btn-primary btn-xs btn-flat"><i class="fa fa-plus"></i> Tambah Penggajian</a>
                <a id="exportPdf" href="<?php echo e(route('hrm.payroll.export_pdf', ['month' => request('month') ?? date('Y-m')])); ?>" target="_blank" class="btn btn-success btn-xs btn-flat">
                    <i class="fa fa-file-pdf-o"></i> Export PDF
                </a>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th> <!-- Kolom Tanggal -->
                        <th>Nama Karyawan</th>
                        <th>Posisi</th>
                        <th>Gaji Pokok</th>
                        <th>Tambahan dan Potongan Gaji</th>
                        <th>Harga Per Jam</th>
                        <th>Total Jam Kerja</th>
                        <th>Total Gaji</th>
                        <th>Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        // Hitung total tambahan gaji
                        $totalAdditionalSalary = 0;
                        if ($payroll->additional_salary) {
                            $additionalSalaries = json_decode($payroll->additional_salary, true);
                            foreach ($additionalSalaries as $additional) {
                                $totalAdditionalSalary += $additional['amount'];
                            }
                        }

                        // Hitung total potongan gaji
                        $totalDeductions = 0;
                        if ($payroll->deductions) {
                            $deductions = json_decode($payroll->deductions, true);
                            foreach ($deductions as $deduction) {
                                $totalDeductions += $deduction['amount'];
                            }
                        }

                        // Hitung total gaji
                        $totalSalary = $payroll->salary + ($payroll->total_hours_worked * $payroll->hourly_rate) + $totalAdditionalSalary - $totalDeductions;

                        $statusClass = $payroll->benefits === 'final' ? 'status-final' : 'status-draft';
                        $statusText = $payroll->benefits === 'final' ? 'Final' : 'Draft';
                    ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($payroll->created_at->format('d-m-Y')); ?></td>
                        <td><?php echo e($payroll->employee->name); ?></td>
                        <td><?php echo e($payroll->employee->position); ?></td>
                        <td><?php echo e(format_uang($payroll->salary)); ?></td>
                        <td>
                            <?php if($payroll->additional_salary || $payroll->deductions): ?>
                                <strong>Tambahan Gaji:</strong><br>
                                <?php if($payroll->additional_salary): ?>
                                    <?php $__currentLoopData = json_decode($payroll->additional_salary, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $additional): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        - <?php echo e(format_uang($additional['amount'])); ?> : <?php echo e($additional['description'] ?? 'Tanpa Deskripsi'); ?><br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    Tidak ada tambahan gaji.<br>
                                <?php endif; ?>

                                <strong>Potongan Gaji:</strong><br>
                                <?php if($payroll->deductions): ?>
                                    <?php $__currentLoopData = json_decode($payroll->deductions, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deduction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        - <?php echo e(format_uang($deduction['amount'])); ?> : <?php echo e($deduction['description'] ?? 'Tanpa Deskripsi'); ?><br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    Tidak ada potongan gaji.<br>
                                <?php endif; ?>
                            <?php else: ?>
                                Tidak ada tambahan atau potongan gaji.
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(format_uang($payroll->hourly_rate)); ?> / Jam</td>
                        <td><?php echo e($payroll->total_hours_worked); ?> Jam</td>
                        <td><?php echo e(format_uang($totalSalary)); ?></td>
                        <td>
                            <span class="status-badge <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
                        </td>
                        <td>
                            <?php if($payroll->benefits !== 'final'): ?>
                                <a href="<?php echo e(route('hrm.payroll.edit', $payroll->id)); ?>" class="btn btn-warning btn-xs btn-flat"><i class="fa fa-edit"></i></a>
                                <button class="btn btn-danger btn-xs btn-flat delete-payroll" data-id="<?php echo e($payroll->id); ?>"><i class="fa fa-trash"></i></button>
                            <?php endif; ?>
                            
                            <?php if($payroll->benefits === 'final'): ?>
                                <a href="<?php echo e(route('hrm.payroll.print', ['id' => $payroll->id, 'month' => request('month') ?? date('Y-m')])); ?>" class="btn btn-info btn-xs btn-flat"><i class="fa fa-print"></i> Cetak</a>
                            <?php else: ?>
                                <button class="btn btn-success btn-xs btn-flat finalize-payroll" data-id="<?php echo e($payroll->id); ?>"><i class="fa fa-check"></i> Finalisasi</button>
                            <?php endif; ?>
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

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')); ?>"></script>
<script>
    $(function () {
        $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: false,
            autoWidth: false,
            bSort: false,
            bPaginate: false,
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });

    function updatePeriode() {
        $('#modal-form').modal('show');
    }

    // SweetAlert for delete confirmation
        $(document).on('click', '.delete-payroll', function() {
            const payrollId = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Data Payroll?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('hrm.payroll.destroy', ['payroll' => 'PLACEHOLDER'])); ?>".replace('PLACEHOLDER', payrollId),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function() {
                            Swal.fire('Deleted!', 'Data payroll berhasil dihapus', 'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Gagal menghapus', 'error');
                        }
                    });
                }
            });
        });

        // SweetAlert for finalize confirmation
        $(document).on('click', '.finalize-payroll', function() {
            const payrollId = $(this).data('id');
            
            Swal.fire({
                title: 'Finalisasi Payroll?',
                text: "Setelah difinalisasi, data tidak bisa diubah lagi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Finalisasi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('hrm.payroll.finalize', ['payroll' => 'PLACEHOLDER'])); ?>".replace('PLACEHOLDER', payrollId),
                        type: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function() {
                            Swal.fire('Success!', 'Payroll berhasil difinalisasi', 'success')
                                .then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Gagal memfinalisasi', 'error');
                        }
                    });
                }
            });
        });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\hrm\payroll\index.blade.php ENDPATH**/ ?>