<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Tujuan setelah pengguna terautentikasi. Satu-satunya area yang butuh login
     * adalah panel admin, jadi bawaan Laravel '/home' (route yang tidak pernah
     * dibuat di project ini) diarahkan ke sana.
     *
     * @var string
     */
    public const HOME = '/admin';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Percobaan login dibatasi per (email + IP), bukan IP saja: beberapa
        // operator di satu kantor berbagi satu IP publik, dan penguncian
        // berbasis IP akan membuat mereka saling mengunci.
        RateLimiter::for('login', function (Request $request) {
            $kunci = mb_strtolower((string) $request->input('email')) . '|' . $request->ip();

            return [
                Limit::perMinute(5)->by($kunci),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
