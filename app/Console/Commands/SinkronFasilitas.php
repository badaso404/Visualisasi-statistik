<?php

namespace App\Console\Commands;

use App\Models\FasilitasUmum;
use App\Services\Statistik\FasilitasJakbarSync;
use Illuminate\Console\Command;

/**
 * Jalur utama penarikan fasilitas umum.
 *
 * Panel admin juga punya tombol sinkronisasi, tapi hanya per kategori: menarik
 * SEMUA kategori berarti ~78 permintaan berjeda 1,1 detik (batas 60/menit dari
 * sumber), jadi sekitar satu setengah menit — melewati batas waktu request web
 * pada umumnya. Di command line tidak ada batas itu.
 *
 *   php artisan statistik:sinkron-fasilitas
 *   php artisan statistik:sinkron-fasilitas --kategori=rptra --kategori=olahraga
 */
class SinkronFasilitas extends Command
{
    protected $signature = 'statistik:sinkron-fasilitas
                            {--kategori=* : Slug kategori tertentu; kosongkan untuk semua}';

    protected $description = 'Menarik data fasilitas umum dari API kecamatan Jakarta Barat';

    public function handle(FasilitasJakbarSync $sync): int
    {
        $kategori = (array) $this->option('kategori');

        if ($tidakDikenal = array_diff($kategori, array_keys(FasilitasUmum::KATEGORI))) {
            $this->error('Kategori tidak dikenal: ' . implode(', ', $tidakDikenal));
            $this->line('Pilihan: ' . implode(', ', array_keys(FasilitasUmum::KATEGORI)));

            return self::FAILURE;
        }

        $this->info('Menarik data fasilitas' . ($kategori ? ' (' . implode(', ', $kategori) . ')' : ' semua kategori') . '…');
        $this->comment('Sumber membatasi 60 permintaan/menit, jadi ini memang berjalan lambat.');

        $hasil = $sync->jalankan($kategori ?: null);

        $this->newLine();
        $this->table(
            ['Ditambah', 'Diperbarui', 'Tanpa kecamatan'],
            [[$hasil['ditambah'], $hasil['diperbarui'], $hasil['tanpa_kecamatan']]]
        );

        if ($hasil['error']) {
            $this->error($hasil['error']);

            return self::FAILURE;
        }

        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
