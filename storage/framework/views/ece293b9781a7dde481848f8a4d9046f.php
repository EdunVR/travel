

<?php $__env->startSection('title'); ?>
Satuan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
<?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
<li class="active">Satuan</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Main row -->
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('<?php echo e(route('satuan.store')); ?>')"
                    class="btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Tambah</button>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered">
                    <thead>
                        <th width="5%">No</th>
                        <th>Satuan</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                </table>
            </div>

        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>

<?php if ($__env->exists('satuan.form')) echo $__env->make('satuan.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            let table;
            $(function () {
                table = $('.table').DataTable({
                    processing: true,
                    autoWidth: false,
                    ajax: '<?php echo e(route('satuan.data')); ?>',
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            sortable: false
                        },
                        {
                            data: 'nama_satuan'
                        },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            searchable: false,
                            sortable: false
                        },
                    ]

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
                                alert('Tidak dapat menyimpan data');
                                return;
                            });
                    }
                })
            });

            function addForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Tambah Satuan');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('post');
                $('#modal-form [name=nama_satuan]').focus();
            }

            function editForm(url) {
                $('#modal-form').modal('show');
                $('#modal-form .modal-title').text('Edit Satuan');
                $('#modal-form form')[0].reset();
                $('#modal-form form').attr('action', url);
                $('#modal-form [name=_method]').val('put');
                $('#modal-form [name=nama_satuan]').focus();

                $.get(url)
                    .done((response) => {
                        $('#modal-form [name=nama_satuan]').val(response.nama_satuan);
                        
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menyimpan data');
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

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\satuan\index.blade.php ENDPATH**/ ?>