

<?php $__env->startSection('title'); ?>
    Daftar PO Penjualan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar PO Penjualan</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row">
                    <?php if($outlets->count() > 1): ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_outlet">Pilih Outlet</label>
                            <select name="id_outlet" id="id_outlet" class="form-control">
                                <option value="">Semua Outlet</option>
                                <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="menunggu">Menunggu</option>
                                <option value="lunas">Lunas</option>
                                <option value="gagal">Gagal</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <?php if(in_array('PO Penjualan Create', Auth::user()->akses ?? [])): ?>
                        <a href="<?php echo e(route('po-penjualan.create')); ?>" class="btn btn-success">
                            <i class="fa fa-plus"></i> Buat PO Baru
                        </a>
                        <?php endif; ?>
                        <button id="print-btn" class="btn btn-primary">
                            <i class="fa fa-print"></i> Cetak Laporan
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-po-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>No. PO</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Customer</th>
                        <th>Total Item</th>
                        <th>Total Harga</th>
                        <th>Ongkir</th>
                        <th>Diskon</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Kasir</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let table;

    $(function () {
        table = $('.table-po-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '<?php echo e(route('po-penjualan.data')); ?>',
                data: function (d) {
                    d.id_outlet = $('#id_outlet').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.status = $('#status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'no_po'},
                {data: 'tanggal'},
                {data: 'nama_outlet'},
                {data: 'nama_member'},
                {data: 'total_item'},
                {data: 'total_harga'},
                {data: 'ongkir'},
                {data: 'diskon'},
                {data: 'bayar'},
                {data: 'status_badge', searchable: false, sortable: false},
                {data: 'kasir'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#id_outlet, #start_date, #end_date, #status').on('change', function () {
            table.ajax.reload();
        });

        $('#print-btn').on('click', function() {
            let id_outlet = $('#id_outlet').val();
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();
            let status = $('#status').val();
            
            let url = '<?php echo e(route("po-penjualan.print-report")); ?>' + 
                     '?start_date=' + start_date + 
                     '&end_date=' + end_date;
            
            if (id_outlet) {
                url += '&id_outlet=' + id_outlet;
            }
            if (status) {
                url += '&status=' + status;
            }
            
            window.open(url, '_blank');
        });
    });

    // Tambahkan di bagian scripts index.blade.php
    function showDetail(url) {
        $('#modal-detail').modal('show');
        $('#modal-detail .modal-content').load(url);
    }

    // Fungsi untuk cetak PDF individual
    function printPO(url) {
        window.open(url, '_blank');
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
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
                });
        }
    }
</script>
<?php $__env->stopPush(); ?>

    <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Detail PO Penjualan</h4>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\po_penjualan\index.blade.php ENDPATH**/ ?>