<?php

// String modul Potensi Kelurahan. Dipisah dari iklim.php karena modul ini tidak
// punya data lokal sama sekali — halamannya menyematkan dashboard Satu Data
// Jakarta — jadi kosakatanya berdiri sendiri.
return [

    'nav'        => 'Potensi Kelurahan',
    'page_title' => 'Potensi Kelurahan',
    'header'     => 'DASHBOARD POTENSI KELURAHAN JAKARTA BARAT',
    'desc'       => 'Dashboard disajikan langsung dari Satu Data Jakarta dan sudah tersaring untuk wilayah Kota Administrasi Jakarta Barat.',
    'blok_label' => 'Pilih blok data',
    'open'       => 'Buka di tab baru',
    'fallback'   => 'Dashboard tidak tampil? Situs sumber hanya mengizinkan penyematan dari domain resmi jakarta.go.id. Gunakan tombol <strong>Buka di tab baru</strong> di atas.',
    'source'     => 'Sumber: Pendataan Potensi Desa/Kelurahan (Podes) — Satu Data Jakarta',

    // Kunci = slug pada URL sumber, jadi jangan diubah walaupun ada salah ketik
    // di sana ("pembanguanan"); yang kita perbaiki cukup labelnya.
    'blok' => [
        'keterangan-umum-kelurahan'              => 'Keterangan Umum',
        'kependudukan-dan-ketenagakerjaan'       => 'Kependudukan & Ketenagakerjaan',
        'perumahan-dan-lingkungan-hidup'         => 'Perumahan & Lingkungan Hidup',
        'bencana-alam-dan-mitigasi-bencana-alam' => 'Bencana Alam & Mitigasi',
        'pendidikan'                             => 'Pendidikan',
        'kesehatan'                              => 'Kesehatan',
        'sosial-budaya'                          => 'Sosial Budaya',
        'olahraga-dan-hiburan'                   => 'Olahraga & Hiburan',
        'angkutan-komunikasi-dan-informasi'      => 'Angkutan, Komunikasi & Informasi',
        'ekonomi'                                => 'Ekonomi',
        'keamanan'                               => 'Keamanan',
        'perlindungan-sosial-pembanguanan-dan-pemberdayaan-masyarakat' => 'Perlindungan Sosial & Pemberdayaan Masyarakat',
    ],
];
