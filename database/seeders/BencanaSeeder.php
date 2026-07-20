<?php

namespace Database\Seeders;

use App\Models\DataBencana;
use Illuminate\Database\Seeder;

/**
 * Rekap bencana triwulanan Jakarta Barat — mengikuti bentuk data
 * API Satu Data Jakarta (per triwulan per jenis, tanpa lokasi/tanggal).
 *
 * Sengaja tidak menembak API saat seeding supaya proses seed tetap jalan
 * tanpa koneksi internet. Untuk data terbaru, gunakan tombol
 * "Sync dari API" di portal admin Kebencanaan.
 */
class BencanaSeeder extends Seeder
{
    public function run(): void
    {
        // [tahun, triwulan, jenis, kejadian, korban meninggal, korban luka]
        $data = [
            [2024, 1, 'Banjir', 64, 0, 0],
            [2024, 1, 'Pohon Tumbang', 30, 0, 1],
            [2024, 2, 'Banjir', 11, 0, 0],
            [2024, 2, 'Pohon Tumbang', 8, 0, 1],
            [2024, 3, 'Banjir', 17, 0, 0],
            [2024, 3, 'Pohon Tumbang', 23, 0, 1],
            [2024, 4, 'Banjir', 17, 0, 0],
            [2024, 4, 'Pohon Tumbang', 24, 0, 0],
            [2025, 1, 'Banjir', 41, 0, 0],
            [2025, 1, 'Pohon Tumbang', 18, 0, 1],
            [2025, 2, 'Angin Kencang', 4, 0, 3],
            [2025, 2, 'Banjir', 18, 0, 0],
            [2025, 2, 'Pohon Tumbang', 19, 0, 0],
            [2025, 3, 'Banjir', 38, 0, 0],
            [2025, 3, 'Pohon Tumbang', 23, 0, 3],
            [2025, 4, 'Angin Kencang', 1, 0, 0],
            [2025, 4, 'Banjir', 14, 0, 0],
            [2025, 4, 'Pohon Tumbang', 21, 0, 0],
            [2026, 1, 'Banjir', 63, 0, 0],
            [2026, 1, 'Pohon Tumbang', 12, 0, 1],
        ];

        foreach ($data as [$tahun, $triwulan, $jenis, $kejadian, $meninggal, $luka]) {
            DataBencana::updateOrCreate(
                [
                    'periode_data'  => sprintf('%04d%02d', $tahun, $triwulan * 3),
                    'wilayah'       => DataBencana::WILAYAH_JAKBAR,
                    'jenis_bencana' => $jenis,
                ],
                [
                    'tahun'                   => $tahun,
                    'triwulan'                => $triwulan,
                    'jumlah_kejadian'         => $kejadian,
                    'jumlah_korban_meninggal' => $meninggal,
                    'jumlah_korban_luka'      => $luka,
                    'sumber'                  => 'Satu Data Jakarta',
                ]
            );
        }
    }
}
