<?php

// String modul Perekonomian.
// Nama lapangan usaha ($row->nama_sektor) datang dari BPS dan disimpan di
// basis data, jadi tetap berbahasa Indonesia di kedua versi.
return [

    'page_title' => 'Perekonomian Jakarta Barat',
    'header'     => 'PEREKONOMIAN JAKARTA BARAT :tahun',

    // ── Kartu indikator ───────────────────────────────────────────
    'card_adhb'       => 'PDRB Harga Berlaku',
    'card_adhb_sub'   => 'nilai ekonomi tahun berjalan',
    'card_adhk'       => 'PDRB Harga Konstan',
    'card_adhk_sub'   => 'tahun dasar 2010',
    'card_tumbuh'     => 'Pertumbuhan Ekonomi',
    'card_tumbuh_sub' => 'atas dasar harga konstan',
    'card_deflator'   => 'Indeks Implisit',
    'card_deflator_sub' => 'tingkat harga terhadap 2010',

    // ── Bagan ─────────────────────────────────────────────────────
    'chart_tren_title'    => 'Tren PDRB (Triliun Rupiah)',
    'chart_tren_hint'     => 'Rentang penuh :dari–:sampai; harga konstan menunjukkan pertumbuhan riil.',
    'chart_sektor_title'  => 'Struktur Ekonomi menurut Lapangan Usaha :tahun',
    'chart_sektor_hint'   => 'Kontribusi tiap kategori terhadap PDRB atas dasar harga berlaku.',
    'series_adhb'         => 'Harga Berlaku',
    'series_adhk'         => 'Harga Konstan 2010',
    'series_distribusi'   => 'Distribusi',

    // ── Tabel lapangan usaha ──────────────────────────────────────
    'table_sektor_title' => 'Lapangan Usaha Terbesar :tahun',
    'table_sektor_file'  => 'lapangan-usaha-:tahun',

    'col_kategori'  => 'Kategori',
    'col_sektor'    => 'Lapangan Usaha',
    'col_adhb'      => 'ADHB (Rp Miliar)',
    'col_distribusi'=> 'Distribusi',
    'col_tumbuh'    => 'Pertumbuhan',

    'row_lainnya' => 'Lainnya (:jumlah lapangan usaha)',
    'row_total'   => 'PDRB Jakarta Barat',
    'catatan_lainnya' => '*Pertumbuhan baris Lainnya adalah rata-rata tertimbang menurut ADHB, bukan angka resmi BPS. Rincian tiap lapangan usaha tersedia pada grafik di atas.',

    // ── Tabel antar-tahun ─────────────────────────────────────────
    'table_tahun_title' => 'Ringkasan Antar-Tahun',
    'table_tahun_file'  => 'perekonomian-antar-tahun',

    'col_tahun'      => 'Tahun',
    'col_adhb_t'     => 'PDRB ADHB (Rp Triliun)',
    'col_adhk_t'     => 'PDRB ADHK (Rp Triliun)',

    'source' => 'Sumber: :sumber',

];
