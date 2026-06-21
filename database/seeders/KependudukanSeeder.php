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

        $summaryData = [
            2024 => ['laki' => 1271376, 'perempuan' => 1285376, 'total' => 2556752, 'sumber' => 'Kota Jakarta Barat Dalam Angka 2025'],
            2025 => ['laki' => 1284090, 'perempuan' => 1298230, 'total' => 2582320, 'sumber' => 'Kota Jakarta Barat Dalam Angka 2026 (Proyeksi)'],
            2026 => ['laki' => 1297051, 'perempuan' => 1311835, 'total' => 2608886, 'sumber' => 'Kota Jakarta Barat Dalam Angka 2027 (Proyeksi)'],
        ];

        foreach ($summaryData as $tahun => $s) {
            DataKependudukan::create([
                'tahun'            => $tahun,
                'jumlah_laki_laki' => $s['laki'],
                'jumlah_perempuan' => $s['perempuan'],
                'jumlah_total'     => $s['total'],
                'sumber'           => $s['sumber'],
            ]);
        }

        $kecamatanData = [
            2024 => [
                'Cengkareng'        => 581788,
                'Kalideres'         => 464076,
                'Kebon Jeruk'       => 362641,
                'Kembangan'         => 311262,
                'Tambora'           => 258061,
                'Grogol Petamburan' => 230173,
                'Palmerah'          => 225842,
                'Taman Sari'        => 122909,
            ],
            2025 => [
                'Cengkareng'        => 587606,
                'Kalideres'         => 468717,
                'Kebon Jeruk'       => 366267,
                'Kembangan'         => 314375,
                'Tambora'           => 260642,
                'Grogol Petamburan' => 232475,
                'Palmerah'          => 228100,
                'Taman Sari'        => 124138,
            ],
            2026 => [
                'Cengkareng'        => 593424,
                'Kalideres'         => 473357,
                'Kebon Jeruk'       => 369894,
                'Kembangan'         => 317487,
                'Tambora'           => 263222,
                'Grogol Petamburan' => 234776,
                'Palmerah'          => 230359,
                'Taman Sari'        => 125367,
            ],
        ];

        foreach ($kecamatanData as $tahun => $kecList) {
            foreach ($kecList as $nama => $jumlah) {
                $kec = Kecamatan::where('nama_kecamatan', $nama)->first();
                if ($kec) {
                    PendudukKecamatan::create([
                        'kecamatan_id'    => $kec->id,
                        'tahun'           => $tahun,
                        'jumlah_penduduk' => $jumlah,
                    ]);
                }
            }
        }

        $kelurahan2024 = [
            // CENGKARENG
            ['nama' => 'Cengkareng Barat',      'kecamatan' => 'Cengkareng',        'jumlah' => 81567,  'lat' => -6.149344020220700,  'lng' => 106.72191972604004],
            ['nama' => 'Cengkareng Timur',      'kecamatan' => 'Cengkareng',        'jumlah' => 103122, 'lat' => -6.144990018828093,  'lng' => 106.73321442418903],
            ['nama' => 'Duri Kosambi',          'kecamatan' => 'Cengkareng',        'jumlah' => 102437, 'lat' => -6.169915026813531,  'lng' => 106.72240541069682],
            ['nama' => 'Kapuk',                 'kecamatan' => 'Cengkareng',        'jumlah' => 171181, 'lat' => -6.1311682818773585, 'lng' => 106.74617941069658],
            ['nama' => 'Kedaung Kali Angke',    'kecamatan' => 'Cengkareng',        'jumlah' => 45231,  'lat' => -6.153358588524503,  'lng' => 106.76190789535333],
            ['nama' => 'Rawa Buaya',            'kecamatan' => 'Cengkareng',        'jumlah' => 82798,  'lat' => -6.1695836601732985, 'lng' => 106.73422248371193],
            // KALIDERES
            ['nama' => 'Kalideres',             'kecamatan' => 'Kalideres',         'jumlah' => 92090,  'lat' => -6.143146918238906,  'lng' => 106.6925258106967],
            ['nama' => 'Pegadungan',            'kecamatan' => 'Kalideres',         'jumlah' => 100018, 'lat' => -6.12978298393808,   'lng' => 106.7108902395324],
            ['nama' => 'Semanan',               'kecamatan' => 'Kalideres',         'jumlah' => 93033,  'lat' => -6.159920858710631,  'lng' => 106.7053791241892],
            ['nama' => 'Tegal Alur',            'kecamatan' => 'Kalideres',         'jumlah' => 106972, 'lat' => -6.117433580320804,  'lng' => 106.71951694841304],
            ['nama' => 'Kamal',                 'kecamatan' => 'Kalideres',         'jumlah' => 71885,  'lat' => -6.104317189875106,  'lng' => 106.70606998000952],
            // KEMBANGAN
            ['nama' => 'Kembangan Utara',       'kecamatan' => 'Kembangan',         'jumlah' => 58000,  'lat' => -6.17903019526423,   'lng' => 106.73527576651773],
            ['nama' => 'Kembangan Selatan',     'kecamatan' => 'Kembangan',         'jumlah' => 52000,  'lat' => -6.183295463198552,  'lng' => 106.7518238818611],
            ['nama' => 'Meruya Utara',          'kecamatan' => 'Kembangan',         'jumlah' => 55000,  'lat' => -6.196196551424615,  'lng' => 106.74767752418948],
            ['nama' => 'Meruya Selatan',        'kecamatan' => 'Kembangan',         'jumlah' => 48000,  'lat' => -6.214414646431168,  'lng' => 106.73633710884627],
            ['nama' => 'Srengseng',             'kecamatan' => 'Kembangan',         'jumlah' => 51000,  'lat' => -6.203312671552099,  'lng' => 106.75674408186129],
            ['nama' => 'Joglo',                 'kecamatan' => 'Kembangan',         'jumlah' => 57262,  'lat' => -6.2201742669590905, 'lng' => 106.73534603263279],
            // KEBON JERUK
            ['nama' => 'Kebon Jeruk',           'kecamatan' => 'Kebon Jeruk',       'jumlah' => 45000,  'lat' => -6.189812964638843,  'lng' => 106.77297459535366],
            ['nama' => 'Sukabumi Utara',        'kecamatan' => 'Kebon Jeruk',       'jumlah' => 38000,  'lat' => -6.209994527897751,  'lng' => 106.77761033768205],
            ['nama' => 'Sukabumi Selatan',      'kecamatan' => 'Kebon Jeruk',       'jumlah' => 42000,  'lat' => -6.217627207911728,  'lng' => 106.76995892418982],
            ['nama' => 'Kelapa Dua',            'kecamatan' => 'Kebon Jeruk',       'jumlah' => 52000,  'lat' => -6.209248160394786,  'lng' => 106.76857017467526],
            ['nama' => 'Duri Kepa',             'kecamatan' => 'Kebon Jeruk',       'jumlah' => 48000,  'lat' => -6.184954763565069,  'lng' => 106.77327345302531],
            ['nama' => 'Kedoya Utara',          'kecamatan' => 'Kebon Jeruk',       'jumlah' => 44000,  'lat' => -6.17683642796524,   'lng' => 106.76742196836871],
            ['nama' => 'Kedoya Selatan',        'kecamatan' => 'Kebon Jeruk',       'jumlah' => 40000,  'lat' => -6.179565598518912,  'lng' => 106.75898572418947],
            // GROGOL PETAMBURAN
            ['nama' => 'Grogol',                'kecamatan' => 'Grogol Petamburan', 'jumlah' => 38000,  'lat' => -6.161100,           'lng' => 106.794400],
            ['nama' => 'Tomang',                'kecamatan' => 'Grogol Petamburan', 'jumlah' => 35000,  'lat' => -6.171459594747076,  'lng' => 106.80038916638735],
            ['nama' => 'Jelambar',              'kecamatan' => 'Grogol Petamburan', 'jumlah' => 32000,  'lat' => -6.161140595392994,  'lng' => 106.78150198918355],
            ['nama' => 'Jelambar Baru',         'kecamatan' => 'Grogol Petamburan', 'jumlah' => 30000,  'lat' => -6.147061119490367,  'lng' => 106.78148983953247],
            ['nama' => 'Tanjung Duren Utara',   'kecamatan' => 'Grogol Petamburan', 'jumlah' => 28000,  'lat' => -6.173309227331231,  'lng' => 106.78897351069689],
            ['nama' => 'Tanjung Duren Selatan', 'kecamatan' => 'Grogol Petamburan', 'jumlah' => 27000,  'lat' => -6.181190794420229,  'lng' => 106.78777436836856],
            ['nama' => 'Wijaya Kusuma',         'kecamatan' => 'Grogol Petamburan', 'jumlah' => 40173,  'lat' => -6.153337382202968,  'lng' => 106.77251192604007],
            // TAMAN SARI
            ['nama' => 'Taman Sari',            'kecamatan' => 'Taman Sari',        'jumlah' => 18000,  'lat' => -6.14379732685488,   'lng' => 106.82321928794053],
            ['nama' => 'Krukut',                'kecamatan' => 'Taman Sari',        'jumlah' => 17000,  'lat' => -6.152019811262052,  'lng' => 106.81768659879287],
            ['nama' => 'Maphar',                'kecamatan' => 'Taman Sari',        'jumlah' => 16000,  'lat' => -6.153510688570124,  'lng' => 106.8192082241892],
            ['nama' => 'Tangki',                'kecamatan' => 'Taman Sari',        'jumlah' => 15000,  'lat' => -6.146765925224026,  'lng' => 106.82253608000981],
            ['nama' => 'Mangga Besar',          'kecamatan' => 'Taman Sari',        'jumlah' => 19000,  'lat' => -6.147259296421718,  'lng' => 106.81917646926611],
            ['nama' => 'Keagungan',             'kecamatan' => 'Taman Sari',        'jumlah' => 18000,  'lat' => -6.1508067231126615, 'lng' => 106.81496859535332],
            ['nama' => 'Glodok',                'kecamatan' => 'Taman Sari',        'jumlah' => 19909,  'lat' => -6.146131019192923,  'lng' => 106.81299589535321],
            ['nama' => 'Pinangsia',             'kecamatan' => 'Taman Sari',        'jumlah' => 17400,  'lat' => -6.137994316592649,  'lng' => 106.81956549535323],
            // PALMERAH
            ['nama' => 'Palmerah',              'kecamatan' => 'Palmerah',          'jumlah' => 76532,  'lat' => -6.201689686563981,  'lng' => 106.78720794565332],
            ['nama' => 'Kota Bambu Utara',      'kecamatan' => 'Palmerah',          'jumlah' => 35000,  'lat' => -6.185321665390175,  'lng' => 106.80740559535369],
            ['nama' => 'Kota Bambu Selatan',    'kecamatan' => 'Palmerah',          'jumlah' => 32000,  'lat' => -6.1849208031456016, 'lng' => 106.80201705302532],
            ['nama' => 'Jatipulo',              'kecamatan' => 'Palmerah',          'jumlah' => 30000,  'lat' => -6.176977394530814,  'lng' => 106.80416789350268],
            ['nama' => 'Kemanggisan',           'kecamatan' => 'Palmerah',          'jumlah' => 28000,  'lat' => -6.192227733989439,  'lng' => 106.78677413953291],
            ['nama' => 'Slipi',                 'kecamatan' => 'Palmerah',          'jumlah' => 24310,  'lat' => -6.193519564092993,  'lng' => 106.80198692604048],
            // TAMBORA
            ['nama' => 'Tambora',               'kecamatan' => 'Tambora',           'jumlah' => 25000,  'lat' => -6.1465594492488265, 'lng' => 106.8085163800099],
            ['nama' => 'Kali Anyar',            'kecamatan' => 'Tambora',           'jumlah' => 22000,  'lat' => -6.156272157242822,  'lng' => 106.79929733768162],
            ['nama' => 'Duri Utara',            'kecamatan' => 'Tambora',           'jumlah' => 20000,  'lat' => -6.154708488929626,  'lng' => 106.8018943260401],
            ['nama' => 'Duri Selatan',          'kecamatan' => 'Tambora',           'jumlah' => 19000,  'lat' => -6.156660786098641,  'lng' => 106.80231319535345],
            ['nama' => 'Angke',                 'kecamatan' => 'Tambora',           'jumlah' => 23000,  'lat' => -6.143385290513976,  'lng' => 106.80015358186078],
            ['nama' => 'Jembatan Besi',         'kecamatan' => 'Tambora',           'jumlah' => 21000,  'lat' => -6.150938590207726,  'lng' => 106.79943978000995],
            ['nama' => 'Jembatan Lima',         'kecamatan' => 'Tambora',           'jumlah' => 20000,  'lat' => -6.145797985414383,  'lng' => 106.80164659615713],
            ['nama' => 'Tanah Sereal',          'kecamatan' => 'Tambora',           'jumlah' => 18000,  'lat' => -6.157298178193364,  'lng' => 106.80887019588408],
            ['nama' => 'Pekojan',               'kecamatan' => 'Tambora',           'jumlah' => 17000,  'lat' => -6.139387986235091,  'lng' => 106.80069726651736],
            ['nama' => 'Roa Malaka',            'kecamatan' => 'Tambora',           'jumlah' => 16000,  'lat' => -6.132476021774949,  'lng' => 106.80749719535319],
            ['nama' => 'Krendang',              'kecamatan' => 'Tambora',           'jumlah' => 19909,  'lat' => -6.152040690405676,  'lng' => 106.80179075302496],
        ];

        $scaleFactor = [2024 => 1.00, 2025 => 1.01, 2026 => 1.02];

        foreach ($scaleFactor as $tahun => $factor) {
            foreach ($kelurahan2024 as $item) {
                $kec = Kecamatan::where('nama_kecamatan', $item['kecamatan'])->first();
                if ($kec) {
                    PendudukKelurahan::create([
                        'kecamatan_id'    => $kec->id,
                        'tahun'           => $tahun,
                        'nama_kelurahan'  => $item['nama'],
                        'latitude'        => $item['lat'],
                        'longitude'       => $item['lng'],
                        'jumlah_penduduk' => (int) round($item['jumlah'] * $factor),
                    ]);
                }
            }
        }
    }
}
