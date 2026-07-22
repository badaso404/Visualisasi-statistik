<?php

namespace App\Services\Statistik;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Menjalankan seeder BPS dari portal admin, bukan hanya dari baris perintah.
 *
 * Sebelumnya pembaruan data tahunan hanya bisa lewat `php artisan db:seed
 * --class=...` di server, sehingga operator bergantung pada akses SSH untuk
 * pekerjaan rutin. Logika pengambilan BPS-nya sendiri tidak dipindah ke sini:
 * seeder sudah memuatnya lengkap dengan pemetaan kode var/turvar, dan
 * menyalinnya ke tempat kedua hanya akan membuat keduanya lekas berbeda.
 *
 * Semua seeder yang dijangkau di sini WAJIB idempoten (updateOrCreate, tanpa
 * truncate) — lihat catatan di masing-masing seeder.
 */
class SinkronisasiBps
{
    /**
     * modul => [label, kelas seeder, tabel yang disentuh].
     *
     * Tabel dipakai untuk menghitung baris sebelum/sesudah sebagai pemeriksaan
     * silang atas jumlah yang dilaporkan event Eloquent.
     */
    private const MODUL = [
        'geografis' => [
            'label'  => 'Geografis',
            'seeder' => \Database\Seeders\GeografisSeeder::class,
            'tabel'  => ['data_geografis', 'luas_kecamatan'],
        ],
        'iklim' => [
            'label'  => 'Iklim',
            'seeder' => \Database\Seeders\IklimSeeder::class,
            'tabel'  => ['data_iklim'],
        ],
        'kependudukan' => [
            'label'  => 'Kependudukan',
            'seeder' => \Database\Seeders\KependudukanSeeder::class,
            'tabel'  => ['data_kependudukan', 'penduduk_kecamatan', 'penduduk_kelurahan'],
        ],
        'pendidikan' => [
            'label'  => 'Pendidikan',
            'seeder' => \Database\Seeders\PendidikanSeeder::class,
            'tabel'  => ['data_pendidikan', 'pendidikan_kecamatan'],
        ],
        'kesehatan' => [
            'label'  => 'Kesehatan',
            'seeder' => \Database\Seeders\KesehatanSeeder::class,
            'tabel'  => ['data_kesehatan', 'tenaga_kesehatan_kecamatan', 'fasilitas_kesehatan_kecamatan'],
        ],
        'kemiskinan' => [
            'label'  => 'Kemiskinan',
            'seeder' => \Database\Seeders\KemiskinanSeeder::class,
            'tabel'  => ['data_kemiskinan'],
        ],
        'perekonomian' => [
            'label'  => 'Perekonomian',
            'seeder' => \Database\Seeders\PerekonomianSeeder::class,
            'tabel'  => ['data_perekonomian', 'pdrb_sektor'],
        ],
    ];

    public static function daftarModul(): array
    {
        return array_map(fn ($m) => $m['label'], self::MODUL);
    }

    public static function dikenal(string $modul): bool
    {
        return isset(self::MODUL[$modul]);
    }

    public static function label(string $modul): string
    {
        return self::MODUL[$modul]['label'] ?? $modul;
    }

    /**
     * Jalankan sinkronisasi satu modul.
     *
     * @return array{ditambah:int, diperbarui:int, error:?string}
     */
    public function jalankan(string $modul): array
    {
        $hasil = ['ditambah' => 0, 'diperbarui' => 0, 'error' => null];

        if (!self::dikenal($modul)) {
            $hasil['error'] = "Modul '{$modul}' tidak dikenal.";

            return $hasil;
        }

        $kelas = self::MODUL[$modul]['seeder'];

        // Jumlah baris dihitung dari event Eloquent, bukan dari selisih COUNT:
        // seeder memakai updateOrCreate sehingga baris yang diperbarui tidak
        // mengubah jumlah baris sama sekali, dan selisih COUNT akan melaporkan
        // "0 perubahan" padahal datanya baru saja disegarkan.
        $ditambah   = 0;
        $diperbarui = 0;

        Event::listen('eloquent.created: *', function () use (&$ditambah) {
            $ditambah++;
        });
        Event::listen('eloquent.updated: *', function () use (&$diperbarui) {
            $diperbarui++;
        });

        try {
            // Seeder memakai $this->command?->info(...) yang null-safe, jadi aman
            // dijalankan tanpa konteks konsol seperti di sini.
            (new $kelas())->run();
        } catch (\Throwable $e) {
            report($e);
            Log::warning("Sinkronisasi {$modul} gagal: " . $e->getMessage());

            $hasil['error'] = 'Sinkronisasi gagal: ' . $e->getMessage();

            return $hasil;
        }

        $hasil['ditambah']   = $ditambah;
        $hasil['diperbarui'] = $diperbarui;

        return $hasil;
    }

    /** Kalimat siap tampil untuk notifikasi di portal. */
    public function ringkas(string $modul, array $hasil): string
    {
        if ($hasil['error']) {
            return $hasil['error'];
        }

        if ($hasil['ditambah'] === 0 && $hasil['diperbarui'] === 0) {
            return self::label($modul) . ': tidak ada data baru dari BPS. '
                . 'Kemungkinan tahun terbaru belum dirilis, atau data di portal sudah sama dengan sumbernya.';
        }

        return self::label($modul) . ': ' . $hasil['ditambah'] . ' baris ditambahkan, '
            . $hasil['diperbarui'] . ' diperbarui. Data lama yang tidak ada di BPS tidak dihapus.';
    }
}
