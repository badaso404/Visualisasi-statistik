<?php

namespace App\Services\Statistik;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Opsi HTTP bersama untuk semua client API statistik.
 *
 * Proxy diletakkan di sini, bukan di tiap pemanggil: server produksi Pemkot
 * tidak bisa keluar internet langsung, jadi begitu STATISTIK_HTTP_PROXY diisi
 * semua sumber (BPS, Satu Data, DSDA) ikut lewat proxy tanpa ubah kode.
 */
abstract class ApiClient
{
    protected function http(int $timeout): PendingRequest
    {
        $options = ['verify' => (bool) config('statistik.http.verify', true)];

        // Guzzle menolak proxy string kosong, jadi hanya dipasang bila terisi.
        if ($proxy = config('statistik.http.proxy')) {
            $options['proxy'] = $proxy;
        }

        return Http::withOptions($options)
            ->withHeaders(['User-Agent' => config('statistik.http.user_agent')])
            ->timeout($timeout)
            ->retry(2, 500);
    }
}
