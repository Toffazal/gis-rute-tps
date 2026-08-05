<?php

use App\Http\Controllers\Sopir\DashboardController;
use App\Http\Controllers\Sopir\TpsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:sopir'])->group(function () {
    Route::get('/sopir', [DashboardController::class, 'index'])->name('sopir');
    Route::resource('/sopir/tps', TpsController::class)->names('sopir.tps');
});
