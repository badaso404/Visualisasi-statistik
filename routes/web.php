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
use App\Http\Controllers\Admin\KemiskinanController;
use App\Http\Controllers\Admin\KemiskinanKecamatanController;

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
    Route::get('/kemiskinan',   [StatistikController::class, 'kemiskinan'])->name('kemiskinan');
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
            ->parameters(['kecamatan' => 'kecamatan'])
            ->only(['index', 'store', 'update', 'destroy']);

        // Geografis + detail luas kecamatan (form berupa modal di halaman index)
        Route::resource('geografis', GeografisController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::resource('luas-kecamatan', LuasKecamatanController::class)
            ->parameters(['luas-kecamatan' => 'luasKecamatan'])
            ->only(['store', 'update', 'destroy']);

        // Iklim
        Route::resource('iklim', IklimController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Kependudukan + detail (form berupa modal di halaman index)
        Route::resource('kependudukan', KependudukanController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        // Isi massal: satu tahun, semua kecamatan sekaligus
        Route::get('penduduk-kecamatan/export', [PendudukKecamatanController::class, 'export'])->name('penduduk-kecamatan.export');
        Route::get('penduduk-kecamatan/template', [PendudukKecamatanController::class, 'template'])->name('penduduk-kecamatan.template');
        Route::post('penduduk-kecamatan/import', [PendudukKecamatanController::class, 'import'])->name('penduduk-kecamatan.import');
        Route::get('penduduk-kecamatan/batch', [PendudukKecamatanController::class, 'batch'])->name('penduduk-kecamatan.batch');
        Route::post('penduduk-kecamatan/batch', [PendudukKecamatanController::class, 'batchStore'])->name('penduduk-kecamatan.batch.store');
        Route::resource('penduduk-kecamatan', PendudukKecamatanController::class)
            ->parameters(['penduduk-kecamatan' => 'pendudukKecamatan'])
            ->only(['store', 'update', 'destroy']);
        Route::resource('penduduk-kelurahan', PendudukKelurahanController::class)
            ->parameters(['penduduk-kelurahan' => 'pendudukKelurahan'])
            ->only(['store', 'update', 'destroy']);
        // Import/Export CSV kelurahan (backup lat/lng agar tak perlu input ulang)
        Route::get('penduduk-kelurahan/template', [PendudukKelurahanController::class, 'template'])->name('penduduk-kelurahan.template');
        Route::get('penduduk-kelurahan/export', [PendudukKelurahanController::class, 'export'])->name('penduduk-kelurahan.export');
        Route::post('penduduk-kelurahan/import', [PendudukKelurahanController::class, 'import'])->name('penduduk-kelurahan.import');

        // Pendidikan + detail (form berupa modal di halaman index)
        Route::resource('pendidikan', PendidikanController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::get('pendidikan-kecamatan/export', [PendidikanKecamatanController::class, 'export'])->name('pendidikan-kecamatan.export');
        Route::get('pendidikan-kecamatan/template', [PendidikanKecamatanController::class, 'template'])->name('pendidikan-kecamatan.template');
        Route::post('pendidikan-kecamatan/import', [PendidikanKecamatanController::class, 'import'])->name('pendidikan-kecamatan.import');
        Route::get('pendidikan-kecamatan/batch', [PendidikanKecamatanController::class, 'batch'])->name('pendidikan-kecamatan.batch');
        Route::post('pendidikan-kecamatan/batch', [PendidikanKecamatanController::class, 'batchStore'])->name('pendidikan-kecamatan.batch.store');
        Route::resource('pendidikan-kecamatan', PendidikanKecamatanController::class)
            ->parameters(['pendidikan-kecamatan' => 'pendidikanKecamatan'])
            ->only(['store', 'update', 'destroy']);

        // Kesehatan + detail (form berupa modal di halaman index)
        Route::resource('kesehatan', KesehatanController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::get('tenaga-kesehatan/export', [TenagaKesehatanController::class, 'export'])->name('tenaga-kesehatan.export');
        Route::get('tenaga-kesehatan/template', [TenagaKesehatanController::class, 'template'])->name('tenaga-kesehatan.template');
        Route::post('tenaga-kesehatan/import', [TenagaKesehatanController::class, 'import'])->name('tenaga-kesehatan.import');
        Route::get('tenaga-kesehatan/batch', [TenagaKesehatanController::class, 'batch'])->name('tenaga-kesehatan.batch');
        Route::post('tenaga-kesehatan/batch', [TenagaKesehatanController::class, 'batchStore'])->name('tenaga-kesehatan.batch.store');
        Route::resource('tenaga-kesehatan', TenagaKesehatanController::class)
            ->parameters(['tenaga-kesehatan' => 'tenagaKesehatan'])
            ->only(['store', 'update', 'destroy']);
        Route::get('fasilitas-kesehatan/export', [FasilitasKesehatanController::class, 'export'])->name('fasilitas-kesehatan.export');
        Route::get('fasilitas-kesehatan/template', [FasilitasKesehatanController::class, 'template'])->name('fasilitas-kesehatan.template');
        Route::post('fasilitas-kesehatan/import', [FasilitasKesehatanController::class, 'import'])->name('fasilitas-kesehatan.import');
        Route::get('fasilitas-kesehatan/batch', [FasilitasKesehatanController::class, 'batch'])->name('fasilitas-kesehatan.batch');
        Route::post('fasilitas-kesehatan/batch', [FasilitasKesehatanController::class, 'batchStore'])->name('fasilitas-kesehatan.batch.store');
        Route::resource('fasilitas-kesehatan', FasilitasKesehatanController::class)
            ->parameters(['fasilitas-kesehatan' => 'fasilitasKesehatan'])
            ->only(['store', 'update', 'destroy']);

        // Monitor Bencana
        Route::get('bencana/export',   [BencanaController::class, 'export'])->name('bencana.export');
        Route::get('bencana/template', [BencanaController::class, 'template'])->name('bencana.template');
        Route::post('bencana/import',  [BencanaController::class, 'import'])->name('bencana.import');
        Route::resource('bencana', BencanaController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::get('titik-bencana/export',   [TitikBencanaController::class, 'export'])->name('titik-bencana.export');
        Route::get('titik-bencana/template', [TitikBencanaController::class, 'template'])->name('titik-bencana.template');
        Route::post('titik-bencana/import',  [TitikBencanaController::class, 'import'])->name('titik-bencana.import');
        Route::resource('titik-bencana', TitikBencanaController::class)
            ->parameters(['titik-bencana' => 'titikBencana'])
            ->only(['index', 'store', 'update', 'destroy']);

        // Kemiskinan + detail per kecamatan (form berupa modal di halaman index)
        Route::resource('kemiskinan', KemiskinanController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::get('kemiskinan-kecamatan/export', [KemiskinanKecamatanController::class, 'export'])->name('kemiskinan-kecamatan.export');
        Route::get('kemiskinan-kecamatan/template', [KemiskinanKecamatanController::class, 'template'])->name('kemiskinan-kecamatan.template');
        Route::post('kemiskinan-kecamatan/import', [KemiskinanKecamatanController::class, 'import'])->name('kemiskinan-kecamatan.import');
        Route::get('kemiskinan-kecamatan/batch', [KemiskinanKecamatanController::class, 'batch'])->name('kemiskinan-kecamatan.batch');
        Route::post('kemiskinan-kecamatan/batch', [KemiskinanKecamatanController::class, 'batchStore'])->name('kemiskinan-kecamatan.batch.store');
        Route::resource('kemiskinan-kecamatan', KemiskinanKecamatanController::class)
            ->parameters(['kemiskinan-kecamatan' => 'kemiskinanKecamatan'])
            ->only(['store', 'update', 'destroy']);

        // Infrastruktur Digital (JakWiFi & CCTV)
        Route::get('infrastruktur-digital', [InfrastrukturDigitalController::class, 'index'])
            ->name('infrastruktur-digital.index');
        Route::get('jak-wifi/export', [JakWifiController::class, 'export'])->name('jak-wifi.export');
        Route::get('jak-wifi/template', [JakWifiController::class, 'template'])->name('jak-wifi.template');
        Route::post('jak-wifi/import', [JakWifiController::class, 'import'])->name('jak-wifi.import');
        Route::get('jak-wifi/batch', [JakWifiController::class, 'batch'])->name('jak-wifi.batch');
        Route::post('jak-wifi/batch', [JakWifiController::class, 'batchStore'])->name('jak-wifi.batch.store');
        Route::resource('jak-wifi', JakWifiController::class)
            ->parameters(['jak-wifi' => 'jakWifi'])
            ->only(['store', 'update', 'destroy']);
        Route::get('cctv/export', [CctvController::class, 'export'])->name('cctv.export');
        Route::get('cctv/template', [CctvController::class, 'template'])->name('cctv.template');
        Route::post('cctv/import', [CctvController::class, 'import'])->name('cctv.import');
        Route::get('cctv/batch', [CctvController::class, 'batch'])->name('cctv.batch');
        Route::post('cctv/batch', [CctvController::class, 'batchStore'])->name('cctv.batch.store');
        Route::resource('cctv', CctvController::class)
            ->only(['store', 'update', 'destroy']);
    });
});
