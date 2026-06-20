<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatistikController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('statistik')->name('statistik.')->group(function () {
    Route::get('/geografis',    [StatistikController::class, 'geografis'])->name('geografis');
    Route::get('/iklim',        [StatistikController::class, 'iklim'])->name('iklim');
    Route::get('/kependudukan', [StatistikController::class, 'kependudukan'])->name('kependudukan');
    Route::get('/pendidikan',   [StatistikController::class, 'pendidikan'])->name('pendidikan');
    Route::get('/kesehatan',    [StatistikController::class, 'kesehatan'])->name('kesehatan');
});
