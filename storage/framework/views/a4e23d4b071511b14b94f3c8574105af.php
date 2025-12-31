

<?php $__env->startSection('title', 'Kalender Pendidikan'); ?>
<?php $__env->startSection('page-title', 'Kalender Pendidikan'); ?>

<?php $__env->startSection('sidebar-menu'); ?>
    <?php echo $__env->make('pendidikan.partials.sidebar-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 16px; border: 1px solid #c3e6cb; font-size: 13px;">
            ✓ <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Event List & Add Form (Left Panel) -->
        <div class="md:col-span-1">
            <!-- Add Event Card -->
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 24px; border: 1px solid #e2e8f0;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i data-feather="plus-circle" style="color: #3b82f6;"></i> Tambah Agenda
                </h3>
                
                <form action="<?php echo e(route('pendidikan.kalender.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; display: block;">Judul Agenda</label>
                        <input type="text" name="judul" required class="form-control" placeholder="Contoh: Ujian Tengah Semester"
                           style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 14px;">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; display: block;">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Detail agenda..."
                           style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 14px;"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3" style="margin-bottom: 12px;">
                        <div>
                            <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; display: block;">Mulai</label>
                            <input type="date" name="tanggal_mulai" required class="form-control"
                               style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; display: block;">Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control"
                               style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px; font-size: 13px;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 4px; display: block;">Kategori</label>
                        <select name="kategori" required class="form-control"
                                style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px; font-size: 14px; background: white;">
                            <option value="Kegiatan">🔵 Kegiatan</option>
                            <option value="Libur">🔴 Libur</option>
                            <option value="Ujian">🟠 Ujian</option>
                            <option value="Rapat">🟣 Rapat</option>
                            <option value="Lainnya">⚪ Lainnya</option>
                        </select>
                    </div>

                    <button type="submit" style="width: 100%; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s;"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i data-feather="save" style="width: 16px; height: 16px;"></i> Simpan Agenda
                    </button>
                </form>
            </div>

            <!-- Upcoming Events List -->
            <div style="background: white; border-radius: 12px; padding: 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; overflow: hidden;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
                    <h3 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0;">Agenda Mendatang</h3>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php $__empty_1 = true; $__currentLoopData = $events->where('tanggal_mulai', '>=', now()->toDateString())->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 12px; align-items: start;">
                            <div style="background: <?php echo e($event->warna); ?>20; color: <?php echo e($event->warna); ?>; width: 40px; height: 40px; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;">
                                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase;"><?php echo e($event->tanggal_mulai->format('M')); ?></span>
                                <span style="font-size: 14px; font-weight: 800; line-height: 1;"><?php echo e($event->tanggal_mulai->format('d')); ?></span>
                            </div>
                            <div style="flex: 1;">
                                <h4 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 4px 0;"><?php echo e($event->judul); ?></h4>
                                <p style="font-size: 12px; color: #64748b; margin: 0;">
                                    <?php echo e($event->kategori); ?> • 
                                    <?php if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai): ?>
                                        Sampai <?php echo e($event->tanggal_selesai->format('d M')); ?>

                                    <?php else: ?>
                                        Sehari
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div style="padding: 30px; text-align: center; color: #94a3b8;">
                            <i data-feather="calendar" style="width: 32px; height: 32px; margin-bottom: 8px; opacity: 0.5;"></i>
                            <p style="font-size: 13px;">Belum ada agenda mendatang</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Calendar Grid (Right Panel) -->
        <div class="md:col-span-2">
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; height: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0;">Semua Agenda</h2>
                    <div style="display: flex; gap: 8px;">
                        <span style="font-size: 12px; padding: 4px 8px; background: #ef444420; color: #ef4444; border-radius: 4px; font-weight: 600;">Libur</span>
                        <span style="font-size: 12px; padding: 4px 8px; background: #f59e0b20; color: #f59e0b; border-radius: 4px; font-weight: 600;">Ujian</span>
                        <span style="font-size: 12px; padding: 4px 8px; background: #3b82f620; color: #3b82f6; border-radius: 4px; font-weight: 600;">Kegiatan</span>
                    </div>
                </div>

                <!-- Event List Card Grid -->
                <div class="grid grid-cols-1 gap-4">
                    <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div style="display: flex; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; transition: all 0.2s; background: white;"
                             onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'; this.style.borderColor='<?php echo e($event->warna); ?>';"
                             onmouseout="this.style.boxShadow='none'; this.style.borderColor='#e2e8f0';">
                            
                            <!-- Date Strip -->
                            <div style="background: <?php echo e($event->warna); ?>; width: 6px;"></div>
                            
                            <div style="padding: 16px; flex: 1; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: <?php echo e($event->warna); ?>;">
                                            <?php echo e($event->kategori); ?>

                                        </span>
                                        <span style="font-size: 11px; color: #94a3b8;">•</span>
                                        <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                                            <?php echo e($event->tanggal_mulai->format('d F Y')); ?>

                                            <?php if($event->tanggal_selesai && $event->tanggal_selesai != $event->tanggal_mulai): ?>
                                                - <?php echo e($event->tanggal_selesai->format('d F Y')); ?>

                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 4px 0;"><?php echo e($event->judul); ?></h3>
                                    <?php if($event->deskripsi): ?>
                                        <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.4;"><?php echo e($event->deskripsi); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <form action="<?php echo e(route('pendidikan.kalender.destroy', $event->id)); ?>" method="POST" onsubmit="return confirm('Hapus agenda ini?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" style="background: #fee2e2; color: #ef4444; border: none; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;"
                                            title="Hapus Agenda"
                                            onmouseover="this.style.background='#fecaca';"
                                            onmouseout="this.style.background='#fee2e2';">
                                        <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 12px; border: 2px dashed #e2e8f0;">
                            <div style="background: #e2e8f0; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                                <i data-feather="calendar" style="width: 32px; height: 32px; color: #94a3b8;"></i>
                            </div>
                            <h3 style="font-size: 16px; font-weight: 600; color: #475569; margin: 0 0 4px 0;">Belum ada agenda</h3>
                            <p style="font-size: 13px; color: #94a3b8; margin: 0;">Tambahkan agenda baru melalui form di sebelah kiri.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to re-init feather icons after Turbo load if needed, though app.js handles it globally -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\v\.gemini\antigravity\scratch\dashboard-riyadlul-huda\resources\views/pendidikan/kalender/index.blade.php ENDPATH**/ ?>