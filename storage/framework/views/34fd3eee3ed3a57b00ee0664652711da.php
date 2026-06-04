

<?php $__env->startSection('title'); ?>
    Tambah Kontra Bon
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('breadcrumb'); ?>
    <li class="active">Tambah Kontra Bon</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <form id="form-kontra-bon" action="<?php echo e(route('kontra_bon.store')); ?>" method="POST">

                    <input type="hidden" name="start_date_filter" id="start_date_filter">
                    <input type="hidden" name="end_date_filter" id="end_date_filter">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <!-- Kolom Kiri: Form Input -->
                        <div class="col-md-6">
                            <?php if($outlets->count() > 1): ?>
                            <div class="form-group">
                                <label for="id_outlet">Outlet</label>
                                <select name="id_outlet" id="id_outlet" class="form-control" required>
                                    <option value="">Pilih Outlet</option>
                                    <?php $__currentLoopData = $outlets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outlet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($outlet->id_outlet); ?>"><?php echo e($outlet->nama_outlet); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php else: ?>
                                <input type="hidden" name="id_outlet" id="id_outlet" value="<?php echo e(auth()->user()->akses_outlet[0]); ?>">
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="id_member">Customer</label>
                                <select name="id_member" id="id_member" class="form-control" required>
                                    <option value="">Pilih Customer</option>
                                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($member->id_member); ?>" data-saldo="<?php echo e($member->saldo); ?>"><?php echo e($member->nama); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="saldo_member">Saldo Member</label>
                                <input type="text" name="saldo_member" id="saldo_member" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_jatuh_tempo">Tanggal Jatuh Tempo</label>
                                <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" class="form-control" required>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Tabel Piutang -->
                        <div class="col-md-6">
                            <!-- TAMBAHKAN INPUT RANGE TANGGAL DI SINI -->
                            <div class="form-group">
                                <label for="range_tanggal">Range Tanggal Hutang</label>
                                <div class="input-group">
                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                    <span class="input-group-addon">s/d</span>
                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="pembayaran">Pembayaran</label>
                                <input type="number" name="pembayaran" id="pembayaran" class="form-control" value="0" required>
                                <button type="button" class="btn btn-primary mt-2" onclick="autoPilihHutang()">Auto Pilih Hutang</button>
                            </div>
                            <div class="form-group">
                                <label for="masuk_saldo">Masuk Saldo</label>
                                <input type="text" name="masuk_saldo" id="masuk_saldo" class="form-control" value="0" readonly>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="tambahkan_saldo" id="tambahkan_saldo"> Tambahkan Saldo ke Pembayaran
                                </label>
                                <input type="hidden" name="tambahkan_saldo_value" id="tambahkan_saldo_value" value="0">
                            </div>
                            <button type="submit" class="btn btn-primary btn-flat">Buat Kontra Bon</button>
                        </div>
                    </div>

                    <!-- Tabel Piutang -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <table class="table table-stiped table-bordered table-piutang">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Tanggal</th>
                                        <th>TrxID</th>
                                        <th>Nominal</th>
                                        <th width="5%"><input type="checkbox" id="check-all"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data piutang akan diisi secara dinamis -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    window.baseUrl = <?php echo json_encode(url('/'), 15, 512) ?>;
</script>
<script>
    const baseUrl = window.baseUrl;
    $(function () {
        // Variabel global untuk menyimpan data piutang
        let piutangData = [];

        // Fungsi untuk mengaktifkan/menonaktifkan checkbox semua
        $('#check-all').on('click', function () {
            $('input[name="piutang_ids[]"]').prop('checked', this.checked);
        });

        // Fungsi untuk auto pilih hutang berdasarkan FIFO dengan filter tanggal
        function autoPilihHutang() {
            let pembayaran = parseFloat($('#pembayaran').val());
            let totalDipilih = 0;
            let startDate = $('#start_date').val() ? new Date($('#start_date').val()) : null;
            let endDate = $('#end_date').val() ? new Date($('#end_date').val()) : null;

            // Reset semua checkbox
            $('input[name="piutang_ids[]"]').prop('checked', false);

            // Loop melalui setiap piutang
            $('input[name="piutang_ids[]"]').each(function () {
                if (totalDipilih < pembayaran) {
                    let row = $(this).closest('tr');
                    let nominal = parseFloat(row.find('td:eq(3)').text().replace(/[^0-9]/g, ''));
                    let tanggalText = row.find('td:eq(1)').text();
                    let tanggal = new Date(tanggalText.split('-').reverse().join('-')); // Format dd-mm-yyyy to yyyy-mm-dd
                    
                    // Filter berdasarkan range tanggal jika ada
                    let dalamRange = true;
                    if (startDate && endDate) {
                        dalamRange = tanggal >= startDate && tanggal <= endDate;
                    }

                    if (dalamRange && totalDipilih + nominal <= pembayaran) {
                        $(this).prop('checked', true);
                        totalDipilih += nominal;
                    }
                }
            });

            // Hitung sisa pembayaran yang masuk ke saldo
            $('#masuk_saldo').val(pembayaran - totalDipilih);
        }

        // Panggil fungsi autoPilihHutang saat tombol ditekan
        window.autoPilihHutang = autoPilihHutang;

        $('#tambahkan_saldo').on('change', function() {
            if (this.checked) {
                $('#tambahkan_saldo_value').val('1');
                console.log($('#tambahkan_saldo_value').val());
            } else {
                $('#tambahkan_saldo_value').val('0');
                console.log($('#tambahkan_saldo_value').val());
            }
        });

        $('#id_outlet').on('change', function () {
            var id_outlet = $(this).val();
            $.ajax({
                url: '<?php echo e(route('kontra_bon.create')); ?>',
                type: 'GET',
                data: {
                    id_outlet: id_outlet
                },
                success: function(response) {
                    // Reload modal member dengan data yang baru
                    $('#id_member').html($(response).find('#id_member').html());
                },
                error: function(xhr, status, error) {
                    console.log("Gagal memuat:", error);
                }
            });
        });

        // Event listener untuk dropdown customer
        $('#id_member').on('change', function() {
            let idMember = $(this).val();
            let saldoMember = $(this).find(':selected').data('saldo');
            $('#saldo_member').val(saldoMember);
            
            if (idMember) {
                fetchPiutang(idMember);
            } else {
                clearPiutangTable();
            }
        });

        $('#tambahkan_saldo').on('change', function() {
            if (this.checked) {
                let saldoMember = parseFloat($('#saldo_member').val());
                let pembayaran = parseFloat($('#pembayaran').val());
                $('#pembayaran').val(pembayaran + saldoMember);
                autoPilihHutang();
            } else {
                let saldoMember = parseFloat($('#saldo_member').val());
                let pembayaran = parseFloat($('#pembayaran').val());
                $('#pembayaran').val(pembayaran - saldoMember);
                autoPilihHutang();
            }
        });

        function fetchPiutang(idMember) {
            $.ajax({
                url: `${baseUrl}/get-piutang/${idMember}`,
                method: 'GET',
                success: function(response) {
                    piutangData = response;
                    updatePiutangTable(response);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        function updatePiutangTable(data) {
            let tableBody = $('.table-piutang tbody');
            tableBody.empty();
            data.forEach((item, index) => {
                let row = `<tr>
                    <td>${index + 1}</td>
                    <td>${item.tanggal}</td>
                    <td>${item.no_transaksi}</td>
                    <td>${item.piutang}</td>
                    <td><input type="checkbox" name="piutang_ids[]" value="${item.id_piutang}" data-penjualan="${item.id_penjualan}"></td>
                </tr>`;
                tableBody.append(row);
            });
        }

        function clearPiutangTable() {
            $('.table-piutang tbody').empty();
        }

        // Event listener untuk range tanggal
        $('#start_date, #end_date').on('change', function() {
            if ($('#id_member').val()) {
                applyDateFilter();
            }
        });

        function applyDateFilter() {
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            
            if (!startDate || !endDate) {
                updatePiutangTable(piutangData);
                return;
            }

            let filteredData = piutangData.filter(item => {
                let itemDate = new Date(item.tanggal.split('-').reverse().join('-'));
                let start = new Date(startDate);
                let end = new Date(endDate);
                end.setHours(23, 59, 59, 999); // Sampai akhir hari
                
                return itemDate >= start && itemDate <= end;
            });

            updatePiutangTable(filteredData);
        }

        $('#form-kontra-bon').on('submit', function() {
            $('#start_date_filter').val($('#start_date').val());
            $('#end_date_filter').val($('#end_date').val());
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\hm\resources\views\kontra_bon\create.blade.php ENDPATH**/ ?>