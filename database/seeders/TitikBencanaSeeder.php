<?php

namespace Database\Seeders;

use App\Models\TitikBencana;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class TitikBencanaSeeder extends Seeder
{
    public function run(): void
    {
        $kec = Kecamatan::pluck('id', 'nama_kecamatan');

        // [kecamatan, kategori, level, nama, lat, lng, keterangan]
        $data = [
            // Zona rawan banjir — Prioritas 1 (rawan tinggi, > 50 cm)
            ['Cengkareng',        'banjir_rawan', 1, 'Kapuk - Kamal Muara',     -6.1751, 106.7272, 'Sering tergenang > 50 cm saat hujan deras'],
            ['Tambora',           'banjir_rawan', 1, 'Pekojan - Angke',         -6.1850, 106.7350, 'Luapan Kali Angke'],
            ['Grogol Petamburan', 'banjir_rawan', 1, 'Jelambar Baru',           -6.1650, 106.7150, 'Genangan rutin musim hujan'],
            // Prioritas 2 (rawan sedang, 20–50 cm)
            ['Kalideres',         'banjir_rawan', 2, 'Tegal Alur',              -6.1900, 106.7400, 'Genangan 20–50 cm'],
            ['Kembangan',         'banjir_rawan', 2, 'Kembangan Utara',         -6.1600, 106.7050, 'Drainase terbatas'],
            // Prioritas 3 (rawan rendah, < 20 cm)
            ['Palmerah',          'banjir_rawan', 3, 'Slipi',                   -6.1700, 106.7500, 'Genangan ringan < 20 cm'],
            ['Kebon Jeruk',       'banjir_rawan', 3, 'Duri Kepa',              -6.1800, 106.7200, 'Genangan sesaat'],
            ['Taman Sari',        'banjir_rawan', 3, 'Tangki',                  -6.1550, 106.7300, 'Genangan ringan'],

            // Pos Damkar
            ['Cengkareng',        'pos_damkar',   null, 'Pos Damkar Cengkareng', -6.1950, 106.7100, 'Siaga 24 jam'],
            ['Kebon Jeruk',       'pos_damkar',   null, 'Pos Damkar Kebon Jeruk', -6.1500, 106.7450, 'Siaga 24 jam'],
            ['Tambora',           'pos_damkar',   null, 'Pos Damkar Tambora',    -6.1680, 106.7920, 'Siaga 24 jam'],

            // Zona aman / titik evakuasi
            ['Grogol Petamburan', 'zona_aman',    null, 'GOR Grogol',           -6.1750, 106.7600, 'Titik kumpul & tempat evakuasi sementara'],
            ['Cengkareng',        'zona_aman',    null, 'Lapangan Cengkareng',  -6.1880, 106.7250, 'Area aman bebas genangan'],
        ];

        foreach ($data as [$namaKec, $kategori, $level, $nama, $lat, $lng, $ket]) {
            TitikBencana::updateOrCreate(
                ['kategori' => $kategori, 'nama' => $nama],
                [
                    'kecamatan_id' => $kec[$namaKec] ?? null,
                    'level'        => $level,
                    'latitude'     => $lat,
                    'longitude'    => $lng,
                    'keterangan'   => $ket,
                ]
            );
        }
    }
}
