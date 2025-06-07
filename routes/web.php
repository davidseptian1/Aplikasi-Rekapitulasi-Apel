<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthV2Controller;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\PenggunaController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\RekapApelController;
use App\Http\Controllers\Backend\RiwayatPiketController;
use App\Http\Controllers\Backend\LaporanSubdisController;
use App\Http\Controllers\Backend\Master\SubdisController;
use App\Http\Controllers\Backend\Master\JabatanController;
use App\Http\Controllers\Backend\Master\JamApelController;
use App\Http\Controllers\Backend\Master\PangkatController;
use App\Http\Controllers\Backend\GrafikKehadiranController;
use App\Http\Controllers\Backend\LaporanPersonelController;
use App\Http\Controllers\Backend\Master\KeteranganController;

Route::middleware('guest')->group(function () {
    // Route::get('/v2', [AuthV2Controller::class, 'index'])->name('loginv2');
    Route::get('/', [AuthV2Controller::class, 'index'])->name('login');
    Route::post('/login', [AuthV2Controller::class, 'login'])->name('login.process');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
});

Route::middleware('auth')->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });

    Route::get('/dashboard/grafik-kehadiran', [GrafikKehadiranController::class, 'index'])
        ->name('grafik.kehadiran.index');

    Route::prefix('piket')->name('piket.')->group(function () {
        Route::post('/', [DashboardController::class, 'store'])->name('store');
        Route::put('/{id}', [DashboardController::class, 'update'])->name('update');
        Route::delete('/{id}', [DashboardController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pangkat')->name('pangkat.')->group(function () {
        Route::get('/', [PangkatController::class, 'index'])->name('index');
        Route::get('/create', [PangkatController::class, 'create'])->name('create');
        Route::post('/', [PangkatController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PangkatController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PangkatController::class, 'update'])->name('update');
        Route::delete('/{id}', [PangkatController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('jabatan')->name('jabatan.')->group(function () {
        Route::get('/', [JabatanController::class, 'index'])->name('index');
        Route::get('/create', [JabatanController::class, 'create'])->name('create');
        Route::post('/', [JabatanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [JabatanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [JabatanController::class, 'update'])->name('update');
        Route::delete('/{id}', [JabatanController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('keterangan')->name('keterangan.')->group(function () {
        Route::get('/', [KeteranganController::class, 'index'])->name('index');
        Route::get('/create', [KeteranganController::class, 'create'])->name('create');
        Route::post('/', [KeteranganController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KeteranganController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KeteranganController::class, 'update'])->name('update');
        Route::delete('/{id}', [KeteranganController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('subdis')->name('subdis.')->group(function () {
        Route::get('/', [SubdisController::class, 'index'])->name('index');
        Route::get('/create', [SubdisController::class, 'create'])->name('create');
        Route::post('/', [SubdisController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SubdisController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SubdisController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubdisController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [PenggunaController::class, 'index'])->name('index');
        Route::get('/create', [PenggunaController::class, 'create'])->name('create');
        Route::post('/', [PenggunaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PenggunaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PenggunaController::class, 'update'])->name('update');
        Route::delete('/{id}', [PenggunaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('rekap-apel')->name('rekap-apel.')->group(function () {
        Route::get('/', [RekapApelController::class, 'index'])->name('index');
        Route::get('/subdis/{id}', [RekapApelController::class, 'showSubdis'])->name('subdis');
        Route::get('/subdis/{id}/pdf', [RekapApelController::class, 'cetakLaporanSubdisPdf'])->name('subdis.pdf');
        Route::get('/anggota/{id}', [RekapApelController::class, 'showAnggota'])->name('anggota');
        Route::post('/update-keterangan/{id}', [RekapApelController::class, 'updateKeterangan'])->name('update-keterangan');
        Route::post('/update-keterangan-bulk', [RekapApelController::class, 'updateKeteranganBulk'])
            ->name('update-keterangan-bulk');
        Route::post('/submit-session/{sessionId}', [RekapApelController::class, 'submitSession'])->name('submit-session');
        Route::post('/verify-session/{sessionId}', [RekapApelController::class, 'verifySession'])->name('verify-session');
        Route::post('/mark-as-done', [RekapApelController::class, 'markAsDone'])->name('mark-as-done');

        Route::get('/laporan-global', [RekapApelController::class, 'showLaporanGlobal'])->name('laporan-global');
        Route::get('/laporan-global/pdf', [RekapApelController::class, 'cetakLaporanGlobalPdf'])->name('laporan-global.pdf');
    });

    Route::prefix('laporan-subdis')->group(function () {
        Route::get('/pdf', [LaporanSubdisController::class, 'cetakPdf'])->name('laporan.subdis.cetakPdf');
    });

    Route::prefix('laporan-personel')->name('laporan.personel.')->group(function () {
        Route::get('keterangan', [LaporanPersonelController::class, 'laporanKeterangan'])->name('keterangan');
        Route::get('keterangan/pdf', [LaporanPersonelController::class, 'cetakPdfKeterangan'])->name('keterangan.pdf');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'index'])->name('edit');
        Route::put('/update-basic', [ProfileController::class, 'updateBasicInfo'])->name('update.basic');
        Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update.password');
    });

    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('piket', [RiwayatPiketController::class, 'index'])->name('piket.index');
    });

    Route::prefix('pengaturan')->name('jam-apel.')->group(function () {
        Route::get('jam-apel', [JamApelController::class, 'index'])->name('index');
        Route::put('jam-apel', [JamApelController::class, 'update'])->name('update');
    });

    Route::post('/logout', [AuthV2Controller::class, 'logout'])->name('logout');
});
