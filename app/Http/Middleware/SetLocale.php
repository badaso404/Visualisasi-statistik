<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membaca locale dari sesi dan menerapkannya ke aplikasi.
 *
 * Locale yang didukung dibatasi secara eksplisit agar parameter URL
 * tidak bisa dipakai untuk meng-inject nilai sembarang.
 */
class SetLocale
{
    /** Locale yang diizinkan. */
    private const SUPPORTED = ['id', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));

        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
