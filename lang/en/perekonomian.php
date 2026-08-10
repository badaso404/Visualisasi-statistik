<?php

// Economy module strings.
// Business-sector names ($row->nama_sektor) come from Statistics Indonesia and
// are stored in the database, so they stay in Indonesian in both versions.
return [

    'page_title' => 'West Jakarta Economy',
    'header'     => 'WEST JAKARTA ECONOMY :tahun',

    // ── Indicator cards ───────────────────────────────────────────
    'card_adhb'       => 'GRDP at Current Prices',
    'card_adhb_sub'   => 'economic value in the current year',
    'card_adhk'       => 'GRDP at Constant Prices',
    'card_adhk_sub'   => '2010 base year',
    'card_tumbuh'     => 'Economic Growth',
    'card_tumbuh_sub' => 'measured at constant prices',
    'card_deflator'   => 'Implicit Deflator',
    'card_deflator_sub' => 'price level relative to 2010',

    // ── Charts ────────────────────────────────────────────────────
    'chart_tren_title'    => 'GRDP Trend (Trillion Rupiah)',
    'chart_tren_hint'     => 'Full range :dari–:sampai; constant prices show real growth.',
    'chart_sektor_title'  => 'Economic Structure by Business Sector, :tahun',
    'chart_sektor_hint'   => 'Each category\'s contribution to GRDP at current prices.',
    'series_adhb'         => 'Current Prices',
    'series_adhk'         => 'Constant 2010 Prices',
    'series_distribusi'   => 'Share',

    // ── Business sector table ─────────────────────────────────────
    'table_sektor_title' => 'Largest Business Sectors, :tahun',
    'table_sektor_file'  => 'business-sectors-:tahun',

    'col_kategori'  => 'Category',
    'col_sektor'    => 'Business Sector',
    'col_adhb'      => 'Current Prices (IDR Billion)',
    'col_distribusi'=> 'Share',
    'col_tumbuh'    => 'Growth',

    'row_lainnya' => 'Other (:jumlah sectors)',
    'row_total'   => 'West Jakarta GRDP',
    'catatan_lainnya' => '*Growth on the Other row is a weighted average by current-price value, not an official Statistics Indonesia figure. Per-sector detail is in the chart above.',

    // ── Year-on-year table ────────────────────────────────────────
    'table_tahun_title' => 'Year-on-Year Summary',
    'table_tahun_file'  => 'economy-by-year',

    'col_tahun'      => 'Year',
    'col_adhb_t'     => 'GRDP Current (IDR Trillion)',
    'col_adhk_t'     => 'GRDP Constant (IDR Trillion)',

    'source' => 'Source: :sumber',

];
