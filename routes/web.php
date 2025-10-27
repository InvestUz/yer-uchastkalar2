<?php

use App\Http\Controllers\YerSotuvController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [YerSotuvController::class, 'index'])->name('yer-sotuvlar.index');
Route::get('/yer/{lot_raqami}', [YerSotuvController::class, 'show'])->name('yer-sotuvlar.show');
