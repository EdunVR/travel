<style>
    /* Alarm Glow Effect */
@keyframes alarm-glow {
    0% { 
        transform: scale(1);
        text-shadow: 0 0 5px #ff6b6b;
    }
    50% { 
        transform: scale(1.1);
        text-shadow: 0 0 20px #ff6b6b, 0 0 30px #ff6b6b;
    }
    100% { 
        transform: scale(1);
        text-shadow: 0 0 5px #ff6b6b;
    }
}

.alarm-glow {
    animation: alarm-glow 1s ease-in-out infinite;
}

.modal-alarm-showing {
    overflow: hidden;
}

/* Modal Alarm Styling */
#modal-alarm .modal-content {
    border: 3px solid #ff6b6b;
    border-radius: 10px;
}

#modal-alarm .modal-header {
    border-bottom: 2px solid #ff6b6b;
}

#alarm-icon {
    animation: alarm-glow 0.8s ease-in-out infinite;
}

/* Highlight untuk invoice yang due tomorrow */
tr.due-tomorrow {
    background-color: #fff9e6 !important;
    border-left: 4px solid #f39c12 !important;
}

tr.overdue {
    background-color: #ffe6e6 !important;
    border-left: 4px solid #e74c3c !important;
}

/* Badge untuk status urgent */
.badge-urgent {
    background-color: #e74c3c;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>

@extends('app')

@section('title')
    History Invoice Penjualan
@endsection

@section('breadcrumb')
    @parent
    <li class="active">History Invoice Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <a href="{{ route('sales.invoice.index') }}" class="btn btn-success btn-sm" style="margin-right: 10px;">
                    <i class="fa fa-plus"></i> Buat Invoice Baru
                </a>
                <a href="{{ route('sales.invoice.setting') }}" class="btn btn-warning btn-sm" style="margin-right: 10px;">
                    <i class="fa fa-cog"></i> Setting Nomor Invoice
                </a>
                <div class="box-tools">
                    
                    <form id="filter-form" method="GET" style="display: inline-block; margin-right: 10px;">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="date" class="form-control" name="start_date" value="{{ $start_date }}" placeholder="Dari Tanggal">
                            <span class="input-group-addon">s/d</span>
                            <input type="date" class="form-control" name="end_date" value="{{ $end_date }}" placeholder="Sampai Tanggal">
                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                <button type="button" class="btn btn-default" onclick="resetFilter()"><i class="fa fa-refresh"></i></button>
                                <button type="button" class="btn btn-success" onclick="exportPdf()"><i class="fa fa-file-pdf-o"></i> Export</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-tabs">
                    <li class="{{ $status == 'terkini' ? 'active' : '' }}">
                        <a href="{{ route('sales.invoice.history', ['status' => 'terkini', 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                            <i class="fa fa-list"></i> Terkini
                        </a>
                    </li>
                    <li class="{{ $status == 'menunggu' ? 'active' : '' }}">
                        <a href="{{ route('sales.invoice.history', ['status' => 'menunggu', 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                            <i class="fa fa-clock-o"></i> Menunggu Pembayaran
                            <span class="label label-warning" id="count-menunggu">0</span>
                        </a>
                    </li>
                    <li class="{{ $status == 'lunas' ? 'active' : '' }}">
                        <a href="{{ route('sales.invoice.history', ['status' => 'lunas', 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                            <i class="fa fa-check-circle"></i> Lunas
                            <span class="label label-success" id="count-lunas">0</span>
                        </a>
                    </li>
                    <li class="{{ $status == 'gagal' ? 'active' : '' }}">
                        <a href="{{ route('sales.invoice.history', ['status' => 'gagal', 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                            <i class="fa fa-times-circle"></i> Retur/gagal
                            <span class="label label-danger" id="count-gagal">0</span>
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div class="table-responsive" style="margin-top: 10px;">
                            <table class="table table-stiped table-bordered" id="table-invoice-history">
                                <thead>
                                    <th width="5%">No</th>
                                    <th>No Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    @if($status != 'lunas')
                                    <th>Jatuh Tempo</th>
                                    <th>Sisa Hari</th>
                                    @else
                                    <th>Tanggal & Jenis Pembayaran</th>
                                    <th>Penerima & Catatan</th>
                                    @endif
                                    <th>Items</th>
                                    @if($status != 'lunas')
                                    <th>Petugas</th>
                                    @endif
                                    <th width="15%"><i class="fa fa-cog"></i></th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Update Status -->
<div class="modal fade" id="modal-status" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Update Status Invoice</h4>
            </div>
            <div class="modal-body">
                <form id="form-status">
                    @csrf
                    <input type="hidden" name="id_sales_invoice" id="id_sales_invoice">
                    <input type="hidden" name="status" id="status">
                    
                    <div id="form-pembayaran" style="display: none;">
                        <div class="form-group">
                            <label for="jenis_pembayaran">Jenis Pembayaran <span class="text-danger">*</span></label>
                            <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-control">
                                <option value="">Pilih Jenis Pembayaran</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                            <small class="text-muted">Wajib diisi untuk status LUNAS</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="penerima">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="penerima" id="penerima" class="form-control" placeholder="Nama penerima pembayaran">
                            <small class="text-muted">Wajib diisi untuk status LUNAS</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="tanggal_pembayaran">Tanggal Pembayaran <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tanggal_pembayaran" id="tanggal_pembayaran" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                            <small class="text-muted">Wajib diisi untuk status LUNAS</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="catatan_pembayaran">Catatan Pembayaran (Opsional)</label>
                            <textarea name="catatan_pembayaran" id="catatan_pembayaran" class="form-control" rows="2" placeholder="Tambahkan catatan pembayaran..."></textarea>
                        </div>
                    </div>
                    
                    <div id="form-catatan" style="display: none;">
                        <div class="form-group">
                            <label for="catatan">Alasan / Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Berikan alasan atau catatan..."></textarea>
                            <small class="text-muted">Untuk status GAGAL, disarankan memberikan alasan</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Info:</strong> Status invoice akan diubah menjadi: <span id="status-label"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-update-status">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alarm untuk Invoice Jatuh Tempo -->
<div class="modal fade" id="modal-alarm" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ff6b6b; color: white;">
                <h4 class="modal-title">
                    <i class="fa fa-exclamation-triangle"></i> PERINGATAN - INVOICE JATUH TEMPO!
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" style="text-align: center;">
                    <i class="fa fa-bell fa-3x" id="alarm-icon" style="color: #ff6b6b;"></i>
                    <h3 style="color: #d63031; margin-top: 15px;" id="alarm-main-title">INVOICE MEMBUTUHKAN PERHATIAN SEGERA!</h3>
                    <p style="font-size: 16px; margin-bottom: 20px;" id="alarm-subtitle">
                        Segera lakukan penagihan kepada customer berikut:
                    </p>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr style="background-color: #ffeaa7;">
                                <th width="5%">#</th>
                                <th>No Invoice</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Sisa Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="alarm-invoices-list">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> 
                    <strong>Informasi:</strong> Invoice ini akan jatuh tempo besok. Segera hubungi customer untuk konfirmasi pembayaran.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="snooze-alarm">
                    <i class="fa fa-clock-o"></i> Tunda 5 Jam
                </button>
                <button type="button" class="btn btn-success" id="close-alarm">
                    <i class="fa fa-check"></i> Mengerti, Akan Ditindaklanjuti
                </button>
            </div>
        </div>
    </div>
</div>

<audio id="alarm-sound" loop muted>
    <source src="{{ asset('sounds/beep-beep.mp3') }}" type="audio/mpeg">
    <source src="{{ asset('sounds/beep-beep.wav') }}" type="audio/wav">
</audio>
@endsection

@push('scripts')
<script>
    let table;
    const currentStatus = '{{ $status }}';
    const baseUrl = window.baseUrl;
    let alarmInterval;
    let alarmSnoozed = false;

    $(function () {
        table = $('#table-invoice-history').DataTable({
            processing: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('sales.invoice.history') }}',
                data: function (d) {
                    d.status = currentStatus;
                    d.start_date = '{{ $start_date }}';
                    d.end_date = '{{ $end_date }}';
                }
            },
            columns: currentStatus === 'lunas' ? [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'no_invoice'},
                {data: 'tanggal'},
                {data: 'member.nama'},
                {data: 'total_formatted', searchable: false},
                {data: 'status_badge', searchable: false, sortable: false},
                { 
                    data: null,
                    render: function(data, type, row) {
                        const tanggal = row.tanggal_pembayaran_formatted;
                        const jenis = row.jenis_pembayaran_badge;
                        return tanggal + '<br>' + jenis;
                    },
                    searchable: false,
                    sortable: false
                },
                {data: 'pembayaran_info', searchable: false, sortable: false},
                {data: 'items_list', searchable: false, sortable: false},
                {data: 'aksi', searchable: false, sortable: false},
            ] : [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'no_invoice'},
                {data: 'tanggal'},
                {data: 'member.nama'},
                {data: 'total_formatted', searchable: false},
                {data: 'status_badge', searchable: false, sortable: false},
                {data: 'due_date_formatted', searchable: false},
                {data: 'sisa_hari', searchable: false, sortable: false},
                {data: 'items_list', searchable: false, sortable: false},
                {data: 'user.name'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            order: [],
            createdRow: function (row, data, dataIndex) {
                if (data.due_date) {
                    const dueDate = new Date(data.due_date);
                    const today = new Date();
                    if (dueDate < today) {
                        $(row).addClass('overdue');
                    } else if (dueDate.getDate() === today.getDate() + 1) {
                        $(row).addClass('due-tomorrow');
                    }
                }
            }
        });

        loadStatusCounts();
        checkDueInvoices();
    });

    function loadStatusCounts() {
        $.get(`{{ route('sales.invoice.status-counts') }}`, function(data) {
            $('#count-menunggu').text(data.menunggu || 0);
            $('#count-lunas').text(data.lunas || 0);
            $('#count-gagal').text(data.gagal || 0);
        }).fail(function() {
            $('#count-menunggu').text('0');
            $('#count-lunas').text('0');
            $('#count-gagal').text('0');
        });
    }

    function updateStatus(id, status) {
        const statusLabel = status === 'lunas' ? 'LUNAS' : 'RETUR/GAGAL';
        
        $('#id_sales_invoice').val(id);
        $('#status').val(status);
        $('#status-label').text(statusLabel);
        
        // Reset form
        $('#jenis_pembayaran').val('').removeAttr('required');
        $('#penerima').val('').removeAttr('required');
        $('#tanggal_pembayaran').val('').removeAttr('required');
        $('#catatan_pembayaran').val('');
        $('#catatan').val('');
        
        // Tampilkan form sesuai status
        if (status === 'lunas') {
            $('#form-pembayaran').show();
            $('#form-catatan').hide();
            $('#jenis_pembayaran').attr('required', 'required');
            $('#penerima').attr('required', 'required');
            $('#tanggal_pembayaran').attr('required', 'required');
        } else {
            $('#form-pembayaran').hide();
            $('#form-catatan').show();
            $('#jenis_pembayaran').removeAttr('required');
            $('#penerima').removeAttr('required');
            $('#tanggal_pembayaran').removeAttr('required');
        }
        
        $('#modal-status').modal('show');
    }

    $('#btn-update-status').click(function() {
        const formData = new FormData(document.getElementById('form-status'));
        const status = $('#status').val();
        const invoiceId = $('#id_sales_invoice').val();

        if (status === 'gagal') {
            formData.delete('jenis_pembayaran');
            formData.delete('penerima');
            formData.delete('tanggal_pembayaran');
            formData.delete('catatan_pembayaran');
        }
        
        $.ajax({
            url: `{{ url('sales-management/invoice') }}/${invoiceId}/status`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#modal-status').modal('hide');
                    alert(response.message);
                    table.ajax.reload();
                    loadStatusCounts();
                }
            },
            error: function(error) {
                alert('Terjadi kesalahan: ' + error.responseJSON.message);
            }
        });
    });

    function resetFilter() {
        window.location.href = '{{ route('sales.invoice.history', ['status' => $status]) }}';
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                    loadStatusCounts();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
    
    function cetakInvoice(event, url) {
        event.preventDefault();
        const printWindow = window.open(url, '_blank');
        
        let printAttempts = 0;
        const tryPrint = function() {
            if (printAttempts++ > 10) return;
            
            try {
                printWindow.print();
            } catch (e) {
                setTimeout(tryPrint, 500);
            }
        };
        
        printWindow.onload = function() {
            setTimeout(tryPrint, 1000);
        };
        
        setTimeout(tryPrint, 2000);
    }

    function exportPdf() {
        const startDate = $('input[name="start_date"]').val();
        const endDate = $('input[name="end_date"]').val();
        const status = '{{ $status }}';
        
        const url = `{{ route('sales.invoice.export-pdf') }}?status=${status}&start_date=${startDate}&end_date=${endDate}`;
        window.open(url, '_blank');
    }

    function checkDueInvoices() {
        alarmSnoozed = getSnoozeStatus();
        
        if (alarmSnoozed) {
            const snoozeData = JSON.parse(localStorage.getItem('alarmSnoozed'));
            const remainingTime = Math.ceil((snoozeData.expires - Date.now()) / 60000); // dalam menit
            console.log('Alarm sedang di snooze, sisa waktu:', remainingTime, 'menit');
            return;
        }
        
        // Cek jika modal sudah terbuka, jangan check lagi
        if ($('#modal-alarm').hasClass('show')) {
            console.log('Modal alarm sudah terbuka, skip check');
            return;
        }
        
        console.log('Checking due soon invoices...');
        
        $.get(`{{ route('sales.invoice.due-soon') }}`, function(response) {
            console.log('Due soon invoices response:', response);
            
            if (response.success && response.invoices && response.invoices.length > 0) {
                // Filter hanya invoice dengan status 'menunggu'
                const urgentInvoices = response.invoices.filter(invoice => 
                    invoice.status === 'menunggu' && invoice.remaining_hours <= 24
                );
                
                console.log('Found', urgentInvoices.length, 'urgent invoices with status menunggu');
                
                if (urgentInvoices.length > 0) {
                    showAlarmModal(urgentInvoices);
                } else {
                    console.log('No urgent invoices found with status menunggu');
                }
            } else {
                console.log('No due soon invoices found');
            }
        }).fail(function(error) {
            console.error('Error checking due soon invoices:', error);
        });
    }

    // Function untuk menampilkan modal alarm
    function showAlarmModal(invoices) {
        // Cek jika modal sudah terbuka
        if ($('#modal-alarm').hasClass('show')) {
            console.log('Modal already showing, skipping');
            return;
        }
        
        console.log('Showing alarm modal for', invoices.length, 'urgent invoices');
        
        // Isi data invoice ke tabel
        const tbody = $('#alarm-invoices-list');
        tbody.empty();
        
        if (invoices.length === 0) {
            console.log('No urgent invoices to show in alarm');
            return;
        }
        
        // Urutkan berdasarkan remaining_hours (yang paling urgent di atas)
        invoices.sort((a, b) => a.remaining_hours - b.remaining_hours);
        
        invoices.forEach((invoice, index) => {
            // Format total number
            const totalFormatted = new Intl.NumberFormat('id-ID').format(invoice.total);
            
            // Tentukan badge berdasarkan remaining_hours
            let badgeClass = 'label-warning';
            let badgeText = invoice.time_description || 'Sisa 1 hari';
            
            if (invoice.remaining_hours < 0) {
                badgeClass = 'label-danger';
                badgeText = invoice.time_description || 'Terlambat';
            } else if (invoice.remaining_hours <= 1) {
                badgeClass = 'label-danger';
                badgeText = invoice.time_description || 'Sisa 1 jam';
            } else if (invoice.remaining_hours <= 24) {
                badgeClass = 'label-warning';
                badgeText = invoice.time_description || 'Sisa 24 jam';
            }
            
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${invoice.no_invoice}</strong></td>
                    <td>${invoice.member.nama}</td>
                    <td>Rp ${totalFormatted}</td>
                    <td><span class="label ${badgeClass}">${badgeText}</span></td>
                </tr>
            `;
            tbody.append(row);
        });
        
        // Update modal title berdasarkan kondisi
        updateModalTitle(invoices);
        
        // Play alarm sound
        playAlarmSound();
        
        // Show modal dengan options yang tepat
        $('#modal-alarm').modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
        
        // Start glow animation
        startAlarmAnimation();
        
        console.log('Alarm modal shown successfully');
    }

    // Function untuk update modal title berdasarkan kondisi invoices
    function updateModalTitle(invoices) {
        const hasOverdue = invoices.some(inv => inv.remaining_hours < 0);
        const hasDueWithin1Hour = invoices.some(inv => inv.remaining_hours >= 0 && inv.remaining_hours <= 1);
        const hasDueWithin24Hours = invoices.some(inv => inv.remaining_hours > 1 && inv.remaining_hours <= 24);
        
        let mainTitle = '';
        let subtitle = 'Segera lakukan penagihan kepada customer berikut:';
        let icon = 'fa-exclamation-triangle';
        
        if (hasOverdue) {
            mainTitle = 'PERINGATAN - INVOICE SUDAH TERLAMBAT!';
            subtitle = 'INVOICE SUDAH MELEWATI TANGGAL JATUH TEMPO! Segera tindaklanjuti:';
            icon = 'fa-exclamation-circle';
        } else if (hasDueWithin1Hour) {
            mainTitle = 'DARURAT - INVOICE JATUH TEMPO KURANG DARI 1 JAM!';
            subtitle = 'Invoice akan jatuh tempo dalam waktu kurang dari 1 jam! Segera konfirmasi:';
            icon = 'fa-bell';
        } else if (hasDueWithin24Hours) {
            mainTitle = 'PERINGATAN - INVOICE JATUH TEMPO DALAM 24 JAM!';
            subtitle = 'Invoice akan jatuh tempo dalam 24 jam ke depan! Segera lakukan penagihan:';
            icon = 'fa-bell';
        }
        
        $('.modal-title').html(`<i class="fa ${icon}"></i> ${mainTitle}`);
        $('#alarm-main-title').text(mainTitle);
        $('#alarm-subtitle').text(subtitle);
        
        // Update background color berdasarkan urgency
        const header = $('.modal-header');
        if (hasOverdue || hasDueWithin1Hour) {
            header.css('background-color', '#e74c3c'); // Merah untuk darurat
        } else {
            header.css('background-color', '#ff6b6b'); // Merah muda untuk warning
        }
    }

    function playAlarmSound() {
        try {
            // Coba gunakan Web Audio API untuk sound yang lebih reliable
            playBeepSound();
            
            // Juga coba play audio element sebagai backup
            const alarmSound = document.getElementById('alarm-sound');
            if (alarmSound) {
                alarmSound.currentTime = 0;
                alarmSound.play().catch(e => {
                    console.log('Audio element play failed:', e);
                });
            }
        } catch (e) {
            console.log('Alarm sound error:', e);
        }
    }

    // Beep sound menggunakan Web Audio API (lebih reliable)
    function playBeepSound() {
        try {
            // Cek jika browser support Web Audio API
            if (!window.AudioContext && !window.webkitAudioContext) {
                console.log('Web Audio API not supported');
                return;
            }
            
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            const audioContext = new AudioContext();
            
            // Create beep-beep pattern
            createBeep(audioContext, 800, 0.3, 0.1);
            setTimeout(() => {
                createBeep(audioContext, 800, 0.3, 0.1);
            }, 200);
            setTimeout(() => {
                createBeep(audioContext, 600, 0.4, 0.15);
            }, 500);
            
        } catch (e) {
            console.log('Web Audio API error:', e);
        }
    }

    // Helper function untuk create single beep
    function createBeep(audioContext, frequency, volume, duration) {
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = frequency;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0, audioContext.currentTime);
        gainNode.gain.linearRampToValueAtTime(volume, audioContext.currentTime + 0.01);
        gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + duration);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + duration);
    }

    // Function untuk memulai animasi alarm
    function startAlarmAnimation() {
        const icon = $('#alarm-icon');
        icon.addClass('alarm-glow');
    }

    // Function untuk menghentikan alarm
    function stopAlarm() {
        console.log('Stopping alarm...');
        
        // Stop audio element
        const alarmSound = document.getElementById('alarm-sound');
        if (alarmSound) {
            alarmSound.pause();
            alarmSound.currentTime = 0;
        }
        
        // Stop animations
        $('#alarm-icon').removeClass('alarm-glow');
        
        // Hide modal
        $('#modal-alarm').modal('hide');
        
        console.log('Alarm stopped');
    }

    // Function untuk refresh data di modal yang sedang terbuka
    function refreshAlarmModal() {
        if (!$('#modal-alarm').hasClass('show')) {
            return; // Modal tidak terbuka, tidak perlu refresh
        }
        
        console.log('Refreshing alarm modal data...');
        
        $.get(`{{ route('sales.invoice.due-soon') }}`, function(response) {
            if (response.success && response.invoices && response.invoices.length > 0) {
                const urgentInvoices = response.invoices.filter(invoice => 
                    invoice.remaining_hours <= 24
                );
                
                if (urgentInvoices.length > 0) {
                    // Update tabel tanpa menutup modal
                    updateAlarmTable(urgentInvoices);
                } else {
                    // Jika tidak ada urgent invoices lagi, tutup modal
                    console.log('No more urgent invoices, closing modal');
                    stopAlarm();
                }
            }
        });
    }

    // Function untuk update tabel tanpa menutup modal
    function updateAlarmTable(invoices) {
        const tbody = $('#alarm-invoices-list');
        tbody.empty();
        
        invoices.sort((a, b) => a.remaining_hours - b.remaining_hours);
        
        invoices.forEach((invoice, index) => {
            const totalFormatted = new Intl.NumberFormat('id-ID').format(invoice.total);
            
            let badgeClass = 'label-warning';
            let badgeText = 'Sisa 1 hari';
            
            if (invoice.remaining_hours === 0) {
                badgeClass = 'label-danger';
                badgeText = 'Jatuh tempo hari ini!';
            } else if (invoice.remaining_hours < 0) {
                badgeClass = 'label-danger';
                badgeText = `Terlambat ${Math.abs(invoice.remaining_hours)} jam`;
            }
            
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${invoice.no_invoice}</strong></td>
                    <td>${invoice.member.nama}</td>
                    <td>Rp ${totalFormatted}</td>
                    <td><span class="label ${badgeClass}">${badgeText}</span></td>
                </tr>
            `;
            tbody.append(row);
        });
        
        updateModalTitle(invoices);
    }

    function getSnoozeStatus() {
        const snoozeData = localStorage.getItem('alarmSnoozed');
        if (snoozeData) {
            const snooze = JSON.parse(snoozeData);
            // Cek jika snooze masih valid (belum expired)
            if (snooze.expires > Date.now()) {
                return true;
            } else {
                localStorage.removeItem('alarmSnoozed');
                return false;
            }
        }
        return false;
    }

    function setSnoozeStatus(minutes = 300) {
        const snoozeData = {
            snoozed: true,
            expires: Date.now() + (minutes * 60 * 1000), // 5 jam default
            snoozedAt: new Date().toISOString()
        };
        localStorage.setItem('alarmSnoozed', JSON.stringify(snoozeData));
        alarmSnoozed = true;
    }

    // Function untuk clear snooze status
    function clearSnoozeStatus() {
        localStorage.removeItem('alarmSnoozed');
        alarmSnoozed = false;
    }

    // Update interval check untuk juga refresh modal yang terbuka
    setInterval(() => {
        checkDueInvoices();
        refreshAlarmModal();
    }, 3600000); // 1 menit

    // Event handlers untuk tombol modal
    $(document).ready(function() {
        alarmSnoozed = getSnoozeStatus();
        if (alarmSnoozed) {
            const snoozeData = JSON.parse(localStorage.getItem('alarmSnoozed'));
            const remainingTime = Math.ceil((snoozeData.expires - Date.now()) / 60000);
            console.log('Alarm initialized as snoozed, remaining:', remainingTime, 'minutes');
        }
        
        // Check alarm 3 detik setelah page load, lalu setiap 1 menit
        setTimeout(() => {
            checkDueInvoices();
        }, 1000);
        
        alarmInterval = setInterval(checkDueInvoices, 3600000); // 1 jam
        
        // Tombol close alarm
        $('#close-alarm').click(function() {
            stopAlarm();
            clearSnoozeStatus(); // Clear snooze status ketika user close manual
            console.log('Alarm closed by user, snooze cleared');
        });
        
        // Tombol snooze alarm
        $('#snooze-alarm').click(function() {
            stopAlarm();
            setSnoozeStatus(300); // Snooze selama 5 jam
            
            const snoozeData = JSON.parse(localStorage.getItem('alarmSnoozed'));
            const snoozedUntil = new Date(snoozeData.expires).toLocaleTimeString();
            
            alert('Alarm akan aktif kembali pada ' + snoozedUntil + ' (5 jam dari sekarang)');
            console.log('Alarm snoozed until:', snoozedUntil);
        });
        
        // Handle modal hidden event
        $('#modal-alarm').on('hidden.bs.modal', function() {
            stopAlarm();
        });
        
        // Handle page/tab change events
        $(window).on('beforeunload', function() {
            // Save snooze status sebelum page unload
            if (alarmSnoozed) {
                console.log('Saving snooze status before page unload');
            }
        });
        
        // Handle visibility change (tab switching)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Tab menjadi active, check snooze status
                alarmSnoozed = getSnoozeStatus();
                if (!alarmSnoozed) {
                    checkDueInvoices();
                }
            }
        });
        
        console.log('Alarm system initialized with localStorage support');
    });

</script>
@endpush
