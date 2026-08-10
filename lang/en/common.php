<?php

// Strings shared across modules. The navigation labels used to sit in
// iklim.php, which was odd — the menu belongs to the whole portal, not to the
// climate module. Anything used by more than one module lives here.
return [

    // ── Language switcher ─────────────────────────────────────────
    'lang_switch' => 'Choose language',

    // ── Sidebar navigation ────────────────────────────────────────
    'nav_overview'      => 'Overview',
    'nav_geografis'     => 'Geography',
    'nav_iklim'         => 'Climate',
    'nav_kependudukan'  => 'Population',
    'nav_pendidikan'    => 'Education',
    'nav_kesehatan'     => 'Health',
    'nav_bencana'       => 'Disasters',
    'nav_kemiskinan'    => 'Poverty',
    'nav_perekonomian'  => 'Economy',
    'nav_infrastruktur' => 'Digital Infrastructure',
    'nav_podes'         => 'Village Potential',

    // ── Recurring controls ────────────────────────────────────────
    'unduh_csv'    => 'Download CSV',
    'sumber'       => 'Source: :sumber &bull; :tahun data',
    'sumber_bps'   => 'Statistics Indonesia, West Jakarta (webapi.bps.go.id)',
    'tahun'        => 'Year',
    'kecamatan'    => 'District',
    'total'        => 'Total',
    'jumlah'       => 'Count',
    'persentase'   => 'Percentage',
    'tidak_ada_data' => 'No data yet',

    // ── Domain terms used by more than one module ──────────────────
    // The overview reuses these groups for its composition charts, so the
    // labels live here rather than being written twice with two different
    // translations.

    // Health facility types (overview + health). Posyandu and Puskesmas are
    // Indonesian institutions with no English equivalent; the local name is
    // kept and glossed rather than translated into something misleading.
    'faskes' => [
        'posyandu'    => 'Posyandu (community health post)',
        'klinik'      => 'Clinic',
        'puskesmas'   => 'Puskesmas (public health centre)',
        'rumah_sakit' => 'Hospital',
    ],

    // Health worker professions (overview + health)
    'nakes' => [
        'perawat'   => 'Nurses',
        'dokter'    => 'Doctors',
        'farmasi'   => 'Pharmacists',
        'bidan'     => 'Midwives',
        'ahli_gizi' => 'Nutritionists',
    ],

    // School levels (overview + education). Indonesian abbreviations are kept
    // in brackets because they are what the source tables use.
    'jenjang' => [
        'sd'  => 'Primary (SD/MI)',
        'smp' => 'Junior high (SMP/MTs)',
        'sma' => 'Senior high (SMA/SMK/MA)',
    ],

    // Disaster types. The keys are the raw values of the jenis_bencana column
    // in the database, so they must not be changed; if an unlisted type turns
    // up, the view falls back to showing the raw value.
    'jenis_bencana' => [
        'Banjir'        => 'Flooding',
        'Tanah Longsor' => 'Landslide',
        'Kebakaran'     => 'Fire',
        'Angin Kencang' => 'High winds',
        'Pohon Tumbang' => 'Fallen trees',
    ],

];
