<style>
    /* Style untuk komponen list */
    .komponen-list {
        max-height: 100px;
        overflow-y: auto;
        padding-right: 5px;
    }
    
    .komponen-list::-webkit-scrollbar {
        width: 5px;
    }
    
    .komponen-list::-webkit-scrollbar-thumb {
        background-color: #ddd;
        border-radius: 10px;
    }
    
    .bullet-point {
        color: #6c757d;
        font-size: 0.8rem;
        line-height: 1.2;
    }
    
    /* Perkecil ukuran button */
    .btn-xs {
        padding: 0.15rem 0.3rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.2rem;
    }
    
    .feather-xs {
        width: 10px;
        height: 10px;
    }
    
    /* Border untuk deskripsi */
    .border-bottom {
        border-bottom: 1px solid #dee2e6 !important;
    }
    
    /* Perbaikan tampilan progress bar */
    .progress {
        margin-bottom: 0.5rem;
    }
    
    .progress-bar {
        font-size: 0.7rem;
        line-height: 20px;
    }
    
    /* Perbaikan tampilan badge */
    .badge {
        margin-bottom: 2px;
        font-size: 0.75rem;
        font-weight: normal;
        display: inline-block;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>



<?php $__env->startSection('title'); ?> Daftar RAB <?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Daftar RAB</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <a href="<?php echo e(route('rab_template.create')); ?>" class="btn btn-success btn-sm btn-flat">
                        <i data-feather="plus-circle"></i> Tambah RAB
                    </a>
                </div>
                <div class="box-tools pull-right">
                    <div class="has-feedback">
                        <input type="text" class="form-control input-sm" id="search-input" placeholder="Cari...">
                        <span class="glyphicon glyphicon-search form-control-feedback"></span>
                    </div>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered" id="rab-table">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal Pembuatan</th>
                        <th>Nama Template</th>
                        <th width="20%">Deskripsi & Komponen</th>
                        <th width="10%">Budget Total</th>
                        <th width="10%">Nilai Disetujui</th>
                        <th width="15%">Realisasi Pemakaian</th>
                        <th width="8%">Status</th>
                        <th width="8%">Produk Terkait</th>
                        <th width="8%"><i data-feather="settings"></i></th>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $rabTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($key+1); ?></td>
                            <td><?php echo e($template->created_at->format('d/m/Y')); ?></td>
                            <td><?php echo e($template->nama_template); ?></td>
                            <td>
                                <div class="border-bottom pb-2 mb-2">
                                    <small class="text-muted"><?php echo e($template->deskripsi ?: '-'); ?></small>
                                </div>
                                <div class="komponen-list">
                                    <?php $__currentLoopData = $template->details->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="d-flex align-items-start mb-1">
                                            <span class="bullet-point">•</span>
                                            <small class="ms-1"><?php echo e($detail->nama_komponen); ?> (<?php echo e($detail->jumlah); ?> <?php echo e($detail->satuan); ?>)</small>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($template->details->count() > 3): ?>
                                        <small class="text-muted">+<?php echo e($template->details->count() - 3); ?> komponen lainnya</small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-right">Rp <?php echo e(number_format($template->total_budget, 0, ',', '.')); ?></td>
                            <td class="text-right">Rp <?php echo e(number_format($template->total_nilai_disetujui, 0, ',', '.')); ?></td>
                            <td>
                                <?php
                                    $percentage = $template->total_nilai_disetujui > 0 
                                        ? ($template->total_realisasi / $template->total_nilai_disetujui) * 100 
                                        : 0;
                                    $sisa = $template->total_nilai_disetujui - $template->total_realisasi;
                                ?>
                                <div class="progress" style="height: 20px; margin-bottom: 5px;">
                                    <div class="progress-bar 
                                        <?php if($percentage > 100): ?> progress-bar-danger 
                                        <?php elseif($percentage > 80): ?> progress-bar-warning 
                                        <?php else: ?> progress-bar-success <?php endif; ?>" 
                                        role="progressbar" 
                                        style="width: <?php echo e($percentage); ?>%"
                                        aria-valuenow="<?php echo e($percentage); ?>" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        <?php echo e(round($percentage)); ?>%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    Terpakai: Rp <?php echo e(number_format($template->total_realisasi, 0, ',', '.')); ?> / 
                                    Sisa: Rp <?php echo e(number_format($sisa, 0, ',', '.')); ?>

                                </small>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo e($template->status == 'Draft' ? 'secondary' : 
                                    ($template->status == 'Disetujui Semua' ? 'success' : 
                                    ($template->status == 'Disetujui Sebagian' ? 'primary' : 
                                    ($template->status == 'Disetujui dengan Revisi' ? 'warning' : 
                                    ($template->status == 'Ditransfer' ? 'info' : 'danger'))))); ?>">
                                    <?php echo e($template->status); ?>

                                </span>
                            </td>
                            <td>
                                <?php $__currentLoopData = $template->products->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge badge-info"><?php echo e($product->nama_produk); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($template->products->count() > 2): ?>
                                    <span class="badge badge-secondary">+<?php echo e($template->products->count() - 2); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('rab_template.show', $template->id_rab)); ?>" class="btn btn-success btn-xs">
                                        <i data-feather="eye" class="feather-xs"></i>
                                    </a>
                                    <a href="<?php echo e(route('rab_template.edit', $template->id_rab)); ?>" class="btn btn-info btn-xs">
                                        <i data-feather="edit" class="feather-xs"></i>
                                    </a>
                                    <button onclick="deleteData(`<?php echo e(route('rab_template.destroy', $template->id_rab)); ?>`)" class="btn btn-danger btn-xs">
                                        <i data-feather="trash-2" class="feather-xs"></i>
                                    </button>
                                </div>
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
<script>
$(function () {
    feather.replace();
    
    // Search functionality
    $('#search-input').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        const $rows = $('#rab-table tbody tr');
        
        $rows.each(function() {
            const rowText = $(this).text().toLowerCase();
            if (rowText.includes(searchText)) {
                $(this).show();
                if (searchText.length > 0) {
                    highlightText($(this), searchText);
                }
            } else {
                $(this).hide();
            }
        });
    });
});

// Highlight search text
function highlightText($element, searchText) {
    $element.find('.search-highlight').contents().unwrap();
    
    $element.contents().each(function() {
        if (this.nodeType === 3) {
            const text = $(this).text();
            const regex = new RegExp(searchText, 'gi');
            const newText = text.replace(regex, match => 
                `<span class="search-highlight">${match}</span>`
            );
            $(this).replaceWith(newText);
        } else {
            $(this).find(':not(script, style, a, i)').each(function() {
                highlightText($(this), searchText);
            });
        }
    });
}

function deleteData(url) {
    if (confirm('Yakin ingin menghapus data terpilih?')) {
        $.post(url, {
            '_token': $('[name=csrf-token]').attr('content'),
            '_method': 'delete'
        })
        .done((response) => {
            window.location.reload();
        })
        .fail((errors) => {
            alert('Tidak dapat menghapus data');
            return;
        });
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\rab_template\index.blade.php ENDPATH**/ ?>