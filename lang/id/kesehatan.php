<?php

// String modul Kesehatan.
// Nama jenis fasilitas dan profesi tenaga kesehatan tidak ditulis ulang di
// sini — dipakai bersama modul Overview lewat common.faskes / common.nakes.
return [

    'page_title' => 'Kesehatan Jakarta Barat',
    'header'     => 'KESEHATAN JAKARTA BARAT :tahun',

    'sumber_default' => 'Dinas Kesehatan Jakarta Barat',

    // ── Kartu ringkasan (tampilan seluruh kota) ───────────────────
    'card_tt_label'    => 'Tempat Tidur Rumah Sakit',
    'card_tt_desc'     => 'Total ketersediaan TT di RS',
    'card_rs_label'    => 'Total Rumah Sakit',
    'card_rs_desc'     => 'RS Umum, Khusus & Bersalin',
    'card_nakes_label' => 'Total Tenaga Kesehatan',
    'card_nakes_desc'  => 'Terbanyak: :nama',
    'card_fas_label'   => 'Total Fasilitas Kesehatan',
    'card_fas_desc'    => 'RS, Puskesmas, Klinik & Posyandu',

    // ── Kartu saat satu kecamatan dipilih (diisi JavaScript) ──────
    // Bagan tenaga dan bagan fasilitas mengisi keempat kartu dengan metrik
    // yang berbeda, jadi labelnya dua set.
    'kc_dokter'        => 'Dokter',
    'kc_dokter_desc'   => 'Dokter di :nama',
    'kc_perawat'       => 'Perawat',
    'kc_perawat_desc'  => 'Perawat di :nama',
    'kc_bidan'         => 'Bidan',
    'kc_bidan_desc'    => 'Bidan di :nama',
    'kc_nakes_desc'    => 'Farmasi :farmasi · Gizi :gizi',

    'kc_rs'            => 'Rumah Sakit',
    'kc_rs_desc'       => 'Unit RS di :nama',
    'kc_pkm'           => 'Puskesmas',
    'kc_pkm_desc'      => 'Unit puskesmas di :nama',
    'kc_klinik'        => 'Klinik Kesehatan',
    'kc_klinik_desc'   => 'Klinik di :nama',
    'kc_fas'           => 'Total Fasilitas',
    'kc_fas_desc'      => 'Posyandu :jumlah unit',

    // ── Bagan ─────────────────────────────────────────────────────
    'chart_tenaga_title'    => 'Tenaga Kesehatan per Kecamatan',
    'chart_tenaga_sub'      => 'Distribusi personel medis aktif — :tahun · klik batang untuk rincian',
    'chart_fasilitas_title' => 'Fasilitas Kesehatan per Kecamatan',
    'chart_fasilitas_sub'   => 'Jumlah unit fasilitas — :tahun · klik batang untuk rincian',
    'badge_data'            => 'Data :tahun',
    'detail_title'          => 'Perbandingan Statistik Lanjutan',
    'detail_sub'            => 'Komparasi jumlah tenaga medis dan unit fasilitas kesehatan per kecamatan — :tahun',

    // Nama seri di dalam bagan (dipakai JavaScript)
    'series_tenaga'    => 'Tenaga Kesehatan',
    'series_fasilitas' => 'Fasilitas Kesehatan',
    'series_medis'     => 'Tenaga Medis',
    'series_fas'       => 'Fasilitas',

    // ── Tabel ─────────────────────────────────────────────────────
    'table_title' => 'Fasilitas per Kecamatan',
    'table_sub'   => 'Rincian unit fasilitas kesehatan tahun :tahun',
    'table_file'  => 'fasilitas-kesehatan-per-kecamatan-:tahun',

    'col_kecamatan' => 'Kecamatan',
    'col_total'     => 'Total',
    'col_rs'        => 'Rumah Sakit',
    'col_pkm'       => 'Puskesmas',
    'col_klinik'    => 'Klinik Kesehatan',
    'col_posyandu'  => 'Posyandu',

    'source' => 'Sumber: :sumber &bull; Data Tahun :tahun',

];
