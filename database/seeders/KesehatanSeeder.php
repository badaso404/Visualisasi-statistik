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

        // Tenaga kesehatan per kecamatan (hanya total dari sumber;
        // rincian profesi diestimasi proporsional dari jumlah_total).
        $tenaga = [
            ['nama' => 'Palmerah',          'total' => 5082],
            ['nama' => 'Kebon Jeruk',       'total' => 2629],
            ['nama' => 'Kalideres',         'total' => 2109],
            ['nama' => 'Grogol Petamburan', 'total' => 2092],
            ['nama' => 'Cengkareng',        'total' => 1736],
            ['nama' => 'Kembangan',         'total' => 1492],
            ['nama' => 'Taman Sari',        'total' => 502],
            ['nama' => 'Tambora',           'total' => 466],
        ];

        // Proporsi profesi nakes (perawat dominan, lalu bidan & dokter).
        $rasioTenaga = [
            'perawat'   => 0.38,
            'bidan'     => 0.22,
            'dokter'    => 0.20,
            'farmasi'   => 0.12,
            'ahli_gizi' => 0.08,
        ];

        foreach ($tenaga as $item) {
            $kec = Kecamatan::where('nama_kecamatan', $item['nama'])->first();
            if ($kec) {
                $b = $this->pecah($item['total'], $rasioTenaga);
                TenagaKesehatanKecamatan::create([
                    'kecamatan_id' => $kec->id,
                    'tahun'        => 2024,
                    'jumlah_total' => $item['total'],
                    'dokter'       => $b['dokter'],
                    'perawat'      => $b['perawat'],
                    'bidan'        => $b['bidan'],
                    'ahli_gizi'    => $b['ahli_gizi'],
                    'farmasi'      => $b['farmasi'],
                ]);
            }
        }

        // Fasilitas kesehatan per kecamatan (rincian diestimasi proporsional).
        $fasilitas = [
            ['nama' => 'Cengkareng',        'total' => 212],
            ['nama' => 'Kebon Jeruk',       'total' => 200],
            ['nama' => 'Kembangan',         'total' => 180],
            ['nama' => 'Grogol Petamburan', 'total' => 160],
            ['nama' => 'Palmerah',          'total' => 134],
            ['nama' => 'Tambora',           'total' => 129],
            ['nama' => 'Kalideres',         'total' => 117],
            ['nama' => 'Taman Sari',        'total' => 97],
        ];

        // Proporsi jenis fasilitas (posyandu terbanyak, RS paling sedikit).
        $rasioFasilitas = [
            'posyandu'         => 0.70,
            'klinik_kesehatan' => 0.22,
            'puskesmas'        => 0.07,
            'rumah_sakit'      => 0.01,
        ];

        foreach ($fasilitas as $item) {
            $kec = Kecamatan::where('nama_kecamatan', $item['nama'])->first();
            if ($kec) {
                $b = $this->pecah($item['total'], $rasioFasilitas);
                FasilitasKesehatanKecamatan::create([
                    'kecamatan_id'     => $kec->id,
                    'tahun'            => 2024,
                    'jumlah_total'     => $item['total'],
                    'klinik_kesehatan' => $b['klinik_kesehatan'],
                    'posyandu'         => $b['posyandu'],
                    'puskesmas'        => $b['puskesmas'],
                    'rumah_sakit'      => $b['rumah_sakit'],
                ]);
            }
        }
    }

    /**
     * Pecah sebuah total ke beberapa komponen sesuai rasio,
     * lalu buang selisih pembulatan ke komponen rasio terbesar
     * agar jumlah rincian tepat sama dengan total.
     *
     * @param  array<string,float>  $rasio
     * @return array<string,int>
     */
    private function pecah(int $total, array $rasio): array
    {
        $hasil = [];
        foreach ($rasio as $key => $r) {
            $hasil[$key] = (int) round($total * $r);
        }

        // Koreksi selisih pembulatan pada komponen dengan rasio terbesar.
        $terbesar = array_key_first($rasio);
        foreach ($rasio as $key => $r) {
            if ($r > $rasio[$terbesar]) {
                $terbesar = $key;
            }
        }
        $hasil[$terbesar] += $total - array_sum($hasil);
        $hasil[$terbesar] = max(0, $hasil[$terbesar]);

        return $hasil;
    }
}
