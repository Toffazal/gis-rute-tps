<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TpsController;
use App\Http\Controllers\Admin\AngkutanController;
use App\Http\Controllers\Admin\SopirController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin');
    Route::resource('/admin/tps', TpsController::class)->names('admin.tps');
    Route::resource('/admin/angkutan', AngkutanController::class)->names('admin.angkutan');
    Route::resource('/admin/sopir', SopirController::class)->names('admin.sopir');
    Route::resource('/admin/website', WebsiteController::class)->only(['index', 'update'])->names('admin.website');
    Route::get('/admin/jadwal/export', [JadwalController::class, 'export'])->name('admin.jadwal.export');
    Route::get('/admin/jadwal/template', [JadwalController::class, 'downloadTemplate'])->name('admin.jadwal.template');
    Route::post('/admin/jadwal/import', [JadwalController::class, 'import'])->name('admin.jadwal.import');
    Route::resource('/admin/jadwal', JadwalController::class)->names('admin.jadwal');

    // Monitoring Rute Sopir
    Route::get('/admin/riwayat', [RiwayatController::class, 'index'])->name('admin.riwayat.index');
    Route::get('/admin/riwayat/{ruteHarian}', [RiwayatController::class, 'show'])->name('admin.riwayat.show');
});
