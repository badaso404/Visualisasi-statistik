<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Opsi HTTP bersama
    |--------------------------------------------------------------------------
    |
    | Dipakai semua client API statistik. Server produksi Pemkot berada di balik
    | proxy dan tidak bisa keluar internet langsung — isi STATISTIK_HTTP_PROXY
    | (atau HTTPS_PROXY) di sana, dan biarkan kosong saat development lokal.
    |
    | user_agent wajib menyerupai browser: WAF BPS menolak request tanpa itu.
    |
    */

    'http' => [
        'proxy'      => env('STATISTIK_HTTP_PROXY', env('HTTPS_PROXY')),
        'verify'     => env('STATISTIK_HTTP_VERIFY', true),
        'user_agent' => env('STATISTIK_USER_AGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) '
            . 'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36'),
    ],

    /*
    |--------------------------------------------------------------------------
    | BPS WebAPI — webapi.bps.go.id
    |--------------------------------------------------------------------------
    |
    | domain 3174 = Kota Jakarta Barat. Dipakai seeder kemiskinan & kesehatan.
    | Key didapat dari registrasi akun di webapi.bps.go.id.
    |
    */

    'bps' => [
        'base_url' => env('BPS_BASE_URL', 'https://webapi.bps.go.id/v1/api'),
        'key'      => env('BPS_API_KEY'),
        'domain'   => env('BPS_DOMAIN', '3174'),
        'timeout'  => (int) env('BPS_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Satu Data Jakarta — ws.jakarta.go.id
    |--------------------------------------------------------------------------
    |
    | Dataset piramida penduduk (~60rb baris) → di-cache agar tidak ditarik
    | tiap request. Tanpa API key.
    |
    */

    'satudata' => [
        'base_url'  => env('SATUDATA_BASE_URL', 'https://ws.jakarta.go.id/gateway/DataPortalSatuDataJakarta/1.0/satudata'),
        'timeout'   => (int) env('SATUDATA_TIMEOUT', 90),
        'cache_ttl' => (int) env('SATUDATA_CACHE_TTL', 21600), // 6 jam
    ],

    /*
    |--------------------------------------------------------------------------
    | DSDA Posko Banjir — Tinggi Muka Air (data live)
    |--------------------------------------------------------------------------
    |
    | Satu-satunya sumber yang memang harus live. TTL pendek, dan hasil gagal
    | tidak pernah di-cache.
    |
    */

    'dsda' => [
        'url'       => env('DSDA_TMA_URL', 'https://poskobanjirdsda.jakarta.go.id/datatma.json'),
        'timeout'   => (int) env('DSDA_TIMEOUT', 8),
        'cache_ttl' => (int) env('DSDA_CACHE_TTL', 300), // 5 menit
    ],

];
