<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Pendamping unique index di tabel statistik (lihat migration
 * add_unique_keys_to_statistik_tables).
 *
 * Tanpa ini database tetap menolak baris kembar, tapi admin akan melihat
 * halaman error SQL. Dengan ini admin dapat pesan yang bisa ditindaklanjuti.
 */
trait ValidasiPeriodeUnik
{
    /**
     * @param  string      $tabel      tabel yang dicek
     * @param  array       $pendamping kolom lain pembentuk kunci, mis. ['kecamatan_id' => 5]
     * @param  Model|null  $item       baris yang sedang diedit (dikecualikan dari cek)
     */
    protected function unikPerPeriode(string $tabel, array $pendamping = [], ?Model $item = null)
    {
        return Rule::unique($tabel)
            ->where(function ($query) use ($pendamping) {
                foreach ($pendamping as $kolom => $nilai) {
                    $query->where($kolom, $nilai);
                }
            })
            ->ignore($item?->getKey());
    }

    /** Pesan seragam untuk semua modul. */
    protected function pesanPeriodeUnik(string $keterangan): array
    {
        return [
            'tahun.unique'          => "Data {$keterangan} sudah ada. Silakan edit baris yang sudah ada, atau pakai Isi Massal untuk memperbaruinya.",
            'bulan.unique'          => "Data {$keterangan} sudah ada. Silakan edit baris yang sudah ada.",
            'nama_kelurahan.unique' => "Data {$keterangan} sudah ada. Silakan edit baris yang sudah ada.",
            'data_geografis_id.unique' => "Data {$keterangan} sudah ada. Silakan edit baris yang sudah ada.",
        ];
    }
}
