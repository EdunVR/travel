<div class="modal fade" id="modal-barcode" tabindex="-1" role="dialog" aria-labelledby="modal-barcode">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Barcode & QR Code Sparepart</h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h4 id="barcode-nama" style="margin-bottom: 5px;"></h4>
                    <h5 id="barcode-kode" style="color: #666; margin-bottom: 20px;"></h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">Barcode</h5>
                                </div>
                                <div class="panel-body">
                                    <div id="barcode-image"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">QR Code</h5>
                                </div>
                                <div class="panel-body">
                                    <div id="qrcode-image"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printBarcodeModal()">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function printBarcodeModal() {
    const kode = $('#barcode-kode').text();
    const nama = $('#barcode-nama').text();
    
    // Buka window baru untuk print
    const printWindow = window.open('', '_blank');
    const barcodeHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Barcode</title>
            <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&display=swap" rel="stylesheet">
            <style>
                body { font-family: Arial; text-align: center; padding: 20px; }
                .barcode { font-family: 'Libre Barcode 128', cursive; font-size: 60px; }
                .qrcode { margin-top: 20px; }
            </style>
        </head>
        <body>
            <h3>${nama}</h3>
            <p>Kode: ${kode}</p>
            <div class="barcode">*${kode}*</div>
            <div class="qrcode">
                <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=${encodeURIComponent(kode + '|' + nama)}" alt="QR Code">
            </div>
            <script>window.print();<\/script>
        </body>
        </html>
    `;
    
    printWindow.document.write(barcodeHTML);
    printWindow.document.close();
}
</script>
