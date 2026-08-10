<?php

// Overview module strings. Some are used from StatistikController@overview
// (the indicator card labels) rather than only from the view — the cards are
// assembled in the controller because each module is read at its own latest
// year.
return [

    'page_title' => 'West Jakarta Statistics Overview',
    'header'     => 'WEST JAKARTA STATISTICS OVERVIEW',

    // ── Empty state ───────────────────────────────────────────────
    'empty_title' => 'No data available yet',
    'empty_hint'  => 'The summary appears once module data has been entered from the admin portal.',

    // ── Indicator cards ───────────────────────────────────────────
    'cards_hint' => 'Summary of each West Jakarta module <strong>click for the full data</strong>',

    'card_geografis'    => 'Land Area',
    'card_kependudukan' => 'Total Population',
    'card_pendidikan'   => 'Students Enrolled',
    'card_kesehatan'    => 'Health Facilities',
    'card_bencana'      => 'Disaster Events',
    'card_kemiskinan'   => 'Population in Poverty',
    'card_perekonomian' => 'GRDP at Current Prices',
    'card_digital'      => 'JakWiFi & CCTV Points',

    // Units shown next to the card figure
    'unit_km2'      => 'km²',
    'unit_jiwa'     => 'people',
    'unit_siswa'    => 'students',
    'unit_unit'     => 'units',
    'unit_kejadian' => 'events',
    'unit_triliun'  => 'trillion',

    // Small line under the card figure
    'sub_geografis'    => ':kecamatan districts &middot; :kelurahan urban villages',
    'sub_kependudukan' => 'M :laki &middot; F :perempuan',
    'sub_pendidikan'   => ':jumlah teaching staff',
    'sub_kesehatan'    => ':jumlah health workers',
    'sub_bencana'      => 'most frequent: :jenis',
    'sub_kemiskinan'   => ':jumlah people',
    'sub_perekonomian' => ':persen% growth',
    'sub_digital'      => ':wifi JakWiFi &middot; :cctv CCTV',

    // ── Charts ────────────────────────────────────────────────────
    'chart_penduduk_title'   => 'Population by District',
    'chart_penduduk_hint'    => 'District colours are consistent across modules.',
    'chart_pendidikan_title' => 'School Participation by Level',
    'chart_pendidikan_hint'  => 'NER counts pupils whose age matches the level, GER counts all pupils — the gap shows pupils outside the standard age range.',
    'chart_faskes_title'     => 'Health Facilities',
    'chart_faskes_hint'      => 'Breakdown by facility type.',
    'chart_nakes_title'      => 'Health Workers',
    'chart_nakes_hint'       => 'Breakdown by profession.',
    'chart_bencana_title'    => 'Disaster Events by Type',
    'chart_bencana_hint'     => 'Quarterly recap for the latest year.',
    'chart_tren_title'       => 'Economy &amp; Poverty Over Time',
    'chart_tren_hint'        => 'GRDP at constant prices (trillion rupiah) against the share of the population in poverty. The range is limited to years both modules cover.',

    // Labels inside the charts (used by JavaScript)
    'series_penduduk'   => 'Population',
    'series_pdrb'       => 'GRDP at Constant Prices',
    'series_miskin'     => 'Population in Poverty (%)',
    'axis_pdrb'         => 'IDR Trillion',
    'axis_miskin'       => '% in poverty',
    'tooltip_jiwa'      => 'people',
    'tooltip_unit'      => 'units',
    'tooltip_orang'     => 'people',
    'tooltip_kejadian'  => 'events',

    // ── Table ─────────────────────────────────────────────────────
    'table_title' => 'Summary by District',
    'table_hint'  => 'Each column comes from a different module at its own latest year, so the figures are not always from the same year. A &mdash; means no data has been entered.',
    'table_file'  => 'summary-by-district',

    'col_kecamatan' => 'District',
    'col_luas'      => 'Area (km²)',
    'col_penduduk'  => 'Population',
    'col_kepadatan' => 'Density (people/km²)',
    'col_pelajar'   => 'Students',
    'col_faskes'    => 'Facilities',
    'col_miskin'    => 'In Poverty',
    'col_digital'   => 'WiFi + CCTV',

    // ── Source ────────────────────────────────────────────────────
    'source' => 'Source: Statistics Indonesia (West Jakarta) &amp; Satu Data Jakarta',

];
