<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Talaran Santri Module Routes
Route::prefix('talaran')->name('talaran.')->middleware(['auth', 'role:pendidikan'])->group(function () {
    Route::get('/', [App\Http\Controllers\TalaranController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\TalaranController::class, 'store'])->name('store');
    Route::put('/{id}', [App\Http\Controllers\TalaranController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\TalaranController::class, 'destroy'])->name('destroy');
    
    // PDF Routes
    Route::get('/cetak/1-2', [App\Http\Controllers\TalaranController::class, 'cetakOneTwo'])->name('cetak.1-2');
    Route::get('/cetak/3-4', [App\Http\Controllers\TalaranController::class, 'cetakThreeFour'])->name('cetak.3-4');
    Route::get('/cetak/full', [App\Http\Controllers\TalaranController::class, 'cetakFull'])->name('cetak.full');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Root Route - Redirect Based on Auth Status
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    $user = Auth::user();
    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'pendidikan' => redirect()->route('pendidikan.dashboard'),
        'sekretaris' => redirect()->route('sekretaris.dashboard'),
        'bendahara' => redirect()->route('bendahara.dashboard'),
        default => redirect()->route('login'),
    };
});

// Database Backup Route (PROTECTED - Admin only)
Route::get('/backup/download', [App\Http\Controllers\BackupController::class, 'download'])
    ->middleware(['auth', 'role:admin'])
    ->name('backup.download');

// Activity Log Route (Admin only)
Route::get('/admin/activity-log', [App\Http\Controllers\ActivityLogController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.activity-log');

// Admin Dashboard & User Management Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\BackupController::class, 'dashboard'])->name('dashboard');
    Route::post('/user', [App\Http\Controllers\BackupController::class, 'storeUser'])->name('user.store');
    Route::put('/user/{user}', [App\Http\Controllers\BackupController::class, 'updateUser'])->name('user.update');
    Route::delete('/user/{user}', [App\Http\Controllers\BackupController::class, 'destroyUser'])->name('user.destroy');
});

// Notification Routes (API for all authenticated users)
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('api.notifications');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| Dashboard Sekretaris Routes
|--------------------------------------------------------------------------
*/

Route::prefix('sekretaris')->middleware(['auth', 'role:sekretaris'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SekretarisController::class, 'dashboard'])->name('sekretaris.dashboard');
    
    // Data Santri
    Route::get('/data-santri', [App\Http\Controllers\SekretarisController::class, 'dataSantri'])->name('sekretaris.data-santri');
    Route::get('/data-santri/create', [App\Http\Controllers\SekretarisController::class, 'createSantri'])->name('sekretaris.data-santri.create');
    Route::post('/data-santri', [App\Http\Controllers\SekretarisController::class, 'storeSantri'])->name('sekretaris.data-santri.store');
    Route::get('/data-santri/{id}/edit', [App\Http\Controllers\SekretarisController::class, 'editSantri'])->name('sekretaris.data-santri.edit');
    Route::put('/data-santri/{id}', [App\Http\Controllers\SekretarisController::class, 'updateSantri'])->name('sekretaris.data-santri.update');
    Route::delete('/data-santri/{id}', [App\Http\Controllers\SekretarisController::class, 'deactivateSantri'])->name('sekretaris.data-santri.deactivate');
    Route::get('/api/kobong/{asramaId}', [App\Http\Controllers\SekretarisController::class, 'getKobongByAsrama'])->name('sekretaris.api.kobong');
    
    // Mutasi Santri
    Route::get('/mutasi-santri', [App\Http\Controllers\SekretarisController::class, 'mutasiSantri'])->name('sekretaris.mutasi-santri');
    Route::post('/mutasi-santri', [App\Http\Controllers\SekretarisController::class, 'storeMutasi'])->name('sekretaris.mutasi-santri.store');
    Route::put('/mutasi-santri/{id}', [App\Http\Controllers\SekretarisController::class, 'updateMutasi'])->name('sekretaris.mutasi-santri.update');
    Route::delete('/mutasi-santri/{id}', [App\Http\Controllers\SekretarisController::class, 'destroyMutasi'])->name('sekretaris.mutasi-santri.destroy');
    
    // Laporan
    Route::get('/laporan', [App\Http\Controllers\SekretarisController::class, 'laporan'])->name('sekretaris.laporan');
    Route::get('/laporan/export-santri', [App\Http\Controllers\SekretarisController::class, 'exportLaporanSantri'])->name('sekretaris.laporan.export-santri');
    Route::get('/laporan/export-statistik-kelas', [App\Http\Controllers\SekretarisController::class, 'exportStatistikKelas'])->name('sekretaris.laporan.export-statistik-kelas');
    Route::get('/laporan/export-statistik-asrama', [App\Http\Controllers\SekretarisController::class, 'exportStatistikAsrama'])->name('sekretaris.laporan.export-statistik-asrama');
    Route::get('/laporan/export-mutasi', [App\Http\Controllers\SekretarisController::class, 'exportLaporanMutasi'])->name('sekretaris.laporan.export-mutasi');
    
    // Import
    Route::get('/data-santri/template-excel', [App\Http\Controllers\SekretarisController::class, 'downloadTemplateExcel'])->name('sekretaris.data-santri.template-excel');
    Route::get('/data-santri/template-csv', [App\Http\Controllers\SekretarisController::class, 'downloadTemplateCsv'])->name('sekretaris.data-santri.template-csv');
    Route::post('/data-santri/import', [App\Http\Controllers\SekretarisController::class, 'importSantri'])->name('sekretaris.data-santri.import');
    
    // Bulk Operations - Kenaikan Kelas
    Route::get('/kenaikan-kelas', [App\Http\Controllers\SekretarisBulkController::class, 'showKenaikanKelas'])->name('sekretaris.kenaikan-kelas');
    Route::post('/kenaikan-kelas', [App\Http\Controllers\SekretarisBulkController::class, 'processKenaikanKelas'])->name('sekretaris.kenaikan-kelas.process');
    Route::get('/api/santri-by-kelas/{kelasId}', [App\Http\Controllers\SekretarisBulkController::class, 'getSantriByKelas'])->name('sekretaris.api.santri-by-kelas');
    
    // Bulk Operations - Perpindahan (Per-Santri Assignment)
    Route::get('/perpindahan', [App\Http\Controllers\SekretarisBulkController::class, 'showPerpindahan'])->name('sekretaris.perpindahan');
    Route::post('/perpindahan', [App\Http\Controllers\SekretarisBulkController::class, 'processPerpindahan'])->name('sekretaris.perpindahan.process');
    Route::get('/api/santri-filtered', [App\Http\Controllers\SekretarisBulkController::class, 'getSantriFiltered'])->name('sekretaris.api.santri-filtered');
    Route::get('/api/kobong-by-asrama/{asramaId}', [App\Http\Controllers\SekretarisBulkController::class, 'getKobongByAsrama'])->name('sekretaris.api.kobong-by-asrama');
    Route::get('/api/kobong-stats/{asramaId}', [App\Http\Controllers\SekretarisBulkController::class, 'getKobongStats'])->name('sekretaris.api.kobong-stats');
});

/*
|--------------------------------------------------------------------------
| Dashboard Bendahara Routes
|--------------------------------------------------------------------------
*/

Route::prefix('bendahara')->middleware(['auth', 'role:bendahara'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\BendaharaController::class, 'dashboard'])->name('bendahara.dashboard');
    
    // Data Santri (Read-only)
    Route::get('/data-santri', [App\Http\Controllers\BendaharaController::class, 'dataSantri'])->name('bendahara.data-santri');
    
    // Syahriah
    Route::get('/syahriah', [App\Http\Controllers\BendaharaController::class, 'syahriah'])->name('bendahara.syahriah');
    Route::post('/syahriah', [App\Http\Controllers\BendaharaController::class, 'storeSyahriah'])->name('bendahara.syahriah.store');
    Route::put('/syahriah/{id}', [App\Http\Controllers\BendaharaController::class, 'updateSyahriah'])->name('bendahara.syahriah.update');
    Route::delete('/syahriah/{id}', [App\Http\Controllers\BendaharaController::class, 'destroySyahriah'])->name('bendahara.syahriah.destroy');
    
    // Cek Tunggakan
    Route::get('/cek-tunggakan', [App\Http\Controllers\BendaharaController::class, 'cekTunggakan'])->name('bendahara.cek-tunggakan');
    Route::post('/cek-tunggakan', [App\Http\Controllers\BendaharaController::class, 'prosesCekTunggakan'])->name('bendahara.cek-tunggakan.process');
    Route::get('/cek-tunggakan/export', [App\Http\Controllers\BendaharaController::class, 'exportLaporanTunggakan'])->name('bendahara.cek-tunggakan.export');
    
    // Pemasukan
    Route::get('/pemasukan', [App\Http\Controllers\BendaharaController::class, 'pemasukan'])->name('bendahara.pemasukan');
    Route::post('/pemasukan', [App\Http\Controllers\BendaharaController::class, 'storePemasukan'])->name('bendahara.pemasukan.store');
    Route::put('/pemasukan/{id}', [App\Http\Controllers\BendaharaController::class, 'updatePemasukan'])->name('bendahara.pemasukan.update');
    Route::delete('/pemasukan/{id}', [App\Http\Controllers\BendaharaController::class, 'destroyPemasukan'])->name('bendahara.pemasukan.destroy');
    
    // Pengeluaran
    Route::get('/pengeluaran', [App\Http\Controllers\BendaharaController::class, 'pengeluaran'])->name('bendahara.pengeluaran');
    Route::post('/pengeluaran', [App\Http\Controllers\BendaharaController::class, 'storePengeluaran'])->name('bendahara.pengeluaran.store');
    Route::put('/pengeluaran/{id}', [App\Http\Controllers\BendaharaController::class, 'updatePengeluaran'])->name('bendahara.pengeluaran.update');
    Route::delete('/pengeluaran/{id}', [App\Http\Controllers\BendaharaController::class, 'destroyPengeluaran'])->name('bendahara.pengeluaran.destroy');
    
    // Pegawai
    Route::get('/pegawai', [App\Http\Controllers\BendaharaController::class, 'pegawai'])->name('bendahara.pegawai');
    Route::post('/pegawai', [App\Http\Controllers\BendaharaController::class, 'storePegawai'])->name('bendahara.pegawai.store');
    Route::put('/pegawai/{id}', [App\Http\Controllers\BendaharaController::class, 'updatePegawai'])->name('bendahara.pegawai.update');
    Route::delete('/pegawai/{id}', [App\Http\Controllers\BendaharaController::class, 'destroyPegawai'])->name('bendahara.pegawai.destroy');
    
    // Gaji
    Route::get('/gaji', [App\Http\Controllers\BendaharaController::class, 'gaji'])->name('bendahara.gaji');
    Route::post('/gaji', [App\Http\Controllers\BendaharaController::class, 'storeGaji'])->name('bendahara.gaji.store');
    Route::put('/gaji/{id}', [App\Http\Controllers\BendaharaController::class, 'updateGaji'])->name('bendahara.gaji.update');
    Route::delete('/gaji/{id}', [App\Http\Controllers\BendaharaController::class, 'destroyGaji'])->name('bendahara.gaji.destroy');
    
    // Laporan
    Route::get('/laporan', [App\Http\Controllers\BendaharaController::class, 'laporan'])->name('bendahara.laporan');
    Route::get('/laporan/export-syahriah', [App\Http\Controllers\BendaharaController::class, 'exportLaporanSyahriah'])->name('bendahara.laporan.export-syahriah');
    Route::get('/laporan/export-pemasukan', [App\Http\Controllers\BendaharaController::class, 'exportLaporanPemasukan'])->name('bendahara.laporan.export-pemasukan');
    Route::get('/laporan/export-pengeluaran', [App\Http\Controllers\BendaharaController::class, 'exportLaporanPengeluaran'])->name('bendahara.laporan.export-pengeluaran');
    Route::get('/laporan/export-kas', [App\Http\Controllers\BendaharaController::class, 'exportLaporanKas'])->name('bendahara.laporan.export-kas');
    Route::get('/laporan/export-gaji', [App\Http\Controllers\BendaharaController::class, 'exportLaporanGaji'])->name('bendahara.laporan.export-gaji');
    Route::get('/laporan/export-keuangan-lengkap', [App\Http\Controllers\BendaharaController::class, 'exportLaporanKeuanganLengkap'])->name('bendahara.laporan.export-keuangan-lengkap');
});

/*
|--------------------------------------------------------------------------
| Dashboard Pendidikan Routes
|--------------------------------------------------------------------------
*/

Route::prefix('pendidikan')->middleware(['auth', 'role:pendidikan'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\PendidikanController::class, 'dashboard'])->name('pendidikan.dashboard');
    
    // Pengaturan Rapor
    Route::get('/settings', [App\Http\Controllers\PendidikanController::class, 'settings'])->name('pendidikan.settings');
    Route::post('/settings', [App\Http\Controllers\PendidikanController::class, 'updateSettings'])->name('pendidikan.settings.update');
    Route::post('/kelas/{id}/ttd', [App\Http\Controllers\PendidikanController::class, 'uploadWaliKelasTTD'])->name('pendidikan.kelas.upload-ttd');

    // Kalender Pendidikan
    Route::get('/kalender', [App\Http\Controllers\PendidikanController::class, 'kalender'])->name('pendidikan.kalender');
    Route::post('/kalender', [App\Http\Controllers\PendidikanController::class, 'storeKalender'])->name('pendidikan.kalender.store');
    Route::delete('/kalender/{id}', [App\Http\Controllers\PendidikanController::class, 'destroyKalender'])->name('pendidikan.kalender.destroy');

    // Nilai Santri
    Route::get('/nilai', [App\Http\Controllers\PendidikanController::class, 'nilai'])->name('pendidikan.nilai');
    Route::post('/nilai', [App\Http\Controllers\PendidikanController::class, 'storeNilai'])->name('pendidikan.nilai.store');
    Route::post('/nilai/bulk', [App\Http\Controllers\PendidikanController::class, 'storeNilaiBulk'])->name('pendidikan.nilai.store-bulk');
    Route::put('/nilai/{id}', [App\Http\Controllers\PendidikanController::class, 'updateNilai'])->name('pendidikan.nilai.update');
    Route::delete('/nilai/{id}', [App\Http\Controllers\PendidikanController::class, 'destroyNilai'])->name('pendidikan.nilai.destroy');
    Route::get('/nilai/cetak', [App\Http\Controllers\PendidikanController::class, 'cetakNilai'])->name('pendidikan.nilai.cetak');
    
    // Mata Pelajaran
    Route::resource('mata-pelajaran', \App\Http\Controllers\MataPelajaranController::class)->except(['create', 'edit', 'show']);

    // Nilai Mingguan / Talaran
    Route::get('nilai-mingguan', [\App\Http\Controllers\NilaiMingguanController::class, 'index'])->name('pendidikan.nilai-mingguan.index');
    Route::post('nilai-mingguan', [\App\Http\Controllers\NilaiMingguanController::class, 'store'])->name('pendidikan.nilai-mingguan.store');
    
    // Ujian Mingguan (New Weekly Exam System)
    Route::get('ujian-mingguan', [\App\Http\Controllers\UjianMingguanController::class, 'index'])->name('pendidikan.ujian-mingguan');
    Route::post('ujian-mingguan/store', [\App\Http\Controllers\UjianMingguanController::class, 'store'])->name('pendidikan.ujian-mingguan.store');
    
    Route::get('/mapel', [App\Http\Controllers\PendidikanController::class, 'mapel'])->name('pendidikan.mapel');
    Route::post('/mapel', [App\Http\Controllers\PendidikanController::class, 'storeMapel'])->name('pendidikan.mapel.store');
    Route::put('/mapel/{id}', [App\Http\Controllers\PendidikanController::class, 'updateMapel'])->name('pendidikan.mapel.update');
    Route::delete('/mapel/{id}', [App\Http\Controllers\PendidikanController::class, 'destroyMapel'])->name('pendidikan.mapel.destroy');
    Route::post('/mapel/{id}/update-guru-badal', [App\Http\Controllers\PendidikanController::class, 'updateGuruBadal'])->name('pendidikan.mapel.update-guru-badal');
    Route::post('/mapel/{id}/update-guru-pengampu', [App\Http\Controllers\PendidikanController::class, 'updateGuruPengampu'])->name('pendidikan.mapel.update-guru-pengampu');
    
    // Jadwal Pelajaran
    Route::get('/jadwal', [App\Http\Controllers\PendidikanController::class, 'jadwal'])->name('pendidikan.jadwal');
    Route::post('/jadwal', [App\Http\Controllers\PendidikanController::class, 'storeJadwal'])->name('pendidikan.jadwal.store');
    Route::put('/jadwal/{id}', [App\Http\Controllers\PendidikanController::class, 'updateJadwal'])->name('pendidikan.jadwal.update');
    Route::delete('/jadwal/{id}', [App\Http\Controllers\PendidikanController::class, 'destroyJadwal'])->name('pendidikan.jadwal.destroy');
    Route::put('/jadwal/kitab/{id}', [App\Http\Controllers\PendidikanController::class, 'updateKitabTalaran'])->name('pendidikan.jadwal.update-kitab');
    Route::put('/jadwal/kitab-global/{semester}', [App\Http\Controllers\PendidikanController::class, 'updateKitabTalaranGlobal'])->name('pendidikan.jadwal.update-kitab-global');
    Route::delete('/jadwal/kitab/delete-by-kelas/{kelasId}', [App\Http\Controllers\PendidikanController::class, 'deleteKitabByKelas'])->name('pendidikan.jadwal.delete-kitab-by-kelas');
    Route::post('/jadwal/{id}/update-guru-badal', [App\Http\Controllers\PendidikanController::class, 'updateGuruBadalJadwal'])->name('pendidikan.jadwal.update-guru-badal');
    Route::post('/kelas/{id}/update-wali-kelas', [App\Http\Controllers\PendidikanController::class, 'updateWaliKelas'])->name('pendidikan.kelas.update-wali-kelas');
    Route::post('/kelas/{id}/update-wali-kelas-dual', [App\Http\Controllers\PendidikanController::class, 'updateWaliKelasDual'])->name('pendidikan.kelas.update-wali-kelas-dual');
    
    // Laporan
    Route::get('/laporan', [App\Http\Controllers\PendidikanController::class, 'laporan'])->name('pendidikan.laporan');
    Route::post('/laporan/tahun-ajaran', [App\Http\Controllers\PendidikanController::class, 'storeTahunAjaran'])->name('pendidikan.tahun-ajaran.store');
    Route::delete('/laporan/tahun-ajaran/{id}', [App\Http\Controllers\PendidikanController::class, 'destroyTahunAjaranId'])->name('pendidikan.tahun-ajaran.destroy');
    Route::get('/laporan/export-rapor', [App\Http\Controllers\PendidikanController::class, 'exportRapor'])->name('pendidikan.laporan.export-rapor');
    Route::get('/laporan/export-rapor-kelas', [App\Http\Controllers\PendidikanController::class, 'exportRaporKelas'])->name('pendidikan.laporan.export-rapor-kelas');
    Route::get('/laporan/export-daftar-nilai', [App\Http\Controllers\PendidikanController::class, 'exportDaftarNilai'])->name('pendidikan.laporan.export-daftar-nilai');
    Route::get('/laporan/export-statistik', [App\Http\Controllers\PendidikanController::class, 'exportStatistikPrestasi'])->name('pendidikan.laporan.export-statistik');
    Route::get('/laporan/export-absensi', [App\Http\Controllers\PendidikanController::class, 'exportRekapAbsensi'])->name('pendidikan.laporan.export-absensi');
    Route::get('/laporan/export-ranking', [App\Http\Controllers\PendidikanController::class, 'exportRankingKelas'])->name('pendidikan.laporan.export-ranking');
    
    // Ijazah
    Route::get('/ijazah', [App\Http\Controllers\PendidikanController::class, 'ijazah'])->name('pendidikan.ijazah');
    Route::post('/ijazah/settings', [App\Http\Controllers\PendidikanController::class, 'updateIjazahSettings'])->name('pendidikan.ijazah.settings');
    Route::get('/ijazah/cetak/{type}/{kelasId}', [App\Http\Controllers\PendidikanController::class, 'cetakIjazah'])->name('pendidikan.ijazah.cetak');
    Route::get('/ijazah/cetak-santri/{type}/{santriId}', [App\Http\Controllers\PendidikanController::class, 'cetakIjazahSantri'])->name('pendidikan.ijazah.cetak-santri');
    
    // Absensi Santri
    Route::get('/absensi', [App\Http\Controllers\PendidikanController::class, 'absensi'])->name('pendidikan.absensi');
    Route::post('/absensi', [App\Http\Controllers\PendidikanController::class, 'storeAbsensi'])->name('pendidikan.absensi.store');
    Route::get('/absensi/cetak', [App\Http\Controllers\PendidikanController::class, 'cetakAbsensi'])->name('pendidikan.absensi.cetak');
    Route::put('/absensi/{id}', [App\Http\Controllers\PendidikanController::class, 'updateAbsensi'])->name('pendidikan.absensi.update');
    Route::delete('/absensi/{id}', [App\Http\Controllers\PendidikanController::class, 'destroyAbsensi'])->name('pendidikan.absensi.destroy');
    Route::get('/absensi/rekap', [App\Http\Controllers\PendidikanController::class, 'rekapAbsensi'])->name('pendidikan.absensi.rekap');
    

});

/*
|--------------------------------------------------------------------------
| Dashboard Admin Pusat Routes
|--------------------------------------------------------------------------
*/

// Admin Routes (Settings/Pengaturan only - Dashboard moved to line 60)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // Settings/Pengaturan
    Route::get('/pengaturan', [App\Http\Controllers\AdminController::class, 'pengaturan'])->name('admin.pengaturan');
    Route::post('/pengaturan/app', [App\Http\Controllers\AdminController::class, 'updateAppSettings'])->name('admin.pengaturan.app');
    Route::post('/pengaturan/user', [App\Http\Controllers\AdminController::class, 'createUser'])->name('admin.pengaturan.user.create');
    Route::put('/pengaturan/user/{id}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.pengaturan.user.update');
    Route::delete('/pengaturan/user/{id}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.pengaturan.user.delete');
});

// Kartu Digital Routes
Route::middleware(['auth', 'role:sekretaris'])->prefix('sekretaris')->name('sekretaris.')->group(function () {
    Route::get('/kartu-digital', [App\Http\Controllers\KartuDigitalController::class, 'index'])->name('kartu-digital');
    Route::get('/kartu-digital/{id}/download', [App\Http\Controllers\KartuDigitalController::class, 'downloadPdf'])->name('kartu-digital.download');
    Route::get('/kartu-digital/{id}/preview', [App\Http\Controllers\KartuDigitalController::class, 'previewPdf'])->name('kartu-digital.preview');
});

// Midtrans Routes
Route::middleware('auth')->group(function () {
    Route::post('/santri/{santri}/generate-va', [App\Http\Controllers\MidtransController::class, 'generateVa'])->name('santri.generate-va');
    Route::post('/santri/{santri}/reset-va', [App\Http\Controllers\MidtransController::class, 'resetVa'])->name('santri.reset-va');
    Route::post('/santri/generate-va-bulk', [App\Http\Controllers\MidtransController::class, 'generateVaBulk'])->name('santri.generate-va-bulk');
    Route::post('/santri/reset-va-bulk', [App\Http\Controllers\MidtransController::class, 'resetVaBulk'])->name('santri.reset-va-bulk');
});

Route::post('/midtrans/webhook', [App\Http\Controllers\MidtransController::class, 'webhook'])->name('midtrans.webhook');
Route::post('/api/midtrans/callback', [App\Http\Controllers\MidtransController::class, 'webhook'])->name('midtrans.callback');
