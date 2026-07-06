<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KecamatanController;
use App\Http\Controllers\Admin\GeografisController;
use App\Http\Controllers\Admin\LuasKecamatanController;
use App\Http\Controllers\Admin\IklimController;
use App\Http\Controllers\Admin\KependudukanController;
use App\Http\Controllers\Admin\PendudukKecamatanController;
use App\Http\Controllers\Admin\PendudukKelurahanController;
use App\Http\Controllers\Admin\PendidikanController;
use App\Http\Controllers\Admin\PendidikanKecamatanController;
use App\Http\Controllers\Admin\KesehatanController;
use App\Http\Controllers\Admin\TenagaKesehatanController;
use App\Http\Controllers\Admin\FasilitasKesehatanController;
use App\Http\Controllers\Admin\BencanaController;
use App\Http\Controllers\Admin\TitikBencanaController;
use App\Http\Controllers\Admin\InfrastrukturDigitalController;
use App\Http\Controllers\Admin\JakWifiController;
use App\Http\Controllers\Admin\CctvController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('statistik')->name('statistik.')->group(function () {
    Route::get('/geografis',    [StatistikController::class, 'geografis'])->name('geografis');
    Route::get('/iklim',        [StatistikController::class, 'iklim'])->name('iklim');
    Route::get('/kependudukan', [StatistikController::class, 'kependudukan'])->name('kependudukan');
    Route::get('/kependudukan-api', [StatistikController::class, 'kependudukanApi'])->name('kependudukan-api'); // uji coba API Satu Data
    Route::get('/pendidikan',   [StatistikController::class, 'pendidikan'])->name('pendidikan');
    Route::get('/kesehatan',    [StatistikController::class, 'kesehatan'])->name('kesehatan');
    Route::get('/bencana',      [StatistikController::class, 'bencana'])->name('bencana');
    Route::get('/infrastruktur-digital', [StatistikController::class, 'infrastrukturDigital'])->name('infrastruktur-digital');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Login (hanya untuk yang belum masuk)
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
    });

    // Area admin (wajib login)
    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('kecamatan', KecamatanController::class)
            ->parameters(['kecamatan' => 'kecamatan'])->except('show');

        // Geografis + detail luas kecamatan
        Route::resource('geografis', GeografisController::class)->except('show');
        Route::resource('luas-kecamatan', LuasKecamatanController::class)
            ->parameters(['luas-kecamatan' => 'luasKecamatan'])
            ->only(['create', 'store', 'edit', 'update', 'destroy']);

        // Iklim
        Route::resource('iklim', IklimController::class)->except('show');

        // Kependudukan + detail
        Route::resource('kependudukan', KependudukanController::class)->except('show');
        Route::resource('penduduk-kecamatan', PendudukKecamatanController::class)
            ->parameters(['penduduk-kecamatan' => 'pendudukKecamatan'])
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('penduduk-kelurahan', PendudukKelurahanController::class)
            ->parameters(['penduduk-kelurahan' => 'pendudukKelurahan'])
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
        // Import/Export CSV kelurahan (backup lat/lng agar tak perlu input ulang)
        Route::get('penduduk-kelurahan/template', [PendudukKelurahanController::class, 'template'])->name('penduduk-kelurahan.template');
        Route::get('penduduk-kelurahan/export', [PendudukKelurahanController::class, 'export'])->name('penduduk-kelurahan.export');
        Route::post('penduduk-kelurahan/import', [PendudukKelurahanController::class, 'import'])->name('penduduk-kelurahan.import');

        // Pendidikan + detail
        Route::resource('pendidikan', PendidikanController::class)->except('show');
        Route::resource('pendidikan-kecamatan', PendidikanKecamatanController::class)
            ->parameters(['pendidikan-kecamatan' => 'pendidikanKecamatan'])
            ->only(['create', 'store', 'edit', 'update', 'destroy']);

        // Kesehatan + detail
        Route::resource('kesehatan', KesehatanController::class)->except('show');
        Route::resource('tenaga-kesehatan', TenagaKesehatanController::class)
            ->parameters(['tenaga-kesehatan' => 'tenagaKesehatan'])
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('fasilitas-kesehatan', FasilitasKesehatanController::class)
            ->parameters(['fasilitas-kesehatan' => 'fasilitasKesehatan'])
            ->only(['create', 'store', 'edit', 'update', 'destroy']);

        // Monitor Bencana
        Route::resource('bencana', BencanaController::class)->except('show');
        Route::resource('titik-bencana', TitikBencanaController::class)
            ->parameters(['titik-bencana' => 'titikBencana'])
            ->except('show');

        // Infrastruktur Digital (JakWiFi & CCTV)
        Route::get('infrastruktur-digital', [InfrastrukturDigitalController::class, 'index'])
            ->name('infrastruktur-digital.index');
        Route::resource('jak-wifi', JakWifiController::class)
            ->parameters(['jak-wifi' => 'jakWifi'])
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('cctv', CctvController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
    });
});
