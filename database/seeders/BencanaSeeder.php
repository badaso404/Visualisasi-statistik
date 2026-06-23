<?php

namespace Database\Seeders;

use App\Models\DataBencana;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class BencanaSeeder extends Seeder
{
    public function run(): void
    {
        $kec = Kecamatan::pluck('id', 'nama_kecamatan');

        $data = [
            ['Cengkareng',        'Banjir',         'Kel. Kapuk',           -6.1466, 106.7380, 12, 0, 3500, '2026-01-15'],
            ['Tambora',           'Kebakaran',      'Kel. Angke',           -6.1428, 106.8000, 8,  2, 420,  '2026-02-03'],
            ['Kalideres',         'Banjir',         'Kel. Kamal',           -6.1050, 106.7000, 9,  1, 2800, '2026-01-20'],
            ['Kembangan',         'Tanah Longsor',  'Kel. Srengseng',       -6.2010, 106.7460, 2,  0, 35,   '2026-03-11'],
            ['Grogol Petamburan', 'Banjir',         'Kel. Jelambar',        -6.1580, 106.7900, 6,  0, 1900, '2026-01-18'],
            ['Palmerah',          'Kebakaran',      'Kel. Slipi',           -6.1930, 106.7950, 4,  0, 210,  '2026-02-22'],
            ['Taman Sari',        'Kebakaran',      'Kel. Tangki',          -6.1490, 106.8160, 5,  1, 310,  '2026-02-14'],
            ['Kebon Jeruk',       'Angin Kencang',  'Kel. Duri Kepa',       -6.1900, 106.7680, 3,  0, 80,   '2026-04-05'],
            ['Cengkareng',        'Pohon Tumbang',  'Kel. Cengkareng Barat',-6.1530, 106.7300, 7,  0, 0,    '2026-04-10'],
            ['Tambora',           'Banjir',         'Kel. Pekojan',         -6.1380, 106.8090, 5,  0, 1200, '2026-01-25'],
            ['Kalideres',         'Angin Kencang',  'Kel. Tegal Alur',      -6.1180, 106.7080, 2,  0, 60,   '2026-04-02'],
            ['Kembangan',         'Banjir',         'Kel. Kembangan Utara', -6.1850, 106.7390, 4,  0, 900,  '2026-01-22'],
        ];

        foreach ($data as [$namaKec, $jenis, $lokasi, $lat, $lng, $kejadian, $korban, $terdampak, $tgl]) {
            DataBencana::updateOrCreate(
                ['nama_lokasi' => $lokasi, 'jenis_bencana' => $jenis, 'tahun' => 2026],
                [
                    'kecamatan_id'     => $kec[$namaKec] ?? null,
                    'tanggal_kejadian' => $tgl,
                    'latitude'         => $lat,
                    'longitude'        => $lng,
                    'jumlah_kejadian'  => $kejadian,
                    'jumlah_korban'    => $korban,
                    'jumlah_terdampak' => $terdampak,
                    'keterangan'       => null,
                    'sumber'           => 'BPBD DKI Jakarta 2026',
                ]
            );
        }
    }
}
