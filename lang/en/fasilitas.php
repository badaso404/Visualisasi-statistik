<?php

// Public Facilities module strings.
return [

    'page_title' => 'Public Facilities of West Jakarta',
    'header'     => 'PUBLIC FACILITIES OF WEST JAKARTA',

    // Keys = category slugs in FasilitasUmum::KATEGORI. Do not change them:
    // the same slugs are used as URL segments of the source API.
    //
    // RPTRA (Ruang Publik Terpadu Ramah Anak) is kept in Indonesian with a
    // gloss: it is a proper programme name in Jakarta, so translating it away
    // would make the label unsearchable.
    'kategori' => [
        'olahraga'          => 'Sports (GOR)',
        'rptra'             => 'RPTRA (Child-Friendly Public Spaces)',
        'tempat-ibadah'     => 'Places of Worship',
        'perpustakaan'      => 'Libraries',
        'transportasi'      => 'Transport',
        'pemadam-kebakaran' => 'Fire Stations',
    ],

    // ── Summary cards ─────────────────────────────────────────────
    'card_total'       => 'Total Facilities',
    'card_total_desc'  => 'Spread across :jumlah districts',
    'card_kategori'    => 'Largest Category',
    'card_kategori_desc' => ':jumlah units',
    'card_kecamatan'   => 'Densest District',
    'card_kecamatan_desc' => ':jumlah facilities',
    'card_rasio'       => 'Facilities per 10,000 People',
    'card_rasio_desc'  => 'Based on :tahun population',
    'card_rasio_kosong' => 'Population data not available',

    // ── Chart panels ──────────────────────────────────────────────
    'panel_sebaran'    => 'Facility Distribution by District',
    'panel_komposisi'  => 'Category Composition',

    // ── Map ───────────────────────────────────────────────────────
    'map_title'   => 'Public Facility Distribution Map',
    'map_catatan' => 'Points are drawn from coordinates entered in the admin panel. '
        . ':tanpa of :total facilities have no coordinates yet and are not shown.',
    'map_kosong'  => 'No facility has coordinates yet, so the map is still empty. '
        . 'Coordinates are entered through the admin panel.',

    // ── Table ─────────────────────────────────────────────────────
    'table_title'  => 'Facility List',
    'table_sub'    => ':total facilities recorded',
    'filter_semua' => 'All Categories',
    'cari'         => 'Search name or address…',

    'col_nama'      => 'Facility Name',
    'col_kategori'  => 'Category',
    'col_kecamatan' => 'District',
    'col_kelurahan' => 'Sub-district',
    'col_alamat'    => 'Address',
    'col_jumlah'    => 'Count',

    'belum_diisi' => 'Not set',
    'empty'       => 'No public facility data yet.',
    'empty_cari'  => 'No facility matches your search.',

    'pager_info'  => 'Showing :from–:to of :total facilities',
    'pager_empty' => 'No data',

    'source' => 'Source: West Jakarta District Website (barat.jakarta.go.id) &bull; Updated :tanggal',

];
