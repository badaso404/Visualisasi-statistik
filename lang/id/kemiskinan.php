<?php

// String modul Kemiskinan.
// Bagian per-kecamatan di view sedang dinonaktifkan (BPS hanya merilis sampai
// level kota), jadi labelnya sengaja belum dipindah ke sini — biar tidak ada
// kunci yang menganggur.
return [

    'page_title' => 'Kemiskinan Jakarta Barat',
    'header'     => 'KEMISKINAN JAKARTA BARAT :tahun',

    // ── Kartu indikator ───────────────────────────────────────────
    'card_jumlah'     => 'Penduduk Miskin',
    'card_jumlah_sub' => 'jiwa',
    'card_persen'     => 'Persentase',
    'card_persen_sub' => 'dari total penduduk',
    'card_garis'      => 'Garis Kemiskinan',
    'card_garis_sub'  => 'per kapita/bulan',
    'card_p1'         => 'Indeks Kedalaman (P1)',
    'card_p1_sub'     => 'jarak ke garis miskin',
    'card_p2'         => 'Indeks Keparahan (P2)',
    'card_p2_sub'     => 'ketimpangan antar-miskin',

    // ── Bagan tren ────────────────────────────────────────────────
    'chart_miskin_title' => 'Tren Jumlah Penduduk Miskin (jiwa)',
    'chart_persen_title' => 'Tren Persentase Penduduk Miskin (%)',
    'chart_garis_title'  => 'Tren Garis Kemiskinan (Rp/kapita/bulan)',
    'chart_indeks_title' => 'Tren Indeks Kedalaman (P1) & Keparahan (P2)',

    // Nama seri (dipakai JavaScript)
    'series_miskin' => 'Penduduk Miskin',
    'series_persen' => 'Persentase',
    'series_garis'  => 'Garis Kemiskinan',
    'series_p1'     => 'P1 (Kedalaman)',
    'series_p2'     => 'P2 (Keparahan)',

    // ── Tabel ─────────────────────────────────────────────────────
    'table_title' => 'Ringkasan Antar-Tahun',
    'table_file'  => 'kemiskinan-antar-tahun',

    'col_tahun'  => 'Tahun',
    'col_jumlah' => 'Penduduk Miskin',
    'col_persen' => 'Persentase',
    'col_garis'  => 'Garis Kemiskinan',
    'col_p1'     => 'P1',
    'col_p2'     => 'P2',

    'empty'  => 'Belum ada data kemiskinan untuk ditampilkan.',
    'source' => 'Sumber: :sumber',

];
