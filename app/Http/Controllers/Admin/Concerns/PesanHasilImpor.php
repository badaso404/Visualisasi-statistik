<?php

namespace App\Http\Controllers\Admin\Concerns;

/**
 * Menyusun umpan balik setelah impor CSV.
 *
 * Dipisah karena versi sebelumnya menyesatkan: selama ADA SATU saja baris yang
 * masuk, hasilnya dikirim lewat channel 'success' sehingga muncul sebagai alert
 * hijau bertuliskan "berhasil" — padahal sebagian baris ditolak diam-diam.
 * Operator membaca warna hijau, menutup notifikasi, lalu bingung kenapa datanya
 * tidak muncul.
 *
 * Aturannya sekarang: begitu ada satu baris yang dilewati, notifikasinya
 * berwarna peringatan, bukan hijau.
 */
trait PesanHasilImpor
{
    /**
     * @param  int    $sukses  baris yang tersimpan
     * @param  array  $gagal   pesan per baris yang dilewati
     * @return array{0: string, 1: string}  [channel flash, pesan]
     */
    protected function hasilImpor(int $sukses, array $gagal): array
    {
        if ($gagal === []) {
            return ['success', "{$sukses} baris berhasil diimpor."];
        }

        $jumlahGagal = count($gagal);

        // Alasan ditampilkan sebagai daftar, bukan disambung jadi satu kalimat
        // panjang, supaya baris mana yang bermasalah langsung terbaca.
        $rincian = implode(' | ', array_slice($gagal, 0, 5));
        if ($jumlahGagal > 5) {
            $rincian .= ' | (dan ' . ($jumlahGagal - 5) . ' baris lain)';
        }

        if ($sukses === 0) {
            return ['error', "Tidak ada data yang masuk — seluruh {$jumlahGagal} baris dilewati. Penyebab: {$rincian}"];
        }

        return ['error', "Sebagian saja yang masuk: {$sukses} baris tersimpan, "
            . "{$jumlahGagal} baris DILEWATI dan tidak tersimpan. Penyebab: {$rincian}"];
    }
}
