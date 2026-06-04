

<?php $__env->startSection('title'); ?>
Piutang
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
<li class="active">Piutang</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <?php if($outlets->count() > 1): ?>
                <div class="form-group">
                    <label for="id_outlet">Pilih Outlet</label>
                    <select name="id_outlet" id="id_outlet" class="form-control">
                        <option value="">Semua Outlet</option>
                        <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-striped table-bordered table-piutang">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Nama Customer</th>
                        <th>Jumlah Piutang</th>
                        <th>Status</th>
                        <!-- <th width="15%"><i class="fa fa-cog"></i></th> -->
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($__env->exists('piutang.detail')) echo $__env->make('piutang.detail', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let table;
    $(function () {
        table = $('.table-piutang').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '<?php echo e(route('piutang.data')); ?>',
                data: function (d) {
                    d.id_outlet = $('#id_outlet').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'tanggal'
                },
                { data: 'nama_outlet' },
                {
                    data: 'nama'
                },
                {
                    data: 'piutang'
                },
                {
                    data: 'status'
                },
                // {
                //     data: 'aksi',
                //     searchable: false,
                //     sortable: false
                // },
            ]
        });
    });

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                })
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\piutang\index.blade.php ENDPATH**/ ?>