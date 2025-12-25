<?php

use App\Http\Controllers\YerSotuvController;
use App\Http\Controllers\GlobalQoldiqController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/captcha/image', [LoginController::class, 'captcha'])->name('captcha.image');
Route::post('/captcha/refresh', [LoginController::class, 'refreshCaptcha'])->name('captcha.refresh');

// Protected Routes - Require Authentication
Route::middleware(['auth', 'role'])->group(function () {
    // Main pages
    Route::get('/umumiy', [YerSotuvController::class, 'index'])->name('yer-sotuvlar.index');
    Route::get('/svod3', [YerSotuvController::class, 'svod3'])->name('yer-sotuvlar.svod3');
    Route::get('/ruyxat', [YerSotuvController::class, 'list'])->name('yer-sotuvlar.list');
    Route::get('/', [YerSotuvController::class, 'monitoring'])->name('yer-sotuvlar.monitoring');
    Route::get('/get-period-options', [YerSotuvController::class, 'getPeriodOptions'])->name('yer-sotuvlar.period-options');
    Route::get('/monitoring_mirzayev', [YerSotuvController::class, 'monitoring_mirzayev'])->name('yer-sotuvlar.monitoring_mirzayev');
    Route::get('/yigma-malumot', [YerSotuvController::class, 'yigmaMalumot'])->name('yer-sotuvlar.yigma');

    // Export routes
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/full', [ExportController::class, 'exportToExcel'])->name('full');
        Route::get('/summary', [ExportController::class, 'exportWithFaktSummary'])->name('summary');
        Route::get('/filtered', [ExportController::class, 'exportFiltered'])->name('filtered');
    });

    // Qoldiq management - Super Admin only
    Route::prefix('qoldiq')->name('qoldiq.')->middleware('role:super_admin')->group(function () {
        Route::get('/', [GlobalQoldiqController::class, 'index'])->name('index');
        Route::get('/create', [GlobalQoldiqController::class, 'create'])->name('create');
        Route::post('/', [GlobalQoldiqController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [GlobalQoldiqController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GlobalQoldiqController::class, 'update'])->name('update');
        Route::delete('/{id}', [GlobalQoldiqController::class, 'destroy'])->name('destroy');
    });

    // Create/Edit routes - Super Admin only
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/yer/create', [YerSotuvController::class, 'create'])->name('yer-sotuvlar.create');
        Route::post('/yer', [YerSotuvController::class, 'store'])->name('yer-sotuvlar.store');
        Route::get('/yer/{lot_raqami}/edit', [YerSotuvController::class, 'edit'])->name('yer-sotuvlar.edit');
        Route::put('/yer/{lot_raqami}', [YerSotuvController::class, 'update'])->name('yer-sotuvlar.update');
    });

    // View route - All authenticated users
    Route::get('/yer/{lot_raqami}', [YerSotuvController::class, 'show'])->name('yer-sotuvlar.show');
});
