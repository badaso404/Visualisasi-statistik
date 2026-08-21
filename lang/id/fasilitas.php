<?php

// String modul Fasilitas Umum.
return [

    'page_title' => 'Fasilitas Umum Jakarta Barat',
    'header'     => 'FASILITAS UMUM JAKARTA BARAT',

    // Kunci = slug kategori pada FasilitasUmum::KATEGORI. Jangan diubah:
    // slug-nya juga dipakai sebagai segmen URL API sumber.
    'kategori' => [
        'olahraga'          => 'Olahraga (GOR)',
        'rptra'             => 'RPTRA',
        'tempat-ibadah'     => 'Tempat Ibadah',
        'perpustakaan'      => 'Perpustakaan',
        'transportasi'      => 'Transportasi',
        'pemadam-kebakaran' => 'Pemadam Kebakaran',
    ],

    // ── Kartu ringkasan ───────────────────────────────────────────
    'card_total'       => 'Total Fasilitas',
    'card_total_desc'  => 'Tersebar di :jumlah kecamatan',
    'card_kategori'    => 'Kategori Terbanyak',
    'card_kategori_desc' => ':jumlah unit',
    'card_kecamatan'   => 'Kecamatan Terpadat',
    'card_kecamatan_desc' => ':jumlah fasilitas',
    'card_rasio'       => 'Fasilitas per 10.000 Jiwa',
    'card_rasio_desc'  => 'Berdasarkan penduduk :tahun',
    'card_rasio_kosong' => 'Data penduduk belum tersedia',

    // ── Panel grafik ──────────────────────────────────────────────
    'panel_sebaran'    => 'Sebaran Fasilitas per Kecamatan',
    'panel_komposisi'  => 'Komposisi Kategori',

    // ── Peta ──────────────────────────────────────────────────────
    'map_title'   => 'Peta Sebaran Fasilitas Umum',
    'map_catatan' => 'Titik digambar dari koordinat yang sudah diisi di panel admin. '
        . ':tanpa dari :total fasilitas belum berkoordinat sehingga belum tampil di peta.',
    'map_kosong'  => 'Belum ada fasilitas yang punya koordinat, jadi peta masih kosong. '
        . 'Koordinat diisi lewat panel admin.',

    // ── Tabel ─────────────────────────────────────────────────────
    'table_title'  => 'Daftar Fasilitas',
    'table_sub'    => ':total fasilitas terdata',
    'filter_semua' => 'Semua Kategori',
    'cari'         => 'Cari nama atau alamat…',

    'col_nama'      => 'Nama Fasilitas',
    'col_kategori'  => 'Kategori',
    'col_kecamatan' => 'Kecamatan',
    'col_kelurahan' => 'Kelurahan',
    'col_alamat'    => 'Alamat',
    'col_jumlah'    => 'Jumlah',

    'belum_diisi' => 'Belum diisi',
    'empty'       => 'Belum ada data fasilitas umum.',
    'empty_cari'  => 'Tidak ada fasilitas yang cocok dengan pencarian.',

    'pager_info'  => 'Menampilkan :from–:to dari :total fasilitas',
    'pager_empty' => 'Tidak ada data',

    'source' => 'Sumber: Situs Kecamatan Jakarta Barat (barat.jakarta.go.id) &bull; Diperbarui :tanggal',

];
