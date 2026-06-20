<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataGeografis;
use App\Models\Kecamatan;
use App\Models\LuasKecamatan;

class GeografisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data geografis kota
        $geo = DataGeografis::create([
            'tahun'         => 2024,
            'luas_kota_km2' => 129.54,
            'ketinggian_mdpl' => 7,
            'sumber'        => 'Kota Jakarta Barat Dalam Angka 2025',
        ]);

        // Data kecamatan
        $data = [
            ['nama' => 'Kalideres',         'luas' => 30.23, 'persen' => 23.3],
            ['nama' => 'Cengkareng',        'luas' => 26.54, 'persen' => 20.5],
            ['nama' => 'Kembangan',         'luas' => 24.16, 'persen' => 18.7],
            ['nama' => 'Kebon Jeruk',       'luas' => 17.98, 'persen' => 13.9],
            ['nama' => 'Grogol Petamburan', 'luas' => 9.99,  'persen' => 7.7],
            ['nama' => 'Taman Sari',        'luas' => 7.73,  'persen' => 6.0],
            ['nama' => 'Palmerah',          'luas' => 7.51,  'persen' => 5.8],
            ['nama' => 'Tambora',           'luas' => 5.4,   'persen' => 4.1],
        ];

        foreach ($data as $item) {
            $kecamatan = Kecamatan::create(['nama_kecamatan' => $item['nama']]);

            LuasKecamatan::create([
                'kecamatan_id'      => $kecamatan->id,
                'data_geografis_id' => $geo->id,
                'luas_km2'          => $item['luas'],
                'persentase'        => $item['persen'],
            ]);
        }
    }
}
