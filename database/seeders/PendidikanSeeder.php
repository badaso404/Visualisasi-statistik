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
            ['nama' => 'Cengkareng',        'pelajar' => 61621, 'pendidik' => 3187, 'negeri' => 0, 'swasta' => 0],
            ['nama' => 'Kalideres',         'pelajar' => 56327, 'pendidik' => 3111, 'negeri' => 0, 'swasta' => 0],
            ['nama' => 'Kembangan',         'pelajar' => 43099, 'pendidik' => 2444, 'negeri' => 0, 'swasta' => 0],
            ['nama' => 'Kebon Jeruk',       'pelajar' => 39072, 'pendidik' => 2133, 'negeri' => 0, 'swasta' => 0],
            ['nama' => 'Grogol Petamburan', 'pelajar' => 27434, 'pendidik' => 1536, 'negeri' => 0, 'swasta' => 0],
            ['nama' => 'Palmerah',          'pelajar' => 22292, 'pendidik' => 1125, 'negeri' => 0, 'swasta' => 0],
            ['nama' => 'Tambora',           'pelajar' => 18876, 'pendidik' => 1064, 'negeri' => 0, 'swasta' => 0],
            ['nama' => 'Taman Sari',        'pelajar' => 11584, 'pendidik' => 688,  'negeri' => 0, 'swasta' => 0],
        ];

        foreach ($data as $item) {
            $kec = Kecamatan::where('nama_kecamatan', $item['nama'])->first();
            if ($kec) {
                PendidikanKecamatan::create([
                    'kecamatan_id'          => $kec->id,
                    'tahun'                 => 2024,
                    'jumlah_pelajar'        => $item['pelajar'],
                    'jumlah_pendidik'       => $item['pendidik'],
                    'jumlah_sekolah_negeri' => $item['negeri'],
                    'jumlah_sekolah_swasta' => $item['swasta'],
                ]);
            }
        }
    }
}
