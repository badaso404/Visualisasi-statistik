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

    /*
    |--------------------------------------------------------------------------
    | Palet warna statistik
    |--------------------------------------------------------------------------
    |
    | Sumber tunggal untuk PHP maupun JavaScript. Palet ini sebelumnya hanya
    | ada sebagai literal JS di statistik/partials/warna-kecamatan.blade.php,
    | sehingga komponen tabel yang merender warnanya di sisi PHP terpaksa
    | menyalin ulang daftar yang sama. Sekarang partial itu memancarkan isi
    | berkas ini, dan komponen tabel membacanya langsung — satu tempat untuk
    | diubah, dua konsumen yang selalu sinkron.
    |
    | Palet kategorikal colorblind-safe & tervalidasi (lihat panduan dataviz).
    |
    */

    'warna' => [

        // Dipakai untuk deret yang BUKAN kecamatan (per bulan, per jenis) dan
        // untuk mewarnai kolom pada komponen <x-statistik.tabel>.
        'kategori' => [
            '#2a78d6',   // biru
            '#1baf7a',   // teal
            '#eda100',   // amber
            '#008300',   // hijau
            '#4a3aa7',   // ungu
            '#e34948',   // merah
            '#e87ba4',   // pink
            '#eb6834',   // oranye
        ],

        // Warna khas per kecamatan — agar Cengkareng selalu amber di modul
        // mana pun. Kunci ditulis huruf besar; pencariannya case-insensitive.
        'kecamatan' => [
            'KALIDERES'         => '#e87ba4',
            'CENGKARENG'        => '#eda100',
            'KEBON JERUK'       => '#e34948',
            'KEMBANGAN'         => '#4a3aa7',
            'GROGOL PETAMBURAN' => '#2a78d6',
            'PALMERAH'          => '#008300',
            'TAMBORA'           => '#1baf7a',
            'TAMAN SARI'        => '#eb6834',
        ],

        // Dipakai saat sebuah nama tidak ada di daftar di atas.
        'netral' => '#9e9e9e',
    ],

];
