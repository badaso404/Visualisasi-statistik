<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataKependudukan;
use App\Models\PendudukKecamatan;
use App\Models\PendudukKelurahan;
use App\Models\Kecamatan;

class KependudukanSeeder extends Seeder
{
    public function run(): void
    {

        DataKependudukan::truncate();
        PendudukKecamatan::truncate();
        PendudukKelurahan::truncate();
        // Summary kota
        DataKependudukan::create([
            'tahun'            => 2024,
            'jumlah_laki_laki' => 1271376,
            'jumlah_perempuan' => 1285376,
            'jumlah_total'     => 2556752,
            'sumber'           => 'Kota Jakarta Barat Dalam Angka 2025',
        ]);

        // Per kecamatan
        $kecamatan = [
            'Cengkareng'        => 581788,
            'Kalideres'         => 464076,
            'Kebon Jeruk'       => 362641,
            'Kembangan'         => 311262,
            'Tambora'           => 258061,
            'Grogol Petamburan' => 230173,
            'Palmerah'          => 225842,
            'Taman Sari'        => 122909,
        ];

        foreach ($kecamatan as $nama => $jumlah) {
            $kec = Kecamatan::where('nama_kecamatan', $nama)->first();
            if ($kec) {
                PendudukKecamatan::create([
                    'kecamatan_id'    => $kec->id,
                    'tahun'           => 2024,
                    'jumlah_penduduk' => $jumlah,
                ]);
            }
        }

        // Per kelurahan (top 8 dari chart)
        // Per kelurahan lengkap 56 kelurahan
        PendudukKelurahan::truncate();
        $kelurahan = [
            // CENGKARENG (6 kelurahan)
            ['nama' => 'Cengkareng Barat',   'kecamatan' => 'Cengkareng', 'jumlah' => 81567,  'lat' => -6.1495, 'lng' => 106.7218],
            ['nama' => 'Cengkareng Timur',   'kecamatan' => 'Cengkareng', 'jumlah' => 103122, 'lat' => -6.1402, 'lng' => 106.7401],
            ['nama' => 'Duri Kosambi',       'kecamatan' => 'Cengkareng', 'jumlah' => 102437, 'lat' => -6.1550, 'lng' => 106.7150],
            ['nama' => 'Kapuk',              'kecamatan' => 'Cengkareng', 'jumlah' => 171181, 'lat' => -6.1317, 'lng' => 106.7372],
            ['nama' => 'Kedaung Kali Angke', 'kecamatan' => 'Cengkareng', 'jumlah' => 45231,  'lat' => -6.1200, 'lng' => 106.7280],
            ['nama' => 'Rawa Buaya',         'kecamatan' => 'Cengkareng', 'jumlah' => 82798,  'lat' => -6.1400, 'lng' => 106.7300],

            // KALIDERES (5 kelurahan)
            ['nama' => 'Kalideres',          'kecamatan' => 'Kalideres',  'jumlah' => 92090,  'lat' => -6.1300, 'lng' => 106.7000],
            ['nama' => 'Pegadungan',         'kecamatan' => 'Kalideres',  'jumlah' => 100018, 'lat' => -6.1100, 'lng' => 106.6950],
            ['nama' => 'Semanan',            'kecamatan' => 'Kalideres',  'jumlah' => 93033,  'lat' => -6.1650, 'lng' => 106.6850],
            ['nama' => 'Tegal Alur',         'kecamatan' => 'Kalideres',  'jumlah' => 106972, 'lat' => -6.1200, 'lng' => 106.7050],
            ['nama' => 'Jurumudi',           'kecamatan' => 'Kalideres',  'jumlah' => 71885,  'lat' => -6.1450, 'lng' => 106.7100],

            // KEMBANGAN (6 kelurahan)
            ['nama' => 'Kembangan Utara',    'kecamatan' => 'Kembangan',  'jumlah' => 58000,  'lat' => -6.1850, 'lng' => 106.7450],
            ['nama' => 'Kembangan Selatan',  'kecamatan' => 'Kembangan',  'jumlah' => 52000,  'lat' => -6.2050, 'lng' => 106.7450],
            ['nama' => 'Meruya Utara',       'kecamatan' => 'Kembangan',  'jumlah' => 55000,  'lat' => -6.1900, 'lng' => 106.7600],
            ['nama' => 'Meruya Selatan',     'kecamatan' => 'Kembangan',  'jumlah' => 48000,  'lat' => -6.2100, 'lng' => 106.7600],
            ['nama' => 'Srengseng',          'kecamatan' => 'Kembangan',  'jumlah' => 51000,  'lat' => -6.2000, 'lng' => 106.7700],
            ['nama' => 'Joglo',              'kecamatan' => 'Kembangan',  'jumlah' => 57262,  'lat' => -6.2150, 'lng' => 106.7550],

            // KEBON JERUK (8 kelurahan)
            ['nama' => 'Kebon Jeruk',        'kecamatan' => 'Kebon Jeruk', 'jumlah' => 45000,  'lat' => -6.1900, 'lng' => 106.7850],
            ['nama' => 'Sukabumi Utara',     'kecamatan' => 'Kebon Jeruk', 'jumlah' => 38000,  'lat' => -6.1800, 'lng' => 106.7800],
            ['nama' => 'Sukabumi Selatan',   'kecamatan' => 'Kebon Jeruk', 'jumlah' => 42000,  'lat' => -6.1950, 'lng' => 106.7800],
            ['nama' => 'Kelapa Dua',         'kecamatan' => 'Kebon Jeruk', 'jumlah' => 52000,  'lat' => -6.1750, 'lng' => 106.7900],
            ['nama' => 'Duri Kepa',          'kecamatan' => 'Kebon Jeruk', 'jumlah' => 48000,  'lat' => -6.1700, 'lng' => 106.7950],
            ['nama' => 'Kedoya Utara',       'kecamatan' => 'Kebon Jeruk', 'jumlah' => 44000,  'lat' => -6.1850, 'lng' => 106.7700],
            ['nama' => 'Kedoya Selatan',     'kecamatan' => 'Kebon Jeruk', 'jumlah' => 40000,  'lat' => -6.2000, 'lng' => 106.7700],
            ['nama' => 'Kebon Jeruk Baru',   'kecamatan' => 'Kebon Jeruk', 'jumlah' => 51262,  'lat' => -6.1950, 'lng' => 106.7850],

            // GROGOL PETAMBURAN (7 kelurahan)
            ['nama' => 'Grogol',             'kecamatan' => 'Grogol Petamburan', 'jumlah' => 38000, 'lat' => -6.1600, 'lng' => 106.7950],
            ['nama' => 'Tomang',             'kecamatan' => 'Grogol Petamburan', 'jumlah' => 35000, 'lat' => -6.1700, 'lng' => 106.8000],
            ['nama' => 'Jelambar',           'kecamatan' => 'Grogol Petamburan', 'jumlah' => 32000, 'lat' => -6.1550, 'lng' => 106.8050],
            ['nama' => 'Jelambar Baru',      'kecamatan' => 'Grogol Petamburan', 'jumlah' => 30000, 'lat' => -6.1600, 'lng' => 106.8100],
            ['nama' => 'Tanjung Duren Utara', 'kecamatan' => 'Grogol Petamburan', 'jumlah' => 28000, 'lat' => -6.1750, 'lng' => 106.7950],
            ['nama' => 'Tanjung Duren Selatan', 'kecamatan' => 'Grogol Petamburan', 'jumlah' => 27000, 'lat' => -6.1850, 'lng' => 106.7950],
            ['nama' => 'Wijaya Kusuma',      'kecamatan' => 'Grogol Petamburan', 'jumlah' => 40173, 'lat' => -6.1500, 'lng' => 106.8000],

            // TAMAN SARI (7 kelurahan)
            ['nama' => 'Taman Sari',         'kecamatan' => 'Taman Sari', 'jumlah' => 18000,  'lat' => -6.1450, 'lng' => 106.8150],
            ['nama' => 'Krukut',             'kecamatan' => 'Taman Sari', 'jumlah' => 17000,  'lat' => -6.1500, 'lng' => 106.8200],
            ['nama' => 'Maphar',             'kecamatan' => 'Taman Sari', 'jumlah' => 16000,  'lat' => -6.1400, 'lng' => 106.8200],
            ['nama' => 'Tangki',             'kecamatan' => 'Taman Sari', 'jumlah' => 15000,  'lat' => -6.1350, 'lng' => 106.8150],
            ['nama' => 'Mangga Besar',       'kecamatan' => 'Taman Sari', 'jumlah' => 19000,  'lat' => -6.1480, 'lng' => 106.8250],
            ['nama' => 'Keagungan',          'kecamatan' => 'Taman Sari', 'jumlah' => 18000,  'lat' => -6.1420, 'lng' => 106.8100],
            ['nama' => 'Glodok',             'kecamatan' => 'Taman Sari', 'jumlah' => 19909,  'lat' => -6.1480, 'lng' => 106.8180],

            // PALMERAH (6 kelurahan)
            ['nama' => 'Palmerah',           'kecamatan' => 'Palmerah',   'jumlah' => 76532,  'lat' => -6.1900, 'lng' => 106.7900],
            ['nama' => 'Kota Bambu Utara',   'kecamatan' => 'Palmerah',   'jumlah' => 35000,  'lat' => -6.1800, 'lng' => 106.7950],
            ['nama' => 'Kota Bambu Selatan', 'kecamatan' => 'Palmerah',   'jumlah' => 32000,  'lat' => -6.1900, 'lng' => 106.7950],
            ['nama' => 'Jatipulo',           'kecamatan' => 'Palmerah',   'jumlah' => 30000,  'lat' => -6.1850, 'lng' => 106.8000],
            ['nama' => 'Kemanggisan',        'kecamatan' => 'Palmerah',   'jumlah' => 28000,  'lat' => -6.1950, 'lng' => 106.7850],
            ['nama' => 'Slipi',              'kecamatan' => 'Palmerah',   'jumlah' => 24310,  'lat' => -6.1800, 'lng' => 106.8050],

            // TAMBORA (11 kelurahan)
            ['nama' => 'Tambora',            'kecamatan' => 'Tambora',    'jumlah' => 25000,  'lat' => -6.1450, 'lng' => 106.8100],
            ['nama' => 'Kali Anyar',         'kecamatan' => 'Tambora',    'jumlah' => 22000,  'lat' => -6.1500, 'lng' => 106.8150],
            ['nama' => 'Duri Utara',         'kecamatan' => 'Tambora',    'jumlah' => 20000,  'lat' => -6.1550, 'lng' => 106.8100],
            ['nama' => 'Duri Selatan',       'kecamatan' => 'Tambora',    'jumlah' => 19000,  'lat' => -6.1600, 'lng' => 106.8100],
            ['nama' => 'Angke',              'kecamatan' => 'Tambora',    'jumlah' => 23000,  'lat' => -6.1400, 'lng' => 106.8050],
            ['nama' => 'Jembatan Besi',      'kecamatan' => 'Tambora',    'jumlah' => 21000,  'lat' => -6.1480, 'lng' => 106.8200],
            ['nama' => 'Jembatan Lima',      'kecamatan' => 'Tambora',    'jumlah' => 20000,  'lat' => -6.1530, 'lng' => 106.8200],
            ['nama' => 'Tanah Sereal',       'kecamatan' => 'Tambora',    'jumlah' => 18000,  'lat' => -6.1580, 'lng' => 106.8150],
            ['nama' => 'Pekojan',            'kecamatan' => 'Tambora',    'jumlah' => 17000,  'lat' => -6.1380, 'lng' => 106.8100],
            ['nama' => 'Roa Malaka',         'kecamatan' => 'Tambora',    'jumlah' => 16000,  'lat' => -6.1350, 'lng' => 106.8150],
            ['nama' => 'Krendang',           'kecamatan' => 'Tambora',    'jumlah' => 19909,  'lat' => -6.1520, 'lng' => 106.8250],
        ];

        foreach ($kelurahan as $item) {
            $kec = Kecamatan::where('nama_kecamatan', $item['kecamatan'])->first();
            if ($kec) {
                PendudukKelurahan::create([
                    'kecamatan_id'    => $kec->id,
                    'tahun'           => 2024,
                    'nama_kelurahan'  => $item['nama'],
                    'latitude'        => $item['lat'],
                    'longitude'       => $item['lng'],
                    'jumlah_penduduk' => $item['jumlah'],
                ]);
            }
        }
    }
}
