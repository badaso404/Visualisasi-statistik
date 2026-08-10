<?php

return [

    // ── Header ────────────────────────────────────────────────────
    'page_title'    => 'Iklim Jakarta Barat',
    'header'        => 'IKLIM JAKARTA BARAT :tahun',
    'source'        => 'Sumber: :sumber &bull; Data Tahun :tahun',
    'source_default'=> 'BPS Kota Jakarta Barat (webapi.bps.go.id)',
    'bmkg_note'     => 'Mengikuti acuan kategori BMKG.',

    // ── Kartu ringkasan ───────────────────────────────────────────
    'card_suhu_label'   => 'RATA-RATA SUHU UDARA (°C)',
    'card_suhu_sub'     => 'suhu rata-rata harian',
    'card_hujan_label'  => 'RATA-RATA HARI HUJAN (HARI/BLN)',
    'card_hujan_sub'    => 'jumlah hari turun hujan',
    'card_lembab_label' => 'KELEMBABAN UDARA (%)',
    'card_lembab_sub'   => 'kadar uap air di udara',
    'card_status_label' => 'STATUS CURAH HUJAN',
    'card_status_sub'   => 'klasifikasi intensitas hujan',

    // Label kartu ketika bulan dipilih dari bar chart (JS mengisi :month)
    'card_suhu_month'   => 'SUHU — :month',
    'card_hujan_month'  => 'HARI HUJAN — :month',
    'card_lembab_month' => 'KELEMBABAN — :month',
    'card_status_month' => 'STATUS — :month',

    // ── Bagan ─────────────────────────────────────────────────────
    'chart_donut_title' => 'DISTRIBUSI CURAH HUJAN',
    'chart_bar_title'   => 'TREN HARI HUJAN BULANAN',
    'chart_bar_hint'    => 'Klik salah satu bulan untuk melihat detailnya di kartu ringkasan',
    'chart_bar_avg'     => 'Rata-rata: :avg hari/bln',

    // ── Tabel ─────────────────────────────────────────────────────
    'table_title'       => 'DATA IKLIM PER BULAN',
    'col_bulan'         => 'Bulan',
    'col_hari_hujan'    => 'Hari Hujan',
    'col_suhu'          => 'Suhu (°C)',
    'col_kelembaban'    => 'Kelembaban (%)',
    'col_angin'         => 'Angin (km/h)',
    'col_tekanan'       => 'Tekanan (mb)',
    'col_penyinaran'    => 'Penyinaran (%)',
    'col_status'        => 'Status',

    // ── Legenda donut ─────────────────────────────────────────────
    'humidity_very_high' => 'Sangat Tinggi',
    'humidity_high'      => 'Tinggi',
    'humidity_medium'    => 'Sedang',

    // ── Kategori BMKG ─────────────────────────────────────────────
    'legend_title'          => 'KETERANGAN KATEGORI',
    'legend_rain_head'      => 'CURAH HUJAN',
    'legend_temp_head'      => 'SUHU UDARA',
    'legend_humidity_head'  => 'KELEMBABAN',

    // Curah hujan
    'cat_rain_normal'   => ':label — sesuai rata-rata historis',
    'cat_rain_waspada'  => ':label — di atas normal, perlu perhatian',
    'cat_rain_siaga'    => ':label — potensi banjir ringan',
    'cat_rain_awas'     => ':label — curah hujan ekstrem',

    // Suhu
    'cat_temp_nyaman'   => ':label — 24–30°C (normal tropis)',
    'cat_temp_panas'    => ':label — 30–33°C',
    'cat_temp_ekstrem'  => ':label — >33°C',

    // Kelembaban
    'cat_hum_ideal'         => ':label — 60–80%',
    'cat_hum_lembab'        => ':label — 80–90%',
    'cat_hum_sangat_lembab' => ':label — >90%',

    // Status badge (sesuai $rainCat / $suhuCat / $lembabCat di view)
    'status_normal'      => 'Normal',
    'status_waspada'     => 'Waspada',
    'status_siaga'       => 'Siaga',
    'status_awas'        => 'Awas',
    'status_nyaman'      => 'Nyaman',
    'status_panas'       => 'Panas',
    'status_ekstrem'     => 'Ekstrem',
    'status_ideal'       => 'Ideal',
    'status_lembab'      => 'Lembab',
    'status_sangat_lembab' => 'Sangat Lembab',

    // ── Nama bulan ────────────────────────────────────────────────
    'months' => [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ],

    // ── Sidebar navigasi ──────────────────────────────────────────
    'nav_overview'    => 'Overview',
    'nav_geografis'   => 'Geografis',
    'nav_iklim'       => 'Iklim',
    'nav_kependudukan'=> 'Kependudukan',
    'nav_pendidikan'  => 'Pendidikan',
    'nav_kesehatan'   => 'Kesehatan',
    'nav_bencana'     => 'Kebencanaan',
    'nav_kemiskinan'  => 'Kemiskinan',
    'nav_perekonomian'=> 'Perekonomian',
    'nav_infrastruktur' => 'Infrastruktur Digital',
    // Label menu modul Potensi Kelurahan ada di lang/*/podes.php bersama string
    // modulnya, bukan di sini.

    // ── Language switcher ─────────────────────────────────────────
    'lang_id' => 'Indonesia',
    'lang_en' => 'English',

    // ── Chart JS strings ──────────────────────────────────────────
    'chart_bar_series'      => 'Hari Hujan (hari)',
    'chart_bar_yaxis'       => 'Hari',
    'chart_bar_annotation'  => 'Rata-rata: :avg hari',
    'chart_bar_tooltip'     => ':val hari',
    'donut_label_center'    => 'Tinggi',

    // ── Pagination ────────────────────────────────────────────────
    'pager_showing' => 'Menampilkan :from–:to dari :total bulan',

    // ── Download button ───────────────────────────────────────────
    'unduh_csv' => 'Unduh CSV',
];
