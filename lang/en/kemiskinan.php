<?php

// Poverty module strings.
// The per-district section of the view is currently disabled (Statistics
// Indonesia only publishes down to city level), so its labels are deliberately
// not moved here — no point carrying unused keys.
return [

    'page_title' => 'West Jakarta Poverty',
    'header'     => 'WEST JAKARTA POVERTY :tahun',

    // ── Indicator cards ───────────────────────────────────────────
    'card_jumlah'     => 'Population in Poverty',
    'card_jumlah_sub' => 'people',
    'card_persen'     => 'Percentage',
    'card_persen_sub' => 'of total population',
    'card_garis'      => 'Poverty Line',
    'card_garis_sub'  => 'per capita/month',
    'card_p1'         => 'Poverty Gap Index (P1)',
    'card_p1_sub'     => 'distance below the poverty line',
    'card_p2'         => 'Poverty Severity Index (P2)',
    'card_p2_sub'     => 'inequality among the poor',

    // ── Trend charts ──────────────────────────────────────────────
    'chart_miskin_title' => 'Trend in People Living in Poverty',
    'chart_persen_title' => 'Trend in Poverty Rate (%)',
    'chart_garis_title'  => 'Trend in the Poverty Line (IDR/capita/month)',
    'chart_indeks_title' => 'Trend in Poverty Gap (P1) & Severity (P2)',

    // Series names (used by JavaScript)
    'series_miskin' => 'Population in Poverty',
    'series_persen' => 'Percentage',
    'series_garis'  => 'Poverty Line',
    'series_p1'     => 'P1 (Gap)',
    'series_p2'     => 'P2 (Severity)',

    // ── Table ─────────────────────────────────────────────────────
    'table_title' => 'Year-on-Year Summary',
    'table_file'  => 'poverty-by-year',

    'col_tahun'  => 'Year',
    'col_jumlah' => 'In Poverty',
    'col_persen' => 'Percentage',
    'col_garis'  => 'Poverty Line',
    'col_p1'     => 'P1',
    'col_p2'     => 'P2',

    'empty'  => 'No poverty data available to display yet.',
    'source' => 'Source: :sumber',

];
