

<?php $__env->startSection('title'); ?>
Supplier
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
<li class="active">Supplier</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Main row -->
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
                <button onclick="addForm('<?php echo e(route('supplier.store')); ?>')"
                    class="btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Outlet</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Hutang ke Supplier</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                </table>
            </div>

        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

<?php if ($__env->exists('supplier.form')) echo $__env->make('supplier.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            let table;
            $(function () {
                table = $('.table').DataTable({
                    processing: true,
                    autoWidth: false,
                    ajax: {
                        url: '<?php echo e(route('supplier.data')); ?>',
                        data: function (d) {
                            d.id_outlet = $('#id_outlet').val();
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex', searchable: false, sortable: false},
                        {data: 'nama'},
                        {data: 'nama_outlet'},
                        {data: 'telepon'},
                        {data: 'alamat'},
                        {data: 'hutang'},
                        {
                            data: 'aksi',
                            name: 'aksi',
                            searchable: false,
                            sortable: false
                        },
                    ]

                });

                $('#id_outlet').on('change', function () {
                    table.ajax.reload();
                });

                $('#modal-form form').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        // handle the invalid form...
                    } else {
                        e.preventDefault();
                        $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                            .done((response) => {
                                $('#modal-form').modal('hide');
                                table.ajax.reload();
                            })
                            .fail((errors) => {
                                console.log(errors);
                                alert('Tidak dapat menyimpan data');
                                return;
                            });
                    }
                })
            });

            function addForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Tambah Supplier');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=nama]').focus();
            }

            function editForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Edit Supplier');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('put');
                $('#modal-form [name=nama]').focus();

                $.get(url)
                    .done((response) => {
                        $('#modal-form [name=nama]').val(response.nama);
                        $('#modal-form [name=telepon]').val(response.telepon);
                        $('#modal-form [name=alamat]').val(response.alamat);
                        $('#modal-form [name=email]').val(response.email);
                        $('#modal-form [name=bank]').val(response.bank);
                        $('#modal-form [name=no_rekening]').val(response.no_rekening);
                        $('#modal-form [name=atas_nama]').val(response.atas_nama);
                        $('#modal-form [name=id_outlet]').val(response.id_outlet);
                    })
                    .fail((errors) => {
                        console.log(errors);
                        alert('Tidak dapat menampilkan data');
                        return;
                    })
            }

            function deleteData(url) {
                if (confirm('Yakin ingin menghapus data?')) {
                    $.post(url, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'delete'
                        })
                        .done((response) => {
                            console.log(response);
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

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\supplier\index.blade.php ENDPATH**/ ?>