<?php

// String modul Kependudukan.
return [

    'page_title' => 'Kependudukan Jakarta Barat',
    'header'     => 'KEPENDUDUKAN JAKARTA BARAT :tahun',

    // ── Kartu ringkasan ───────────────────────────────────────────
    // Ikut berubah saat sebuah kecamatan diklik, jadi label yang sama juga
    // dipakai dari JavaScript.
    'card_laki'      => 'LAKI-LAKI',
    'card_perempuan' => 'PEREMPUAN',
    'card_total'     => 'TOTAL PENDUDUK',

    // Kartu berganti metrik saat satu kecamatan dipilih: per kecamatan
    // hanya tersedia total, bukan pecahan laki-laki/perempuan.
    'card_kec_total' => 'PENDUDUK KECAMATAN',
    'card_kec_kel' => 'JUMLAH KELURAHAN',
    'card_kec_persen' => 'KONTRIBUSI JAKBAR',

    // ── Bagan & peta ──────────────────────────────────────────────
    'chart_kelurahan_title' => 'POPULASI PENDUDUK KELURAHAN',
    'chart_kecamatan_title' => 'POPULASI PENDUDUK KECAMATAN',
    'map_title'             => 'PERSEBARAN PENDUDUK KECAMATAN & KELURAHAN',
    'btn_kembali'           => '← Kembali',

    // Label di dalam bagan (dipakai JavaScript)
    'series_penduduk'   => 'Penduduk',
    'chart_nodata'      => 'Klik kecamatan di sebelah kanan →',
    'chart_terpadat'    => 'Kelurahan Terpadat per Kecamatan',
    'chart_kelurahan_of'=> 'Kelurahan - :nama',
    'chart_hint_klik'   => '👆 Klik bar untuk lihat kelurahan',
    'chart_hint_lihat'  => '← Sedang melihat: :nama',

    // Peta
    'map_popup_kec'   => 'Kec. :nama',
    'legend_title'    => 'Kecamatan',
    'map_popup_kel'   => 'Kecamatan: :kecamatan',
    'satuan_jiwa'     => 'jiwa',

    'source' => 'Sumber: :sumber',

];
