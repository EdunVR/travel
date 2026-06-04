<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Roomlist - <?php echo e($booking->keberangkatan->keberangkatan_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .room-header {
            background-color: #e0e0e0;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ROOMLIST</h1>
        <h2><?php echo e($booking->keberangkatan->keberangkatan_name); ?></h2>
        <h2><?php echo e($booking->hotel->hotel_name); ?></h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Hotel:</div>
            <div class="info-value"><?php echo e($booking->hotel->hotel_name); ?> (<?php echo e($booking->hotel->star_rating); ?>★)</div>
        </div>
        <div class="info-row">
            <div class="info-label">Location:</div>
            <div class="info-value"><?php echo e($booking->hotel->location); ?>, <?php echo e($booking->hotel->city); ?>, <?php echo e($booking->hotel->country); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Check-in Date:</div>
            <div class="info-value"><?php echo e($booking->check_in_date->format('d F Y')); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Check-out Date:</div>
            <div class="info-value"><?php echo e($booking->check_out_date->format('d F Y')); ?></div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Rooms:</div>
            <div class="info-value"><?php echo e($booking->room_count); ?> rooms</div>
        </div>
        <div class="info-row">
            <div class="info-label">Booking Reference:</div>
            <div class="info-value"><?php echo e($booking->booking_reference ?? '-'); ?></div>
        </div>
    </div>

    <?php if($roomsGrouped->count() > 0): ?>
        <?php $__currentLoopData = $roomsGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roomNumber => $assignments): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="room-header">
                Room <?php echo e($roomNumber); ?> (<?php echo e($assignments->count()); ?> <?php echo e(Str::plural('guest', $assignments->count())); ?>)
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Jamaah Name</th>
                        <th style="width: 15%;">Passport No</th>
                        <th style="width: 10%;">Gender</th>
                        <th style="width: 10%;">Bed</th>
                        <th style="width: 15%;">Room Type</th>
                        <th style="width: 15%;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($assignment->jamaahBooking->jamaah->nama_lengkap ?? '-'); ?></td>
                            <td><?php echo e($assignment->jamaahBooking->jamaah->no_paspor ?? '-'); ?></td>
                            <td><?php echo e($assignment->jamaahBooking->jamaah->jenis_kelamin ?? '-'); ?></td>
                            <td><?php echo e($assignment->bed_number ?? '-'); ?></td>
                            <td><?php echo e($assignment->room_type ?? '-'); ?></td>
                            <td><?php echo e($assignment->notes ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="summary">
            <strong>Summary:</strong><br>
            Total Rooms: <?php echo e($roomsGrouped->count()); ?><br>
            Total Guests: <?php echo e($booking->roomAssignments->count()); ?><br>
            Keberangkatan: <?php echo e($booking->keberangkatan->keberangkatan_name); ?><br>
            Package: <?php echo e($booking->keberangkatan->travelPackage->package_name ?? '-'); ?>

        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #999;">
            <p>No room assignments have been made yet.</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        Generated on: <?php echo e($generatedAt->format('d F Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\travel\hotel-booking\roomlist-pdf.blade.php ENDPATH**/ ?>