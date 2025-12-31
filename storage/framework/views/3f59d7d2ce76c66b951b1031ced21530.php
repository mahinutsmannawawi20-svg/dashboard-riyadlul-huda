

<?php $__env->startSection('title', 'Data Mata Ujian'); ?>
<?php $__env->startSection('page-title', 'Data Mata Ujian'); ?>

<?php $__env->startSection('sidebar-menu'); ?>
    <?php echo $__env->make('pendidikan.partials.sidebar-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); border-radius: 12px; padding: 24px 28px; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i data-feather="book-open" style="width: 24px; height: 24px; color: white;"></i>
                </div>
                <div>
                    <h2 style="font-size: 24px; font-weight: 700; color: white; margin: 0;">Data Mata Ujian</h2>
                    <p style="font-size: 14px; color: rgba(255,255,255,0.9); margin: 4px 0 0 0;">Kelola mata ujian dan kategori</p>
                </div>
            </div>
            <button onclick="openModal()" style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: white; border: none; border-radius: 8px; color: #4f46e5; font-weight: 600; cursor: pointer;">
                <i data-feather="plus" style="width: 18px; height: 18px;"></i>
                Tambah Mata Ujian
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div style="background: white; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <form method="GET" action="<?php echo e(route('mata-pelajaran.index')); ?>" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
            <!-- Search -->
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #64748b;">Cari Mata Ujian</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Ketik nama mata ujian..." style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.2s;" onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79, 70, 229, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
            </div>

            <!-- Filter Kategori -->
            <div style="min-width: 180px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #64748b;">Kategori</label>
                <select name="kategori" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 14px; background: white; cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79, 70, 229, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    <option value="">Semua Kategori</option>
                    <option value="Wajib" <?php echo e(request('kategori') == 'Wajib' ? 'selected' : ''); ?>>Wajib (Kelas)</option>
                    <option value="Umum" <?php echo e(request('kategori') == 'Umum' ? 'selected' : ''); ?>>Umum</option>
                    <option value="Mulok" <?php echo e(request('kategori') == 'Mulok' ? 'selected' : ''); ?>>Mulok</option>
                </select>
            </div>

            <!-- Filter Kelas -->
            <div style="min-width: 180px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #64748b;">Kelas</label>
                <select name="kelas" style="width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 14px; background: white; cursor: pointer; transition: all 0.2s;" onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79, 70, 229, 0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    <option value="">Semua Kelas</option>
                    <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id); ?>" <?php echo e(request('kelas') == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_kelas); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(79, 70, 229, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(79, 70, 229, 0.2)'">
                    <i data-feather="filter" style="width: 16px; height: 16px;"></i>
                    Filter
                </button>
                
                <a href="<?php echo e(route('mata-pelajaran.index')); ?>" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: white; border: 1.5px solid #e2e8f0; border-radius: 8px; color: #64748b; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    <i data-feather="x" style="width: 16px; height: 16px;"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 16px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">No</th>
                        <th style="padding: 16px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Mata Ujian</th>
                        <th style="padding: 16px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Kategori</th>
                        <th style="padding: 16px; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Kelas</th>
                        <th style="padding: 16px; text-align: center; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">KKM</th>
                        <th style="padding: 16px; text-align: right; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $mapel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                        <td style="padding: 16px; color: #64748b; font-size: 14px;"><?php echo e($index + 1); ?></td>
                        <td style="padding: 16px; font-weight: 500; color: #1e293b; font-size: 14px;"><?php echo e($m->nama_mapel); ?></td>
                        <td style="padding: 16px;">
                            <?php
                                $badgeColor = match($m->kategori) {
                                    'Wajib' => 'background: #dbeafe; color: #1e40af;',
                                    'Umum' => 'background: #dcfce7; color: #166534;',
                                    'Mulok' => 'background: #fef3c7; color: #92400e;',
                                    default => 'background: #f1f5f9; color: #475569;'
                                };
                            ?>
                            <span style="padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 500; <?php echo e($badgeColor); ?>">
                                <?php echo e($m->kategori); ?>

                            </span>
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                <?php $__empty_1 = true; $__currentLoopData = $m->kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <span style="padding: 2px 8px; border-radius: 4px; background: #f1f5f9; color: #475569; font-size: 11px; border: 1px solid #e2e8f0;">
                                        <?php echo e($k->nama_kelas); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <span style="font-size: 12px; color: #94a3b8; font-style: italic;">Semua Kelas</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="padding: 16px; text-align: center; color: #64748b; font-size: 14px;"><?php echo e($m->kkm); ?></td>
                        <td style="padding: 16px; text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                <button onclick="editMapel(<?php echo e($m->load('kelas')); ?>)" style="padding: 6px; background: #fff7ed; border: 1px solid #ffedd5; border-radius: 6px; color: #ea580c; cursor: pointer;">
                                    <i data-feather="edit-2" style="width: 16px; height: 16px;"></i>
                                </button>
                                <form action="<?php echo e(route('mata-pelajaran.destroy', $m->id)); ?>" method="POST" onsubmit="return confirm('Hapus mata pelajaran ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" style="padding: 6px; background: #fef2f2; border: 1px solid #fee2e2; border-radius: 6px; color: #ef4444; cursor: pointer;">
                                        <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        
        <?php echo e($mapel->links('vendor.pagination.custom')); ?>

    </div>

    <!-- Modal -->
    <div id="mapelModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; width: 450px; max-width: 90%; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); max-height: 90vh; display: flex; flex-direction: column;">
            <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="modalTitle" style="margin: 0; font-size: 18px; font-weight: 600; color: #1e293b;">Tambah Mata Ujian</h3>
                <button onclick="closeModal()" style="background: none; border: none; cursor: pointer; color: #64748b;">
                    <i data-feather="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>
            
            <form id="mapelForm" action="<?php echo e(route('mata-pelajaran.store')); ?>" method="POST" style="padding: 20px; overflow-y: auto;">
                <?php echo csrf_field(); ?>
                <div id="methodField"></div>
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #334155;">Nama Mata Ujian</label>
                    <input type="text" name="nama_mapel" id="namaMapel" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #334155;">Kategori</label>
                    <select name="kategori" id="kategoriMapel" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                        <option value="Wajib">Wajib (Kelas)</option>
                        <option value="Umum">Umum</option>
                        <option value="Mulok">Mulok</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #334155;">KKM</label>
                    <input type="number" name="kkm" id="kkmMapel" value="70" required min="0" max="100" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #334155;">Berlaku untuk Kelas</label>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; border: 1px solid #e2e8f0; padding: 10px; border-radius: 6px;">
                        <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                            <input type="checkbox" name="kelas_ids[]" value="<?php echo e($kelas->id); ?>" class="kelas-checkbox" style="width: 16px; height: 16px;">
                            <?php echo e($kelas->nama_kelas); ?>

                        </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">* Kosongkan jika berlaku untuk semua kelas</small>
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #334155;">Konfigurasi Tambahan</label>
                    <div style="border: 1px solid #e2e8f0; padding: 12px; border-radius: 6px; background: #f8fafc;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="is_talaran" id="isTalaran" value="1" style="width: 16px; height: 16px; accent-color: #4f46e5;">
                            <div>
                                <span style="font-size: 14px; font-weight: 500; color: #334155; display: block;">Wajib Ujian Mingguan?</span>
                                <small style="color: #64748b; font-size: 12px;">Jika dicentang, mapel ini akan muncul di menu Input Ujian Mingguan.</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="closeModal()" style="padding: 10px 20px; background: white; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 500; color: #64748b; cursor: pointer;">Batal</button>
                    <button type="submit" style="padding: 10px 20px; background: #4f46e5; border: none; border-radius: 6px; font-weight: 500; color: white; cursor: pointer;">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('mapelModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Tambah Mata Ujian';
            document.getElementById('mapelForm').action = "<?php echo e(route('mata-pelajaran.store')); ?>";
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('namaMapel').value = '';
            document.getElementById('kategoriMapel').value = 'Umum';
            document.getElementById('kkmMapel').value = '70';
            
            // Reset checking boxes
            document.querySelectorAll('.kelas-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('isTalaran').checked = false;
        }

        function editMapel(mapel) {
            document.getElementById('mapelModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Edit Mata Ujian';
            document.getElementById('mapelForm').action = `/pendidikan/mata-pelajaran/${mapel.id}`;
            document.getElementById('methodField').innerHTML = '<?php echo method_field("PUT"); ?>';
            
            document.getElementById('namaMapel').value = mapel.nama_mapel;
            document.getElementById('kategoriMapel').value = mapel.kategori;
            document.getElementById('kkmMapel').value = mapel.kkm;

            // Set checkboxes logic
            const assignedKelasIds = mapel.kelas ? mapel.kelas.map(k => k.id) : [];
            document.querySelectorAll('.kelas-checkbox').forEach(cb => {
                cb.checked = assignedKelasIds.includes(parseInt(cb.value));
            });
            
            // Set Talaran Checkbox
            document.getElementById('isTalaran').checked = mapel.is_talaran == 1;
        }

        function closeModal() {
            document.getElementById('mapelModal').style.display = 'none';
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\v\.gemini\antigravity\scratch\dashboard-riyadlul-huda\resources\views/pendidikan/mata-pelajaran/index.blade.php ENDPATH**/ ?>