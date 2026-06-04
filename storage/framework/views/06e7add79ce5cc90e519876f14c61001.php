<!-- Print Modal -->
<div class="modal fade" id="modal-print-kontrabon" tabindex="-1" role="dialog" aria-labelledby="modal-print-kontrabon-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-green-600 text-white">
                <h5 class="modal-title" id="modal-print-kontrabon-label">
                    <i class="bx bx-printer mr-2"></i>
                    Print Kontra Bon
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status" id="print-loading">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted" id="print-status">Memuat dokumen...</p>
                </div>
                
                <!-- PDF Viewer Container -->
                <div id="pdf-container" class="d-none">
                    <div class="embed-responsive embed-responsive-4by3">
                        <iframe id="pdf-viewer" class="embed-responsive-item border rounded" src="" allowfullscreen></iframe>
                    </div>
                </div>
                
                <!-- Error Container -->
                <div id="pdf-error" class="d-none text-center">
                    <div class="alert alert-danger">
                        <i class="bx bx-error-circle bx-lg mb-2"></i><br>
                        <strong>Gagal memuat dokumen PDF</strong><br>
                        <span id="error-message">Terjadi kesalahan saat memuat dokumen</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bx bx-x"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" id="btn-download" onclick="downloadPDF()" disabled>
                    <i class="bx bx-download"></i> Download PDF
                </button>
                <button type="button" class="btn btn-success" id="btn-print" onclick="printPDF()" disabled>
                    <i class="bx bx-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPrintUrl = '';

function showPrintModal(kontraBonId) {
    // Close detail modal if open
    $('#modal-detail').modal('hide');
    
    // Show print modal
    $('#modal-print-kontrabon').modal('show');
    
    // Reset modal state
    resetPrintModal();
    
    // Load PDF
    loadKontraBonPDF(kontraBonId);
}

function resetPrintModal() {
    $('#print-loading').removeClass('d-none');
    $('#print-status').text('Memuat dokumen...');
    $('#pdf-container').addClass('d-none');
    $('#pdf-error').addClass('d-none');
    $('#btn-download, #btn-print').prop('disabled', true);
    $('#pdf-viewer').attr('src', '');
    currentPrintUrl = '';
}

function loadKontraBonPDF(kontraBonId) {
    const pdfUrl = `<?php echo e(route('admin.penjualan.kontrabon.print', ':id')); ?>`.replace(':id', kontraBonId);
    currentPrintUrl = pdfUrl;
    
    // Update status
    $('#print-status').text('Memuat PDF...');
    
    // Create a new iframe to test if PDF loads
    const testFrame = document.createElement('iframe');
    testFrame.style.display = 'none';
    testFrame.onload = function() {
        // PDF loaded successfully
        setTimeout(() => {
            $('#print-loading').addClass('d-none');
            $('#pdf-container').removeClass('d-none');
            $('#pdf-viewer').attr('src', pdfUrl);
            $('#btn-download, #btn-print').prop('disabled', false);
        }, 500);
        
        // Remove test frame
        document.body.removeChild(testFrame);
    };
    
    testFrame.onerror = function() {
        // PDF failed to load
        showPDFError('Gagal memuat dokumen PDF');
        document.body.removeChild(testFrame);
    };
    
    // Add test frame to body and set source
    document.body.appendChild(testFrame);
    testFrame.src = pdfUrl;
    
    // Fallback timeout
    setTimeout(() => {
        if ($('#pdf-container').hasClass('d-none')) {
            // Still loading after 10 seconds, assume success
            $('#print-loading').addClass('d-none');
            $('#pdf-container').removeClass('d-none');
            $('#pdf-viewer').attr('src', pdfUrl);
            $('#btn-download, #btn-print').prop('disabled', false);
        }
    }, 10000);
}

function showPDFError(message) {
    $('#print-loading').addClass('d-none');
    $('#pdf-container').addClass('d-none');
    $('#pdf-error').removeClass('d-none');
    $('#error-message').text(message);
    $('#btn-download, #btn-print').prop('disabled', true);
}

function downloadPDF() {
    if (currentPrintUrl) {
        // Create temporary link to download
        const link = document.createElement('a');
        link.href = currentPrintUrl;
        link.download = 'kontra-bon.pdf';
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function printPDF() {
    if (currentPrintUrl) {
        // Open PDF in new window for printing
        const printWindow = window.open(currentPrintUrl, '_blank');
        if (printWindow) {
            printWindow.onload = function() {
                printWindow.print();
            };
        } else {
            alert('Popup diblokir. Silakan izinkan popup untuk mencetak dokumen.');
        }
    }
}

// Handle modal events
$('#modal-print-kontrabon').on('hidden.bs.modal', function () {
    resetPrintModal();
});
</script><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\penjualan\kontrabon\modals\print.blade.php ENDPATH**/ ?>