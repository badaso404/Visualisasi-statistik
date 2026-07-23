<?php

namespace App\Models\Concerns;

/**
 * Menyeragamkan penghapusan berjenjang untuk modul yang tabel anaknya terhubung
 * ke induk lewat kolom `tahun`, bukan foreign key (satu record induk per tahun).
 *
 * Setara cascadeOnDelete milik geografis — yang memang punya FK
 * data_geografis_id — tetapi tanpa mengubah skema: begitu record induk dihapus,
 * seluruh baris anak pada tahun yang sama ikut terhapus sehingga tidak ada data
 * yatim yang menggantung di database.
 *
 * Model induk cukup mendeklarasikan daftar kelas anaknya:
 *   protected array $anakPerTahun = [PendudukKecamatan::class, ...];
 */
trait MenghapusAnakPerTahun
{
    public static function bootMenghapusAnakPerTahun(): void
    {
        static::deleting(function ($induk) {
            foreach ($induk->anakPerTahun as $kelasAnak) {
                $kelasAnak::where('tahun', $induk->tahun)->delete();
            }
        });
    }
}
