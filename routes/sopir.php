<?php

use App\Http\Controllers\Sopir\DashboardController;
use App\Http\Controllers\Sopir\RuteController;
use App\Http\Controllers\Sopir\JadwalController;
use App\Http\Controllers\Sopir\RiwayatController;
use App\Http\Controllers\Sopir\TpsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:sopir'])->group(function () {
    // Dashboard
    Route::get('/sopir', [DashboardController::class, 'index'])->name('sopir');

    // Rute Actions
    Route::post('/sopir/rute/{ruteHarian}/mulai', [RuteController::class, 'mulaiRute'])->name('sopir.rute.mulai');
    Route::post('/sopir/rute/tujuan/{ruteTujuan}/tiba', [RuteController::class, 'tiba'])->name('sopir.rute.tiba');
    Route::post('/sopir/rute/tujuan/{ruteTujuan}/selesai', [RuteController::class, 'selesai'])->name('sopir.rute.selesai');
    Route::post('/sopir/rute/tujuan/{ruteTujuan}/skip', [RuteController::class, 'skip'])->name('sopir.rute.skip');
    Route::post('/sopir/rute/tujuan/{ruteTujuan}/mulai-manual', [RuteController::class, 'mulaiManual'])->name('sopir.rute.mulaiManual');
    Route::post('/sopir/rute/{ruteHarian}/selesai-rute', [RuteController::class, 'selesaiRute'])->name('sopir.rute.selesaiRute');

    // Jadwal (read-only)
    Route::get('/sopir/jadwal', [JadwalController::class, 'index'])->name('sopir.jadwal.index');

    // Riwayat
    Route::get('/sopir/riwayat', [RiwayatController::class, 'index'])->name('sopir.riwayat.index');
    Route::get('/sopir/riwayat/{ruteHarian}', [RiwayatController::class, 'show'])->name('sopir.riwayat.show');

    // TPS
    Route::resource('/sopir/tps', TpsController::class)->names('sopir.tps');
});
