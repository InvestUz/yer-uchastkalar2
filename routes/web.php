<?php

use App\Http\Controllers\YerSotuvController;
use App\Http\Controllers\GlobalQoldiqController;
use Illuminate\Support\Facades\Route;

// Main pages
Route::get('/', [YerSotuvController::class, 'index'])->name('yer-sotuvlar.index');
Route::get('/svod3', [YerSotuvController::class, 'svod3'])->name('yer-sotuvlar.svod3');
Route::get('/ruyxat', [YerSotuvController::class, 'list'])->name('yer-sotuvlar.list');
Route::get('/monitoring', [YerSotuvController::class, 'monitoring'])->name('yer-sotuvlar.monitoring');
Route::get('/monitoring_mirzayev', [YerSotuvController::class, 'monitoring_mirzayev'])->name('yer-sotuvlar.monitoring_mirzayev');

// Qoldiq management
Route::prefix('qoldiq')->name('qoldiq.')->group(function () {
    Route::get('/', [GlobalQoldiqController::class, 'index'])->name('index');
    Route::get('/create', [GlobalQoldiqController::class, 'create'])->name('create');
    Route::post('/', [GlobalQoldiqController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [GlobalQoldiqController::class, 'edit'])->name('edit');
    Route::put('/{id}', [GlobalQoldiqController::class, 'update'])->name('update');
    Route::delete('/{id}', [GlobalQoldiqController::class, 'destroy'])->name('destroy');
});

// IMPORTANT: Put /create BEFORE /{lot_raqami} routes
Route::get('/yer/create', [YerSotuvController::class, 'create'])->name('yer-sotuvlar.create');
Route::post('/yer', [YerSotuvController::class, 'store'])->name('yer-sotuvlar.store');

// Dynamic routes (these should come AFTER specific routes)
Route::get('/yer/{lot_raqami}', [YerSotuvController::class, 'show'])->name('yer-sotuvlar.show');
Route::get('/yer/{lot_raqami}/edit', [YerSotuvController::class, 'edit'])->name('yer-sotuvlar.edit');
Route::put('/yer/{lot_raqami}', [YerSotuvController::class, 'update'])->name('yer-sotuvlar.update');
