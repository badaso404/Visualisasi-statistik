<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataKemiskinan;
use App\Models\KemiskinanKecamatan;
use App\Models\Kecamatan;

class KemiskinanSeeder extends Seeder
{
    public function run(): void
    {
        // Ringkasan indikator per tahun (angka ilustratif, pola BPS Jakarta Barat).
        // 2023 sedikit lebih tinggi dari 2024 → tren menurun.
        DataKemiskinan::create([
            'tahun'                      => 2023,
            'jumlah_penduduk_miskin'     => 95340,
            'persentase_penduduk_miskin' => 3.72,
            'garis_kemiskinan'           => 858469,
            'indeks_kedalaman'           => 0.63,
            'indeks_keparahan'           => 0.16,
            'sumber'                     => 'Kota Jakarta Barat Dalam Angka 2024',
        ]);

        DataKemiskinan::create([
            'tahun'                      => 2024,
            'jumlah_penduduk_miskin'     => 92150,
            'persentase_penduduk_miskin' => 3.55,
            'garis_kemiskinan'           => 915192,
            'indeks_kedalaman'           => 0.58,
            'indeks_keparahan'           => 0.14,
            'sumber'                     => 'Kota Jakarta Barat Dalam Angka 2025',
        ]);

        // Per kecamatan (nilai dasar tahun 2024). 2023 = sedikit lebih tinggi (×1.035).
        $data = [
            ['nama' => 'Cengkareng',        'miskin' => 18500, 'kk' => 5290, 'bantuan' => 20350, 'persen' => 3.42],
            ['nama' => 'Kalideres',         'miskin' => 15200, 'kk' => 4340, 'bantuan' => 16720, 'persen' => 3.31],
            ['nama' => 'Tambora',           'miskin' => 13800, 'kk' => 3940, 'bantuan' => 15180, 'persen' => 5.68],
            ['nama' => 'Kembangan',         'miskin' => 10100, 'kk' => 2890, 'bantuan' => 11110, 'persen' => 3.05],
            ['nama' => 'Palmerah',          'miskin' => 9200,  'kk' => 2630, 'bantuan' => 10120, 'persen' => 4.11],
            ['nama' => 'Kebon Jeruk',       'miskin' => 9400,  'kk' => 2690, 'bantuan' => 10340, 'persen' => 2.88],
            ['nama' => 'Grogol Petamburan', 'miskin' => 8700,  'kk' => 2490, 'bantuan' => 9570,  'persen' => 3.63],
            ['nama' => 'Taman Sari',        'miskin' => 7250,  'kk' => 2070, 'bantuan' => 7980,  'persen' => 4.94],
        ];

        foreach ([2024 => 1.0, 2023 => 1.035] as $tahun => $faktor) {
            foreach ($data as $item) {
                $kec = Kecamatan::where('nama_kecamatan', $item['nama'])->first();
                if (! $kec) {
                    continue;
                }
                KemiskinanKecamatan::create([
                    'kecamatan_id'           => $kec->id,
                    'tahun'                  => $tahun,
                    'jumlah_penduduk_miskin' => (int) round($item['miskin']  * $faktor),
                    'jumlah_keluarga_miskin' => (int) round($item['kk']      * $faktor),
                    'penerima_bantuan'       => (int) round($item['bantuan'] * $faktor),
                    'persentase'             => round($item['persen'] * $faktor, 2),
                ]);
            }
        }
    }
}
