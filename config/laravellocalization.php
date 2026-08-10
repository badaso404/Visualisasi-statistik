<?php

/*
|--------------------------------------------------------------------------
| mcamara/laravel-localization
|--------------------------------------------------------------------------
|
| Config bawaan package berisi ~300 baris daftar bahasa dunia yang semuanya
| dikomentari. Di sini hanya disalin kunci yang benar-benar dibaca package,
| supaya jelas apa yang berlaku tanpa perlu menyisir file panjang.
|
*/

return [

    // Urutannya menentukan bahasa default: entri pertama dipakai kalau URL
    // tidak membawa prefix. Bahasa Indonesia lebih dulu karena ini portal
    // pemerintah daerah — Inggris sifatnya pelengkap.
    'supportedLocales' => [
        'id' => ['name' => 'Indonesian', 'script' => 'Latn', 'native' => 'Bahasa Indonesia', 'regional' => 'id_ID'],
        'en' => ['name' => 'English',    'script' => 'Latn', 'native' => 'English',          'regional' => 'en_GB'],
    ],

    // Urutan tampil di pemilih bahasa (dipakai getSupportedLocales()).
    'localesOrder' => ['id', 'en'],

    // Dimatikan supaya bahasa yang tampil selalu bisa ditebak dari URL saja.
    // Kalau dinyalakan, pengunjung dengan browser berbahasa Inggris akan
    // dilempar ke /en pada kunjungan pertama — perilaku yang bikin bingung
    // saat menguji dan bikin cache halaman tidak konsisten.
    'useAcceptLanguageHeader' => false,

    // Inti dari strategi "tambahan, bukan pengganti":
    //   /statistik/iklim      → Indonesia (URL lama, tidak berubah sama sekali)
    //   /en/statistik/iklim   → Inggris   (URL baru)
    // Tanpa ini semua tautan lama akan dialihkan ke /id/... dan link yang
    // terlanjur disebar jadi ikut berubah.
    'hideDefaultLocaleInURL' => true,

    // Tidak dipakai: tidak ada segmen URL yang perlu dipetakan (mis. 'en-GB'
    // menjadi 'uk').
    'localesMapping' => [],

    // Akhiran locale untuk setlocale() PHP. Biarkan bawaan.
    'utf8suffix' => env('LARAVELLOCALIZATION_UTF8SUFFIX', '.UTF-8'),

    // Path yang tidak boleh disentuh middleware. Panel admin berbahasa
    // Indonesia saja, jadi sekalian dikecualikan di sini selain tidak
    // dimasukkan ke grup route berprefix.
    'urlsIgnored' => ['/admin', '/admin/*'],

    // Method non-GET tidak pernah dialihkan: redirect akan mengubahnya jadi
    // GET dan body form-nya hilang.
    'httpMethodsIgnored' => ['POST', 'PUT', 'PATCH', 'DELETE'],

];
