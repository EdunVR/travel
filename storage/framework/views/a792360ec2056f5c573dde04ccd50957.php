

<?php $__env->startSection('title', 'Document Management - ' . $booking->jamaah->nama_lengkap); ?>

<?php $__env->startSection('content'); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Document Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.inventaris.booking.index')); ?>">Bookings</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.inventaris.booking.show', $booking->id)); ?>"><?php echo e($booking->booking_code); ?></a></li>
                    <li class="breadcrumb-item active">Documents</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Jamaah Info Card -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Jamaah Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo e($booking->jamaah->nama_lengkap); ?></p>
                        <p><strong>Booking Code:</strong> <?php echo e($booking->booking_code); ?></p>
                        <p><strong>Package:</strong> <?php echo e($booking->travelPackage->package_name); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Departure:</strong> <?php echo e($booking->keberangkatan ? $booking->keberangkatan->keberangkatan_name : 'Not assigned'); ?></p>
                        <p><strong>Departure Date:</strong> <?php echo e($booking->keberangkatan ? $booking->keberangkatan->departure_date->format('d M Y') : '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Checklist -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Document Checklist</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $document = $booking->documents->where('document_type', $docType)->first();
                            $statusColor = $document ? (new \App\Models\JamaahDocument(['status' => $document->status]))->getStatusBadgeColor() : 'secondary';
                            $statusText = $document ? ucfirst($document->status) : 'Not uploaded';
                            $isExpiring = $document && $document->isExpiringSoon();
                            $isExpired = $document && $document->isExpired();
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-<?php echo e($statusColor); ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-1">
                                                <?php if($document): ?>
                                                    <i class="fas fa-check-circle text-<?php echo e($statusColor); ?>"></i>
                                                <?php else: ?>
                                                    <i class="far fa-circle text-secondary"></i>
                                                <?php endif; ?>
                                                <?php echo e(ucwords(str_replace('_', ' ', $docType))); ?>

                                            </h5>
                                            <span class="badge badge-<?php echo e($statusColor); ?>"><?php echo e($statusText); ?></span>
                                            
                                            <?php if($document): ?>
                                                <?php if($document->document_number): ?>
                                                    <p class="mb-0 mt-2"><small><strong>Number:</strong> <?php echo e($document->document_number); ?></small></p>
                                                <?php endif; ?>
                                                <?php if($document->expiry_date): ?>
                                                    <p class="mb-0"><small><strong>Expiry:</strong> <?php echo e($document->expiry_date->format('d M Y')); ?></small></p>
                                                    <?php if($isExpired): ?>
                                                        <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Expired</span>
                                                    <?php elseif($isExpiring): ?>
                                                        <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Expiring Soon</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if($document->verified_at): ?>
                                                    <p class="mb-0"><small><strong>Verified by:</strong> <?php echo e($document->verifier->name ?? 'N/A'); ?></small></p>
                                                    <p class="mb-0"><small><strong>Verified at:</strong> <?php echo e($document->verified_at->format('d M Y H:i')); ?></small></p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="btn-group-vertical">
                                            <button type="button" class="btn btn-sm btn-primary mb-1" onclick="openUploadModal('<?php echo e($docType); ?>', <?php echo e($document ? $document->id : 'null'); ?>)">
                                                <i class="fas fa-upload"></i> <?php echo e($document ? 'Replace' : 'Upload'); ?>

                                            </button>
                                            <?php if($document): ?>
                                                <button type="button" class="btn btn-sm btn-info mb-1" onclick="previewDocument(<?php echo e($document->id); ?>)">
                                                    <i class="fas fa-eye"></i> Preview
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success mb-1" onclick="downloadDocument(<?php echo e($document->id); ?>)">
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                                <?php if($document->status !== 'approved'): ?>
                                                    <button type="button" class="btn btn-sm btn-warning mb-1" onclick="openVerifyModal(<?php echo e($document->id); ?>, 'approved')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                <?php endif; ?>
                                                <?php if($document->status !== 'rejected'): ?>
                                                    <button type="button" class="btn btn-sm btn-danger mb-1" onclick="openVerifyModal(<?php echo e($document->id); ?>, 'rejected')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if($document && $document->notes): ?>
                                        <div class="mt-2">
                                            <small><strong>Notes:</strong> <?php echo e($document->notes); ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document" style="margin-top: 2rem;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" id="upload_document_type" name="document_type">
                    
                    <div class="form-group">
                        <label>Document Type</label>
                        <input type="text" class="form-control" id="upload_document_type_display" readonly>
                    </div>

                    <div class="form-group">
                        <label>Document Number</label>
                        <input type="text" class="form-control" name="document_number" placeholder="Enter document number">
                    </div>

                    <div class="form-group">
                        <label>Issue Date</label>
                        <input type="date" class="form-control" name="issue_date">
                    </div>

                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" class="form-control" name="expiry_date">
                    </div>

                    <div class="form-group">
                        <label>File <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="documentFile" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <label class="custom-file-label" for="documentFile">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Additional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document" style="margin-top: 2rem;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify Document</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="verifyForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" id="verify_document_id">
                    <input type="hidden" id="verify_status" name="status">
                    
                    <p>Are you sure you want to <strong id="verify_action_text"></strong> this document?</p>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Verification notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" style="margin-top: 2rem;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="previewFrame" style="width: 100%; height: 500px; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // File input label update
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Upload form submission
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        $.ajax({
            url: '<?php echo e(route("admin.inventaris.document.upload", $booking->id)); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message);
                $('#uploadModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        toastr.error(errors[field][0]);
                    }
                } else {
                    toastr.error(xhr.responseJSON.message || 'Upload failed');
                }
            }
        });
    });

    // Verify form submission
    $('#verifyForm').on('submit', function(e) {
        e.preventDefault();
        
        let documentId = $('#verify_document_id').val();
        let formData = $(this).serialize();
        
        $.ajax({
            url: '/travel/document/' + documentId + '/verify',
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success(response.message);
                $('#verifyModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Verification failed');
            }
        });
    });
});

function openUploadModal(docType, documentId) {
    $('#upload_document_type').val(docType);
    $('#upload_document_type_display').val(docType.replace('_', ' ').toUpperCase());
    $('#uploadForm')[0].reset();
    $('.custom-file-label').html('Choose file');
    $('#uploadModal').modal('show');
}

function openVerifyModal(documentId, status) {
    $('#verify_document_id').val(documentId);
    $('#verify_status').val(status);
    $('#verify_action_text').text(status);
    $('#verifyForm')[0].reset();
    $('#verifyModal').modal('show');
}

function previewDocument(documentId) {
    $('#previewFrame').attr('src', '/travel/document/' + documentId + '/preview');
    $('#previewModal').modal('show');
}

function downloadDocument(documentId) {
    window.location.href = '/travel/document/' + documentId + '/download';
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.border-left-primary { border-left: 4px solid #007bff; }
.border-left-success { border-left: 4px solid #28a745; }
.border-left-info { border-left: 4px solid #17a2b8; }
.border-left-warning { border-left: 4px solid #ffc107; }
.border-left-danger { border-left: 4px solid #dc3545; }
.border-left-secondary { border-left: 4px solid #6c757d; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\document\index.blade.php ENDPATH**/ ?>