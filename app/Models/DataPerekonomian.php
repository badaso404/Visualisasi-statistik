<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPerekonomian extends Model
{
    protected $table = 'data_perekonomian';

    protected $fillable = [
        'tahun',
        'pdrb_adhb',          // Juta Rupiah, atas dasar harga berlaku
        'pdrb_adhk',          // Juta Rupiah, atas dasar harga konstan 2010
        'laju_pertumbuhan',   // % pertumbuhan ekonomi
        'sumber',
    ];

    /**
     * Banyaknya tahun terakhir yang bisa dipilih pengunjung di halaman publik.
     * Grafik tren sengaja tidak dibatasi ini — trennya butuh rentang panjang.
     */
    public const TAHUN_DITAMPILKAN = 3;

    /**
     * Tahun paling awal yang masih tampil di halaman publik, atau null bila tabel
     * kosong. Portal admin memakainya untuk menandai baris mana yang terlihat
     * pengunjung, agar ambangnya tidak ditulis ulang di dua tempat.
     */
    public static function batasTahunPublik(): ?int
    {
        $terbaru = static::max('tahun');

        return $terbaru ? (int) $terbaru - self::TAHUN_DITAMPILKAN + 1 : null;
    }

    /** Rincian 17 lapangan usaha pada tahun yang sama. */
    public function sektor()
    {
        return $this->hasMany(PdrbSektor::class, 'tahun', 'tahun');
    }
}
