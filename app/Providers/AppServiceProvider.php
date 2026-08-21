<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bawaan Laravel 10 adalah tautan halaman bergaya Tailwind, sedangkan
        // panel admin memakai Bootstrap 5 — tanpa ini navigasi halaman pada
        // tabel fasilitas umum (satu-satunya tabel admin yang berhalaman)
        // tampil sebagai tautan telanjang tanpa gaya.
        Paginator::useBootstrapFive();
    }
}
