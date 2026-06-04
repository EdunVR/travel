<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Roomlist - <?php echo e($keberangkatan->keberangkatan_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-section table {
            width: 100%;
        }
        .info-section td {
            padding: 3px 5px;
        }
        table.roomlist {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.roomlist th,
        table.roomlist td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        table.roomlist th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .hotel-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .hotel-title {
            background-color: #e8e8e8;
            padding: 8px;
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 10px;
            border-left: 4px solid #333;
        }
        .room-group {
            margin-bottom: 15px;
        }
        .room-header {
            background-color: #f5f5f5;
            padding: 5px 8px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .gender-male {
            color: #1976d2;
        }
        .gender-female {
            color: #c2185b;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>ROOMLIST HOTEL</h2>
        <h3><?php echo e($keberangkatan->keberangkatan_name); ?></h3>
        <p><?php echo e($keberangkatan->travelPackage->package_name); ?></p>
    </div>

    <div class="info-section">
        <table>
            <tr>
                <td width="150"><strong>Kode Keberangkatan:</strong></td>
                <td><?php echo e($keberangkatan->keberangkatan_code); ?></td>
                <td width="150"><strong>Tanggal Keberangkatan:</strong></td>
                <td><?php echo e($keberangkatan->departure_date->format('d F Y')); ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal Kepulangan:</strong></td>
                <td><?php echo e($keberangkatan->return_date->format('d F Y')); ?></td>
                <td><strong>Total Jamaah:</strong></td>
                <td><?php echo e($jamaahBookings->count()); ?> orang</td>
            </tr>
            <tr>
                <td><strong>Dibuat:</strong></td>
                <td colspan="3"><?php echo e(now()->format('d F Y H:i')); ?></td>
            </tr>
        </table>
    </div>

    <?php if($hotelBookings->count() > 0): ?>
        
        <?php $__currentLoopData = $hotelBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotelBooking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="hotel-section">
            <div class="hotel-title">
                <?php echo e($hotelBooking->hotel->hotel_name ?? 'Hotel'); ?>

                <span style="font-size: 9pt; font-weight: normal;">
                    (<?php echo e($hotelBooking->check_in_date ? $hotelBooking->check_in_date->format('d/m/Y') : '-'); ?> - 
                    <?php echo e($hotelBooking->check_out_date ? $hotelBooking->check_out_date->format('d/m/Y') : '-'); ?>)
                </span>
            </div>

            <?php
                $roomAssignments = $hotelBooking->roomAssignments->groupBy('room_number');
            ?>

            <?php $__empty_1 = true; $__currentLoopData = $roomAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roomNumber => $assignments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="room-group">
                <div class="room-header">
                    Kamar <?php echo e($roomNumber); ?> - <?php echo e($assignments->first()->room_type ?? 'Standard'); ?>

                    (<?php echo e($assignments->count()); ?> orang)
                </div>
                <table class="roomlist">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Nama Jamaah</th>
                            <th width="80">Jenis Kelamin</th>
                            <th width="120">No. Telepon</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $jamaah = $assignment->jamaahBooking->jamaah ?? null;
                            ?>
                            <?php if($jamaah): ?>
                            <tr>
                                <td style="text-align: center;"><?php echo e($index + 1); ?></td>
                                <td><?php echo e($jamaah->nama ?? $jamaah->ktp_nama ?? '-'); ?></td>
                                <td style="text-align: center;" class="<?php echo e($jamaah->gender === 'male' ? 'gender-male' : 'gender-female'); ?>">
                                    <?php echo e($jamaah->gender === 'male' ? 'Laki-laki' : ($jamaah->gender === 'female' ? 'Perempuan' : '-')); ?>

                                </td>
                                <td><?php echo e($jamaah->telepon ?? '-'); ?></td>
                                <td style="font-size: 9pt;"><?php echo e($assignment->notes ?? '-'); ?></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p style="text-align: center; color: #666; padding: 20px;">Belum ada penempatan kamar untuk hotel ini.</p>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        
        <?php
            // Group jamaah by hotel booking
            $byHotel = [];
            foreach($jamaahBookings as $booking) {
                foreach($booking->hotelBookings ?? [] as $hb) {
                    $hotelName = $hb->hotel->hotel_name ?? 'Hotel';
                    $cityType = ucfirst($hb->city_type ?? 'hotel');
                    $key = $hotelName . ' (' . $cityType . ')';
                    if (!isset($byHotel[$key])) $byHotel[$key] = ['hotel' => $hb, 'bookings' => []];
                    $byHotel[$key]['bookings'][] = ['booking' => $booking, 'hb' => $hb];
                }
            }
        ?>

        <?php if(count($byHotel) > 0): ?>
            <?php $__currentLoopData = $byHotel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotelKey => $hotelData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="hotel-section">
                <div class="hotel-title">
                    <?php echo e($hotelKey); ?>

                    <span style="font-size: 9pt; font-weight: normal;">
                        (<?php echo e($hotelData['hotel']->check_in_date ? \Carbon\Carbon::parse($hotelData['hotel']->check_in_date)->format('d/m/Y') : '-'); ?> -
                        <?php echo e($hotelData['hotel']->check_out_date ? \Carbon\Carbon::parse($hotelData['hotel']->check_out_date)->format('d/m/Y') : '-'); ?>,
                        <?php echo e($hotelData['hotel']->nights ?? 0); ?> malam)
                    </span>
                </div>
                <table class="roomlist">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Nama Jamaah</th>
                            <th width="80">Tipe Kamar</th>
                            <th width="80">Jenis Kelamin</th>
                            <th width="120">No. Telepon</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $hotelData['bookings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $jamaah = $item['booking']->jamaah; ?>
                        <tr>
                            <td style="text-align:center;"><?php echo e($idx + 1); ?></td>
                            <td><?php echo e($jamaah->nama ?? $jamaah->ktp_nama ?? '-'); ?></td>
                            <td style="text-align:center;"><?php echo e($item['hb']->room_type ? ucfirst($item['hb']->room_type) : '-'); ?></td>
                            <td style="text-align:center;"><?php echo e($jamaah->gender === 'male' ? 'L' : ($jamaah->gender === 'female' ? 'P' : '-')); ?></td>
                            <td><?php echo e($jamaah->telepon ?? '-'); ?></td>
                            <td style="font-size:9pt;"><?php echo e($item['hb']->is_charged ? 'Charge' : 'Include'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
        <div class="hotel-section">
            <div class="hotel-title">Daftar Jamaah (Belum Ada Data Hotel)</div>
            <p style="padding: 10px; background-color: #fff3cd; border: 1px solid #ffc107; margin-bottom: 15px; font-size: 9pt;">
                <strong>Catatan:</strong> Belum ada penempatan hotel. Berikut adalah daftar jamaah dengan preferensi kamar mereka.
            </p>

            <?php
                // Group by gender for better organization
                $maleJamaah = $jamaahBookings->filter(fn($b) => $b->jamaah && $b->jamaah->gender === 'male');
                $femaleJamaah = $jamaahBookings->filter(fn($b) => $b->jamaah && $b->jamaah->gender === 'female');
            ?>

            <?php if($maleJamaah->count() > 0): ?>
            <div class="room-group">
                <div class="room-header">Jamaah Laki-laki (<?php echo e($maleJamaah->count()); ?> orang)</div>
                <table class="roomlist">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Nama Jamaah</th>
                            <th width="100">Preferensi Kamar</th>
                            <th width="120">No. Telepon</th>
                            <th>Permintaan Khusus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $maleJamaah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $jamaah = $booking->jamaah;
                                $roomPref = match($jamaah->room_preference) {
                                    'single' => 'Single',
                                    'double' => 'Double',
                                    'triple' => 'Triple',
                                    'quad' => 'Quad',
                                    default => '-'
                                };
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo e($index + 1); ?></td>
                                <td><?php echo e($jamaah->nama ?? $jamaah->ktp_nama ?? '-'); ?></td>
                                <td style="text-align: center;"><?php echo e($roomPref); ?></td>
                                <td><?php echo e($jamaah->telepon ?? '-'); ?></td>
                                <td style="font-size: 9pt;"><?php echo e($jamaah->special_requests ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if($femaleJamaah->count() > 0): ?>
            <div class="room-group">
                <div class="room-header">Jamaah Perempuan (<?php echo e($femaleJamaah->count()); ?> orang)</div>
                <table class="roomlist">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Nama Jamaah</th>
                            <th width="100">Preferensi Kamar</th>
                            <th width="120">No. Telepon</th>
                            <th>Permintaan Khusus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $femaleJamaah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $jamaah = $booking->jamaah;
                                $roomPref = match($jamaah->room_preference) {
                                    'single' => 'Single',
                                    'double' => 'Double',
                                    'triple' => 'Triple',
                                    'quad' => 'Quad',
                                    default => '-'
                                };
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo e($index + 1); ?></td>
                                <td><?php echo e($jamaah->nama ?? $jamaah->ktp_nama ?? '-'); ?></td>
                                <td style="text-align: center;"><?php echo e($roomPref); ?></td>
                                <td><?php echo e($jamaah->telepon ?? '-'); ?></td>
                                <td style="font-size: 9pt;"><?php echo e($jamaah->special_requests ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if($jamaahBookings->count() === 0): ?>
            <div class="no-data">
                Tidak ada data jamaah untuk keberangkatan ini.
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>_______________________</p>
        <p>Authorized Signature</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\document\roomlist-pdf.blade.php ENDPATH**/ ?>