<?php

namespace Tests\Concerns;

use App\Models\DataKemiskinan;
use App\Models\DataKependudukan;
use App\Models\DataKesehatan;
use App\Models\DataPendidikan;
use App\Models\DataPerekonomian;

/**
 * Pembuat record ringkasan induk untuk tes tabel anak.
 *
 * Sejak tahun tabel anak diikat ke tahun induk (lihat
 * App\Http\Controllers\Admin\Concerns\TahunMengikutiInduk), tes yang menulis
 * data per kecamatan/kelurahan/sektor harus lebih dulu menyediakan ringkasan
 * tahunnya — persis seperti yang dilakukan operator di portal.
 *
 * Angkanya sengaja sekadar memenuhi kolom NOT NULL; tidak ada tes yang
 * memeriksa nilainya, hanya keberadaan tahunnya.
 */
trait MembuatRingkasanInduk
{
    protected function indukKependudukan(int ...$tahun): void
    {
        foreach ($tahun as $t) {
            DataKependudukan::updateOrCreate(['tahun' => $t], [
                'jumlah_laki_laki' => 10, 'jumlah_perempuan' => 10, 'jumlah_total' => 20,
            ]);
        }
    }

    protected function indukPendidikan(int ...$tahun): void
    {
        foreach ($tahun as $t) {
            DataPendidikan::updateOrCreate(['tahun' => $t], [
                'apm_sd_mi' => 99, 'apm_smp_mts' => 88, 'apm_sma_smk_man' => 77,
                'apk_sd_mi' => 105, 'apk_smp_mts' => 95, 'apk_sma_smk_man' => 85,
            ]);
        }
    }

    protected function indukKesehatan(int ...$tahun): void
    {
        foreach ($tahun as $t) {
            DataKesehatan::updateOrCreate(['tahun' => $t], ['jumlah_tempat_tidur_rs' => 1200]);
        }
    }

    protected function indukKemiskinan(int ...$tahun): void
    {
        foreach ($tahun as $t) {
            DataKemiskinan::updateOrCreate(['tahun' => $t], [
                'jumlah_penduduk_miskin' => 100000, 'persentase_penduduk_miskin' => 4.1,
                'garis_kemiskinan' => 700000, 'indeks_kedalaman' => 0.5, 'indeks_keparahan' => 0.1,
            ]);
        }
    }

    protected function indukPerekonomian(int ...$tahun): void
    {
        foreach ($tahun as $t) {
            DataPerekonomian::updateOrCreate(['tahun' => $t], [
                'pdrb_adhb' => 627869621.19, 'pdrb_adhk' => 383113079.03, 'laju_pertumbuhan' => 5.27,
            ]);
        }
    }

    /** Tahun yang lazim dipakai tes; cukup untuk sebagian besar kasus. */
    protected function semuaInduk(int ...$tahun): void
    {
        $tahun = $tahun ?: [2023, 2024, 2025, 2026];

        $this->indukKependudukan(...$tahun);
        $this->indukPendidikan(...$tahun);
        $this->indukKesehatan(...$tahun);
        $this->indukKemiskinan(...$tahun);
        $this->indukPerekonomian(...$tahun);
    }
}
