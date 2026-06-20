<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataIklim;

class IklimSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['bulan' => 1,  'hari_hujan' => 17,   'tekanan_udara' => 1009.98, 'suhu_udara' => 28.45, 'kecepatan_angin' => 1.49, 'kelembaban_udara' => 79.72, 'penyinaran_matahari' => 2.8],
            ['bulan' => 2,  'hari_hujan' => 19,   'tekanan_udara' => 1010.85, 'suhu_udara' => 28.33, 'kecepatan_angin' => 1.41, 'kelembaban_udara' => 81.75, 'penyinaran_matahari' => 3.6],
            ['bulan' => 3,  'hari_hujan' => 20,   'tekanan_udara' => 1009.83, 'suhu_udara' => 28.41, 'kecepatan_angin' => 1.22, 'kelembaban_udara' => 81.09, 'penyinaran_matahari' => 3.2],
            ['bulan' => 4,  'hari_hujan' => 14,   'tekanan_udara' => 1009.31, 'suhu_udara' => 29.43, 'kecepatan_angin' => 1.16, 'kelembaban_udara' => 78.65, 'penyinaran_matahari' => 3.8],
            ['bulan' => 5,  'hari_hujan' => 4,    'tekanan_udara' => 1008.27, 'suhu_udara' => 30.13, 'kecepatan_angin' => 1.02, 'kelembaban_udara' => 74.23, 'penyinaran_matahari' => 4.3],
            ['bulan' => 6,  'hari_hujan' => 7,    'tekanan_udara' => 1008.72, 'suhu_udara' => 29.12, 'kecepatan_angin' => 1.09, 'kelembaban_udara' => 77.03, 'penyinaran_matahari' => 4.1],
            ['bulan' => 7,  'hari_hujan' => 28,   'tekanan_udara' => 1010.51, 'suhu_udara' => 27.96, 'kecepatan_angin' => 0.98, 'kelembaban_udara' => 76.98, 'penyinaran_matahari' => 4.5],
            ['bulan' => 8,  'hari_hujan' => 31,   'tekanan_udara' => 1010.36, 'suhu_udara' => 29.02, 'kecepatan_angin' => 1.21, 'kelembaban_udara' => 70.54, 'penyinaran_matahari' => 5.9],
            ['bulan' => 9,  'hari_hujan' => 30,   'tekanan_udara' => 1011.41, 'suhu_udara' => 29.14, 'kecepatan_angin' => 1.20, 'kelembaban_udara' => 71.53, 'penyinaran_matahari' => 6.3],
            ['bulan' => 10, 'hari_hujan' => 31,   'tekanan_udara' => 1010.72, 'suhu_udara' => 30.07, 'kecepatan_angin' => 1.05, 'kelembaban_udara' => 68.85, 'penyinaran_matahari' => 6.0],
            ['bulan' => 11, 'hari_hujan' => 30,   'tekanan_udara' => 1009.32, 'suhu_udara' => 29.21, 'kecepatan_angin' => 1.21, 'kelembaban_udara' => 75.42, 'penyinaran_matahari' => 3.9],
            ['bulan' => 12, 'hari_hujan' => 31,   'tekanan_udara' => 1008.91, 'suhu_udara' => 28.42, 'kecepatan_angin' => 1.44, 'kelembaban_udara' => 78.44, 'penyinaran_matahari' => 1.7],
        ];

        foreach ($data as $item) {
            DataIklim::create(array_merge($item, [
                'tahun'  => 2024,
                'sumber' => 'Kota Jakarta Barat Dalam Angka 2025',
            ]));
        }
    }
}
