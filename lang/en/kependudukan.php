<?php

// Population module strings.
return [

    'page_title' => 'West Jakarta Population',
    'header'     => 'WEST JAKARTA POPULATION :tahun',

    // ── Summary cards ─────────────────────────────────────────────
    // These change when a district is clicked, so the same labels are also
    // used from JavaScript.
    'card_laki'      => 'MALE',
    'card_perempuan' => 'FEMALE',
    'card_total'     => 'TOTAL POPULATION',

    // The cards switch metric when a district is selected: per district only
    // the total is available, not the male/female split.
    'card_kec_total' => 'DISTRICT POPULATION',
    'card_kec_kel' => 'URBAN VILLAGES',
    'card_kec_persen' => 'SHARE OF WEST JAKARTA',

    // ── Charts & map ──────────────────────────────────────────────
    'chart_kelurahan_title' => 'POPULATION BY URBAN VILLAGE',
    'chart_kecamatan_title' => 'POPULATION BY DISTRICT',
    'map_title'             => 'POPULATION SPREAD BY DISTRICT & URBAN VILLAGE',
    'btn_kembali'           => '← Back',

    // Labels inside the charts (used by JavaScript)
    'series_penduduk'   => 'Population',
    'chart_nodata'      => 'Click a district on the right →',
    'chart_terpadat'    => 'Most Populous Urban Village per District',
    'chart_kelurahan_of'=> 'Urban villages - :nama',
    'chart_hint_klik'   => '👆 Click a bar to see urban villages',
    'chart_hint_lihat'  => '← Now viewing: :nama',

    // Map
    'map_popup_kec'   => ':nama District',
    'legend_title'    => 'Districts',
    'map_popup_kel'   => 'District: :kecamatan',
    'satuan_jiwa'     => 'people',

    'source' => 'Source: :sumber',

];
