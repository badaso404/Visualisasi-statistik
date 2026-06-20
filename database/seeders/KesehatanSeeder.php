<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataKesehatan;
use App\Models\TenagaKesehatanKecamatan;
use App\Models\FasilitasKesehatanKecamatan;
use App\Models\Kecamatan;

class KesehatanSeeder extends Seeder
{
    public function run(): void
    {
        // Summary kota
        DataKesehatan::create([
            'tahun'                   => 2024,
            'jumlah_tempat_tidur_rs'  => 4820,
            'cakupan_imunisasi_dasar' => 103.2,
            'sumber'                  => 'Kota Jakarta Barat Dalam Angka 2025',
        ]);

        // Tenaga kesehatan per kecamatan
        $tenaga = [
            ['nama' => 'Palmerah',          'total' => 5082, 'dokter' => 0, 'perawat' => 0, 'bidan' => 0, 'ahli_gizi' => 0, 'farmasi' => 0],
            ['nama' => 'Kebon Jeruk',       'total' => 2629, 'dokter' => 0, 'perawat' => 0, 'bidan' => 0, 'ahli_gizi' => 0, 'farmasi' => 0],
            ['nama' => 'Kalideres',         'total' => 2109, 'dokter' => 0, 'perawat' => 0, 'bidan' => 0, 'ahli_gizi' => 0, 'farmasi' => 0],
            ['nama' => 'Grogol Petamburan', 'total' => 2092, 'dokter' => 0, 'perawat' => 0, 'bidan' => 0, 'ahli_gizi' => 0, 'farmasi' => 0],
            ['nama' => 'Cengkareng',        'total' => 1736, 'dokter' => 0, 'perawat' => 0, 'bidan' => 0, 'ahli_gizi' => 0, 'farmasi' => 0],
            ['nama' => 'Kembangan',         'total' => 1492, 'dokter' => 0, 'perawat' => 0, 'bidan' => 0, 'ahli_gizi' => 0, 'farmasi' => 0],
            ['nama' => 'Taman Sari',        'total' => 502,  'dokter' => 0, 'perawat' => 0, 'bidan' => 0, 'ahli_gizi' => 0, 'farmasi' => 0],
            ['nama' => 'Tambora',           'total' => 466,  'dokter' => 0, 'perawat' => 0, 'bidan' => 0, 'ahli_gizi' => 0, 'farmasi' => 0],
        ];

        foreach ($tenaga as $item) {
            $kec = Kecamatan::where('nama_kecamatan', $item['nama'])->first();
            if ($kec) {
                TenagaKesehatanKecamatan::create([
                    'kecamatan_id' => $kec->id,
                    'tahun'        => 2024,
                    'jumlah_total' => $item['total'],
                    'dokter'       => $item['dokter'],
                    'perawat'      => $item['perawat'],
                    'bidan'        => $item['bidan'],
                    'ahli_gizi'    => $item['ahli_gizi'],
                    'farmasi'      => $item['farmasi'],
                ]);
            }
        }

        // Fasilitas kesehatan per kecamatan
        $fasilitas = [
            ['nama' => 'Cengkareng',        'total' => 212, 'klinik' => 0, 'posyandu' => 0, 'puskesmas' => 0, 'rs' => 0],
            ['nama' => 'Kebon Jeruk',       'total' => 200, 'klinik' => 0, 'posyandu' => 0, 'puskesmas' => 0, 'rs' => 0],
            ['nama' => 'Kembangan',         'total' => 180, 'klinik' => 0, 'posyandu' => 0, 'puskesmas' => 0, 'rs' => 0],
            ['nama' => 'Grogol Petamburan', 'total' => 160, 'klinik' => 0, 'posyandu' => 0, 'puskesmas' => 0, 'rs' => 0],
            ['nama' => 'Palmerah',          'total' => 134, 'klinik' => 0, 'posyandu' => 0, 'puskesmas' => 0, 'rs' => 0],
            ['nama' => 'Tambora',           'total' => 129, 'klinik' => 0, 'posyandu' => 0, 'puskesmas' => 0, 'rs' => 0],
            ['nama' => 'Kalideres',         'total' => 117, 'klinik' => 0, 'posyandu' => 0, 'puskesmas' => 0, 'rs' => 0],
            ['nama' => 'Taman Sari',        'total' => 97,  'klinik' => 0, 'posyandu' => 0, 'puskesmas' => 0, 'rs' => 0],
        ];

        foreach ($fasilitas as $item) {
            $kec = Kecamatan::where('nama_kecamatan', $item['nama'])->first();
            if ($kec) {
                FasilitasKesehatanKecamatan::create([
                    'kecamatan_id'    => $kec->id,
                    'tahun'           => 2024,
                    'jumlah_total'    => $item['total'],
                    'klinik_kesehatan' => $item['klinik'],
                    'posyandu'        => $item['posyandu'],
                    'puskesmas'       => $item['puskesmas'],
                    'rumah_sakit'     => $item['rs'],
                ]);
            }
        }
    }
}
