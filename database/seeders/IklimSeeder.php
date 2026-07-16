<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\Statistik\BpsClient;
use App\Models\DataIklim;

class IklimSeeder extends Seeder
{
    /**
     * Data iklim bulanan diambil LANGSUNG dari BPS WebAPI (domain 3174 = Kota
     * Jakarta Barat). BPS sendiri menyusun ini dari BMKG Stasiun Kemayoran
     * (lihat `note` pada respons API), jadi ini data BMKG yang sudah dibersihkan,
     * historis, dan berformat JSON rapih.
     *
     * Tiga variabel digabung menjadi satu baris per (tahun, bulan):
     *   - var 116 "Keadaan Iklim" → hari hujan, tekanan, kecepatan angin, penyinaran
     *   - var 64  "Suhu"          → rata-rata (turvar 44)
     *   - var 115 "Kelembaban"    → rata-rata (turvar 44)
     *
     * Key datacontent BPS berformat: {vervar}{var}{turvar}{thKode}{turth}.
     * Untuk iklim, vervar = BULAN (1-12) dan turth = 0.
     */
    public function run(): void
    {
        $rows = $this->fetchBps();

        if (empty($rows)) {
            $this->command?->warn('IklimSeeder: gagal mengambil data BPS, dilewati.');
            return;
        }

        DataIklim::truncate();   // idempoten — hanya data BPS yang ditampilkan

        foreach ($rows as $row) {
            DataIklim::create($row);
        }

        $tahunUnik = array_values(array_unique(array_map(fn ($r) => $r['tahun'], $rows)));
        sort($tahunUnik);
        $this->command?->info('IklimSeeder: ' . count($rows) . ' baris bulanan diambil dari BPS ('
            . implode(', ', $tahunUnik) . ').');
    }

    /**
     * turvar var 116 → kolom tabel data_iklim.
     * Curah hujan (48) sengaja tidak diambil karena tabel belum punya kolomnya.
     */
    private const KEADAAN_IKLIM = [
        49 => 'hari_hujan',
        50 => 'tekanan_udara',
        51 => 'kecepatan_angin',
        52 => 'penyinaran_matahari',
    ];

    private const TURVAR_RATA_RATA = 44;   // untuk var 64 (suhu) & var 115 (kelembaban)

    /**
     * Tarik & rakit data BPS menjadi baris siap-insert. Mengembalikan array baris
     * [['tahun'=>.., 'bulan'=>.., 'hari_hujan'=>.., ...], ...].
     *
     * Basis tahun mengikuti ketersediaan var 116; suhu & kelembaban di-enrich
     * bila tahunnya tersedia (nilai yang hilang di-default 0, konsisten dengan
     * seeder lain).
     */
    private function fetchBps(): array
    {
        $bps = app(BpsClient::class);

        // Dibatasi 4 tahun terakhir (2020-2023) agar dropdown tahun ringkas dan
        // sejajar dengan modul lain. BPS Jakbar tidak punya iklim > 2023, dan 2022
        // kosong, jadi efektifnya 2020, 2021, 2023.
        $rows = [];
        foreach ($bps->tahunTersedia(var: 116, minTahun: 2020) as $thKode => $tahun) {
            $iklim      = $bps->datacontent(var: 116, thKode: $thKode);
            $suhu       = $bps->datacontent(var: 64,  thKode: $thKode);
            $kelembaban = $bps->datacontent(var: 115, thKode: $thKode);

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $baris = ['tahun' => $tahun, 'bulan' => $bulan];
                $adaData = false;

                foreach (self::KEADAAN_IKLIM as $turvar => $kolom) {
                    $key = "{$bulan}116{$turvar}{$thKode}0";
                    if (isset($iklim[$key])) {
                        $baris[$kolom] = $iklim[$key];
                        $adaData = true;
                    }
                }

                $keySuhu = "{$bulan}64" . self::TURVAR_RATA_RATA . "{$thKode}0";
                if (isset($suhu[$keySuhu])) {
                    $baris['suhu_udara'] = $suhu[$keySuhu];
                    $adaData = true;
                }

                $keyKelembaban = "{$bulan}115" . self::TURVAR_RATA_RATA . "{$thKode}0";
                if (isset($kelembaban[$keyKelembaban])) {
                    $baris['kelembaban_udara'] = $kelembaban[$keyKelembaban];
                    $adaData = true;
                }

                // Lewati bulan yang benar-benar kosong di semua sumber.
                if (! $adaData) {
                    continue;
                }

                // Kolom tabel NOT NULL → default 0 untuk metrik yang tak tersedia
                // pada bulan tersebut (mengikuti pola `?? 0` seeder lain).
                $rows[] = array_merge([
                    'hari_hujan'          => 0,
                    'tekanan_udara'       => 0,
                    'suhu_udara'          => 0,
                    'kecepatan_angin'     => 0,
                    'kelembaban_udara'    => 0,
                    'penyinaran_matahari' => 0,
                    'sumber'              => 'BPS Kota Jakarta Barat (webapi.bps.go.id)',
                ], $baris);
            }
        }

        return $rows;
    }
}
