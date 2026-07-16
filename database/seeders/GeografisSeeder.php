<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\Statistik\BpsClient;
use App\Models\DataGeografis;
use App\Models\Kecamatan;
use App\Models\LuasKecamatan;

class GeografisSeeder extends Seeder
{
    /**
     * Luas wilayah = BPS var 33 (Geografi). Jumlah Kelurahan/RW/RT per kecamatan
     * diambil dari BPS var 155 (Jumlah Kelurahan, RW, RT, KK menurut Kecamatan),
     * domain 3174 = Kota Jakarta Barat. Bila BPS tak terjangkau, kolom RW/RT/
     * kelurahan dibiarkan null (luas tetap terisi dari nilai statis di bawah).
     */

    // nama kecamatan (DB) => vervar BPS pada var 155
    private const VERVAR = [
        'Kembangan'         => 1,
        'Kebon Jeruk'       => 2,
        'Palmerah'          => 3,
        'Grogol Petamburan' => 4,
        'Tambora'           => 5,
        'Taman Sari'        => 6,
        'Cengkareng'        => 7,
        'Kalideres'         => 8,
    ];

    // turvar var 155
    private const T_KELURAHAN = 161;
    private const T_RW        = 162;
    private const T_RT        = 163;

    public function run(): void
    {
        // Data geografis kota
        $geo = DataGeografis::create([
            'tahun'           => 2024,
            'luas_kota_km2'   => 129.54,
            'ketinggian_mdpl' => 7,
            'sumber'          => 'Kota Jakarta Barat Dalam Angka 2025 (BPS)',
        ]);

        // Luas per kecamatan (BPS var 33)
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

        // Administrasi (kelurahan/RW/RT) dari BPS var 155 — tahun terbaru yang tersedia
        $admin = $this->fetchAdmin();

        foreach ($data as $item) {
            $kecamatan = Kecamatan::create(['nama_kecamatan' => $item['nama']]);
            $adm       = $admin[$item['nama']] ?? [];

            LuasKecamatan::create([
                'kecamatan_id'      => $kecamatan->id,
                'data_geografis_id' => $geo->id,
                'luas_km2'          => $item['luas'],
                'persentase'        => $item['persen'],
                'jumlah_kelurahan'  => $adm['kelurahan'] ?? null,
                'jumlah_rw'         => $adm['rw'] ?? null,
                'jumlah_rt'         => $adm['rt'] ?? null,
            ]);
        }
    }

    /**
     * @return array<string, array{kelurahan:?int, rw:?int, rt:?int}>  keyed nama kecamatan
     */
    private function fetchAdmin(): array
    {
        $bps = app(BpsClient::class);

        // Ambil tahun terbaru yang tersedia untuk var 155
        $tahun = $bps->tahunTersedia(155);
        if (empty($tahun)) {
            $this->command?->warn('GeografisSeeder: BPS var 155 tak terjangkau, RW/RT/kelurahan dilewati.');
            return [];
        }
        // Pilih tahun TERBESAR (array bisa terurut menurun)
        $thKode = (string) array_search(max($tahun), $tahun, true);

        $dc = $bps->datacontent(155, $thKode);
        if (empty($dc)) {
            return [];
        }

        // key datacontent: {vervar}155{turvar}{th}0
        $out = [];
        foreach (self::VERVAR as $nama => $vv) {
            $kel = $dc["{$vv}155" . self::T_KELURAHAN . "{$thKode}0"] ?? null;
            $rw  = $dc["{$vv}155" . self::T_RW . "{$thKode}0"] ?? null;
            $rt  = $dc["{$vv}155" . self::T_RT . "{$thKode}0"] ?? null;

            if ($kel !== null || $rw !== null || $rt !== null) {
                $out[$nama] = [
                    'kelurahan' => $kel !== null ? (int) $kel : null,
                    'rw'        => $rw !== null ? (int) $rw : null,
                    'rt'        => $rt !== null ? (int) $rt : null,
                ];
            }
        }

        return $out;
    }
}
