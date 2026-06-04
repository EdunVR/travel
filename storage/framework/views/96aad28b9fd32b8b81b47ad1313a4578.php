<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Penawaran <?php echo e($preorder->kode_preorder); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 10px;
            line-height: 1.3;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
        }
        .doc-number {
            text-align: center;
            font-size: 12px;
            margin-bottom: 30px;
        }
        .client-info {
            margin-bottom: 30px;
        }
        .client-info table {
            width: 100%;
        }
        .client-info td {
            padding: 3px 0;
            vertical-align: top;
        }
        .greeting {
            margin-bottom: 20px;
            text-align: justify;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .product-image {
            max-width: 80px;
            max-height: 60px;
            object-fit: cover;
        }
        .terms {
            margin: 30px 0;
        }
        .terms h4 {
            margin-bottom: 10px;
            font-size: 12px;
        }
        .terms ul {
            margin: 0;
            padding-left: 20px;
        }
        .terms li {
            margin-bottom: 5px;
        }
        .bank-info {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #000;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 60px;
            margin-bottom: 5px;
        }
        .page-break {
            page-break-before: always;
        }
        .specs-section {
            margin-top: 30px;
        }
        .specs-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .spec-item {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .spec-item h5 {
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        .spec-list {
            margin: 0;
            padding-left: 20px;
        }
        .spec-list li {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">PT. DAHANA REKAYASA NUSANTARA</div>
        <div class="company-address">
            Lingkungan Industri Kecil / UPTD Logam Blok. B2<br>
            Jl. Soekarno Hatta Km. 12.5 - Gedebage, Bandung, Jawa Barat, Indonesia 40296<br>
            Ph : +62 811 2121 511
        </div>
    </div>

    <!-- Title -->
    <div class="title">PENAWARAN</div>
    
    <!-- Document Number -->
    <div class="doc-number">
        No. <?php echo e($preorder->kode_preorder); ?>

    </div>

    <!-- Client Information -->
    <div class="client-info">
        <table>
            <tr>
                <td style="width: 15%;">Klien</td>
                <td style="width: 2%;">:</td>
                <td style="width: 33%;"><?php echo e($preorder->customer->nama ?? '-'); ?></td>
                <td style="width: 15%;">Tgl Penawaran</td>
                <td style="width: 2%;">:</td>
                <td><?php echo e($preorder->tanggal->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <td>Up</td>
                <td>:</td>
                <td><?php echo e($preorder->customer->nama_cp ?? '-'); ?></td>
                <td>Alamat</td>
                <td>:</td>
                <td><?php echo e($preorder->customer->alamat ?? '-'); ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>Telp</td>
                <td>:</td>
                <td><?php echo e($preorder->customer->no_hp ?? '-'); ?></td>
            </tr>
        </table>
    </div>

    <!-- Greeting -->
    <div class="greeting">
        Dengan hormat,<br><br>
        Berikut kami sampaikan harga final produk untuk perusahaan Bapak/Ibu, dengan rincian sebagai berikut :
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 35%;">DESKRIPSI</th>
                <th style="width: 10%;">JUMLAH</th>
                <th style="width: 15%;">HARGA UNIT</th>
                <th style="width: 15%;">NETT</th>
                <th style="width: 20%;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $preorder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($index + 1); ?></td>
                <td>
                    <?php if($item->product_image): ?>
                    <img src="<?php echo e($item->product_image); ?>" alt="<?php echo e($item->deskripsi); ?>" class="product-image"><br>
                    <?php endif; ?>
                    <?php echo e($item->deskripsi); ?>

                    
                    <?php if($item->material_instalasi_biaya > 0 || $item->pemasangan_pelatihan_biaya > 0 || $item->ongkos_kirim_biaya > 0): ?>
                    <br><br><strong>Biaya Tambahan:</strong>
                    
                    <?php if($item->material_instalasi_biaya > 0): ?>
                    <br>• Material Instalasi: Rp <?php echo e(number_format($item->material_instalasi_biaya, 0, ',', '.')); ?> / <?php echo e($item->material_instalasi_satuan); ?>

                    <?php if($item->material_instalasi_keterangan): ?>
                    <br>&nbsp;&nbsp;<em><?php echo e($item->material_instalasi_keterangan); ?></em>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if($item->pemasangan_pelatihan_biaya > 0): ?>
                    <br>• Pemasangan & Pelatihan: Rp <?php echo e(number_format($item->pemasangan_pelatihan_biaya, 0, ',', '.')); ?> / <?php echo e($item->pemasangan_pelatihan_satuan); ?>

                    <?php if($item->pemasangan_pelatihan_keterangan): ?>
                    <br>&nbsp;&nbsp;<em><?php echo e($item->pemasangan_pelatihan_keterangan); ?></em>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if($item->ongkos_kirim_biaya > 0): ?>
                    <br>• Ongkos Kirim: Rp <?php echo e(number_format($item->ongkos_kirim_biaya, 0, ',', '.')); ?> / <?php echo e($item->ongkos_kirim_satuan); ?>

                    <?php if($item->ongkos_kirim_komponen && count($item->ongkos_kirim_komponen) > 0): ?>
                    <br>&nbsp;&nbsp;Komponen: 
                    <?php $__currentLoopData = $item->formatted_ongkos_kirim_komponen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $komponen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($komponen['nama']); ?> (<?php echo e($komponen['formatted_biaya']); ?>)<?php echo e(!$loop->last ? ', ' : ''); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <br><strong>Total Biaya Tambahan: Rp <?php echo e(number_format($item->calculateTotalBiayaTambahan(), 0, ',', '.')); ?></strong>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?php echo e(number_format($item->qty, 0)); ?></td>
                <td class="text-right">Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?></td>
                <td class="text-right">
                    Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?>

                    <?php if($item->calculateTotalBiayaTambahan() > 0): ?>
                    <br>+ Rp <?php echo e(number_format($item->calculateTotalBiayaTambahan(), 0, ',', '.')); ?>

                    <br><strong>= Rp <?php echo e(number_format($item->total_with_additional_costs, 0, ',', '.')); ?></strong>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($item->material_instalasi_biaya > 0 || $item->pemasangan_pelatihan_biaya > 0 || $item->ongkos_kirim_biaya > 0): ?>
                    Termasuk biaya tambahan
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <!-- Totals -->
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal Produk:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->subtotal, 0, ',', '.')); ?></strong></td>
                <td></td>
            </tr>
            <?php if($preorder->total_additional_costs > 0): ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Total Biaya Tambahan:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->total_additional_costs, 0, ',', '.')); ?></strong></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->subtotal_with_additional_costs, 0, ',', '.')); ?></strong></td>
                <td></td>
            </tr>
            <?php else: ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->subtotal, 0, ',', '.')); ?></strong></td>
                <td></td>
            </tr>
            <?php endif; ?>
            <?php if($preorder->diskon > 0): ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Diskon:</strong></td>
                <td class="text-right"><strong>-Rp <?php echo e(number_format($preorder->diskon, 0, ',', '.')); ?></strong></td>
                <td></td>
            </tr>
            <?php endif; ?>
            <?php if($preorder->pajak > 0): ?>
            <tr>
                <td colspan="4" class="text-right"><strong>Pajak:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->pajak, 0, ',', '.')); ?></strong></td>
                <td></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right"><strong>Rp <?php echo e(number_format($preorder->grand_total_with_additional_costs, 0, ',', '.')); ?></strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Terms -->
    <div class="terms">
        <h4>Syarat dan Ketentuan:</h4>
        <ul>
            <li>Harga tersebut diatas tidak termasuk Pajak</li>
            <li>Harga tersebut tidak termasuk water treatment feeding water boiler</li>
            <li>Garansi mesin 1 tahun, electrical 3 bulan</li>
            <li>Pembayaran pertama - Down Payment minimal 50% dari total nilai invoice</li>
            <li>Pembayaran kedua 35% dari nilai invoice dibayarkan ketika barang akan dikirim</li>
            <li>Pembayaran ketiga 15% dari nilai invoice dibayarkan setelah barang diuji dan dinyatakan baik</li>
            <li>Penambahan titik instalasi dihitung terpisah</li>
            <li>Proses manufaktur 60 hari kerja dari DP</li>
            <li>Pembayaran via rekening perusahaan</li>
        </ul>
    </div>

    <!-- Bank Info -->
    <div class="bank-info">
        <strong>Informasi Rekening:</strong><br>
        BCA PT Dahana Rekayasa Nusantara : 6395813432
    </div>

    <!-- Signature -->
    <div class="signature">
        <div class="signature-box">
            Hormat Kami,<br><br>
            <div class="signature-line"></div>
            <strong>Egie Helmi Fauzi</strong><br>
            Direktur
        </div>
    </div>

    <!-- Product Specifications (Second Page) -->
    <?php if($preorder->items->where('product_specifications', '!=', null)->count() > 0): ?>
    <div class="page-break">
        <div class="specs-title">Spesifikasi Produk</div>
        
        <?php $__currentLoopData = $preorder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($item->product_specifications): ?>
            <div class="spec-item">
                <h5><?php echo e($item->deskripsi); ?></h5>
                
                <?php if(is_array($item->product_specifications)): ?>
                    <ul class="spec-list">
                        <?php $__currentLoopData = $item->product_specifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(is_array($value)): ?>
                                <li><strong><?php echo e($key); ?>:</strong>
                                    <ul>
                                        <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subKey => $subValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($subKey); ?>: <?php echo e($subValue); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <li><strong><?php echo e($key); ?>:</strong> <?php echo e($value); ?></li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php else: ?>
                    <div><?php echo nl2br(e($item->product_specifications)); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\hm\resources\views\admin\pre-orders\pdf\penawaran.blade.php ENDPATH**/ ?>