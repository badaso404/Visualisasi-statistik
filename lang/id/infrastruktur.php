<?php

// String modul Infrastruktur Digital.
return [

    'page_title' => 'Infrastruktur Digital Jakarta Barat',
    'header'     => 'INFRASTRUKTUR DIGITAL JAKARTA BARAT :tahun',

    // ── Kartu ringkasan ───────────────────────────────────────────
    'card_wifi'        => 'Total JakWiFi',
    'card_wifi_desc'   => 'Aktif: :jumlah titik',
    'card_cctv'        => 'Total Unit CCTV',
    'card_cctv_desc'   => 'Online: :jumlah unit',
    'card_aktif'       => 'Perangkat Aktif',
    'card_aktif_desc'  => 'WiFi :wifi% &middot; CCTV :cctv% online',
    'card_pengguna'    => 'Pengguna JakWiFi',
    'card_pengguna_desc' => 'Terbanyak:',

    // ── Panel ─────────────────────────────────────────────────────
    'panel_distribusi' => 'Distribusi Infrastruktur Regional',
    'panel_notifikasi' => 'Notifikasi Terkini',
    'lihat_semua'      => 'Lihat Semua',

    // Notifikasi. Dua di antaranya masih contoh statis di view (pemeliharaan &
    // firmware) — belum ada sumber datanya, jadi teksnya ikut dipindah apa
    // adanya supaya tetap ganti bahasa.
    'alert_wifi_off'   => 'Titik JakWiFi Offline',
    'alert_wifi_meta'  => 'Kecamatan :nama &bull; :jumlah titik',
    'alert_maintenance'=> 'Pemeliharaan Terjadwal',
    'alert_maintenance_meta' => 'Cengkareng Sektor B &bull; 1 jam lalu',
    'alert_cctv_off'   => 'CCTV Kehilangan Koneksi',
    'alert_cctv_meta'  => 'Kecamatan :nama &bull; :jumlah unit',
    'alert_firmware'   => 'Pembaruan Firmware Berhasil',
    'alert_firmware_meta' => 'Seluruh node region &bull; 5 jam lalu',

    // ── Peta ──────────────────────────────────────────────────────
    'map_title'    => 'Peta Sebaran Infrastruktur Digital',
    'map_heat'     => 'Heat Map Sebaran',
    'map_titik'    => 'Titik Sebaran',
    'map_catatan'  => 'Kepadatan titik mengikuti data asli per kecamatan; posisi tiap titik bersifat ilustratif.',

    // ── Tabel ─────────────────────────────────────────────────────
    'table_title'  => 'Rincian Unit Infrastruktur',
    'table_sub'    => 'Rekap JakWiFi &amp; CCTV per kecamatan — :tahun',
    'filter_semua' => 'Semua Jenis',
    'export_csv'   => 'Export CSV',

    'col_kecamatan' => 'Kecamatan',
    'col_jenis'     => 'Jenis',
    'col_total'     => 'Total Unit',
    'col_aktif'     => 'Aktif',
    'col_status'    => 'Status',
    'col_offline'   => 'Offline',

    'status_aktif'   => 'Aktif',
    'status_offline' => ':jumlah offline',
    'empty'          => 'Belum ada data untuk tahun :tahun.',

    'pager_info'  => 'Menampilkan :from–:to dari :total unit',
    'pager_empty' => 'Tidak ada data',

    'source' => 'Sumber: Diskominfotik Jakarta Barat &bull; Data Tahun :tahun',

];
