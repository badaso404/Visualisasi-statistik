<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JakWifiKecamatan;
use App\Models\CctvKecamatan;
use App\Models\Kecamatan;

class InfrastrukturDigitalSeeder extends Seeder
{
    public function run(): void
    {
        // JakWiFi per kecamatan: [nama => [titik, aktif, pengguna]]
        // Angka contoh (dummy) — silakan sesuaikan dengan data resmi Diskominfotik.
        $jakWifi = [
            2023 => [
                ['nama' => 'Cengkareng',        'titik' => 142, 'aktif' => 128, 'pengguna' => 38200],
                ['nama' => 'Kalideres',         'titik' => 118, 'aktif' => 105, 'pengguna' => 31500],
                ['nama' => 'Kebon Jeruk',       'titik' => 96,  'aktif' => 90,  'pengguna' => 27400],
                ['nama' => 'Kembangan',         'titik' => 88,  'aktif' => 79,  'pengguna' => 22100],
                ['nama' => 'Grogol Petamburan', 'titik' => 104, 'aktif' => 95,  'pengguna' => 29800],
                ['nama' => 'Palmerah',          'titik' => 82,  'aktif' => 74,  'pengguna' => 24600],
                ['nama' => 'Taman Sari',        'titik' => 67,  'aktif' => 61,  'pengguna' => 19300],
                ['nama' => 'Tambora',           'titik' => 73,  'aktif' => 66,  'pengguna' => 21700],
            ],
            2024 => [
                ['nama' => 'Cengkareng',        'titik' => 168, 'aktif' => 156, 'pengguna' => 45700],
                ['nama' => 'Kalideres',         'titik' => 139, 'aktif' => 128, 'pengguna' => 37900],
                ['nama' => 'Kebon Jeruk',       'titik' => 115, 'aktif' => 108, 'pengguna' => 32800],
                ['nama' => 'Kembangan',         'titik' => 102, 'aktif' => 95,  'pengguna' => 26400],
                ['nama' => 'Grogol Petamburan', 'titik' => 121, 'aktif' => 114, 'pengguna' => 35200],
                ['nama' => 'Palmerah',          'titik' => 97,  'aktif' => 90,  'pengguna' => 28900],
                ['nama' => 'Taman Sari',        'titik' => 79,  'aktif' => 73,  'pengguna' => 22800],
                ['nama' => 'Tambora',           'titik' => 86,  'aktif' => 80,  'pengguna' => 25300],
            ],
        ];

        // CCTV per kecamatan: [nama => [unit, aktif, terintegrasi]]
        $cctv = [
            2023 => [
                ['nama' => 'Cengkareng',        'unit' => 214, 'aktif' => 198, 'terintegrasi' => 176],
                ['nama' => 'Kalideres',         'unit' => 187, 'aktif' => 170, 'terintegrasi' => 149],
                ['nama' => 'Kebon Jeruk',       'unit' => 165, 'aktif' => 152, 'terintegrasi' => 138],
                ['nama' => 'Kembangan',         'unit' => 143, 'aktif' => 131, 'terintegrasi' => 118],
                ['nama' => 'Grogol Petamburan', 'unit' => 178, 'aktif' => 164, 'terintegrasi' => 145],
                ['nama' => 'Palmerah',          'unit' => 152, 'aktif' => 140, 'terintegrasi' => 122],
                ['nama' => 'Taman Sari',        'unit' => 129, 'aktif' => 118, 'terintegrasi' => 101],
                ['nama' => 'Tambora',           'unit' => 141, 'aktif' => 130, 'terintegrasi' => 112],
            ],
            2024 => [
                ['nama' => 'Cengkareng',        'unit' => 248, 'aktif' => 236, 'terintegrasi' => 214],
                ['nama' => 'Kalideres',         'unit' => 216, 'aktif' => 203, 'terintegrasi' => 182],
                ['nama' => 'Kebon Jeruk',       'unit' => 192, 'aktif' => 181, 'terintegrasi' => 167],
                ['nama' => 'Kembangan',         'unit' => 168, 'aktif' => 158, 'terintegrasi' => 145],
                ['nama' => 'Grogol Petamburan', 'unit' => 205, 'aktif' => 194, 'terintegrasi' => 178],
                ['nama' => 'Palmerah',          'unit' => 179, 'aktif' => 169, 'terintegrasi' => 152],
                ['nama' => 'Taman Sari',        'unit' => 151, 'aktif' => 142, 'terintegrasi' => 128],
                ['nama' => 'Tambora',           'unit' => 164, 'aktif' => 154, 'terintegrasi' => 138],
            ],
        ];

        $kecamatanId = Kecamatan::pluck('id', 'nama_kecamatan');

        foreach ($jakWifi as $tahun => $rows) {
            foreach ($rows as $item) {
                if ($id = $kecamatanId->get($item['nama'])) {
                    JakWifiKecamatan::create([
                        'kecamatan_id'    => $id,
                        'tahun'           => $tahun,
                        'jumlah_titik'    => $item['titik'],
                        'titik_aktif'     => $item['aktif'],
                        'jumlah_pengguna' => $item['pengguna'],
                        'keterangan'      => 'Data contoh',
                    ]);
                }
            }
        }

        foreach ($cctv as $tahun => $rows) {
            foreach ($rows as $item) {
                if ($id = $kecamatanId->get($item['nama'])) {
                    CctvKecamatan::create([
                        'kecamatan_id' => $id,
                        'tahun'        => $tahun,
                        'jumlah_unit'  => $item['unit'],
                        'unit_aktif'   => $item['aktif'],
                        'terintegrasi' => $item['terintegrasi'],
                        'keterangan'   => 'Data contoh',
                    ]);
                }
            }
        }
    }
}
