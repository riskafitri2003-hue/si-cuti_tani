<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PengajuanCutiController;
use App\Http\Controllers\SaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master data pegawai - hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('pegawai', PegawaiController::class)->except(['show']);
        Route::get('/pegawai/{pegawai}/create-account', [PegawaiController::class, 'createAccountForm'])->name('pegawai.create-account.form');
        Route::post('/pegawai/{pegawai}/create-account', [PegawaiController::class, 'createAccount'])->name('pegawai.create-account.store');

        // Kelola saldo cuti
        Route::get('/kelola-saldo', [PegawaiController::class, 'kelolaSaldo'])->name('pegawai.kelola-saldo');
        Route::post('/kelola-saldo', [PegawaiController::class, 'updateSaldo'])->name('pegawai.update-saldo');

        // Kelola akun pegawai
        Route::get('/kelola-akun', [PegawaiController::class, 'akunIndex'])->name('pegawai.kelola-akun');
        Route::post('/kelola-akun/{pegawai}/store', [PegawaiController::class, 'akunStore'])->name('pegawai.akun.store');
        Route::put('/kelola-akun/{pegawai}/update', [PegawaiController::class, 'akunUpdate'])->name('pegawai.akun.update');
        Route::delete('/kelola-akun/{pegawai}/destroy', [PegawaiController::class, 'akunDestroy'])->name('pegawai.akun.destroy');
    });

    // Laporan - hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });

    // Rekap saran/masukan - hanya admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/rekap-saran', [SaranController::class, 'index'])->name('saran.index');
    });

    // Pengajuan cuti
    Route::get('/cuti', [PengajuanCutiController::class, 'index'])->name('cuti.index');
    Route::get('/cuti/create', [PengajuanCutiController::class, 'create'])->name('cuti.create');
    Route::post('/cuti', [PengajuanCutiController::class, 'store'])->name('cuti.store');
    Route::get('/cuti/{cuti}', [PengajuanCutiController::class, 'show'])->name('cuti.show');
    Route::get('/cuti/{cuti}/dokumen', [PengajuanCutiController::class, 'downloadDokumen'])->name('cuti.dokumen');
    Route::post('/cuti/{cuti}/kirim-email', [PengajuanCutiController::class, 'kirimEmail'])->name('cuti.kirim-email');
    Route::post('/cuti/{cuti}/saran', [PengajuanCutiController::class, 'storeSaran'])->name('cuti.saran.store');

    // Approval Atasan Langsung
    Route::middleware('role:atasan_langsung')->group(function () {
        Route::get('/cuti/{cuti}/atasan-langsung', [PengajuanCutiController::class, 'approveAtasanLangsungForm'])->name('cuti.atasan-langsung.form');
        Route::post('/cuti/{cuti}/atasan-langsung', [PengajuanCutiController::class, 'approveAtasanLangsung'])->name('cuti.atasan-langsung.store');
    });

    // Approval Kasubag Umum
    Route::middleware('role:kasubag')->group(function () {
        Route::get('/cuti/{cuti}/kasubag', [PengajuanCutiController::class, 'approveKasubagForm'])->name('cuti.kasubag.form');
        Route::post('/cuti/{cuti}/kasubag', [PengajuanCutiController::class, 'approveKasubag'])->name('cuti.kasubag.store');
    });

    // Approval Sekretaris
    Route::middleware('role:sekretaris')->group(function () {
        Route::get('/cuti/{cuti}/sekretaris', [PengajuanCutiController::class, 'approveSekretarisForm'])->name('cuti.sekretaris.form');
        Route::post('/cuti/{cuti}/sekretaris', [PengajuanCutiController::class, 'approveSekretaris'])->name('cuti.sekretaris.store');
    });

    // Approval Kepala Dinas
    Route::middleware('role:kepala_dinas')->group(function () {
        Route::get('/cuti/{cuti}/kepala-dinas', [PengajuanCutiController::class, 'approveKepalaDinasForm'])->name('cuti.kepala-dinas.form');
        Route::post('/cuti/{cuti}/kepala-dinas', [PengajuanCutiController::class, 'approveKepalaDinas'])->name('cuti.kepala-dinas.store');
    });

    // Approval Wali Kota
    Route::middleware('role:walikota')->group(function () {
        Route::get('/cuti/{cuti}/walikota', [PengajuanCutiController::class, 'approveWalikotaForm'])->name('cuti.walikota.form');
        Route::post('/cuti/{cuti}/walikota', [PengajuanCutiController::class, 'approveWalikota'])->name('cuti.walikota.store');
    });
});
