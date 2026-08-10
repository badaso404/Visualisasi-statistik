<?php

// Digital Infrastructure module strings.
return [

    'page_title' => 'West Jakarta Digital Infrastructure',
    'header'     => 'WEST JAKARTA DIGITAL INFRASTRUCTURE :tahun',

    // ── Summary cards ─────────────────────────────────────────────
    'card_wifi'        => 'Total JakWiFi',
    'card_wifi_desc'   => 'Active: :jumlah points',
    'card_cctv'        => 'Total CCTV Units',
    'card_cctv_desc'   => 'Online: :jumlah units',
    'card_aktif'       => 'Devices Online',
    'card_aktif_desc'  => 'WiFi :wifi% &middot; CCTV :cctv% online',
    'card_pengguna'    => 'JakWiFi Users',
    'card_pengguna_desc' => 'Most in:',

    // ── Panels ────────────────────────────────────────────────────
    'panel_distribusi' => 'Regional Infrastructure Distribution',
    'panel_notifikasi' => 'Latest Notifications',
    'lihat_semua'      => 'View All',

    // Notifications. Two of these are still static examples in the view
    // (maintenance & firmware) — there is no data source for them yet, so the
    // text is carried over as-is just to keep it switching language.
    'alert_wifi_off'   => 'JakWiFi Points Offline',
    'alert_wifi_meta'  => ':nama District &bull; :jumlah points',
    'alert_maintenance'=> 'Scheduled Maintenance',
    'alert_maintenance_meta' => 'Cengkareng Sector B &bull; 1 hour ago',
    'alert_cctv_off'   => 'CCTV Lost Connection',
    'alert_cctv_meta'  => ':nama District &bull; :jumlah units',
    'alert_firmware'   => 'Firmware Update Successful',
    'alert_firmware_meta' => 'All regional nodes &bull; 5 hours ago',

    // ── Map ───────────────────────────────────────────────────────
    'map_title'    => 'Digital Infrastructure Distribution Map',
    'map_heat'     => 'Distribution Heat Map',
    'map_titik'    => 'Point Distribution',
    'map_catatan'  => 'Point density follows the real per-district data; the position of each point is illustrative.',

    // ── Table ─────────────────────────────────────────────────────
    'table_title'  => 'Infrastructure Unit Breakdown',
    'table_sub'    => 'JakWiFi &amp; CCTV recap by district — :tahun',
    'filter_semua' => 'All Types',
    'export_csv'   => 'Export CSV',

    'col_kecamatan' => 'District',
    'col_jenis'     => 'Type',
    'col_total'     => 'Total Units',
    'col_aktif'     => 'Active',
    'col_status'    => 'Status',
    'col_offline'   => 'Offline',

    'status_aktif'   => 'Active',
    'status_offline' => ':jumlah offline',
    'empty'          => 'No data for :tahun yet.',

    'pager_info'  => 'Showing :from–:to of :total units',
    'pager_empty' => 'No data',

    'source' => 'Source: West Jakarta Communications & IT Agency &bull; :tahun data',

];
