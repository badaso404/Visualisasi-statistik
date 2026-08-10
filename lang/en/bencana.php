<?php

// Disaster module strings.
// Disaster type names are not listed here — they are mapped from the
// jenis_bencana column via common.jenis_bencana so they match the Overview.
return [

    'page_title' => 'West Jakarta Disaster Monitor',
    'header'     => 'WEST JAKARTA DISASTER MONITOR',

    // ── Summary cards ─────────────────────────────────────────────
    'card_kejadian'  => 'TOTAL EVENTS',
    'card_meninggal' => 'FATALITIES',
    'card_luka'      => 'INJURED',
    'card_jenis'     => 'MOST FREQUENT TYPE',
    // The card title changes when a donut slice is clicked (set by JavaScript)
    'card_jenis_dipilih' => 'SELECTED TYPE',

    // ── Charts & map ──────────────────────────────────────────────
    'chart_donut_title' => 'Share by Disaster Type',
    'chart_donut_hint'  => '· click a type for its summary',
    'chart_donut_total' => 'Total',
    'map_title'         => 'Disaster Distribution Map',
    'chart_tw_title'    => 'Disaster Types by Quarter',
    'chart_tw_hint'     => '· West Jakarta :tahun',
    'chart_tren_title'  => 'Event Trend by Quarter',
    'chart_tren_hint'   => '· all periods',
    'series_kejadian'   => 'Events',
    'chart_kosong'      => 'No data yet.',

    // Map tabs
    'tab_banjir'    => 'Flood Watch',
    'tab_damkar'    => 'Fire Stations',
    'tab_zona_aman' => 'Safe Zones',

    // Map points
    'titik_pintu_air'   => 'Floodgate',
    'titik_rumah_pompa' => 'Pump House',
    'titik_posko'       => 'Water Agency Post',
    'titik_damkar'      => 'Fire Station',
    'popup_maps'        => 'Open in Maps',
    'popup_zona_aman'   => 'Safe evacuation area',
    'popup_tinggi'      => 'Water level',
    'popup_update'      => 'Updated',
    'popup_status'      => 'Status',
    'popup_sumber_dsda' => 'Source: Jakarta Water Agency (real-time)',
    'popup_rawan'       => 'Flood-prone',
    'popup_acuan'       => 'Nearest reference post: :pos (:jarak km)',
    'legend_title'      => 'Legend:',
    'legend_siaga'      => '🔴 red badge/dot = alert status · real-time DSDA',

    // Base map layers
    'basemap_satelit' => 'Satellite',
    'basemap_terang'  => 'Light Map',
    'basemap_jalan'   => 'Street Map',

    // ── Table ─────────────────────────────────────────────────────
    'table_title'  => 'Quarterly Disaster Recap',
    'table_sub'    => 'West Jakarta &middot; :tahun &middot; quarterly aggregate (not a per-location event log)',
    'table_file'   => 'disaster-recap-:tahun',
    'filter_semua' => 'All types',
    'search'       => 'Search period or type',

    'col_periode'   => 'Period',
    'col_triwulan'  => 'Quarter',
    'col_jenis'     => 'Disaster Type',
    'col_kejadian'  => 'Events',
    'col_meninggal' => 'Fatalities',
    'col_luka'      => 'Injured',

    'empty_rekap'  => 'No recap data for this year yet. Run "Sync dari API" in the admin portal.',
    'empty_search' => 'No data matches your search.',
    'pager_info'   => 'Showing :from–:to of :total reports',

    'source' => 'Source: :sumber &middot; map points: BPBD &amp; Jakarta Water Agency',

];
