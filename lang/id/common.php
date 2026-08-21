<?php

// String yang dipakai lintas modul. Label navigasi sempat menumpang di
// iklim.php — janggal, karena menu milik seluruh portal, bukan milik modul
// iklim. Semua yang dipakai lebih dari satu modul dikumpulkan di sini.
return [

    // ── Pemilih bahasa ────────────────────────────────────────────
    'lang_switch' => 'Pilih bahasa',

    // ── Sidebar navigasi ──────────────────────────────────────────
    'nav_overview'      => 'Overview',
    'nav_geografis'     => 'Geografis',
    'nav_iklim'         => 'Iklim',
    'nav_kependudukan'  => 'Kependudukan',
    'nav_pendidikan'    => 'Pendidikan',
    'nav_kesehatan'     => 'Kesehatan',
    'nav_bencana'       => 'Kebencanaan',
    'nav_kemiskinan'    => 'Kemiskinan',
    'nav_perekonomian'  => 'Perekonomian',
    'nav_infrastruktur' => 'Infrastruktur Digital',
    'nav_fasilitas'     => 'Fasilitas Umum',
    'nav_podes'         => 'Potensi Kelurahan',

    // ── Kontrol berulang ──────────────────────────────────────────
    'unduh_csv'    => 'Unduh CSV',
    'sumber'       => 'Sumber: :sumber &bull; Data Tahun :tahun',
    'sumber_bps'   => 'BPS Kota Jakarta Barat (webapi.bps.go.id)',
    'tahun'        => 'Tahun',
    'kecamatan'    => 'Kecamatan',
    'total'        => 'Total',
    'jumlah'       => 'Jumlah',
    'persentase'   => 'Persentase',
    'tidak_ada_data' => 'Belum ada data',

    // ── Istilah domain yang dipakai lebih dari satu modul ──────────
    // Overview ikut memakai kelompok-kelompok ini untuk grafik komposisinya,
    // jadi label-nya dikumpulkan di sini supaya tidak ditulis dua kali dengan
    // terjemahan yang berbeda.

    // Jenis fasilitas kesehatan (overview + kesehatan)
    'faskes' => [
        'posyandu'    => 'Posyandu',
        'klinik'      => 'Klinik',
        'puskesmas'   => 'Puskesmas',
        'rumah_sakit' => 'Rumah Sakit',
    ],

    // Profesi tenaga kesehatan (overview + kesehatan)
    'nakes' => [
        'perawat'   => 'Perawat',
        'dokter'    => 'Dokter',
        'farmasi'   => 'Farmasi',
        'bidan'     => 'Bidan',
        'ahli_gizi' => 'Ahli Gizi',
    ],

    // Jenjang sekolah (overview + pendidikan)
    'jenjang' => [
        'sd'  => 'SD/MI',
        'smp' => 'SMP/MTs',
        'sma' => 'SMA/SMK/MA',
    ],

    // Jenis bencana. Kuncinya adalah nilai apa adanya dari kolom
    // jenis_bencana di basis data, jadi jangan diubah; kalau muncul jenis baru
    // yang belum terdaftar, view menampilkan nilai aslinya.
    'jenis_bencana' => [
        'Banjir'        => 'Banjir',
        'Tanah Longsor' => 'Tanah Longsor',
        'Kebakaran'     => 'Kebakaran',
        'Angin Kencang' => 'Angin Kencang',
        'Pohon Tumbang' => 'Pohon Tumbang',
    ],

];
