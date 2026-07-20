<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataBencana extends Model
{
    protected $table = 'data_bencana';

    protected $fillable = [
        'kecamatan_id',
        'jenis_bencana',
        'nama_lokasi',
        'tahun',
        'periode_data',
        'triwulan',
        'wilayah',
        'tanggal_kejadian',
        'latitude',
        'longitude',
        'jumlah_kejadian',
        'jumlah_korban_meninggal',
        'jumlah_korban_luka',
        'jumlah_korban',
        'jumlah_terdampak',
        'keterangan',
        'sumber',
    ];

    /** Wilayah Jakarta Barat sesuai penamaan API Satu Data Jakarta. */
    public const WILAYAH_JAKBAR = 'KOTA ADM. JAKARTA BARAT';

    /** Label periode yang enak dibaca: "202403" → "2024 TW1". */
    public function getPeriodeLabelAttribute(): string
    {
        if (!$this->periode_data) {
            return $this->tahun ? (string) $this->tahun : '-';
        }
        $th = substr($this->periode_data, 0, 4);
        return $this->triwulan ? "{$th} TW{$this->triwulan}" : $th;
    }

    /** Ubah "202403" → nomor triwulan (1-4) berdasarkan bulan. */
    public static function triwulanDariPeriode(?string $periode): ?int
    {
        if (!$periode || strlen($periode) < 6) return null;
        $bulan = (int) substr($periode, 4, 2);
        return $bulan >= 1 ? (int) ceil($bulan / 3) : null;
    }

    /** Daftar jenis bencana yang tersedia (untuk dropdown form). */
    public const JENIS = [
        'Banjir',
        'Kebakaran',
        'Tanah Longsor',
        'Angin Kencang',
        'Pohon Tumbang',
        'Gempa Bumi',
        'Lainnya',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
