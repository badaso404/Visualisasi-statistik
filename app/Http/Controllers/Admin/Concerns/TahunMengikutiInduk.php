<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Mengikat tahun sebuah tabel anak pada tahun yang sudah ada di tabel induknya.
 *
 * Alasannya bukan kerapian melainkan visibilitas: halaman publik menyusun
 * daftar tahun dari tabel INDUK dan menampilkan "data belum tersedia" bila
 * ringkasan tahun itu tidak ada. Baris anak untuk tahun tanpa induk memang
 * tersimpan, tetapi tidak akan pernah bisa dibuka pengunjung — kerja operator
 * hilang tanpa ada yang memberi tahu.
 *
 * Berbeda dari luas_kecamatan yang menyimpan data_geografis_id sebagai foreign
 * key, tabel anak lain hanya menyimpan `tahun` sebagai angka biasa. Karena itu
 * ikatan di sini ditegakkan oleh validasi, bukan oleh skema, dan harus dipasang
 * di SEMUA pintu masuk: modal, form isi massal, dan impor CSV.
 */
trait TahunMengikutiInduk
{
    /**
     * Nama tabel induk pemasok daftar tahun, atau null bila modul ini memang
     * berdiri sendiri (mis. JakWiFi & CCTV yang tidak punya tabel ringkasan).
     */
    abstract protected function tabelInduk(): ?string;

    /** Sebutan induk pada pesan galat, mis. 'ringkasan kependudukan'. */
    protected function sebutanInduk(): string
    {
        return 'ringkasan';
    }

    /** Modul tempat ringkasan itu diisi, untuk mengarahkan operator. */
    protected function tabInduk(): string
    {
        return 'tab Ringkasan';
    }

    protected function terikatInduk(): bool
    {
        return $this->tabelInduk() !== null;
    }

    /** Tahun-tahun yang sudah punya ringkasan induk, terbaru dulu. */
    protected function tahunInduk(): Collection
    {
        if (!$this->terikatInduk()) {
            return collect();
        }

        return DB::table($this->tabelInduk())
            ->select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
    }

    protected function tahunPunyaInduk(int $tahun): bool
    {
        return !$this->terikatInduk()
            || DB::table($this->tabelInduk())->where('tahun', $tahun)->exists();
    }

    /**
     * Aturan validasi kolom `tahun` pada tabel anak. Modul tanpa induk tetap
     * mendapat batas wajar 1900-2100 seperti sebelumnya.
     */
    protected function aturanTahunInduk(): array
    {
        $dasar = ['required', 'integer', 'min:1900', 'max:2100'];

        return $this->terikatInduk()
            ? array_merge($dasar, [Rule::exists($this->tabelInduk(), 'tahun')])
            : $dasar;
    }

    protected function pesanTahunInduk(): array
    {
        if (!$this->terikatInduk()) {
            return [];
        }

        return [
            'tahun.exists' => 'Belum ada ' . $this->sebutanInduk() . ' untuk tahun tersebut. '
                . 'Tambahkan dulu lewat ' . $this->tabInduk()
                . ', karena tanpa itu datanya tidak akan tampil di situs publik.',
        ];
    }
}
