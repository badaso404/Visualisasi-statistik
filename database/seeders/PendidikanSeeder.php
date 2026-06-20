<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataPendidikan;
use App\Models\PendidikanKecamatan;
use App\Models\Kecamatan;

class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        // Summary APM & APK
        DataPendidikan::create([
            'tahun'           => 2024,
            'apm_sd_mi'       => 98.13,
            'apm_smp_mts'     => 90.98,
            'apm_sma_smk_man' => 62.98,
            'apk_sd_mi'       => 101.91,
            'apk_smp_mts'     => 98.15,
            'apk_sma_smk_man' => 74.77,
            'sumber'          => 'Kota Jakarta Barat Dalam Angka 2025',
        ]);

        // Per kecamatan
        $data = [
            ['nama' => 'Cengkareng',        'murid' => 61621, 'guru' => 3187, 'sekolah' => 0],
            ['nama' => 'Kalideres',         'murid' => 56327, 'guru' => 3111, 'sekolah' => 0],
            ['nama' => 'Kembangan',         'murid' => 43099, 'guru' => 2444, 'sekolah' => 0],
            ['nama' => 'Kebon Jeruk',       'murid' => 39072, 'guru' => 2133, 'sekolah' => 0],
            ['nama' => 'Grogol Petamburan', 'murid' => 27434, 'guru' => 1536, 'sekolah' => 0],
            ['nama' => 'Palmerah',          'murid' => 22292, 'guru' => 1125, 'sekolah' => 0],
            ['nama' => 'Tambora',           'murid' => 18876, 'guru' => 1064, 'sekolah' => 0],
            ['nama' => 'Taman Sari',        'murid' => 11584, 'guru' => 688,  'sekolah' => 0],
        ];

        foreach ($data as $item) {
            $kec = Kecamatan::where('nama_kecamatan', $item['nama'])->first();
            if ($kec) {
                PendidikanKecamatan::create([
                    'kecamatan_id'  => $kec->id,
                    'tahun'         => 2024,
                    'jumlah_murid'  => $item['murid'],
                    'jumlah_guru'   => $item['guru'],
                    'jumlah_sekolah' => $item['sekolah'],
                ]);
            }
        }
    }
}
