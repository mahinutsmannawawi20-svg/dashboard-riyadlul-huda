<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Tunggakan Santri</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #dc2626; text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #dc2626; color: white; font-size: 12px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #fef2f2; }
        .summary { margin-top: 20px; padding: 15px; background-color: #fef2f2; border-radius: 5px; border: 1px solid #fecaca; }
        .summary h3 { color: #dc2626; margin-top: 0; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #fecaca !important; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
        .header-info { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; }
    </style>
</head>
<body>
    <h2>LAPORAN TUNGGAKAN SANTRI</h2>
    <p class="subtitle">Yayasan Pondok Pesantren Riyadlul Huda</p>
    
    <div class="header-info">
        <div>
            <strong>Tanggal Cetak:</strong> <?php echo e(now()->format('d F Y, H:i')); ?> WIB
        </div>
        <div>
            <strong>Biaya/Bulan:</strong> Rp <?php echo e(number_format($biayaBulanan, 0, ',', '.')); ?>

        </div>
    </div>

    <?php if(request()->anyFilled(['kelas_id', 'asrama_id', 'gender'])): ?>
    <div style="margin-bottom: 15px; padding: 10px; background: #f8fafc; border-radius: 5px; font-size: 13px;">
        <strong>Filter:</strong>
        <?php if(request('asrama_id')): ?>
            Asrama: <?php echo e($asramaList->find(request('asrama_id'))->nama_asrama ?? '-'); ?> |
        <?php endif; ?>
        <?php if(request('kelas_id')): ?>
            Kelas: <?php echo e($kelasList->find(request('kelas_id'))->nama_kelas ?? '-'); ?> |
        <?php endif; ?>
        <?php if(request('gender')): ?>
            Gender: <?php echo e(ucfirst(request('gender'))); ?>

        <?php endif; ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 80px;">NIS</th>
                <th>Nama Santri</th>
                <th style="width: 100px;">Kelas</th>
                <th style="width: 100px;">Asrama</th>
                <th style="width: 100px;" class="text-center">Tunggakan</th>
                <th style="width: 130px;" class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $santriWithArrears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item['santri']->nis); ?></td>
                    <td style="font-weight: 600;"><?php echo e($item['santri']->nama_santri); ?></td>
                    <td><?php echo e($item['santri']->kelas->nama_kelas ?? '-'); ?></td>
                    <td><?php echo e($item['santri']->asrama->nama_asrama ?? '-'); ?></td>
                    <td class="text-center">
                        <span class="badge badge-warning"><?php echo e($item['unpaid_months']); ?> bulan</span>
                    </td>
                    <td class="text-right" style="color: #dc2626; font-weight: bold;">
                        Rp <?php echo e(number_format($item['total_arrears'], 0, ',', '.')); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px;">Tidak ada data tunggakan</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <?php if(count($santriWithArrears) > 0): ?>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right; font-weight: bold;">TOTAL KESELURUHAN:</td>
                <td class="text-center" style="font-weight: bold;"><?php echo e($grandTotalBulan); ?> bulan</td>
                <td class="text-right" style="font-weight: bold; color: #dc2626; font-size: 14px;">
                    Rp <?php echo e(number_format($grandTotalRupiah, 0, ',', '.')); ?>

                </td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <div class="summary">
        <h3>Ringkasan</h3>
        <p><strong>Total Santri Menunggak:</strong> <?php echo e($totalSantriMenunggak); ?> orang</p>
        <p><strong>Total Bulan Tunggakan:</strong> <?php echo e($grandTotalBulan); ?> bulan</p>
        <p><strong>Total Nominal Tunggakan:</strong> Rp <?php echo e(number_format($grandTotalRupiah, 0, ',', '.')); ?></p>
    </div>

    <p class="footer">
        Dicetak pada: <?php echo e(now()->format('d/m/Y H:i:s')); ?><br>
        Yayasan Pondok Pesantren Riyadlul Huda<br>
        <em>Dokumen ini digenerate secara otomatis oleh sistem.</em>
    </p>
</body>
</html>
<?php /**PATH C:\Users\v\.gemini\antigravity\scratch\dashboard-riyadlul-huda\resources\views/bendahara/exports/laporan-tunggakan.blade.php ENDPATH**/ ?>