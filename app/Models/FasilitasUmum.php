<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

class FasilitasUmum extends Model
{
    protected $table = 'fasilitas_umum';

    protected $fillable = [
        'kategori',
        'sumber_id',
        'nama',
        'alamat',
        'kecamatan_id',
        'kelurahan',
        'latitude',
        'longitude',
        'foto',
        'sumber',
    ];

    /**
     * Kategori yang dikenal modul ini: slug => label Indonesia.
     *
     * Slug-nya sengaja sama persis dengan segmen URL API sumber
     * (…/fasilitas/rptra, …/fasilitas/tempat-ibadah) supaya sinkronisasi tidak
     * perlu tabel terjemahan sendiri.
     *
     * Pendidikan & kesehatan ADA di API sumber tapi tidak diambil di sini:
     * keduanya sudah punya modul statistiknya sendiri (pendidikan_kecamatan,
     * fasilitas_kesehatan_kecamatan), jadi menariknya lagi hanya melahirkan dua
     * angka berbeda untuk hal yang sama di satu portal.
     *
     * @var array<string, string>
     */
    public const KATEGORI = [
        'olahraga'          => 'Olahraga (GOR)',
        'rptra'             => 'RPTRA',
        'tempat-ibadah'     => 'Tempat Ibadah',
        'perpustakaan'      => 'Perpustakaan',
        'transportasi'      => 'Transportasi',
        'pemadam-kebakaran' => 'Pemadam Kebakaran',
    ];

    /** Ikon Font Awesome per kategori, dipakai kartu & tabel halaman publik. */
    public const IKON = [
        'olahraga'          => 'fa-futbol',
        'rptra'             => 'fa-tree',
        'tempat-ibadah'     => 'fa-mosque',
        'perpustakaan'      => 'fa-book',
        'transportasi'      => 'fa-bus',
        'pemadam-kebakaran' => 'fa-fire-extinguisher',
    ];

    /**
     * Warna per kategori — dipakai bersama oleh grafik, lencana tabel, legenda,
     * dan titik peta supaya satu kategori selalu berwarna sama di mana pun.
     *
     * Urutannya mengikuti urutan KATEGORI di atas, dan itu penting: urutan
     * itulah yang menentukan warna mana bersebelahan di batang bertumpuk, di
     * donat, dan di legenda. Susunan ini sudah diukur, bukan dikira-kira —
     * lolos seluruh cek keterbedaan warna (termasuk untuk buta warna
     * protan/deutan/tritan) pada pasangan yang bersebelahan.
     *
     * Kuning #eda100 kontrasnya di bawah 3:1 terhadap latar putih. Itu boleh di
     * sini karena identitas kategori tidak pernah disampaikan lewat warna saja:
     * ada legenda berlabel, lencana bertulisan di tiap baris, dan tabel
     * lengkapnya sendiri.
     *
     * Dua warna sengaja diganti dari palet lama (nilai lamanya ditinggalkan
     * sebagai catatan, jangan dihapus):
     *   rptra        #2e7d32 -> #eda100  hijau tua nyaris tak terbedakan dari
     *                                    hijau olahraga (#008300) di sebelahnya
     *   pemadam      #e53935 -> #eb6834  merah lama terlalu dekat dengan oranye
     * dan transportasi bertukar ke biru agar merah & oranye tidak bersebelahan.
     */
    public const WARNA = [
        'olahraga'          => '#008300',   // hijau
        'rptra'             => '#eda100',   // kuning   — WARNA LAMA: #2e7d32
        'tempat-ibadah'     => '#4a3aa7',   // violet
        'perpustakaan'      => '#e34948',   // merah    — WARNA LAMA: #2a78d6
        'transportasi'      => '#2a78d6',   // biru     — WARNA LAMA: #eb6834
        'pemadam-kebakaran' => '#eb6834',   // oranye   — WARNA LAMA: #e53935
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /** Label kategori, sudah lewat berkas bahasa agar ikut EN/ID. */
    public function labelKategori(): string
    {
        return static::label($this->kategori);
    }

    /**
     * Kategori dari data lama / hasil input manual bisa saja tidak ada di
     * berkas bahasa; nilainya ditampilkan apa adanya daripada bocor sebagai
     * "fasilitas.kategori.xxx" di halaman.
     */
    public static function label(string $kategori): string
    {
        $kunci = 'fasilitas.kategori.' . $kategori;

        return Lang::has($kunci) ? __($kunci) : (self::KATEGORI[$kategori] ?? $kategori);
    }

    public function ikon(): string
    {
        return self::IKON[$this->kategori] ?? 'fa-location-dot';
    }

    public function warna(): string
    {
        return self::WARNA[$this->kategori] ?? '#888';
    }

    /** Hanya kategori yang dikenal — melindungi query dari slug asal-asalan. */
    public function scopeKategori(Builder $query, ?string $kategori): Builder
    {
        if ($kategori && array_key_exists($kategori, self::KATEGORI)) {
            $query->where('kategori', $kategori);
        }

        return $query;
    }

    /** Pencarian bebas untuk tabel admin: nama atau alamat. */
    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        $kata = trim((string) $kata);

        if ($kata !== '') {
            $query->where(function (Builder $q) use ($kata) {
                $q->where('nama', 'like', "%{$kata}%")
                  ->orWhere('alamat', 'like', "%{$kata}%")
                  ->orWhere('kelurahan', 'like', "%{$kata}%");
            });
        }

        return $query;
    }
}
