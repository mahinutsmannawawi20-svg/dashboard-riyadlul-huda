<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BendaharaController;
use App\Http\Controllers\Api\SekretarisController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PendidikanController;
use App\Http\Controllers\Api\NotificationController;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Sekretaris Routes
    Route::prefix('sekretaris')->group(function () {
        Route::get('/dashboard', [SekretarisController::class, 'dashboard']);
        Route::get('/get-filters', [SekretarisController::class, 'getFilters']);
        Route::get('/santri', [SekretarisController::class, 'index']);
        Route::post('/santri', [SekretarisController::class, 'storeSantri']);
        Route::put('/santri/{id}', [SekretarisController::class, 'updateSantri']);
        Route::delete('/santri/{id}', [SekretarisController::class, 'deactivateSantri']);
        Route::get('/perizinan', [SekretarisController::class, 'perizinan']);
        Route::post('/perizinan', [SekretarisController::class, 'storePerizinan']);
        Route::post('/perizinan/{id}/approval', [SekretarisController::class, 'approvePerizinan']);
    });



    // Bendahara Routes
    Route::prefix('bendahara')->group(function () {
        Route::get('/dashboard', [BendaharaController::class, 'dashboard']);
        Route::get('/kas', [BendaharaController::class, 'kas']);
        Route::post('/kas', [BendaharaController::class, 'storeKas']);
        Route::get('/cek-tunggakan', [BendaharaController::class, 'cekTunggakan']);
        Route::get('/bank-accounts', [BendaharaController::class, 'getBankAccounts']);
        Route::post('/bank-accounts', [BendaharaController::class, 'storeBankAccount']);
        Route::get('/withdrawals', [BendaharaController::class, 'getWithdrawals']);
        Route::post('/withdrawals', [BendaharaController::class, 'requestWithdrawal']);
    });

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/withdrawals', [AdminController::class, 'trackingWithdrawals']);
        Route::post('/withdrawals/{id}/approve', [AdminController::class, 'approveWithdrawal']);
    });

    // Pendidikan Routes
    Route::prefix('pendidikan')->group(function () {
        Route::get('/kalender', [PendidikanController::class, 'kalender']);
        Route::get('/e-rapor', [PendidikanController::class, 'eRapor']);
        Route::get('/ijazah', [PendidikanController::class, 'ijazah']);
    });

    // Notifications Routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

