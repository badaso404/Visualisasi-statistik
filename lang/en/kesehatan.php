<?php

// Health module strings.
// Facility types and health-worker professions are not repeated here — they
// are shared with the Overview module via common.faskes / common.nakes.
return [

    'page_title' => 'West Jakarta Health',
    'header'     => 'WEST JAKARTA HEALTH :tahun',

    'sumber_default' => 'West Jakarta Health Agency',

    // ── Summary cards (city-wide view) ────────────────────────────
    'card_tt_label'    => 'Hospital Beds',
    'card_tt_desc'     => 'Total beds available in hospitals',
    'card_rs_label'    => 'Total Hospitals',
    'card_rs_desc'     => 'General, specialist & maternity',
    'card_nakes_label' => 'Total Health Workers',
    'card_nakes_desc'  => 'Most in: :nama',
    'card_fas_label'   => 'Total Health Facilities',
    'card_fas_desc'    => 'Hospitals, Puskesmas, clinics & Posyandu',

    // ── Cards when a district is selected (set by JavaScript) ─────
    // The staffing chart and the facilities chart fill the same four cards
    // with different metrics, hence two sets of labels.
    'kc_dokter'        => 'Doctors',
    'kc_dokter_desc'   => 'Doctors in :nama',
    'kc_perawat'       => 'Nurses',
    'kc_perawat_desc'  => 'Nurses in :nama',
    'kc_bidan'         => 'Midwives',
    'kc_bidan_desc'    => 'Midwives in :nama',
    'kc_nakes_desc'    => 'Pharmacy :farmasi · Nutrition :gizi',

    'kc_rs'            => 'Hospitals',
    'kc_rs_desc'       => 'Hospital units in :nama',
    'kc_pkm'           => 'Puskesmas',
    'kc_pkm_desc'      => 'Puskesmas units in :nama',
    'kc_klinik'        => 'Clinics',
    'kc_klinik_desc'   => 'Clinics in :nama',
    'kc_fas'           => 'Total Facilities',
    'kc_fas_desc'      => 'Posyandu :jumlah units',

    // ── Charts ────────────────────────────────────────────────────
    'chart_tenaga_title'    => 'Health Workers by District',
    'chart_tenaga_sub'      => 'Distribution of active medical personnel — :tahun · click a bar for details',
    'chart_fasilitas_title' => 'Health Facilities by District',
    'chart_fasilitas_sub'   => 'Number of facility units — :tahun · click a bar for details',
    'badge_data'            => ':tahun data',
    'detail_title'          => 'Extended Statistical Comparison',
    'detail_sub'            => 'Medical personnel against health facility units, district by district — :tahun',

    // Series names inside the charts (used by JavaScript)
    'series_tenaga'    => 'Health Workers',
    'series_fasilitas' => 'Health Facilities',
    'series_medis'     => 'Medical Personnel',
    'series_fas'       => 'Facilities',

    // ── Table ─────────────────────────────────────────────────────
    'table_title' => 'Facilities by District',
    'table_sub'   => 'Breakdown of health facility units for :tahun',
    'table_file'  => 'health-facilities-by-district-:tahun',

    'col_kecamatan' => 'District',
    'col_total'     => 'Total',
    'col_rs'        => 'Hospitals',
    'col_pkm'       => 'Puskesmas',
    'col_klinik'    => 'Clinics',
    'col_posyandu'  => 'Posyandu',

    'source' => 'Source: :sumber &bull; :tahun data',

];
