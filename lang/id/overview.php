<?php

// String modul Overview. Sebagian dipakai dari StatistikController@overview
// (label kartu indikator), bukan hanya dari view — kartunya memang dirakit di
// controller karena tiap modul dibaca pada tahun terbarunya sendiri.
return [

    'page_title' => 'Overview Statistik Jakarta Barat',
    'header'     => 'OVERVIEW STATISTIK JAKARTA BARAT',

    // ── Keadaan kosong ────────────────────────────────────────────
    'empty_title' => 'Data belum tersedia',
    'empty_hint'  => 'Ringkasan akan muncul setelah data modul diisi dari portal admin.',

    // ── Kartu indikator ───────────────────────────────────────────
    'cards_hint' => 'Ringkasan data tiap modul Jakarta Barat <strong>klik untuk data lengkap</strong>',

    'card_geografis'    => 'Luas Wilayah',
    'card_kependudukan' => 'Jumlah Penduduk',
    'card_pendidikan'   => 'Jumlah Pelajar',
    'card_kesehatan'    => 'Fasilitas Kesehatan',
    'card_bencana'      => 'Kejadian Bencana',
    'card_kemiskinan'   => 'Penduduk Miskin',
    'card_perekonomian' => 'PDRB Harga Berlaku',
    'card_digital'      => 'Titik JakWiFi & CCTV',

    // Satuan di sebelah angka kartu
    'unit_km2'      => 'km²',
    'unit_jiwa'     => 'jiwa',
    'unit_siswa'    => 'siswa',
    'unit_unit'     => 'unit',
    'unit_kejadian' => 'kejadian',
    'unit_triliun'  => 'triliun',

    // Baris kecil di bawah angka kartu
    'sub_geografis'    => ':kecamatan kecamatan &middot; :kelurahan kelurahan',
    'sub_kependudukan' => 'L :laki &middot; P :perempuan',
    'sub_pendidikan'   => ':jumlah tenaga pendidik',
    'sub_kesehatan'    => ':jumlah tenaga kesehatan',
    'sub_bencana'      => 'terbanyak: :jenis',
    'sub_kemiskinan'   => ':jumlah jiwa',
    'sub_perekonomian' => 'pertumbuhan :persen%',
    'sub_digital'      => ':wifi JakWiFi &middot; :cctv CCTV',

    // ── Bagan ─────────────────────────────────────────────────────
    'chart_penduduk_title'   => 'Penduduk per Kecamatan',
    'chart_penduduk_hint'    => 'Warna kecamatan konsisten dengan modul lain.',
    'chart_pendidikan_title' => 'Partisipasi Sekolah per Jenjang',
    'chart_pendidikan_hint'  => 'APM menghitung siswa yang usianya sesuai jenjang, APK seluruh siswa — selisihnya menunjukkan siswa di luar usia jenjangnya.',
    'chart_faskes_title'     => 'Fasilitas Kesehatan',
    'chart_faskes_hint'      => 'Komposisi menurut jenis fasilitas.',
    'chart_nakes_title'      => 'Tenaga Kesehatan',
    'chart_nakes_hint'       => 'Komposisi menurut profesi.',
    'chart_bencana_title'    => 'Kejadian Bencana menurut Jenis',
    'chart_bencana_hint'     => 'Rekap triwulanan tahun terakhir.',
    'chart_tren_title'       => 'Ekonomi &amp; Kemiskinan Antar-Tahun',
    'chart_tren_hint'        => 'PDRB harga konstan (triliun rupiah) dibanding persentase penduduk miskin. Rentang dibatasi pada tahun yang datanya dimiliki kedua modul.',

    // Label di dalam bagan (dipakai JavaScript)
    'series_penduduk'   => 'Penduduk',
    'series_pdrb'       => 'PDRB Harga Konstan',
    'series_miskin'     => 'Penduduk Miskin (%)',
    'axis_pdrb'         => 'Rp Triliun',
    'axis_miskin'       => '% penduduk miskin',
    'tooltip_jiwa'      => 'jiwa',
    'tooltip_unit'      => 'unit',
    'tooltip_orang'     => 'orang',
    'tooltip_kejadian'  => 'kejadian',

    // ── Tabel ─────────────────────────────────────────────────────
    'table_title' => 'Ringkasan per Kecamatan',
    'table_hint'  => 'Setiap kolom berasal dari modul berbeda pada tahun terbarunya masing-masing, sehingga angkanya tidak selalu setahun. Tanda &mdash; berarti data belum diisi.',
    'table_file'  => 'ringkasan-per-kecamatan',

    'col_kecamatan' => 'Kecamatan',
    'col_luas'      => 'Luas (km²)',
    'col_penduduk'  => 'Penduduk',
    'col_kepadatan' => 'Kepadatan (jiwa/km²)',
    'col_pelajar'   => 'Pelajar',
    'col_faskes'    => 'Faskes',
    'col_miskin'    => 'Penduduk Miskin',
    'col_digital'   => 'WiFi + CCTV',

    // ── Sumber ────────────────────────────────────────────────────
    'source' => 'Sumber: BPS Kota Jakarta Barat &amp; Satu Data Jakarta',

];
