<?php

// String modul Kebencanaan.
// Nama jenis bencana tidak ada di sini — dipetakan dari nilai kolom
// jenis_bencana lewat common.jenis_bencana supaya sama dengan modul Overview.
return [

    'page_title' => 'Monitor Bencana Jakarta Barat',
    'header'     => 'MONITOR BENCANA JAKARTA BARAT',

    // ── Kartu ringkasan ───────────────────────────────────────────
    'card_kejadian'  => 'TOTAL KEJADIAN',
    'card_meninggal' => 'KORBAN MENINGGAL',
    'card_luka'      => 'KORBAN LUKA-LUKA',
    'card_jenis'     => 'JENIS TERBANYAK',
    // Judul kartu berganti saat sebuah irisan donut diklik (diisi JavaScript)
    'card_jenis_dipilih' => 'JENIS DIPILIH',

    // ── Bagan & peta ──────────────────────────────────────────────
    'chart_donut_title' => 'Proporsi Jenis Bencana',
    'chart_donut_hint'  => '· klik jenis untuk lihat ringkasannya',
    'chart_donut_total' => 'Total',
    'map_title'         => 'Peta Sebaran Bencana',
    'chart_tw_title'    => 'Jenis Bencana per Triwulan',
    'chart_tw_hint'     => '· Jakarta Barat :tahun',
    'chart_tren_title'  => 'Tren Kejadian per Triwulan',
    'chart_tren_hint'   => '· seluruh periode',
    'series_kejadian'   => 'Kejadian',
    'chart_kosong'      => 'Belum ada data.',

    // Tab peta
    'tab_banjir'    => 'Pantau Banjir',
    'tab_damkar'    => 'Damkar',
    'tab_zona_aman' => 'Zona Aman',

    // Titik peta
    'titik_pintu_air'   => 'Pintu Air',
    'titik_rumah_pompa' => 'Rumah Pompa',
    'titik_posko'       => 'Posko SDA',
    'titik_damkar'      => 'Pos Damkar',
    'popup_maps'        => 'Buka Maps',
    'popup_zona_aman'   => 'Area aman evakuasi',
    'popup_tinggi'      => 'Tinggi air',
    'popup_update'      => 'Update',
    'popup_status'      => 'Status',
    'popup_sumber_dsda' => 'Sumber: DSDA DKI Jakarta (real-time)',
    'popup_rawan'       => 'Rawan Banjir',
    'popup_acuan'       => 'Acuan pos terdekat: :pos (:jarak km)',
    'legend_title'      => 'Keterangan :',
    'legend_siaga'      => '🔴 badge/titik merah = status siaga · real-time DSDA',

    // Lapisan peta dasar
    'basemap_satelit' => 'Satelit',
    'basemap_terang'  => 'Peta Terang',
    'basemap_jalan'   => 'Peta Jalan',

    // ── Tabel ─────────────────────────────────────────────────────
    'table_title'  => 'Rekap Bencana per Triwulan',
    'table_sub'    => 'Jakarta Barat &middot; :tahun &middot; agregat triwulanan (bukan log kejadian per lokasi)',
    'table_file'   => 'rekap-bencana-:tahun',
    'filter_semua' => 'Semua jenis',
    'search'       => 'Cari periode atau jenis',

    'col_periode'   => 'Periode',
    'col_triwulan'  => 'Triwulan',
    'col_jenis'     => 'Jenis Bencana',
    'col_kejadian'  => 'Kejadian',
    'col_meninggal' => 'Korban Meninggal',
    'col_luka'      => 'Korban Luka',

    'empty_rekap'  => 'Belum ada data rekap untuk tahun ini. Jalankan "Sync dari API" di portal admin.',
    'empty_search' => 'Tidak ada data yang cocok dengan pencarian.',
    'pager_info'   => 'Menampilkan :from–:to dari :total laporan',

    'source' => 'Sumber: :sumber &middot; titik peta: BPBD &amp; DSDA DKI Jakarta',

];
